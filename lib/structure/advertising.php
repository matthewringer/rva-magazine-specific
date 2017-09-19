<?php

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function get_ad_unit_ID_by_code($code) {
	global $dfp_ad_units;
	if(!isset($dfp_ad_units)) {
		$dfp_ad_units = get_posts([
			'post_type' => 'dfp_ads',
			'post_status' => 'publish',
			'numberposts' => -1
			]);
	}
	foreach($dfp_ad_units as $p) {
		$meta = get_post_meta($p->ID);
		if($meta['dfp_ad_code'][0] == $code) { return $p->ID; }
	}
	return null;
}

function get_ad_unit_ID_by_title($title) {
	
	global $dfp_ad_units;

	if(!isset($dfp_ad_units)) {
		$dfp_ad_units = get_posts([
			'post_type' => 'dfp_ads',
			'post_status' => 'publish',
			'numberposts' => -1
			]);
	}

	foreach($dfp_ad_units as $p) {
		if($p->post_title == $title) { return $p->ID; }
	}
	return null;
}

/**
 * Creates shortcode for rva_ad, which prints a google dfp ad_unit
 *
 * @since  0.0.1
 *
 * @param $atts array
 *
 * @return mixed Returns HTML data for the position
 */
function rva_ad_shortcode($atts) {
	if(!array_key_exists('code', $atts)) return '';
	$code = $atts['code'];
	ob_start();
	?>
	<div class="<?php echo $atts['class']; ?> ">
	<?php
	$ad_unit_id = null;
	// For ad_units on category or archive pages
	if(is_archive() && is_category()) {
		$queried_object = get_queried_object();
		$rva_post_category = $queried_object->slug;
		// Check to see if the category is overriden
		if ( get_option(RVA_CATEGORY_FIELD_PREPEND_AD_UNIT . '_' . $rva_post_category) ) {
			$ad_unit_id = get_ad_unit_ID_by_code($code.'_'.$rva_post_category);
		}
	} else { // For ad units on all other post types
		foreach ( explode('/', get_query_var('category_name')) as $segment )
		{
			if( 1 == get_option(RVA_CATEGORY_FIELD_PREPEND_AD_UNIT . '_' . $segment) ) {
				$ad_unit_id = get_ad_unit_ID_by_code($code.'_'.$segment);
			}
		}
	}
	//If there was not ad_unit for the override get the default
	if ( !isset($ad_unit_id) ) {
		$ad_unit_id = get_ad_unit_ID_by_code($code);
	}
	echo do_shortcode('[dfp_ads id="'.$ad_unit_id.'"]');
	?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'rva_ad', 'rva_ad_shortcode' );

/**
 * Short code for GoogleAdSense Placement
 */
function rva_AdSense_shortcode($atts) {
	extract( shortcode_atts( [ 'width' => '728px', 'height' => '90px', 'data_ad_client'=>'ca-pub-5586294093687760', 'data_ad_slot'=>'4032495938' ], $atts) );
	ob_start();
	?>
		<script async src="//"></script>
		<!-- RVA MAG ADSENSE -->
		<ins class="adsbygoogle"
			style="display:inline-block;width:<?php echo $width; ?>;height:<?php echo $height; ?>;"
			data-ad-client="<?php echo $data_ad_client; ?>"
			data-ad-slot="<?php echo $data_ad_slot; ?>"></ins>
		<script>
		(adsbygoogle = window.adsbygoogle || []).push({});
		</script>
	<?php
	return ob_get_clean();
}
add_shortcode( 'rva_AdSense', 'rva_AdSense_shortcode' );

/**
 * AdSense header
 */
add_action('wp_head', function(){
    ?>
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
    (adsbygoogle = window.adsbygoogle || []).push({
        google_ad_client: "ca-pub-5586294093687760",
        enable_page_level_ads: true
    });
    </script>
    <?php
});

