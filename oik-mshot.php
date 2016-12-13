<?php
/**
Plugin Name: oik mshot
Depends: oik base plugin, oik fields
Plugin URI: http://www.oik-plugins.com/oik-plugins/oik-mshot
Description: [bw_mshot] shortcode to display the screenshot for a website's home page
Version: 0.1
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

// We should only do this when it's admin and oik has been loaded **?**
add_action( "oik_loaded", "oikms_loaded" );
//add_filter( "oik_admin_menu", "oikms_admin_menu" );


/**
 * Implements action "oik_loaded"
 * 
 * 
 */
function oikms_loaded() {
  bw_add_shortcode( "bw_mshot", "oikms_mshot", oik_path( "shortcodes/oik-mshot.php", "oik-mshot"), false );
} 

function _oikms_private() {
}
