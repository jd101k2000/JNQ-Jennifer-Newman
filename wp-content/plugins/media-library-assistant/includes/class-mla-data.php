<?php
/**
 * Database and template file access for MLA needs
 *
 * @package Media Library Assistant
 * @since 0.1
 */
 
/**
 * Class MLA (Media Library Assistant) Data provides database and template file access for MLA needs
 *
 * The _template functions are inspired by the book "WordPress 3 Plugin Development Essentials."
 * Templates separate HTML markup from PHP code for easier maintenance and localization.
 *
 * @package Media Library Assistant
 * @since 0.1
 */
class MLAData {
	/**
	 * Provides a unique suffix for the ALT Text SQL VIEW
	 *
	 * @since 0.40
	 */
	const MLA_ALT_TEXT_VIEW_SUFFIX = 'alt_text_view';
	
	/**
	 * Provides a unique name for the ALT Text SQL VIEW
	 *
	 * @since 0.40
	 *
	 * @var	array
	 */
	private static $mla_alt_text_view = NULL;
	
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 0.1
	 */
	public static function initialize() {
		global $table_prefix;
		self::$mla_alt_text_view = $table_prefix . MLA_OPTION_PREFIX . self::MLA_ALT_TEXT_VIEW_SUFFIX;

		add_action( 'save_post', 'MLAData::mla_save_post_action', 10, 1);
		add_action( 'edit_attachment', 'MLAData::mla_save_post_action', 10, 1);
		add_action( 'add_attachment', 'MLAData::mla_save_post_action', 10, 1);
	}
	
	/**
	 * Load an HTML template from a file
	 *
	 * Loads a template to a string or a multi-part template to an array.
	 * Multi-part templates are divided by comments of the form <!-- template="key" -->,
	 * where "key" becomes the key part of the array.
	 *
	 * @since 0.1
	 *
	 * @param	string 	Complete path and name of the template file, option name or the raw template
	 * @param	string 	Optional type of template source; 'file' (default), 'option', 'string'
	 *
	 * @return	string|array|false|NULL
	 *  		string for files that do not contain template divider comments,
	 * 			array for files containing template divider comments,
	 *			false if file or option does not exist,
	 *			NULL if file could not be loaded.
	 */
	public static function mla_load_template( $source, $type = 'file' ) {
		switch ( $type ) {
			case 'file':
				if ( !file_exists( $source ) )
					return false;
				
				$template = file_get_contents( $source, true );
				if ( $template == false ) {
					error_log( 'ERROR: mla_load_template file not found ' . var_export( $source, true ), 0 );
					return NULL;
				}
				break;
			case 'option':
				$template =  MLAOptions::mla_get_option( $source );
				if ( $template == false ) {
					return false;
				}
				break;
			case 'string':
				$template = $source;
				if ( empty( $template ) ) {
					return false;
				}
				break;
			default:
				error_log( 'ERROR: mla_load_template bad source type ' . var_export( $type, true ), 0 );
				return NULL;
		}
		
		$match_count = preg_match_all( '#\<!-- template=".+" --\>#', $template, $matches, PREG_OFFSET_CAPTURE );
		
		if ( ( $match_count == false ) || ( $match_count == 0 ) )
			return $template;
		
		$matches = array_reverse( $matches[0] );
		
		$template_array = array();
		$current_offset = strlen( $template );
		foreach ( $matches as $key => $value ) {
			$template_key = preg_split( '#"#', $value[0] );
			$template_key = $template_key[1];
			$template_value = substr( $template, $value[1] + strlen( $value[0] ), $current_offset - ( $value[1] + strlen( $value[0] ) ) );
			/*
			 * Trim exactly one newline sequence from the start of the value
			 */
			if ( 0 === strpos( $template_value, "\r\n" ) )
				$offset = 2;
			elseif ( 0 === strpos( $template_value, "\n\r" ) )
				$offset = 2;
			elseif ( 0 === strpos( $template_value, "\n" ) )
				$offset = 1;
			elseif ( 0 === strpos( $template_value, "\r" ) )
				$offset = 1;
			else
				$offset = 0;

			$template_value = substr( $template_value, $offset );
				
			/*
			 * Trim exactly one newline sequence from the end of the value
			 */
			$length = strlen( $template_value );
			if ( $length > 2)
				$postfix = substr( $template_value, ($length - 2), 2 );
			else
				$postfix = $template_value;
				
			if ( 0 === strpos( $postfix, "\r\n" ) )
				$length -= 2;
			elseif ( 0 === strpos( $postfix, "\n\r" ) )
				$length -= 2;
			elseif ( 0 === strpos( $postfix, "\n" ) )
				$length -= 1;
			elseif ( 0 === strpos( $postfix, "\r" ) )
				$length -= 1;
				
			$template_array[ $template_key ] = substr( $template_value, 0, $length );
			$current_offset = $value[1];
		} // foreach $matches
		
		return $template_array;
	}
	
	/**
	 * Expand a template, replacing place holders with their values
	 *
	 * A simple parsing function for basic templating.
	 *
	 * @since 0.1
	 *
	 * @param	string	A formatting string containing [+placeholders+]
	 * @param	array	An associative array containing keys and values e.g. array('key' => 'value')
	 *
	 * @return	string	Placeholders corresponding to the keys of the hash will be replaced with their values
	 */
	public static function mla_parse_template( $tpl, $hash ) {
		foreach ( $hash as $key => $value ) {
			if ( is_scalar( $value ) )
				$tpl = str_replace( '[+' . $key . '+]', $value, $tpl );
		}
		
		return $tpl;
	}
	
	/**
	 * Analyze a template, returning an array of the place holders it contains
	 *
	 * @since 0.90
	 *
	 * @param	string	A formatting string containing [+placeholders+]
	 *
	 * @return	array	Placeholder information: each entry is an array with
	 * 					['prefix'] => string, ['value'] => string, ['option'] => string 'single'|'export'
	 */
	public static function mla_get_template_placeholders( $tpl ) {
		$results = array();
		$match_count = preg_match_all( '/\[\+[^+]+\+\]/', $tpl, $matches );
		if ( ( $match_count == false ) || ( $match_count == 0 ) )
			return $results;
			
		foreach ( $matches[0] as $match ) {
			$key = substr( $match, 2, (strlen( $match ) - 4 ) );
			$result = array( 'prefix' => '', 'value' => '', 'option' => 'text' );
			$match_count = preg_match( '/\[\+(.+):(.+)/', $match, $matches );
			if ( 1 == $match_count ) {
				$result['prefix'] = $matches[1];
				$tail = $matches[2];
			}
			else {
				$tail = substr( $match, 2);
			}
			
			$match_count = preg_match( '/([^,]+)(,(single|export))\+\]/', $tail, $matches );
			if ( 1 == $match_count ) {
				$result['value'] = $matches[1];
				$result['option'] = $matches[3];
			}
			else {
				$result['value'] = substr( $tail, 0, (strlen( $tail ) - 2 ) );
			}
			
		$results[ $key ] = $result;
		} // foreach
		
		return $results;
	}
	
	/**
	 * Cache the results of mla_count_list_table_items for reuse in mla_query_list_table_items
	 *
	 * @since 1.40
	 *
	 * @var	array
	 */
	private static $mla_list_table_items = NULL;
	
	/**
	 * Get the total number of attachment posts
	 *
	 * @since 0.30
	 *
	 * @param	array	Query variables, e.g., from $_REQUEST
	 * @param	int		(optional) number of rows to skip over to reach desired page
	 * @param	int		(optional) number of rows on each page
	 *
	 * @return	integer	Number of attachment posts
	 */
	public static function mla_count_list_table_items( $request, $offset = NULL, $count = NULL )
	{
		if ( NULL != $offset && NULL != $count ) {
			$request = self::_prepare_list_table_query( $request, $offset, $count );
			self::$mla_list_table_items = self::_execute_list_table_query( $request );
			return self::$mla_list_table_items->found_posts;
		}

		$request = self::_prepare_list_table_query( $request );
		$results = self::_execute_list_table_query( $request );
		self::$mla_list_table_items = NULL;
		
		return $results->found_posts;
	}
	
	/**
	 * Retrieve attachment objects for list table display
	 *
	 * Supports prepare_items in class-mla-list-table.php.
	 * Modeled after wp_edit_attachments_query in wp-admin/post.php
	 *
	 * @since 0.1
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 * @param	int		number of rows to skip over to reach desired page
	 * @param	int		number of rows on each page
	 *
	 * @return	array	attachment objects (posts) including parent data, meta data and references
	 */
	public static function mla_query_list_table_items( $request, $offset, $count ) {
		if ( NULL == self::$mla_list_table_items ) {
			$request = self::_prepare_list_table_query( $request, $offset, $count );
			self::$mla_list_table_items = self::_execute_list_table_query( $request );
		}

		$attachments = self::$mla_list_table_items->posts;
		foreach ( $attachments as $index => $attachment ) {
			/*
			 * Add parent data
			 */
			$parent_data = self::mla_fetch_attachment_parent_data( $attachment->post_parent );
			foreach ( $parent_data as $parent_key => $parent_value ) {
				$attachments[ $index ]->$parent_key = $parent_value;
			}
			
			/*
			 * Add meta data
			 */
			$meta_data = self::mla_fetch_attachment_metadata( $attachment->ID );
			foreach ( $meta_data as $meta_key => $meta_value ) {
				$attachments[ $index ]->$meta_key = $meta_value;
			}
			/*
			 * Add references
			 */
			$references = self::mla_fetch_attachment_references( $attachment->ID, $attachment->post_parent );
			$attachments[ $index ]->mla_references = $references;
		}
		
		return $attachments;
	}
	
	/**
	 * Retrieve attachment objects for the WordPress Media Manager
	 *
	 * Supports month-year and taxonomy-term filters as well as the enhanced search box
	 *
	 * @since 1.20
	 *
	 * @param	array	query parameters from Media Manager
	 * @param	int		number of rows to skip over to reach desired page
	 * @param	int		number of rows on each page
	 *
	 * @return	array	attachment objects (posts)
	 */
	public static function mla_query_media_modal_items( $request, $offset, $count ) {
		$request = self::_prepare_list_table_query( $request, $offset, $count );
		return self::_execute_list_table_query( $request );
	}
	
	/**
	 * WP_Query filter "parameters"
	 *
	 * This array defines parameters for the query's join, where and orderby filters.
	 * The parameters are set up in the _prepare_list_table_query function, and
	 * any further logic required to translate those values is contained in the filters.
	 *
	 * Array index values are: use_postmeta_view, postmeta_key, postmeta_value, patterns, detached, orderby, order, mla-metavalue, debug, s, mla_search_connector, mla_search_fields, sentence, exact
	 *
	 * @since 0.30
	 *
	 * @var	array
	 */
	private static $query_parameters = array();

	/**
	 * Sanitize and expand query arguments from request variables
	 *
	 * Prepare the arguments for WP_Query.
	 * Modeled after wp_edit_attachments_query in wp-admin/post.php
	 *
	 * @since 0.1
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 * @param	int		Optional number of rows (default 0) to skip over to reach desired page
	 * @param	int		Optional number of rows on each page (0 = all rows, default)
	 *
	 * @return	array	revised arguments suitable for WP_Query
	 */
	private static function _prepare_list_table_query( $raw_request, $offset = 0, $count = 0 ) {
		/*
		 * Go through the $raw_request, take only the arguments that are used in the query and
		 * sanitize or validate them.
		 */
		if ( ! is_array( $raw_request ) ) {
			error_log( 'ERROR: _prepare_list_table_query $raw_request = ' . var_export( $raw_request, true ), 0 );
			return null;
		}
		
		$clean_request = array (
			'm' => 0,
			'orderby' => MLAOptions::mla_get_option( 'default_orderby' ),
			'order' => MLAOptions::mla_get_option( 'default_order' ),
			'post_type' => 'attachment',
			'post_status' => 'inherit',
			'mla_search_connector' => 'AND',
			'mla_search_fields' => array()
		);
		
		foreach ( $raw_request as $key => $value ) {
			switch ( $key ) {
				/*
				 * 'sentence' and 'exact' modify the keyword search ('s')
				 * Their value is not important, only their presence.
				 */
				case 'sentence':
				case 'exact':
				case 'mla-tax':
				case 'mla-term':
					$clean_request[ $key ] = sanitize_key( $value );
					break;
				case 'orderby':
					if ( 'none' == $value )
						$clean_request[ $key ] = $value;
					else {
						$sortable_columns = MLA_List_Table::mla_get_sortable_columns( );
						foreach ($sortable_columns as $sort_key => $sort_value ) {
							if ( $value == $sort_value[0] ) {
								$clean_request[ $key ] = $value;
								break;
							}
						} // foreach
					}
					break;
				/*
				 * post__in and post__not_in are used in the Media Modal Ajax queries
				 */
				case 'post__in':
				case 'post__not_in':
				case 'post_mime_type':
					$clean_request[ $key ] = $value;
					break;
				case 'parent':
				case 'post_parent':
					$clean_request[ 'post_parent' ] = absint( $value );
					break;
				/*
				 * ['m'] - filter by year and month of post, e.g., 201204
				 */
				case 'author':
				case 'm':
					$clean_request[ $key ] = absint( $value );
					break;
				/*
				 * ['mla_filter_term'] - filter by category or tag ID; -1 allowed
				 */
				case 'mla_filter_term':
					$clean_request[ $key ] = intval( $value );
					break;
				case 'order':
					switch ( $value = strtoupper ($value ) ) {
						case 'ASC':
						case 'DESC':
							$clean_request[ $key ] = $value;
							break;
						default:
							$clean_request[ $key ] = 'ASC';
					}
					break;
				case 'detached':
					if ( '1' == $value )
						$clean_request['detached'] = '1';
					break;
				case 'status':
					if ( 'trash' == $value )
						$clean_request['post_status'] = 'trash';
					break;
				/*
				 * ['s'] - Search Media by one or more keywords
				 * ['mla_search_connector'], ['mla_search_fields'] - Search Media options
				 */
				case 's':
					switch ( substr( $value, 0, 3 ) ) {
						case '>|<':
							$clean_request['debug'] = 'console';
							break;
						case '<|>':
							$clean_request['debug'] = 'log';
							break;
					}
					
					if ( isset( $clean_request['debug'] ) )
						$value = substr( $value, 3 );
				
					$value = stripslashes( trim( $value ) );
					
					if ( ! empty( $value ) )
						$clean_request[ $key ] = $value;
					break;
				case 'mla_search_connector':
				case 'mla_search_fields':
					$clean_request[ $key ] = $value;
					break;
				case 'mla-metakey':
				case 'mla-metavalue':
					$clean_request[ $key ] = stripslashes( $value );
					break;
				case 'meta_query':
					if ( ! empty( $value ) ) {
						if ( is_array( $value ) )
							$clean_request[ $key ] = $value;
						else {
							$clean_request[ $key ] = unserialize( stripslashes( $value ) );
							unset( $clean_request[ $key ]['slug'] );
						} // not array
					}
					break;
				default:
					// ignore anything else in $_REQUEST
			} // switch $key
		} // foreach $raw_request
		
		/*
		 * Pass query parameters to the filters for _execute_list_table_query
		 */
		self::$query_parameters = array( 'use_postmeta_view' => false, 'orderby' => $clean_request['orderby'], 'order' => $clean_request['order'] );
		self::$query_parameters['detached'] = isset( $clean_request['detached'] );
		
		/*
		 * Matching a meta_value to NULL requires a LEFT JOIN to a view and a special WHERE clause
		 * Matching a wildcard pattern requires mainpulating the WHERE clause, too
		 */
		if ( isset( $clean_request['meta_query']['key'] ) ) {
			self::$query_parameters['use_postmeta_view'] = true;
			self::$query_parameters['postmeta_key'] = $clean_request['meta_query']['key'];
			self::$query_parameters['postmeta_value'] = NULL;
			unset( $clean_request['meta_query'] );
		}
		elseif ( isset( $clean_request['meta_query']['patterns'] ) ) {
			self::$query_parameters['patterns'] = $clean_request['meta_query']['patterns'];
			unset( $clean_request['meta_query']['patterns'] );
		}

		if ( isset( $clean_request['debug'] ) ) {
			self::$query_parameters['debug'] = $clean_request['debug'];
			unset( $clean_request['debug'] );
		}
		
		/*
		 * We must patch the WHERE clause if there are leading spaces in the meta_value
		 */
		if ( isset( $clean_request['mla-metavalue'] ) && (' ' == $clean_request['mla-metavalue'][0] ) )
			self::$query_parameters['mla-metavalue'] = $clean_request['mla-metavalue'];

		/*
		 * We will handle keyword search in the mla_query_posts_search_filter.
		 * There must be at least one search field to do a search.
		 */
		if ( isset( $clean_request['s'] ) ) {
			if ( ! empty( $clean_request['mla_search_fields'] ) ) {
				self::$query_parameters['s'] = $clean_request['s'];
				self::$query_parameters['mla_search_connector'] = $clean_request['mla_search_connector'];
				self::$query_parameters['mla_search_fields'] = $clean_request['mla_search_fields'];
				self::$query_parameters['sentence'] = isset( $clean_request['sentence'] );
				self::$query_parameters['exact'] = isset( $clean_request['exact'] );
				
			 	if ( in_array( 'alt-text', self::$query_parameters['mla_search_fields'] ) )
					self::$query_parameters['use_postmeta_view'] = true;
					self::$query_parameters['postmeta_key'] = '_wp_attachment_image_alt';
			} // !empty
			
			unset( $clean_request['s'] );
			unset( $clean_request['mla_search_connector'] );
			unset( $clean_request['mla_search_fields'] );
			unset( $clean_request['sentence'] );
			unset( $clean_request['exact'] );
		}

		/*
		 * We have to handle custom field/post_meta values here
		 * because they need a JOIN clause supplied by WP_Query
		 */
		if ( 'c_' == substr( self::$query_parameters['orderby'], 0, 2 ) ) {
			$option_value = MLAOptions::mla_custom_field_option_value( self::$query_parameters['orderby'] );
			if ( isset( $option_value['name'] ) ) {
				self::$query_parameters['use_postmeta_view'] = true;
				self::$query_parameters['postmeta_key'] = $option_value['name'];
				if ( isset($clean_request['orderby']) )
					unset($clean_request['orderby']);
				if ( isset($clean_request['order']) )
					unset($clean_request['order']);
			}
		} // custom field
		else {
			switch ( self::$query_parameters['orderby'] ) {
				/*
				 * '_wp_attachment_image_alt' is special; we'll handle it in the JOIN and ORDERBY filters
				 */
				case '_wp_attachment_image_alt':
					self::$query_parameters['use_postmeta_view'] = true;
					self::$query_parameters['postmeta_key'] = '_wp_attachment_image_alt';
					if ( isset($clean_request['orderby']) )
						unset($clean_request['orderby']);
					if ( isset($clean_request['order']) )
						unset($clean_request['order']);
					break;
				case '_wp_attached_file':
					$clean_request['meta_key'] = '_wp_attached_file';
					$clean_request['orderby'] = 'meta_value';
					$clean_request['order'] = self::$query_parameters['order'];
					break;
			} // switch $orderby
		}

		/*
		 * Ignore incoming paged value; use offset and count instead
		 */
		if ( ( (int) $count ) > 0 ) {
			$clean_request['offset'] = $offset;
			$clean_request['posts_per_page'] = $count;
		}
		elseif ( ( (int) $count ) == -1 )
			$clean_request['posts_per_page'] = $count;
		
		/*
		 * ['mla_filter_term'] - filter by taxonomy
		 *
		 * cat =  0 is "All Categories", i.e., no filtering
		 * cat = -1 is "No Categories"
		 */
		if ( isset( $clean_request['mla_filter_term'] ) ) {
			if ( $clean_request['mla_filter_term'] != 0 ) {
				$tax_filter =  MLAOptions::mla_taxonomy_support('', 'filter');
				if ( $clean_request['mla_filter_term'] == -1 ) {
					$term_list = get_terms( $tax_filter, array(
						'fields' => 'ids',
						'hide_empty' => false
					) );
					$clean_request['tax_query'] = array(
						array(
							'taxonomy' => $tax_filter,
							'field' => 'id',
							'terms' => $term_list,
							'operator' => 'NOT IN' 
						) 
					);
				}  // mla_filter_term == -1
				else {
					$clean_request['tax_query'] = array(
						array(
							'taxonomy' => $tax_filter,
							'field' => 'id',
							'terms' => array(
								(int) $clean_request['mla_filter_term'] 
							) 
						) 
					);
				} // mla_filter_term != -1
			} // mla_filter_term != 0
			
			unset( $clean_request['mla_filter_term'] );
		} // isset mla_filter_term
		
		if ( isset( $clean_request['mla-tax'] )  && isset( $clean_request['mla-term'] )) {
			$clean_request['tax_query'] = array(
				array(
					'taxonomy' => $clean_request['mla-tax'],
					'field' => 'slug',
					'terms' => $clean_request['mla-term'],
					'include_children' => false 
				) 
			);
			
			unset( $clean_request['mla-tax'] );
			unset( $clean_request['mla-term'] );
		} // isset mla_tax
		
		if ( isset( $clean_request['mla-metakey'] ) && isset( $clean_request['mla-metavalue'] ) ) {
			$clean_request['meta_key'] = $clean_request['mla-metakey'];
			$clean_request['meta_value'] = $clean_request['mla-metavalue'];

			unset( $clean_request['mla-metakey'] );
			unset( $clean_request['mla-metavalue'] );
		} // isset mla_tax
		
		return $clean_request;
	}

	/**
	 * Add filters, run query, remove filters
	 *
	 * @since 0.30
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 *
	 * @return	object	WP_Query object with query results
	 */
	private static function _execute_list_table_query( $request ) {
		global $wpdb, $table_prefix;

		/*
		 * Custom fields are special; we have to use an SQL VIEW to build 
		 * an intermediate table and modify the JOIN to include posts
		 * with no value for the metadata field.
		 */
		if ( self::$query_parameters['use_postmeta_view'] ) {
			$view_name = self::$mla_alt_text_view;
			$key_name = self::$query_parameters['postmeta_key'];
			$table_name = $table_prefix . 'postmeta';

			$result = $wpdb->query(
					"
					CREATE OR REPLACE VIEW {$view_name} AS
					SELECT post_id, meta_value
					FROM {$table_name}
					WHERE {$table_name}.meta_key = '{$key_name}'
					"
			);
		}

		add_filter( 'posts_search', 'MLAData::mla_query_posts_search_filter', 10, 2 ); // $search, &$this
		add_filter( 'posts_join', 'MLAData::mla_query_posts_join_filter' );
		add_filter( 'posts_where', 'MLAData::mla_query_posts_where_filter' );
		add_filter( 'posts_orderby', 'MLAData::mla_query_posts_orderby_filter' );

		if ( isset( self::$query_parameters['debug'] ) ) {
			global $wp_filter;
			$debug_array = array( 'posts_search' => $wp_filter['posts_search'], 'posts_join' => $wp_filter['posts_join'], 'posts_where' => $wp_filter['posts_where'], 'posts_orderby' => $wp_filter['posts_orderby'] );
			
			if ( 'console' == self::$query_parameters['debug'] ) {
				trigger_error( '_execute_list_table_query $wp_filter = ' . var_export( $debug_array, true ), E_USER_WARNING );
			}
			else {
				error_log( 'DEBUG: _execute_list_table_query $wp_filter = ' . var_export( $debug_array, true ), 0 );
			}
		} // debug

		$results = new WP_Query( $request );
		
		if ( isset( self::$query_parameters['debug'] ) ) {
			$debug_array = array( 'request' => $request, 'query_parameters' => self::$query_parameters, 'SQL_request' => $results->request, 'post_count' => $results->post_count, 'found_posts' => $results->found_posts );

			if ( 'console' == self::$query_parameters['debug'] ) {
				trigger_error( '_execute_list_table_query WP_Query = ' . var_export( $debug_array, true ), E_USER_WARNING );
			}
			else {
				error_log( 'DEBUG: _execute_list_table_query WP_Query = ' . var_export( $debug_array, true ), 0 );
			}
		} // debug

		remove_filter( 'posts_orderby', 'MLAData::mla_query_posts_orderby_filter' );
		remove_filter( 'posts_where', 'MLAData::mla_query_posts_where_filter' );
		remove_filter( 'posts_join', 'MLAData::mla_query_posts_join_filter' );
		remove_filter( 'posts_search', 'MLAData::mla_query_posts_search_filter' );

		if ( self::$query_parameters['use_postmeta_view'] ) {
			$result = $wpdb->query( "DROP VIEW {$view_name}" );
		}

		return $results;
	}
	
	/**
	 * Adds a keyword search to the WHERE clause, if required
	 * 
	 * Defined as public because it's a filter.
	 *
	 * @since 0.60
	 *
	 * @param	string	query clause before modification
	 * @param	object	WP_Query object
	 *
	 * @return	string	query clause after keyword search addition
	 */
	public static function mla_query_posts_search_filter( $search_string, &$query_object ) {
		global $table_prefix, $wpdb;
		/*
		 * Process the keyword search argument, if present.
		 */
		$search_clause = '';
		if ( isset( self::$query_parameters['s'] ) ) {
		
			if ( isset( self::$query_parameters['debug'] ) ) {
				$debug_array = array( 's' => self::$query_parameters['s'] );
			} // debug
	
			/*
			 * Interpret a numeric value as the ID of a specific attachment or the ID of a parent post/page
			 */
			if( is_numeric( self::$query_parameters['s'] ) ) {
				$id = absint( self::$query_parameters['s'] );
				$search_clause = ' AND ( ( ' . $wpdb->posts . '.ID = ' . $id . ' ) OR ( ' . $wpdb->posts . '.post_parent = ' . $id . ' ) ) ';
		
				if ( isset( self::$query_parameters['debug'] ) ) {
					$debug_array['search_clause'] = $search_clause;
					$debug_array['search_string'] = $search_string;
					
					if ( 'console' == self::$query_parameters['debug'] ) {
						trigger_error( 'mla_query_posts_search_filter is_numeric = ' . var_export( $debug_array, true ), E_USER_WARNING );
					}
					else {
						error_log( 'DEBUG: mla_query_posts_search_filter is_numeric = ' . var_export( $debug_array, true ), 0 );
					}
				} // debug
		
				return $search_clause;
			}
			
			if (  self::$query_parameters['sentence'] ) {
				$search_terms = array( self::$query_parameters['s'] );
			} else {
				preg_match_all('/".*?("|$)|((?<=[\r\n\t ",+])|^)[^\r\n\t ",+]+/', self::$query_parameters['s'], $matches);
				$search_terms = array_map('_search_terms_tidy', $matches[0]);
			}
			
			$fields = self::$query_parameters['mla_search_fields'];
			$percent = self::$query_parameters['exact'] ? '' : '%';
			$connector = '';
			foreach ( $search_terms as $term ) {
				$term = esc_sql( like_escape( $term ) );
				$inner_connector = '';
				$search_clause .= "{$connector}(";
				
				if ( in_array( 'content', $fields ) ) {
					$search_clause .= "{$inner_connector}({$wpdb->posts}.post_content LIKE '{$percent}{$term}{$percent}')";
					$inner_connector = ' OR ';
				}
				
				if ( in_array( 'title', $fields ) ) {
					$search_clause .= "{$inner_connector}({$wpdb->posts}.post_title LIKE '{$percent}{$term}{$percent}')";
					$inner_connector = ' OR ';
				}
				
				if ( in_array( 'excerpt', $fields ) ) {
					$search_clause .= "{$inner_connector}({$wpdb->posts}.post_excerpt LIKE '{$percent}{$term}{$percent}')";
					$inner_connector = ' OR ';
				}
				
				if ( in_array( 'alt-text', $fields ) ) {
					$view_name = self::$mla_alt_text_view;
					$search_clause .= "{$inner_connector}({$view_name}.meta_value LIKE '{$percent}{$term}{$percent}')";
					$inner_connector = ' OR ';
				}
				
				if ( in_array( 'name', $fields ) ) {
					$search_clause .= "{$inner_connector}({$wpdb->posts}.post_name LIKE '{$percent}{$term}{$percent}')";
				}
				
				$search_clause .= ")";
				$connector = ' ' . self::$query_parameters['mla_search_connector'] . ' ';
			} // foreach

			if ( !empty($search_clause) ) {
				$search_clause = " AND ({$search_clause}) ";
				if ( !is_user_logged_in() )
					$search_clause .= " AND ($wpdb->posts.post_password = '') ";
			}
			
			if ( isset( self::$query_parameters['debug'] ) ) {
				$debug_array['search_clause'] = $search_clause;
				$debug_array['search_string'] = $search_string;
				
				if ( 'console' == self::$query_parameters['debug'] ) {
					trigger_error( 'mla_query_posts_search_filter not numeric = ' . var_export( $debug_array, true ), E_USER_WARNING );
				}
				else {
					error_log( 'DEBUG: mla_query_posts_search_filter not numeric = ' . var_export( $debug_array, true ), 0 );
				}
			} // debug
		} // isset 's'
		
		return $search_clause;
	}

	/**
	 * Adds a JOIN clause, if required, to handle sorting/searching on custom fields or ALT Text
	 * 
	 * Defined as public because it's a filter.
	 *
	 * @since 0.30
	 *
	 * @param	string	query clause before modification
	 *
	 * @return	string	query clause after "LEFT JOIN view ON post_id" item modification
	 */
	public static function mla_query_posts_join_filter( $join_clause ) {
		global $table_prefix;
		/*
		 * '_wp_attachment_image_alt' is special; we have to use an SQL VIEW to
		 * build an intermediate table and modify the JOIN to include posts with
		 * no value for this metadata field.
		 */
		if ( self::$query_parameters['use_postmeta_view'] ) {
			$view_name = self::$mla_alt_text_view;
			$join_clause .= " LEFT JOIN {$view_name} ON ({$table_prefix}posts.ID = {$view_name}.post_id)";
		}

		return $join_clause;
	}

	/**
	 * Adds a WHERE clause for detached items
	 * 
	 * Modeled after _edit_attachments_query_helper in wp-admin/post.php.
	 * Defined as public because it's a filter.
	 *
	 * @since 0.1
	 *
	 * @param	string	query clause before modification
	 *
	 * @return	string	query clause after "detached" item modification
	 */
	public static function mla_query_posts_where_filter( $where_clause ) {
		global $table_prefix;

		/*
		 * WordPress filters meta_value thru trim() - which we must reverse
		 */
		if ( isset( self::$query_parameters['mla-metavalue'] ) ) {
			$where_clause = preg_replace( '/(^.*meta_value AS CHAR\) = \')([^\']*)/', '${1}' . self::$query_parameters['mla-metavalue'], $where_clause );
		}
			
		/*
		 * Matching a NULL meta value 
		 */
		if ( array_key_exists( 'postmeta_value', self::$query_parameters ) && NULL == self::$query_parameters['postmeta_value'] ) {
			$where_clause .= ' AND ' . self::$mla_alt_text_view . '.meta_value IS NULL';
		}
		
		/*
		 * WordPress modifies the LIKE clause - which we must reverse
		 */
		if ( isset( self::$query_parameters['patterns'] ) ) {
			foreach ( self::$query_parameters['patterns'] as $pattern ) {
				$match_clause = '%' . str_replace( '%', '\\\\%', $pattern ) . '%';
				$where_clause = str_replace( "LIKE '{$match_clause}'", "LIKE '{$pattern}'", $where_clause );
			}
		}
			
		/*
		 * Unattached items require some help
		 */
		if ( self::$query_parameters['detached'] )
			$where_clause .= " AND {$table_prefix}posts.post_parent < 1";

		return $where_clause;
	}

	/**
	 * Adds a ORDERBY clause, if required
	 * 
	 * Expands the range of sort options because the logic in WP_Query is limited.
	 * Defined as public because it's a filter.
	 *
	 * @since 0.30
	 *
	 * @param	string	query clause before modification
	 *
	 * @return	string	updated query clause
	 */
	public static function mla_query_posts_orderby_filter( $orderby_clause ) {
		global $table_prefix;

		if ( isset( self::$query_parameters['orderby'] ) ) {
			if ( 'c_' == substr( self::$query_parameters['orderby'], 0, 2 ) ) {
				$orderby = self::$mla_alt_text_view . '.meta_value';
			} // custom field sort
			else {
				switch ( self::$query_parameters['orderby'] ) {
					case 'none':
						$orderby = '';
						$orderby_clause = '';
						break;
					/*
					 * There are two columns defined that end up sorting on post_title,
					 * so we can't use the database column to identify the column but
					 * we actually sort on the database column.
					 */
					case 'title_name':
						$orderby = "{$table_prefix}posts.post_title";
						break;
					/*
					 * The _wp_attached_file meta data value is present for all attachments, and the
					 * sorting on the meta data value is handled by WP_Query
					 */
					case '_wp_attached_file':
						$orderby = '';
						break;
					/*
					 * The _wp_attachment_image_alt value is only present for images, so we have to
					 * use the view we prepared to get attachments with no meta data value
					 */
					case '_wp_attachment_image_alt':
						$orderby = self::$mla_alt_text_view . '.meta_value';
						break;
					default:
						$orderby = "{$table_prefix}posts." . self::$query_parameters['orderby'];
				} // $query_parameters['orderby']
			}
			
			if ( ! empty( $orderby ) )
				$orderby_clause = $orderby . ' ' . self::$query_parameters['order'];
		} // isset

		return $orderby_clause;
	}
	
	/** 
	 * Retrieve an Attachment array given a $post_id
	 *
	 * The (associative) array will contain every field that can be found in
	 * the posts and postmeta tables, and all references to the attachment.
	 * 
	 * @since 0.1
	 * @uses $post WordPress global variable
	 * 
	 * @param	int		The ID of the attachment post
	 * @return	NULL|array NULL on failure else associative array
	 */
	function mla_get_attachment_by_id( $post_id ) {
		global $post;
		static $save_id = -1, $post_data;
		
		if ( $post_id == $save_id )
			return $post_data;
		elseif ( $post_id == -1 ) {
			$save_id = -1;
			return NULL;
		}
		
		$item = get_post( $post_id );
		if ( empty( $item ) ) {
			error_log( "ERROR: mla_get_attachment_by_id(" . $post_id . ") not found", 0 );
			return NULL;
		}
		
		if ( $item->post_type != 'attachment' ) {
			error_log( "ERROR: mla_get_attachment_by_id(" . $post_id . ") wrong post_type: " . $item->post_type, 0 );
			return NULL;
		}
		
		$post_data = (array) $item;
		$post = $item;
		setup_postdata( $item );
		
		/*
		 * Add parent data
		 */
		$post_data = array_merge( $post_data, self::mla_fetch_attachment_parent_data( $post_data['post_parent'] ) );
		
		/*
		 * Add meta data
		 */
		$post_data = array_merge( $post_data, self::mla_fetch_attachment_metadata( $post_id ) );
		
		/*
		 * Add references
		 */
		$post_data['mla_references'] = self::mla_fetch_attachment_references( $post_id, $post_data['post_parent'] );
		
		$save_id = $post_id;
		return $post_data;
	}
	
	/**
	 * Returns information about an attachment's parent, if found
	 *
	 * @since 0.1
	 *
	 * @param	int		post ID of attachment's parent, if any
	 *
	 * @return	array	Parent information; post_date, post_title and post_type
	 */
	public static function mla_fetch_attachment_parent_data( $parent_id ) {
		static $save_id = -1, $parent_data;
		
		if ( $save_id == $parent_id )
			return $parent_data;
			
		$parent_data = array();
		if ( $parent_id ) {
			$parent = get_post( $parent_id );
			if ( isset( $parent->post_date ) )
				$parent_data['parent_date'] = $parent->post_date;
			if ( isset( $parent->post_title ) )
				$parent_data['parent_title'] = $parent->post_title;
			if ( isset( $parent->post_type ) )
				$parent_data['parent_type'] = $parent->post_type;
		}
		
		$save_id = $parent_id;
		return $parent_data;
	}
	
	/**
	 * Finds the value of a key in a possibily nested array structure
	 *
	 * Used primarily to extract fields from the _wp_attachment_metadata custom field.
	 * Could also be used with the ID3 metadata exposed in WordPress 3.6 and later.
	 *
	 * @since 1.30
	 *
	 * @param string key value, e.g. array1.array2.element
	 * @param array PHP nested arrays
	 * @param string format option  'text'|'single'|'export'|'array'|'multi'
	 * @param boolean keep existing values - for 'multi' option
	 *
	 * @return string value matching key(.key ...) or ''
	 */
	public static function mla_find_array_element( $needle, $haystack, $option, $keep_existing = false ) {
		$key_array = explode( '.', $needle );
		if ( is_array( $key_array ) ) {
			foreach( $key_array as $key ) {
				if ( is_array( $haystack ) ) {
					if ( isset( $haystack[ $key ] ) )
						$haystack = $haystack[ $key ];
					else
						$haystack = '';
				}
				else
					$haystack = '';
			} // foreach $key
		}
		else $haystack = '';

		if ( 'single' == $option && is_array( $haystack )) 
			$haystack = current( $haystack );
			
		if ( is_array( $haystack ) ) {
			switch ( $option ) {
				case 'export':
					$haystack = var_export( $haystack, true );
					break;
				case 'multi':
					$haystack[0x80000000] = $option;
					$haystack[0x80000001] = $keep_existing;
					// fallthru
				case 'array':
					return $haystack;
					break;
				default:
					$haystack = implode( ',', $haystack );
			} // $option
		}
			
		return sanitize_text_field( $haystack );
	} // mla_find_array_element
	
	/**
	 * Fetch and filter meta data for an attachment
	 * 
	 * Returns a filtered array of a post's meta data. Internal values beginning with '_'
	 * are stripped out or converted to an 'mla_' equivalent. Array data is replaced with
	 * a string containing the first array element.
	 *
	 * @since 0.1
	 *
	 * @param	int		post ID of attachment
	 *
	 * @return	array	Meta data variables
	 */
	public static function mla_fetch_attachment_metadata( $post_id ) {
		static $save_id = 0, $results;
		
		if ( $save_id == $post_id )
			return $results;
			
		$attached_file = NULL;
		$results = array();
		$post_meta = get_metadata( 'post', $post_id );
		if ( is_array( $post_meta ) ) {
			foreach ( $post_meta as $post_meta_key => $post_meta_value ) {
				if ( empty( $post_meta_key ) )
					continue;
					
				if ( '_' == $post_meta_key{0} ) {
					if ( stripos( $post_meta_key, '_wp_attached_file' ) === 0 ) {
						$key = 'mla_wp_attached_file';
						$attached_file = $post_meta_value[0];
					} elseif ( stripos( $post_meta_key, '_wp_attachment_metadata' ) === 0 ) {
						$key = 'mla_wp_attachment_metadata';
						$post_meta_value = unserialize( $post_meta_value[0] );
					} elseif ( stripos( $post_meta_key, '_wp_attachment_image_alt' ) === 0 ) {
						$key = 'mla_wp_attachment_image_alt';
					} else {
						continue;
					}
				} else {
					if ( stripos( $post_meta_key, 'mla_' ) === 0 )
						$key = $post_meta_key;
					else
						$key = 'mla_item_' . $post_meta_key;
				}
				
//				if ( is_array( $post_meta_value ) && count( $post_meta_value ) == 1 &&  isset( $post_meta_value[0] ) )
				if ( is_array( $post_meta_value ) && count( $post_meta_value ) == 1 )
					$value = array_shift( $post_meta_value );
				else
					$value = $post_meta_value;
				
				$results[ $key ] = $value;
			} // foreach $post_meta

			if ( !empty( $attached_file ) ) {
				$last_slash = strrpos( $attached_file, '/' );
				if ( false === $last_slash ) {
					$results['mla_wp_attached_path'] = '';
					$results['mla_wp_attached_filename'] = $attached_file;
				}
				else {
					$results['mla_wp_attached_path'] = substr( $attached_file, 0, $last_slash + 1 );
					$results['mla_wp_attached_filename'] = substr( $attached_file, $last_slash + 1 );
				}
			} // $attached_file
		} // is_array($post_meta)
		
		$save_id = $post_id;
		return $results;
	}
	
	/**
	 * Find Featured Image and inserted image/link references to an attachment
	 * 
	 * Searches all post and page content to see if the attachment is used 
	 * as a Featured Image or inserted in the post as an image or link.
	 *
	 * @since 0.1
	 *
	 * @param	int	post ID of attachment
	 * @param	int	post ID of attachment's parent, if any
	 *
	 * @return	array	Reference information; see $references array comments
	 */
	public static function mla_fetch_attachment_references( $ID, $parent ) {
		global $wpdb;
		static $save_id = 0, $references, $inserted_in_option = NULL;
		
		if ( $save_id == $ID )
			return $references;
		
		/*
		 * tested_reference	true if any of the four where-used types was processed
		 * found_reference	true if any where-used array is not empty()
		 * found_parent		true if $parent matches a where-used post ID
		 * is_unattached	true if $parent is zero (0)
		 * base_file		relative path and name of the uploaded file, e.g., 2012/04/image.jpg
		 * path				path to the file, relative to the "uploads/" directory, e.g., 2012/04/
		 * file				The name portion of the base file, e.g., image.jpg
		 * files			base file and any other image size files. Array key is path and file name.
		 *					Non-image file value is a string containing file name without path
		 *					Image file value is an array with file name, width and height
		 * features			Array of objects with the post_type and post_title of each post
		 *					that has the attachment as a "Featured Image"
		 * inserts			Array of specific files (i.e., sizes) found in one or more posts/pages
		 *					as an image (<img>) or link (<a href>). The array key is the path and file name.
		 *					The array value is an array with the ID, post_type and post_title of each reference
		 * mla_galleries	Array of objects with the post_type and post_title of each post
		 *					that was returned by an [mla_gallery] shortcode
		 * galleries		Array of objects with the post_type and post_title of each post
		 *					that was returned by a [gallery] shortcode
		 * parent_type		'post' or 'page' or the custom post type of the attachment's parent
		 * parent_title		post_title of the attachment's parent
		 * parent_errors	UNATTACHED, ORPHAN, BAD/INVALID PARENT
		 */
		$references = array(
			'tested_reference' => false,
			'found_reference' => false,
			'found_parent' => false,
			'is_unattached' => ( ( (int) $parent ) === 0 ),
			'base_file' => '',
			'path' => '',
			'file' => '',
			'files' => array(),
			'features' => array(),
			'inserts' => array(),
			'mla_galleries' => array(),
			'galleries' => array(),
			'parent_type' => '',
			'parent_title' => '',
			'parent_errors' => ''
		);
		
		/*
		 * Fill in Parent data
		 */
		$parent_data = self::mla_fetch_attachment_parent_data( $parent );
		if ( isset( $parent_data['parent_type'] ) ) 
			$references['parent_type'] =  $parent_data['parent_type'];
		if ( isset( $parent_data['parent_title'] ) ) 
			$references['parent_title'] =  $parent_data['parent_title'];

		$references['base_file'] = get_post_meta( $ID, '_wp_attached_file', true );
		$attachment_metadata = get_post_meta( $ID, '_wp_attachment_metadata', true );
		$sizes = isset( $attachment_metadata['sizes'] ) ? $attachment_metadata['sizes'] : NULL;
		if ( !empty( $sizes ) ) {
			/* Using the name as the array key ensures each name is added only once */
			foreach ( $sizes as $size ) {
				$references['files'][ $references['path'] . $size['file'] ] = $size;
			}
		}
		
		$references['files'][ $references['base_file'] ] = $references['base_file'];
		$pathinfo = pathinfo($references['base_file']);
		$references['file'] = $pathinfo['basename'];
		if ( '.' == $pathinfo['dirname'] )
			$references['path'] = '';
		else
			$references['path'] = $pathinfo['dirname'] . '/';

		/*
		 * Process the where-used settings option
		 */
		if ('checked' == MLAOptions::mla_get_option( 'exclude_revisions' ) )
			$exclude_revisions = "(post_type <> 'revision') AND ";
		else
			$exclude_revisions = '';

		/*
		 * Accumulate reference test types, e.g.,  0 = no tests, 4 = all tests
		 */
		$reference_tests = 0;

		/*
		 * Look for the "Featured Image(s)", if enabled
		 */
		if ( MLAOptions::$process_featured_in ) {
			$reference_tests++;
			$features = $wpdb->get_results( 
					"
					SELECT post_id
					FROM {$wpdb->postmeta}
					WHERE meta_key = '_thumbnail_id' AND meta_value = {$ID}
					"
			);
			
			if ( !empty( $features ) ) {
				foreach ( $features as $feature ) {
					$feature_results = $wpdb->get_results(
							"
							SELECT post_type, post_title
							FROM {$wpdb->posts}
							WHERE {$exclude_revisions}(ID = {$feature->post_id})
							"
					);
						
					if ( !empty( $feature_results ) ) {
						$references['found_reference'] = true;
						$references['features'][ $feature->post_id ] = $feature_results[0];
					
						if ( $feature->post_id == $parent ) {
							$references['found_parent'] = true;
						}
					} // !empty
				} // foreach $feature
			}
		} // $process_featured_in
		
		/*
		 * Look for item(s) inserted in post_content
		 */
		if ( MLAOptions::$process_inserted_in ) {
			$reference_tests++;

			if ( NULL == $inserted_in_option )
				$inserted_in_option = MLAOptions::mla_get_option( MLAOptions::MLA_INSERTED_IN_TUNING );
				
			if ( 'base' == $inserted_in_option ) {
				$like = like_escape( $references['path'] . $pathinfo['filename'] ) . '%.' . like_escape( $pathinfo['extension'] );
				$inserts = $wpdb->get_results(
					$wpdb->prepare(
						"
						SELECT ID, post_type, post_title 
						FROM {$wpdb->posts}
						WHERE {$exclude_revisions}(
							CONVERT(`post_content` USING utf8 )
							LIKE %s)
						", "%{$like}%"
					)
				);
				
				if ( !empty( $inserts ) ) {
					$references['found_reference'] = true;
					$references['inserts'][ $pathinfo['filename'] ] = $inserts;
					
					foreach ( $inserts as $insert ) {
						if ( $insert->ID == $parent ) {
							$references['found_parent'] = true;
						}
					} // foreach $insert
				} // !empty
			} // process base names
			else {
				foreach ( $references['files'] as $file => $file_data ) {
					$like = like_escape( $file );
					$inserts = $wpdb->get_results(
						$wpdb->prepare(
							"
							SELECT ID, post_type, post_title 
							FROM {$wpdb->posts}
							WHERE {$exclude_revisions}(
								CONVERT(`post_content` USING utf8 )
								LIKE %s)
							", "%{$like}%"
						)
					);
					
					if ( !empty( $inserts ) ) {
						$references['found_reference'] = true;
						$references['inserts'][ $file ] = $inserts;
						
						foreach ( $inserts as $insert ) {
							if ( $insert->ID == $parent ) {
								$references['found_parent'] = true;
							}
						} // foreach $insert
					} // !empty
				} // foreach $file
			} // process intermediate sizes
		} // $process_inserted_in
		
		/*
		 * Look for [mla_gallery] references
		 */
		if ( MLAOptions::$process_mla_gallery_in ) {
			$reference_tests++;
			if ( self::_build_mla_galleries( MLAOptions::MLA_MLA_GALLERY_IN_TUNING, self::$mla_galleries, '[mla_gallery', $exclude_revisions ) ) {
				$galleries = self::_search_mla_galleries( self::$mla_galleries, $ID );
				if ( !empty( $galleries ) ) {
					$references['found_reference'] = true;
					$references['mla_galleries'] = $galleries;
	
					foreach ( $galleries as $post_id => $gallery ) {
						if ( $post_id == $parent ) {
							$references['found_parent'] = true;
						}
					} // foreach $gallery
				} // !empty
				else
					$references['mla_galleries'] = array();
			}
		} // $process_mla_gallery_in
		
		/*
		 * Look for [gallery] references
		 */
		if ( MLAOptions::$process_gallery_in ) {
			$reference_tests++;
			if ( self::_build_mla_galleries( MLAOptions::MLA_GALLERY_IN_TUNING, self::$galleries, '[gallery', $exclude_revisions ) ) {
				$galleries = self::_search_mla_galleries( self::$galleries, $ID );
				if ( !empty( $galleries ) ) {
					$references['found_reference'] = true;
					$references['galleries'] = $galleries;
	
					foreach ( $galleries as $post_id => $gallery ) {
						if ( $post_id == $parent ) {
							$references['found_parent'] = true;
						}
					} // foreach $gallery
				} // !empty
				else
					$references['galleries'] = array();
			}
		} // $process_gallery_in
		
		/*
		 * Evaluate and summarize reference tests
		 */
		$errors = '';
		if ( 0 == $reference_tests ) {
			$references['tested_reference'] = false;
			$errors .= '(NO REFERENCE TESTS)';
		}
		else {
			$references['tested_reference'] = true;
			$suffix = ( 4 == $reference_tests ) ? '' : '?';

			if ( !$references['found_reference'] )
				$errors .= "(ORPHAN{$suffix}) ";
			
			if ( !$references['found_parent'] && !empty( $references['parent_title'] ) )
				$errors .= "(BAD PARENT{$suffix})";
		}
		
		if ( $references['is_unattached'] )
			$errors .= '(UNATTACHED) ';
		elseif ( empty( $references['parent_title'] ) ) 
			$errors .= '(INVALID PARENT) ';

		$references['parent_errors'] = trim( $errors );
		
		$save_id = $ID;
		return $references;
	}
	
	/**
	 * Objects containing [gallery] shortcodes
	 *
	 * This array contains all of the objects containing one or more [gallery] shortcodes
	 * and array(s) of which attachments each [gallery] contains. The arrays are built once
	 * each page load and cached for subsequent calls.
	 *
	 * The outer array is keyed by post_id. It contains an associative array with:
	 * ['parent_title'] post_title of the gallery parent, 
	 * ['parent_type'] 'post' or 'page' or the custom post_type of the gallery parent,
	 * ['results'] array ( ID => ID ) of attachments appearing in ANY of the parent's galleries.
	 * ['galleries'] array of [gallery] entries numbered from one (1), containing:
	 * galleries[X]['query'] contains a string with the arguments of the [gallery], 
	 * galleries[X]['results'] contains an array ( ID ) of post_ids for the objects in the gallery.
	 *
	 * @since 0.70
	 *
	 * @var	array
	 */
	private static $galleries = null;

	/**
	 * Objects containing [mla_gallery] shortcodes
	 *
	 * This array contains all of the objects containing one or more [mla_gallery] shortcodes
	 * and array(s) of which attachments each [mla_gallery] contains. The arrays are built once
	 * each page load and cached for subsequent calls.
	 *
	 * @since 0.70
	 *
	 * @var	array
	 */
	private static $mla_galleries = null;

	/**
	 * Invalidates the $mla_galleries or $galleries array and cached values
	 *
	 * @since 1.00
	 *
	 * @param	string name of the gallery's cache/option variable
	 *
	 * @return	void
	 */
	public static function mla_flush_mla_galleries( $option_name ) {
		delete_transient( MLA_OPTION_PREFIX . 't_' . $option_name );

		switch ( $option_name ) {
			case MLAOptions::MLA_GALLERY_IN_TUNING:
				self::$galleries = null;
				break;
			case MLAOptions::MLA_MLA_GALLERY_IN_TUNING:
				self::$mla_galleries = null;
				break;
			default:
				//	ignore everything else
		} // switch
	}
	
	/**
	 * Invalidates $mla_galleries and $galleries arrays and cached values after post, page or attachment updates
	 *
	 * @since 1.00
	 *
	 * @param	integer ID of post/page/attachment; not used at this time
	 *
	 * @return	void
	 */
	public static function mla_save_post_action( $post_id ) {
		self::mla_flush_mla_galleries( MLAOptions::MLA_GALLERY_IN_TUNING );
		self::mla_flush_mla_galleries( MLAOptions::MLA_MLA_GALLERY_IN_TUNING );
	}
	
	/**
	 * Builds the $mla_galleries or $galleries array
	 *
	 * @since 0.70
	 *
	 * @param	string name of the gallery's cache/option variable
	 * @param	array by reference to the private static galleries array variable
	 * @param	string the shortcode to be searched for and processed
	 * @param	boolean true to exclude revisions from the search
	 *
	 * @return	boolean true if the galleries array is not empty
	 */
	private static function _build_mla_galleries( $option_name, &$galleries_array, $shortcode, $exclude_revisions ) {
		global $wpdb, $post;

		if ( is_array( $galleries_array ) ) {
			if ( ! empty( $galleries_array ) ) {
				return true;
			} else {
				return false;
			}
		}

		$option_value = MLAOptions::mla_get_option( $option_name );
		if ( 'disabled' == $option_value )
			return false;
		elseif ( 'cached' == $option_value ) {
			$galleries_array = get_transient( MLA_OPTION_PREFIX . 't_' . $option_name );
			if ( is_array( $galleries_array ) ) {
				if ( ! empty( $galleries_array ) ) {
					return true;
				} else {
					return false;
				}
			}
			else
				$galleries_array = NULL;
		} // cached
		
		/*
		 * $galleries_array is null, so build the array
		 */
		$galleries_array = array();
		
		if ( $exclude_revisions )
			$exclude_revisions = "(post_type <> 'revision') AND ";
		else
			$exclude_revisions = '';
		
		$like = like_escape( $shortcode );
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT ID, post_type, post_title, post_content
				FROM {$wpdb->posts}
				WHERE {$exclude_revisions}(
					CONVERT(`post_content` USING utf8 )
					LIKE %s)
				", "%{$like}%"
			)
		);

		if ( empty( $results ) )
			return false;
			
		foreach ( $results as $result ) {
			$count = preg_match_all( "/\\{$shortcode}([^\\]]*)\\]/", $result->post_content, $matches, PREG_PATTERN_ORDER );
			if ( $count ) {
				$result_id = $result->ID;
				$galleries_array[ $result_id ]['parent_title'] = $result->post_title;
				$galleries_array[ $result_id ]['parent_type'] = $result->post_type;
				$galleries_array[ $result_id ]['results'] = array();
				$galleries_array[ $result_id ]['galleries'] = array();
				$instance = 0;
				
				foreach ( $matches[1] as $index => $match ) {
					/*
					 * Filter out shortcodes that are not an exact match
					 */
					if ( empty( $match ) || ( ' ' == substr( $match, 0, 1 ) ) ) {
						$instance++;
						$galleries_array[ $result_id ]['galleries'][ $instance ]['query'] = trim( $matches[1][$index] );
						$galleries_array[ $result_id ]['galleries'][ $instance ]['results'] = array();
						
						$post = $result; // set global variable for mla_gallery_shortcode
						$attachments = MLAShortcodes::mla_get_shortcode_attachments( $result_id, $galleries_array[ $result_id ]['galleries'][ $instance ]['query'] );

						if ( is_string( $attachments ) ) {
							trigger_error( htmlentities( sprintf( '(%1$s) %2$s (ID %3$d) query "%4$s" failed, returning "%5$s"', $result->post_type, $result->post_title, $result->ID, $galleries_array[ $result_id ]['galleries'][ $instance ]['query'], $attachments) ), E_USER_WARNING );
						}
						elseif ( ! empty( $attachments ) )
							foreach ( $attachments as $attachment ) {
								$galleries_array[ $result_id ]['results'][ $attachment->ID ] = $attachment->ID;
								$galleries_array[ $result_id ]['galleries'][ $instance ]['results'][] = $attachment->ID;
							} // foreach $attachment
					} // exact match
				} // foreach $match
			} // if $count
		} // foreach $result

	/*
	 * Maybe cache the results
	 */	
	if ( 'cached' == $option_value ) {
		set_transient( MLA_OPTION_PREFIX . 't_' . $option_name, $galleries_array, 900 ); // fifteen minutes
	}

	return true;
	}
	
	/**
	 * Search the $mla_galleries or $galleries array
	 *
	 * @since 0.70
	 *
	 * @param	array	by reference to the private static galleries array variable
	 * @param	int		the attachment ID to be searched for and processed
	 *
	 * @return	array	All posts/pages with one or more galleries that include the attachment.
	 * 					The array key is the parent_post ID; each entry contains post_title and post_type.
	 */
	private static function _search_mla_galleries( &$galleries_array, $attachment_id ) {
		$gallery_refs = array();
		if ( ! empty( $galleries_array ) ) {
			foreach ( $galleries_array as $parent_id => $gallery ) {
				if ( in_array( $attachment_id, $gallery['results'] ) ) {
					$gallery_refs[ $parent_id ] = array ( 'post_title' => $gallery['parent_title'], 'post_type' => $gallery['parent_type'] );
				}
			} // foreach gallery
		} // !empty
		
		return $gallery_refs;
	}
		
	/**
	 * Array of PDF indirect objects
	 *
	 * This array contains all of the indirect object offsets and lengths
	 *
	 * @since 1.4x
	 *
	 * @var	array
	 */
	private static $pdf_indirect_objects = NULL;

	/**
	 * Build an array of indirect object definitions
	 * 
	 * Creates the array of indirect object offsets and lengths
	 * @since 1.4x
	 *
	 * @param	string	The entire PDF document, passsed by reference
	 *
	 * @return	void
	 */
	private static function _build_pdf_indirect_objects( &$string ) {
		if ( ! is_null( self::$pdf_indirect_objects ) )
			return;
			
		$match_count = preg_match_all( '!(\d+)\\h+(\d+)\\h+obj|endobj|stream(\x0D\x0A|\x0A)|endstream!', $string, $matches, PREG_OFFSET_CAPTURE );
error_log( '_build_pdf_indirect_objects $match_count = ' . var_export( $match_count, true), 0 );
//error_log( '_build_pdf_indirect_objects $matches = ' . var_export( $matches, true), 0 );
//if( 100 < $match_count ) $match_count = 100;

		self::$pdf_indirect_objects = array();
		$object_level = 0;
		$is_stream = false;
		for ( $index = 0; $index < $match_count; $index++ ) {
//error_log( "_build_pdf_indirect_objects \$matches[0][ {$index} ][0] = " . var_export( $matches[0][ $index ][0], true), 0 );
//error_log( "_build_pdf_indirect_objects \$matches[0][ {$index} ][1] = " . var_export( $matches[0][ $index ][1], true), 0 );
			if ( $is_stream ) {
				if ( 'endstream' == substr( $matches[0][ $index ][0], 0, 9 ) ) {
					$is_stream = false;
				}
			}
			elseif ( 'endobj' == substr( $matches[0][ $index ][0], 0, 6 ) ) {
				$object_level--;
				$object_entry['/length'] = $matches[0][ $index ][1] - $object_entry['start'];
				self::$pdf_indirect_objects[ ($object_entry['number'] * 1000) + $object_entry['generation'] ] = $object_entry;
			}
			elseif ( 'obj' == substr( $matches[0][ $index ][0], -3 ) ) {
				$object_level++;
				$object_entry = array( 
					'number' => $matches[1][ $index ][0],
					'generation' => $matches[2][ $index ][0],
					'start' => $matches[0][ $index ][1] + strlen( $matches[0][ $index ][0] )
					);
			}
			elseif ( 'stream' == substr( $matches[0][ $index ][0], 0, 6 ) ) {
				$is_stream = true;
			}
			else
				error_log( 'ERROR: _build_pdf_indirect_objects bad value $index = ' . $index, 0 );
		} // for each match
//error_log( '_build_pdf_indirect_objects self::$pdf_indirect_objects = ' . var_export( self::$pdf_indirect_objects, true), 0 );
	}
		
	/**
	 * Find the offset, length and contents of an indirect object containing a dictionary
	 * 
	 * @since 1.4x
	 *
	 * @param	string	The entire PDF document, passsed by reference
	 * @param	integer	The object number
	 * @param	integer	The object generation number; default zero (0)
	 *
	 * @return	mixed	NULL on failure else array( 'start' => offset in the file, 'length' => object length, 'content' => dictionary contents )
	 */
	private static function _find_pdf_indirect_dictionary( &$string, $object, $generation = 0 ) {
//error_log( '_find_pdf_indirect_dictionary $object = ' . var_export( $object, true), 0 );
		/*
		 * Match the object header
		 */
//		$pattern = sprintf( '![\\x00-\\x20]+%1$d\\h+%2$d\\h+obj[\\x00-\\x20]+(<<)!', $object, $generation );
		$pattern = sprintf( '![\\x00-\\x20]+%1$d\\h+%2$d\\h+obj[\\x00-\\x20]*(<<)!', $object, $generation );
		$match_count = preg_match( $pattern, $string, $matches, PREG_OFFSET_CAPTURE );
//error_log( '_find_pdf_indirect_dictionary header $match_count = ' . var_export( $match_count, true), 0 );
//error_log( '_find_pdf_indirect_dictionary header $matches = ' . var_export( $matches, true), 0 );

		if ($match_count) {
			$start = $matches[1][1];
			/*
			 * Match the object trailer
			 */
			$pattern = '!>>[\\x00-\\x20]+endobj!';
			$match_count = preg_match( $pattern, $string, $matches, PREG_OFFSET_CAPTURE, $start );
//error_log( '_find_pdf_indirect_dictionary trailer $match_count = ' . var_export( $match_count, true), 0 );
//error_log( '_find_pdf_indirect_dictionary trailer $matches = ' . var_export( $matches, true), 0 );
			 
			if ($match_count) {
				$results = array( 'start' => $start, 'length' => ($matches[0][1] + 2) - $start );
//error_log( '_find_pdf_indirect_dictionary $results = ' . var_export( $results, true), 0 );
				$results['content'] = substr( $string, $results['start'], $results['length'] );
//error_log( '_find_pdf_indirect_dictionary content = ' . "\r\n" . var_export( self::_hex_dump( $results['content'], $results['length'], 32 ), true), 0 );
				return $results;
			} // found trailer
		} // found header

		return NULL; 
	}
		
	/**
	 * Parse a ISO 8601 Timestamp
	 * 
	 * @since 1.4x
	 *
	 * @param	string	ISO string of the form YYYY-MM-DDTHH:MM:SS-HH:MM (inc time zone)
	 *
	 * @return	string	formatted date string YYYY-MM-DD HH:mm:SS
	 */
	private static function _parse_iso8601_date( $source_string ) {
		if ( 1 == preg_match( '/^\\d\\d\\d\\d-\\d\\d-\\d\\dT\\d\\d:\\d\\d:\\d\\d-\\d\\d:\\d\\d/', $source_string ) )
			return sprintf( '%1$s-%2$s-%3$s %4$s:%5$s:%6$s',
				substr( $source_string, 0, 4),
				substr( $source_string, 5, 2),
				substr( $source_string, 8, 2),
				substr( $source_string, 11, 2),
				substr( $source_string, 14, 2),
				substr( $source_string, 17, 2) );
		else
			return $source_string;
	}
		
	/**
	 * Parse a PDF date string
	 * 
	 * @since 1.4x
	 *
	 * @param	string	PDF date string of the form D:YYYYMMDDHHmmSSOHH'mm
	 *
	 * @return	string	formatted date string YYYY-MM-DD HH:mm:SS
	 */
	private static function _parse_pdf_date( $source_string ) {
		if ( 'D:' == substr( $source_string, 0, 2) && ctype_digit( substr( $source_string, 2, 12 ) ) ) 
			return sprintf( '%1$s-%2$s-%3$s %4$s:%5$s:%6$s',
				substr( $source_string, 2, 4),
				substr( $source_string, 6, 2),
				substr( $source_string, 8, 2),
				substr( $source_string, 10, 2),
				substr( $source_string, 12, 2),
				substr( $source_string, 14, 2) );
		else
			return $source_string;
	}
		
	/**
	 * Parse a PDF Unicode (16-bit Big Endian) object
	 * 
	 * @since 1.4x
	 *
	 * @param	string	PDF string of 16-bit characters
	 *
	 * @return	string	UTF-8 encoded string
	 */
	private static function _parse_pdf_UTF16BE( &$source_string ) {
		$output = '';
		for ($index = 2; $index < strlen( $source_string ); ) {
			$value = ( ord( $source_string[ $index++ ] ) << 8 ) + ord( $source_string[ $index++ ] );
 			if ( $value < 0x80 )
				$output .= chr( $value );
			elseif ( $value < 0x100 )
				$output .= self::$utf8_chars[ $value - 0x80 ];
			else {
				$output .= '.'; // TODO encode the rest
			}
		}

		return $output;
	}
		
	/**
	 * Parse a PDF string object
	 * 
	 * Returns an array with one dictionary entry. The array also has a '/length' element containing
	 * the number of bytes occupied by the string in the source string, including the enclosing parentheses. 
	 *
	 * @since 1.4x
	 *
	 * @param	string	data within which the string occurs
	 * @param	integer	offset within the source string of the opening '(' character.
	 *
	 * @return	array	( key => array( 'type' => type, 'value' => value, '/length' => length ) ) for the string
	 */
	private static function _parse_pdf_string( &$source_string, $offset ) {
		if ( '(' != $source_string[ $offset ] )
			return array( 'type' => 'unknown', 'value' => '', '/length' => 0 );

		/*
		 * Brute force, here we come...
		 */
		$output = '';
		$level = 0;
		$in_string = true;
		$index = $offset + 1;
		while ( $in_string ) {
			$byte = $source_string[ $index++ ];
			if ( '\\' == $byte ) {
				switch ( $source_string[ $index ] ) {
					case chr( 0x0A ):
						if ( chr( 0x0D ) == $source_string[ $index + 1 ] )
							$index++;
						break;
					case chr( 0x0D ):
						if ( chr( 0x0A ) == $source_string[ $index + 1 ] )
							$index++;
						break;
					case 'n':
						$output .= chr( 0x0A );
						break;
					case 'r':
						$output .= chr( 0x0D );
						break;
					case 't':
						$output .= chr( 0x09 );
						break;
					case 'b':
						$output .= chr( 0x08 );
						break;
					case 'f':
						$output .= chr( 0x0C );
						break;
					case '(':
						$output .= '(';
						break;
					case ')':
						$output .= ')';
						break;
					case '\\':
						$output .= '\\';
						break;
					default: // should be a 1- to 3-digit octal value
						for ($digit_count = 0; ++$digit_count < 4; )
							if ( ! ctype_digit( $source_string[ $index + $digit_count ] ) )
								break;

						if ( $digit_count ) {
							$output .= chr( octdec( substr( $source_string, $index, $digit_count ) ) );
							$index += $digit_count - 1;
						}
						else // invalid! Back up and just discard the backslash
							$index--;
				} // switch
				
				$index++;
			} // REVERSE SOLIDUS
			else {
				if ( '(' == $byte )
					$level++;
				elseif ( ')' == $byte ) {
					if ( 0 == $level-- ) {
						$in_string = false;
						continue;
					}
				}
					
				$output .= $byte;
			} // just another 8-bit value, but check for balanced parentheses
		} // $in_string
		
		return array( 'type' => 'string', 'value' => $output, '/length' => $index - $offset );
	}
		
	/**
	 * Parse a PDF dictionary object
	 * 
	 * Returns an array of dictionary contents, classified by object type: boolean, numeric, string, hex (string),
	 * indirect (object), name, array, dictionary, stream, and null.
	 * The array also has a '/length' element containing the number of bytes occupied by the
	 * dictionary in the source string, excluding the enclosing delimiters, if passed in.
	 * @since 1.4x
	 *
	 * @param	string	data within which the string occurs
	 * @param	integer	offset within the source string of the opening '<<' characters or the first content character.
	 *
	 * @return	array	( '/length' => length, key => array( 'type' => type, 'value' => value ) ) for each dictionary field
	 */
	private static function _parse_pdf_dictionary( &$source_string, $offset ) {
//error_log( '_parse_pdf_dictionary $source_string = '. var_export( $source_string, true ), 0 );
		if ( '<<' == substr( $source_string, $offset, 2 ) ) {
			$offset += 2;
			$delimiter_length = 4;
		}
		else
			$delimiter_length = 0;

			
		$dictionary = array();
		// \x00-\x20 for whitespace
		// \(|\)|\<|\>|\[|\]|\{|\}|\/|\% for delimiters
		$match_count = preg_match_all( '!/([^\x00-\x20|\(|\)|\<|\>|\[|\]|\{|\}|\/|\%]*)([\x00-\x20]*)!', substr( $source_string, $offset ), $matches, PREG_OFFSET_CAPTURE );
//error_log( '_parse_pdf_dictionary $matches = '. var_export( $matches, true ), 0 );

		$end_data = -1;
		for ( $match_index = 0; $match_index < $match_count; $match_index++ ) {
			$name = $matches[1][ $match_index ][0];
			$value_start = $offset + $matches[2][ $match_index ][1] + strlen( $matches[2][ $match_index ][0] );
			/*
			 * Skip over false matches within a string or nested dictionary
			 */
			if ( $value_start < $end_data )
				continue;
			
			$end_data = -1;
			$value_count = preg_match(
				'!(\/?[^\/\x0D\x0A]*)!',
				substr( $source_string, $value_start ), $value_matches, PREG_OFFSET_CAPTURE );

			if ( 1 == $value_count ) {
				$value = $value_matches[0][0];
				$length = strlen( $value );
				$dictionary[ $name ]['value'] = $value;
				if ( ! isset( $value[0] ) ) {
					error_log( "ERROR: _parse_pdf_dictionary bad value [ {$name} ] dump = " . var_export( self::_hex_dump( $value, 32, 16 ), true ), 0 );
					continue;
				}
				
				if ( in_array( $value, array( 'true', 'false' ) ) )
					$dictionary[ $name ]['type'] = 'boolean';
				elseif ( is_numeric( $value ) )
					$dictionary[ $name ]['type'] = 'numeric';
				elseif ( '(' == $value[0] ) {
					$dictionary[ $name ] = self::_parse_pdf_string( $source_string, $value_start );
					$end_data = $value_start + $dictionary[ $name ]['/length'];
					unset( $dictionary[ $name ]['/length'] );
				}
				elseif ( '<' == $value[0] ) {
					if ( '<' == $value[1] ) {
						$dictionary[ $name ]['value'] = self::_parse_pdf_dictionary( $source_string, $value_start );
						$dictionary[ $name ]['type'] = 'dictionary';
						$length = $dictionary[ $name ]['value']['/length'];
						unset( $dictionary[ $name ]['value']['/length'] );
						$end_data = $value_start + $length;
					}
					else
						$dictionary[ $name ]['type'] = 'hex';
				}
				elseif ( '/' == $value[0] ) {
					$dictionary[ $name ]['value'] = substr( $value, 1 );
					$dictionary[ $name ]['type'] = 'name';
					$match_index++; // Skip to the next key
				}
				elseif ( '[' == $value[0] ) {
					$dictionary[ $name ]['type'] = 'array';
					$array_length = strpos( $source_string, ']', $value_start ) - ($value_start + 1);
					$dictionary[ $name ]['value'] = substr( $source_string, $value_start + 1, $array_length );
					$end_data = 2 + $value_start + $array_length;
				}
				elseif ( 'null' == $value )
					$dictionary[ $name ]['type'] = 'null';
				elseif ( 'stream' == substr( $value, 0, 6 ) )
					$dictionary[ $name ]['type'] = 'stream';
				else {
					$object_count = preg_match( '!(\d+)\h+(\d+)\h+R!', $value, $object_matches );
					if ( 1 == $object_count ) {
						$dictionary[ $name ]['type'] = 'indirect';
						$dictionary[ $name ]['object'] = $object_matches[1];
						$dictionary[ $name ]['generation'] = $object_matches[2];
					}
					else {
						$dictionary[ $name ]['type'] = 'unknown';
					}
				}
			}
			else {
				$dictionary[ $matches[1][ $match_index ][0] ] = array( 'value' => '' );
				$dictionary[ $matches[1][ $match_index ][0] ]['type'] = 'nomatch';
			}
		} // foreach match

		$dictionary['/length'] = $value_start + $length - $delimiter_length;
//$tail = substr( $source_string, $offset );
//error_log( '_parse_pdf_dictionary content = ' . "\r\n" . var_export( self::_hex_dump( $tail, $dictionary['/length'], 32 ), true), 0 );
		return $dictionary;
	}
		
	/**
	 * Parse an XMP object
	 * 
	 * Returns an array of dictionary contents, classified by object type: boolean, numeric, string, hex (string),
	 * indirect (object), name, array, dictionary, stream, and null.
	 * The array also has a '/length' element containing the number of bytes occupied by the
	 * dictionary in the source string, excluding the enclosing delimiters, if passed in.
	 * @since 1.4x
	 *
	 * @param	string	data within which the string occurs
	 * @param	integer	offset within the source string of the opening '<<' characters or the first content character.
	 *
	 * @return	array	( '/length' => length, key => array( 'type' => type, 'value' => value ) ) for each dictionary field
	 */
	private static function _parse_xmp_metadata( &$source_string, $offset ) {
		$start_tag = strpos( $source_string, '<x:xmpmeta', $offset );
		if ( false === $start_tag )
			return NULL;
			
		$end_tag = strpos( $source_string, '</x:xmpmeta>', $offset + $start_tag );
		if ( false === $start_tag )
			return NULL;

//error_log( '_parse_xmp_metadata XMP = ' . var_export( "<?xml version='1.0'? >\n" . substr($source_string, $start_tag, ( $end_tag + 12 ) - $start_tag ), true ), 0 );
		$xmp_string = "<?xml version='1.0'?>\n" . substr($source_string, $start_tag, ( $end_tag + 12 ) - $start_tag );
		$xmp_values = array();
		$xml_parser = xml_parser_create('UTF-8');
		if ( xml_parser_set_option( $xml_parser, XML_OPTION_SKIP_WHITE, 0 ) && xml_parser_set_option( $xml_parser, XML_OPTION_CASE_FOLDING, 0 ) ) {
			if (xml_parse_into_struct( $xml_parser, $xmp_string, $xmp_values ) == 0)
				error_log( 'ERROR: _parse_xmp_metadata xml_parse_into_struct failed.' );
		}
		else
			error_log( 'ERROR: _parse_xmp_metadata set option failed.' );

		xml_parser_free($xml_parser);
//error_log( '_parse_xmp_metadata $xmp_string = ' . var_export( $xmp_string, true ), 0 );
//error_log( '_parse_xmp_metadata $xml_parser xmp_values = ' . var_export( $xmp_values, true ), 0 );

		if ( empty( $xmp_values ) )
			return NULL;
		
		$results = array();
		$xmlns = array();
		$array_name = '';
		$array_index = -1;
		foreach ( $xmp_values as $value ) {
			$language = 'x-default';
			if ( isset( $value['attributes'] ) ) {
				foreach( $value['attributes'] as $att_tag => $att_value ) {
					if ( 'xmlns:' == substr( $att_tag, 0, 6 ) )
						$xmlns[ substr( $att_tag, 6 ) ] = $att_value;
					elseif ( 'x:xmptk' == $att_tag )
						$results['xmptk'] = $att_value;
					elseif ( 'xml:lang' == $att_tag )
						$language = $att_value;
				}
			} // attributes
			
			switch ( $value['tag'] ) {
				case 'x:xmpmeta':
				case 'rdf:RDF':
				case 'rdf:Description':
				case 'rdf:ID':
				case 'rdf:nodeID':
					break;
				case 'rdf:li':
					if ( $value['type'] == 'complete' ) {
						if ( 'x-default' != $language )
							break;
							
						if ( ! empty ( $array_name ) ) {
							if ( isset( $value['value'] ) )
								$results[ $array_name ][ $array_index++ ] = $value['value'];
							else
								$results[ $array_name ][ $array_index++ ] = '';
						}
					} // complete
					
					break;
				case 'rdf:Seq':
				case 'rdf:Bag':
				case 'rdf:Alt':
					switch ( $value['type'] ) {
						case 'open':
							$array_index = 0;
							break;
						case 'close':
							$array_index = -1;
					}
					
					break;
				default:
					switch ( $value['type'] ) {
						case 'open':
							$array_name = $value['tag'];
							break;
						case 'close':
							$array_name = '';
							break;
						case 'complete':
							if ( isset( $value['attributes'] ) )
								$results[ $value['tag'] ] = $value['attributes'];
							elseif ( isset( $value['value'] ) )
								$results[ $value['tag'] ] = $value['value'];
							else
								$results[ $value['tag'] ] = '';
					} // type
			} // switch tag
		} // foreach value

		/*
		 * Parse "namespace:name" names into arrays of simple names
		 */
		$namespace_arrays = array();
		foreach ( $results as $key => $value ) {
			if ( is_string( $value ) )
				$value = self::_parse_iso8601_date( self::_parse_pdf_date( $value ) );
				
			if ( false !== ($colon = strpos( $key, ':' ) ) ) {
				$array_name = substr( $key, 0, $colon );
				$array_index = substr( $key, $colon + 1 );
				$namespace_arrays[ $array_name ][ $array_index ] = $value;

				if ( ! isset( $results[ $array_index ] ) ) {
					if ( is_array( $value ) && 1 == count( $value ) && isset( $value[0] ) ) 
						$results[ $array_index ] = $value[0];
					else
						$results[ $array_index ] = $value;
				}

				unset( $results[ $key ] );
			}
		}
		
		/*
		 * Try to populate all the PDF-standard keys (except Trapped)
		 * Title - The document's title
		 * Author - The name of the person who created the document
		 * Subject - The subject of the document
		 * Keywords - Keywords associated with the document
		 * Creator - the name of the conforming product that created the original document
		 * Producer - the name of the conforming product that converted it to PDF
		 * CreationDate - The date and time the document was created
		 * ModDate - The date and time the document was most recently modified
		 */
		if ( ! isset( $results['Title'] ) ) {
			if ( isset( $namespace_arrays['dc']['title'] ) ) // HANDLE ARRAY
				$results['Title'] = implode( ',', $namespace_arrays['dc']['title'] );
		}
		
		if ( ! isset( $results['Author'] ) ) {
			if ( isset( $namespace_arrays['dc']['creator'] ) ) // HANDLE ARRAY
				$results['Author'] = implode( ',', $namespace_arrays['dc']['creator'] );
		}
		
		if ( ! isset( $results['Subject'] ) ) {
		}
		
		if ( ! isset( $results['Keywords'] ) ) {
		}
		
		if ( ! isset( $results['Creator'] ) ) {
			if ( isset( $namespace_arrays['xmp']['CreatorTool'] ) )
				$results['Creator'] = $namespace_arrays['xmp']['CreatorTool'];
		}
		
		if ( ! isset( $value['Producer'] ) ) {
		}
		
		if ( ! isset( $results['CreationDate'] ) ) {
			if ( isset( $namespace_arrays['xmp']['CreateDate'] ) )
				$results['CreationDate'] = $namespace_arrays['xmp']['CreateDate'];
		}
		
		if ( ! isset( $results['ModDate'] ) ) {
			if ( isset( $namespace_arrays['xmp']['ModifyDate'] ) )
				$results['ModDate'] = $namespace_arrays['xmp']['ModifyDate'];
		}
		
		if ( ! empty( $xmlns ) )
			$results['xmlns'] = $xmlns;

		$results = array_merge( $results, $namespace_arrays );
error_log( '_parse_xmp_metadata $results = ' . var_export( $results, true ), 0 );
		return $results;
	}
		
	/**
	 * Extract Metadata from a PDF file
	 * 
	 * @since 1.4x
	 *
	 * @param	string	full path to the desired file
	 *
	 * @return	array	( key => value ) for each metadata field, in string format
	 */
	private static function _extract_pdf_metadata( $file_name ) {
$microtime_start = microtime( true );
		$metadata = array();
		self::$pdf_indirect_objects = NULL;

		$pdf = file_get_contents( $file_name, true );
		if ( $pdf == false ) {
			error_log( 'ERROR: PDF file not found ' . var_export( $path, true ), 0 );
			return $metadata;
		}

//self::_parse_xmp_metadata( $pdf, 0);
//self::_build_pdf_indirect_objects( $pdf );
//error_log( '_extract_pdf_metadata start = ' . "\r\n" . var_export( self::_hex_dump( substr( $pdf, 0, strlen( $pdf ) ), strlen( $pdf ), 32 ), true ) . "\r\n", 0 );
		
		$header = substr( $pdf, 0, 8 );
		if ( '%PDF-' == substr( $header, 0, 5 ) ) {
			$metadata['PDF_Version'] = substr( $header, 1, 7 );
			$metadata['PDF_VersionNumber'] = substr( $header, 5, 3 );
		}

		$match_count = preg_match_all( '/[\r|\n]+trailer[\r|\n]+/', $pdf, $matches, PREG_OFFSET_CAPTURE );
//error_log( '_extract_pdf_metadata trailer $match_count = ' . var_export( $match_count, true ), 0 );
//error_log( '_extract_pdf_metadata trailer $matches = ' . "\r\n" . var_export( $matches, true ) . "\r\n", 0 );
		if ( 0 == $match_count )
			$matches = array( 0 => array() );
		
		foreach( $matches[0] as $match_index => $match ) {	
$tail = substr( $pdf, (integer) $match[1] );
//error_log( "_extract_pdf_metadata tail[{$match_index}] start = " . "\r\n" . var_export( self::_hex_dump( $tail, 512, 32 ), true ) . "\r\n", 0 );
$xref_count = preg_match_all( '/[\r|\n]+startxref[\r|\n]+(.*)[\r|\n]+/', $tail, $xref_matches ); //, PREG_OFFSET_CAPTURE );
//error_log( '_extract_pdf_metadata $xref_count = '. var_export( $xref_count, true ), 0 );
//error_log( '_extract_pdf_metadata $xref_matches = '. var_export( $xref_matches, true ), 0 );
//error_log( '_extract_pdf_metadata xref start = ' . "\r\n" . var_export( self::_hex_dump( substr( $pdf, $xref_matches[1][0] ), 512, 32 ), true ) . "\r\n", 0 );

//			$match_count = preg_match_all( '/[\r|\n]+<<(.*)>>[\r|\n]+/', $tail, $matches ); //, PREG_OFFSET_CAPTURE );
			$match_count = preg_match( '/[\r|\n]+<<(.*)>>[\r|\n]+/', $pdf, $matches, 0, $match[1] ); //, PREG_OFFSET_CAPTURE );
//error_log( "_extract_pdf_metadata trailer dictionary[{$match_index}] \$match_count = " . var_export( $match_count, true ), 0 );
//error_log( "_extract_pdf_metadata trailer dictionary[{$match_index}] \$matches = \r\n" . var_export( $matches, true ) . "\r\n", 0 );

			if ( 0 < $match_count ) {
//				$dictionary = self::_parse_pdf_dictionary( $matches[1][ $match_count - 1 ], 0 );
				$dictionary = self::_parse_pdf_dictionary( $matches[0], 0 );
error_log( "_extract_pdf_metadata trailer dictionary [{$match_index}] = ". var_export( $dictionary, true ), 0 );
				 
//if ( isset( $dictionary['Prev'] ) ) error_log( '_extract_pdf_metadata Prev = ' . var_export( $dictionary['Prev']['value'], true ), 0 );
//if ( isset( $dictionary['Prev'] ) ) error_log( '_extract_pdf_metadata Prev start = ' . "\r\n" . var_export( self::_hex_dump( substr( $pdf, $dictionary['Prev']['value'] ), 2048, 32 ), true ) . "\r\n", 0 );
//if ( isset( $dictionary['XRefStm'] ) ) error_log( '_extract_pdf_metadata XRefStm = ' . var_export( $dictionary['XRefStm']['value'], true ), 0 );
//if ( isset( $dictionary['XRefStm'] ) ) error_log( '_extract_pdf_metadata XRefStm start = ' . "\r\n" . var_export( self::_hex_dump( substr( $pdf, $dictionary['XRefStm']['value'] ), 2048, 32 ), true ) . "\r\n", 0 );

				if ( isset( $dictionary['Info'] ) ) {
					$info_dictionary = NULL;

					$info = self::_find_pdf_indirect_dictionary( $pdf, $dictionary['Info']['object'], $dictionary['Info']['generation'] );
					if ( $info )
						$info_dictionary = self::_parse_pdf_dictionary( $info['content'], 0 );
					else {
						$info_ref = ($dictionary['Info']['object'] * 1000) + $dictionary['Info']['generation'];
error_log( '_extract_pdf_metadata $info_ref = '. var_export( $info_ref, true ), 0 );
						self::_build_pdf_indirect_objects( $pdf );
						if ( isset( self::$pdf_indirect_objects[ $info_ref ] ) )
							$info_dictionary = self::_parse_pdf_dictionary( substr( $pdf, self::$pdf_indirect_objects[ $info_ref ]['start'], self::$pdf_indirect_objects[ $info_ref ]['/length'] ), 0 );
					}
								
					if ( $info_dictionary ) {
//error_log( '_extract_pdf_metadata $info_dictionary length = '. var_export( $info_dictionary['/length'], true ), 0 );
						unset( $info_dictionary['/length'] );
//error_log( '_extract_pdf_metadata $info_dictionary = '. var_export( $info_dictionary, true ), 0 );

						foreach( $info_dictionary as $name => $value ) {
							if ( 'string' == $value['type'] ) {
								$prefix = substr( $value['value'], 0, 2 );
								if ( 'D:' == $prefix )
									$metadata[ $name ] = self::_parse_pdf_date( $value['value'] );
								elseif ( ( chr(0xFE) . chr(0xFF) ) == $prefix ) 
									$metadata[ $name ] = self::_parse_pdf_UTF16BE( $value['value'] );
								else
									$metadata[ $name ] = $value['value'];
							 }
							 else
								$metadata[ $name ] = $value['value'];
						} // each info entry
					} // found Info object
				} // found Info ref
				elseif ( isset( $dictionary['Root'] ) ) {
					$root_dictionary = NULL;

					$root = self::_find_pdf_indirect_dictionary( $pdf, $dictionary['Root']['object'], $dictionary['Root']['generation'] );
					if ( $root )
						$root_dictionary = self::_parse_pdf_dictionary( $root['content'], 0 );
					else {
							$root_ref = ($dictionary['Root']['object'] * 1000) + $dictionary['Root']['generation'];
error_log( '_extract_pdf_metadata $root_ref = '. var_export( $root_ref, true ), 0 );
							self::_build_pdf_indirect_objects( $pdf );
							if ( isset( self::$pdf_indirect_objects[ $root_ref ] ) )
								$root_dictionary = self::_parse_pdf_dictionary( substr( $pdf, self::$pdf_indirect_objects[ $root_ref ]['start'], self::$pdf_indirect_objects[ $root_ref ]['/length'] ), 0 );
					 }
								
error_log( '_extract_pdf_metadata trailer dictionary  Root = ' . var_export( $dictionary['Root'], true ), 0 );
error_log( "_extract_pdf_metadata Root dictionary = ". var_export( $root_dictionary, true ), 0 );
				 } // found Root ref
			 } // found dictionary
		} // foreach trailer
		
		$xmp = self::_parse_xmp_metadata( $pdf, 0);
		if ( is_array( $xmp ) )
			$metadata = array_merge( $metadata, $xmp );
			
$microtime_stop = microtime( true );
error_log( "_extract_pdf_metadata ({$file_name}) " . ($microtime_stop - $microtime_start), 0 );
//error_log( "_extract_pdf_metadata metadata = ". var_export( $metadata, true ), 0 );
		return $metadata;
	}
		
	/**
	 * UTF-8 replacements for invalid SQL characters
	 *
	 * @since 1.41
	 *
	 * @var	array
	 */
	private static $utf8_chars = array(
		"\xC2\x80", "\xC2\x81", "\xC2\x82", "\xC2\x83", "\xC2\x84", "\xC2\x85", "\xC2\x86", "\xC2\x87", 
		"\xC2\x88", "\xC2\x89", "\xC2\x8A", "\xC2\x8B", "\xC2\x8C", "\xC2\x8D", "\xC2\x8E", "\xC2\x8F", 
		"\xC2\x90", "\xC2\x91", "\xC2\x92", "\xC2\x93", "\xC2\x94", "\xC2\x95", "\xC2\x96", "\xC2\x97", 
		"\xC2\x98", "\xC2\x99", "\xC2\x9A", "\xC2\x9B", "\xC2\x9C", "\xC2\x9D", "\xC2\x9E", "\xC2\x9F", 
		"\xC2\xA0", "\xC2\xA1", "\xC2\xA2", "\xC2\xA3", "\xC2\xA4", "\xC2\xA5", "\xC2\xA6", "\xC2\xA7", 
		"\xC2\xA8", "\xC2\xA9", "\xC2\xAA", "\xC2\xAB", "\xC2\xAC", "\xC2\xAD", "\xC2\xAE", "\xC2\xAF", 
		"\xC2\xB0", "\xC2\xB1", "\xC2\xB2", "\xC2\xB3", "\xC2\xB4", "\xC2\xB5", "\xC2\xB6", "\xC2\xB7", 
		"\xC2\xB8", "\xC2\xB9", "\xC2\xBA", "\xC2\xBB", "\xC2\xBC", "\xC2\xBD", "\xC2\xBE", "\xC2\xBF", 
		"\xC3\x80", "\xC3\x81", "\xC3\x82", "\xC3\x83", "\xC3\x84", "\xC3\x85", "\xC3\x86", "\xC3\x87", 
		"\xC3\x88", "\xC3\x89", "\xC3\x8A", "\xC3\x8B", "\xC3\x8C", "\xC3\x8D", "\xC3\x8E", "\xC3\x8F", 
		"\xC3\x90", "\xC3\x91", "\xC3\x92", "\xC3\x93", "\xC3\x94", "\xC3\x95", "\xC3\x96", "\xC3\x97", 
		"\xC3\x98", "\xC3\x99", "\xC3\x9A", "\xC3\x9B", "\xC3\x9C", "\xC3\x9D", "\xC3\x9E", "\xC3\x9F", 
		"\xC3\xA0", "\xC3\xA1", "\xC3\xA2", "\xC3\xA3", "\xC3\xA4", "\xC3\xA5", "\xC3\xA6", "\xC3\xA7", 
		"\xC3\xA8", "\xC3\xA9", "\xC3\xAA", "\xC3\xAB", "\xC3\xAC", "\xC3\xAD", "\xC3\xAE", "\xC3\xAF", 
		"\xC3\xB0", "\xC3\xB1", "\xC3\xB2", "\xC3\xB3", "\xC3\xB4", "\xC3\xB5", "\xC3\xB6", "\xC3\xB7", 
		"\xC3\xB8", "\xC3\xB9", "\xC3\xBA", "\xC3\xBB", "\xC3\xBC", "\xC3\xBD", "\xC3\xBE", "\xC3\xBF"
	);

	/**
	 * Replace SQL incorrect characters (0x80 - 0xFF) with their UTF-8 equivalents
	 * 
	 * @since 1.41
	 *
	 * @param	string	unencoded string
	 *
	 * @return	string	UTF-8 encoded string
	 */
	private static function _bin_to_utf8( $string ) {
		if ( seems_utf8( $string ) )
			return $string;

		if(function_exists('utf8_encode')) {
			return utf8_encode( $string );
		}

		$output = '';
		for ($index = 0; $index < strlen( $string ); $index++ ) {
			$value = ord( $string[ $index ] );
			if ( $value < 0x80 )
				$output .= chr( $value );
			else
				$output .= self::$utf8_chars[ $value - 0x80 ];
		}

		return $output;
	}
		
	/**
	 * IPTC Dataset identifiers and names
	 *
	 * This array contains the identifiers and names of Datasets defined in
	 * the "IPTC-NAA Information Interchange Model Version No. 4.1".
	 *
	 * @since 0.90
	 *
	 * @var	array
	 */
	private static $mla_iptc_records = array(
		// Envelope Record
		"1#000" => "Model Version",
		"1#005" => "Destination",
		"1#020" => "File Format",
		"1#022" => "File Format Version",
		"1#030" => "Service Identifier",
		"1#040" => "Envelope Number",
		"1#050" => "Product ID",
		"1#060" => "Envelope Priority",
		"1#070" => "Date Sent",
		"1#080" => "Time Sent",
		"1#090" => "Coded Character Set",
		"1#100" => "UNO",
		"1#120" => "ARM Identifier",
		"1#122" => "ARM Version",
		
		// Application Record
		"2#000" => "Record Version",
		"2#003" => "Object Type Reference",
		"2#004" => "Object Attribute Reference",
		"2#005" => "Object Name",
		"2#007" => "Edit Status",
		"2#008" => "Editorial Update",
		"2#010" => "Urgency",
		"2#012" => "Subject Reference",
		"2#015" => "Category",
		"2#020" => "Supplemental Category",
		"2#022" => "Fixture Identifier",
		"2#025" => "Keywords",
		"2#026" => "Content Location Code",
		"2#027" => "Content Location Name",
		"2#030" => "Release Date",
		"2#035" => "Release Time",
		"2#037" => "Expiration Date",
		"2#038" => "Expiration Time",
		"2#040" => "Special Instructions",
		"2#042" => "Action Advised",
		"2#045" => "Reference Service",
		"2#047" => "Reference Date",
		"2#050" => "Reference Number",
		"2#055" => "Date Created",
		"2#060" => "Time Created",
		"2#062" => "Digital Creation Date",
		"2#063" => "Digital Creation Time",
		"2#065" => "Originating Program",
		"2#070" => "Program Version",
		"2#075" => "Object Cycle",
		"2#080" => "By-line",
		"2#085" => "By-line Title",
		"2#090" => "City",
		"2#092" => "Sub-location",
		"2#095" => "Province or State",
		"2#100" => "Country or Primary Location Code",
		"2#101" => "Country or Primary Location Name",
		"2#103" => "Original Transmission Reference",
		"2#105" => "Headline",
		"2#110" => "Credit",
		"2#115" => "Source",
		"2#116" => "Copyright Notice",
		"2#118" => "Contact",
		"2#120" => "Caption or Abstract",
		"2#122" => "Caption Writer or Editor",
		"2#125" => "Rasterized Caption",
		"2#130" => "Image Type",
		"2#131" => "Image Orientation",
		"2#135" => "Language Identifier",
		"2#150" => "Audio Type",
		"2#151" => "Audio Sampling Rate",
		"2#152" => "Audio Sampling Resolution",
		"2#153" => "Audio Duration",
		"2#154" => "Audio Outcue",
		"2#200" => "ObjectData Preview File Format",
		"2#201" => "ObjectData Preview File Format Version",
		"2#202" => "ObjectData Preview Data",
		
		// Pre ObjectData Descriptor Record
		"7#010"  => "Size Mode",
		"7#020"  => "Max Subfile Size",
		"7#090"  => "ObjectData Size Announced",
		"7#095"  => "Maximum ObjectData Size",
		
		// ObjectData Record
		"8#010"  => "Subfile",
		
		// Post ObjectData Descriptor Record
		"9#010"  => "Confirmed ObjectData Size"
	);

	/**
	 * IPTC Dataset friendly name/slug and identifiers
	 *
	 * This array contains the sanitized names and identifiers of Datasets defined in
	 * the "IPTC-NAA Information Interchange Model Version No. 4.1".
	 *
	 * @since 0.90
	 *
	 * @var	array
	 */
	public static $mla_iptc_keys = array(
		// Envelope Record
		'model-version' => '1#000',
		'destination' => '1#005',
		'file-format' => '1#020',
		'file-format-version' => '1#022',
		'service-identifier' => '1#030',
		'envelope-number' => '1#040',
		'product-id' => '1#050',
		'envelope-priority' => '1#060',
		'date-sent' => '1#070',
		'time-sent' => '1#080',
		'coded-character-set' => '1#090',
		'uno' => '1#100',
		'arm-identifier' => '1#120',
		'arm-version' => '1#122',

		// Application Record
		'record-version' => '2#000',
		'object-type-reference' => '2#003',
		'object-attribute-reference' => '2#004',
		'object-name' => '2#005',
		'edit-status' => '2#007',
		'editorial-update' => '2#008',
		'urgency' => '2#010',
		'subject-reference' => '2#012',
		'category' => '2#015',
		'supplemental-category' => '2#020',
		'fixture-identifier' => '2#022',
		'keywords' => '2#025',
		'content-location-code' => '2#026',
		'content-location-name' => '2#027',
		'release-date' => '2#030',
		'release-time' => '2#035',
		'expiration-date' => '2#037',
		'expiration-time' => '2#038',
		'special-instructions' => '2#040',
		'action-advised' => '2#042',
		'reference-service' => '2#045',
		'reference-date' => '2#047',
		'reference-number' => '2#050',
		'date-created' => '2#055',
		'time-created' => '2#060',
		'digital-creation-date' => '2#062',
		'digital-creation-time' => '2#063',
		'originating-program' => '2#065',
		'program-version' => '2#070',
		'object-cycle' => '2#075',
		'by-line' => '2#080',
		'by-line-title' => '2#085',
		'city' => '2#090',
		'sub-location' => '2#092',
		'province-or-state' => '2#095',
		'country-or-primary-location-code' => '2#100',
		'country-or-primary-location-name' => '2#101',
		'original-transmission-reference' => '2#103',
		'headline' => '2#105',
		'credit' => '2#110',
		'source' => '2#115',
		'copyright-notice' => '2#116',
		'contact' => '2#118',
		'caption-or-abstract' => '2#120',
		'caption-writer-or-editor' => '2#122',
		'rasterized-caption' => '2#125',
		'image-type' => '2#130',
		'image-orientation' => '2#131',
		'language-identifier' => '2#135',
		'audio-type' => '2#150',
		'audio-sampling-rate' => '2#151',
		'audio-sampling-resolution' => '2#152',
		'audio-duration' => '2#153',
		'audio-outcue' => '2#154',
		'objectdata-preview-file-format' => '2#200',
		'objectdata-preview-file-format-version' => '2#201',
		'objectdata-preview-data' => '2#202',
		
		// Pre ObjectData Descriptor Record
		'size-mode' => '7#010',
		'max-subfile-size' => '7#020',
		'objectdata-size-announced' => '7#090',
		'maximum-objectdata-size' => '7#095',
		
		// ObjectData Record
		'subfile' => '8#010',
		
		// Post ObjectData Descriptor Record
		'confirmed-objectdata-size' => '9#010'
);

	/**
	 * IPTC Dataset descriptions
	 *
	 * This array contains the descriptions of Datasets defined in
	 * the "IPTC-NAA Information Interchange Model Version No. 4.1".
	 *
	 * @since 0.90
	 *
	 * @var	array
	 */
	private static $mla_iptc_descriptions = array(
		// Envelope Record
		"1#000" => "2 octet binary IIM version number",
		"1#005" => "Max 1024 characters of Destination (ISO routing information); repeatable",
		"1#020" => "2 octet binary file format number, see IPTC-NAA V4 Appendix A",
		"1#022" => "2 octet binary file format version number",
		"1#030" => "Max 10 characters of Service Identifier and product",
		"1#040" => "8 Character Envelope Number",
		"1#050" => "Max 32 characters subset of provider's overall service; repeatable",
		"1#060" => "1 numeric character of envelope handling priority (not urgency)",
		"1#070" => "8 numeric characters of Date Sent by service - CCYYMMDD",
		"1#080" => "11 characters of Time Sent by service - HHMMSS±HHMM",
		"1#090" => "Max 32 characters of control functions, etc.",
		"1#100" => "14 to 80 characters of eternal, globally unique identification for objects",
		"1#120" => "2 octet binary Abstract Relationship Model Identifier",
		"1#122" => "2 octet binary Abstract Relationship Model Version",
		
		// Application Record
		"2#000" => "2 octet binary Information Interchange Model, Part II version number",
		"2#003" => "3 to 67 Characters of Object Type Reference number and optional text",
		"2#004" => "3 to 67 Characters of Object Attribute Reference number and optional text; repeatable",
		"2#005" => "Max 64 characters of the object name or shorthand reference",
		"2#007" => "Max 64 characters of the status of the objectdata",
		"2#008" => "2 numeric characters of the type of update this object provides",
		"2#010" => "1 numeric character of the editorial urgency of content",
		"2#012" => "13 to 236 characters of a structured definition of the subject matter; repeatable",
		"2#015" => "Max 3 characters of the subject of the objectdata, DEPRECATED",
		"2#020" => "Max 32 characters (each) of further refinement of subject, DEPRECATED; repeatable",
		"2#022" => "Max 32 characters identifying recurring, predictable content",
		"2#025" => "Max 64 characters (each) of tags; repeatable",
		"2#026" => "3 characters of ISO3166 country code or IPTC-assigned code; repeatable",
		"2#027" => "Max 64 characters of publishable country/geographical location name; repeatable",
		"2#030" => "8 numeric characters of Release Date - CCYYMMDD",
		"2#035" => "11 characters of Release Time (earliest use) - HHMMSS±HHMM",
		"2#037" => "8 numeric characters of Expiration Date (latest use) -  CCYYMDD",
		"2#038" => "11 characters of Expiration Time (latest use) - HHMMSS±HHMM",
		"2#040" => "Max 256 Characters of editorial instructions, e.g., embargoes and warnings",
		"2#042" => "2 numeric characters of type of action this object provides to a previous object",
		"2#045" => "Max 10 characters of the Service ID (1#030) of a prior envelope; repeatable",
		"2#047" => "8 numeric characters of prior envelope Reference Date (1#070) - CCYYMMDD; repeatable",
		"2#050" => "8 characters of prior envelope Reference Number (1#040); repeatable",
		"2#055" => "8 numeric characters of intellectual content Date Created - CCYYMMDD",
		"2#060" => "11 characters of intellectual content Time Created - HHMMSS±HHMM",
		"2#062" => "8 numeric characters of digital representation creation date - CCYYMMDD",
		"2#063" => "11 characters of digital representation creation time - HHMMSS±HHMM",
		"2#065" => "Max 32 characters of the program used to create the objectdata",
		"2#070" => "Program Version - Max 10 characters of the version of the program used to create the objectdata",
		"2#075" => "1 character where a=morning, p=evening, b=both",
		"2#080" => "Max 32 Characters of the name of the objectdata creator, e.g., the writer, photographer; repeatable",
		"2#085" => "Max 32 characters of the title of the objectdata creator; repeatable",
		"2#090" => "Max 32 Characters of the city of objectdata origin",
		"2#092" => "Max 32 Characters of the location within the city of objectdata origin",
		"2#095" => "Max 32 Characters of the objectdata origin Province or State",
		"2#100" => "3 characters of ISO3166 or IPTC-assigned code for Country of objectdata origin",
		"2#101" => "Max 64 characters of publishable country/geographical location name of objectdata origin",
		"2#103" => "Max 32 characters of a code representing the location of original transmission",
		"2#105" => "Max 256 Characters of a publishable entry providing a synopsis of the contents of the objectdata",
		"2#110" => "Max 32 Characters that identifies the provider of the objectdata (Vs the owner/creator)",
		"2#115" => "Max 32 Characters that identifies the original owner of the intellectual content",
		"2#116" => "Max 128 Characters that contains any necessary copyright notice",
		"2#118" => "Max 128 characters that identifies the person or organisation which can provide further background information; repeatable",
		"2#120" => "Max 2000 Characters of a textual description of the objectdata",
		"2#122" => "Max 32 Characters that the identifies the person involved in the writing, editing or correcting the objectdata or caption/abstract; repeatable",
		"2#125" => "7360 binary octets of the rasterized caption - 1 bit per pixel, 460x128-pixel image",
		"2#130" => "2 characters of color composition type and information",
		"2#131" => "1 alphabetic character indicating the image area layout - P=portrait, L=landscape, S=square",
		"2#135" => "2 or 3 aphabetic characters containing the major national language of the object, according to the ISO 639:1988 codes",
		"2#150" => "2 characters identifying monaural/stereo and exact type of audio content",
		"2#151" => "6 numeric characters representing the audio sampling rate in hertz (Hz)",
		"2#152" => "2 numeric characters representing the number of bits in each audio sample",
		"2#153" => "6 numeric characters of the Audio Duration - HHMMSS",
		"2#154" => "Max 64 characters of the content of the end of an audio objectdata",
		"2#200" => "2 octet binary file format of the ObjectData Preview",
		"2#201" => "2 octet binary particular version of the ObjectData Preview File Format",
		"2#202" => "Max 256000 binary octets containing the ObjectData Preview data",
		
		// Pre ObjectData Descriptor Record
		"7#010"  => "1 numeric character - 0=objectdata size not known, 1=objectdata size known at beginning of transfer",
		"7#020"  => "4 octet binary maximum subfile dataset(s) size",
		"7#090"  => "4 octet binary objectdata size if known at beginning of transfer",
		"7#095"  => "4 octet binary largest possible objectdata size",
		
		// ObjectData Record
		"8#010"  => "Subfile DataSet containing the objectdata itself; repeatable",
		
		// Post ObjectData Descriptor Record
		"9#010"  => "4 octet binary total objectdata size"
	);

	/**
	 * IPTC file format identifiers and descriptions
	 *
	 * This array contains the file format identifiers and descriptions defined in
	 * the "IPTC-NAA Information Interchange Model Version No. 4.1" for dataset 1#020.
	 *
	 * @since 0.90
	 *
	 * @var	array
	 */
	private static $mla_iptc_formats = array(
		00 => "No ObjectData",
		01 => "IPTC-NAA Digital Newsphoto Parameter Record",
		02 => "IPTC7901 Recommended Message Format",
		03 => "Tagged Image File Format (Adobe/Aldus Image data)",
		04 => "Illustrator (Adobe Graphics data)",
		05 => "AppleSingle (Apple Computer Inc)",
		06 => "NAA 89-3 (ANPA 1312)",
		07 => "MacBinary II",
		08 => "IPTC Unstructured Character Oriented File Format (UCOFF)",
		09 => "United Press International ANPA 1312 variant",
		10 => "United Press International Down-Load Message",
		11 => "JPEG File Interchange (JFIF)",
		12 => "Photo-CD Image-Pac (Eastman Kodak)",
		13 => "Microsoft Bit Mapped Graphics File [*.BMP]",
		14 => "Digital Audio File [*.WAV] (Microsoft & Creative Labs)",
		15 => "Audio plus Moving Video [*.AVI] (Microsoft)",
		16 => "PC DOS/Windows Executable Files [*.COM][*.EXE]",
		17 => "Compressed Binary File [*.ZIP] (PKWare Inc)",
		18 => "Audio Interchange File Format AIFF (Apple Computer Inc)",
		19 => "RIFF Wave (Microsoft Corporation)",
		20 => "Freehand (Macromedia/Aldus)",
		21 => "Hypertext Markup Language - HTML (The Internet Society)",
		22 => "MPEG 2 Audio Layer 2 (Musicom), ISO/IEC",
		23 => "MPEG 2 Audio Layer 3, ISO/IEC",
		24 => "Portable Document File (*.PDF) Adobe",
		25 => "News Industry Text Format (NITF)",
		26 => "Tape Archive (*.TAR)",
		27 => "Tidningarnas Telegrambyrå NITF version (TTNITF DTD)",
		28 => "Ritzaus Bureau NITF version (RBNITF DTD)",
		29 => "Corel Draw [*.CDR]"
	);

	/**
	 * IPTC image type identifiers and descriptions
	 *
	 * This array contains the image type identifiers and descriptions defined in
	 * the "IPTC-NAA Information Interchange Model Version No. 4.1" for dataset 2#130, octet 2.
	 *
	 * @since 0.90
	 *
	 * @var	array
	 */
	private static $mla_iptc_image_types = array(
		"M" => "Monochrome",
		"Y" => "Yellow Component",
		"M" => "Magenta Component",
		"C" => "Cyan Component",
		"K" => "Black Component",
		"R" => "Red Component",
		"G" => "Green Component",
		"B" => "Blue Component",
		"T" => "Text Only",
		"F" => "Full colour composite, frame sequential",
		"L" => "Full colour composite, line sequential",
		"P" => "Full colour composite, pixel sequential",
		"S" => "Full colour composite, special interleaving"
	);

	/**
	 * Parse one IPTC metadata field
	 * 
	 * Returns a string value, converting array data to a string as necessary.
	 *
	 * @since 1.41
	 *
	 * @param	string	field name - IPTC Identifier or friendly name/slug
	 * @param	string	metadata array containing iptc, exif, and pdf metadata arrays
	 *
	 * @return	mixed	string/array representation of metadata value or an empty string
	 */
	public static function mla_iptc_metadata_value( $iptc_key, $item_metadata ) {
		// convert friendly name/slug to identifier
		if ( array_key_exists( $iptc_key, self::$mla_iptc_keys ) ) {
			$iptc_key = self::$mla_iptc_keys[ $iptc_key ];
		}
				
		$text = '';
		if ( array_key_exists( $iptc_key, $item_metadata['mla_iptc_metadata'] ) ) {
			$text = $item_metadata['mla_iptc_metadata'][ $iptc_key ];
			if ( is_array( $text ) ) {
				foreach ($text as $key => $value )
					$text[ $key ] = self::_bin_to_utf8( $value );
			}
			elseif ( is_string( $text ) )
				$text = self::_bin_to_utf8( $text );
		}
		
		return $text;
	}
		
	/**
	 * Parse one EXIF metadata field
	 * 
	 * Returns a string value, converting array data to a string as necessary.
	 * Also handles the special pseudo-values 'ALL_EXIF' and 'ALL_IPTC'.
	 *
	 * @since 1.13
	 *
	 * @param	string	field name
	 * @param	string	metadata array containing iptc, exif, and pdf metadata arrays
	 *
	 * @return	string	string representation of metadata value or an empty string
	 */
	public static function mla_exif_metadata_value( $exif_key, $item_metadata ) {
		if ( 'pdf:' == substr( $exif_key, 0, 4 ) )
			return self::mla_pdf_metadata_value( substr( $exif_key, 4 ), $item_metadata );
			
		$text = '';
		if ( array_key_exists( $exif_key, $item_metadata['mla_exif_metadata'] ) ) {
			$text = $item_metadata['mla_exif_metadata'][ $exif_key ];
			if ( is_array( $text ) ) {
				foreach ($text as $key => $value ) {
					if ( is_array( $value ) )
						$text[ $key ] = self::_bin_to_utf8( var_export( $value, true ) );
					else
						$text[ $key ] = self::_bin_to_utf8( $value );
				}
			}
			elseif ( is_string( $text ) )
				$text = self::_bin_to_utf8( $text );
		} elseif ( 'ALL_EXIF' == $exif_key ) {
			$clean_data = array();
			foreach ( $item_metadata['mla_exif_metadata'] as $key => $value ) {
				if ( is_array( $value ) ) 
					$clean_data[ $key ] = '(ARRAY)';
				elseif ( is_string( $value ) )
					$clean_data[ $key ] = self::_bin_to_utf8( substr( $value, 0, 256 ) );
				else
					$clean_data[ $key ] = $value;
			}
			
			$text = var_export( $clean_data, true);
		} elseif ( 'ALL_IPTC' == $exif_key ) {
			$clean_data = array();
			foreach ( $item_metadata['mla_iptc_metadata'] as $key => $value ) {
				if ( is_array( $value ) ) {
					foreach ($value as $text_key => $text )
						$value[ $text_key ] = self::_bin_to_utf8( $text );
						
					$clean_data[ $key ] = 'ARRAY(' . implode( ',', $value ) . ')';
				}
				elseif ( is_string( $value ) )
					$clean_data[ $key ] = self::_bin_to_utf8( substr( $value, 0, 256 ) );
				else
					$clean_data[ $key ] = self::_bin_to_utf8( $value );
			}

			$text = var_export( $clean_data, true);
		}
		
		return $text;
	}
		
	/**
	 * Parse one PDF metadata field
	 * 
	 * Returns a string value, converting array data to a string as necessary.
	 * Also handles the special pseudo-value 'ALL_PDF'.
	 *
	 * @since 1.4x
	 *
	 * @param	string	field name
	 * @param	string	metadata array containing iptc, exif, and pdf metadata arrays
	 *
	 * @return	string	string representation of metadata value or an empty string
	 */
	public static function mla_pdf_metadata_value( $pdf_key, $item_metadata ) {
		$text = '';
		if ( array_key_exists( $pdf_key, $item_metadata['mla_pdf_metadata'] ) ) {
			$text = $item_metadata['mla_pdf_metadata'][ $pdf_key ];
			if ( is_array( $text ) ) {
				foreach ($text as $key => $value ) {
					if ( is_array( $value ) )
						$text[ $key ] = self::_bin_to_utf8( var_export( $value, true ) );
					else
						$text[ $key ] = self::_bin_to_utf8( $value );
				}
			}
			elseif ( is_string( $text ) )
				$text = self::_bin_to_utf8( $text );
		} elseif ( 'ALL_PDF' == $pdf_key ) {
			$clean_data = array();
			foreach ( $item_metadata['mla_pdf_metadata'] as $key => $value ) {
				if ( is_array( $value ) ) 
					$clean_data[ $key ] = '(ARRAY)';
				elseif ( is_string( $value ) )
					$clean_data[ $key ] = self::_bin_to_utf8( substr( $value, 0, 256 ) );
				else
					$clean_data[ $key ] = $value;
			}
			
			$text = var_export( $clean_data, true);
		} // ALL_PDF
		
		return $text;
	}
		
	/**
	 * Fetch and filter IPTC and EXIF meta data for an image attachment
	 * 
	 * Returns 
	 *
	 * @since 0.90
	 *
	 * @param	int		post ID of attachment
	 * @param	string	optional; if $post_id is zero, path to the image file.
	 *
	 * @return	array	Meta data variables
	 */
	public static function mla_fetch_attachment_image_metadata( $post_id, $path = '' ) {
		$results = array(
			'mla_iptc_metadata' => array(),
			'mla_exif_metadata' => array(),
			'mla_pdf_metadata' => array()
			);

		if ( 0 != $post_id )
			$path = get_attached_file($post_id);

		if ( ! empty( $path ) ) {
			if ( 'pdf' == strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ) ) {
				$results['mla_pdf_metadata'] = self::_extract_pdf_metadata( $path );
				return $results;
			}

			$size = getimagesize( $path, $info );
			
			if ( is_callable( 'iptcparse' ) ) {
				if ( !empty( $info['APP13'] ) ) {
					$iptc_values = iptcparse( $info['APP13'] );
					if ( ! is_array( $iptc_values ) )
						$iptc_values = array();
						
					foreach ( $iptc_values as $key => $value ) {
						if ( in_array( $key, array( '1#000', '1#020', '1#022', '1#120', '1#122', '2#000',  '2#200', '2#201' ) ) ) {
							$value = unpack( 'nbinary', $value[0] );
							$results['mla_iptc_metadata'][ $key ] = (string) $value['binary'];
						}
						elseif ( 1 == count( $value ) )
							$results['mla_iptc_metadata'][ $key ] = $value[0];
						else
							$results['mla_iptc_metadata'][ $key ] = $value;
							
					} // foreach $value
				} // !empty
			}
				
			if ( is_callable( 'exif_read_data' ) && in_array( $size[2], array( IMAGETYPE_JPEG, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM ) ) ) {
				$results['mla_exif_metadata'] = exif_read_data( $path );
			}
		}
		
		/*
		 * Expand EXIF array values
		 */
		foreach ( $results['mla_exif_metadata'] as $exif_key => $exif_value ) {
			if ( is_array( $exif_value ) ) {
				foreach ( $exif_value as $key => $value ) {
					$results['mla_exif_metadata'][ $exif_key . '.' . $key ] = $value;
				}
			} // is_array
		}

		return $results;
	}
	
	/**
	 * Update custom field data for a single attachment.
	 * 
	 * @since 1.40
	 * 
	 * @param	int		The ID of the attachment to be updated
	 * @param	array	Field name => value pairs
	 *
	 * @return	string	success/failure message(s)
	 */
	public static function mla_update_item_postmeta( $post_id, $new_meta ) {
		$post_data = MLAData::mla_fetch_attachment_metadata( $post_id );
		$message = '';
		
		foreach ( $new_meta as $meta_key => $meta_value ) {
			if ( $multi_key = isset( $meta_value[0x80000000] ) )
				unset( $meta_value[0x80000000] );
				
			if ( $keep_existing = isset( $meta_value[0x80000001] ) ) {
				$keep_existing = (boolean) $meta_value[0x80000001];
				unset( $meta_value[0x80000001] );
			}
				
			if ( $no_null = isset( $meta_value[0x80000002] ) ) {
				$no_null = (boolean) $meta_value[0x80000002];
				unset( $meta_value[0x80000002] );
			}
				
			if ( isset( $post_data[ 'mla_item_' . $meta_key ] ) ) {
				$old_meta_value = $post_data[ 'mla_item_' . $meta_key ];
				
				if ( $multi_key && $no_null ) {
					if ( is_string( $old_meta_value ) )
						$old_meta_value = trim( $old_meta_value );
						
					$delete = empty( $old_meta_value );
				}
				else 
					$delete = NULL == $meta_value;
				
				if ( $delete) {
					if ( delete_post_meta( $post_id, $meta_key ) )
						$message .= sprintf( 'Deleting %1$s<br>', $meta_key );
						
					continue;
				}
			}
			else {
				if ( NULL != $meta_value ) {
					if ( $multi_key )
						foreach ( $meta_value as $new_value ) {
							if ( add_post_meta( $post_id, $meta_key, $new_value ) )
								$message .= sprintf( 'Adding %1$s = [%2$s]<br>', $meta_key, $new_value );
						}
					else		
						if ( add_post_meta( $post_id, $meta_key, $meta_value ) )
							$message .= sprintf( 'Adding %1$s = %2$s<br>', $meta_key, $meta_value );
				}

				continue; // no change or message if old and new are both NULL
			} // no old value
			
			if ( is_array( $old_meta_value ) ) {
				$old_text = var_export( $old_meta_value, true );
			}
			else
				$old_text = $old_meta_value;

			/*
			 * Multi-key change from existing values to new values
			 */
			if ( $multi_key ) {
				/*
				 * Test for "no changes"
				 */
				if ( $meta_value == (array) $old_meta_value )
					continue;
					
				if ( ! $keep_existing ) {
					if ( delete_post_meta( $post_id, $meta_key ) )
						$message .= sprintf( 'Deleting old %1$s values<br>', $meta_key );
					$old_meta_value = array();
				}
				elseif ( $old_text == $old_meta_value ) // single value
					$old_meta_value = array( $old_meta_value );

				$updated = 0;
				foreach ( $meta_value as $new_value ) {
					if ( ! in_array( $new_value, $old_meta_value ) ) {
						add_post_meta( $post_id, $meta_key, $new_value );
						$old_meta_value[] = $new_value; // prevent duplicates
						$updated++;
					}
				}
					
				if ( $updated ) {
					$meta_value = get_post_meta( $post_id, $meta_key );
					if ( is_array( $meta_value ) )
						if ( 1 == count( $meta_value ) )
							$new_text = $meta_value[0];
						else
							$new_text = var_export( $meta_value, true );
					else
						$new_text = $meta_value;
	
						$message .= sprintf( 'Changing %1$s from "%2$s" to "%3$s"; %4$d updates<br>', $meta_key, $old_text, $new_text, $updated );
				}
			}
			elseif ( $old_meta_value != $meta_value ) {
				if ( is_array( $old_meta_value ) )
					delete_post_meta( $post_id, $meta_key );

				if ( is_array( $meta_value ) )
					$new_text = var_export( $meta_value, true );
				else
					$new_text = $meta_value;
				
				$message .= sprintf( 'Changing %1$s from "%2$s" to "%3$s"<br>', $meta_key, $old_text, $new_text );
				$results = update_post_meta( $post_id, $meta_key, $meta_value );
			}
		} // foreach $new_meta
		
		return $message;
	}
	
	/**
	 * Update a single item; change the meta data 
	 * for a single attachment.
	 * 
	 * @since 0.1
	 * 
	 * @param	int		The ID of the attachment to be updated
	 * @param	array	Field name => value pairs
	 * @param	array	Optional taxonomy term values, default null
	 * @param	array	Optional taxonomy actions (add, remove, replace), default null
	 *
	 * @return	array	success/failure message and NULL content
	 */
	public static function mla_update_single_item( $post_id, $new_data, $tax_input = NULL, $tax_actions = NULL ) {
		$post_data = MLAData::mla_get_attachment_by_id( $post_id );
		
		if ( !isset( $post_data ) )
			return array(
				'message' => 'ERROR: Could not retrieve Attachment.',
				'body' => '' 
			);
		
		$message = '';
		$updates = array( 'ID' => $post_id );
		$new_data = stripslashes_deep( $new_data );
		$new_meta = NULL;

		foreach ( $new_data as $key => $value ) {
			switch ( $key ) {
				case 'post_title':
					if ( $value == $post_data[ $key ] )
						break;
						
					$message .= sprintf( 'Changing Title from "%1$s" to "%2$s"<br>', esc_attr( $post_data[ $key ] ), esc_attr( $value ) );
					$updates[ $key ] = $value;
					break;
				case 'post_name':
					if ( $value == $post_data[ $key ] )
						break;
					
					$value = sanitize_title( $value );
					
					/*
					 * Make sure new slug is unique
					 */
					$args = array(
						'name' => $value,
						'post_type' => 'attachment',
						'post_status' => 'inherit',
						'showposts' => 1 
					);
					$my_posts = get_posts( $args );
					
					if ( $my_posts ) {
						$message .= sprintf( 'ERROR: Could not change Name/Slug "%1$s"; name already exists<br>', $value );
					} else {
						$message .= sprintf( 'Changing Name/Slug from "%1$s" to "%2$s"<br>', esc_attr( $post_data[ $key ] ), $value );
						$updates[ $key ] = $value;
					}
					break;
				case 'image_alt':
					$key = 'mla_wp_attachment_image_alt';
					if ( !isset( $post_data[ $key ] ) )
						$post_data[ $key ] = '';
					
					if ( $value == $post_data[ $key ] )
						break;
					
					if ( empty( $value ) ) {
						if ( delete_post_meta( $post_id, '_wp_attachment_image_alt', $value ) )
							$message .= sprintf( 'Deleting Alternate Text, was "%1$s"<br>', esc_attr( $post_data[ $key ] ) );
						else
							$message .= sprintf( 'ERROR: Could not delete Alternate Text, remains "%1$s"<br>', esc_attr( $post_data[ $key ] ) );
					} else {
						if ( update_post_meta( $post_id, '_wp_attachment_image_alt', $value ) )
							$message .= sprintf( 'Changing Alternate Text from "%1$s" to "%2$s"<br>', esc_attr( $post_data[ $key ] ), esc_attr( $value ) );
						else
							$message .= sprintf( 'ERROR: Could not change Alternate Text from "%1$s" to "%2$s"<br>', esc_attr( $post_data[ $key ] ), esc_attr( $value ) );
					}
					break;
				case 'post_excerpt':
					if ( $value == $post_data[ $key ] )
						break;
						
					$message .= sprintf( 'Changing Caption from "%1$s" to "%2$s"<br>', esc_attr( $post_data[ $key ] ), esc_attr( $value ) );
					$updates[ $key ] = $value;
					break;
				case 'post_content':
					if ( $value == $post_data[ $key ] )
						break;
						
					$message .= sprintf( 'Changing Description from "%1$s" to "%2$s"<br>', esc_textarea( $post_data[ $key ] ), esc_textarea( $value ) );
					$updates[ $key ] = $value;
					break;
				case 'post_parent':
					if ( $value == $post_data[ $key ] )
						break;
						
					$value = absint( $value );
					
					$message .= sprintf( 'Changing Parent from "%1$s" to "%2$s"<br>', $post_data[ $key ], $value );
					$updates[ $key ] = $value;
					break;
				case 'menu_order':
					if ( $value == $post_data[ $key ] )
						break;
						
					$value = absint( $value );
					
					$message .= sprintf( 'Changing Menu Order from "%1$s" to "%2$s"<br>', $post_data[ $key ], $value );
					$updates[ $key ] = $value;
					break;
				case 'post_author':
					if ( $value == $post_data[ $key ] )
						break;
						
					$value = absint( $value );
					
					$from_user = get_userdata( $post_data[ $key ] );
					$to_user = get_userdata( $value );
					$message .= sprintf( 'Changing Author from "%1$s" to "%2$s"<br>', $from_user->display_name, $to_user->display_name );
					$updates[ $key ] = $value;
					break;
				case 'taxonomy_updates':
					$tax_input = $value['inputs'];
					$tax_actions = $value['actions'];
					break;
				case 'custom_updates':
					$new_meta = $value;
					break;
				default:
					// Ignore anything else
			} // switch $key
		} // foreach $new_data
		
		if ( !empty( $tax_input ) ) {
			foreach ( $tax_input as $taxonomy => $tags ) {
				if ( !empty( $tax_actions ) ) 
					$tax_action = $tax_actions[ $taxonomy ];
				else
					$tax_action = 'replace';
					
				$taxonomy_obj = get_taxonomy( $taxonomy );

				if ( current_user_can( $taxonomy_obj->cap->assign_terms ) ) {
					$terms_before = wp_get_post_terms( $post_id, $taxonomy, array(
						'fields' => 'ids' // all' 
					) );
					if ( is_array( $tags ) ) // array = hierarchical, string = non-hierarchical.
						$tags = array_filter( $tags );
					
					switch ( $tax_action ) {
						case 'add':
							$action_name = 'Adding';
							$result = wp_set_post_terms( $post_id, $tags, $taxonomy, true );
							break;
						case 'remove':
							$action_name = 'Removing';
							$tags = self::_remove_tags( $terms_before, $tags, $taxonomy_obj );
							$result = wp_set_post_terms( $post_id, $tags, $taxonomy );
							break;
						case 'replace':
							$action_name = 'Replacing';
							$result = wp_set_post_terms( $post_id, $tags, $taxonomy );
							break;
						default:
							$action_name = 'Ignoring';
							$result = NULL;
							// ignore anything else
					}
					
					$terms_after = wp_get_post_terms( $post_id, $taxonomy, array(
						'fields' => 'ids' // all' 
					) );
					
					if ( $terms_before != $terms_after )
						$message .= sprintf( '%1$s "%2$s" terms<br>', $action_name, $taxonomy );
				} // current_user_can
				else {
					$message .= sprintf( 'You cannot assign "%1$s" terms<br>', $action_name, $taxonomy );
				}
			} // foreach $tax_input
		} // !empty $tax_input
		
		if ( is_array( $new_meta ) )
			$message .= self::mla_update_item_postmeta( $post_id, $new_meta );
		
		if ( empty( $message ) )
			return array(
				'message' => 'Item: ' . $post_id . ', no changes detected.',
				'body' => '' 
			);
		else {
			MLAData::mla_get_attachment_by_id( -1 ); // invalidate the cached item

			if ( wp_update_post( $updates ) ) {
				$final_message = 'Item: ' . $post_id . ' updated.';
				/*
				 * Uncomment this for debugging.
				 */
				// $final_message .= '<br>' . $message;
				// error_log( 'DEBUG: message = ' . var_export( $message, true ), 0 );
				
				return array(
					'message' => $final_message,
					'body' => '' 
				);
			}
			else
				return array(
					'message' => 'ERROR: Item ' . $post_id . ' update failed.',
					'body' => '' 
				);
		}
	}
	
	/**
	 * Remove tags from a term ids list
	 * 
	 * @since 0.40
	 * 
	 * @param	array	The term ids currently assigned
	 * @param	array | string	The term ids (array) or names (string) to remove
	 * @param	object	The taxonomy object
	 *
	 * @return	array	Term ids of the surviving tags
	 */
	private static function _remove_tags( $terms_before, $tags, $taxonomy_obj ) {
		if ( ! is_array( $tags ) ) {
			/*
			 * Convert names to term ids
			 */
			$comma = _x( ',', 'tag delimiter' );
			if ( ',' !== $comma )
				$tags = str_replace( $comma, ',', $tags );
			$terms = explode( ',', trim( $tags, " \n\t\r\0\x0B," ) );

			$tags = array();
			foreach ( (array) $terms as $term) {
				if ( !strlen(trim($term)) )
					continue;

				// Skip if a non-existent term name is passed.
				if ( ! $term_info = term_exists($term, $taxonomy_obj->name ) )
					continue;

				if ( is_wp_error($term_info) )
					continue;

				$tags[] = $term_info['term_id'];
			} // foreach term
		} // not an array
		
		$tags = array_map( 'intval', $tags );
		$tags = array_unique( $tags );
		$terms_after = array_diff( array_map( 'intval', $terms_before ), $tags );
		return $terms_after;
	}
	
	/**
	 * Format printable version of binary data
	 * 
	 * @since 0.90
	 * 
	 * @param	string	Binary data
	 * @param	integer	Bytes to format, default = 0 (all bytes)
	 * @param	intger	Bytes to format on each line
	 *
	 * @return	string	Printable representation of $data
	 */
	private static function _hex_dump( $data, $limit = 0, $bytes_per_row = 16 ) {
		if ( 0 == $limit )
			$limit = strlen( $data );
			
		$position = 0;
		$output = "\r\n";
		
		while ( $position < $limit ) {
			$row_length = strlen( substr( $data, $position ) );
			
			if ( 0 == $row_length )
				break;
			
			if ( $row_length > ( $limit - $position ) )
				$row_length = $limit - $position;

			if ( $row_length > $bytes_per_row )
				$row_length = $bytes_per_row;

			$row_data = substr( $data, $position, $row_length );
			
			$print_string = '';
			$hex_string = '';
			for ( $index = 0; $index < $row_length; $index++ ) {
				$char = ord( substr( $row_data, $index, 1 ) );
				if ( ( 31 < $char ) && ( 127 > $char ) )
					$print_string .= chr($char);
				else
					$print_string .= '.';
					
				$hex_string .= ' ' . bin2hex( chr($char) );
			} // for
			
			$output .= str_pad( $print_string, $bytes_per_row, ' ', STR_PAD_RIGHT ) . $hex_string . "\r\n";
			$position += $row_length;
		} // while
		
		return $output;
	}
} // class MLAData
?>