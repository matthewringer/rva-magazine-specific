<?php

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Shortcode to display a short list of featured posts
 */
function featured_posts($atts) {
	wp_reset_postdata();

	global $wpdb;
	global $post;
	extract( shortcode_atts( [ 
		'ID' => get_the_ID(),
		'posts_per_page'    => 3,
		'title' => 'MORE FROM RVA'
	], $atts) );

	$category 		= get_the_category();
	$category_ID 	= $category[0]->term_id;

	ob_start();

		if(isset($title)) echo'<h1 class="margin-bottom">'.$title.'</h1>';
		
		$exclude = [ $ID ];
		$loop = new WP_Query([
			'posts_per_page'    => $posts_per_page,
			'post__not_in' 		=> $exclude,
			'meta_key' => 'rva_post_views_count',
			'orderby' => 'meta_value_num',
			'order' => 'DESC',
			'date_query' => [
				[
					'column' => 'post_date_gmt',
					'after' => '1 month ago',
				],
			]
		]);
		while( $loop->have_posts() ) {
			$loop->the_post();
			array_push($exclude, $post->ID);
			echo do_shortcode('[rva_post_thumbnail class=" entry-thumbnail "]');
		}

		$loop = new WP_Query([
			'posts_per_page'    => $posts_per_page,
			'post__not_in' 		=> $exclude,
			'cat'               => $category_ID,
		]);

		while( $loop->have_posts() ) {
			$loop->the_post();
			echo do_shortcode('[rva_post_thumbnail class=" entry-thumbnail "]');
		}

	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode('rva_post_links', 'featured_posts');