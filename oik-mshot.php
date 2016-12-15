<?php
/**
Plugin Name: oik mshot
Depends: oik base plugin, oik fields
Plugin URI: http://www.oik-plugins.com/oik-plugins/oik-mshot
Description: [bw_mshot] shortcode to display the screenshot for a website's (home) page and provide the "mshot" custom field type
Version: 0.3.1
Author: bobbingwide
Author URI: http://www.bobbingwide.com
License: GPL2

    Copyright 2012-2016 Bobbing Wide (email : herb@bobbingwide.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html

*/

/**
 * Implements action "oik_loaded"
 */
function oikms_loaded() {
  bw_add_shortcode( "bw_mshot", "oikms_mshot", oik_path( "shortcodes/oik-mshot.php", "oik-mshot"), false );
} 

/**
 * Implement "oik_pre_form_field" for oik-mshot
 */
function oikms_pre_form_field() {
  oik_require( "includes/oik-mshot.inc", "oik-mshot" );
  oik_require( "includes/oik-mshot2.inc", "oik-mshot" );
}

/**
 * Implement "oik_pre_theme_field" for oik-mshot 
 */
function oikms_pre_theme_field() {
  oik_require( "includes/oik-mshot.inc", "oik-mshot" );
  oik_require( "includes/oik-mshot2.inc", "oik-mshot" );
}

/**
 * Implement "oik_admin_menu" for oik-mshot 
 */
function oikms_admin_menu() {
  oik_register_plugin_server( __FILE__ );
} 

/**
 * Implement "admin_notices" dependency checking for oik-mshot
 *
 * - Requires oik v2.0
 * - Now requires oik v2.2 and oik-fields
 * - v0.3.1 now requires oik v3.1 and oik-fields v1.40.4
 */ 
function oikms_activation() {
  static $plugin_basename = null;
  if ( !$plugin_basename ) {
    $plugin_basename = plugin_basename(__FILE__);
    add_action( "after_plugin_row_oik-mshot/oik-mshot.php", "oikms_activation" ); 
    if ( !function_exists( "oik_plugin_lazy_activation" ) ) { 
      require_once( "admin/oik-activation.php" );
    }
  }  
  $depends = "oik:3.1,oik-fields:1.40.4";
  oik_plugin_lazy_activation( __FILE__, $depends, "oik_plugin_plugin_inactive" );
}

/**
 * Implement "oik_query_field_types" for oik-mshot
 * 
 */
function oikms_query_field_types( $field_types ) {
  $field_types['mshot'] = __( "mshot - screen capture", 'oik-mshot' ); 
  $field_types['mshot2'] = __( "cached mshot - screen capture", 'oik-mshot' ); 
  return( $field_types );
}

/**
 * Implement save_post for oik-mshot
 *
 * @param ID $post_id
 * @param array $post
 */
function oikms_save_post( $post_id, $post ) {
	// Check validity of the call
  if ( $post->post_status != "auto-draft"  ) {
		oik_require( "admin/oik-mshot-save-post.php", "oik-mshot" );
		oikms_lazy_save_post( $post_id, $post );
	}	
}

/**
 * Initialisation for oik-mshot 
 */
function oikms_plugin_loaded() {
  add_action( "oik_loaded", "oikms_loaded" );
  add_action( "oik_pre_form_field", "oikms_pre_form_field" );
  add_action( "oik_pre_theme_field", "oikms_pre_theme_field" );
  add_action( "admin_notices", "oikms_activation" );
  add_action( "oik_admin_menu", "oikms_admin_menu" );
  add_action( "oik_query_field_types", "oikms_query_field_types" );
	add_action( "save_post", "oikms_save_post", 9, 2 );
}

oikms_plugin_loaded();
