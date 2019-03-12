<?php
/**
 * Meta Boxes file.
 *
 * @package genesis-simple-share
 */

/**
 * Meta Boxes class.
 */
class Genesis_Simple_Share_Entry_Meta {

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_meta' ) );

	}

	/**
	 * Callback on the `admin_menu` action.
	 * Adds the post meta boxes for supported post types.
	 *
	 * @access public
	 * @return void
	 */
	public function add_meta_box() {

		$post_types = (array) get_post_types( array( 'public' => true ) );

		/**
		 * Allows filtering the $post_types that are supported.
		 *
		 * @access public
		 * @param  array $post_types supported post types
		 * @return void
		 */
		$post_types = apply_filters( 'genesis_simple_share_post_types_support', $post_types );

		foreach ( $post_types as $type ) {
			if ( genesis_get_option( 'general_' . $type, 'genesis_simple_share' ) ) {
				add_meta_box(
					'genesis-simple-share-entry-meta',
					__( 'Share Settings', 'genesis-simple-share' ),
					array( $this, 'meta_box' ),
					$type,
					'side',
					'default'
				);
			}
		}

	}

	/**
	 * Metabox
	 */
	public function meta_box() {

		$check = get_post_meta( get_the_ID(), '_disable_gss', true ) ? 1 : '';

		wp_nonce_field( 'genesis_simple_share_inpost_save', 'genesis_simple_share_inpost_nonce' )
		?>
		<p>
			<input type="checkbox" id="_disable_gss" name="_disable_gss" <?php checked( $check, '1' ); ?> />
			<label for="_disable_gss"><?php esc_html_e( 'Disable Share Buttons', 'genesis-simple-share' ); ?></label>
		</p>
		<p>
			<label for="_gss_alternate_url"><?php esc_html_e( 'Alternate URL', 'genesis-simple-share' ); ?></label>
			<input type="text" id="_gss_alternate_url" name="_gss_alternate_url" value="<?php echo esc_url( get_post_meta( get_the_ID(), '_gss_alternate_url', true ) ); ?>" />
			<span class="description"><?php esc_html_e( 'The alternate URL is used in place of the default link. This is the URL that will be shared and checked for social shares.', 'genesis-simple-share' ); ?></span>
		</p>
		<?php

	}

	/**
	 * Save function.
	 *
	 * @param string $post_id Post Id.
	 */
	public function save_meta( $post_id ) {

		// Bail if we're doing an auto save.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// if our nonce isn't there, or we can't verify it, bail.
		if ( ! isset( $_POST['genesis_simple_share_inpost_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['genesis_simple_share_inpost_nonce'] ) ), 'genesis_simple_share_inpost_save' ) ) {
			return;
		}

		// if our current user can't edit this post, bail.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$keys = array(
			'_disable_gss',
			'_gss_alternate_url',
		);

		foreach ( $keys as $key ) {
			if ( isset( $_POST[ $key ] ) ) {

				switch ( $key ) {
					case '_disable_gss':
						$value = 1;
						break;
					case '_gss_alternate_url':
						$value = esc_url( sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
						break;
				}

				update_post_meta( $post_id, $key, $value );
			} else {
				delete_post_meta( $post_id, $key );
			}
		}

	}

}

global $genesis_simple_share_entry_meta;

$genesis_simple_share_entry_meta = new Genesis_Simple_Share_Entry_Meta();
