<?php

class Genesis_Simple_Share_Entry_Meta {

	function __construct() {
	
		add_action( 'admin_menu', array( $this, 'add_meta_box' ) );
		add_action( 'save_post' , array( $this, 'save_meta'    ) );
		
	}
	
	function add_meta_box() {
		
		foreach ( (array) get_post_types( array( 'public' => true ) ) as $type ) {
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
	
	function meta_box() {
	
		$check = get_post_meta( get_the_ID(), '_disable_gss', true ) ? 1 : '';

		wp_nonce_field( 'genesis_simple_share_inpost_save', 'genesis_simple_share_inpost_nonce' )
		?>
		<p>
			<input type="checkbox" id="_disable_gss" name="_disable_gss" <?php checked( $check, '1' ); ?> />
			<label for="_disable_gss"><?php _e( 'Disable Share Buttons', 'genesis-simple-share' ); ?></label>
		</p>
		<?php
		
	}
	
	function save_meta( $post_id ) {
	
		// Bail if we're doing an auto save
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		
		// if our nonce isn't there, or we can't verify it, bail
		if( ! isset( $_POST['genesis_simple_share_inpost_nonce'] ) || ! wp_verify_nonce( $_POST['genesis_simple_share_inpost_nonce'], 'genesis_simple_share_inpost_save' ) ) {
			return;
		}
		
		// if our current user can't edit this post, bail
		if( !current_user_can( 'edit_posts' ) ) {
			return;
		}
		
		if( isset( $_POST['_disable_gss'] ) ){
			update_post_meta( $post_id, '_disable_gss', 1 );
		}
		else {
			delete_post_meta( $post_id, '_disable_gss' );
		}
		
	}
	
}

global $genesis_simple_share_entry_meta;

$genesis_simple_share_entry_meta = new Genesis_Simple_Share_Entry_Meta;
