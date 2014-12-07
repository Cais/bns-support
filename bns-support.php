<?php
/*
Plugin Name: BNS Support
Plugin URI: http://buynowshop.com/plugins/bns-support/
Description: Displays useful technical support information in a widget area (sidebar); or, via a shortcode on a post or page. The displayed details are easy to share by copying and pasting. Information available includes such things as the web site URL; the WordPress version; the current theme; a list of active plugins ... and much more. This is help for those that help. NB: The information is only viewable by logged-in users, and by default, only the site administrator(s).
Version: 1.9
Text Domain: bns-support
Author: Edward Caissie
Author URI: http://edwardcaissie.com/
License: GNU General Public License v2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/**
 * BNS Support
 * Displays useful technical support information in a widget area (sidebar); or,
 * via a shortcode on a post or page. The displayed details are easy to share by
 * copying and pasting. Information available includes such things as the web
 * site URL; the WordPress version; the current theme; a list of active plugins
 * ... and much more. This is help for those that help.
 *
 * NB: The information is only viewable by logged-in users, and by default, only
 * the site administrator(s).
 *
 * @package     BNS_Support
 * @version     1.9
 * @date        December 2014
 *
 * @link        http://buynowshop.com/plugins/bns-support/
 * @link        https://github.com/Cais/bns-support/
 * @link        https://wordpress.org/plugins/bns-support/
 *
 * @author      Edward Caissie <edward.caissie@gmail.com>
 * @copyright   Copyright (c) 2009-2014, Edward Caissie
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 2, as published by the
 * Free Software Foundation.
 *
 * You may NOT assume that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to:
 *
 *      Free Software Foundation, Inc.
 *      51 Franklin St, Fifth Floor
 *      Boston, MA  02110-1301  USA
 *
 * The license for this software can also likely be found here:
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
class BNS_Support_Widget extends WP_Widget {
	/**
	 * Constructor / BNS Support Widget
	 *
	 * @package     BNS_Support
	 * @since       0.1
	 *
	 * @internal    Requires WordPress version 3.6
	 * @internal    @uses shortcode_atts - uses optional filter variable
	 *
	 * @uses        (CONSTANT)  WP_CONTENT_DIR
	 * @uses        (GLOBAL)    $wp_version
	 * @uses        BNS_Support_Widget::WP_Widget(factory)
	 * @uses        BNS_Support_Widget::BNS_Support_load_widget
	 * @uses        BNS_Support_Widget::scripts_and_styles
	 * @uses        BNS_Support_Widget::extra_theme_headers
	 * @uses        BNS_Support_Widget::bns_support_shortcode
	 * @uses        BNS_Support_Widget::bns_support_plugin_meta
	 * @uses        __
	 * @uses        add_action
	 * @uses        add_filter
	 * @uses        add_shortcode
	 * @uses        apply_filters
	 * @uses        content_url
	 *
	 * @version     1.8
	 * @date        April 20, 2014
	 * Added `bns_support_exit_message` filter
	 * Added Plugin Row Meta details
	 * Defined constants `BNS_CUSTOM_PATH` and `BNS_CUSTOM_URL`
	 * Modified "short" description for better aesthetics in Appearance > Widgets panel
	 * Removed `width` array element from `$control_ops` as not necessary
	 * Updated required WordPress version to 3.6
	 *
	 * @version     1.8.1
	 * @date        May 19, 2014
	 * Added check for defined constants `BNS_CUSTOM_PATH` and `BNS_CUSTOM_URL`
	 *
	 * @version     1.9
	 * @date        December 7, 2014
	 * Added constant defining `BNS_SUPPORT_HOME` as `BuyNowShop.com` for use in reference URL paths
	 */
	function BNS_Support_Widget() {
		/**
		 * Check installed WordPress version for compatibility
		 * @internal    Requires WordPress version 3.6
		 * @internal    @uses shortcode_atts with optional shortcode filter parameter
		 */
		global $wp_version;
		$exit_message = apply_filters( 'bns_support_exit_message', __( 'BNS Support requires WordPress version 3.6 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please Update!</a>', 'bns-support' ) );
		if ( version_compare( $wp_version, "3.6", "<" ) ) {
			exit ( $exit_message );
		}
		/** End if - version compare */

		/** Widget settings */
		$widget_ops = array(
			'classname'   => 'bns-support',
			'description' => __( 'Helpful technical support details for logged in users.', 'bns-support' )
		);

		/** Widget control settings */
		$control_ops = array( 'id_base' => 'bns-support' );

		/** Create the widget */
		$this->WP_Widget( 'bns-support', 'BNS Support', $widget_ops, $control_ops );

		/** Define plugin home URL */
		if ( ! defined( 'BNS_SUPPORT_HOME' ) ) {
			define( 'BNS_SUPPORT_HOME', 'BuyNowShop.com' );
		}
		/** End if - not defined */

		/** Define location for BNS plugin customizations */
		if ( ! defined( 'BNS_CUSTOM_PATH' ) ) {
			define( 'BNS_CUSTOM_PATH', WP_CONTENT_DIR . '/bns-customs/' );
		}
		/** end if - not defined */
		if ( ! defined( 'BNS_CUSTOM_URL' ) ) {
			define( 'BNS_CUSTOM_URL', content_url( '/bns-customs/' ) );
		}
		/** end if - not defined */

		/** Add scripts and styles */
		add_action(
			'wp_enqueue_scripts', array(
				$this,
				'scripts_and_styles'
			)
		);

		/** Add custom headers */
		add_filter(
			'extra_theme_headers', array(
				$this,
				'extra_theme_headers'
			)
		);

		/** Add shortcode */
		add_shortcode(
			'tech_support', array(
				$this,
				'bns_support_shortcode'
			)
		);

		/** Add Plugin Row Meta details */
		add_filter(
			'plugin_row_meta', array(
				$this,
				'bns_support_plugin_meta'
			), 10, 2
		);

		/** Add widget */
		add_action( 'widgets_init', array( $this, 'BNS_Support_load_widget' ) );

	}
	/** End function - constructor */


	/**
	 * BNS Support Extra Theme Headers
	 * Add the 'WordPress Tested Version', 'WordPress Required Version' and
	 * 'Template Version' custom theme header details for reference
	 *
	 * @package  BNS_Support
	 * @since    1.4
	 *
	 * @param   $headers
	 *
	 * @return  array
	 *
	 * @internal see WordPress core trac ticket #16868
	 * @link     https://core.trac.wordpress.org/ticket/16868
	 *
	 * @version  1.9
	 * @date     December 6, 2014
	 * Renamed to `extra_theme_headers`
	 */
	function extra_theme_headers( $headers ) {

		if ( ! in_array( 'WordPress Tested Version', $headers ) ) {
			$headers[] = 'WordPress Tested Version';
		}
		/** End if - not in array */

		if ( ! in_array( 'WordPress Required Version', $headers ) ) {
			$headers[] = 'WordPress Required Version';
		}
		/** End if - not in array */

		if ( ! in_array( 'Template Version', $headers ) ) {
			$headers[] = 'Template Version';
		}

		/** End if - not in array */

		return $headers;

	}
	/** End function - extra theme headers */


	/**
	 * Enqueue Plugin Scripts and Styles
	 * Adds plugin stylesheet and allows for custom stylesheet to be added by end-user.
	 *
	 * @package BNS_Support
	 * @since   1.0
	 *
	 * @uses    (CONSTANT) BNS_CUSTOM_PATH
	 * @uses    (CONSTANT) BNS_CUSTOM_URL
	 * @uses    BNS_Support_Widget::plugin_data
	 * @uses    plugin_dir_path
	 * @uses    plugin_dir_url
	 * @uses    wp_enqueue_style
	 *
	 * @version 1.2
	 * @date    August 2, 2012
	 * Programmatically add version number to enqueue calls
	 *
	 * @version 1.6.1
	 * @date    December 7, 2013
	 * Add the option to put custom stylesheet in `/wp-content/` folder
	 *
	 * @version 1.8
	 * @date    April 20, 2014
	 * Move custom stylesheet into `/wp-content/bns-customs/` folder
	 */
	function scripts_and_styles() {
		/** @var string $bns_support_data - holds the plugin header data */
		$bns_support_data = $this->plugin_data();

		/* Enqueue Scripts */
		/* Enqueue Styles */
		wp_enqueue_style( 'BNS-Support-Style', plugin_dir_url( __FILE__ ) . 'bns-support-style.css', array(), $bns_support_data['Version'], 'screen' );

		/**
		 * Add custom styles
		 * NB: This location will be killed when plugin is updated due to core
		 * WordPress functionality - place the custom stylesheet directly in
		 * the /wp-content/ folder for future proofing your custom styles.
		 */
		if ( is_readable( plugin_dir_path( __FILE__ ) . 'bns-support-custom-style.css' ) ) {
			wp_enqueue_style( 'BNS-Support-Custom-Style', plugin_dir_url( __FILE__ ) . 'bns-support-custom-style.css', array(), $bns_support_data['Version'], 'screen' );
		}
		/** End if - is readable */

		/** For custom stylesheets in the /wp-content/bns-custom/ folder */
		if ( is_readable( BNS_CUSTOM_PATH . 'bns-support-custom-style.css' ) ) {
			wp_enqueue_style( 'BNS-Support-Custom-Style', BNS_CUSTOM_URL . 'bns-support-custom-style.css', array(), $bns_support_data['Version'], 'screen' );
		}
		/** End if - is readable */

	}
	/** End function - scripts and styles */


	/**
	 * Theme Version Check
	 * Using custom headers from the theme if they exist, check what version of
	 * WordPress the theme has been tested up to and what version of WordPress
	 * the theme requires. Also note, if the detail exists, the Parent-Theme
	 * (Template) version the Child-Theme references.
	 *
	 * @internal see core trac ticket #16868
	 * @link     https://core.trac.wordpress.org/ticket/16868
	 *
	 * @package  BNS_Support
	 * @since    1.4
	 *
	 * @uses     __
	 * @uses     apply_filters
	 *
	 * @param   $wp_tested
	 * @param   $wp_required
	 * @param   $wp_template
	 *
	 * @return  string
	 */
	function theme_version_check( $wp_tested, $wp_required, $wp_template ) {
		/** @var string $output - initialize as empty string */
		$output = '';

		if ( ( ! empty( $wp_tested ) ) || ( ! empty( $wp_required ) ) || ( ! empty( $wp_template ) ) ) {

			$output .= '<ul>';

			if ( ! empty( $wp_tested ) ) {
				$output .= '<li class="bns-support-theme-tested">'
				           . sprintf(
						'<strong>%1$s</strong>: %2$s',
						apply_filters( 'bns_support_theme_tested', __( 'Tested To', 'bns-support' ) ),
						$wp_tested
					)
				           . '</li>';
			}
			/** End if - not empty tested */

			if ( ! empty( $wp_required ) ) {
				$output .= '<li class="bns-support-theme-required">'
				           . sprintf(
						'<strong>%1$s</strong>: %2$s',
						apply_filters( 'bns_support_theme_required', __( 'Required', 'bns-support' ) ),
						$wp_required
					)
				           . '</li>';
			}
			/** End if - not empty required */

			if ( ! empty( $wp_template ) && is_child_theme() ) {
				$output .= '<li class="bns-support-template">'
				           . sprintf(
						'<strong>%1$s</strong>: %2$s',
						apply_filters( 'bns_support_template', __( 'Parent Version', 'bns-support' ) ),
						$wp_template
					)
				           . '</li>';
			}
			/** End if - not empty tested */

			$output .= '</ul>';

		}

		/** End if - not empty */

		return $output;

	}
	/** End function - theme version check */


	/**
	 * Mod Rewrite Check
	 *
	 * @package BNS_Support
	 * @since   1.5
	 *
	 * @uses    __
	 * @uses    apply_filters
	 *
	 * @return  string|null - Enabled|Disabled
	 *
	 * @version 1.7
	 * @date    January 25, 2014
	 * Refactored to move entire check and output into method
	 *
	 * @version 1.9
	 * @date    December 7, 2014
	 * Use `apply_filters` on both `return` statements
	 */
	function mod_rewrite_check() {

		if ( function_exists( 'apache_get_modules' ) ) {

			if ( in_array( 'mod_rewrite', apache_get_modules() ) ) {
				$rewrite_check = __( 'Mod Rewrite: Enabled', 'bns-support' );
			} else {
				$rewrite_check = __( 'Mod Rewrite: Disabled', 'bns-support' );
			}

			/** End if - in array */

			return apply_filters( 'bns_support_mod_rewrite', '<li class="bns-support-mod-rewrite">' . $rewrite_check . '</li>' );

		} else {

			/** If there is nothing to return then return nothing ... er, null */
			return apply_filters( 'bns_support_mod_rewrite', null );

		}

	}
	/** End function - mod rewrite check */


	/**
	 * Memory Limit Value
	 * Returns the value of the PHP Memory Limit or indicates no limit is set
	 *
	 * @package BNS_Support
	 * @since   1.7
	 *
	 * @uses    __
	 * @uses    apply_filters
	 *
	 * @return  mixed|void
	 */
	function memory_limit_value() {

		if ( ini_get( 'memory_limit' ) == '-1' ) {
			$value = __( 'No Memory Limit Set', 'bns-support' );
		} else {
			$value = sprintf( __( 'Memory Limit: %1$s', 'bns-support' ), ini_get( 'memory_limit' ) );
		}

		/** End if - memory limit */

		return apply_filters( 'bns_support_memory_limit_value', '<li class="bns-support-memory-limit">' . $value . '</li>' );

	}
	/** End function - memory limit value */


	/**
	 * PHP Details
	 * Returns the PHP details of the installation server
	 *
	 * @package BNS_Support
	 * @since   1.6.3
	 *
	 * @uses    BNS_Support::memory_limit_value
	 * @uses    BNS_Support::mod_rewrite_check
	 * @uses    __
	 * @uses    apply_filters
	 *
	 * @version 1.7
	 * @date    January 25, 2014
	 * Added `memory_limit_value` to output
	 * Moved all Mod Rewrite code into `mod_rewrite_check` method
	 *
	 * @version 1.9
	 * @date    December 7, 2014
	 * Added `bns_support_php_details` filter hook to return statement
	 */
	function php_details() {
		/** PHP Version */
		$output = '<li class="bns-support-php-version"><!-- PHP Details Start -->';

		$output .= apply_filters(
			'bns_support_php_version',
			sprintf(
				__( '<strong>PHP version:</strong> %1$s', 'bns-support' ),
				phpversion()
			)
		);

		$output .= '<ul class="bns-support-php-sub-details">';

		/** Add PHP Safe Mode status */
		$output .= ini_get( 'safe_mode' ) ? '<li>' . __( 'Safe Mode: On', 'bns-support' ) . '</li>' : '<li>' . __( 'Safe Mode: Off', 'bns-support' ) . '</li>';

		/** Add PHP Allow URL fopen status */
		$output .= ini_get( 'allow_url_fopen' ) ? '<li>' . __( 'Allow URL fopen:  On', 'bns-support' ) . '</li>' : '<li>' . __( 'Allow URL fopen:  Off', 'bns-support' ) . '</li>';

		/** Add PHP Memory Limit value */
		$output .= $this->memory_limit_value();

		/** Add Mod Rewrite status */
		$output .= $this->mod_rewrite_check();

		$output .= '</ul><!-- .bns-support-php-sub-details -->';

		$output .= '</li><!-- PHP Details End -->';

		return apply_filters( 'bns_support_php_details', $output );

	}
	/** End function - bns support shortcode */


	/**
	 * GD Library Version
	 * Returns the version of the GD extension.
	 *
	 * @package BNS_Support
	 * @since   1.7
	 *
	 * @uses    __
	 * @uses    apply_filters
	 *
	 * @version 1.9
	 * @date    December 7, 2014
	 * Added `bns_support_gd_library_version` in the return statement
	 */
	function gd_library_version() {

		if ( function_exists( 'gd_info' ) ) {

			$info = gd_info();
			$keys = array_keys( $info );

			$results = sprintf( __( '<li><strong>GD Library Support:</strong> %1$s</li>', 'bns-support' ), $info[ $keys[0] ] );

		} else {

			$results = __( '<li><strong>GD Library Support:</strong> none</li>', 'bns-support' );

		}
		/** End if - function exists */

		return apply_filters( 'bns_support_gd_library_version', $results );

	}
	/** End function - gd library version */


	/**
	 * MySQL Version Details
	 * Returns a human readable version of the MySQL server version
	 *
	 * @package BNS_Support
	 * @since   1.6.1
	 *
	 * @uses    __
	 * @uses    apply_filters
	 *
	 * @version 1.6.2
	 * @date    December 10, 2013
	 * Corrected database connection
	 */
	function mysql_version_details() {
		/** MySQL Version */
		/** @var $mysql_version_number - pull MySQL server version details */
		$mysql_version_number = mysqli_get_server_version( mysqli_connect( DB_HOST, DB_USER, DB_PASSWORD ) );
		/** Deconstruct the version number for more easily read output */
		/** @var $main_version - stripped minor and sub versions */
		$main_version = floor( $mysql_version_number / 10000 );
		/** @var $minor_version - stripped major and sub versions */
		$minor_version = floor( ( $mysql_version_number - $main_version * 10000 ) / 100 );
		/** @var $sub_version - stripped major and minor versions */
		$sub_version = $mysql_version_number - ( $main_version * 10000 + $minor_version * 100 );

		/** @var string $mysql_version_output - re-construct the version number to a more easily read output */
		$mysql_version_output = $main_version . '.' . $minor_version . '.' . $sub_version;

		/** Return the filtered MySQL version */

		return '<li class="bns-support-mysql-version">'
		       . apply_filters(
			'bns_support_mysql_version',
			sprintf(
				__( '<strong>MySQL version:</strong> %1$s', 'bns-support' ),
				$mysql_version_output
			)
		)
		       . '</li>';
	}
	/** End function - mysql version details */


	/**
	 * Get Plugin Data
	 * Collects the information about the plugin from the first 8192 characters
	 * of the plugin file
	 *
	 * @package    BNS_Support
	 * @since      1.7
	 *
	 * @uses       apply_filters
	 *
	 * @param    $plugin_file
	 *
	 * @return    array|string
	 */
	function get_plugin_data( $plugin_file ) {
		/** We don't need to write to the file, so just open for reading. */
		$fp = fopen( $plugin_file, 'r' );

		/** Pull only the first 8kB of the file in. */
		$plugin_data = fread( $fp, 8192 );

		/** PHP will close file handle, but we are good citizens. */
		fclose( $fp );

		preg_match( '|Plugin Name:(.*)$|mi', $plugin_data, $name );
		preg_match( '|Plugin URI:(.*)$|mi', $plugin_data, $uri );
		preg_match( '|Version:(.*)|i', $plugin_data, $version );
		preg_match( '|Description:(.*)$|mi', $plugin_data, $description );
		preg_match( '|Author:(.*)$|mi', $plugin_data, $author_name );
		preg_match( '|Author URI:(.*)$|mi', $plugin_data, $author_uri );
		preg_match( '|Text Domain:(.*)$|mi', $plugin_data, $text_domain );
		preg_match( '|Domain Path:(.*)$|mi', $plugin_data, $domain_path );

		foreach (
			array(
				'name',
				'uri',
				'version',
				'description',
				'author_name',
				'author_uri',
				'text_domain',
				'domain_path'
			) as $field
		) {
			if ( ! empty( ${$field} ) ) {
				${$field} = trim( ${$field}[1] );
			} else {
				${$field} = '';
			}
			/** End if - not empty */
		}
		/** End foreach - array */

		$plugin_data = array(
			'Name'        => $name,
			'Title'       => $name,
			'PluginURI'   => $uri,
			'Description' => $description,
			'Author'      => $author_name,
			'AuthorURI'   => $author_uri,
			'Version'     => $version,
			'TextDomain'  => $text_domain,
			'DomainPath'  => $domain_path
		);

		return apply_filters( 'bns_support_plugin_data', $plugin_data );

	}
	/** End function - get plugin data */


	/**
	 * BNS List Active Plugins
	 * @link       http://wordpress.org/extend/plugins/wp-plugin-lister/
	 * @author     Paul G Petty
	 * @link       http://paulgriffinpetty.com
	 *
	 * Some of the functionality of Paul G Getty's Plugin Lister code has been
	 * used to replace the old code by Lester Chan
	 *
	 * Completely merged, stripped out excess, and rewritten 'Plugin Lister'
	 * @package    BNS_Support
	 * @since      1.1
	 *
	 * @uses       (CONSTANT) WP_PLUGIN_DIR
	 * @uses       __
	 * @uses       apply_filters
	 * @uses       get_option
	 * @uses       get_plugin_data
	 *
	 * @version    1.4
	 * @date       February 14, 2013
	 * Sorted out AuthorURI conditional test
	 *
	 * @version    1.7
	 * @date       January 26, 2014
	 * Renamed function to `BNS List Active Plugins` from `WP List All Active Plugins`
	 * Clean up output and improve i18n implementation
	 * Change from echo to return data
	 * Added filter `bns_support_plugin_list`
	 * Moved `get_plugin_data` out of function and call as method instead
	 */
	function bns_list_active_plugins() {

		$p = get_option( 'active_plugins' );

		$plugin_list = '';

		$plugin_list .= '<ul class="bns-support-plugin-list">';

		foreach ( $p as $q ) {

			$d = $this->get_plugin_data( WP_PLUGIN_DIR . '/' . $q );

			$plugin_list .= '<li class="bns-support-plugin-list-item">';

			if ( ! empty( $d['AuthorURI'] ) ) {
				$plugin_list .= sprintf(
					                __( '%1$s by %2$s %3$s', 'bns-support' ),
					                sprintf(
						                '<strong><a href="' . $d['PluginURI'] . '">' . __( '%1$s %2$s', 'bns-support' ) . '</a></strong>',
						                $d['Title'],
						                $d['Version']
					                ),
					                $d['Author'],
					                '(<a href="' . $d['AuthorURI'] . '">url</a>)'
				                ) . '<br />';
			} else {
				$plugin_list .= sprintf(
					                __( '%1$s by %2$s', 'bns-support' ),
					                sprintf(
						                '<strong><a href="' . $d['PluginURI'] . '">' . __( '%1$s %2$s', 'bns-support' ) . '</a></strong>',
						                $d['Title'],
						                $d['Version']
					                ),
					                $d['Author']
				                ) . '<br />';
			}
			/** End if - not empty Author URI */

			$plugin_list .= '</li>';

		}
		/** End foreach - p as q */

		$plugin_list .= '</ul>';

		return apply_filters( 'bns_support_plugin_list', $plugin_list );

	}
	/** End function - list all active plugins */


	/**
	 * Widget
	 *
	 * @package BNS_Support
	 * @since   0.1
	 *
	 * @uses    (CONSTANT) WP_DEBUG
	 * @uses    (CONSTANT) BNS_SUPPORT_HOME
	 * @uses    (GLOBAL) $current_user
	 * @uses    BNS_Support_Widget::bns_list_active_plugins
	 * @uses    BNS_Support_Widget::gd_library_version
	 * @uses    BNS_Support_Widget::mysql_version_details
	 * @uses    BNS_Support_Widget::php_details
	 * @uses    __
	 * @uses    apply_filters
	 * @uses    current_user_can
	 * @uses    get_bloginfo
	 * @uses    get_current_site
	 * @uses    is_child_theme
	 * @uses    is_multisite
	 * @uses    is_user_logged_in
	 * @uses    wp_get_theme
	 *
	 * @param   array $args
	 * @param   array $instance
	 *
	 * @version 1.7
	 * @date    January 27, 2014
	 * Added GD Library Support display
	 * Fix unordered list of active plugins
	 *
	 * @version 1.7.1
	 * @date    February 2, 2014
	 * Removed CSS wrapper and adjusted CSS elements accordingly
	 *
	 * @version 1.9
	 * @date    December 7, 2014
	 * Implemented the `BNS_SUPPORT_HOME` constant
	 */
	function widget( $args, $instance ) {
		extract( $args );
		/** User-selected settings */
		$title        = apply_filters( 'widget_title', $instance['title'] );
		$blog_admin   = $instance['blog_admin'];
		$show_plugins = $instance['show_plugins'];
		$credits      = $instance['credits'];

		global $current_user;
		/** Must be logged in */
		if ( ( is_user_logged_in() ) ) {
			if ( ( ! $blog_admin ) || ( current_user_can( 'manage_options' ) ) ) {

				/** @var    $before_widget  string - defined by theme */
				echo $before_widget;
				/** Widget $title, $before_widget, and $after_widget defined by theme */
				if ( $title ) {
					/**
					 * @var $before_title   string - defined by theme
					 * @var $after_title    string - defined by theme
					 */
					echo $before_title . $title . $after_title;
				}
				/** End if - title */

				/** Start displaying BNS Support information */
				echo '<ul>';

				/** Blog URL */
				echo apply_filters(
					'bns_support_url',
					'<li class="bns-support-url"><strong>URL</strong>: ' . get_bloginfo( 'url' ) . '</li>'
				);

				/** Versions for various major factors */
				global $wp_version;

				echo '<li class="bns-support-wp-version"><!-- WordPress Details start -->';

				echo apply_filters(
					'bns_support_wp_version',
					'<strong>' . __( 'WordPress Version:', 'bns-support' ) . '</strong>' . ' ' . $wp_version
				);

				/** WP_DEBUG Status */
				echo '<ul><li class="bns-support-wp-debug-status">'
				     . apply_filters(
						'bns_support_wp_debug_status',
						sprintf(
							__( '<strong>WP_DEBUG Status:</strong> %1$s', 'bns-support' ),
							WP_DEBUG
								? __( 'True', 'bns-support' )
								: __( 'False', 'bns-support' )
						)
					)
				     . '</li></ul><!-- bns-support-wp-debug-status -->';

				/** MultiSite Enabled */
				echo '<ul><li class="bns-support-ms-enabled">'
				     . apply_filters(
						'bns_support_ms_enabled',
						sprintf(
							__( '<strong>Multisite Enabled:</strong> %1$s', 'bns-support' ),
							function_exists( 'is_multisite' ) && is_multisite()
								? __( 'True', 'bns-support' )
								: __( 'False', 'bns-support' )
						)
					)
				     . '</li><!-- bns-support-ms-enabled --></ul>';

				echo '</li><!-- WordPress Details End -->';

				/** @var $active_theme_data - array object containing the current theme's data */
				$active_theme_data = wp_get_theme();
				$wp_tested         = $active_theme_data->get( 'WordPress Tested Version' );
				$wp_required       = $active_theme_data->get( 'WordPress Required Version' );
				$wp_template       = $active_theme_data->get( 'Template Version' );

				/** Theme Display with Parent/Child-Theme recognition */
				if ( is_child_theme() ) {
					/** @var $parent_theme_data - array object containing the Parent Theme's data */
					$parent_theme_data = $active_theme_data->parent();
					/** @noinspection PhpUndefinedMethodInspection - IDE commentary */
					$output = sprintf(
						__( '<li class="bns-support-child-theme"><strong>Theme:</strong> %1$s v%2$s a Child-Theme of %3$s v%4$s%5$s</li>', 'bns-support' ),
						$active_theme_data->get( 'Name' ),
						$active_theme_data->get( 'Version' ),
						$parent_theme_data->get( 'Name' ),
						$parent_theme_data->get( 'Version' ),
						$this->theme_version_check( $wp_tested, $wp_required, $wp_template )
					);
					echo apply_filters(
						'bns_support_Child_theme',
						$output
					);
				} else {
					$output = sprintf(
						__( '<li class="bns-support-parent-theme"><strong>Theme:</strong> %1$s v%2$s%3$s</li>', 'bns-support' ),
						$active_theme_data->get( 'Name' ),
						$active_theme_data->get( 'Version' ),
						$this->theme_version_check( $wp_tested, $wp_required, $wp_template )
					);
					echo apply_filters(
						'bns_support_parent_theme',
						$output
					);
				}
				/** End if - is child theme */

				/** Display PHP Details */
				echo $this->php_details();

				/** Display MySQL Version Details */
				echo $this->mysql_version_details();

				/** Display GD Library Version */
				echo $this->gd_library_version();

				/** Multisite Check */
				if ( is_multisite() ) {

					$current_site = get_current_site();
					$home_domain  = 'http://' . $current_site->domain . $current_site->path;
					if ( current_user_can( 'manage_options' ) ) {
						/** If multisite is "true" then direct ALL users to main site administrator */
						echo apply_filters(
							'bns_support_ms_user',
							'<li class="bns-support-ms-user">'
							. sprintf( __( 'Please review with your main site administrator at %1$s for additional assistance.', 'bns-support' ), '<a href="' . $home_domain . '">' . $current_site->site_name . '</a>' )
							. '</li>'
						);
					} else {
						echo apply_filters(
							'bns_support_ms_admin',
							'<li class="bns-support-ms-admin">' . __( 'You are the Admin!', 'bns-support' ) . '</li>'
						);
					}
					/** End if - current user can */

				} else {

					/** ---- Current User Level ---- */
					$user_roles = $current_user->roles;
					$user_role  = array_shift( $user_roles );
					echo apply_filters(
						'bns_support_current_user',
						'<li class="bns-support-current-user">'
						. sprintf( __( '<strong>Current User Role</strong>: %1$s ', 'bns-support' ), $user_role )
						. '</li>'
					);

					if ( $show_plugins ) {
						echo '<li class="bns-support-active-plugins">' . apply_filters(
								'bns_support_active_plugins',
								'<strong>' . __( 'Active Plugins:', 'bns-support' ) . '</strong>'
							);

						/** Show Active Plugins List */
						echo $this->bns_list_active_plugins();

						echo '</li>';
					}
					/** End if - show plugins */

				}
				/** End if - is multisite */

				/** Leave some wiggle room at the end of the output */
				apply_filters( 'bns_support_extended', null );

				echo '</ul>';
				/** End - Display BNS Support information */

				/** Gratuitous self-promotion */
				if ( $credits ) {
					echo apply_filters(
						'bns_support_credits',
						'<h6 class="bns-support-credits">'
						. sprintf( __( 'Compliments of %1$s at %2$s', 'bns-support' ), '<a href="http://' . BNS_SUPPORT_HOME . '/wordpress-services/" target="_blank">WordPress Services</a>', '<a href="http://' . BNS_SUPPORT_HOME . '" target="_blank">' . BNS_SUPPORT_HOME . '</a>' )
						. '</h6>'
					);
				}
				/** End if - credits */

				/** @var $after_widget string - defined by theme */
				echo $after_widget;

			}
			/** End if - admin logged in */

		}
		/** End if - user logged in */

	}
	/** End function - widget */


	/**
	 * Update
	 *
	 * @package BNS_Support
	 * @since   0.1
	 *
	 * @param   array $new_instance
	 * @param   array $old_instance
	 *
	 * @return  array
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		/* Strip tags (if needed) and update the widget settings. */
		$instance['title']        = strip_tags( $new_instance['title'] );
		$instance['blog_admin']   = $new_instance['blog_admin'];
		$instance['show_plugins'] = $new_instance['show_plugins'];
		$instance['credits']      = $new_instance['credits'];

		return $instance;

	}
	/** End function - update */


	/**
	 * Form
	 *
	 * @package BNS_Support
	 * @since   0.1
	 *
	 * @uses    (CONSTANT) BNS_SUPPORT_HOME
	 * @uses    WP_Widget::get_field_id
	 * @uses    WP_Widget::get_field_name
	 * @uses    _e
	 * @uses    checked
	 * @uses    wp_parse_args
	 *
	 * @param   array $instance
	 *
	 * @return  string|void
	 *
	 * @version 1.6
	 * @date    September 7, 2013
	 * Changed `show_plugins` default to true (more common usage than false)
	 *
	 * @version 1.9
	 * @date    December 7, 2014
	 * Implemented the `BNS_SUPPORT_HOME` constant
	 */
	function form( $instance ) {
		/* Set up some default widget settings. */
		$defaults = array(
			'title'        => get_bloginfo( 'name' ),
			'blog_admin'   => true,
			'show_plugins' => true,
			'credits'      => false,
		);
		$instance = wp_parse_args( ( array ) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'bns-support' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['blog_admin'], true ); ?>
			       id="<?php echo $this->get_field_id( 'blog_admin' ); ?>"
			       name="<?php echo $this->get_field_name( 'blog_admin' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'blog_admin' ); ?>"><?php _e( 'Only show to administrators?', 'bns-support' ); ?></label>
		</p>

		<hr />

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_plugins'], true ); ?>
			       id="<?php echo $this->get_field_id( 'show_plugins' ); ?>"
			       name="<?php echo $this->get_field_name( 'show_plugins' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_plugins' ); ?>"><?php _e( 'Show active plugins?', 'bns-support' ); ?></label>
		</p>

		<hr />

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['credits'], true ); ?>
			       id="<?php echo $this->get_field_id( 'credits' ); ?>"
			       name="<?php echo $this->get_field_name( 'credits' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'credits' ); ?>"><?php _e( 'Show complimentary link to ', 'bns-support' ); ?></label>
			<a href="http://<?php echo BNS_SUPPORT_HOME; ?>/"><?php echo BNS_SUPPORT_HOME; ?></a>?
		</p>

	<?php
	}
	/** End function - form */


	/**
	 * Load Widget
	 *
	 * @package BNS_Support
	 * @since   0.1
	 *
	 * @uses    register_widget
	 */
	function BNS_Support_load_widget() {
		register_widget( 'BNS_Support_Widget' );
	}
	/** End function  - register widget */


	/**
	 * BNS Support Shortcode
	 *
	 * @package BNS_Support
	 * @since   1.6
	 *
	 * @param   $atts
	 *
	 * @uses    get_bloginfo
	 * @uses    shortcode_atts
	 * @uses    the_widget
	 *
	 * @return  string
	 *
	 * @version 1.6.1
	 * @date    September 7, 2013
	 * Added shortcode name parameter for core filter auto-creation
	 *
	 * @version 1.8
	 * @date    April 20, 2014
	 * Added CSS class wrapper for shortcode output
	 */
	function bns_support_shortcode( $atts ) {
		/** Let's start by capturing the output */
		ob_start();

		/** Pull the widget together for use elsewhere */
		the_widget(
			'BNS_Support_Widget',
			$instance = shortcode_atts(
				array(
					'title'        => get_bloginfo( 'name' ),
					'blog_admin'   => true,
					'show_plugins' => true,
					'credits'      => false,
				), $atts, 'tech_support'
			),
			$args = array(
				/** clear variables defined by theme for widgets */
				$before_widget = '',
				$after_widget = '',
				$before_title = '',
				$after_title = '',
			)
		);

		/** Get the_widget output and put it into its own variable */
		$bns_support_content = ob_get_clean();

		/** @var string $bns_support_content - wrapped `the_widget` output */
		$bns_support_content = '<div class="bns-support-shortcode">' . $bns_support_content . '</div><!-- bns-support-shortcode -->';

		/** Return the widget output for the shortcode to use */

		return $bns_support_content;

	}
	/** End function - shortcode */


	/**
	 * Plugin Data
	 * Returns the plugin header data as an array
	 *
	 * @package    BNS_Support
	 * @since      1.8
	 *
	 * @uses       get_plugin_data
	 *
	 * @return array
	 */
	function plugin_data() {
		/** Call the wp-admin plugin code */
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		/** @var object $plugin_data - holds the plugin header data */
		$plugin_data = get_plugin_data( __FILE__ );

		return $plugin_data;
	}
	/** End function - plugin data */


	/**
	 * BNS Support Plugin Meta
	 * Adds additional links to plugin meta links
	 *
	 * @package    BNS_SUpport
	 * @since      1.8
	 *
	 * @uses       __
	 * @uses       plugin_basename
	 *
	 * @param   $links
	 * @param   $file
	 *
	 * @return  array $links
	 */
	function bns_support_plugin_meta( $links, $file ) {

		$plugin_file = plugin_basename( __FILE__ );

		if ( $file == $plugin_file ) {

			$links = array_merge(
				$links, array(
					'fork_link'    => '<a href="https://github.com/Cais/BNS-Support">' . __( 'Fork on GitHub', 'bns-support' ) . '</a>',
					'wish_link'    => '<a href="http://www.amazon.ca/registry/wishlist/2NNNE1PAQIRUL">' . __( 'Grant a wish?', 'bns-support' ) . '</a>',
					'support_link' => '<a href="http://wordpress.org/support/plugin/bns-support">' . __( 'WordPress Support Forums', 'bns-support' ) . '</a>'
				)
			);

		}

		/** End if - file is the same as plugin */

		return $links;

	}
	/** End function - plugin meta */


}

/** End class - BNS Support Widget */

/** @var $bns_support - instantiate the class */
$bns_support = new BNS_Support_Widget();