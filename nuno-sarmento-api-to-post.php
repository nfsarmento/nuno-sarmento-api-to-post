<?php
/*
Plugin Name: Nuno Sarmento API To Post
Description: Simple Json WP API to Post.
Plugin URI: https://www.nuno-sarmento.com
Version: 1.0.1
Author: Nuno Morais Sarmento
Author URI: https://www.nuno-sarmento.com
License: GPL2
*/
/*
Copyright 2017  Nuno Morais Sarmento  (email : nfsarmento@gmail.com)

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


/* Do not access this file directly */
defined('ABSPATH') or die('°_°’');

/* ------------------------------------------
// Constants ---------------------------
--------------------------------------------- */

/* Set plugin version constant. */

if( ! defined( 'NUNO_SARMENTO_API_TO_POST_BASE_VERSION' ) ) {
	define( 'NUNO_SARMENTO_API_TO_POST_BASE_VERSION', '1.0.0' );
}

/* Set plugin name. */

if( ! defined( 'NUNO_SARMENTO_API_TO_POST_NAME' ) ) {
	define( 'NUNO_SARMENTO_API_TO_POST_NAME', 'Nuno Sarmento API To Post' );
}

/* Set constant path to the plugin directory. */

if ( ! defined( 'NUNO_SARMENTO_API_TO_POST_BASE_PATH' ) ) {
	define( 'NUNO_SARMENTO_API_TO_POST_BASE_PATH', plugin_dir_path( __FILE__ ) );
}

/* Set the constant path to the plugin directory URI. */

if ( ! defined( 'NUNO_SARMENTO_API_TO_POST_BASE_URI' ) ) {
	define( 'NUNO_SARMENTO_API_TO_POST_BASE_URI', plugin_dir_url( __FILE__ ) );
}

/* ------------------------------------------
// i18n ----------------------------
--------------------------------------------- */

load_plugin_textdomain( 'nuno-sarmento-api-to-post', false, basename( dirname( __FILE__ ) ) . '/languages' );

/* ------------------------------------------
// Includes ---------------------------
--------------------------------------------- */

/* Settings */
if ( ! class_exists( 'Nuno_Sarmento_ATP_OptionsPage' ) ) {
  require_once NUNO_SARMENTO_API_TO_POST_BASE_PATH . 'admin/nuno-sarmento-api-to-post-settings.php';
}

/* Functions */

if ( ! @include( 'nuno-sarmento-api-to-post-functions.php' ) ) {
	require_once( NUNO_SARMENTO_API_TO_POST_BASE_PATH . 'includes/nuno-sarmento-api-to-post-functions.php' );
}


/* ------------------------------------------
// TinyMce Button for shortcode -------------
--------------------------------------------- */

add_action( 'admin_init', 'nsatp_tinymce_button' );
function nsatp_tinymce_button() {
     if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
          add_filter( 'mce_buttons', 'nsatp_register_tinymce_button' );
          add_filter( 'mce_external_plugins', 'nsatp_add_tinymce_button' );
     }
}

function nsatp_register_tinymce_button( $buttons ) {
     array_push( $buttons, "nsatp_button_eek" );
     return $buttons;
}

function nsatp_add_tinymce_button( $plugin_array ) {
	   $plugin_array['nsatp_button_script'] = plugin_dir_url( __FILE__ ) . 'assets/js/ns-apitopost-tinybt.js';
     return $plugin_array;
}
