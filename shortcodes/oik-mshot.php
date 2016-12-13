<?php // (C) Copyright Bobbing Wide 2012, 2013

/**
 * Return an mshots image file 
 *
 * @link http://www.wprecipes.com/wordpress-shortcode-display-a-thumbnail-of-any-website
 * @link http://www.binarymoon.co.uk/2010/02/automated-take-screenshots-website-free/
 *
 */
function oikms_load_mshot( $atts ) {

  $mshot = bw_array_get( $atts, "mshot", 'http://s.wordpress.com/mshots/v1/' );
  $url = bw_array_get_arr( $atts, "url,0", "http://www.oik-plugins.co.uk" );
  $title = bw_array_get( $atts, "title", null );
  $w = bw_array_get( $atts, "w", 600 );
  $h = bw_array_get( $atts, "h", null );
  
  $src = $mshot;
  $src .= urlencode( $url );
  $src .= '?';
  $src .= build_query( array( "w" => $w, "h" => $h ));
  
  oik_require( "includes/oik-remote.inc" );
  
  $result = bw_remote_get( $src ); 
   
  e( $src );
  
}  


function oikms_mshot( $atts=null ) {
  //oikms_load_mshot( $atts );
  
  $mshot = bw_array_get( $atts, "mshot", 'http://s.wordpress.com/mshots/v1/' );
  $url = bw_array_get( $atts, "url", "http://www.oik-plugins.com" );
  $title = bw_array_get( $atts, "title", null );
  $w = bw_array_get( $atts, "w", 600 );
  $h = bw_array_get( $atts, "h", null );
  
  $src = $mshot;
  $src .= urlencode( $url );
  $src .= '?';
  $src .= build_query( array( "w" => $w, "h" => $h ));
  $image = retimage( "mshot", $src, $title );
  e( $image );
  return( bw_ret() );
}
  
