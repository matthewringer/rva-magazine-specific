<?php
/**
 * Class RVAMag_Magazine_Post_Type
 *
 * @link		
 * @since		0.0.1
 *
 * @package		WordPress
 * @subpackage 	RVAMag
 */
namespace RVAMag;

//require_once RVA_MAG_DOCROOT.'/lib/admin/Input.php';

use RVAMag\Admin\Input as Input;
use RVAMag\Globals_Container as RVA_Globals;

class Magazine_Post_Type {

	/**
	 * Name of the custom post type
	 *
	 * @since 	0.0.1
	 * @access public
	 *
	 * @const string
	 */
	public $name = 'magazine';


	/**
	 * Metabox Nonce for security
	 *
	 * @since 0.0.1
	 * @access private
	 *
	 * @var string
	 */
	private $nonce = 'rvamag_nonce';

	/**
	 * 
	 */
	public static function init_post_type() {
		/*
		 * Initialization for Magazine Post Type
		 */
		$magazine_post_type = new Magazine_Post_Type();
		add_action( 'init', [ $magazine_post_type, 'create_post_type' ], 0, 0 );
		add_action( 'add_meta_boxes', [ $magazine_post_type, 'add_meta_boxes' ], 10, 2 );
		add_action( "save_post_{$magazine_post_type->name}", [ $magazine_post_type, 'save_meta_box' ], 10, 2 );
	}

	/**
	 * Create Post Type
	 *
	 * Creates the Magazine custom post type.
	 */
	public function create_post_type() {
		$args 			= $this->get_args();
		$args['labels']	= $this->get_labels();

		register_post_type($this->name, $args);
	}

	/**
	 * Add Meta Boxes
	 *
	 * @since  0.0.1
	 * @access public
	 */
	public function add_meta_boxes() {
		global $post;
		$metaboxes = $this->get_metaboxes();

		foreach ( $metaboxes as $box ) {
			add_meta_box(
				$box['id'],
				$box['title'],
				$box['callback'],
				$box['name'],
				$box['context'],
				$box['priority']
			);
		}
	}

	/**
	 * Saves a metabox
	 *
	 * @since  0.0.1
	 * @access public
	 *
	 * @param int $post_id The post ID.
	 */
	public function save_meta_box( $post_id ) {
		// Exits script depending on save status
		if (
			wp_is_post_autosave( $post_id ) ||
			wp_is_post_revision( $post_id ) ||
			! Input::verify_nonce( $this->nonce, __FILE__ )
		) {

			return;
		}

		$fields = $this->get_fields();
		foreach( $fields as $input ) {
			Input::update_meta( $post_id, $input );
		}
	}

	/**
	 * suplimental Metaboxes for Magazine post type
	 *
	 * @since 	0.0.1
	 * @access 	protected
	 *
	 * @var array $metaboxes
	 */
	private function get_metaboxes() {

		//global $post;
		$metaboxes = [
			[
				'name'			=> $this->name,
				'callback'		=> [ $this, 'rva_magazine_issue_box_contents' ],
				'id' 			=> 'rva_magazine_issue_box',
				'title' 		=> 'Magazine Issue',
				'context' 		=> 'normal',
				'priority' 		=> 'high',
			]
		];
		return $metaboxes;
	}

	/**
	* Callback for in-post Scripts meta box.
	*
	* @since 0.0.1
	*/
	function rva_magazine_issue_box_contents() {
		//$post_id = get_the_ID();
		global $post;
		wp_nonce_field( basename( __FILE__ ), $this->nonce );
		$fields = $this->get_fields();

		echo '<table>';
		foreach( $fields as $input ) {
			$input->value = get_post_meta( $post->ID, $input->name, true );
			$input->create_input();
		}
		echo '</table>';

		wp_nonce_field( basename( __FILE__ ), $this->nonce );
	}

	/**
	 *
	 */
	private function get_fields() {
		//Issue Number
		$fields[] = new Input(
			[
				'id'	=> 'rva_magazine_issue_number',
				'type'  => 'text',
				'name'  => 'rva_magazine_issue_number',
				'label' => 'Issue Number',
				'value' => '',
			]
		);
		
		// Legacy wpcf-issuenumber
		$fields[] = new Input(
			[
				'id'	=> 'wpcf-issuenumber',
				'type'  => 'text',
				'name'  => 'wpcf-issuenumber',
				'label' => 'Legacy Issue Number',
				'value' => '',
			]
		);

		return $fields;
	}

	/**
	 * Creates post_type arguements.
	 *
	 * @since 	0.0.1
	 * @access private
	 *
	 * @return array Returns array of arguements
	 */
	private static function get_args() {

		return [
			'public'				=> true,
			'exclude_from_search' 	=> false,
			'publicly_queryable'	=> true,
			'show_ui'				=> true,
			'show_in_nav_menus'		=> true,
			'show_in_menu'			=> true,
			'show_in_admin_bar'		=> true,
			'menu_position'			=> 5,
			'menu_icon'				=> 'dashicons-admin-appearance',
			'capability_type'		=> 'post',
			'hierarchical'			=> false,
			'supports'				=> array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
			'has_archive'			=> true,
			'rewrite'				=> array( 'slug' => 'magazine' ),
			'query_var'				=> true
		];

	}

	/**
	 * Creates post_type labels.
	 *
	 * @since 	0.0.1
	 * @access private
	 *
	 * @return array Returns array of arguements
	 */
	private static function get_labels() {

		return [
			'name'               => _x( 'Magazine', 'Post Type General Name', 'rvamag' ),
			'singular_name'      => _x( 'Magazine', 'Post Type Singular Name', 'rvamag' ),
			'menu_name'          => __( 'Magazines', 'rvamag' ),
			'parent_item_colon'  => __( 'Parent Magazines:', 'rvamag' ),
			'all_items'          => __( 'All Ad Magazines', 'rvamag' ),
			'view_item'          => __( 'View Magazine', 'rvamag' ),
			'add_new_item'       => __( 'Add New Magazine', 'rvamag' ),
			'add_new'            => __( 'Add New', 'rvamag' ),
			'edit_item'          => __( 'Edit Magazine', 'rvamag' ),
			'update_item'        => __( 'Update Magazine', 'rvamag' ),
			'search_items'       => __( 'Search Magazines', 'rvamag' ),
			'not_found'          => __( 'Not found', 'rvamag' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'rvamag' ),
		];

	}

}