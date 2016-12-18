<?php // (C) Copyright Bobbing Wide 2013-2016

/**
 * Implement bw_form_field_ hook for "mshot2"
 *
 * The mshot2 form field is stored in two parts:
 *
 * - url - the URL used to create the screenshot
 * - id - the post ID of the attachment created for that URL
 * 
 * @param string $name - field name 
 * @param string $type - expected to be mshot2
 * @param string $title 
 * @param array $value - an array of serialized post meta data
 * @param array $args - additional parameters
 */
function bw_form_field_mshot2( $name, $type, $title, $value, $args ) {
	bw_trace2();
	$serialized = bw_array_get( $value, 0, $value );
	$unserialized = unserialize( $serialized );
	$url = bw_array_get( $unserialized, "url", null );
	$id = bw_array_get( $unserialized, "id", null );
  bw_form_field_( $name, $type, $title, $url, $args );
	if ( $id ) {
		oikms_form_field_image( $name . "_cached", "image", "cached", $id, $args ); 
	}	else {
	
	 bw_form_field_( "ID", "number", "cached", $id, $args );
	}
}

/**
 * Display the cached image
 *
 * @TODO use bw_form_field_image when available
 * 
 * @param string $name - field name - not particularly relevant when it's output only
 * @param string $type - expected to be image
 * @param string $title 
 * @param string|ID $value - the post ID of the attachment to be displayed
 * @param array $args - additional parameters
 */
function oikms_form_field_image( $name, $type, $title, $value, $args ) {
	oik_require( "includes/bw_images.inc" );
	$thumbnail = bw_thumbnail( $value, array( "thumbnail" => "full" ), true );
	bw_tablerow( array( $title, $thumbnail ) );
}

/**
 * Theme an "mshot2" field 
 *
 * What we display depends on the values for url and id obtained from the serialised field.
 * Note: This field is not repeatable; it doesn't support #multiple.
 *
 * url | id  | processing
 * --- | --- | ---------
 * null | null | Don't display anything
 * null | set | Don't display anything - the id should not have been set
 * set  | null | Display the mshot dynamically
 * set  | set | Display the cached image 
 * 
 * @param string $key - field name e.g. _oikpp_mshot
 * @param mixed $value - post metadata value
 * @param array $field - the field structure if defined using bw_register_field()
 */
function bw_theme_field_mshot2( $key, $value, $field ) {
  oik_require( "shortcodes/oik-mshot.php", "oik-mshot" );
  bw_trace2(); 
	$serialized = bw_array_get( $value, 0, $value );
	$unserialized = unserialize( $serialized );
	$url = bw_array_get( $unserialized, "url", null );
	$id = bw_array_get( $unserialized, "id", null );
  if ( $url ) {
		if ( $id ) {
			oik_require( "includes/bw_images.inc" );
			e( bw_thumbnail( $id, array( "thumbnail" => "full" ), true ) );
		} else {
			e( oikms_mshot( array( "url" => $url ) ) );
		}
		bw_theme_field_URL( $key, $url );
  }  
}


