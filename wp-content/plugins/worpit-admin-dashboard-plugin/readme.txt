=== iControlWP - Take Control of WordPress  ===
Contributors: paultgoodchild, dlgoodchild
Donate link: http://www.icontrolwp.com/
Tags: manage, wordpress manage, wordpress admin, backup, restore, bulk, icontrolwp, worpit
Requires at least: 3.2.0
Tested up to: 3.6
Stable tag: 2.3.6

== Description ==

The [iControlWP Secure WordPress Manager](http://www.icontrolwp.com/?src=icwp_readme) lets you manage all your WordPress websites from a central dashboard.

With iControlWP you can:

*	Manage WordPress websites from a single centralized Dashboard.
*	One-click update a plugin, a theme or even the WordPress Core.
*	Update all plugins, all themes, all WordPress cores across all sites, or just a selection at once.
*	Optimize all your WordPress sites without extra plugins: [clean up WordPress](http://www.icontrolwp.com/2012/07/optimize-clean-up-wordpress-worpit/?src=wpt_readme) 
and [optimize the WordPress database](http://www.icontrolwp.com/2012/07/how-to-optimize-wordpress-databases-with-worpit/?src=wpt_readme).
*	Add WordPress security options across all your websites at once. 
*	Install a brand new WordPress website automatically, anywhere you have cPanel web hosting
*	Install the iControlWP WordPress plugin from WordPress.org, or have iControlWP do it for you automatically
*	Log into your WordPress sites without remembering your WordPress login details.

No more logging in to each individual website to perform the same, repetitive tasks.  Do them in bulk, on all your sites at once.

== Frequently Asked Questions ==

= Is iControlWP Free? =

No, but you can try it out using our unlimited-sites free trial.

[You can sign up free here](http://www.icontrolwp.com/?src=wpt_readme)

= Is iControlWP secure? =

Yes! We take great care to ensure the integrity of the connection between iControlWP and your website.

All sensitive data is encrypted on our system so it is never human-readable by anyone.

See the next question for more in-depth explanation.

= I want more to know more about iControlWP plugin security? =

We take security seriously at iControlWP - prevention is far better than cure and we trust when you see what steps we
take to ensure the integrity of your website you'll know we taking the biggest steps to secure your site.

*	Each new plugin install creates a unique secure code that you must supply correctly to add the site to your iControlWP account.
*	When iControlWP and your plugin are connected, iControlWP creates a unique PIN code, encrypts with an MD5 hash and stores in
on your site. ONLY attempts to connect to your plugin that supply this correct PIN code will ever be able succeed.
*	We also take it a step FURTHER - each connection performs a unique hand-shake process (that you wont find in other similar products)
between iControlWP and your WordPress websites to ensure that no-one else, anywhere, can spoof your WordPress site. The plugin will **always**
check to ensure that the connection has originated from iControlWP.com. If not, the connection is disregarded completely and immediately.

= Will the iControlWP plugin slow down my site? =

Not a chance! We have the absolute SMALLEST plugin (compared to similar products of this type) around.

We install only the absolute necessary code, and when you need more, iControlWP's unique "Action Pack" delivery
system sends just what your site needs.

= How does iControlWP work? =

With the plugin installed and the connection setup to your own iControlWP account, the iControlWP system
will periodically ask your WordPress website for some information.  Currently we check:

*	WordPress Core update status
*	WordPress.org Plugins update status
*	WordPress.org Themes update status
*	Other server environment information that helps us to determine compatibility with the iControlWP system.
This includes things like PHP version, HTTP server type and version. You can review all this captured
information from within iControlWP and is useful as a handy reference.

We then take this information and display it to you on the iControlWP dashboard - then it's over to you and what you want to do with it.

= What is WorpDrive? =

WorpDrive is a new, [far more clever approach to WordPress backup and restore](http://worpdrive.com/?src=wpt_readme), 
and is a premium product available from with the iControlWP control panel.

It doesn't use FTP, Amazon S3, or any of the traditional painful approaches to website backup, 
and you don't need to buy/rent any other 3rd party storage service.

WorpDrive is an ALL-IN-ONE backup and restore system for your WordPress website and is a bargain at twice the price.

= Is WorpDrive free? =

No. WorpDrive is available for a small monthly fee.

== Screenshots ==

1. At-a-Glance Summary tell you all your available updates split across plugin/theme names and site names with options to update all, or update individually.

2. See all your sites at a glance, which sites have upgrades available and where.

3. See a full summary of your site information, updates, WordPress Core version, Themes and much more.

4. Get a view of all your plugins and how each one is distributed across each site.

== Changelog ==

= 2.3.6 =

* IMPROVE:	W3 Total Cache compatibility.

= 2.3.5 =

* FIX:		WPMS notices.

= 2.3.4 =

* ADDED:	Full support for WP Engine.

= 2.3.3 =

* ADDED: Support for automatically adding whitelisted IP addresses for the WordPress Simple Firewall plugin.

= 2.3.2 =

* ADDED: Direct support for WordPress Firewall 2 plugin by automatically whitelist iControlWP IPs.
* ADDED: Support for the Ultimate Maintenance Mode plugin.

= 2.2 =

* ADDED: Ability to add a site to an iControlWP account from within the WordPress site itself
* ADDED: Support for the underConstruction plugin to ensure we by-pass any redirection.

= 2.1 =

* ADDED: support for initial plugin white labelling options
* ADDED: login-as redirect options and support for WPMS login as
* ADDED: Automatic Better WP Security IP Whitelisting for the iControlWP service
* ADDED: Automatic Wordfence IP Whitelisting for the iControlWP service

= 2.0 =

* NAME CHANGE: Was Worpit, now iControlWP.com
* ADDED: support for logging in as any user, not just admin.
* FIX: preventing the plugin from disconnecting from the service in some cases.

= 1.3.1 =

* FIX: Improved security

= 1.3.0 =

* CHANGED: Plugin communication with iControlWP App changed to help avoid security restrictions that impact direct access to PHP files.

= 1.2.3 =

* TWEAKED: Plugin's custom access rules.

= 1.2.2 =

* ADDED: a fix for whe na site changes its underlying file structure and the location of the plugin moves.

= 1.2.1 =

* Plugin now redirects to the iControlWP settings page upon activation.
* FIX: a bug with the code in the plugin option to mask WP version.

= 1.2.0 =

* Plugin re-architecture to use HTTP GET instead of POST to receive directives from worpitapp.com
* Tested with WordPress 3.5

= 1.1.3 =

* Adds custom options for setting various security related WordPress settings.
* Now easier to find the plugin URL when adding a site.

= 1.1.2 =

* Adds a .htaccess to the plugin root folder to cater for people who don't have their own .htaccess to prevent directory listing
* Fixes compatibility with other plugins who have the same function names in some cases.
* Work around Maintenance Mode plugin so iControlWP commands still work even in maintenance mode.

= 1.1.1 =

* Fix for handshaking features.

= 1.1.0 =

* Minimum required version to support [WorpDrive WordPress Backup and Recovery Service](http://worpdrive.com/?src=wpt_readme)
* Better stability features that also allow for better handling of errors and unexpected plugin output.

= 1.0.15 =

* Removes interference from the 'Secure WordPess' plugin when the iControlWP plugin initialises due to a request from the iControlWP service.

= 1.0.14 =

* No functional change, just some wording on plugin.

= 1.0.13 =

* Latest stable release.

= 1.0 =

* Worpit - Manage WordPress Better Initial Release.

== Upgrade Notice ==

= 2.0 =

* NAME CHANGE: Was Worpit, now iControlWP.com
* ADDED: support for logging in as any user, not just admin.
* ADDED: Support for WordPress Firewall 2, Better WP Security, and Wordfence by automatically whitelist iControlWP IPs.
* ADDED: Ability to add a site to an iControlWP account from within the WordPress site itself
* ADDED: Support for the underConstruction and Ultimate Maintenance Mode plugins.
