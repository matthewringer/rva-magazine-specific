<?php

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Feild metadata constants
define( 'RVA_POST_FIELDS', 'rva_post_'); //rva_post_featured_post
define( 'RVA_POST_FIELDS_FEATURED_POST', RVA_POST_FIELDS.'featured_post');

$postmeta_featured_post = RVA_POST_FIELDS_FEATURED_POST;

/**
 * Register a new meta box to the post or page edit screen to allow the user to add a Tagline.
 */
function rva_add_post_meta_boxes() {

    add_meta_box('rva_inpost_tagline_box', __( 'Post Tagline' ), 'rva_inpost_tagline_box', ['post'], 'normal', 'high');

    add_meta_box('rva_featured_post_box', __( 'Featured Post' ), 'rva_featured_post_box', ['post'], 'side', 'high');

} add_action( 'add_meta_boxes', 'rva_add_post_meta_boxes' );

/**
 * Callback for in-post Scripts meta box.
 *
 * @since 2.0.0
 */
function rva_featured_post_box() {
    $custom_field = get_post_meta( get_the_ID(), RVA_POST_FIELDS_FEATURED_POST, true );
	  wp_nonce_field( 'rva_post_featured', 'rvamag_nonce' );
	?>
    <label class="screen-reader-text" for="post_featured"><?php _e('Featured Post') ?></label>
    <input name="post_featured" type="checkbox" size="50" id="post_featured" value='1' <?php checked( '1', $custom_field ); ?> />
    <span>Feature on the front page.</span>
	<?php

}

/**
 * Save the Tagline settings when we save a post or page.
 *
 *
 * @param int      $post_id Post ID.
 * @param stdClass $post    Post object.
 * @return null
 */
function rva_post_featured_box_save( $post_id, $post ) {

  // if ( !isset( $_POST['post_featured'] ) || !wp_verify_nonce( $_POST['rva_post_featured'], 'rvamag_nonce' ) )
  //   return $post_id;

  // $post_type = get_post_type_object( $post->post_type );
  // if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ) // Check if the current user has permission to edit the post.
  //   return $post_id;

  $meta_value = ( isset( $_POST['post_featured'] ) ? sanitize_text_field( $_POST['post_featured'] ) : '0' );
  if($meta_value == 1) {
    //global $wpdb;
    //$wpdb->delete ( $wpdb->postmeta , array('meta_key' => RVA_POST_FIELDS_FEATURED_POST);
    update_post_meta( $post_id, RVA_POST_FIELDS_FEATURED_POST, $meta_value );
  
  } else {
  
    delete_post_meta( $post_id, RVA_POST_FIELDS_FEATURED_POST );
  
  }

      

} add_action( 'save_post', 'rva_post_featured_box_save', 1, 2 );


/**
 * Callback for in-post Scripts meta box.
 *
 * @since 2.0.0
 */
function rva_inpost_tagline_box() {

    $post_id = get_the_ID();
    $field = 'rva_post_tagline';
    $custom_field = get_post_meta( $post_id, $field, true );
	  wp_nonce_field( 'rva_post_tagline', 'rvamag_nonce' );
	?>

    <label class="screen-reader-text" for="post_tagline"><?php _e('Tagline') ?></label><input name="post_tagline" type="text" size="50" id="post_tagline" value="<?php echo esc_attr( $custom_field );  ?>" />

	<?php

}

/**
 * Save the Tagline settings when we save a post or page.
 *
 *
 * @param int      $post_id Post ID.
 * @param stdClass $post    Post object.
 * @return null
 */
function genesis_inpost_tagline_save( $post_id, $post ) {


    /* Verify the nonce before proceeding. */
  //if (check_admin_referer( 'rva_post_tagline', 'rvamag_nonce' ) )  // Security check
  if ( !isset( $_POST['rva_inpost_tagline_nonce'] ) || !wp_verify_nonce( $_POST['rva_inpost_tagline_nonce'], 'rva_inpost_tagline_save' ) )
    return $post_id;

  /* Get the post type object. */
  $post_type = get_post_type_object( $post->post_type );

  /* Check if the current user has permission to edit the post. */
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;

  /* Get the posted data and sanitize it for use as an HTML class. */
  $new_meta_value = ( isset( $_POST['post_tagline'] ) ? sanitize_text_field( $_POST['post_tagline'] ) : '' );

  /* Get the meta key. */
  $meta_key = 'rva_post_tagline';

  /* Get the meta value of the custom field key. */
  $meta_value = get_post_meta( $post_id, $meta_key, true );

  /* If a new meta value was added and there was no previous value, add it. */
  if ( $new_meta_value && '' == $meta_value )
    add_post_meta( $post_id, $meta_key, $new_meta_value, true );

  /* If the new meta value does not match the old value, update it. */
  elseif ( $new_meta_value && $new_meta_value != $meta_value )
    update_post_meta( $post_id, $meta_key, $new_meta_value );

  /* If there is no new meta value but an old value exists, delete it. */
  elseif ( '' == $new_meta_value && $meta_value )
    delete_post_meta( $post_id, $meta_key, $meta_value );

} add_action( 'save_post', 'genesis_inpost_tagline_save', 1, 2 );