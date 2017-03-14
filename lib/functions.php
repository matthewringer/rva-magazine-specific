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

 function rva_load_more_posts() {
	
	wp_enqueue_script( 'rva-load-more', RVA_MAG_PLUGURL . '/assets/js/load-more.js', array( 'jquery' ), '1.0', true );

	//wp_enqueue_script( 'rva-load-more', get_stylesheet_directory_uri() . '/js/load-more.js', array( 'jquery' ), '1.0', true );
	
	global $wp_query;
	$args = array(
		'url'   => admin_url( 'admin-ajax.php' ),
		'query' => $wp_query->query,
	);
	wp_localize_script( 'rva-load-more', 'rvaloadmore', $args );

 }