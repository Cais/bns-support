<?php
/**
 * Plugin Name: BNS Support
 * Plugin URI: https://buynowshop.com/plugins/bns-support/
 * Description: Displays useful technical support information in a widget area (sidebar); or, via a shortcode on a post or page. The displayed details are easy to share by copying and pasting. Information available includes such things as the web site URL; the WordPress version; the current theme; a list of active plugins ... and much more. This is help for those that help. NB: The information is only viewable by logged-in users, and by default, only the site administrator(s).
 * Version: 2.3
 * Text Domain: bns-support
 * Author: Edward Caissie
 * Author URI: https://edwardcaissie.com/
 * License: GNU General Public License v2
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
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
 * @version     2.3
 * @date        April 2019
 *
 * @link        https://buynowshop.com/plugins/bns-support/
 * @link        https://github.com/Cais/bns-support/
 * @link        https://wordpress.org/plugins/bns-support/
 *
 * @author      Edward Caissie <edward.caissie@gmail.com>
 * @copyright   Copyright (c) 2009-2019, Edward Caissie
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

/**
 * Class BNS_Support_Widget
 */
class BNS_Support_Widget extends WP_Widget {

	/**
	 * Set the instance to null initially
	 *
	 * @var $instance null
	 */
	private static $instance = null;

	/**
	 * Create Instance
	 *
	 * Creates a single instance of the class
	 *
	 * @return    null|BNS_Support_Widget
	 *
	 * @since    2.0
	 * @date     June 7, 2015
	 *
	 * @package  BNS_Support_Widget
	 */
	public static function create_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}


	/**
	 * Constructor / BNS Support Widget
	 *
	 * @package     BNS_Support
	 * @since       0.1
	 *
	 * @internal    Requires WordPress version 3.6
	 * @internal    @see shortcode_atts - uses optional filter variable
	 *
	 * @see         (CONSTANT)  WP_CONTENT_DIR
	 * @see         (GLOBAL)    $wp_version
	 * @see         BNS_Support_Widget::WP_Widget(factory)
	 * @see         BNS_Support_Widget::BNS_Support_load_widget
	 * @see         BNS_Support_Widget::scripts_and_styles
	 * @see         BNS_Support_Widget::extra_theme_headers
	 * @see         BNS_Support_Widget::bns_support_shortcode
	 * @see         BNS_Support_Widget::bns_support_plugin_meta
	 * @see         __
	 * @see         add_action
	 * @see         add_filter
	 * @see         add_shortcode
	 * @see         apply_filters
	 * @see         content_url
	 *
	 * @version     2.0
	 * @date        June 7, 2015
	 *
	 * @version     2.1
	 * @date        July 2015
	 * Renamed constructor method to `__construct`
	 */
	public function __construct() {

		/**
		 * Check installed WordPress version for compatibility
		 *
		 * @internal    Requires WordPress version 3.6
		 * @internal    @see shortcode_atts with optional shortcode filter parameter
		 */
		global $wp_version;
		$exit_message = apply_filters( 'bns_support_exit_message', __( 'BNS Support requires WordPress version 3.6 or newer.', 'bns-support' ) . ' <a href="http://codex.wordpress.org/Upgrading_WordPress">' . __( 'Please Update!', 'bns-support' ) . '</a>' );
		if ( version_compare( $wp_version, '3.6', '<' ) ) {
			exit( esc_html( $exit_message ) );
		}

		/** Widget settings */
		$widget_ops = array(
			'classname'   => 'bns-support',
			'description' => __( 'Helpful technical support details for logged in users.', 'bns-support' ),
		);

		/** Widget control settings */
		$control_ops = array( 'id_base' => 'bns-support' );

		/** Create the widget */
		parent::__construct( 'bns-support', 'BNS Support', $widget_ops, $control_ops );

		/** Define plugin home URL */
		if ( ! defined( 'BNS_SUPPORT_HOME' ) ) {
			define( 'BNS_SUPPORT_HOME', 'BuyNowShop.com' );
		}

		/** Define location for BNS plugin customizations */
		if ( ! defined( 'BNS_CUSTOM_PATH' ) ) {
			define( 'BNS_CUSTOM_PATH', WP_CONTENT_DIR . '/bns-customs/' );
		}
		if ( ! defined( 'BNS_CUSTOM_URL' ) ) {
			define( 'BNS_CUSTOM_URL', content_url( '/bns-customs/' ) );
		}

	}

	/**
	 * BNS Support `init` method
	 *
	 * Add the available hooks used in the plugin.
	 *
	 * @package BNS_Support
	 * @since   2.3
	 *
	 * @see     BNS_Support_Widget::scripts_and_styles
	 * @see     BNS_Support_Widget::extra_theme_headers
	 * @see     BNS_Support_Widget::bns_support_shortcode
	 * @see     BNS_Support_Widget::bns_support_plugin_meta
	 * @see     BNS_Support_Widget::BNS_Support_load_widget
	 * @see     BNS_Support_Widget::update_message
	 * @see     add_action()
	 * @see     add_filter()
	 * @see     add_shortcode()
	 * @see     plugin_basename()
	 */
	public function init() {

		/** Add scripts and styles */
		add_action(
			'wp_enqueue_scripts',
			array(
				$this,
				'scripts_and_styles',
			)
		);

		/** Add custom headers */
		add_filter(
			'extra_theme_headers',
			array(
				$this,
				'extra_theme_headers',
			)
		);

		/** Add shortcode */
		add_shortcode(
			'tech_support',
			array(
				$this,
				'bns_support_shortcode',
			)
		);

		/** Add Plugin Row Meta details */
		add_filter(
			'plugin_row_meta',
			array(
				$this,
				'bns_support_plugin_meta',
			),
			10,
			2
		);

		/** Add widget */
		add_action( 'widgets_init', array( $this, 'BNS_Support_load_widget' ) );

		/** Add plugin update message */
		add_action(
			'in_plugin_update_message-' . plugin_basename( __FILE__ ),
			array(
				$this,
				'update_message',
			)
		);

	}

	/**
	 * BNS Support Extra Theme Headers
	 *
	 * Add the 'WordPress Tested Version', 'WordPress Required Version' and
	 * 'Template Version' custom theme header details for reference
	 *
	 * @param array $headers array of plugin details taken from header block.
	 *
	 * @return  array
	 *
	 * @package  BNS_Support
	 * @since    1.4
	 *
	 * @internal see WordPress core trac ticket #16868
	 * @link     https://core.trac.wordpress.org/ticket/16868
	 *
	 * @version  1.9
	 * @date     December 6, 2014
	 * Renamed to `extra_theme_headers`
	 */
	public function extra_theme_headers( $headers ) {

		if ( ! in_array( 'WordPress Tested Version', $headers, true ) ) {
			$headers[] = 'WordPress Tested Version';
		}

		if ( ! in_array( 'WordPress Required Version', $headers, true ) ) {
			$headers[] = 'WordPress Required Version';
		}

		if ( ! in_array( 'Template Version', $headers, true ) ) {
			$headers[] = 'Template Version';
		}

		return $headers;

	}


	/**
	 * Enqueue Plugin Scripts and Styles
	 * Adds plugin stylesheet and allows for custom stylesheet to be added by end-user.
	 *
	 * @package BNS_Support
	 * @since   1.0
	 *
	 * @see     (CONSTANT) BNS_CUSTOM_PATH
	 * @see     (CONSTANT) BNS_CUSTOM_URL
	 * @see     BNS_Support_Widget::plugin_data
	 * @see     plugin_dir_path
	 * @see     plugin_dir_url
	 * @see     wp_enqueue_style
	 *
	 * @version 1.6.1
	 * @date    December 7, 2013
	 * Add the option to put custom stylesheet in `/wp-content/` folder
	 *
	 * @version 1.8
	 * @date    April 20, 2014
	 * Move custom stylesheet into `/wp-content/bns-customs/` folder
	 *
	 * @version 2.3
	 * @date    2016-06-20
	 * Removed reference to custom stylesheets within plugin folders as not update safe
	 */
	public function scripts_and_styles() {

		/** Holds the plugin header data */
		$bns_support_data = $this->plugin_data();

		/** Enqueue Scripts */
		/** Enqueue Styles */
		wp_enqueue_style( 'BNS-Support-Style', plugin_dir_url( __FILE__ ) . 'bns-support-style.css', array(), $bns_support_data['Version'], 'screen' );

		/** For custom stylesheets in the /wp-content/bns-custom/ folder */
		if ( is_readable( BNS_CUSTOM_PATH . 'bns-support-custom-style.css' ) ) {
			wp_enqueue_style( 'BNS-Support-Custom-Style', BNS_CUSTOM_URL . 'bns-support-custom-style.css', array(), $bns_support_data['Version'], 'screen' );
		}

	}


	/**
	 * Theme Version Check
	 *
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
	 * @see      __
	 * @see      apply_filters
	 *
	 * @param string $wp_tested   tested theme version.
	 * @param string $wp_required required theme version.
	 * @param string $wp_template required theme template version.
	 *
	 * @return  string
	 */
	public function theme_version_check( $wp_tested, $wp_required, $wp_template ) {

		/** Initialize as empty string */
		$output = '';

		if ( ( ! empty( $wp_tested ) ) || ( ! empty( $wp_required ) ) || ( ! empty( $wp_template ) ) ) {

			$output .= '<ul>';

			if ( ! empty( $wp_tested ) ) {
				$output .= '<li class="bns-support-theme-tested">' . sprintf( '<strong>%1$s</strong>: %2$s', apply_filters( 'bns_support_theme_tested', __( 'Tested To', 'bns-support' ) ), $wp_tested ) . '</li>';
			}

			if ( ! empty( $wp_required ) ) {
				$output .= '<li class="bns-support-theme-required">' . sprintf( '<strong>%1$s</strong>: %2$s', apply_filters( 'bns_support_theme_required', __( 'Required', 'bns-support' ) ), $wp_required ) . '</li>';
			}

			if ( ! empty( $wp_template ) && is_child_theme() ) {
				$output .= '<li class="bns-support-template">' . sprintf( '<strong>%1$s</strong>: %2$s', apply_filters( 'bns_support_template', __( 'Parent Version', 'bns-support' ) ), $wp_template ) . '</li>';
			}

			$output .= '</ul>';

		}

		return $output;

	}


	/**
	 * Mod Rewrite Check
	 *
	 * @return  string|null - Enabled|Disabled
	 *
	 * @since   1.5
	 *
	 * @see     __
	 * @see     apply_filters
	 *
	 * @package BNS_Support
	 * @version 1.7
	 * @date    January 25, 2014
	 * Refactored to move entire check and output into method
	 *
	 * @version 1.9
	 * @date    December 7, 2014
	 * Use `apply_filters` on both `return` statements
	 */
	public function mod_rewrite_check() {

		if ( function_exists( 'apache_get_modules' ) ) {

			if ( in_array( 'mod_rewrite', apache_get_modules(), true ) ) {
				$rewrite_check = esc_html__( 'Mod Rewrite: Enabled', 'bns-support' );
			} else {
				$rewrite_check = esc_html__( 'Mod Rewrite: Disabled', 'bns-support' );
			}

			return apply_filters( 'bns_support_mod_rewrite', '<li class="bns-support-mod-rewrite">' . $rewrite_check . '</li>' );

		} else {

			/** If there is nothing to return then return nothing ... er, null */
			return apply_filters( 'bns_support_mod_rewrite', null );

		}

	}


	/**
	 * Memory Limit Value
	 *
	 * Returns the value of the PHP Memory Limit or indicates no limit is set
	 *
	 * @return  mixed
	 * @since   1.7
	 *
	 * @see     __
	 * @see     apply_filters
	 *
	 * @package BNS_Support
	 */
	public function memory_limit_value() {

		$value = ini_get( 'memory_limit' ) === '-1' ?
			esc_html__( 'No Memory Limit Set', 'bns-support' ) :
			/* translators: this is expected to be a number */
			sprintf( esc_html__( 'Memory Limit: %1$s', 'bns-support' ), ini_get( 'memory_limit' ) );

		return apply_filters( 'bns_support_memory_limit_value', '<li class="bns-support-memory-limit">' . $value . '</li>' );

	}


	/**
	 * PHP Details
	 *
	 * Returns the PHP details of the installation server
	 *
	 * @package BNS_Support
	 * @since   1.6.3
	 *
	 * @see     BNS_Support::memory_limit_value
	 * @see     BNS_Support::mod_rewrite_check
	 * @see     __
	 * @see     apply_filters
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
	public function php_details() {

		/** PHP Version */
		$output = '<li class="bns-support-php-version"><!-- PHP Details Start -->';

		$output .= apply_filters(
			'bns_support_php_version',
			sprintf(
				/* translators: this is expected to be a number */
				__( '<strong>PHP version:</strong> %1$s', 'bns-support' ),
				phpversion()
			)
		);

		$output .= '<ul class="bns-support-php-sub-details">';

		/** Add PHP Safe Mode status */
		$output .= ini_get( 'safe_mode' ) ?
			'<li>' . __( 'Safe Mode: On', 'bns-support' ) . '</li>' :
			'<li>' . __( 'Safe Mode: Off', 'bns-support' ) . '</li>';

		/** Add PHP Allow URL fopen status */
		$output .= ini_get( 'allow_url_fopen' ) ?
			'<li>' . __( 'Allow URL fopen:  On', 'bns-support' ) . '</li>' :
			'<li>' . __( 'Allow URL fopen:  Off', 'bns-support' ) . '</li>';

		/** Add PHP Memory Limit value */
		$output .= $this->memory_limit_value();

		/** Add Mod Rewrite status */
		$output .= $this->mod_rewrite_check();

		$output .= '</ul><!-- .bns-support-php-sub-details -->';

		$output .= '</li><!-- PHP Details End -->';

		return apply_filters( 'bns_support_php_details', $output );

	}


	/**
	 * GD Library Version
	 *
	 * Returns the version of the GD extension.
	 *
	 * @package BNS_Support
	 * @since   1.7
	 *
	 * @see     __
	 * @see     apply_filters
	 *
	 * @version 1.9
	 * @date    December 7, 2014
	 * Added `bns_support_gd_library_version` in the return statement
	 *
	 * @version 2.3
	 * @date    2019-04-03
	 * Refactored to a more DRY approach.
	 */
	public function gd_library_version() {

		$results = '<li><strong>' . __( 'GD Library Support', 'bns-support' ) . ':</strong> ';

		if ( function_exists( 'gd_info' ) ) {

			$info = gd_info();
			$keys = array_keys( $info );

			$results .= $info[ $keys[0] ] . '</li>';

		} else {

			$results .= __( 'none', 'bns-support' ) . '</li>';

		}

		return apply_filters( 'bns_support_gd_library_version', $results );

	}


	/**
	 * MySQL Version Details
	 *
	 * Returns a human readable version of the MySQL server version
	 *
	 * @package BNS_Support
	 * @since   1.6.1
	 *
	 * @see     __
	 * @see     apply_filters
	 *
	 * @version 1.6.2
	 * @date    December 10, 2013
	 * Corrected database connection
	 *
	 * @version 2.3
	 * @date    2019-04-03
	 * Refactored to use a query on a copy of the $wpdb global
	 */
	public function mysql_version_details() {

		global $wpdb;
		$this->db             = $wpdb;
		$mysql_version_output = $this->db->get_var( 'SELECT VERSION();' );

		/** Return the filtered MySQL version */
		return '<li class="bns-support-mysql-version">' . apply_filters( 'bns_support_mysql_version', '<strong>' . __( 'MySQL version: ', 'bns-support' ) . '</strong>' . $mysql_version_output ) . '</li>';

	}


	/**
	 * Is There Email
	 *
	 * Tests to see if the PHP mail function is available
	 *
	 * @return string
	 * @since   2.2
	 * @date    September 30, 2015
	 *
	 * @see     __
	 * @see     apply_filters
	 *
	 * @package BNS_Support
	 */
	public function is_there_email() {

		if ( function_exists( 'mail' ) ) {
			$you_have_mail = apply_filters( 'bns_support_mail_yes', __( 'Yes', 'bns-support' ) );
		} else {
			$you_have_mail = apply_filters( 'bns_support_mail_no', __( 'No', 'bns-support' ) );
		}

		return '<li class="bns-support-mail">' . apply_filters( 'bns_support_mail_label', '<strong>' . __( 'PHP mail exists: ', 'bns-support' ) . '</strong>' . $you_have_mail ) . '</li>';

	}


	/**
	 * Collect Plugin Data
	 *
	 * Collects the information about the plugin from the first 8192 characters
	 * of the plugin file
	 *
	 * @param string $plugin_file first 8000 characters of plugin file.
	 *
	 * @return    array|string
	 *
	 * @see        apply_filters
	 *
	 * @package    BNS_Support
	 * @since      1.7
	 *
	 * @version    2.3
	 * @date       2016-06-20
	 * Renamed method to `collect_plugin_data` from `get_plugin_data`
	 */
	public function collect_plugin_data( $plugin_file ) {

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

		foreach ( array( 'name', 'uri', 'version', 'description', 'author_name', 'author_uri', 'text_domain', 'domain_path' ) as $field ) {
			if ( ! empty( ${$field} ) ) {
				${$field} = trim( ${$field}[1] );
			} else {
				${$field} = '';
			}
		}

		$plugin_data = array(
			'Name'        => $name,
			'Title'       => $name,
			'PluginURI'   => $uri,
			'Description' => $description,
			'Author'      => $author_name,
			'AuthorURI'   => $author_uri,
			'Version'     => $version,
			'TextDomain'  => $text_domain,
			'DomainPath'  => $domain_path,
		);

		return apply_filters( 'bns_support_plugin_data', $plugin_data );

	}


	/**
	 * BNS List Active Plugins
	 *
	 * @link       https://wordpress.org/extend/plugins/wp-plugin-lister/
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
	 * @see        (CONSTANT) WP_PLUGIN_DIR
	 * @see        BNS_Support_Widget::collect_plugin_data
	 * @see        __
	 * @see        apply_filters
	 * @see        get_option
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
	 * Moved `collect_plugin_data` out of function and call as method instead
	 */
	public function bns_list_active_plugins() {

		$p = get_option( 'active_plugins' );

		$plugin_list = '';

		$plugin_list .= '<ul class="bns-support-plugin-list">';

		foreach ( $p as $q ) {

			$d = $this->collect_plugin_data( WP_PLUGIN_DIR . '/' . $q );

			$plugin_list .= '<li class="bns-support-plugin-list-item">';

			if ( ! empty( $d['AuthorURI'] ) ) {
				/* translators: the variables are respectively Plugin Name, Author, and Version Number */
				$plugin_list .= sprintf( __( '%1$s by %2$s %3$s', 'bns-support' ), sprintf( '<strong><a href="' . $d['PluginURI'] . '">' . __( '%1$s %2$s', 'bns-support' ) . '</a></strong>', $d['Title'], $d['Version'] ), $d['Author'], '(<a href="' . $d['AuthorURI'] . '">url</a>)' ) . '<br />';

			} else {
				/* translators: the variables are respectively Plugin Name and Author */
				$plugin_list .= sprintf( __( '%1$s by %2$s', 'bns-support' ), sprintf( '<strong><a href="' . $d['PluginURI'] . '">' . __( '%1$s %2$s', 'bns-support' ) . '</a></strong>', $d['Title'], $d['Version'] ), $d['Author'] ) . '<br />';

			}

			$plugin_list .= '</li>';

		}

		$plugin_list .= '</ul>';

		return apply_filters( 'bns_support_plugin_list', $plugin_list );

	}


	/**
	 * Widget
	 *
	 * @param array $args     display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
	 * @param array $instance specific values used in widget instance.
	 *
	 * @package    BNS_Support
	 * @since      0.1
	 *
	 * @see        (CONSTANT) WP_DEBUG
	 * @see        (CONSTANT) BNS_SUPPORT_HOME
	 * @see        (GLOBAL) $current_user
	 * @see        BNS_Support_Widget::bns_list_active_plugins
	 * @see        BNS_Support_Widget::gd_library_version
	 * @see        BNS_Support_Widget::mysql_version_details
	 * @see        BNS_Support_Widget::php_details
	 * @see        apply_filters
	 * @see        current_user_can
	 * @see        esc_attr()
	 * @see        esc_html
	 * @see        esc_html__
	 * @see        esc_url
	 * @see        get_bloginfo
	 * @see        get_current_site
	 * @see        is_child_theme
	 * @see        is_multisite
	 * @see        is_user_logged_in
	 * @see        wp_get_theme
	 *
	 * @version    1.7
	 * @date       January 27, 2014
	 * Added GD Library Support display
	 * Fix unordered list of active plugins
	 *
	 * @version    1.7.1
	 * @date       February 2, 2014
	 * Removed CSS wrapper and adjusted CSS elements accordingly
	 *
	 * @version    1.9
	 * @date       December 7, 2014
	 * Implemented the `BNS_SUPPORT_HOME` constant
	 *
	 * @version    2.3
	 * @date       2019-04-03
	 * Removed credits section
	 * Improved escaping implementation
	 * Removed credits section
	 */
	public function widget( $args, $instance ) {

		/** User-selected settings */
		$title        = apply_filters( 'widget_title', $instance['title'] );
		$blog_admin   = $instance['blog_admin'];
		$show_plugins = $instance['show_plugins'];

		global $current_user;

		/** Must be logged in */
		if ( ( is_user_logged_in() ) ) {

			if ( ( ! $blog_admin ) || ( current_user_can( 'manage_options' ) ) ) {

				echo $args['before_widget'];

				if ( $title ) {
					echo $args['before_title'] . esc_attr( $title ) . $args['after_title'];
				}

				/** Start displaying BNS Support information */
				echo '<ul>';

				/** Blog URL */
				echo '<li class="bns-support-url"><strong>' . esc_html( apply_filters( 'bns_support_url', __( 'Site URL: ', 'bns-support' ) ) ) . '</strong><a href="' . esc_url( get_bloginfo( 'url' ) ) . '">' . esc_url( get_bloginfo( 'url' ) ) . '</a></li>';

				/** Versions for various major factors */
				global $wp_version;

				echo '<li class="bns-support-wp-version"><!-- WordPress Details start -->';

				echo '<strong>' . esc_html( apply_filters( 'bns_support_wp_version', __( 'WordPress Version: ', 'bns-support' ) ) ) . esc_html( $wp_version ) . '</strong>';

				/** WP_DEBUG Status */
				echo '<ul><li class="bns-support-wp-debug-status"><strong>';
				echo esc_html( apply_filters( 'bns_support_wp_debug_status', __( 'WP_DEBUG Status: ', 'bns-support' ) ) ) . '</strong>';
				echo WP_DEBUG ? esc_attr__( 'True', 'bns-support' ) : esc_attr__( 'False', 'bns-support' );
				echo '</li></ul><!-- bns-support-wp-debug-status -->';

				/** MultiSite Enabled */
				echo '<ul><li class="bns-support-ms-enabled"><strong>';
				echo esc_html( apply_filters( 'bns_support_ms_enabled', __( 'Multisite Enabled: ', 'bns-support' ) ) ) . '</strong>';
				function_exists( 'is_multisite' ) && is_multisite() ? esc_attr_e( 'True', 'bns-support' ) : esc_attr_e( 'False', 'bns-support' );
				echo '</li><!-- bns-support-ms-enabled --></ul>';

				echo '</li><!-- WordPress Details End -->';

				/** Get current theme data */
				$active_theme_data = wp_get_theme();
				$wp_tested         = $active_theme_data->get( 'WordPress Tested Version' );
				$wp_required       = $active_theme_data->get( 'WordPress Required Version' );
				$wp_template       = $active_theme_data->get( 'Template Version' );

				/** Theme Display with Parent/Child-Theme recognition */
				if ( is_child_theme() ) {

					/** Get parent theme's data */
					$parent_theme_data = $active_theme_data->parent();

					$output = sprintf(
						/* translators: The variables are the parent theme and its version followed by the child-theme and its version respectively */
						__( '<li class="bns-support-child-theme"><strong>Theme:</strong> %1$s v%2$s a Child-Theme of %3$s v%4$s%5$s</li>', 'bns-support' ),
						$active_theme_data->get( 'Name' ),
						$active_theme_data->get( 'Version' ),
						$parent_theme_data->get( 'Name' ),
						$parent_theme_data->get( 'Version' ),
						$this->theme_version_check( $wp_tested, $wp_required, $wp_template )
					);

					echo apply_filters( 'bns_support_Child_theme', $output );

				} else {

					$output = sprintf(
						/* translators: The variables are the parent theme and its version */
						__( '<li class="bns-support-parent-theme"><strong>Theme:</strong> %1$s v%2$s%3$s</li>', 'bns-support' ),
						$active_theme_data->get( 'Name' ),
						$active_theme_data->get( 'Version' ),
						$this->theme_version_check( $wp_tested, $wp_required, $wp_template )
					);

					echo apply_filters( 'bns_support_parent_theme', $output );

				}

				/** Display PHP Details */
				echo $this->php_details();

				/** Display MySQL Version Details */
				echo $this->mysql_version_details();

				/** Display PHP mail function Details */
				echo $this->is_there_email();

				/** Display GD Library Version */
				echo $this->gd_library_version();

				/** Multisite Check */
				if ( is_multisite() ) {

					$current_site = get_current_site();
					$home_domain  = 'http://' . $current_site->domain . $current_site->path;

					if ( current_user_can( 'manage_options' ) ) {

						/** If multisite is "true" then direct ALL users to main site administrator */
						/* translators: the main multisite name as defined in its settings */
						echo '<li class="bns-support-ms-user">' . esc_html( apply_filters( 'bns_support_ms_user', sprintf( __( 'Please review with your main site administrator at %1$s for additional assistance.', 'bns-support' ), '<a href="' . esc_url( $home_domain ) . '">' . esc_html( $current_site->site_name ) . '</a>' ) ) ) . '</li>';

					} else {

						echo '<li class="bns-support-ms-admin">' . esc_html( apply_filters( 'bns_support_ms_admin', __( 'You are the Admin!', 'bns-support' ) ) ) . '</li>';

					}
				} else {

					/** ---- Current User Level ---- */
					$user_roles = $current_user->roles;
					$user_role  = array_shift( $user_roles );
					echo '<li class="bns-support-current-user"><strong>' . esc_html( apply_filters( 'bns_support_current_user', __( 'Current User Role: ', 'bns-support' ) ) ) . '</strong>' . esc_html( $user_role ) . '</li>';

					if ( $show_plugins ) {

						echo '<li class="bns-support-active-plugins"><strong>' . esc_html( apply_filters( 'bns_support_active_plugins', __( 'Active Plugins:', 'bns-support' ) ) ) . '</strong>';

						/** Show Active Plugins List */
						echo $this->bns_list_active_plugins();

						echo '</li>';

					}
				}

				/** Leave some wiggle room at the end of the output */
				apply_filters( 'bns_support_extended', null );

				echo '</ul>';
				/** End - Display BNS Support information */

				echo $args['after_widget'];

			}
		}

	}


	/**
	 * Update
	 *
	 * @param array $new_instance New settings for this instance as input by the user via WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return  array
	 * @since      0.1
	 *
	 * @see        wp_strip_all_tags()
	 *
	 * @package    BNS_Support
	 *
	 * @version    2.3
	 * @date       2019-04-03
	 * Replaced `strip_tags` with `wp_strip_all_tags`
	 *
	 * @version    2.3
	 * @date       2019-04-03
	 * Removed credits section
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		/* Strip tags (if needed) and update the widget settings. */
		$instance['title']        = wp_strip_all_tags( $new_instance['title'], true );
		$instance['blog_admin']   = $new_instance['blog_admin'];
		$instance['show_plugins'] = $new_instance['show_plugins'];

		return $instance;

	}


	/**
	 * Form
	 *
	 * @param array $instance current widget instance values.
	 *
	 * @package    BNS_Support
	 * @since      0.1
	 *
	 * @see        (CONSTANT) BNS_SUPPORT_HOME
	 * @see        WP_Widget::get_field_id
	 * @see        WP_Widget::get_field_name
	 * @see        esc_attr
	 * @see        esc_html_e
	 * @see        esc_url
	 * @see        checked
	 * @see        wp_parse_args
	 *
	 * @version    1.6
	 * @date       September 7, 2013
	 * Changed `show_plugins` default to true (more common usage than false)
	 *
	 * @version    1.9
	 * @date       December 7, 2014
	 * Implemented the `BNS_SUPPORT_HOME` constant
	 *
	 * @version    2.3
	 * @date       2019-04-03
	 * Removed credits section
	 */
	public function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
			'title'        => get_bloginfo( 'name' ),
			'blog_admin'   => true,
			'show_plugins' => true,
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'bns-support' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['blog_admin'], true ); ?> id="<?php echo esc_attr( $this->get_field_id( 'blog_admin' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'blog_admin' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'blog_admin' ) ); ?>"><?php esc_html_e( 'Only show to administrators?', 'bns-support' ); ?></label>
		</p>

		<hr />

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_plugins'], true ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_plugins' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_plugins' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_plugins' ) ); ?>"><?php esc_html_e( 'Show active plugins?', 'bns-support' ); ?></label>
		</p>

		<?php

	}


	/**
	 * Load Widget
	 *
	 * @package BNS_Support
	 * @since   0.1
	 *
	 * @see     register_widget
	 */
	public function BNS_Support_load_widget() {
		register_widget( 'BNS_Support_Widget' );
	}


	/**
	 * BNS Support Shortcode
	 *
	 * @param array $atts use defined attributes in shortcode tag.
	 *
	 * @return  string
	 *
	 * @package    BNS_Support
	 * @see        get_bloginfo
	 * @see        shortcode_atts
	 * @see        the_widget
	 *
	 * @since      1.6
	 *
	 * @version    1.6.1
	 * @date       September 7, 2013
	 * Added shortcode name parameter for core filter auto-creation
	 *
	 * @version    1.8
	 * @date       April 20, 2014
	 * Added CSS class wrapper for shortcode output
	 *
	 * @version    2.3
	 * @date       2019-04-03
	 * Corrected `$args` array to correctly set values to null.
	 */
	public function bns_support_shortcode( $atts ) {

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
				),
				$atts,
				'tech_support'
			),
			/** Clear variables defined by theme for widgets */
			$args =
				array(
					$before_widget => '',
					$after_widget  => '',
					$before_title  => '',
					$after_title   => '',
				)
		);

		/** Get the_widget output and put it into its own variable */
		$bns_support_content = ob_get_clean();

		/** Wrap `the_widget` output */
		$bns_support_content = '<div class="bns-support-shortcode">' . $bns_support_content . '</div><!-- bns-support-shortcode -->';

		/** Return the widget output for the shortcode to use */

		return $bns_support_content;

	}


	/**
	 * Plugin Data
	 *
	 * Returns the plugin header data as an array
	 *
	 * @return    array
	 * @since      1.8
	 *
	 * @see        get_plugin_data
	 *
	 * @package    BNS_Support
	 */
	public function plugin_data() {

		/** Call the wp-admin plugin code */
		require_once ABSPATH . '/wp-admin/includes/plugin.php';

		/** Holds the plugin header data */
		$plugin_data = get_plugin_data( __FILE__ );

		return $plugin_data;

	}


	/**
	 * BNS Support Plugin Meta
	 *
	 * Adds additional links to plugin meta links
	 *
	 * @param array  $links existing links used for the plugin meta details.
	 * @param string $file  used for the plugin reference.
	 *
	 * @return  array $links
	 * @see        plugin_basename
	 *
	 * @package    BNS_SUpport
	 * @since      1.8
	 *
	 * @see        __
	 */
	public function bns_support_plugin_meta( $links, $file ) {

		$plugin_file = plugin_basename( __FILE__ );

		if ( $file === $plugin_file ) {

			$links = array_merge(
				$links,
				array(
					'fork_link'    => '<a href="https://github.com/Cais/BNS-Support">' . __( 'Fork on GitHub', 'bns-support' ) . '</a>',
					'wish_link'    => '<a href="http://www.amazon.ca/registry/wishlist/2NNNE1PAQIRUL">' . __( 'Grant a wish?', 'bns-support' ) . '</a>',
					'support_link' => '<a href="https://wordpress.org/support/plugin/bns-support">' . __( 'WordPress Support Forums', 'bns-support' ) . '</a>',
				)
			);

		}

		return $links;

	}


	/**
	 * Update Message
	 *
	 * @param array $args plugin details.
	 *
	 * @since   2.0
	 *
	 * @see     BNS_Support_Widget::plugin_data
	 * @see     get_transient
	 * @see     is_wp_error
	 * @see     set_transient
	 * @see     wp_kses_post
	 * @see     wp_remote_get
	 *
	 * @package BNS_Support
	 */
	public function update_message( $args ) {

		/** Holds the plugin header data */
		$bns_support_data = $this->plugin_data();

		$transient_name = 'bns_support_upgrade_notice_' . $args['Version'];
		$upgrade_notice = get_transient( $transient_name );

		if ( false === $upgrade_notice ) {

			/** Get the readme.txt file from WordPress */
			$response = wp_remote_get( 'https://plugins.svn.wordpress.org/bns-support/trunk/readme.txt' );

			if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
				$matches = null;
			}

			$regexp = '~==\s*Changelog\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( $bns_support_data['Version'], null ) . '\s*=|$)~Uis';

			$upgrade_notice = '';

			if ( preg_match( $regexp, $response['body'], $matches ) ) {
				$version = trim( $matches[1] );
				$notices = (array) preg_split( '~[\r\n]+~', trim( $matches[2] ) );

				if ( version_compare( $bns_support_data['Version'], $version, '<' ) ) {

					/** Start building message (inline styles) */
					$upgrade_notice = '<style type="text/css">
							.bns_support_plugin_upgrade_notice { padding-top: 20px; }
							.bns_support_plugin_upgrade_notice ul { width: 50%; list-style: disc; margin-left: 20px; margin-top: 0; }
							.bns_support_plugin_upgrade_notice li { margin: 0; }
						</style>';

					/** Start building message (begin block) */
					$upgrade_notice .= '<div class="bns_support_plugin_upgrade_notice">';

					$ul = false;

					foreach ( $notices as $index => $line ) {

						if ( preg_match( '~^=\s*(.*)\s*=$~i', $line ) ) {

							if ( $ul ) {
								$upgrade_notice .= '</ul><div style="clear: left;"></div>';
							}

							$upgrade_notice .= '<hr/>';
							continue;

						}

						/** Body of message */
						$return_value = '';

						if ( preg_match( '~^\s*\*\s*~', $line ) ) {

							if ( ! $ul ) {
								$return_value = '<ul">';
								$ul           = true;
							}

							$line = preg_replace( '~^\s*\*\s*~', '', htmlspecialchars( $line ) );

							$return_value .= '<li style=" ' . ( 0 === $index % 2 ? 'clear: left;' : '' ) . '">' . $line . '</li>';

						} else {

							if ( $ul ) {

								$return_value = '</ul><div style="clear: left;"></div><p>' . $line . '</p>';
								$ul           = false;

							} else {

								$return_value .= '<p>' . $line . '</p>';

							}
						}

						$upgrade_notice .= wp_kses_post( preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $return_value ) );

					}

					$upgrade_notice .= '</div>';

				}
			}

			/** Set transient - minimize calls to WordPress */
			set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );

		}

		echo $upgrade_notice;

	}

}

/** Instantiate the class */
$bns_support = new BNS_Support_Widget();
$bns_support->init();
