<?php // (C) Copyright Bobbing Wide 2016

/** 
 * Class: OIK_mshot2
 * 
 * post_meta data for the mshot2 field type
 *
 */
class OIK_mshot2 {

								
	public $url;
	public $id;
	public $field; // Field name e.g. _mshot
	public $value; // post_meta data
	public $current_url;
	public $current_id;
	public $post_id;
	public $mshot;
	public $file;
	
	/**
	 * Constructor for OIK_mshot2 class
	 */
	
	function __construct( $post_id, $field ) {
		$this->post_id = $post_id;
		$this->field = $field;
	}
	
	/**
	 * Get the post_meta for the post id and field name
	 */
	function get_post_meta() {
		$value = get_post_meta( $this->post_id, $this->field, false );
		$serialized = bw_array_get( $value, 0, $value );
		$unserialized = unserialize( $serialized );
		$this->current_url = bw_array_get( $unserialized, "url", null );
		$this->current_id = bw_array_get( $unserialized, "id", null );
		if ( !is_numeric( $this->current_id ) ) {
			$this->current_id = 0;
			//gob();
		}
	}
	
	
	/** 
	 * Set the $_POST value to update the post_meta
	 *
	 * We rely on oik-fields to update the post_meta data
	 * 
	 * Note: The url and id can be set to null.
	 */
	function set_post_meta() {
		$_POST[ $this->field ] = serialize( array( "url" => $this->url, "id" => $this->id ) );
	}
	
	/**
	 * Fetch the mshot 
	 * 
	 * using the mshot service that Automattic use for wordpress.com
	 * fetch the screenshot to be saved.
	 
	 * Note: From my local machine I get 
	 * Warning: file_get_contents(http://s.wordpress.com/mshots/v1/http%3A%2F%2Fbigram.co.uk?w=600): 
	 * failed to open stream: HTTP request failed! HTTP/1.1 403 Forbidden in 
	 */
	function fetch_mshot() {
		oik_require( "shortcodes/oik-mshot.php", "oik-mshot" );
		//oik_require_lib( "class-oik-remote" );
		$atts = array( "url" => $this->url );
		$mshoturl = oikms_get_mshot_url( $atts );
		bw_trace2( $mshoturl, "mshoturl" );
		//$mshot = file_get_contents( $mshoturl );
		//$this->mshot = oik_remote::bw_remote_get2( $mshoturl );
		//bw_trace2( $this->mshot, "mshot" );
		$file = download_url( $mshoturl );
		bw_trace2( $file, "file" );
		
		// @TODO check that the file is not default.gif
		// 
    $this->id = oikms_create_attachment( $atts, $file, "cached screenshot", $this->post_id );
		bw_trace2( $this->id, "attachment id" );
		
		// gob();
	}
	
	
	
	function save_mshot_as_attachment() {
		// this will set $this->id
		
	} 
	
	/**
	 * Check if the mshot needs updating
	 *
	 * And perform the update if necessary
	 *
	 * url | current_url | current_id | Processing
	 * --- | ----------- | ---------- | -----------
	 * set | diff        | ? | Fetch the mshot, save as an attachment and set post meta
	 * set | set         | ? | Do nothing the value is unchanged
	 * set | set         | null | fetch the mshost, save as an attachment and set post meta
	 * set | null        | ? | fetch the mshot, save as an attachment and set post meta
	 * null | set        | ? | Update the post meta
	 * null | null       | ? | Do nothing
	 * 
	 */
	
	function check() {
		$fetch = false;
		$this->get_post_meta();
		$this->get_url();
		if ( $this->url ) {
			if ( $this->current_url ) {
				if ( $this->url != $this->current_url ) {
					$fetch = true;
				} else {
					if ( !$this->current_id ) {
						$fetch = true;
					} else {
						gob();
					}
				}
			} else {
				$fetch = true;
			}
		} else {
			$this->id = $this->current_id; // or null ?
		}
		if ( $fetch ) {
			$this->fetch_mshot();
			$this->save_mshot_as_attachment();
		}
		$this->set_post_meta();
	}
	
	/**
	 * Get the url specified on the page
	 *
	 * Do we need to validate it?
	 */
	function get_url() {
		$this->url = $_POST[ $this->field ];
	}




}
