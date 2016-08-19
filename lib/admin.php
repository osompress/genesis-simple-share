<?php
/**
 * Creates the plugin admin page.
 *
 *
 * @category Genesis Simple Share
 * @package  Admin
 * @author   copyblogger
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 */

//* Prevent direct access to the plugin
if ( ! defined( 'ABSPATH' ) ) {
	die( __( 'Sorry, you are not allowed to access this page directly.', 'genesis-simple-share' ) );
}


/**
 * Registers a new admin page, providing content and corresponding menu items
 *
 * @category Genesis
 * @package Admin
 *
 * @since 0.1.0
 */
class Genesis_Simple_Share_Boxes extends Genesis_Admin_Boxes {

	var $sort_text;

	/**
	 * Create an admin menu item and settings page.
	 *
	 * @since 0.1.0
	 *
	 * @uses MEMBER_ACCESS_SETTINGS_FIELD settings field key
	 *
	 */
	function __construct() {

		$this->sort_text = sprintf( '%s', __( 'You can change button position by reordering these boxes:', 'genesis-simple-share' ) );

		$settings_field   = 'genesis_simple_share';

		$default_settings = apply_filters(
			'genesis_simple_share_defaults',
			array(
				'general_size'       => 'small',
				'general_appearance' => 'filled',
				'general_position'   => 'before_content',
				'general_post'       => 1,
				'googlePlus'         => 1,
				'facebook'           => 1,
				'twitter'            => 1,
				'pinterest'          => 1,
				'linkedin'           => 1,
				'stumbleupon'        => 1,
			)
		);

		$menu_ops = array(
			'submenu' => array(
				/** Do not use without 'main_menu' */
				'parent_slug' => 'genesis',
				'page_title'  => __( 'Genesis Simple Share Settings', 'genesis-simple-share' ),
				'menu_title'  => __( 'Simple Share', 'genesis-simple-share' )
			)
		);

		$page_ops = array();
		/** Just use the defaults */

		$this->create( 'genesis_simple_share_settings', $menu_ops, $page_ops, $settings_field, $default_settings );

		add_action( 'genesis_settings_sanitizer_init', array( $this, 'sanitizer_filters' ) );

		add_filter( "update_user_metadata", array( $this, 'user_meta_save'   ), 10, 4 );
		add_filter( "get_user_option_meta-box-order_genesis_page_genesis_simple_share_settings", array( $this, 'user_meta_return' ) );

	}

	/**
	 * Register each of the settings with a sanitization filter type.
	 *
	 * @since 0.9.0
	 *
	 * @uses genesis_add_option_filter() Assign filter to array of settings.
	 *
	 * @see \Genesis_Settings_Sanitizer::add_filter() Add sanitization filters to options.
	 */
	function sanitizer_filters() {

		$one_zero = array(
			'googlePlus',
			'facebook',
			'twitter',
			'pinterest',
			'linkedin',
			'stumbleupon',
			'general_show_archive',
		);

		$post_types = get_post_types( array( 'public' => true, ) );

		foreach( $post_types as $post_type ){
			$one_zero[] = 'general_' . $post_type;
		}

		genesis_add_option_filter(
			'one_zero',
			$this->settings_field,
			$one_zero
		);

		genesis_add_option_filter(
			'no_html',
			$this->settings_field,
			array(
				'general_size',
				'general_position',
				'general_appearance',
				'twitter_id',
			)
		);

		genesis_add_option_filter(
			'url',
			$this->settings_field,
			array(
				'image_url',
			)
		);

	}

	/**
	 * Loads required scripts.
	 *
	 * @since 0.1.0
	 *
	 */
	function scripts() {

		global $wp_styles;

		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );

		//use wp_enqueue_script() and wp_enqueue_style() to load scripts and styles
		wp_enqueue_script(
			'genesis-simple-share-plugin-js',
			plugins_url( 'sharrre/jquery.sharrre.min.js', __FILE__ ),
			array( 'jquery' ),
			'0.1.0'
		);

		wp_enqueue_style(
			'genesis-simple-share-plugin-css',
			plugins_url( 'css/share.css', __FILE__ ),
			array(),
			'0.1.0'
		);

		wp_enqueue_style(
			'genesis-simple-share-genericons-css',
			plugins_url( 'css/genericons.css', __FILE__ ),
			array(),
			'0.1.0'
		);

		wp_enqueue_script(
			'genesis-simple-share-admin-js',
			plugins_url( 'js/admin.js', __FILE__ ),
			array( 'jquery' ),
			'0.1.0'
		);

		wp_enqueue_style(
			'genesis-simple-share-admin-css',
			plugins_url( 'css/admin.css', __FILE__ ),
			array(),
			'0.1.0'
		);

		wp_enqueue_style(
			'genesis-simple-share-admin-css-ie',
			plugins_url( 'css/admin-ie.css', __FILE__ ),
			array( 'genesis-simple-share-admin-css' ),
			'0.1.0'
		);

		//* Add IE Styles
		$wp_styles->add_data( 'genesis-simple-share-admin-css-ie', 'conditional', 'lt IE 10' );

	}

	/**
	 * Hijacks meta save and switches to the option save for the meta box order on the simple share settings page
	 *
	 * @since 0.1.0
	 *
	 * @param null   $check        default null value if other value returned the meta is not saved
	 * @param string $object_id    ID of the object being edited
	 * @param string $meta_key     key being edited
	 * @param string $meta_value   value being assigned to the key
	 *
	 * return boolean
	 */
	function user_meta_save( $check, $object_id, $meta_key, $meta_value ) {

		if( 'meta-box-order_genesis_page_genesis_simple_share_settings' == $meta_key )
			return update_option( 'genesis_simple_share_sort', $meta_value );

		return $check;
	}

	/**
	 * Hijacks user meta check for the meta box order on simple share settings page
	 *
	 * @since 0.1.0
	 *
	 * @param mixed   $result   old value
	 *
	 * return array
	 */
	function user_meta_return( $result ) {

		if( $new_result = get_option( 'genesis_simple_share_sort' ) )
			return $new_result;

		return $result;
	}

	/**
	 * Register meta boxes.
	 *
	 *
	 * @since 0.1.0
	 *
	 */
	function metaboxes() {

		//var_dump( $current_screen );

		add_action( 'genesis_simple_share_admin_table_before_rows', array( $this, 'live_preview'     )    );
		add_action( $this->pagehook . '_settings_page_boxes'      , array( $this, 'general_settings' ), 0 );
		add_action( $this->pagehook . '_settings_page_boxes'      , array( $this, 'sort_text'        ), 0 );

		add_meta_box( 'genesis_simple_share_google_plus', __( 'Google+', 'genesis-simple-share' ), array( $this, 'google_plus' ), $this->pagehook, 'main' );
		add_meta_box( 'genesis_simple_share_facebook', __( 'Facebook', 'genesis-simple-share' ), array( $this, 'facebook' ), $this->pagehook, 'main' );
		add_meta_box( 'genesis_simple_share_twitter', __( 'Twitter', 'genesis-simple-share' ), array( $this, 'twitter' ), $this->pagehook, 'main' );
		add_meta_box( 'genesis_simple_share_pinterest', __( 'Pinterest', 'genesis-simple-share' ), array( $this, 'pinterest' ), $this->pagehook, 'main' );
		add_meta_box( 'genesis_simple_share_linkedin', __( 'Linkedin', 'genesis-simple-share' ), array( $this, 'linkedin' ), $this->pagehook, 'main' );
		add_meta_box( 'genesis_simple_share_stumbleupon', __( 'StumbleUpon', 'genesis-simple-share' ), array( $this, 'stumbleupon' ), $this->pagehook, 'main' );



	}

	/**
	 * Create General settings metabox output
	 *
	 *
	 * @since 0.1.0
	 *
	 */
	function general_settings() {

		$id = 'general';

?>

		<div class="wrap gss-clear">

			<table class="form-table">
				<tbody>

					<?php
		do_action( 'genesis_simple_share_admin_table_before_rows' );

		$this->select_field( $id . '_size', __( 'Icon Size', 'genesis-simple-share' ), array(
				'small'  => __( 'Small Bar' , 'genesis-simple-share' ),
				'medium' => __( 'Medium Bar', 'genesis-simple-share' ),
				'tall'   => __( 'Box'       , 'genesis-simple-share' ),
			) );

		$this->select_field( $id . '_appearance', __( 'Icon Appearance', 'genesis-simple-share' ), array(
				'outlined' => __( 'Outlined', 'genesis-simple-share' ),
				'filled'   => __( 'Filled'  , 'genesis-simple-share' ),
			) );

		$this->position( $id );
		$this->post_type_checkbox( $id );

		do_action( 'genesis_simple_share_admin_table_after_rows' );
?>
				</tbody>
			</table>
		</div>

		<?php

	}

	function live_preview() {
?>
		<tr valign="top" class="share-preview-row">
			<th scope="row">Live Preview</th>
			<td>
			<?php

		require_once( GENESIS_SIMPLE_SHARE_LIB . 'admin-icon-preview.php' );
		genesis_share_icon_preview_output( 'preview' );

?>
			</td>
		</tr>
		<?php
	}

	function sort_text() {
		printf( '<br /><br /><h3>%s</h3>', $this->sort_text );
	}

	/**
	 * Create Google+ settings metabox output
	 *
	 *
	 * @since 0.1.0
	 *
	 */
	function google_plus() {

		$id = 'googlePlus';

		$this->checkbox( $id , __( 'Use this button?', 'genesis-simple-share' ) );

		//echo $this->sort_text;

	}

	/**
	 * Create Facebook settings metabox output
	 *
	 *
	 * @since 0.1.0
	 *
	 */
	function facebook() {

		$id = 'facebook';

		$this->checkbox( $id , __( 'Use this button?', 'genesis-simple-share' ) );

		//echo $this->sort_text;

	}

	/**
	 * Create Twitter settings metabox output
	 *
	 *
	 * @since 0.1.0
	 *
	 */
	function twitter() {

		$id = 'twitter';

		$this->checkbox( $id , __( 'Use this button?', 'genesis-simple-share' ) );

		?><p>
			<label for="<?php echo $this->get_field_id( 'twitter_id' ); ?>"><?php _e( 'Enter Twitter ID for @via to be added to default tweet text:', 'genesis-simple-share' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'twitter_id' ); ?>" id="<?php echo $this->get_field_id( 'twitter_id' ); ?>" value="<?php echo esc_attr( str_replace( '@', '', $this->get_field_value( 'twitter_id' ) ) ); ?>" size="27" />
		</p><?php


	}

	/**
	 * Create StumbleUpon settings metabox output
	 *
	 *
	 * @since 0.1.0
	 *
	 */
	function stumbleupon() {

		$id = 'stumbleupon';

		$this->checkbox( $id , __( 'Use this button?', 'genesis-simple-share' ) );

	}

	/**
	 * Create Pinterest settings metabox output
	 *
	 *
	 * @since 0.1.0
	 *
	 */
	function pinterest() {

		$id = 'pinterest';

		$this->checkbox( $id , __( 'Use this button?', 'genesis-simple-share' ) );

		?><p>
			<label for="<?php echo $this->get_field_id( 'image_url' ); ?>"><?php _e( 'Enter Default Image URL if there is no image available in content being shared:', 'genesis-simple-share' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'image_url' ); ?>" id="<?php echo $this->get_field_id( 'image_url' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'image_url' ) ); ?>" size="27" />
		</p><?php

	}

	/**
	 * Create Linkedin settings metabox output
	 *
	 *
	 * @since 0.1.0
	 *
	 */
	function linkedin() {

		$id = 'linkedin';

		$this->checkbox( $id , __( 'Use this button?', 'genesis-simple-share' ) );

	}

	/**
	 * Outputs select field to select position for the icon
	 *
	 * @since 0.1.0
	 *
	 * @param string  $id        ID base to use when building select box.
	 *
	 */
	function position( $id ){

		$this->select_field( $id . '_position', __( 'Icon Display Position'   , 'genesis-simple-share' ), array(
				'off'            => __( 'Select display position to enable icons.', 'genesis-simple-share' ),
				'before_content' => __( 'Before the Content'                      , 'genesis-simple-share' ),
				'after_content'  => __( 'After the Content'                       , 'genesis-simple-share' ),
				'both'           => __( 'Before and After the Content'            , 'genesis-simple-share' ),
			) );

	}

	/**
	 * Outputs select field
	 *
	 * @since 0.1.0
	 *
	 * @param string  $id        ID to use when building select box.
	 * @param string  $name      Label text for the select field.
	 * @param array   $option    Array key $option=>$title used to build select options.
	 *
	 */
	function select_field( $id, $name, $options = array() ){
		$current = $this->get_field_value( $id );
?>
		<tr valign="top">
			<th scope="row"><label for="<?php echo $this->get_field_id( $name ); ?>"><?php echo $name ?></label></th>
			<td><select name="<?php echo $this->get_field_name( $id ); ?>" class="<?php echo 'genesis_simple_share_' . $id; ?>" id="<?php echo $this->get_field_id( $id ); ?>">
				<?php
		if ( ! empty( $options ) ) {
			foreach ( (array) $options as $option => $title ) {

				printf( '<option value="%s"%s>%s</option>',
					esc_attr( $option ),
					selected( $current, $option, false ),
					esc_html( $title )
				);

			}
		}
?>
			</select></td>
		</tr><?php
	}

	/**
	 * Outputs checkbox fields for public post types.
	 *
	 * @since 0.1.0
	 *
	 * @param string  $id        ID base to use when building checkbox.
	 *
	 */
	function post_type_checkbox( $id ){

		$post_types = get_post_types( array( 'public' => true, ) );

		printf( '<tr valign="top"><th scope="row">%s</th>', __( 'Enable on:', 'genesis-simple-share' ) );

		echo '<td>';

		foreach( $post_types as $post_type )
			$this->checkbox( $id . '_' . $post_type, $post_type );

		$this->checkbox( $id . '_show_archive', __( 'Show on Archive Pages', 'genesis-simple-share' ) );

		echo '</td></tr>';

	}

	/**
	 * Outputs checkbox field.
	 *
	 * @since 0.1.0
	 *
	 * @param string  $id        ID to use when building  checkbox.
	 * @param string  $name      Label text for the checkbox.
	 *
	 */
	function checkbox( $id, $name ){
		printf( '<label for="%s"><input type="checkbox" name="%s" id="%s" value="1"%s /> %s </label> ',
			$this->get_field_id( $id ),
			$this->get_field_name( $id ),
			$this->get_field_id( $id ),
			checked( $this->get_field_value( $id ), 1, false ),
			$name
		);
		echo '<br />';
	}


}

global $genesis_simple_share;

$genesis_simple_share = new Genesis_Simple_Share_Boxes;
