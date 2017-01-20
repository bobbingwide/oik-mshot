<?php // (C) Copyright Bobbing Wide 2016, 2017

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
		$this->current_url = null;
		$this->current_id = null;
	}

	/**
	 * Get the post_meta for the post id and field name
	 * 
	 * We assume the current_url and current_id are not set. 
	 * If the current_id is not numeric we set it to 0.
	 */
	function get_post_meta() {
		$this->current_url = null;
		$this->current_id = 0;
		$value = get_post_meta( $this->post_id, $this->field, false );
		bw_trace2( $value, "post_meta" );
		$serialized = bw_array_get( $value, 0, null );
		if ( $serialized ) {
			$unserialized = unserialize( $serialized );
			$this->current_url = bw_array_get( $unserialized, "url", null );
			$this->current_id = bw_array_get( $unserialized, "id", null );
			if ( !is_numeric( $this->current_id ) ) {
				bw_trace2( $this->current_id, "current_id non-numeric", false, BW_TRACE_ERROR );
				$this->current_id = 0;
			}
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
	 
	 * 
	 * download_url() doesn't get this problem, but it might return an 'http_404' code when it's actually received a 307.
	 * This can be detected by looking at
	 * 
	 * @TODO check that the file is not default.gif
	 * @link https://github.com/Automattic/mShots
	 */
	function fetch_mshot() {
		oik_require( "shortcodes/oik-mshot.php", "oik-mshot" );
		$atts = array( "url" => $this->url );
		$mshoturl = oikms_get_mshot_url( $atts );
		$file = $this->download_url( $mshoturl );
		if ( is_wp_error( $file ) ) {
			$this->id = $this->current_id;
		} else {
			$this->id = oikms_create_attachment( $atts, $file, "cached screenshot for " . $this->url, $this->post_id );
			bw_trace2( $this->id, "attachment id" );
		}
	}

	/**
	 * Downloads the URL to a file.
	 * 
	 * Having received the file name we need to check it's what we wanted.
	 * We need to stop the loop after a sensible number of requests.
	 */
	function download_url( $mshoturl ) { 		
		bw_trace2( $mshoturl, "mshoturl" );
		$jpeg = false;
		$count = 0;
		while ( false === $jpeg && $count <= 3 ) {
			$count++;
			$file = download_url( $mshoturl );
			bw_trace2( $file, "file or WP_Error" );
			if ( is_wp_error( $file ) ) {
				break; 
			}	else {
				$jpeg = $this->check_downloaded_file( $file );
			}
		}
		return( $file );
	}
	
	/**
	 * Check we've got a jpeg
	 *
	 * We want to avoid the default.gif file
	 *
	 * @param string $file
	 * @return bool true when it's a jpeg
	 */
	function check_downloaded_file( $file ) {
		$image_size = getimagesize( $file );
		$mime_type = bw_array_get( $image_size, "mime", null );
		$jpeg = "image/jpeg" === $mime_type;
		if ( !$jpeg ) {	
		 	unlink( $file );
		}
		return( $jpeg );
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
						//gob();
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
