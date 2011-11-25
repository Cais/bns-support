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
 * @todo Find way to display active plugins with links ...
 */

/** ------------------------------------------------------------------------- */
/**
 * Plugin Lister
 * - Thanks to a GPL-compatible license, Plugin Lister by Paul G Getty is being
 * included in its entirety.
 *
 * @package BNS_Support
 * @since 1.9.1
 *
 *
 * @subpackage Plugin_Lister
 * @link http://wordpress.org/extend/plugins/wp-plugin-lister/
 * @version 2.1.0
 *
 * @author Paul G Petty
 * @link http://paulgriffinpetty.com
 *
 * @todo completely merge, strip out excess, and re-write(?) 'Plugin Lister'
 */

define("PluginListerVersion", "2.1.0");

$PluginListerOptions = array(
    'title' => '',
    'description' => '',
    'show_theme_data' => 'off'
);

define("PluginListerOptions", serialize($PluginListerOptions));

function wp_list_all_active_plugins() {

    if(!function_exists('get_plugin_data')){

    	function get_plugin_data( $plugin_file, $markup = true, $translate = true ) {
    		// We don't need to write to the file, so just open for reading.
    		$fp = fopen($plugin_file, 'r');

    		// Pull only the first 8kiB of the file in.
    		$plugin_data = fread( $fp, 8192 );

    		// PHP will close file handle, but we are good citizens.
    		fclose($fp);

    		preg_match( '|Plugin Name:(.*)$|mi', $plugin_data, $name );
    		preg_match( '|Plugin URI:(.*)$|mi', $plugin_data, $uri );
    		preg_match( '|Version:(.*)|i', $plugin_data, $version );
    		preg_match( '|Description:(.*)$|mi', $plugin_data, $description );
    		preg_match( '|Author:(.*)$|mi', $plugin_data, $author_name );
    		preg_match( '|Author URI:(.*)$|mi', $plugin_data, $author_uri );
    		preg_match( '|Text Domain:(.*)$|mi', $plugin_data, $text_domain );
    		preg_match( '|Domain Path:(.*)$|mi', $plugin_data, $domain_path );

    		foreach ( array( 'name', 'uri', 'version', 'description', 'author_name', 'author_uri', 'text_domain', 'domain_path' ) as $field ) {
    			if ( !empty( ${$field} ) )
    				${$field} = trim(${$field}[1]);
    			else
    				${$field} = '';
    		}

    		$plugin_data = array(
    					'Name' => $name, 'Title' => $name, 'PluginURI' => $uri, 'Description' => $description,
    					'Author' => $author_name, 'AuthorURI' => $author_uri, 'Version' => $version,
    					'TextDomain' => $text_domain, 'DomainPath' => $domain_path
    					);
    		if ( $markup || $translate )
    			$plugin_data = _get_plugin_data_markup_translate($plugin_data, $markup, $translate);
    		return $plugin_data;
    	}

    }

    if(!function_exists('_get_plugin_data_markup_translate')){

    	function _get_plugin_data_markup_translate($plugin_data, $markup = true, $translate = true) {

    		//Translate fields
    		if( $translate && ! empty($plugin_data['TextDomain']) ) {
    			if( ! empty( $plugin_data['DomainPath'] ) )
                    load_plugin_textdomain($plugin_data['TextDomain'], dirname($plugin_file). $plugin_data['DomainPath']);
    			else
    				load_plugin_textdomain($plugin_data['TextDomain'], dirname($plugin_file));

    			foreach ( array('Name', 'PluginURI', 'Description', 'Author', 'AuthorURI', 'Version') as $field )
    				$plugin_data[ $field ] = translate($plugin_data[ $field ], $plugin_data['TextDomain']);
    		}

    		//Apply Markup
    		if ( $markup ) {
    			if ( ! empty($plugin_data['PluginURI']) && ! empty($plugin_data['Name']) )
    				$plugin_data['Title'] = '<a href="' . $plugin_data['PluginURI'] . '" title="' . __( 'Visit plugin homepage' ) . '">' . $plugin_data['Name'] . '</a>';
    			else
    				$plugin_data['Title'] = $plugin_data['Name'];

    			if ( ! empty($plugin_data['AuthorURI']) )
    				$plugin_data['Author'] = '<a href="' . $plugin_data['AuthorURI'] . '" title="' . __( 'Visit author homepage' ) . '">' . $plugin_data['Author'] . '</a>';

    			$plugin_data['Description'] = wptexturize( $plugin_data['Description'] );
    			if( ! empty($plugin_data['Author']) )
    				$plugin_data['Description'] .= ' <cite>' . sprintf( __('By %s'), $plugin_data['Author'] ) . '.</cite>';
    		}

    		$plugins_allowedtags = array('a' => array('href' => array(),'title' => array()),'abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array());

    		// Sanitize all displayed data
    		$plugin_data['Title']       = wp_kses($plugin_data['Title'], $plugins_allowedtags);
    		$plugin_data['Version']     = wp_kses($plugin_data['Version'], $plugins_allowedtags);
    		$plugin_data['Description'] = wp_kses($plugin_data['Description'], $plugins_allowedtags);
    		$plugin_data['Author']      = wp_kses($plugin_data['Author'], $plugins_allowedtags);

    		return $plugin_data;
    	}

    }

	$p = get_option('active_plugins');

    $plugin_list = "";

	$plugin_list .= "<ul>";

	foreach ($p as $q) {

		$d = get_plugin_data( WP_PLUGIN_DIR."/".$q , false , false );

		$plugin_list .= "<li>";
		$plugin_list .= "<h4><a href='" . $d['PluginURI'] . "' target='_new'>" . $d['Title'] . "</a></h4>";
        $plugin_list .= "(Version: " . $d['Version'] . ")";
		$plugin_list .= "<p>";

		$plugin_list .= $d['Description'];

		if ($d['AuthorURI'] != "") {
		  $plugin_list .= "<br /><em>Created by:</em> <a href='".$d['AuthorURI']."' target='_new'>".$d['Author']."</a>";
		} else {
    		$plugin_list .= "<br /><em>Created by:</em> ".$d['Author'];
		}

		$plugin_list .= "</p>";
		$plugin_list .= "</li>";

	}

    echo $plugin_list;

}
add_action('activate_plugin_lister.php', 'wp_list_all_active_plugins');
/** ------------------------------------------------------------------------- */

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
                /** Widget settings */
                $widget_ops = array( 'classname' => 'bns-support', 'description' => __( 'Widget to display and share common helpful support details.' ) );
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
                if ( ( is_user_logged_in() ) ) { /* Must be logged in */
                    /**
                     * @todo Change to conditional based on capability not `user_level`
                     */
                    /** @noinspection PhpUndefinedFieldInspection */
                    if ( ( !$blog_admin ) || ( $current_user->user_level == '10' ) ) {
                        echo '<div class="bns-support">'; /* CSS wrapper */

                        /** @var    $before_widget  string - defined by theme */
                        echo $before_widget;
                        /** Widget $title, $before_widget, and $after_widget defined by theme */
                        if ( $title )
                            /**
                             * @var $before_title   string - defined by theme
                             * @var $after_title    string - defined by theme
                             */
                            /** @noinspection PhpUndefinedVariableInspection */
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
                            /** @noinspection PhpUndefinedFieldInspection */
                            $home_domain = 'http://' . $current_site->domain . $current_site->path;
                            /**
                             * @todo Change to conditional based on capability not `user_level`
                             */
                            /** @noinspection PhpUndefinedFieldInspection */
                            if ( $current_user->user_level < 10 ) {
                                /* If multisite is "true" then direct ALL users to main site administrator */
                                /** @noinspection PhpUndefinedFieldInspection */
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

                            /**
                             * @todo Display Active Plugins
                             */
                            if ( $show_plugins ) {
                                echo '<li><strong>Active Plugins</strong>:';
                                echo 'Still a Work in Bacon ... er, Progress.';
                                echo '</li>';

                                wp_list_all_active_plugins();
                            }
                            
                        }
                        echo '</ul>';
                        /* End - Display support information */

                        /* Gratuitous self-promotion */
                        if ( $credits ) {
                            echo '<h6>Compliments of <a href="http://buynowshop.com/wordpress-services" target="_blank">WordPress Services</a> at <a href="http://buynowshop.com" target="_blank">BuyNowShop.com</a></h6>';
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