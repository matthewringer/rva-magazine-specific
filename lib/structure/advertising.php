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

