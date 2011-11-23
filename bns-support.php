<?php
/*
Plugin Name: BNS Support
Plugin URI: http://buynowshop.com/plugins/bns-support/
Description: Simple display of useful support information in the sidebar. Easy to copy and paste details, such as: the blog name; WordPress version; name of installed theme; and, active plugins list. Help for those that help. The information is only viewable by logged-in readers; and, by optional default, the blog administrator(s) only.
Version: 1.1
Text Domain: bns-support
Author: Edward Caissie
Author URI: http://edwardcaissie.com/
License: GNU General Public License v2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/**
 * BNS Support
 *
 * This plugin will allow you to style sections of post content with added
 * emphasis by leveraging a style element from the active theme.
 *
 * @package     BNS_Support
 * @link        http://buynowshop.com/plugins/bns-support/
 * @link        https://github.com/Cais/bns-support/
 * @link        http://wordpress.org/extend/plugins/bns-support/
 * @version     1.1
 * @author      Edward Caissie <edward.caissie@gmail.com>
 * @copyright   Copyright (c) 2009-2011, Edward Caissie
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
 * Last revised November 23, 2011
 */

/**
 * BNS Support TextDomain
 * Make plugin text available for translation (i18n)
 *
 * @package:    BNS_Support
 * @since:      0.6
 *
 * @internal    Note: Translation files are expected to be found in the plugin root folder / directory.
 */
load_plugin_textdomain( 'bns-support' );
// End: BNS Support TextDomain

/**
 * Check installed WordPress version for compatibility
 *
 * @package     BNS_Inline_Asides
 * @since       0.1
 * @internal    Version 2.8 being used in reference to ...
 *
 * @version     1.1
 * Last revised November 23, 2011
 * Re-write to be i18n compatible
 *
 * @todo Check version requirements
 */
global $wp_version;
$exit_message = __( 'BNS Support requires WordPress version 2.8 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please Update!</a>', 'bns-support' );
if ( version_compare( $wp_version, "2.8", "<" ) ) {
    exit ( $exit_message );
}

/**
 * @todo Find another way to display active plugins without throwing offset errors like Lester Chan's code does currently
 */
/* ---- Credit to Lester Chan's WP-PluginsUsed ---- */
/* ---- http://lesterchan.net/portfolio/programming/php/#wp-pluginsused ---- */
### Define: Show Plugin Version Number?
define('PLUGINSUSED_SHOW_VERSION', true);

### Variable: Plugins To Hide?
$pluginsused_hidden_plugins = array();

### Function: WordPress Get Plugin Data
function get_pluginsused_data( $plugin_file ) {
        $plugin_data = implode( '', file( $plugin_file ) );
        preg_match( "|Plugin Name:(.*)|i", $plugin_data, $plugin_name );
        preg_match( "|Plugin URI:(.*)|i", $plugin_data, $plugin_uri );
        preg_match( "|Description:(.*)|i", $plugin_data, $description );
        preg_match( "|Author:(.*)|i", $plugin_data, $author_name );
        preg_match( "|Author URI:(.*)|i", $plugin_data, $author_uri );
        if ( preg_match( "|Version:(.*)|i", $plugin_data, $version ) ) {
            $version = trim( $version[1] );
        } else {
            $version = '';
        }
        $plugin_name = trim( $plugin_name[1] );
        $plugin_uri = trim( $plugin_uri[1] );
        $description = wptexturize( trim( $description[1] ) );
        $author = trim( $author_name[1] );
        $author_uri = trim( $author_uri[1] );
        return array( 'Plugin_Name' => $plugin_name, 'Plugin_URI' => $plugin_uri, 'Description' => $description, 'Author' => $author, 'Author_URI' => $author_uri, 'Version' => $version );
}

### Function: WordPress Get Plugins
function get_pluginsused() {
        global $wp_plugins;
        if ( isset( $wp_plugins ) ) {
            return $wp_plugins;
        }
        $wp_plugins = array();
        $plugin_root = WP_PLUGIN_DIR;
        $plugins_dir = @ dir( $plugin_root );
        if ( $plugins_dir ) {
            while ( ( $file = $plugins_dir->read() ) !== false ) {
                if ( substr( $file, 0, 1 ) == '.' ) {
                    continue;
                }
                if ( is_dir( $plugin_root.'/'.$file ) ) {
                    $plugins_subdir = @ dir( $plugin_root.'/'.$file );
                    if ( $plugins_subdir ) {
                        while ( ( $subfile = $plugins_subdir->read() ) !== false ) {
                            if ( substr( $subfile, 0, 1 ) == '.' ) {
                                continue;
                            }
                            if ( substr( $subfile, -4 ) == '.php' ) {
                                $plugin_files[] = "$file/$subfile";
                            }
                        }
                    }
                } else {
                    if ( substr( $file, -4 ) == '.php' ) {
                        $plugin_files[] = $file;
                    }
                }
            }
        }
        if ( ! $plugins_dir || !$plugin_files ) {
            return $wp_plugins;
        }
        foreach ( $plugin_files as $plugin_file ) {
            if ( !is_readable( "$plugin_root/$plugin_file" ) ) {
                continue;
            }
            $plugin_data = get_pluginsused_data( "$plugin_root/$plugin_file" );
            if ( empty( $plugin_data['Plugin_Name'] ) ) {
                continue;
            }
            $wp_plugins[plugin_basename( $plugin_file )] = $plugin_data;
        }
        uasort( $wp_plugins, create_function( '$a, $b', 'return strnatcasecmp( $a["Plugin_Name"], $b["Plugin_Name"] );' ) );
        return $wp_plugins;
}

### Function: Process Plugins Used
function process_pluginsused() {
        global $plugins_used, $pluginsused_hidden_plugins;
        if ( empty( $plugins_used ) ) {
            $plugins_used = array();
            $active_plugins = get_option( 'active_plugins' );
            $plugins = get_pluginsused();
            $plugins_allowedtags = array( 'a' => array( 'href' => array(),'title' => array() ),'abbr' => array( 'title' => array() ),'acronym' => array( 'title' => array() ),'code' => array(),'em' => array(),'strong' => array() );
            foreach ( $plugins as $plugin_file => $plugin_data ) {
                if ( ! in_array( $plugin_data['Plugin_Name'], $pluginsused_hidden_plugins ) ) {
                    $plugin_data['Plugin_Name'] = wp_kses( $plugin_data['Plugin_Name'], $plugins_allowedtags );
                    $plugin_data['Plugin_URI'] = wp_kses( $plugin_data['Plugin_URI'], $plugins_allowedtags );
                    $plugin_data['Description'] = wp_kses( $plugin_data['Description'], $plugins_allowedtags );
                    $plugin_data['Author'] = wp_kses( $plugin_data['Author'], $plugins_allowedtags );
                    $plugin_data['Author_URI'] = wp_kses( $plugin_data['Author_URI'], $plugins_allowedtags );
                    if ( PLUGINSUSED_SHOW_VERSION ) {
                        $plugin_data['Version'] = wp_kses( $plugin_data['Version'], $plugins_allowedtags );
                    } else {
                        $plugin_data['Version'] = '';
                    }
                    if ( ! empty( $active_plugins ) && in_array( $plugin_file, $active_plugins ) ) {
                        $plugins_used['active'][] = $plugin_data;
                    } else {
                        $plugins_used['inactive'][] = $plugin_data;
                    }
                }
            }
        }
}

### Function: Display Plugins
function display_pluginsused( $type, $display = false ) {
        global $plugins_used;
        $temp = '';
        if ( empty( $plugins_used ) ) {
            process_pluginsused();
        }
        if ( $type == 'stats' ) {
            $total_active_pluginsused = sizeof( $plugins_used['active'] );
            $total_inactive_pluginsused = sizeof( $plugins_used['inactive'] );
            $total_pluginsused = ( $total_active_pluginsused+$total_inactive_pluginsused );
            $temp = sprintf( _n( 'There is <strong>%s</strong> plugin used:', 'There are <strong>%s</strong> plugins used:', $total_pluginsused ), number_format_i18n( $total_pluginsused ) ) . ' ' . sprintf( _n( '<strong>%s active plugin</strong>','<strong>%s active plugins</strong>', $total_active_pluginsused ), number_format_i18n($total_active_pluginsused)).' '.__('and', 'wp-pluginsused').' '.sprintf(_n('<strong>%s inactive plugin</strong>.', '<strong>%s inactive plugins</strong>.', $total_inactive_pluginsused ), number_format_i18n( $total_inactive_pluginsused ) );
        } else if ( $type == 'active' ) {
            if ( $plugins_used['active'] ) {
                foreach ( $plugins_used['active'] as $active_plugins ) {
                    $active_plugins['Plugin_Name'] = strip_tags( $active_plugins['Plugin_Name'] );
                    $active_plugins['Plugin_URI'] = strip_tags( $active_plugins['Plugin_URI'] );
                    $active_plugins['Description'] = strip_tags( $active_plugins['Description'] );
                    $active_plugins['Version'] = strip_tags( $active_plugins['Version'] );
                    $active_plugins['Author'] = strip_tags( $active_plugins['Author'] );
                    $active_plugins['Author_URI'] = strip_tags( $active_plugins['Author_URI'] );
                    $active_plugins['Version'] = strip_tags( $active_plugins['Version'] );
                    /* $temp .= '<p><img src="'.plugins_url('wp-pluginsused/images/plugin_active.gif').'" alt="'.$active_plugins['Plugin_Name'].' '.$active_plugins['Version'].'" title="'.$active_plugins['Plugin_Name'].' '.$active_plugins['Version'].'" style="vertical-align: middle;" />&nbsp;&nbsp;<strong><a href="'.$active_plugins['Plugin_URI'].'" title="'.$active_plugins['Plugin_Name'].' '.$active_plugins['Version'].'">'.$active_plugins['Plugin_Name'].' '.$active_plugins['Version'].'</a></strong><br /><strong>&raquo; '.$active_plugins['Author'].' (<a href="'.$active_plugins['Author_URI'].'" title="'.$active_plugins['Author'].'">'.__('url', 'wp-pluginsused').'</a>)</strong><br />'.$active_plugins['Description'].'</p>'; */
                    $temp .= '<p class="bns-support-active-plugin-item"><strong><a href="' . $active_plugins['Plugin_URI'] . '" title="' . $active_plugins['Plugin_Name'] . ' ' . $active_plugins['Version'] . '">' . $active_plugins['Plugin_Name'] . ' ' . $active_plugins['Version'] . '</a></strong><br />by ' .$active_plugins['Author'] . ' (<a href="' . $active_plugins['Author_URI'] . '" title="' . $active_plugins['Author'] . '">' . __( 'url' ) . '</a>)</p>';
                }
            }
        } else{
            if ( $plugins_used['inactive'] ) {
                foreach ( $plugins_used['inactive'] as $inactive_plugins ) {
                    $inactive_plugins['Plugin_Name'] = strip_tags( $inactive_plugins['Plugin_Name'] );
                    $inactive_plugins['Plugin_URI'] = strip_tags( $inactive_plugins['Plugin_URI'] );
                    $inactive_plugins['Description'] = strip_tags( $inactive_plugins['Description'] );
                    $inactive_plugins['Version'] = strip_tags( $inactive_plugins['Version'] );
                    $inactive_plugins['Author'] = strip_tags( $inactive_plugins['Author'] );
                    $inactive_plugins['Author_URI'] = strip_tags( $inactive_plugins['Author_URI'] );
                    $inactive_plugins['Version'] = strip_tags( $inactive_plugins['Version'] );
                    $temp .= '<p><img src="' . plugins_url( 'wp-pluginsused/images/plugin_inactive.gif' ) . '" alt="' . $inactive_plugins['Plugin_Name'] . ' ' . $inactive_plugins['Version'] . '" title="' . $inactive_plugins['Plugin_Name'] . ' ' . $inactive_plugins['Version'] . '" style="vertical-align: middle;" />&nbsp;&nbsp;<strong><a href="' . $inactive_plugins['Plugin_URI'] . '" title="' . $inactive_plugins['Plugin_Name'] . ' ' . $inactive_plugins['Version'] . '">' . $inactive_plugins['Plugin_Name'] . ' ' . $inactive_plugins['Version'] . '</a></strong><br /><strong>&raquo; ' . $inactive_plugins['Author'] . ' (<a href="' . $inactive_plugins['Author_URI'] . '" title="' . $inactive_plugins['Author'] . '">' . __( 'url' ) . '</a>)</strong><br />' . $inactive_plugins['Description'] . '</p>';
                }
            }
        }
        if ( $display ) {
            echo $temp;
        } else {
            return $temp;
        }
}
/* ---- Above credit to Lester Chan's plugin WP-PluginsUsed ---- */

/**
 * Enqueue Plugin Scripts and Styles
 *
 * Adds plugin stylesheet and allows for custom stylesheet to be added by end-user.
 *
 * @package BNS_Support
 * @since   1.0
 * @version 1.1
 *
 * Last revised November 23, 2011
 */
function BNS_Support_Scripts_and_Styles() {
        /* Enqueue Scripts */
        /* Enqueue Styles */
        wp_enqueue_style( 'BNS-Support-Style', plugin_dir_url( __FILE__ ) . 'bns-support-style.css', array(), '1.1', 'screen' );
        if ( is_readable( plugin_dir_path( __FILE__ ) . 'bns-support-custom-style.css' ) ) { // Only enqueue if available
            wp_enqueue_style( 'BNS-Support-Custom-Style', plugin_dir_url( __FILE__ ) . 'bns-support-custom-style.css', array(), '1.1', 'screen' );
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
                /* Widget settings. */
                $widget_ops = array( 'classname' => 'bns-support', 'description' => __( 'Widget to display and share common helpful support details.' ) );
                /* Widget control settings. */
                $control_ops = array( 'width' => 200, 'id_base' => 'bns-support' );
                /* Create the widget. */
                $this->WP_Widget( 'bns-support', 'BNS Support', $widget_ops, $control_ops );
        }

        function widget( $args, $instance ) {
                extract( $args );
                /* User-selected settings. */
                $title        = apply_filters( 'widget_title', $instance['title'] );
                $blog_admin   = $instance['blog_admin'];
                $show_plugins = $instance['show_plugins'];
                $credits      = $instance['credits'];

                global $current_user;
                if ( ( is_user_logged_in() ) ) { /* Must be logged in */
                    /**
                     * @todo Change to conditional based on capability not `user_level`
                     */
                    if ( ( !$blog_admin ) || ( $current_user->user_level == '10' ) ) {
                        echo '<div class="bns-support">'; /* CSS wrapper */

                        /* Before widget (defined by themes). */
                        echo $before_widget;

                        /* Title of widget (before and after defined by themes). */
                        if ( $title )
                            echo $before_title . $title . $after_title;

                        /* Start - Display support information */
                        echo '<ul>';

                        /* ---- Blog URL ---- */
                        echo '<li><strong>URL</strong>: ' . get_bloginfo( 'url' ) . '</li>';

                        /* ---- WordPress Version ---- */
                        global $wp_version;
                        echo '<li><strong>WordPress Version:</strong> ' . $wp_version . '</li>';
                        echo '<li><strong>PHP version:</strong> ' . phpversion() . '</li>';
                        echo '<li><strong>MySQL version:</strong> ' . mysqli_get_client_info() . '</li>';
                        echo '<li><strong>Multisite Enabled:</strong> ' . ( ( function_exists( 'is_multisite' ) && is_multisite() ) ? 'True' : 'False' ) . '</li>';

                        /* ---- Child Theme with Version and Parent Theme with Version ---- */
                        $theme_version = ''; /* Clear variable */
                        /* Get details of the theme / child theme */
                        $blog_css_url = get_stylesheet_directory() . '/style.css';
                        $my_theme_data = get_theme_data( $blog_css_url );
                        $parent_blog_css_url = get_template_directory() . '/style.css';
                        $parent_theme_data = get_theme_data( $parent_blog_css_url );
                        /* Create and append to string to be displayed */
                        $theme_version .= $my_theme_data['Name'] . ' v' . $my_theme_data['Version'];
                        if ( $blog_css_url != $parent_blog_css_url ) {
                            $theme_version .= ' a child of the ' . $parent_theme_data['Name'] . ' theme v' . $parent_theme_data['Version'];
                        }

                        /* Display string */
                        echo '<li><strong>Theme</strong>: ' . $theme_version . '</li>';

                        if ( function_exists( 'is_multisite' ) && is_multisite() ) {
                            $current_site = get_current_site();
                            $home_domain = 'http://' . $current_site->domain . $current_site->path;
                            /**
                             * @todo Change to conditional based on capability not `user_level`
                             */
                            if ( $current_user->user_level < 10 ) {
                                /* If multisite is "true" then direct ALL users to main site administrator */
                                echo '<li>Please review with your main site administrator at <a href="' . $home_domain . '">' . $current_site->site_name . '</a> for additional assistance.</li>';
                            } else {
                                echo 'You are the Admin';
                            }
                        } else {
                            /* ---- Current User Level ---- */
                            $user_roles = $current_user->roles;
                            /**
                             * @todo Re-write to show all roles of current user
                             */
                            $user_role = array_shift($user_roles);
                            echo '<li><strong>Current User Role</strong>: ' . $user_role . '</li>';
                            /* ---- Active Plugins ---- */
                            /* Code credit to Lester Chan's plugin at http://lesterchan.net/portfolio/programming/php/#wp-pluginsused */
                            if ( $show_plugins ) {
                                echo '<li><strong>Active Plugins</strong>:';
                                display_pluginsused( 'active', $display = true );
                                echo '</li>';
                            }
                        }
                        echo '</ul>';
                        /* End - Display support information */

                        /* Gratuitous self-promotion */
                        if ( $credits ) {
                            echo '<h6>Compliments of <a href="http://buynowshop.com/wordpress-services" target="_blank">WordPress Services</a> at <a href="http://buynowshop.com" target="_blank">BuyNowShop.com</a></h6>';
                        }

                        /* After widget (defined by themes). */
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
                    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
                    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
                </p>

                <p>
                    <input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['blog_admin'], true ); ?> id="<?php echo $this->get_field_id( 'blog_admin' ); ?>" name="<?php echo $this->get_field_name( 'blog_admin' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'blog_admin' ); ?>"><?php _e( 'Only show to administrators?' ); ?></label>
                </p>

                <hr />

                <p>
                    <input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_plugins'], true ); ?> id="<?php echo $this->get_field_id( 'show_plugins' ); ?>" name="<?php echo $this->get_field_name( 'show_plugins' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'show_plugins' ); ?>"><?php _e( 'Show active plugins?' ); ?></label>
                </p>

                <hr />

                <p>
                    <input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['credits'], true ); ?> id="<?php echo $this->get_field_id( 'credits' ); ?>" name="<?php echo $this->get_field_name( 'credits' ); ?>" />
                    <label for="<?php echo $this->get_field_id( 'credits' ); ?>"><?php _e( 'Show complimentary link to ' ); ?></label><a href="http://buynowshop.com/">BuyNowShop.com</a>?
                </p>

                <?php }
} // End class BNS_Support_Widget
?>