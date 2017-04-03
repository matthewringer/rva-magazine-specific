<?php
/*
Plugin Name: Example Dashboard Widget
Plugin URI: http://codex.wordpress.com/Example_Dashboard_Widget
Description: Demonstrates how to add a simple dashboard widget
Version: 1.0
Author: My Name
Author URI: http://example.com/
License: GPLv2 or later
*/
add_action('wp_dashboard_setup', array('My_Dashboard_Widget','init') );

class My_Dashboard_Widget {

    /**
     * The id of this widget.
     */
    const wid = 'my_widget_example';

    /**
     * Hook to wp_dashboard_setup to add the widget.
     */
    public static function init() {

        //Register the widget...
        wp_add_dashboard_widget(
            self::wid,                                  //A unique slug/ID
            __( 'Example Dashboard Widget', 'nouveau' ),//Visible name for the widget
            array('My_Dashboard_Widget','widget')      //Callback for the main widget content
            //array('My_Dashboard_Widget','config')       //Optional callback for widget configuration content
        );
    }

    /**
     * Load the widget code
     */
    public static function widget() {
        global $wpdb;

        $qry = "SELECT DISTINCT(taxonomy) FROM wp_term_taxonomy";
        //, count, description, parent, term_taxonomy_id, term_id
        $tax_info =  $wpdb->get_results( $qry );
        
        ?>
        <h2> Taxonomies </h1>
        <ul><?php
        foreach($tax_info as $row) {

            ?><li><?php echo $row->taxonomy ; ?> </li>
        <?php } ?>
        </ul><?php

        $qry = "SELECT DISTINCT(post_type) FROM wp_posts";
        //, count, description, parent, term_taxonomy_id, term_id
        $post_info =  $wpdb->get_results( $qry );
        ?>
        <h2> Post Types </h1>
        <ul><?php
        foreach($post_info as $row) {
            $qry = "SELECT COUNT(post_type) AS count FROM wp_posts WHERE post_type = '$row->post_type'";
            $count_query = $wpdb->get_results( $qry );
            ?><li><?php echo $row->post_type . ": ".$count_query[0]->count; ?> </li>
        <?php } ?>
        </ul><?php


        $qry = "SELECT tt.taxonomy, tt.term_taxonomy_id, t.name, t.slug 
                FROM wp_term_taxonomy tt 
                JOIN wp_terms t ON tt.term_id = t.term_id
                WHERE tt.taxonomy = 'category'";
        $category_info =  $wpdb->get_results( $qry );
        ?>
        <h2> Post Categories </h1>
        <ul><?php
        $post_count = 0;
        foreach($category_info as $row) {
            $qry = "SELECT $wpdb->term_taxonomy.count 
                    FROM $wpdb->terms, $wpdb->term_taxonomy 
                    WHERE $wpdb->terms.term_id=$wpdb->term_taxonomy.term_id 
                    AND $wpdb->term_taxonomy.taxonomy='category'
                    AND $wpdb->terms.slug='$row->slug'";
            $count_query = $wpdb->get_results( $qry );
            $post_count = $post_count + $count_query[0]->count;
            ?><li><?php echo $row->name . ": ".$count_query[0]->count; ?> </li>
        <?php }

        printf("<li><span>Posts Total: %u </span></li>", $post_count);
        
        $postsWithoutCategories = new WP_Query(array(
            'post_type'         => 'post',
            'category__not_in'  => get_terms('category', array(
                'fields'        => 'ids'
            )),
            'posts_per_page'	=> '-1'
	    ));
        printf("<li><span>Posts without Category: %u </span></li>", count($postsWithoutCategories->posts));


        // $post_with_thumbnail = new WP_Query([
        //         'post_type'  => 'post',
        //         'posts_per_page' => -1,
        //         'meta_query' => [
        //             [
        //             'key' => '_thumbnail_id',
        //             'compare' => 'EXISTS'
        //             ],
        //             ]
        //             ]);

        // printf("<li><span>Posts with Thumbnail: %u </span></li>", count($post_with_thumbnail->posts));
        //  $post_with_thumbnail = new WP_Query([
        //         'post_type'  => 'post',
        //         'posts_per_page' => 6,
        //         'meta_query' => [
        //             [
        //             'key' => '_thumbnail_id',
        //             'compare' => 'EXISTS'
        //             ],
        //             ]
        //             ]);
        
        // while ( $post_with_thumbnail->have_posts() ) : $post = $post_with_thumbnail->the_post(); 
        //     vprintf('<li><a href="%s"> %s </a></li>', [ get_permalink( $post->ID ), get_the_title() ] );
            
        //     $post_attachment_metadata = wp_get_attachment_metadata( $post_id );
        //     print("<li><span>--$post_attachment_metadata</span></li>");

        // endwhile;


        // $post_without_thumbnail = new WP_Query(array(
        //     'post_type'  => 'post',
        //     'posts_per_page' => -1,
        //     'meta_query' => array(
        //         array(
        //         'key' => '_thumbnail_id',
        //         'compare' => 'NOT EXISTS'
        //         ),
        //     )));
        // printf("<li><span>Posts without Thumbnail: %u </span></li>", count($post_without_thumbnail->posts));

        // $post_without_thumbnail = new WP_Query(array(
        //     'post_type'  => 'post',
        //     'posts_per_page' => 10,
        //     'meta_query' => array(
        //         array(
        //         'key' => '_thumbnail_id',
        //         'compare' => 'NOT EXISTS'
        //         ),
        //     )));
        // printf("<li><span>Posts without Thumbnail: %u </span></li>", count($post_without_thumbnail->posts));
        // while ( $post_without_thumbnail->have_posts() ) : $post = $post_without_thumbnail->the_post(); 
        //     vprintf('<li><a href="%s"> %s </a></li>', [ get_permalink( $post->ID ), get_the_title() ] );
        // endwhile;
        ?>
        </ul><?php
    }

}