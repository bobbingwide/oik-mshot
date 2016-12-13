<?php
/**
Plugin Name: oik mshot
Depends: oik base plugin, oik fields
Plugin URI: http://www.oik-plugins.com/oik-plugins/oik-mshot
Description: [bw_mshot] shortcode to display the screenshot for a website's (home) page and provide the "mshot" custom field type
Version: 0.2
Author: bobbingwide
Author URI: http://www.bobbingwide.com
License: GPL2

    Copyright 2012, 2013 Bobbing Wide (email : herb@bobbingwide.com )

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
}

/**
 * Implement "oik_pre_theme_field" for oik-mshot 
 */
function oikms_pre_theme_field() {
  oik_require( "includes/oik-mshot.inc", "oik-mshot" );
}

/**
 * Implement "oik_admin_menu" for oik-mshot 
 */
function oikms_admin_menu() {
  oik_register_plugin_server( __FILE__ );
} 

/**
 * Dependency checking for oik-fields. Requires oik v2.0
 */ 
function oikms_activation() {
  static $plugin_basename = null;
  if ( !$plugin_basename ) {
    $plugin_basename = plugin_basename(__FILE__);
    add_action( "after_plugin_row_" . $plugin_basename, __FUNCTION__ );   
    require_once( "admin/oik-activation.php" );
  }  
  $depends = "oik:2.0-alpha,oik-fields:";
  oik_plugin_lazy_activation( __FILE__, $depends, "oik_plugin_plugin_inactive" );
}

/**
 * Implement "oik_query_field_types" for oik-mshot
 * 
 */
function oikms_query_field_types( $field_types ) {
  $field_types['mshot'] = __( "mshot - screen capture", 'oik-mshot' ); 
  return( $field_types );
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
}

oikms_plugin_loaded();
