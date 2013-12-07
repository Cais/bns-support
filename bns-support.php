<?php
/*
Plugin Name: BNS Support
Plugin URI: http://buynowshop.com/plugins/bns-support/
Description: Simple display of useful support information in the sidebar. Easy to copy and paste details, such as: the blog name; WordPress version; name of installed theme; and, active plugins list. Help for those that help. The information is only viewable by logged-in readers; and, by optional default, the blog administrator(s) only.
Version: 1.6.1
Text Domain: bns-support
Author: Edward Caissie
Author URI: http://edwardcaissie.com/
License: GNU General Public License v2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/**
 * BNS Support
 * This plugin will allow you to style sections of post content with added
 * emphasis by leveraging a style element from the active theme.
 *
 * @package     BNS_Support
 * @link        http://buynowshop.com/plugins/bns-support/
 * @link        https://github.com/Cais/bns-support/
 * @link        http://wordpress.org/extend/plugins/bns-support/
 * @version     1.6.1
 * @author      Edward Caissie <edward.caissie@gmail.com>
 * @copyright   Copyright (c) 2009-2013, Edward Caissie
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
 *
 * @version 1.5
 * @date    April 14, 2013
 * Added 'mod_rewrite' display check
 *
 * @version 1.5.1
 * @date    May 28, 2013
 * Added conditional check for 'apache_get_modules'
 *
 * @version 1.6
 * @date    August 25, 2013
 * Added shortcode functionality
 *
 * @version 1.6.1
 * @date    December 2013
 * Added shortcode name parameter for core filter auto-creation
 * Added new method `MySQL Version Details` and corrected the reported data
 * Minor rearrangement of layout for better readability
 *
 * @todo Improve code structures to better allow more details/sub-details to be added
 */

class BNS_Support_Widget extends WP_Widget {
    /**
     * Constructor / BNS Support Widget
     *
     * @package BNS_Support
     * @since   0.1
     *
     * @uses    WP_Widget (class)
     * @uses    add_action
     */
    function BNS_Support_Widget() {
        /** Widget settings */
        $widget_ops = array( 'classname' => 'bns-support', 'description' => __( 'Widget to display and share common helpful support details.', 'bns-support' ) );
        /** Widget control settings */
        $control_ops = array( 'width' => 200, 'id_base' => 'bns-support' );
        /** Create the widget */
        $this->WP_Widget( 'bns-support', 'BNS Support', $widget_ops, $control_ops );

        /**
         * Check installed WordPress version for compatibility
         *
         * @package     BNS_Support
         * @since       0.1
         *
         * @internal    Version 3.4 - see `wp_get_theme`
         *
         * @version     1.2
         * @date        July 13, 2012
         */
        global $wp_version;
        $exit_message = __( 'BNS Support requires WordPress version 3.0 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please Update!</a>', 'bns-support' );
        if ( version_compare( $wp_version, "3.4", "<" ) ) {
            exit ( $exit_message );
        } /** End if - version compare */

        /** Add scripts and styles */
        add_action( 'wp_enqueue_scripts', array( $this, 'BNS_Support_scripts_and_styles' ) );

        /** Add custom headers */
        add_filter( 'extra_theme_headers', array( $this, 'BNS_Support_extra_theme_headers' ) );

        /** Add shortcode */
        add_shortcode( 'tech_support', array( $this, 'bns_support_shortcode' ) );

        /** Add widget */
        add_action( 'widgets_init', array( $this, 'BNS_Support_load_widget' ) );

    } /** End function - constructor */


    /**
     * BNS Support Extra Theme Headers
     * Add the 'WordPress Tested Version', 'WordPress Required Version' and
     * 'Template Version' custom theme header details for reference
     *
     * @package BNS_Support
     * @since   1.4
     *
     * @param   $headers
     *
     * @return  array
     *
     * @internal see WordPress core trac ticket #16868
     * @link https://core.trac.wordpress.org/ticket/16868
     */
    function BNS_Support_extra_theme_headers( $headers ) {

        if ( ! in_array( 'WordPress Tested Version', $headers ) ) {
            $headers[] = 'WordPress Tested Version';
        } /** End if - not in array */

        if ( ! in_array( 'WordPress Required Version', $headers ) ) {
            $headers[] = 'WordPress Required Version';
        } /** End if - not in array */

        if ( ! in_array( 'Template Version', $headers ) ) {
            $headers[] = 'Template Version';
        } /** End if - not in array */

        return $headers;

    } /** End function - extra theme headers */


    /**
     * BNS Support Enqueue Plugin Scripts and Styles
     * Adds plugin stylesheet and allows for custom stylesheet to be added by end-user.
     *
     * @package BNS_Support
     * @since   1.0
     *
     * @uses    WP_CONTENT_DIR
     * @uses    content_url
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
     */
    function BNS_Support_scripts_and_styles() {
        /** Call the wp-admin plugin code */
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        /** @var $bns_support_data - holds the plugin header data */
        $bns_support_data = get_plugin_data( __FILE__ );

        /* Enqueue Scripts */
        /* Enqueue Styles */
        wp_enqueue_style( 'BNS-Support-Style', plugin_dir_url( __FILE__ ) . 'bns-support-style.css', array(), $bns_support_data['Version'], 'screen' );

        /**
         * Add custom styles
         * NB: This location will be killed when plugin is updated due to core
         * WordPress functionality - place the custom stylesheet directly in
         * the /wp-content/ folder for future proofing your custom styles.
         */
        if ( is_readable( plugin_dir_path( __FILE__ ) . 'bns-support-custom-style.css' ) ) { // Only enqueue if available
            wp_enqueue_style( 'BNS-Support-Custom-Style', plugin_dir_url( __FILE__ ) . 'bns-support-custom-style.css', array(), $bns_support_data['Version'], 'screen' );
        } /** End if - is readable */

        /** For placing the custom stylesheet in the /wp-content/ folder */
        /** @todo Find alternative to using WP_CONTENT_DIR constant? */
        if ( is_readable( WP_CONTENT_DIR . '/bns-support-custom-style.css' ) ) {
            wp_enqueue_style( 'BNS-Support-Custom-Style', content_url() . '/bns-support-custom-style.css', array(), $bns_support_data['Version'], 'screen' );
        } /** End if - is readable */


    } /** End function - scripts and styles */


    /**
     * Theme Version Check
     * Using custom headers from the theme if they exist, check what version of
     * WordPress the theme has been tested up to and what version of WordPress
     * the theme requires. Also note, if the detail exists, the Parent-Theme
     * (Template) version the Child-Theme references.
     *
     * @internal see core trac ticket #16868
     * @link https://core.trac.wordpress.org/ticket/16868
     *
     * @package BNS_Support
     * @since   1.4
     *
     * @uses    apply_filters
     *
     * @param   $wp_tested
     * @param   $wp_required
     * @param   $wp_template
     *
     * @return  string
     */
    function theme_version_check( $wp_tested, $wp_required, $wp_template ) {
        /** @var $output - initialize as empty string */
        $output = '';

        if ( ( ! empty( $wp_tested ) ) || ( ! empty( $wp_required ) ) || ( ! empty( $wp_template ) ) ) {

            $output .= '<ul>';

            if ( ! empty( $wp_tested ) ) {
                $output .= '<li class="bns-support-theme-tested">'
                    . sprintf( '<strong>%1$s</strong>: %2$s',
                        apply_filters( 'bns_support_theme_tested', __( 'Tested To', 'bns-support' ) ),
                        $wp_tested )
                    . '</li>';
            } /** End if - not empty tested */

            if ( ! empty( $wp_required ) ) {
                $output .= '<li class="bns-support-theme-required">'
                    . sprintf( '<strong>%1$s</strong>: %2$s',
                        apply_filters( 'bns_support_theme_required', __( 'Required', 'bns-support') ),
                        $wp_required )
                    . '</li>';
            } /** End if - not empty required */

            if ( ! empty( $wp_template ) && is_child_theme() ) {
                $output .= '<li class="bns-support-template">'
                    . sprintf( '<strong>%1$s</strong>: %2$s',
                        apply_filters( 'bns_support_template', __( 'Parent Version', 'bns-support' ) ),
                        $wp_template )
                    . '</li>';
            } /** End if - not empty tested */

            $output .= '</ul>';

        } /** End if - not empty */

        return $output;

    } /** End function - theme version check */


    /**
     * Mod Rewrite Check
     *
     * @package BNS_Support
     * @since   1.5
     *
     * @return  string - Enabled|Disabled
     */
    function mod_rewrite_check() {
        if ( in_array( 'mod_rewrite', apache_get_modules() ) ) {
            return 'Enabled';
        } else {
            return 'Disabled';
        } /** End if - in array */

    } /** End function - mod rewrite check */


    /**
     * MySQL Version Details
     * Returns a human readable version of the MySQL server version
     *
     * @package BNS_Support
     * @since   1.6.1
     *
     * @uses    apply_filters
     */
    function mysql_version_details() {
        /** MySQL Version */
        /** @var $mysql_version_number - pull MySQL server version details */
        $mysql_version_number = mysqli_get_server_version( mysqli_connect() );
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
        . apply_filters( 'bns_support_mysql_version',
            sprintf( __( '<strong>MySQL version:</strong> %1$s', 'bns-support' ),
                $mysql_version_output
            )
        )
        . '</li>';
    } /** End function - mysql version details */


    /**
     * WP List All Active Plugins
     * @link    http://wordpress.org/extend/plugins/wp-plugin-lister/
     * @author  Paul G Petty
     * @link    http://paulgriffinpetty.com
     *
     * Some of the functionality of Paul G Getty's Plugin Lister code has been
     * used to replace the old code by Lester Chan
     *
     * @package BNS_Support
     * @since   1.1
     * Completely merged, stripped out excess, and rewritten 'Plugin Lister'
     *
     * @version 1.4
     * @date    February 14, 2013
     * Sorted out AuthorURI conditional test
     */
    function wp_list_all_active_plugins() {
        if ( ! function_exists( 'get_plugin_data' ) ) {
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

                foreach ( array( 'name', 'uri', 'version', 'description', 'author_name', 'author_uri', 'text_domain', 'domain_path' ) as $field ) {
                    if ( ! empty( ${$field} ) ) {
                        ${$field} = trim(${$field}[1]);
                    } else {
                        ${$field} = '';
                    } /** End if - not empty */
                } /** End foreach - array */

                $plugin_data = array(
                    'Name'          => $name,
                    'Title'         => $name,
                    'PluginURI'     => $uri,
                    'Description'   => $description,
                    'Author'        => $author_name,
                    'AuthorURI'     => $author_uri,
                    'Version'       => $version,
                    'TextDomain'    => $text_domain,
                    'DomainPath'    => $domain_path
                );

                return $plugin_data;

            } /** End function - get plugin data */

        } /** End if - not function exists */

        $p = get_option( 'active_plugins' );

        $plugin_list = '';
        $plugin_list .= '<ul>';

        foreach ( $p as $q ) {
            $d = get_plugin_data( WP_PLUGIN_DIR . '/' . $q );
            $plugin_list .= '<li>';
            $plugin_list .= __( '<strong><a href="' . $d['PluginURI'] . '">' . $d['Title'] . ' ' . $d['Version'] . '</a></strong>', 'bns-support' ) . '<br />';

            if ( ! empty( $d['AuthorURI'] ) ) {
                $plugin_list .= sprintf( __( 'by %1$s (<a href="' . $d['AuthorURI'] . '">url</a>)', 'bns-support' ), $d['Author'] ) . '<br />';
            } else {
                $plugin_list .= sprintf( __( 'by %1$s', 'bns-support' ), $d['Author'] ) . '<br />';
            } /** End if - not empty */

            $plugin_list .= '</li>';

        } /** End foreach - p as q */

        $plugin_list .= '</ul>';

        echo $plugin_list;

    } /** End function - list all active plugins */


    /**
     * Widget
     *
     * @package BNS_Support
     * @since   0.1
     *
     * @uses    BNS_Support::wp_list_all_active_plugins
     * @uses    apply_filters
     * @uses    current_user_can
     * @uses    get_current_site
     * @uses    is_child_theme
     * @uses    is_multisite
     * @uses    wp_get_theme
     *
     * @param   array $args
     * @param   array $instance
     *
     * @version 1.4.1
     * @date    February 27, 2013
     * Change the widget output to a better grouping of details
     *
     * @version 1.5
     * @date    April 14, 2013
     * Refactored 'MultiSite Enabled', 'PHP Version', and 'MySQL Version' to be
     * better filtered
     *
     * @version 1.6.1
     * @date    December 7, 2013
     * Add `WP_DEBUG` status reference
     * Extracted `MySQL Version Details` into its own method
     */
    function widget( $args, $instance) {
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
                echo '<div class="bns-support">'; /* CSS wrapper */

                /** @var    $before_widget  string - defined by theme */
                echo $before_widget;
                /** Widget $title, $before_widget, and $after_widget defined by theme */
                if ( $title ) {
                    /**
                     * @var $before_title   string - defined by theme
                     * @var $after_title    string - defined by theme
                     */
                    echo $before_title . $title . $after_title;
                } /** End if - title */

                /** Start displaying BNS Support information */
                echo '<ul>';

                /** Blog URL */
                echo apply_filters( 'bns_support_url',
                    '<li class="bns-support-url"><strong>URL</strong>: ' . get_bloginfo( 'url' ) . '</li>'
                );

                /** Versions for various major factors */
                global $wp_version;

                echo '<li class="bns-support-wp-version"><!-- WordPress Details start -->';

                echo apply_filters( 'bns_support_wp_version',
                     '<strong>' .__( 'WordPress Version:', 'bns-support' ) . '</strong>' . ' ' . $wp_version
                );

                /** WP_DEBUG Status */
                echo '<ul><li class="bns-support-wp-debug-status">'
                        . apply_filters( 'bns_support_wp_debug_status',
                            sprintf( __( '<strong>WP_DEBUG Status:</strong> %1$s', 'bns-support' ),
                                WP_DEBUG
                                        ? __( 'True', 'bns-support' )
                                        : __( 'False', 'bns-support' )
                            )
                        )
                        . '</li></ul><!-- bns-support-wp-debug-status -->';

                /** MultiSite Enabled */
                echo '<ul><li class="bns-support-ms-enabled">'
                        . apply_filters( 'bns_support_ms_enabled',
                            sprintf( __( '<strong>Multisite Enabled:</strong> %1$s', 'bns-support' ),
                                function_exists( 'is_multisite' ) && is_multisite()
                                        ? __( 'True', 'bns-support' )
                                        : __( 'False', 'bns-support' )
                            )
                        )
                        . '</li><!-- bns-support-ms-enabled --></ul>';

                echo '</li><!-- WordPress Details End -->';

                /** @var $active_theme_data - array object containing the current theme's data */
                $active_theme_data = wp_get_theme();
                $wp_tested = $active_theme_data->get( 'WordPress Tested Version' );
                $wp_required = $active_theme_data->get( 'WordPress Required Version' );
                $wp_template = $active_theme_data->get( 'Template Version' );

                /** Theme Display with Parent/Child-Theme recognition */
                if ( is_child_theme() ) {
                    /** @var $parent_theme_data - array object containing the Parent Theme's data */
                    $parent_theme_data = $active_theme_data->parent();
                    /** @noinspection PhpUndefinedMethodInspection - IDE commentary */
                    $output = sprintf( __( '<li class="bns-support-child-theme"><strong>Theme:</strong> %1$s v%2$s a Child-Theme of %3$s v%4$s%5$s</li>', 'bns-support' ),
                        $active_theme_data->get( 'Name' ),
                        $active_theme_data->get( 'Version' ),
                        $parent_theme_data->get( 'Name' ),
                        $parent_theme_data->get( 'Version' ),
                        $this->theme_version_check( $wp_tested, $wp_required, $wp_template )
                    );
                    echo apply_filters( 'bns_support_Child_theme',
                        $output
                    );
                } else {
                    $output = sprintf( __( '<li class="bns-support-parent-theme"><strong>Theme:</strong> %1$s v%2$s%3$s</li>', 'bns-support' ),
                        $active_theme_data->get( 'Name' ),
                        $active_theme_data->get( 'Version' ),
                        $this->theme_version_check( $wp_tested, $wp_required, $wp_template )
                    );
                    echo apply_filters( 'bns_support_parent_theme',
                        $output
                    );
                } /** End if - is child theme */

                /** PHP Version */
                echo '<li class="bns-support-php-version"><!-- PHP Details Start -->';

                echo apply_filters( 'bns_support_php_version',
                    sprintf( __( '<strong>PHP version:</strong> %1$s', 'bns-support' ),
                        phpversion()
                    )
                );

                /**
                 * Mod Rewrite Support
                 * @todo Find a method that works with minimum WordPress PHP required version
                 */
                if ( function_exists( 'apache_get_modules' ) ) {
                    echo '<ul><li class="bns-support-mod-rewrite">'
                            . apply_filters( 'bns_support_mod_rewrite',
                                sprintf( __( '<strong>Mod Rewrite:</strong> %1$s', 'bns-support' ),
                                    $this->mod_rewrite_check()
                                )
                            )
                            . '</li></ul>';
                }

                echo '</li><!-- PHP Details End -->';

                echo $this->mysql_version_details();

                /** Multisite Check */
                if ( is_multisite() ) {

                    $current_site = get_current_site();
                    /** @noinspection PhpUndefinedFieldInspection */
                    $home_domain = 'http://' . $current_site->domain . $current_site->path;
                    if ( current_user_can( 'manage_options' ) ) {
                        /** If multisite is "true" then direct ALL users to main site administrator */
                        echo apply_filters( 'bns_support_ms_user',
                            '<li class="bns-support-ms-user">'
                                . sprintf( __( 'Please review with your main site administrator at %1$s for additional assistance.', 'bns-support' ), '<a href="' . $home_domain . '">' . $current_site->site_name . '</a>' )
                            . '</li>'
                        );
                    } else {
                        echo apply_filters( 'bns_support_ms_admin',
                            '<li class="bns-support-ms-admin">' . __( 'You are the Admin!', 'bns-support') . '</li>'
                        );
                    } /** End if - current user can */

                } else {

                    /** ---- Current User Level ---- */
                    $user_roles = $current_user->roles;
                    $user_role = array_shift($user_roles);
                    echo apply_filters( 'bns_support_current_user',
                        '<li class="bns-support-current-user">'
                            . sprintf( __( '<strong>Current User Role</strong>: %1$s ', 'bns-support' ), $user_role )
                        . '</li>'
                    );

                    if ( $show_plugins ) {
                        echo apply_filters( 'bns_support_active_plugins',
                            '<li class="bns-support-active-plugins"><strong>' . __( 'Active Plugins:', 'bns-support') . '</strong></li>'
                        );

                        $this->wp_list_all_active_plugins();
                    } /** End if - show plugins */

                } /** End if - is multisite */

                echo '</ul>';
                /** End - Display BNS Support information */

                /** Gratuitous self-promotion */
                if ( $credits ) {
                    echo apply_filters( 'bns_support_credits',
                        '<h6 class="bns-support-credits">'
                            . sprintf( __( 'Compliments of %1$s at %2$s', 'bns-support' ), '<a href="http://buynowshop.com/wordpress-services" target="_blank">WordPress Services</a>', '<a href="http://buynowshop.com" target="_blank">BuyNowShop.com</a>' )
                        . '</h6>'
                    );
                } /** End if - credits */

                /** @var $after_widget string - defined by theme */
                echo $after_widget;

                echo '</div> <!-- .bns-support -->';
                /** End CSS wrapper */

            } /** End if - admin logged in */

        } /** End if - user logged in */

    } /** End function - widget */


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

    } /** End function - update */


    /**
     * Form
     *
     * @package BNS_Support
     * @since   0.1
     *
     * @uses    get_field_id
     * @uses    get_field_name
     * @uses    wp_parse_args
     *
     * @param   array $instance
     *
     * @return  string|void
     *
     * @version 1.6
     * @date    September 7, 2013
     * Changed `show_plugins` default to true (more common usage than false)
     */
    function form( $instance ) {
        /* Set up some default widget settings. */
        $defaults = array(
            'title'         => get_bloginfo( 'name' ),
            'blog_admin'    => true,
            'show_plugins'  => true,
            'credits'       => false,
        );
        $instance = wp_parse_args( ( array ) $instance, $defaults ); ?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'bns-support' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['blog_admin'], true ); ?> id="<?php echo $this->get_field_id( 'blog_admin' ); ?>" name="<?php echo $this->get_field_name( 'blog_admin' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'blog_admin' ); ?>"><?php _e( 'Only show to administrators?', 'bns-support' ); ?></label>
        </p>

        <hr />

        <p>
            <input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_plugins'], true ); ?> id="<?php echo $this->get_field_id( 'show_plugins' ); ?>" name="<?php echo $this->get_field_name( 'show_plugins' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_plugins' ); ?>"><?php _e( 'Show active plugins?', 'bns-support' ); ?></label>
        </p>

        <hr />

        <p>
            <input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['credits'], true ); ?> id="<?php echo $this->get_field_id( 'credits' ); ?>" name="<?php echo $this->get_field_name( 'credits' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'credits' ); ?>"><?php _e( 'Show complimentary link to ', 'bns-support' ); ?></label><a href="http://buynowshop.com/">BuyNowShop.com</a>?
        </p>

    <?php
    } /** End function - form */


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
    } /** End function  - register widget */


    /**
     * BNS Support Shortcode
     *
     * @package BNS_Support
     * @since   1.6
     *
     * @param   $atts
     *
     * @uses    shortcode_atts
     * @uses    the_widget
     *
     * @return  string
     *
     * @version 1.6.1
     * @date    September 7, 2013
     * Added shortcode name parameter for core filter auto-creation
     */
    function bns_support_shortcode( $atts ) {
        /** Let's start by capturing the output */
        ob_start();

        /** Pull the widget together for use elsewhere */
        the_widget( 'BNS_Support_Widget',
            $instance = shortcode_atts( array(
                'title'         => get_bloginfo( 'name' ),
                'blog_admin'    => true,
                'show_plugins'  => true,
                'credits'       => false,
            ), $atts, 'tech_support' ),
            $args = array(
                /** clear variables defined by theme for widgets */
                $before_widget  = '',
                $after_widget   = '',
                $before_title   = '',
                $after_title    = '',
            )
        );

        /** Get the_widget output and put it into its own variable */
        $bns_support_content = ob_get_clean();

        /** Return the widget output for the shortcode to use */
        return $bns_support_content;

    } /** End function - bns support shortcode */


} /** End class */


/** @var $bns_support - instantiate the class */
$bns_support = new BNS_Support_Widget();