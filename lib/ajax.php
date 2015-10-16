<?php
/**
 * Create ajax handle for checking cookies
 * and updating metadata
 *
 * @since 1.0.7
 * @uses important genesis_simple_love ajax used.
 * @param nonce, post_id
 * @return json datatype
 */
if( ! class_exists( 'Genesis_Simple_Share_AJAX' ) ){
	class Genesis_Simple_Share_AJAX{
		function __construct(){
			add_action( 'wp_ajax_genesis_simple_love', array( $this, 'ajax_love' ));
			add_action( 'wp_ajax_nopriv_genesis_simple_love', array( $this, 'ajax_love' ));
		}

		function ajax_love() {
			$result 	= array();
			$nonce 		= $_REQUEST['nonce'];
			$post_id 	= url_to_postid( $_REQUEST['data_url'] );
			$loved 		= array();

	        if ( !wp_verify_nonce( $nonce, 'genesis_love' )) {
				exit( 'You don\'t have any power here!' );
			}

	        $handle = '';
	        if( isset( $_COOKIE['genesis_simple_love'] ) ){
	            $loved = @unserialize(base64_decode($_COOKIE['genesis_simple_love']));
	        }

	        //get love
	        if( isset( $_REQUEST['data'] ) && 'getCount' == $_REQUEST['data'] ){
	        	echo '{"url":"'. $_REQUEST['data_url'] .'","count":"'. (int) get_post_meta($post_id, '_genesis_simple_love_', true) .'"}';
	        	die();
	        }

	        //save love
	        if ( is_array( $loved ) && !in_array( $post_id, $loved ) ){
	        		$loved[] 		= $post_id;
	                $post_loved 	= (int) get_post_meta($post_id, '_genesis_simple_love_', true);
	                $post_loved++;
	                update_post_meta( $post_id, '_genesis_simple_love_', $post_loved );
	                
	                $_COOKIE['genesis_simple_love']  = base64_encode(serialize($loved));
	                setcookie( 'genesis_simple_love', $_COOKIE['genesis_simple_love'] , time()+(10*365*24*60*60),'/' );

	                $result['type'] 		= 'success';
	                $result['message'] 		= apply_filters( 'genesis_simple_love_message', __('Thank You for loving this!', 'genesis-simple-share' ) );
	                $result['count'] 		= $post_loved;
	                $result['id'] 			= $post_id;
	        } else {
	        	$post_loved 		= (int) get_post_meta( $post_id, '_genesis_simple_love_', true );
	        	$result['type'] 	= 'error';
	        	$result['message'] 	= apply_filters( 'genesis_simple_loved', __( 'You already loved this. Thanks!', 'genesis-simple-share' ) );
	        	$result['count'] 	= $post_loved;
	        	$result['id'] 		= $post_id;
	        }

			echo $result = json_encode( $result );
			die();
		}
	}
	new Genesis_Simple_Share_AJAX();
}