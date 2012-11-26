<?php
/*
Plugin Name: BNS Support
Plugin URI: http://buynowshop.com/plugins/bns-support/
Description: Simple display of useful support information in the sidebar. Easy to copy and paste details, such as: the blog name; WordPress version; name of installed theme; and, active plugins list. Help for those that help. The information is only viewable by logged-in readers; and, by optional default, the blog administrator(s) only.
Version: 1.3
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
 * @version     1.3
 * @author      Edward Caissie <edward.caissie@gmail.com>
 * @copyright   Copyright (c) 2009-2012, Edward Caissie
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
 * @version 1.3
 * @date    November 26, 2012
 * Add filter hooks and CSS classes to output strings
 * Remove load_plugin_textdomain as redundant
 */

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
}

/** ------------------------------------------------------------------------- */
/**
 * WP List All Active Plugins
 * @link http://wordpress.org/extend/plugins/wp-plugin-lister/
 * @author Paul G Petty
 * @link http://paulgriffinpetty.com
 *
 * Some of the functionality of Paul G Getty's Plugin Lister code has been used
 * to replace the old code by Lester Chan
 *
 * @package BNS_Support
 * @since 1.9.1
 * Completely merge, strip out excess, and re-write (as needed) 'Plugin Lister'
 */
function wp_list_all_active_plugins() {
    if ( ! function_exists( 'get_plugin_data' ) ) {
        function get_plugin_data( $plugin_file ) {
            // We don't need to write to the file, so just open for reading.
            $fp = fopen( $plugin_file, 'r' );

            // Pull only the first 8kB of the file in.
            $plugin_data = fread( $fp, 8192 );

            // PHP will close file handle, but we are good citizens.
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
                if ( ! empty( ${$field} ) )
                    ${$field} = trim(${$field}[1]);
                else
                    ${$field} = '';
            }

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
        }
    }

    $p = get_option( 'active_plugins' );

    $plugin_list = '';
    $plugin_list .= '<ul>';
        foreach ( $p as $q ) {
            $d = get_plugin_data( WP_PLUGIN_DIR . '/' . $q );
            $plugin_list .= '<li>';
                $plugin_list .= __( '<strong><a href="' . $d['PluginURI'] . '">' . $d['Title'] . ' ' . $d['Version'] . '</a></strong>', 'bns-support' ) . '<br />';
                // if ($d['AuthorURI'] != "") {
                /** Serious hack ... the following line tests if the above preg_match failed and wrote the conditional check instead of the AuthorURI to the variable
                 * @todo sort out where the root issue is for this; original coded commented out above does not work as expected
                 */
                if ( substr( $d['AuthorURI'], 0, 8 ) !== '(.*)$|mi' ) {
                    $plugin_list .= sprintf( __( 'by %1$s (<a href="' . $d['AuthorURI'] . '">url</a>)', 'bns-support' ), $d['Author'] ) . '<br />';
                } else {
                    $plugin_list .= sprintf( __( 'by %1$s', 'bns-support' ), $d['Author'] ) . '<br />';
                }
            $plugin_list .= '</li>';
        }
    $plugin_list .= '</ul>';
    echo $plugin_list;
}
/** ------------------------------------------------------------------------- */

/**
 * Enqueue Plugin Scripts and Styles
 *
 * Adds plugin stylesheet and allows for custom stylesheet to be added by end-user.
 *
 * @package BNS_Support
 * @since   1.0
 *
 * @uses    plugin_dir_path
 * @uses    plugin_dir_url
 * @uses    wp_enqueue_style
 *
 * @version 1.2
 * @date    August 2, 2012
 * Programmatically add version number to enqueue calls
 */
function BNS_Support_Scripts_and_Styles() {
    /** Call the wp-admin plugin code */
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
    /** @var $bns_support_data - holds the plugin header data */
    $bns_support_data = get_plugin_data( __FILE__ );

    /* Enqueue Scripts */
    /* Enqueue Styles */
    wp_enqueue_style( 'BNS-Support-Style', plugin_dir_url( __FILE__ ) . 'bns-support-style.css', array(), $bns_support_data['Version'], 'screen' );
    if ( is_readable( plugin_dir_path( __FILE__ ) . 'bns-support-custom-style.css' ) ) { // Only enqueue if available
        wp_enqueue_style( 'BNS-Support-Custom-Style', plugin_dir_url( __FILE__ ) . 'bns-support-custom-style.css', array(), $bns_support_data['Version'], 'screen' );
    }
}
add_action( 'wp_enqueue_scripts', 'BNS_Support_Scripts_and_Styles' );
// End: Enqueue Plugin Scripts and Styles

/** Register widget */
function load_BNS_Support_Widget() {
	register_widget( 'BNS_Support_Widget' );
}
add_action( 'widgets_init', 'load_BNS_Support_Widget' );

class BNS_Support_Widget extends WP_Widget {
    function BNS_Support_Widget() {
        /** Widget settings */
        $widget_ops = array( 'classname' => 'bns-support', 'description' => __( 'Widget to display and share common helpful support details.', 'bns-support' ) );
        /** Widget control settings */
        $control_ops = array( 'width' => 200, 'id_base' => 'bns-support' );
        /** Create the widget */
        $this->WP_Widget( 'bns-support', 'BNS Support', $widget_ops, $control_ops );
    }

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
                if ( $title )
                    /**
                     * @var $before_title   string - defined by theme
                     * @var $after_title    string - defined by theme
                     */
                    echo $before_title . $title . $after_title;

                /** Start displaying BNS Support information */
                echo '<ul>';

                /** Blog URL */
                echo apply_filters( 'bns_support_url', '<li class="bns-support-url"><strong>URL</strong>: ' . get_bloginfo( 'url' ) . '</li>' );

                /** Versions for various major factors */
                global $wp_version;
                echo apply_filters( 'bns_support_wp_version', '<li class="bns-support-wp-version"><strong>' . __( 'WordPress Version:', 'bns-support' ) . '</strong>' . ' ' . $wp_version . '</li>' );
                echo apply_filters( 'bns_support_php_version', '<li class="bns-support-php-version"><strong>' . __( 'PHP version:', 'bns-support' ) . '</strong>' . ' ' . phpversion() . '</li>' );
                /** @noinspection PhpParamsInspection - MySQLi link not required to get client version */
                echo apply_filters( 'bns_support_mysql_version', '<li class="bns-support-mysql-version"><strong>' . __( 'MySQL version:', 'bns-support' ) . '</strong> ' . ' ' . mysqli_get_client_info() . '</li>' );
                echo apply_filters( 'bns_support_ms_enabled', '<li class="bns-support-ms-enabled"><strong>' . __( 'Multisite Enabled:', 'bns-support' ) . '</strong> ' . ' ' . ( ( function_exists( 'is_multisite' ) && is_multisite() ) ? __( 'True', 'bns-support' ) : __( 'False', 'bns-support' ) ) . '</li>' );

                /** Theme Display with Parent/Child-Theme recognition */
                /** @var $active_theme_data - array object containing the current theme's data */
                $active_theme_data = wp_get_theme();
                if ( is_child_theme() ) {
                    /** @var $parent_theme_data - array object containing the Parent Theme's data */
                    $parent_theme_data = $active_theme_data->parent();
                    /** @noinspection PhpUndefinedMethodInspection - IDE commentary */
                    $output = sprintf( __( '<li class="bns-support-child-theme"><strong>Theme:</strong> %1$s v%2$s a Child-Theme of %3$s v%4$s</li>', 'bns-support' ),
                        $active_theme_data->get( 'Name' ),
                        $active_theme_data->get( 'Version' ),
                        $parent_theme_data->get( 'Name' ),
                        $parent_theme_data->get( 'Version' ) );
                    echo apply_filters( 'bns_support_Child_theme', $output );
                } else {
                    $output = sprintf( __( '<li class="bns-support-parent-theme"><strong>Theme:</strong> %1$s v%2$s</li>', 'bns-support' ),
                        $active_theme_data->get( 'Name' ),
                        $active_theme_data->get( 'Version' ) );
                    echo apply_filters( 'bns_support_parent_theme', $output );
                }

                if ( function_exists( 'is_multisite' ) && is_multisite() ) {
                    $current_site = get_current_site();
                    /** @noinspection PhpUndefinedFieldInspection */
                    $home_domain = 'http://' . $current_site->domain . $current_site->path;
                    if ( current_user_can( 'manage_options' ) ) {
                        /** If multisite is "true" then direct ALL users to main site administrator */
                        echo apply_filters( 'bns_support_ms_user', '<li class="bns-support-ms-user">' . sprintf( __( 'Please review with your main site administrator at %1$s for additional assistance.', 'bns-support' ), '<a href="' . $home_domain . '">' . $current_site->site_name . '</a>' ) . '</li>' );
                    } else {
                        echo apply_filters( 'bns_support_ms_admin', '<li class="bns-support-ms-admin">' . __( 'You are the Admin!', 'bns-support') . '</li>' );
                    }
                } else {
                    /* ---- Current User Level ---- */
                    $user_roles = $current_user->roles;
                    /**
                     * @todo Re-write to show all roles of current user
                     */
                    $user_role = array_shift($user_roles);
                    echo apply_filters( 'bns_support_current_user', '<li class="bns-support-current-user">' . sprintf( __( '<strong>Current User Role</strong>: %1$s ', 'bns-support' ), $user_role ) . '</li>' );

                    if ( $show_plugins ) {
                        echo apply_filters( 'bns_support_active_plugins', '<li class="bns-support-active-plugins"><strong>' . __( 'Active Plugins:', 'bns-support') . '</strong></li>' );

                        wp_list_all_active_plugins();
                    }

                }
                echo '</ul>';
                // End - Display BNS Support information

                /** Gratuitous self-promotion */
                if ( $credits ) {
                    echo apply_filters( 'bns_support_credits', '<h6 class="bns-support-credits">' . sprintf( __( 'Compliments of %1$s at %2$s', 'bns-support' ), '<a href="http://buynowshop.com/wordpress-services" target="_blank">WordPress Services</a>', '<a href="http://buynowshop.com" target="_blank">BuyNowShop.com</a>' ) . '</h6>' );
                }

                /** @var $after_widget string - defined by theme */
                echo $after_widget;

                echo '</div> <!-- .bns-support -->'; /* end CSS wrapper */
            } /* Admin logged in */
        } /* Logged in */
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        /* Strip tags (if needed) and update the widget settings. */
        $instance['title']        = strip_tags( $new_instance['title'] );
        $instance['blog_admin']   = $new_instance['blog_admin'];
        $instance['show_plugins'] = $new_instance['show_plugins'];
        $instance['credits']      = $new_instance['credits'];
        return $instance;
    }

    function form( $instance ) {
        /* Set up some default widget settings. */
        $defaults = array(
            'title'         => get_bloginfo('name'),
            'blog_admin'    => true,
            'show_plugins'  => false,
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

        <?php }
}