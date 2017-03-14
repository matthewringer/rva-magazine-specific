<?php

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'add_meta_boxes_post', 'rva_add_inpost_tagline_box' );
/**
 * Register a new meta box to the post or page edit screen to allow the user to add a Tagline.
 */
function rva_add_inpost_tagline_box() {

    add_meta_box('rva_inpost_tagline_box', __( 'Post Tagline' ), 'rva_inpost_tagline_box', 'post', 'normal', 'high');

}

/**
 * Callback for in-post Scripts meta box.
 *
 * @since 2.0.0
 */
function rva_inpost_tagline_box() {

    $post_id = get_the_ID();
    $field = 'rva_post_tagline';
    $custom_field = get_post_meta( $post_id, $field, true );

	wp_nonce_field( 'rva_inpost_tagline_save', 'rva_inpost_tagline_nonce' );
	?>

    <label class="screen-reader-text" for="post_tagline"><?php _e('Tagline') ?></label><input name="post_tagline" type="text" size="50" id="post_tagline" value="<?php echo esc_attr( $custom_field );  ?>" />

	<?php

}

add_action( 'save_post', 'genesis_inpost_tagline_save', 1, 2 );
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

}