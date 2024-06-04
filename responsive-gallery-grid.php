<?php
/*
Plugin Name: Responsive Gallery Grid
Plugin URI: https://responsive-gallery-grid.bdwm.be
Description: Converts the default wordpress gallery to a Google+ styled image gallery grid, where the images are scaled to fill the gallery container, while maintaining image aspect ratio's.
Author: Jules Colle, BDWM
Author URI: http://bdwm.be
Version: 2.3.17

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

// don't do anything if the RGG plugin is already loaded
// (RGG and RGG Pro should not be activated together, but if they are, make sure only the first one loaded is used.)

if (!function_exists('rgg_gallery_shortcode')) {

    define('RGG_PLUGIN', 'rgg');
    define('RGG_OPTIONS', 'rgg_options');

    define( 'RGG_VERSION', '2.3.17' );
    define( 'RGG_REQUIRED_WP_VERSION', '4.1' );
    define( 'RGG_PLUGIN_PATH', __FILE__ );
    define( 'RGG_PLUGIN_BASENAME', plugin_basename( RGG_PLUGIN_PATH ) );
    define( 'RGG_PLUGIN_NAME', trim( dirname( RGG_PLUGIN_BASENAME ), '/' ) );
    define( 'RGG_PLUGIN_DIR', untrailingslashit( dirname( RGG_PLUGIN_PATH ) ) );
    define( 'RGG_PLUGIN_DIR_URL', plugins_url('',RGG_PLUGIN_PATH));

	define('RGG_IS_PRO', file_exists(RGG_PLUGIN_DIR.'/pro/rgg_pro_options.php'));

	global $rgg_options, $rgg_settings;
	$rgg_settings = array();
	require_once RGG_PLUGIN_DIR.'/rgg-options.php';
	if (RGG_IS_PRO) {
		require_once RGG_PLUGIN_DIR.'/pro/update.php';
	}
    require_once RGG_PLUGIN_DIR.'/gallery-shortcode.php';


    // register scripts and styles

    function rgg_register_scripts() {


        global $rgg_settings, $rgg_options;

        if (count($rgg_settings) == 0) return;

	    $rgg_settings['rgg_is_pro'] = RGG_IS_PRO;

	    $deps = array('jquery');

        foreach ($rgg_settings as $single_gallery_settings) {

            if (!is_array($single_gallery_settings)) {
                continue;
            }

            if (
                $single_gallery_settings['lightbox'] == 'simplelightbox'
            ) {
                wp_enqueue_script( 'rgg-simplelightbox', RGG_PLUGIN_DIR_URL . '/lib/simplelightbox/simple-lightbox.min.js', array( 'jquery' ), RGG_VERSION );
                $deps[] = 'rgg-simplelightbox';
            }
            if ($single_gallery_settings['lightbox'] == 'image-above') {
                wp_enqueue_script('slick', RGG_PLUGIN_DIR_URL.'/lib/slick/slick.1.9.0.min.js', ['jquery'], RGG_VERSION);
                $deps[] = 'slick';
            }

        }

        wp_enqueue_script('jquery');
        wp_enqueue_script('rgg-main', RGG_PLUGIN_DIR_URL.'/js/main.js', $deps, RGG_VERSION );
        wp_localize_script( 'rgg-main', 'rgg_params', $rgg_settings );
    }

    function rgg_register_styles() {

	    //global $rgg_settings, $rgg_options;

	    // enqueue css

	    $deps = array();

	    // big change: load all css always. (in header.) This is because there is no way to know at this point if a shortcode will be loaded that will need the css.
        // TODO: at least minimize it then?

        wp_enqueue_style( 'rgg-simplelightbox', RGG_PLUGIN_DIR_URL.'/lib/simplelightbox/simplelightbox.min.css', array(), RGG_VERSION);
        $deps[] = 'rgg-simplelightbox';

        wp_enqueue_style('slickstyle', RGG_PLUGIN_DIR_URL.'/lib/slick/slick.1.9.0.min.css', [], RGG_VERSION);
        wp_enqueue_style('slick-theme', RGG_PLUGIN_DIR_URL.'/lib/slick/slick-theme.css', ['slickstyle'], RGG_VERSION);
        $deps[] = 'slick-theme';

        wp_enqueue_style( 'rgg-style', RGG_PLUGIN_DIR_URL.'/css/style.css', $deps, RGG_VERSION );

    }

    add_action('wp_footer', 'rgg_register_scripts');
    add_action('wp_enqueue_scripts', 'rgg_register_styles');
}