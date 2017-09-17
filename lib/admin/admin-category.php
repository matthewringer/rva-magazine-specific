<?php

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once 'Input.php';
use RVAMag\Admin\Input as Input;

define( 'RVA_CATEGORY_FIELDS', 'rva_cat_'); //rva_post_featured_post
define( 'RVA_CATEGORY_FIELD_PREPEND_AD_UNIT', RVA_POST_FIELDS.'prepend_ad_units');
define( 'RVA_NONCE', 'rvamag_nonce');

/**
 * Get the featured post field
 * @since 0.0.1
 *
 * @return Input
 */
function get_prepend_ads_checkbox($term) {

  $label = 'Override Ad_Units';
  $term_slug = $term->slug;
  $value = get_option(RVA_CATEGORY_FIELD_PREPEND_AD_UNIT . '_' . $term_slug); 

  ?>
  <tr class="form-field term-description-wrap">
    <th scope="row"><label for="<?php echo RVA_CATEGORY_FIELD_PREPEND_AD_UNIT; ?>"><?php echo $label; ?></label></th>
    <td>
      <input type="checkbox" name="<?php echo RVA_CATEGORY_FIELD_PREPEND_AD_UNIT; ?>" id="<?php echo RVA_CATEGORY_FIELD_PREPEND_AD_UNIT; ?>"<?php checked( $value, 1 ); ?> value="1" />
      <p class="description">This setting overrides the default ad_unit behavior.</p>
      <p class="description">When checked, the category <b>slug</b> will be appended to each ad_unit on the category archive page and all posts that fall under the category. If multiple ad_unit overrides are applied to a category, the override closest to the post will take effect.</p>
      <p class="description">To function there must be both a matching <a href="/wp-admin/edit.php?post_type=dfp_ads">Ad Positon</a> in wordpress and Ad Unit in DFP configured. </p>
      <p class="description"> format: ad_unit_name_category  </p>
    </td>
  </tr>
  <?php

}
add_action( 'category_edit_form_fields', 'get_prepend_ads_checkbox', 10, 2 );

function save_extra_taxonomy_fields($term_id){

  //collect all term related data for this new taxonomy
  $term_item = get_term($term_id,'category');
  $term_slug = $term_item->slug;
  
  //collect our custom fields
  $term_category_text = sanitize_text_field($_POST[RVA_CATEGORY_FIELD_PREPEND_AD_UNIT]);

  //save our custom fields as wp-options
  update_option(RVA_CATEGORY_FIELD_PREPEND_AD_UNIT . '_' . $term_slug, $term_category_text);

}
add_action ( 'edited_category', 'save_extra_taxonomy_fields');