<?php


/**
 * 
 */
function get_current_page_url() {
	$pageURL = 'http';
	if( isset($_SERVER["HTTPS"]) ) {
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;

} add_shortcode ('geturl', 'get_current_page_url');

/** 
 * 
 */
 function show_php_ini() {
			$inipath = php_ini_loaded_file();
			if ($inipath) {
				echo 'Loaded php.ini: ' . $inipath;
			} else {
				echo 'A php.ini file is not loaded';
			}
 }

/**
 * 
 * Place "rva_set_post_views(get_the_ID());" in post or page that views should be tracked.
 */
function rva_set_post_views($postID) {
	$count_key = 'rva_post_views_count';
	$count = get_post_meta($postID, $count_key, true);
	if($count==''){
		$count = 0;
		delete_post_meta($postID, $count_key);
		add_post_meta($postID, $count_key, '0');
	}else{
		$count++;
		update_post_meta($postID, $count_key, $count);
	}
}
//To keep the count accurate, lets get rid of prefetching
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);


/** 
 *
 *
 */
 function rva_get_post_views($postID) {

	$count_key = 'rva_post_views_count';
	$count = get_post_meta($postID, $count_key, true);
	if($count==''){
		delete_post_meta($postID, $count_key);
		add_post_meta($postID, $count_key, '0');
		return "0 View";
	}

	return $count.' Views';

}

/**
 * Returns query params for popular posts list
 * @param	string	: posts_per_page
 * @param	string	: date_after
 * @param	array	: authors
 * @return	array	
 */
function rva_popular_posts_query( $posts_per_page, $date_after = "", $authors=null ) {
	
	$query = [
		'posts_per_page' => $posts_per_page,
		'meta_key' => 'rva_post_views_count',
		'orderby' => 'meta_value_num',
		'order' => 'DESC',
	];

	if ( !empty( $authors ) && $authors[0] != '' ) {
		$query['author__in'] = $authors; 
	}

	if ( !empty( $date_after ) ) {
		$query['date_query'] = [[ 'after' => $date_after ]]; 
	}

	return $query;
}


/**
 * ShortCode Popular Posts
 */
 function rva_popular_posts($atts) {

	extract( shortcode_atts( [ 'authors' => null, 'date_after' => '1 week ago', 'posts_per_page' => 5 ], $atts) );
	$args = rva_popular_posts_query( $posts_per_page, $date_after, explode(", ",$authors));
	$popular_posts = new WP_Query($args);

	ob_start();

	//echo var_dump($authors);
	
	?>
	<div class="rva-gutter-box rva-popular-posts">
		<h2>MOST POPULAR<h2>
		<table class="decimal">
		<?php $i = 0; while ( $popular_posts->have_posts() ) : $popular_posts->the_post(); $i++; ?>
		<tr>
		<td><?php echo $i; ?></td><td> <a href="<?php echo get_the_permalink();?>" > <?php echo the_title(); ?> </a> </td>
		</tr>
		<?php endwhile; ?>
		</table>
	</div>
	<?php
	
	wp_reset_postdata();
	$content = ob_get_clean();
	return $content;

 } add_shortcode('rva_popular_posts','rva_popular_posts');


 /**
  * 
  *
  */
function rva_photo_credit() {

	ob_start();

	$photog_name = get_post_meta( get_post_thumbnail_id(), 'rva_photographer_name', true );
	$photog_url = get_post_meta( get_post_thumbnail_id(), 'rva_photographer_url', true );
	
	if($photog_name == '' || !$photog_name): ?>
	<?php elseif($photog_url != '' ): ?>
		<span class="rva-photo-credit"> <a href="<?php echo $photog_url; ?> " <?php echo $photog_name; ?> </a></span> 
	<?php else: ?>
		<span class="rva-photo-credit"> <?php echo $photog_name; ?> </span> 
	<?php endif;

	return ob_get_clean();
} add_shortcode('rva_photo_credit', 'rva_photo_credit');