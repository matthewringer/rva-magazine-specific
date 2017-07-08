<?php

//TODO: Move to plugin

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
	ob_start();
	?>
	<div class="<?php echo $atts['class']; ?> ">
		<?php
		if(array_key_exists ( 'name', $atts)) { 
			echo do_shortcode('[dfp_ads name="'.$atts['name'].'"]'); 
		} ?>
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

