<?php
/*
 *	Theme Name: RVA Magazine
 *	Description: A theme for RVA Magazine based on  Bones for Genesis 2.0
 *	Author: Matthew Ringer
 *	Author URI: https://unfork.me
 *	Template: genesis
 *	Template Version: 1.0.0
 *
 *	License: WTFPL
 *	License URI: http://www.wtfpl.net/txt/copying/
*/

define( 'RVA_SETTINGS_FIELD', 'rva-magazine-settings');

// genesis_get_option( 'someoptionname_custom_option', RVA_SETTINGS_FIELD );

/**
 *
 * @since 1.0.0
 *
 * This function stores the default RVA Magazine theme options. It can be filtered using rvamagazine_default_theme_options.
 *
 * @return array $options Array of options filtered by rva_default_theme_options.
 *
 */
function rva_default_theme_options() {
     $options = array(
        // Social Media Profile links
		'rva_socialmedia_account_links' => [
			'twitter_url'   => 'http://twitter.com/RVAMAG', //rva_socialmedia_twitter_url
			'facebook_url'  => 'https://www.facebook.com/RVAMAG/', //rva_socialmedia_facebook_url
			'instagram_url' => 'http://instagram.com/rvamag', //rva_socialmedia_instagram_url
			'tumblr_url'    => 'http://rvamag.tumblr.com/', //rva_socialmedia_tumblr_url
			'pintrest_url'  => 'http://pinterest.com/rvamag', //rva_socialmedia_pintrest_url
			'youtube_url'   => 'https://www.youtube.com/user/hellorva', //rva_socialmedia_youtube_url
			'snapchat_url'	=> 'https://www.snapchat.com/RVAMAG', //rva_socialmedia_snapchat_url
		],

        // Show Like Buttons
        'rva_facebook_like_btn' => 0,
		'rva_google_plus_btn'   => 0,
		'rva_twitter_tweet_btn' => 0,

        // Facebook Account info
        'rva_facebook_appid' => '1636311590010567',

        //Front Page | depricated....
        'rva_subscribe_email_image'     => '1',
        'rva_subscribe_magazine_image'  => '1',

     );
     return apply_filters('rva_default_theme_options', $options);
 }

/**
 * Sanitization class for all user inputs
 * Options: one_zero, no_html, safe_hrml, requires_unfiltered_html
 *
 * @since 1.0.0
 */
function rva_sanatize_inputs() {
    genesis_add_options_filter( 'no_html', RVA_SETTINGS_FIELD, array('rva_socialmedia_twitter_url', 'rva_socialmedia_facebook_url', 'rva_socialmedia_instagram_url', 'rva_socialmedia_tumblr_url', 'rva_socialmedia_pintrest_url', 'rva_socialmedia_youtube_url', 'rva_socialmedia_snapchat_url' ) );
}

/**
 * Enqueue any scripts needed for the RVA Settigns page.
 * @since 1.0.0
 */
function rva_settings_scripts() {
    wp_enqueue_script( 'common' );
	wp_enqueue_script( 'wp-lists' );
	wp_enqueue_script( 'postbox' );

	wp_enqueue_script( 'rva_admin_js', RVA_MAG_PLUGURL . '/assets/js/admin.js', array( 'jquery' ), '1.0', true );
    //wp_enqueue_script( 'rva_admin_js', get_stylesheet_directory_uri() . '/build/js/admin.js', array( 'jquery' ), '1.0', true );
}

/**
 * This function registers the settings for the RVA Magazine theme settings area. 
 * It also restores default options when the Reset button is selected.
 *
 * @since 1.0.0
 */
function rva_register_settings() {

	register_setting( RVA_SETTINGS_FIELD, RVA_SETTINGS_FIELD );
	add_option( RVA_SETTINGS_FIELD, rva_default_theme_options() );

	if ( genesis_get_option( 'reset', RVA_SETTINGS_FIELD ) ) {
		update_option( RVA_SETTINGS_FIELD, rva_default_theme_options() );
		genesis_admin_redirect( RVA_SETTINGS_FIELD, array( 'reset' => 'true' ) );
		exit;
	}

} add_action( 'admin_init', 'rva_register_settings' );

/**
 * This function displays admin notices when the user updates RVA Magazine theme settings.
 *
 * @since 1.0.0
 */
function rva_theme_settings_notice() {
	
	if ( ! isset( $_REQUEST['page'] ) || $_REQUEST['page'] != RVA_SETTINGS_FIELD ) {
		return;
	}
	if ( isset( $_REQUEST['reset'] ) && 'true' == $_REQUEST['reset'] ) {
		echo '<div id="message" class="updated settings-error notice is-dismissible"><p><strong>' . __( 'Settings reset.', 'genesis' ) . '</strong></p></div>';
	} elseif ( isset( $_REQUEST['settings-updated'] ) && 'true' == $_REQUEST['settings-updated'] ) {
		echo '<div id="message" class="updated settings-error notice is-dismissible"><p><strong>' . __( 'Settings saved.', 'genesis' ) . '</strong></p></div>';
	}

} add_action( 'admin_notices', 'rva_theme_settings_notice' );

/**
 * This function registers the RVA settings page and prepares the styles, scripts and metaboxes to be loaded.
 *
 * @since 1.0.0
 *
 * @global string $_rva_settings_pagehook The unique hookname for the settings page.
 */
function rva_theme_options() {
	
	global $_rva_settings_pagehook;
	
    $page_title =   'RVA Magazine Admin';
    $menu_title =   'RVA Mag Settings';
    $capability =   'manage_options';
    $menu_slug  =   RVA_SETTINGS_FIELD;
    $function   =   'rva_theme_options_page';
    $icon_url   =   null;
    $position   =   2;

    $_rva_settings_pagehook =  add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );

	//add_action( 'load-' . $_rva_settings_pagehook, 'rva_settings_styles' );
	add_action( 'load-' . $_rva_settings_pagehook, 'rva_settings_scripts' );
	add_action( 'load-' . $_rva_settings_pagehook, 'rva_settings_boxes' );

} add_action( 'admin_menu', 'rva_theme_options' );

/**
 * This function sets up the metaboxes to be populated by their respective callback functions.
 *
 * @since 1.0.0
 *
 * @global string $_ctsettings_settings_pagehook The unique hookname for the settings page.
 */
function rva_settings_boxes() {

	global $_rva_settings_pagehook;
	add_meta_box( 'rva-social-box', __( 'Social Sharing Settings', 'genesis' ), 'rva_social_metabox', $_rva_settings_pagehook, 'main' );
    add_meta_box( 'rva-subscription-box', __( 'Subscription Settings', 'genesis' ), 'rva_subscription_metabox', $_rva_settings_pagehook, 'main' );

	//TODO: remove 
    //add_meta_box( 'rva-ad-placement-box', __( 'DFP Placement Settings', 'genesis' ), 'rva_ad_placements_metabox', $_rva_settings_pagehook, 'main' );

}

/*
 * Ad Placements Metabox
 */
function rva_ad_placements_metabox() { 
    ?>
    <p>Leaderboard:<br />
	<input type="text" name="<?php echo RVA_SETTINGS_FIELD; ?>[rva_socialmedia_twitter_url]" value="<?php echo esc_attr( genesis_get_option('rva_socialmedia_twitter_url', RVA_SETTINGS_FIELD ) ); ?>" size="50" /> </p>

	<p>Ad placement:<br />
	<input type="text" name="<?php echo RVA_SETTINGS_FIELD; ?>[rva_socialmedia_facebook_url]" value="<?php echo esc_attr( genesis_get_option('rva_socialmedia_facebook_url', RVA_SETTINGS_FIELD ) ); ?>" size="50" /> </p>

    <p>Ad placement:<br />
	<input type="text" name="<?php echo RVA_SETTINGS_FIELD; ?>[rva_socialmedia_instagram_url]" value="<?php echo esc_attr( genesis_get_option('rva_socialmedia_instagram_url', RVA_SETTINGS_FIELD ) ); ?>" size="50" /> </p>

    <p>Ad placement:<br />
	<input type="text" name="<?php echo RVA_SETTINGS_FIELD; ?>[rva_socialmedia_tumblr_url]" value="<?php echo esc_attr( genesis_get_option('rva_socialmedia_tumblr_url', RVA_SETTINGS_FIELD ) ); ?>" size="50" /> </p>

    <p>Ad placement:<br />
	<input type="text" name="<?php echo RVA_SETTINGS_FIELD; ?>[rva_socialmedia_pintrest_url]" value="<?php echo esc_attr( genesis_get_option('rva_socialmedia_pintrest_url', RVA_SETTINGS_FIELD ) ); ?>" size="50" /> </p>
    <?php
}

/**
 * Callback function for the CT Settings Social Sharing metabox.
 *
 * @since 1.0.0
 */
function rva_social_metabox() { 
	?>
    <p>Twitter URL:<br />
	<input type="text" name="<?php echo RVA_SETTINGS_FIELD; ?>[rva_socialmedia_twitter_url]" value="<?php echo esc_attr( genesis_get_option('rva_socialmedia_twitter_url', RVA_SETTINGS_FIELD ) ); ?>" size="50" /> </p>

	<p>Facebook URL:<br />
	<input type="text" name="<?php echo RVA_SETTINGS_FIELD; ?>[rva_socialmedia_facebook_url]" value="<?php echo esc_attr( genesis_get_option('rva_socialmedia_facebook_url', RVA_SETTINGS_FIELD ) ); ?>" size="50" /> </p>

    <p>Facebook AppId:<br />
	<input type="text" name="<?php echo RVA_SETTINGS_FIELD; ?>[rva_facebook_appid]" value="<?php echo esc_attr( genesis_get_option('rva_facebook_appid', RVA_SETTINGS_FIELD ) ); ?>" size="50" /> </p>

    <p>Instagam URL:<br />
	<input type="text" name="<?php echo RVA_SETTINGS_FIELD; ?>[rva_socialmedia_instagram_url]" value="<?php echo esc_attr( genesis_get_option('rva_socialmedia_instagram_url', RVA_SETTINGS_FIELD ) ); ?>" size="50" /> </p>

    <p>Tumblr URL:<br />
	<input type="text" name="<?php echo RVA_SETTINGS_FIELD; ?>[rva_socialmedia_tumblr_url]" value="<?php echo esc_attr( genesis_get_option('rva_socialmedia_tumblr_url', RVA_SETTINGS_FIELD ) ); ?>" size="50" /> </p>

    <p>Pintrest URL:<br />
	<input type="text" name="<?php echo RVA_SETTINGS_FIELD; ?>[rva_socialmedia_pintrest_url]" value="<?php echo esc_attr( genesis_get_option('rva_socialmedia_pintrest_url', RVA_SETTINGS_FIELD ) ); ?>" size="50" /> </p>

	<p>YouTube URL:<br />
	<input type="text" name="<?php echo RVA_SETTINGS_FIELD; ?>[rva_socialmedia_youtube_url]" value="<?php echo esc_attr( genesis_get_option('rva_socialmedia_youtube_url', RVA_SETTINGS_FIELD ) ); ?>" size="50" /> </p>

	<p>SnapChat URL:<br />
	<input type="text" name="<?php echo RVA_SETTINGS_FIELD; ?>[rva_socialmedia_snapchat_url]" value="<?php echo esc_attr( genesis_get_option('rva_socialmedia_snapchat_url', RVA_SETTINGS_FIELD ) ); ?>" size="50" /> </p>

	<p><?php _e( 'Check any of the following if you want Facebook, Twitter or Google+ buttons for your posts.', 'genesis' ); ?></p>
	<table class="form-table ctsettings-social">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<input type="checkbox" name="<?php echo RVA_SETTINGS_FIELD; ?>[rva_facebook_like_btn]" id="<?php echo RVA_SETTINGS_FIELD; ?>[rva_facebook_like_btn]" value="1" <?php checked( 1, genesis_get_option( 'rva_facebook_like_btn', RVA_SETTINGS_FIELD ) ); ?> /> <label for="<?php echo RVA_SETTINGS_FIELD; ?>[rva_facebook_like_btn]"><?php _e( 'Include a Facebook Like button on your posts?', 'genesis' ); ?></label>
				</th>
			</tr>
			<tr valign="top">
				<th scope="row">
					<input type="checkbox" name="<?php echo RVA_SETTINGS_FIELD; ?>[rva_twitter_tweet_btn]" id="<?php echo RVA_SETTINGS_FIELD; ?>[rva_twitter_tweet_btn]" value="1" <?php checked( 1, genesis_get_option( 'rva_twitter_tweet_btn', RVA_SETTINGS_FIELD ) ); ?> /> <label for="<?php echo RVA_SETTINGS_FIELD; ?>[rva_twitter_tweet_btn]"><?php _e( 'Include a Twitter Tweet button on your posts?', 'genesis' ); ?></label>
				</th>
			</tr>
			<tr valign="top">
				<th scope="row">
					<input type="checkbox" name="<?php echo RVA_SETTINGS_FIELD; ?>[rva_google_plus_btn]" id="<?php echo RVA_SETTINGS_FIELD; ?>[rva_google_plus_btn]" value="1" <?php checked( 1, genesis_get_option( 'rva_google_plus_btn', RVA_SETTINGS_FIELD ) ); ?> /> <label for="<?php echo RVA_SETTINGS_FIELD; ?>[rva_google_plus_btn]"><?php _e( 'Include a Google Plus button on your posts?', 'genesis' ); ?></label>
				</th>
			</tr>
		</tbody>
	</table>
	<?php
}

/*
 * Subscription admin metabox
 */
function rva_subscription_metabox() { 

    print_media_selector( 'rva_subscribe_email_image' );

    print_media_selector( 'rva_subscribe_magazine_image' );
}

/*
 * Display a media selector in the admin section
 */
function print_media_selector( $setting_id ) {
    $image_id = genesis_get_option( $setting_id, RVA_SETTINGS_FIELD );
    wp_enqueue_media();
    ?>
        <div class='image-preview-wrapper'>
            <?php printf( 
                '<img id="image_preview_'.$setting_id.'" src="%s" height="100">', isset( $image_id) ? wp_get_attachment_url( esc_attr( $image_id ) ) : ''
            ); ?> 
        </div>
        <input id="btn_upload_<?php echo $setting_id; ?>" type="button" class="button" value="<?php _e( 'Select Image' ); ?>" />
        
        <?php printf( 
            '<input type="hidden" id="'.$setting_id.'" name="'.RVA_SETTINGS_FIELD.'['.$setting_id.']" value="%s" />',
            isset( $image_id) ? esc_attr( $image_id ) : ''
        ); ?>
        <script type='text/javascript'>
            jQuery(document).ready( function($){
                rva_admin.bind_media_picker('#btn_upload_<?php echo $setting_id ?>', '#image_preview_<?php echo $setting_id ?>', '#<?php echo $setting_id ?>');
            });
        </script>
    <?php
}

/**
 * This function sets the column layout to one for the RVA Magazine settings page.
 *
 * @since 1.0.0
 *
 * @param mixed $columns
 * @param mixed $screen
 * @return $columns
 */
function rva_settings_layout_columns( $columns, $screen ) {

	global $_rva_settings_pagehook;
	if ( $screen == $_rva_settings_pagehook ) {
		$columns[ $_rva_settings_pagehook ] = 1;
	}

	return $columns;

} add_filter( 'screen_layout_columns', 'rva_settings_layout_columns', 10, 2 );

/**
 * This function displays the content for the CT Settings settings page, builds the forms and outputs the metaboxes.
 *
 * @since 1.0.0
 *
 * @global $_ctsettings_settings_pagehook
 * @global $screen_layout_columns
 */
function rva_theme_options_page() {
	global $_rva_settings_pagehook, $screen_layout_columns;
	$width = "width: 99%;";
	$hide2 = $hide3 = " display: none;";
	?>
	<div id="rvasettings" class="wrap genesis-metaboxes">
		<form method="post" action="options.php">

			<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
			<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
			<?php settings_fields( RVA_SETTINGS_FIELD ); ?>

			<?php screen_icon( 'options-general' ); ?>
            <h1><?php _e( 'RVA Magazine - Theme Settings', 'genesis' ); ?></h1>
            <br>
            <br>
			<h2>
				
				<input type="submit" class="button-primary genesis-h2-button" value="<?php _e( 'Save Settings', 'genesis' ) ?>" />
				<input type="submit" class="button-highlighted genesis-h2-button" name="<?php echo RVA_SETTINGS_FIELD; ?>[reset]" value="<?php _e( 'Reset Settings', 'genesis' ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Are you sure you want to reset?', 'genesis' ) ); ?>');" />
			</h2>

			<div class="metabox-holder">
				<div class="postbox-container" style="<?php echo $width; ?>">
					<?php do_meta_boxes( $_rva_settings_pagehook, 'main', null ); ?>
				</div>
			</div>

		</form>
	</div>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			// close postboxes that should be closed
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			// postboxes setup
			postboxes.add_postbox_toggles('<?php echo $_rva_settings_pagehook; ?>');
		});
		//]]>
	</script>
	<?php
}