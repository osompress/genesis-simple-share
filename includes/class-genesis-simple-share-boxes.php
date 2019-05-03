<?php
/**
 * Creates the plugin admin page.
 *
 * @category Genesis Simple Share
 * @package  Admin
 * @author   copyblogger
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( esc_html__( 'Sorry, you are not allowed to access this page directly.', 'genesis-simple-share' ) );
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

	/**
	 * Sort message.
	 *
	 * @var string
	 */
	public $sort_text;

	/**
	 * Create an admin menu item and settings page.
	 *
	 * @since 0.1.0
	 *
	 * @uses MEMBER_ACCESS_SETTINGS_FIELD settings field key
	 */
	public function __construct() {

		$this->sort_text = sprintf( '%s', __( 'You can change button position by reordering these boxes:', 'genesis-simple-share' ) );

		$settings_field = 'genesis_simple_share';

		$default_settings = apply_filters(
			'genesis_simple_share_defaults',
			array(
				'general_size'         => 'small',
				'general_appearance'   => 'filled',
				'general_position'     => 'before_content',
				'general_post'         => 1,
				'general_disble_count' => 0,
				'facebook'             => 1,
				'twitter'              => 1,
				'pinterest'            => 1,
				'linkedin'             => 1,
			)
		);

		$menu_ops = array(
			'submenu' => array(
				/** Do not use without 'main_menu' */
				'parent_slug' => 'genesis',
				'page_title'  => __( 'Genesis Simple Share Settings', 'genesis-simple-share' ),
				'menu_title'  => __( 'Simple Share', 'genesis-simple-share' ),
			),
		);

		$page_ops = array();
		/** Just use the defaults */

		$this->create( 'genesis_simple_share_settings', $menu_ops, $page_ops, $settings_field, $default_settings );

		add_action( 'genesis_settings_sanitizer_init', array( $this, 'sanitizer_filters' ) );

		add_filter( 'update_user_metadata', array( $this, 'user_meta_save' ), 10, 4 );
		add_filter( 'get_user_option_meta-box-order_genesis_page_genesis_simple_share_settings', array( $this, 'user_meta_return' ) );

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
	public function sanitizer_filters() {

		$one_zero = array(
			'googlePlus',
			'facebook',
			'twitter',
			'pinterest',
			'linkedin',
			'general_show_archive',
		);

		$post_types = get_post_types( array( 'public' => true ) );

		foreach ( $post_types as $post_type ) {
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
	 */
	public function scripts() {

		global $wp_styles;

		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );

		// Use wp_enqueue_script() and wp_enqueue_style() to load scripts and styles.
		wp_enqueue_script(
			'genesis-simple-share-plugin-js',
			GENESIS_SIMPLE_SHARE_URL . '/assets/js/sharrre/jquery.sharrre.min.js',
			array( 'jquery' ),
			GENESIS_SIMPLE_SHARE_VERSION,
			false
		);

		wp_enqueue_style(
			'genesis-simple-share-plugin-css',
			GENESIS_SIMPLE_SHARE_URL . '/assets/css/share.min.css',
			array(),
			GENESIS_SIMPLE_SHARE_VERSION
		);

		wp_enqueue_style(
			'genesis-simple-share-genericons-css',
			GENESIS_SIMPLE_SHARE_URL . '/assets/css/genericons.min.css',
			array(),
			GENESIS_SIMPLE_SHARE_VERSION
		);

		wp_enqueue_script(
			'genesis-simple-share-admin-js',
			GENESIS_SIMPLE_SHARE_URL . '/assets/js/admin.min.js',
			array( 'jquery' ),
			GENESIS_SIMPLE_SHARE_VERSION,
			false
		);

		wp_enqueue_style(
			'genesis-simple-share-admin-css',
			GENESIS_SIMPLE_SHARE_URL . '/assets/css/admin.min.css',
			array(),
			GENESIS_SIMPLE_SHARE_VERSION
		);

		wp_enqueue_style(
			'genesis-simple-share-admin-css-ie',
			GENESIS_SIMPLE_SHARE_URL . '/assets/css/admin-ie.min.css',
			array( 'genesis-simple-share-admin-css' ),
			GENESIS_SIMPLE_SHARE_VERSION
		);

		// Add IE Styles.
		$wp_styles->add_data( 'genesis-simple-share-admin-css-ie', 'conditional', 'lt IE 10' );

	}

	/**
	 * Hijacks meta save and switches to the option save for the meta box order on the simple share settings page
	 *
	 * @since 0.1.0
	 *
	 * @param null   $check        default null value if other value returned the meta is not saved.
	 * @param string $object_id    ID of the object being edited.
	 * @param string $meta_key     key being edited.
	 * @param string $meta_value   value being assigned to the key.
	 *
	 * @return boolean
	 */
	public function user_meta_save( $check, $object_id, $meta_key, $meta_value ) {

		if ( 'meta-box-order_genesis_page_genesis_simple_share_settings' === $meta_key ) {
			return update_option( 'genesis_simple_share_sort', $meta_value );
		}

		return $check;
	}

	/**
	 * Hijacks user meta check for the meta box order on simple share settings page
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $result old value.
	 *
	 * @return array
	 */
	public function user_meta_return( $result ) {

		$new_result = get_option( 'genesis_simple_share_sort' );

		if ( $new_result ) {
			return $new_result;
		}

		return $result;
	}

	/**
	 * Register meta boxes.
	 *
	 * @since 0.1.0
	 */
	public function metaboxes() {

		add_action( 'genesis_simple_share_admin_table_before_rows', array( $this, 'live_preview' ) );
		add_action( $this->pagehook . '_settings_page_boxes', array( $this, 'general_settings' ), 0 );
		add_action( $this->pagehook . '_settings_page_boxes', array( $this, 'sort_text' ), 0 );

		add_meta_box( 'genesis_simple_share_facebook', __( 'Facebook', 'genesis-simple-share' ), array( $this, 'facebook' ), $this->pagehook, 'main' );
		add_meta_box( 'genesis_simple_share_twitter', __( 'Twitter', 'genesis-simple-share' ), array( $this, 'twitter' ), $this->pagehook, 'main' );
		add_meta_box( 'genesis_simple_share_pinterest', __( 'Pinterest', 'genesis-simple-share' ), array( $this, 'pinterest' ), $this->pagehook, 'main' );
		add_meta_box( 'genesis_simple_share_linkedin', __( 'Linkedin', 'genesis-simple-share' ), array( $this, 'linkedin' ), $this->pagehook, 'main' );
	}

	/**
	 * Create General settings metabox output
	 *
	 * @since 0.1.0
	 */
	public function general_settings() {

		$id = 'general';

		?>

		<div class="wrap gss-clear">

			<table class="form-table">
				<tbody>

					<?php
					do_action( 'genesis_simple_share_admin_table_before_rows' );

					$this->select_field(
						$id . '_size',
						__( 'Icon Size', 'genesis-simple-share' ),
						array(
							'small'  => __( 'Small Bar', 'genesis-simple-share' ),
							'medium' => __( 'Medium Bar', 'genesis-simple-share' ),
							'tall'   => __( 'Box', 'genesis-simple-share' ),
						)
					);

					$this->select_field(
						$id . '_appearance',
						__( 'Icon Appearance', 'genesis-simple-share' ),
						array(
							'outlined' => __( 'Outlined', 'genesis-simple-share' ),
							'filled'   => __( 'Filled', 'genesis-simple-share' ),
						)
					);

					$this->position( $id );

					$this->disable_count( $id );

					$this->post_type_checkbox( $id );

					do_action( 'genesis_simple_share_admin_table_after_rows' );
					?>
				</tbody>
			</table>
		</div>

		<?php

	}

	/**
	 * Live preview.
	 */
	public static function live_preview() {
		?>
		<tr valign="top" class="share-preview-row">
			<th scope="row">Live Preview</th>
			<td>
			<?php

			require_once GENESIS_SIMPLE_SHARE_INC . 'class-genesis-simple-share-preview.php';
			genesis_share_icon_preview_output( 'preview' );

			?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Sort text output.
	 */
	public function sort_text() {
		printf( '<br /><br /><h3>%s</h3>', esc_html( $this->sort_text ) );
	}

	/**
	 * Create Facebook settings metabox output
	 *
	 * @since 0.1.0
	 */
	public function facebook() {

		$id = 'facebook';

		$this->checkbox( $id, __( 'Use this button?', 'genesis-simple-share' ) );
	}

	/**
	 * Create Twitter settings metabox output
	 *
	 * @since 0.1.0
	 */
	public function twitter() {

		$id = 'twitter';

		$this->checkbox( $id, __( 'Use this button?', 'genesis-simple-share' ) );

		?>
		<p>
			<label for="<?php echo esc_html( $this->get_field_id( 'twitter_id' ) ); ?>"><?php esc_html_e( 'Enter Twitter ID for @via to be added to default tweet text:', 'genesis-simple-share' ); ?></label>
			<input type="text" name="<?php echo esc_html( $this->get_field_name( 'twitter_id' ) ); ?>" id="<?php echo esc_html( $this->get_field_id( 'twitter_id' ) ); ?>" value="<?php echo esc_attr( str_replace( '@', '', $this->get_field_value( 'twitter_id' ) ) ); ?>" size="27" />
		</p>
		<?php
	}

	/**
	 * Create Pinterest settings metabox output
	 *
	 * @since 0.1.0
	 */
	public function pinterest() {

		$id = 'pinterest';

		$this->checkbox( $id, __( 'Use this button?', 'genesis-simple-share' ) );

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image_url' ) ); ?>"><?php esc_html_e( 'Enter Default Image URL if there is no image available in content being shared:', 'genesis-simple-share' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'image_url' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'image_url' ) ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'image_url' ) ); ?>" size="27" />
		</p>
		<?php

	}

	/**
	 * Create Linkedin settings metabox output
	 *
	 * @since 0.1.0
	 */
	public function linkedin() {

		$id = 'linkedin';

		$this->checkbox( $id, __( 'Use this button?', 'genesis-simple-share' ) );

	}

	/**
	 * Outputs select field to select position for the icon
	 *
	 * @since 0.1.0
	 *
	 * @param string $id        ID base to use when building select box.
	 */
	public function position( $id ) {

		$this->select_field(
			$id . '_position',
			__( 'Icon Display Position', 'genesis-simple-share' ),
			array(
				'off'            => __( 'Select display position to enable icons.', 'genesis-simple-share' ),
				'before_content' => __( 'Before the Content', 'genesis-simple-share' ),
				'after_content'  => __( 'After the Content', 'genesis-simple-share' ),
				'both'           => __( 'Before and After the Content', 'genesis-simple-share' ),
			)
		);

	}

	/**
	 * Outputs the checkbox to disable the count.
	 *
	 * @access public
	 *
	 * @param mixed $id Count Id.
	 *
	 * @return void
	 */
	public function disable_count( $id ) {
		$this->checkbox_table( $id . '_disable_count', __( 'Hide Count', 'genesis-simple-share' ) );
	}

	/**
	 * Outputs select field
	 *
	 * @since 0.1.0
	 *
	 * @param string $id        ID to use when building select box.
	 * @param string $name      Label text for the select field.
	 * @param array  $options    Array key $option=>$title used to build select options.
	 */
	public function select_field( $id, $name, $options = array() ) {
		$current = $this->get_field_value( $id );
		?>
		<tr valign="top">
			<th scope="row"><label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php echo esc_html( $name ); ?></label></th>
			<td><select name="<?php echo esc_attr( $this->get_field_name( $id ) ); ?>" class="<?php echo esc_attr( 'genesis_simple_share_' . $id ); ?>" id="<?php echo esc_attr( $this->get_field_id( $id ) ); ?>">
				<?php
				if ( ! empty( $options ) ) {
					foreach ( (array) $options as $option => $title ) {

						printf(
							'<option value="%s"%s>%s</option>',
							esc_attr( $option ),
							selected( $current, $option, false ),
							esc_html( $title )
						);
					}
				}
				?>
			</select></td>
		</tr>
		<?php
	}

	/**
	 * Outputs checkbox fields for public post types.
	 *
	 * @since 0.1.0
	 *
	 * @param string $id        ID base to use when building checkbox.
	 */
	public function post_type_checkbox( $id ) {

		$post_types = get_post_types( array( 'public' => true ) );

		/**
		 * Allows filtering the $post_types that are supported.
		 *
		 * @access public
		 * @param  array $post_types supported post types
		 * @return void
		 */
		$post_types = apply_filters( 'genesis_simple_share_post_types_support', $post_types );

		printf( '<tr valign="top"><th scope="row">%s</th>', esc_html__( 'Enable on:', 'genesis-simple-share' ) );

		echo '<td>';

		foreach ( $post_types as $post_type ) {
			$this->checkbox( $id . '_' . $post_type, $post_type );
		}

		$this->checkbox( $id . '_show_archive', __( 'Show on Archive Pages', 'genesis-simple-share' ) );

		echo '</td></tr>';

	}

	/**
	 * Outputs checkbox field.
	 *
	 * @since 0.1.0
	 *
	 * @param string $id        ID to use when building  checkbox.
	 * @param string $name      Label text for the checkbox.
	 */
	public function checkbox( $id, $name ) {
		printf(
			'<label for="%s"><input type="checkbox" name="%s" id="%s" value="1"%s /> %s </label> ',
			esc_attr( $this->get_field_id( $id ) ),
			esc_attr( $this->get_field_name( $id ) ),
			esc_attr( $this->get_field_id( $id ) ),
			checked( $this->get_field_value( $id ), 1, false ),
			esc_html( $name )
		);
		echo '<br />';
	}

	/**
	 * Shows the checkbox table.
	 *
	 * @param  string $id          Field id.
	 * @param  string $name        Field name.
	 * @param  string $description Description.
	 */
	public function checkbox_table( $id, $name, $description = '' ) {

		$description = $description ? sprintf( '<p class="description">%s</p>', $description ) : '';

		printf(
			'<tr valign="top"><th scope="row"><label for="%1$s">%4$s</th><td><input type="checkbox" class="genesis_simple_share_%3$s" name="%2$s" id="%1$s" value="1"%5$s />%6$s</td></tr>',
			esc_attr( $this->get_field_id( $id ) ),
			esc_attr( $this->get_field_name( $id ) ),
			esc_attr( $id ),
			esc_html( $name ),
			checked( $this->get_field_value( $id ), 1, false ),
			esc_html( $description )
		);

	}

}

global $genesis_simple_share;

$genesis_simple_share = new Genesis_Simple_Share_Boxes();
