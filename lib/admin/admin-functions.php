<?php

/**
 * Add Photographer Name and URL fields to media uploader
 * Creates DB fields: rva_photographer_name, and rva_photographer_url
 * @param $form_fields array, fields to include in attachment form
 * @param $post object, attachment record in database
 * @return $form_fields, modified form fields
 */
function rva_attachment_field_credit( $form_fields, $post ) {

	$form_fields['rva-photographer-name'] = array(
		'label' => 'Photo Credit',
		'input' => 'text',
		'value' => get_post_meta( $post->ID, 'rva_photographer_name', true ),
		'helps' => 'If provided, photo credit will be displayed',
	);

	$form_fields['rva-photographer-url'] = array(
		'label' => 'Photographer URL',
		'input' => 'text',
		'value' => get_post_meta( $post->ID, 'rva_photographer_url', true ),
		'helps' => '',
	);

	return $form_fields;

} add_filter( 'attachment_fields_to_edit', 'rva_attachment_field_credit', 10, 2 );

/**
 * Save values of Photographer Name and URL in media uploader
 *
 * @param $post array, the post data for database
 * @param $attachment array, attachment fields from $_POST form
 * @return $post array, modified post data
 */
function rva_attachment_field_credit_save( $post, $attachment ) {

	if( isset( $attachment['rva-photographer-name'] ) ) {
		update_post_meta( $post['ID'], 'rva_photographer_name', $attachment['rva-photographer-name'] );
	}

	if( isset( $attachment['rva-photographer-url'] ) ) {
		update_post_meta( $post['ID'], 'rva_photographer_url', esc_url( $attachment['rva-photographer-url'] ) );
	}

	return $post;

} add_filter( 'attachment_fields_to_save', 'rva_attachment_field_credit_save', 10, 2 );


/**
 * Restrict Post Category to a single term. TODO: Blog post...
 */
function single_term_taxonomies() {

	$taxes = get_taxonomies();
	
	foreach ( $taxes as $tax ) {
		if ( is_taxonomy_hierarchical( $tax ) ) {
			$custom_tax_mb = new Taxonomy_Single_Term( $tax, array(), 'select' );
			$custom_tax_mb ->set( 'force_selection', true );
			$custom_tax_mb->set( 'allow_new_terms', true );
		}
	}

} add_action( 'admin_init', 'single_term_taxonomies');

/**
 *	Write to the debug log.
 */
if ( ! function_exists('write_log')) {

   function write_log ( $log )  {
	  if ( is_array( $log ) || is_object( $log )) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}