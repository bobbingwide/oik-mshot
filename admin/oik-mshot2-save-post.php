<?php // (C) Copyright Bobbing Wide 2016

/**
 * Lazy implementation of save_post for oik-mshot
 * 
 * @param ID $post_id
 * @param object $post
 */
function oikms_lazy_save_post( $post_id, $post ) {
	$fields = oikms_get_mshot_fields( $post->post_type );
	if ( count( $fields ) ) {
		foreach ( $fields as $field ) {
			oikms_check_mshot_id( $post_id, $post, $field );
		}
	}
}

/**
 * Return array of mshot2 fields
 * 
 * mshot2 fields contain cached mshots stored as attachments
 * 
 * The url part contains the address
 * The id is the post ID of the saved screenshot
 * 
 * @param string $post_type
 * @return array of mshot2 field names
 */
function oikms_get_mshot_fields( $post_type ) {
	$fields = array();
	global $bw_mapping;
	if ( isset(  $bw_mapping['field'][$post_type] )) {
		foreach ( $bw_mapping['field'][$post_type] as $field ) {
			$type = bw_query_field_type( $field );
			if ( "mshot2" == $type ) {
				$fields[ $field ] = $field;
			}
		}
	}	
	bw_trace2( $fields, "fields", false );
	return( $fields );
}

/**
 * Check if the current mshot ID needs updating
 * 
 * - Obtain the current serialized value for the given post and field name
 * - Check if we need to do anything
 * - Do what needs to be done
 * - Set the $_POST[ $field ] to the serialized value to be saved
 * 
 * @param ID $post_id
 * @param object $post
 * @param string $field - the field name
 */
function oikms_check_mshot_id( $post_id, $post, $field ) {
	oik_require( "admin/class-oik-mshot2.php", "oik-mshot" );
	$mshot = new OIK_mshot2( $post_id, $field );
	$mshot->check();
	//
	//$mshot = $_POST[ $field ];
	//$id = 19851;
	// We need to store it as a serialized field, not an array
	// 
	//$_POST[ $field ] = serialize( array( "url" => $mshot, "id" => $id ) );
	bw_trace2( $_POST, "_POST" );
}

	
