<?php // (C) Copyright Bobbing Wide 2016

/**
 * @package oik-mshot
 * 
 * Test the functions in admin/class-oik-mshot2.php
 */
class Tests_class_oik_mshot2 extends BW_UnitTestCase {

	function setUp() {
		parent::setUp();
		oik_require( "admin/class-oik-mshot2.php", "oik-mshot" );
	}
	
	/**
	 * Unit test for fetch_mshot
	 * 
	 */
	function test_fetch_mshot() {
		$post_id = self::factory()->post->create( array( 'post_title' => 'Test mshot2 for example.com' ) ); 
		bw_trace2( $post_id, "post_id" );
    $this->assertNotEquals( 0, $post_id );
		$mshot = new OIK_mshot2( $post_id, "_mshot_test" );
		//$_POST[ "_mshot_test" ] = "http://qw/bw/" . time(); 
		$_POST[ "_mshot_test" ] = "http://example.com/" . time();
		$mshot->get_post_meta();
		$this->assertEquals( 0, $mshot->current_id );
		$mshot->get_url();
		$mshot->fetch_mshot();
		$this->assertNotEquals( 0, $mshot->id );
	}
		

}
