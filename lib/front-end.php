<?php
/**
 * Creates the plugin front end output.
 *
 *
 * @category Genesis Simple Share
 * @package  Output
 * @author   copyblogger
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 */
 
/* Prevent direct access to the plugin */
if ( !defined( 'ABSPATH' ) ) {
    die( "Sorry, you are not allowed to access this page directly." );
}

/**
 * Generates output on the front end of a Premise enabled site
 *
 * @category Genesis Simple Share
 * @package Output
 *
 * @since 0.1.0
 */
class Gensis_Simple_Share_Front_End {
	
	var $icons;
	var $appearance;
	var $size;
	
	/**
	 * Create front end output.
	 *
	 * @since 0.1.0
	 *
	 */
	function __construct() {
	
		$icons = get_option( 'genesis_simple_share_sort', array(
			'main' => 'genesis_simple_share_google_plus,genesis_simple_share_facebook,genesis_simple_share_twitter,genesis_simple_share_pinterest,genesis_simple_share_linkedin,genesis_simple_share_stumbleupon' 
			) );
			
			$icons = explode( ',', $icons['main'] );
		
		$icon_sort = array();
		
		foreach( $icons as $icon ){
			switch( $icon ){
				
				case 'genesis_simple_share_google_plus':
					$icon_sort[] = 'googlePlus';
					break;
					
				case 'genesis_simple_share_facebook':
					$icon_sort[] = 'facebook';
					break;
					
				case 'genesis_simple_share_twitter':
					$icon_sort[] = 'twitter';
					break;
					
				case 'genesis_simple_share_pinterest':
					$icon_sort[] = 'pinterest';
					break;
					
				case 'genesis_simple_share_linkedin':
					$icon_sort[] = 'linkedin';
					break;
					
				case 'genesis_simple_share_stumbleupon':
					$icon_sort[] = 'stumbleupon';
					break;
				
			}
		}
		
		//echo '<pre><code>'; var_dump($icon_sort); echo '</code></pre>';
	
		$this->icons      = $this->get_display_icons( $icon_sort );
		$this->appearance = genesis_get_option( 'general_appearance', 'genesis_simple_share' );
		$this->size       = genesis_get_option( 'general_size'      , 'genesis_simple_share' );
		
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		
		//add other actions as needed to create output
		
		add_filter( 'the_content', array( $this, 'icon_output' ), 15 );
		add_filter( 'the_excerpt', array( $this, 'icon_output' ), 15 );
		
		if( genesis_get_option( 'content_archive_limit' ) && 'full' == genesis_get_option( 'content_archive' ) ){
			add_action( 'genesis_post_content' , array( $this, 'before_entry_icons' ), 9  );
			add_action( 'genesis_entry_content', array( $this, 'before_entry_icons' ), 9  );
			add_action( 'genesis_post_content' , array( $this, 'after_entry_icons'  ), 11 );
			add_action( 'genesis_entry_content', array( $this, 'after_entry_icons'  ), 11 );
		}
		
	}
	
	/**
	 * Loads required scripts.
	 *
	 * @since 0.1.0
	 *
	 */
	function load_scripts() {
	
		if( $this->is_archive() && ! genesis_get_option( 'general_show_archive', 'genesis_simple_share' ) )
			return;
			
		
		//use wp_enqueue_script() and wp_enqueue_style() to load scripts and styles
		wp_enqueue_script( 'genesis-simple-share-plugin-js', 
							plugins_url( 'sharrre/jquery.sharrre.min.js', __FILE__ ), 
							array( 'jquery' ), 
							'0.1.0'
						);
						
		wp_enqueue_style( 	'genesis-simple-share-plugin-css', 
							plugins_url( 'css/share.css', __FILE__ ), 
							array(), 
							'0.1.0' 
						);
						
		wp_enqueue_style( 	'genesis-simple-share-genericons-css', 
							plugins_url( 'css/genericons.css', __FILE__ ), 
							array(), 
							'0.1.0' 
						);
						
		if( $this->is_archive() )
			wp_enqueue_script( 'genesis-simple-share-waypoint-js', 
							plugins_url( 'jquery-waypoints/waypoints.min.js', __FILE__ ), 
							array( 'jquery' ), 
							'0.1.0'
						);
		
	}
	
	/**
	 * Conditionally outputs icon output
	 * Alters the_content if icons are available
	 *
	 * @since 0.1.0
	 *
	 * @param   string $content the_content
	 *
	 * @returns string $content conditionally modified $content or unmodified $content if icons not available.
	 *
	 */
	function icon_output( $content ){
	
		if( $this->is_archive() && ! genesis_get_option( 'general_show_archive', 'genesis_simple_share' ) )
			return $content;
	
		if( ! $this->icons )
			return $content; //return early if no icons available
			
		switch( genesis_get_option( 'general_position', 'genesis_simple_share' ) ){
			
			case 'before_content':
				$content = $this->get_icon_output( 'before', $this->icons ) . $content;
				break;
				
			case 'after_content':
				$content .= $this->get_icon_output( 'after', $this->icons );
				break;
				
			case 'both':
				$content = $this->get_icon_output( 'before', $this->icons ) . $content . $this->get_icon_output( 'after', $this->icons );
				break;
				
		}
			
		return $content;
		
	}
	
	/**
	 * Conditionally outputs icon output
	 * Alters the_content if icons are available
	 *
	 * @since 0.1.0
	 *
	 * @param   string $content the_content
	 *
	 * @returns string $content conditionally modified $content or unmodified $content if icons not available.
	 *
	 */
	function before_entry_icons(){
	
		if( ! $this->is_archive() || ! genesis_get_option( 'general_show_archive', 'genesis_simple_share' ) )
			return;
	
		if( ! $this->icons )
			return; //return early if no icons available
			
		$position = genesis_get_option( 'general_position', 'genesis_simple_share' );
			
		if( 'before_content' == $position || 'both' == $position ) {
			
			echo $this->get_icon_output( 'before', $this->icons );
			
		}
		
	}
	
	/**
	 * Conditionally outputs icon output
	 * Alters the_content if icons are available
	 *
	 * @since 0.1.0
	 *
	 * @param   string $content the_content
	 *
	 * @returns string $content conditionally modified $content or unmodified $content if icons not available.
	 *
	 */
	function after_entry_icons(){
	
		if( ! $this->is_archive() || ! genesis_get_option( 'general_show_archive', 'genesis_simple_share' ) )
			return;
	
		if( ! $this->icons )
			return; //return early if no icons available
			
		$position = genesis_get_option( 'general_position', 'genesis_simple_share' );
			
		if( 'after_content' == $position || 'both' == $position ) {
			
			echo $this->get_icon_output( 'after', $this->icons );
			
		}
		
	}
	
	/**
	 * Check to see if any icons are set to show for the post type and return array of icons or false
	 *
	 * @since 0.1.0
	 *
	 * @param   string $post_type post type to check against
	 *
	 * @returns array/boolean     conditionally returns array of available icons or false.
	 *
	 */
	function get_display_icons( $icon_sort ){
	
		$icons = array();
		
		foreach( $icon_sort as $icon )
			if( genesis_get_option( $icon, 'genesis_simple_share' ) )
				$icons[] = $icon;
			
		if( ! empty( $icons ) )
			return $icons;
			
		return false;
		
	}
	
	/**
	 * Build output for the icons based on position
	 *
	 * @since 0.1.0
	 *
	 * @param   string $location before or after location
	 * @param   array  $icons    array of icons to use when building output
	 *
	 * @returns string           HTML and JS required to build the share icons.
	 *
	 */
	function get_icon_output( $location, $icons = array() ){
	
		if( empty( $icons ) || ( in_array( $location, array( 'before', 'after' ) ) && ! genesis_get_option( 'general_' . get_post_type(), 'genesis_simple_share' ) ) )
			return;
			
		$icons = empty( $icons ) ? $this->icons : $icons;
		
		if( empty( $icons ) )
			return;
		
		$id = get_the_ID();
		
		$scripts = '';
		$buttons = array();
		
		foreach( $icons as $icon ){
			
			$shares[] = $icon .': true';
			
			$div_id =  strtolower( $icon .'-'. $location .'-'. $id );
			
			$image = ( $image = genesis_get_image( array( 'format' => 'url', 'size' => 'full' ) ) ) ? $image : $this->get_first_image();
			
			$image = $image ? $image : genesis_get_option( 'image_url', 'genesis_simple_share' );
			$description = the_title_attribute( array( 'echo' => false ) );
			
			//media
			$button = 'twitter'   == $icon && ( $via = genesis_get_option( 'twitter_id', 'genesis_simple_share' ) ) ?  " twitter: { via: '". str_replace( '@', '', $via ) ."' }" : '';
			$button = 'pinterest' == $icon && $image ?  " pinterest: { media: '$image', description: '$description' }" : $button;
			
			if( $this->is_archive() )
				$scripts .= sprintf( "$('#%s').waypoint( function() {
										$('#%s').sharrre({
										  share: {
										    %s: true
										  },
										  urlCurl: '%s',
										  enableHover: false,
										  enableTracking: true,
										  buttons: { %s },
										  click: function(api, options){
										    api.simulateClick();
										    api.openPopup('%s');
										  }
										});
										},
										{ offset: 'bottom-in-view' });\n",
										$div_id,
										$div_id,
										$icon,
										plugins_url( 'sharrre/sharrre.php', __FILE__ ),
										$button,
										$icon
										);

			else
				$scripts .= sprintf( "$('#%s').sharrre({
										  share: {
										    %s: true
										  },
										  urlCurl: '%s',
										  enableHover: false,
										  enableTracking: true,
										  buttons: { %s },
										  click: function(api, options){
										    api.simulateClick();
										    api.openPopup('%s');
										  }
										});\n",
										$div_id,
										$icon,
										plugins_url( 'sharrre/sharrre.php', __FILE__ ),
										$button,
										$icon
										);
									
			switch( $icon ){
				
				case 'twitter' :
				
					$data_title = 'Tweet';
					break;
					
				case 'googlePlus' :
				
					$data_title = '+1';
					break;
					
				case 'facebook' :
				
					$data_title = 'Like';
					break;
					
				case 'pinterest' :
				
					$data_title = 'Pin';
					break;
					
				default:
				
					$data_title = 'Share';
				
			}
		
			$buttons[] = sprintf( '<div class="%s" id="%s" data-url="%s" data-text="%s" data-title="%s"></div>',
				$icon,
				$div_id,
				get_permalink( $id ),
				$description,
				$data_title
			);
				
		}
		
		$divs = implode( '', $buttons );
		
		$div_id = 'share-'. $location .'-' . $id;
		
		$div = sprintf( '<div class="share-%s share-%s share-%s" id="%s">%s</div>',
				$location,
				$this->appearance,
				$this->size,
				$div_id,
				$divs
			);
			
		$script = "
			<script type='text/javascript'>
				jQuery(document).ready(function($) {
					$scripts
				});
		</script>";
		
		return $div . $script;
			
	}
	
	/**
	 * Build output for the icons based on position
	 *
	 * @since 0.1.0
	 *
	 * @param   string $location before or after location
	 * @param   array  $icons    array of icons to use when building output
	 *
	 * @returns string           HTML and JS required to build the share icons.
	 *
	 */
	function get_hide_icon_output( $location, $icons = array() ){
		$id = get_the_ID();
			
			$div_id = 'share-'. $location .'-' . $id;
			
			$div = sprintf( '<div class="share-%s share-%s share-%s" id="%s" data-url="%s" data-text="%s" data-title="share"></div>',
				$location,
				$this->appearance,
				$this->size,
				$div_id,
				get_permalink( $id ),
				the_title_attribute( array( 'echo' => false ) )
			);
			
			$shares = array();
			$buttons = '';
			
			foreach( $icons as $icon => $args ){
				
				$shares[] = $icon .': true';
				
			}
			
			$share = implode( ',', $shares );
			
			$script = "
				<script type='text/javascript'>
					jQuery(document).ready(function($) {
						$('#$div_id').share({
						  share: {
						    $share
						  },
						  ". /*buttons: {
						    googlePlus: {size: 'tall', annotation:'bubble'},
						    facebook: {layout: 'box_count'},
						    twitter: {count: 'vertical', via: '_JulienH'}
						  },*/"
						  hover: function(api, options){
						    $(api.element).find('.buttons').show();
						  },
						  hide: function(api, options){
						    $(api.element).find('.buttons').hide();
						  },
						  enableTracking: true
						});
					});
			</script>";
			
			return $div . $script;
			
	}
	
	/**
	 * Checks to see if any archive including home, search, and the blog template is being used
	 *
	 * @since 0.1.0
	 *
	 * @returns boolean true if archive of any kind, else false
	 *
	 */
	function is_archive() {
	
		if( is_home() || is_archive() || is_search() || is_page_template('page_blog.php') )
			return true;
	
		return false;
	}
	
	/**
	 * Checks content for <img> tags and returns the src value of the first image tag
	 *
	 * @since 0.1.0
	 *
	 * @returns mixed
	 *
	 */
	function get_first_image( ) { 
	
		$content = get_the_content();
	 
		$output = preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $matches);  
		
		if( $output )
			return $matches[1][0]; 
		 
		return false;
		
	}  

		
}

/**
 * Loads the Share Class
 *
 * @since 0.1.0
 */
function genesis_simple_share() {
	global $Genesis_Simple_Share;
	
	$Genesis_Simple_Share = new Gensis_Simple_Share_Front_End;
	
}

genesis_simple_share();

/**
 * Gets the Share Icon output. 
 * Can specify the icons to use in the optional second param
 *
 * @since 0.1.0
 *
 * @param   string $location before or after location
 * @param   array  $icons    array of icons to use when building output
 *
 * @returns string           HTML and JS required to build the share icons.
 *
 */
function genesis_share_get_icon_output( $position, $icons = array() ) {
	global $Genesis_Simple_Share;
	
	return $Genesis_Simple_Share->get_icon_output( $position, $icons );
	
}

/**
 * Wrapper function for genesis_share_get_icon_output to echo output
 * Can specify the icons to use in the optional second param
 *
 * @since 0.1.0
 *
 * @param   string $location before or after location
 * @param   array  $icons    array of icons to use when building output
 *
 * @returns string           HTML and JS required to build the share icons.
 *
 */
function genesis_share_icon_output( $position, $icons = array() ) {
	
	echo genesis_share_get_icon_output( $position, $icons );
	
}
