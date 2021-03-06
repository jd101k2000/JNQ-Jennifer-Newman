<?php

class Worpit_Controllers_Base {
	
	protected $m_aOutput = array();
		
	/**
	 * @since 1.0.14
	 *
	 * @return string
	 */
	protected function writeOutputLog() {
		if ( !count( $this->m_aOutput ) ) {
			return '';
		}
		
		$sFile = dirname(__FILE__).'/../../logs/output.'.time().'.php';
		if ( !is_dir( dirname( $sFile ) ) && @mkdir( dirname( $sFile ), 0755, true ) ) {
		
		}
		
		$sLogContents = print_r( $this->m_aOutput, true );
		//@file_put_contents( $sFile, $sLogContents );
		return $sLogContents;
	}

	/**
	 * @param array $inaData
	 * @param string $insMessage
	 *
	 * @uses exit
	 */
	protected function success( $insBase64Data = '', $insMessage = '' ) {
		$aResponse = array(
			'success'			=> true,
			'message'			=> $insMessage,
			'data'				=> $insBase64Data,
			'base64response'	=> 1
		);
		
		echo '<worpitresponse>'.serialize( $aResponse ).'</worpitresponse>';
		echo "\n";
		echo '<worpitoutput>'.$this->writeOutputLog().'</worpitoutput>';
		
		exit( 0 );
	}
	
	/**
	 * @param string $insMessage
	 * @param integer $innErrno
	 *
	 * @uses exit
	 */
	protected function fail( $insMessage, $innErrno = -1 ) {
		$aResponse = array(
			'success'			=> false,
			'error'				=> $insMessage,
			'errno'				=> $innErrno,
			'base64response'	=> 1
		);
		
		echo '<worpitresponse>'.serialize( $aResponse ).'</worpitresponse>';
		echo "\n";
		echo '<worpitoutput>'.$this->writeOutputLog().'</worpitoutput>';
		
		exit( $innErrno );
	}
	
	/**
	 * @param mixed $inmMessage
	 * @return array
	 */
	protected function log( $inmMessage ) {
		return array_push( $this->m_aOutput, $inmMessage );
	}
	
	/**
	 * @param array $inaArray
	 * @return array
	 */
	protected function logMerge( $inaArray ) {
		$this->m_aOutput = array_merge( $this->m_aOutput, $inaArray );
		return $this->m_aOutput;
	}
	
	/**
	 * This function returns an array of variables which will then be converted into constants.
	 * The constants will be written to
	 *
	 * @return array
	 */
	public function getPackageConstants() {
		/**
		 * http://codex.wordpress.org/Editing_wp-config.php
		 *
		 * $x = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
		 * $x will equal "http://[url-path-to-plugins]/[myplugin]/"
		 *
		 * WP_CONTENT_DIR  // no trailing slash, full paths only
		 * WP_CONTENT_URL  // full url
		 * WP_PLUGIN_DIR  // full path, no trailing slash
		 * WP_PLUGIN_URL  // full url, no trailing slash
		 */
		
		/**
		 * add_action( 'login_init',          'send_frame_options_header',     10, 0 );
		 * add_action( 'admin_init',          'send_frame_options_header', 10, 0 );
		 */
		
		$sHomeDir = preg_replace( '|https?://[^/]+|i', '', trim( get_option( 'home' ), '/' ) . '/' );
		$sSiteDir = preg_replace( '|https?://[^/]+|i', '', trim( get_option( 'siteurl' ), '/' ) . '/' );

		$aPackageConstants = array(
			'variable_prefix'		=> Worpit_Plugin::$VariablePrefix,

			'home'					=> get_option( 'home' ),
			'siteurl'				=> get_option( 'siteurl' ),
			'document_root'			=> $_SERVER['DOCUMENT_ROOT'],

			'abspath'				=> worpitCLP( ABSPATH ),
			'includes_dir'			=> worpitCLP( ABSPATH . WPINC ),

			'content_dir'			=> worpitCLP( WP_CONTENT_DIR ),
			'plugin_dir'			=> worpitCLP( WP_PLUGIN_DIR ),

			'content_url'			=> WP_CONTENT_URL,
			'plugin_url'			=> WP_PLUGIN_URL,

			'plugin_worpit'			=> str_replace( worpitCLP( WP_PLUGIN_DIR ).'/', '', worpitCLP( dirname(__FILE__) ) ).'/worpit.php',

			'site_dir'				=> $sSiteDir,
			'abs_site_dir'			=> worpitCLP( $_SERVER['DOCUMENT_ROOT'] ).rtrim( $sSiteDir, '/' ),

			'home_dir'				=> $sHomeDir,
			'abs_home_dir'			=> worpitCLP( $_SERVER['DOCUMENT_ROOT'] ).rtrim( $sHomeDir, '/' ),
		);

		return $aPackageConstants;
	}
}