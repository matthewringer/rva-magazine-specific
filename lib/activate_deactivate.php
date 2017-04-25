<?php
require_once(ABSPATH . 'wp-admin/includes/taxonomy.php');
if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


//add_action('switch_theme', 'rva_deactivation');
function rva_deactivation(){
    $socialMediaOptions=array(
    );
	//delete_option('rva_socialmedia_options',  serialize($socialMediaOptions));

	// Delete all menus
	$primary_menu_name = 'RVA Primary Menu';
	wp_delete_nav_menu($primary_menu_name);
	
	$secondary_menu_name = 'RVA Secondary Menu';
	wp_delete_nav_menu($secondary_menu_name);
}

//add_action('after_switch_theme', 'rva_activation');
function rva_activation(){
    rva_create_primary_memu();
	rva_create_secondary_memu();
	rva_create_pages();

}

/**
 * Configure Theme menus
 */
function rva_create_primary_memu() {

	$primary_menu_items = [
		'news' => 'NEWS',
		'music' => 'MUSIC',
		'art' => 'ART',
		'politics' => 'POLITICS',
		'eatdrink' => [ 'name' => 'EAT DRINK', 'children' => [
			'beermadness' => 'BEER Madness',
			'readerspoll' => 'Readers Poll',
			'rvaontap' => 'RVA ON TAP',
			'goodeats' => 'GOOD EATS'
		] ],
		'photo' => 'PHOTO',
		'watch' => 'WATCH',
		'events' => 'EVENTS',
		'magazine' => 'MAGAZINE'
	];

	//create ctegories sub categories recursivly
	try {
		rva_create_categories_and_childen($primary_menu_items);
	} catch(Exception $ex){
		write_log($ex->message);
	}

    //give your menu a name
    $menu_name = 'RVA Primary Menu';
	$menu = wp_get_nav_menu_object( $menu_name );

	if(!$menu) {

		$menu_id = wp_create_nav_menu($menu_name);
		$menu = get_term_by( 'id', $menu_id , 'nav_menu' );
		write_log('Menu Created name: '. $menu->name);

		// Insert top level menu items.
		foreach ( $primary_menu_items as $key => $value ) {
			
			$name = $value;
			if(is_array($value)) {

				$name = $value['name'];
				$children = $value['children'];

			}

			wp_update_nav_menu_item($menu->term_id, 0, array(
			'menu-item-title' =>  __($name),
			//'menu-item-classes' => 'home',
			'menu-item-url' => home_url( '/'.$key ), 
			'menu-item-status' => 'publish'));

		}

		//then you set the wanted theme  location
		$locations = get_theme_mod('nav_menu_locations');
		$locations['primary'] = $menu->term_id;
		set_theme_mod( 'nav_menu_locations', $locations );

	}

} add_action('after_switch_theme', 'rva_create_primary_memu');

/**
 * Creates Categories and subcategiries recursivly
 *
 * @param array $categories An array of categories to add or update
 * @param int $patent The id of the parent category (optional)
 *
 * @return int
 */
function rva_create_categories_and_childen( $categories, $parent = null ) {

	foreach ( $categories as $key => $value ) {

		$id = null;
		$children = null;
		$name = $value;

		write_log([$key, $id, $children, $value]);

		if(is_array($value)) {

			$name = $value['name'];
			// or add a page...
			$children = $value['children'];
			write_log('Should have children:'. $name);

		}

		$cat = get_category_by_slug( $key );
		if( $cat == false ) {

			$cat = [ 'category_nicename' => $key, 'cat_name' => $value ];

			if( isset( $parent ) ) { $cat['category_parent'] = $parent; }

			$id = wp_insert_category( $cat );

		} else {

			$id = $cat->term_id;

		}

		if(isset($id) && isset($children)) {

			write_log('Add children for category:'. $id);
			rva_create_categories_and_childen($children, $id);

		}

	}

}

/**
 * Create secondary menus
 */
function rva_create_secondary_memu() {

	$secondary_menu_items = [
		'about' => 'About', //page
		'contact' => 'Contact', //page
		'subscribe' => 'Subscribe', //Link to bigwrk
		'contributors' => 'Contributors', //archive
		'sponsors' => 'Sponsors', //page
		//'newsletters' => 'Newsletters', //js function...
		'advertising' => 'Advertising', //page

	];

	//TODO: Verify all linked pages exist. 

	//give your menu a name
	$menu_name = 'RVA Secondary Menu';
	$menu = wp_get_nav_menu_object( $menu_name );
	if(!$menu) {

		$menu_id = wp_create_nav_menu($menu_name);
		$menu = get_term_by( 'id', $menu_id , 'nav_menu' );
		write_log('Menu Created name: '. $menu->name);

		// Insert top level menu items.
		foreach ( $secondary_menu_items as $key => $value ) {

			wp_update_nav_menu_item($menu->term_id, 0, array(
			'menu-item-title' =>  __($value),
			'menu-item-url' => home_url( '/'.$key ), 
			'menu-item-status' => 'publish'));

		}
		//then you set the wanted theme  location
		$locations = get_theme_mod('nav_menu_locations');
		$locations['secondary'] = $menu->term_id;
		set_theme_mod( 'nav_menu_locations', $locations );

	}

} add_action('after_switch_theme', 'rva_create_secondary_memu');


/**
 * 
 */
function rva_create_pages() {

	$about_page = array(	
		'slug' => 'about',
		'title' =>'About',
		'template' => '/page_templates/about.php',
		'post_excerpt' => 'About RVA Magazine'
	);
	rva_create_page($about_page);

	$contact_page = array(	
		'slug' => 'contact',
		'title' => 'Contact',
		'template' => '/page_templates/page.php',
		'post_excerpt' => 'Contact RVA Magazine'
	);
	rva_create_page($contact_page);

	$advertizing_page = array(
		'slug' => 'advertising',
		'title' =>'Advertising',
		'template' => '/page_templates/page.php',
		'post_excerpt' => 'Advertize with RVA Magazine'
	);
	rva_create_page($advertizing_page);

	$sponsors_page = array(
		'slug' => 'sponsors',
		'title' =>'Sponsors',
		'template' => '/page_templates/page.php',
		'post_excerpt' => 'RVA Magazine Sponsors'
	);

	rva_create_page($sponsors_page);


} add_action('after_switch_theme', 'rva_create_pages');

function rva_create_page($page_args){
	$page = get_page_by_path( $page_args['slug'] );
	if(!isset($page)){
		$page_id = wp_insert_post(array(
			'post_title' => $page_args['title'],
			'post_type' =>'page',		
			'post_name' => $page_args['slug'],
			'post_status' => 'publish',
			'post_excerpt' => $page_args['post_excerpt']	
		));
	add_post_meta( $page_id, '_wp_page_template', $page_args['template'] );
	}
}
