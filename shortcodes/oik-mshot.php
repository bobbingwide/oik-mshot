<?php // (C) Copyright Bobbing Wide 2012, 2014


/**
 * Load the saved mshot image if it already exists 
 *
 * If we hve previously saved this image then the value for '_wp_attached_file' may contain a modified version of the original file name.
 * At the front will be the prefix for uploaded files
 * and at the end the filename may have had some additional characters added to make it unique.
 * So, http://herbmiller.me/about/site-history will get created as mshot-herbmiller.me-about-site-history.jpg when first saved
 * giving a file name of 2013/10/mshot-herbmiller.me-about-site-history.jpg
 * BUT if saved multiple times (why would that be ?) then a suffix is added before the file extension
 * e.g. 
 *
 */
function oikms_load_attachment( $atts ) {
  $filename = oikms_create_attachment_filename( $atts );
  $filename = str_replace( ".jpg", "", $filename );
  $args = array();
  $args['post_type'] = "attachment";
  // $args['numberposts'] = 1;
  $args['orderby'] = "date";
  $args['order'] = "DESC"; 
  $args['post_status'] = 'inherit';
  //$args['name'] = $filename; 
  $args['post_parent'] = 0;
  $args['meta_key'] =  "_wp_attached_file";
  $args['meta_compare'] = "LIKE";
  $args['meta_value'] = $filename; 
  oik_require( "includes/bw_posts.inc" );
  $posts = bw_get_posts( $args );  
  if ( $posts ) {
    $id = $posts[0]->ID;
    // $image = bw_thumbnail( $id, "full", true );
    p( "attachment $id" );
    $image = $posts[0]->guid;
  } else { 
    $image = null;  
  }  
  return( $image );  
}

/**
 * Implement our own media_sideload_image for an mshot image
 */
function oikms_media_sideload_image( $atts, $src ) {
  p( "caching file $src" );
  require_once( ABSPATH . 'wp-admin/includes/media.php' );
  require_once( ABSPATH . 'wp-admin/includes/file.php' );
  require_once( ABSPATH . 'wp-admin/includes/image.php' );
  $url = bw_array_get( $atts, "url", null );
  $desc = bw_array_get( $atts, "title", $url );
  // $image = media_sideload_image( $src, 0, $desc );
  $file = download_url( $src );
  if ( is_wp_error( $file ) ) {
    bw_trace2( $file ); 
    oikms_mshot( $atts );
    $image = null;
  } else { 
    p( "cached $file" );
    $post_id = oikms_create_attachment( $atts, $file, $desc );
    if ( is_wp_error( $post_id ) ) { 
      bw_trace2( $post_id );
      unlink( $file );
      $image = null;
    } else {
      $image = wp_get_attachment_url( $post_id );
    }
  }
  return( $image );
}


/**
function oikms_wp_handle_sideload( &$file, $overrides = false, $time = null ) {
  wp_handle_sideload( $file, array( 
}
*/

/**
 * Create the attachment file name given the URL
 */
function oikms_create_attachment_filename( $atts ) {
  $url = bw_array_get( $atts, "url", null ); 
  if ( $url ) {
    $parsed_url = parse_url( $url );
    $target_file_name = "mshot-";
    $target_file_name .= $parsed_url['host'];
    $target_file_name .= bw_array_get( $parsed_url, 'path', null );
    $target_file_name .= ".jpg";
    $target_file_name = str_replace( "/", "-", $target_file_name );
  } else {
    $target_file_name = "unknown.jpg";
  }   
  return( $target_file_name );
}  

/**
 * Create the attachment file from the temporary file
 *
 * Use media_handle_sideload() to do the validation and storage stuff
 */
function oikms_create_attachment( $atts, $file, $desc, $post_id=0 ) {
  $file_array['tmp_name'] = $file;
  $file_array['type'] = mime_content_type( $file ); 
  $file_array['name'] = oikms_create_attachment_filename( $atts );   
  
  bw_trace2( $file_array ); 
  
  $id = media_handle_sideload( $file_array, $post_id, $desc );
  if ( is_wp_error( $id ) ) {
    bw_trace2( $id );
  } else {
    // e( "attachment: $id" );
  }    
  return( $id );
}

/**
 * Return the URL for accessing the mshot 
 */
function oikms_get_mshot_url( $atts ) {
  $mshot = bw_array_get( $atts, "mshot", 'http://s.wordpress.com/mshots/v1/' );
  $url = bw_array_get( $atts, "url", "http://www.oik-plugins.com" );
  $title = bw_array_get( $atts, "title", null );
  $w = bw_array_get( $atts, "w", 600 );
  $h = bw_array_get( $atts, "h", null );
  
  $src = $mshot;
  $src .= urlencode( $url );
  $src .= '?';
  $src .= build_query( array( "w" => $w, "h" => $h ));
  return( $src );
}    

/**
 * Return an mshots image file 
 *
 * @link http://www.wprecipes.com/wordpress-shortcode-display-a-thumbnail-of-any-website
 * @link http://www.binarymoon.co.uk/2010/02/automated-take-screenshots-website-free/
 *
 */
function oikms_load_mshot( $atts ) {
  $image = oikms_load_attachment( $atts );
  if ( !$image ) { 
    $src = oikms_get_mshot_url( $atts ); 
    $image = oikms_media_sideload_image( $atts, $src );
  }  
  return( $image );
}  

/**
 * Implement [bw_mshot] shortcode
 * 
 * Use cache to cause the shortcode to cache the mshot as an attachment
 */
function oikms_mshot( $atts=null, $content=null, $tag=null ) {
  $cache = bw_array_get( $atts, "cache", null );
  if ( $cache ) {
     $atts['url'] = $cache;
     $src = oikms_load_mshot( $atts );
  } else {
    $src = oikms_get_mshot_url( $atts ); 
  } 
  $title = bw_array_get( $atts, "title", null );
  $image = retimage( "mshot", $src, $title );
  e( $image );
  return( bw_ret() );
}
  
