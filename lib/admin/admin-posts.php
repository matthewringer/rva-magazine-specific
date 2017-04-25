<?php

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include 'Input.php';
use RVAMag\Admin\Input as Input;

// Feild metadata constants
define( 'RVA_POST_FIELDS', 'rva_post_'); //rva_post_featured_post
define( 'RVA_POST_FIELDS_FEATURED_POST', RVA_POST_FIELDS.'featured_post');
define( 'RVA_NONCE', 'rvamag_nonce');

$postmeta_featured_post = RVA_POST_FIELDS_FEATURED_POST;

/**
 * Register a new meta box to the post or page edit screen to allow the user to add a Tagline.
 */
function rva_add_post_meta_boxes() {

    add_meta_box('rva_post_tagline_box', __( 'Post Tagline' ), 'rva_post_tagline_box', ['post'], 'normal', 'high');

	add_meta_box('rva_post_event_box', __( 'Event Info' ), 'rva_post_event_box', ['post'], 'normal', 'high');

    add_meta_box('rva_featured_post_box', __( 'Featured Post' ), 'rva_featured_post_box', ['post'], 'side', 'high');

	add_meta_box('rva_post_video_runtime_box', __( 'Video Runtime' ), 'rva_post_video_runtime_box', ['post'], 'side', 'high');

} add_action( 'add_meta_boxes', 'rva_add_post_meta_boxes' );

/**
 * Get the featured post field
 * @since 0.0.1
 *
 * @return Input
 */
function get_post_featured_field() {
	$input = new Input(
		[
			'id'	=> RVA_POST_FIELDS_FEATURED_POST,
			'type'  => 'checkbox',
			'name'  => RVA_POST_FIELDS_FEATURED_POST,
			'label' => 'Featured Post',
			'value' => '',
		]
	);

	return $input;
}

/**
 * Callback for in-post Scripts meta box.
 *
 * @since 2.0.0
 */
function rva_featured_post_box() {

	$post_id = get_the_ID();
	$input = get_post_featured_field();

	$input->value = get_post_meta( $post_id, $input->name, true );
	$input->create_input();
	wp_nonce_field( basename( __FILE__ ), RVA_NONCE );

}

/**
 * Save the featurebox settings when we save a post or page.
 *
 *
 * @param int      $post_id Post ID.
 * @param stdClass $post    Post object.
 * @return null
 */
function rva_post_featured_box_save( $post_id, $post ) {

	$input = get_post_featured_field();

	// Exits script depending on save status
	if (
		wp_is_post_autosave( $post_id )
		|| wp_is_post_revision( $post_id )
		|| ! Input::verify_nonce( RVA_NONCE, __FILE__ ) 
		//|| ! current_user_can( $post_type->cap->edit_post, $post_id )
	) {

		return;
	}
	
	delete_post_meta( $post_id, RVA_POST_FIELDS_FEATURED_POST );
	Input::update_meta( $post_id, $input );

} add_action( 'save_post', 'rva_post_featured_box_save', 1, 2 );


/**
 * Get the post tagline field
 *
 * @since 0.0.1
 *
 * @return Input
 */
function get_post_tagline_field() {

	$input = new Input(
		[
			'id'	=> 'rva_post_tagline',
			'type'  => 'text',
			'name'  => 'rva_post_tagline',
			'label' => 'Tagline',
			'value' => '',
		]
	);

	return $input;
}

/**
 * Callback for in-post Scripts meta box.
 *
 * @since 0.0.1
 */
function rva_post_tagline_box() {

	$post_id = get_the_ID();
	$input = $input = get_post_tagline_field();;

	$input->value = get_post_meta( $post_id, $input->name, true );
	$input->create_input();
	wp_nonce_field( basename( __FILE__ ), RVA_NONCE );
}

/**
 * Save the Tagline settings when we save a post or page.
 *
 * @since 0.0.1
 *
 * @param int      $post_id Post ID.
 * @param stdClass $post    Post object.
 * @return null
 */
function rva_post_tagline_save( $post_id, $post ) {

	$input = get_post_tagline_field();

	// Exits script depending on save status
	if (
		wp_is_post_autosave( $post_id ) ||
		wp_is_post_revision( $post_id ) ||
		! Input::verify_nonce( RVA_NONCE, __FILE__ ) //||
		//! current_user_can( $post_type->cap->edit_post, $post_id )
	) {

		return;
	}
	Input::update_meta( $post_id, $input );

} add_action( 'save_post', 'rva_post_tagline_save', 1, 2 );


/**
 * Get the post event fields
 *
 * @since 0.0.1
 *
 * @return array Input
 */
function get_post_event_fields() {
	// Date Time
	$fields[] = new Input(
		[
			'id'	=> 'rva_post_event_datetime',
			'type'  => 'datetime',
			'name'  => 'rva_post_event_datetime',
			'label' => 'Date Time',
			'value' => '',
		]
	);
	// Title
	$fields[] = new Input(
		[
			'id'	=> 'rva_post_event_title',
			'type'  => 'text',
			'name'  => 'rva_post_event_title',
			'label' => 'Title',
			'value' => '',
		]
	);
	// Description
	$fields[] = new Input(
		[
			'id'	=> 'rva_post_event_description',
			'type'  => 'textarea',
			'name'  => 'rva_post_event_description',
			'label' => 'Description',
			'value' => '',
		]
	);
	// Venue
	$fields[] = new Input(
		[
			'id'	=> 'rva_post_event_venue',
			'type'  => 'text',
			'name'  => 'rva_post_event_venue',
			'label' => 'Venue',
			'value' => '',
		]
	);
	// Price
	$fields[] = new Input(
		[
			'id'	=> 'rva_post_event_price',
			'type'  => 'text',
			'name'  => 'rva_post_event_price',
			'label' => 'Price',
			'value' => '',
		]
	);
	// Tickets Link
	$fields[] = new Input(
		[
			'id'	=> 'rva_post_event_tickets',
			'type'  => 'text',
			'name'  => 'rva_post_event_tickets',
			'label' => 'Tickets Link',
			'value' => '',
		]
	);
	// Editor's Pick
	$fields[] = new Input(
		[
			'id'	=> 'rva_post_event_editorspick',
			'type'  => 'checkbox',
			'name'  => 'rva_post_event_editorspick',
			'label' => 'Editor\'s Pick',
			'value' => '',
		]
	);
	// Must See
	$fields[] = new Input(
		[
			'id'	=> 'rva_post_event_mustsee',
			'type'  => 'checkbox',
			'name'  => 'rva_post_event_mustsee',
			'label' => 'Must See',
			'value' => '',
		]
	);
	return $fields;
}

/**
 * Callback for in-post Scripts meta box.
 *
 * @since 0.0.1
 */
function rva_post_event_box() {

	$post_id = get_the_ID();
	$fields = get_post_event_fields();

	echo '<table>';
	foreach( $fields as $input ) {
		$input->value = get_post_meta( $post_id, $input->name, true );
		$input->create_input();
	}
	echo '</table>';

	wp_nonce_field( basename( __FILE__ ), RVA_NONCE );
}

/**
 * Save the Event settings when we save a post.
 *
 * @since 0.0.1
 *
 * @param int      $post_id Post ID.
 * @param stdClass $post    Post object.
 * @return null
 */
function rva_post_event_save( $post_id, $post ) {
	//$input = get_post_event_fields();
	// Exits script depending on save status
	if (
		wp_is_post_autosave( $post_id ) ||
		wp_is_post_revision( $post_id ) ||
		! Input::verify_nonce( RVA_NONCE, __FILE__ ) //||
		//! current_user_can( $post_type->cap->edit_post, $post_id )
	) {

		return;
	}

	$fields = get_post_event_fields();
	foreach( $fields as $input ) {
		Input::update_meta( $post_id, $input );
	}

} add_action( 'save_post', 'rva_post_event_save', 1, 2 );

/**
 * Get the post video runtime field
 * @since 0.0.1
 *
 * @return Input
 */
function get_post_video_runtime_field() {
	$input = new Input(
		[
			'id'	=> 'rva_post_video_runtime',
			'type'  => 'text',
			'name'  => 'rva_post_video_runtime',
			'label' => 'Video Runtime',
			'value' => '',
		]
	);

	return $input;
}

/**
 * Callback for in-post Scripts meta box.
 *
 * @since 2.0.0
 */
function rva_post_video_runtime_box() {

	$post_id = get_the_ID();
	$input = get_post_video_runtime_field();

	$input->value = get_post_meta( $post_id, $input->name, true );
	$input->create_input();
	wp_nonce_field( basename( __FILE__ ), RVA_NONCE );

}

/**
 * Save the video runtime when we save a post or page.
 *
 *
 * @param int      $post_id Post ID.
 * @param stdClass $post    Post object.
 * @return null
 */
function rva_post_video_runtime_box_save( $post_id, $post ) {

	$input = get_post_video_runtime_field();

	// Exits script depending on save status
	if (
		wp_is_post_autosave( $post_id )
		|| wp_is_post_revision( $post_id )
		|| ! Input::verify_nonce( RVA_NONCE, __FILE__ ) 
		//|| ! current_user_can( $post_type->cap->edit_post, $post_id )
	) {

		return;
	}

	Input::update_meta( $post_id, $input );

} add_action( 'save_post', 'rva_post_video_runtime_box_save', 1, 2 );



