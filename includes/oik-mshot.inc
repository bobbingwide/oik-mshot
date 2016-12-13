<?php // (C) Copyright Bobbing Wide 2013

/**
 * Implement bw_form_field_ hook for "mshot"
 *
 * The mshot form field is simply an URL
 * But perhaps there should be a preview field as well? **?** 2013/07/03
 * 
 */
function bw_form_field_mshot( $name, $type, $title, $value, $args ) {
  bw_form_field_( $name, $type, $title, $value, $args );
}

/**
 * Theme an "mshot" field 
 
 * @param string $key - field name e.g. _oikpp_mshot
 * @param mixed $value - post metadata value
 * @param array $field - the field structure if defined using bw_register_field()
 */
function bw_theme_field_mshot( $key, $value, $field ) {
  oik_require( "shortcodes/oik-mshot.php", "oik-mshot" );
  e( oikms_mshot( array( "url" => $value ) ) );
  bw_theme_field_URL( $key, $value );
}


