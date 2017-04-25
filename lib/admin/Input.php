<?php
/**
 * An Input class for RVA Mag
 *
 * Input class for creating a field/metabox input
 * 
 * @link    
 * @since   0.0.1
 *
 * @package WordPress
 * @subpackage RVAMag
 */
namespace RVAMag\Admin;

require_once RVA_MAG_DOCROOT.'/lib/Globals_Container.php';

use RVAMag\Globals_Container as RVAMag_Globals;

class Input {

	/**
	 * Input Type
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Input name
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Input ID
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Input Label
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @var string
	 */
	 public $label;

	/**
	 * Input Value
	 * 
	 * @since 0.0.1
	 * @access public
	 *
	 * @var string|int
	 */
	public $value;

	/**
	 * Input Validation Callback
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @var string|array Callback method for verification/saving
	 */
	public $callback;

	/**
	 * PHP5 Constructor
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @param $field array Optional
	 */
	public function __construct( $field = null ) {
		//echo print_r($field, true);
		if ( $field !== null ) {
			// Set defaults
			$this->id 		= $this->get_field_value( $field['id'] );
			$this->type 	= $this->get_field_value( $field['type'] );
			$this->name 	= $this->get_field_value( $field['name'] );
			$this->label 	= $this->get_field_value( $field['label'] );
			$this->value 	= $this->get_field_value( $field['value'] );
		}
	}

	/**
	 * Creates inpuit field for Meta Box
	 *
	 * Creates an input row for custom field in CPT Input box. Must be wrapped 
	 * in a <table> tag.
	 *
	 * @access public
	 * @since 0.0.1
	 *
	 * returns an HTML input
	 */
	public function create_input() {
		global $post;
		?>
		<tr>
			<td><label for="<?php echo $this->id; ?>" class="rva-admin-row-title"><?php printf( __( $this->label, 'rva-mag' ) ); ?></label></tb>
			<td align="left">
				<?php
				switch( $this->type ) :
					// Text Input
					case 'text' :
						?>
						<input type="<?php echo $this->type; ?>" style="width: 100%;" name="<?php echo $this->name; ?>" id="<?php echo $this->id; ?>" value="<?php $this->field_value($this->value); ?>" size="50" />
						<?php
						break;

					// Text Area
					case 'textarea' :
						?>
						<textarea name="<?php echo $this->name; ?>" id="<?php echo $this->id; ?>" cols="49" rows="2"><?php echo esc_textarea( $this->get_field_value( $this->value ) ); ?></textarea>
						<?php
						break;

					// Checkbox
					case 'checkbox' :
						?>
						<input type="<?php echo $this->type; ?>" name="<?php echo $this->name; ?>" id="<?php echo $this->id; ?>"<?php checked( $this->value, 1 ); ?> value="1" />
						<?php
						break;

					//DateTime
					case 'datetime' :
						//TODO: set the the time zone
						?>
						<input type="<?php echo $this->type; ?>" style="width: 100%;" name="<?php echo $this->name; ?>" id="<?php echo $this->id; ?>" value="<?php $this->field_value($this->value); ?>" size="50" />
						<script>
							jQuery('#<?php echo $this->id; ?>').datetimepicker(
								{
									format: 'Y/m/d h:i A'
								}
							);
						</script>
						<?php

				endswitch;
				?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Field Value
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @see RVA_Mag_Post_Type::get_field_value
	 * @param $values
	 *
	 * Echos out value.
	 */
	public function field_value( $value ) {

		echo $this->get_field_value( $value );

	}

	/**
	 * Checks if a value is set before trying to return it.
	 *
	 * This prevents unnecessary errors from occuring for undefined indices
	 * It's not designed to provide any security.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @param $value int|string|null
	 * 
	 * @return mixed
	 */
	 public function get_field_value( $value ) {

		 return ( ( isset( $value ) && $value != null ) ? $value : null );
	 }

	/**
	 * Update's the meta data for all supplied fields utilizing $_POST data.
	 *
	 * @since  0.0.1
	 * @access public
	 *
	 * @param int   $post_id
	 * @param $input of type \RVAMag\Admin\Input
	 */
	public static function update_meta( $post_id, $input ) {

		// Checks for input and sanitizes/saves if needed
		if ( RVAMag_Globals::post_var_exists( $input->name ) ) {
			$new_value = sanitize_text_field( RVAMag_Globals::get_post_var( $input->name ) );
		} else {
			$new_value = ( RVAMag_Globals::post_var_exists( $input->name ) ? true : false );
		}
		update_post_meta( $post_id, $input->name, $new_value );
	}

	/**
	 * Verifies the nonce is correct.
	 *
	 * @since  0.0.1
	 * @access public
	 *
	 * @param string $nonce
	 * @param string $file default: __FILE__
	 *
	 * @return bool
	 */
	public static function verify_nonce($nonce, $file = __FILE__) {
		if (
			RVAMag_Globals::post_var_exists( $nonce )
			&& wp_verify_nonce( RVAMag_Globals::get_post_var( $nonce ), basename( $file ) )
		) {

			return true;
		} else {

			return false;
		}
	}

}