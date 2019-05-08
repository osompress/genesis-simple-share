<?php
/**
 * Creates the plugin front end output.
 *
 * @category Genesis Simple Share
 * @author   copyblogger
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 *
 * @package genesis-simple-share
 */

/* Prevent direct access to the plugin */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Sorry, you are not allowed to access this page directly.' );
}

/**
 * Generates output on the front end of a Premise enabled site
 *
 * @category Genesis Simple Share
 * @package Output
 *
 * @since 0.1.0
 */
class Genesis_Simple_Share_Front_End {

	/**
	 * Icons.
	 *
	 * @var array
	 */
	public $icons;

	/**
	 * Icon text.
	 *
	 * @var array
	 */
	public $icon_text = array();

	/**
	 * Appearance.
	 *
	 * @var string
	 */
	public $appearance;

	/**
	 * Size.
	 *
	 * @var string
	 */
	public $size;

	/**
	 * Archive.
	 *
	 * @var string
	 */
	public $archive;

	/**
	 * Location.
	 *
	 * @var array
	 */
	public $locations = array();

	/**
	 * Create front end output.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		$this->set_icons();
		$this->set_icon_text();
		$this->set_appearance();
		$this->set_size();

		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ), 5 );

		add_action( 'genesis_loop', array( $this, 'start_icon_actions' ), 5 );
		add_action( 'genesis_loop', array( $this, 'end_icon_actions' ), 15 );

	}

	/**
	 * Loads required scripts.
	 *
	 * @since 0.1.0
	 */
	public function load_scripts() {

		$url = GENESIS_SIMPLE_SHARE_URL;

		// use wp_enqueue_script() and wp_enqueue_style() to load scripts and styles.
		wp_register_script(
			'genesis-simple-share-plugin-js',
			$url . '/assets/js/sharrre/jquery.sharrre.min.js',
			array( 'jquery' ),
			'0.1.0',
			false
		);

		wp_register_style(
			'genesis-simple-share-plugin-css',
			$url . '/assets/css/share.min.css',
			array(),
			'0.1.0'
		);

		wp_register_style(
			'genesis-simple-share-genericons-css',
			$url . '/assets/css/genericons.min.css',
			array(),
			'0.1.0'
		);

		wp_register_script(
			'genesis-simple-share-waypoint-js',
			$url . '/assets/js/waypoints.min.js',
			array( 'jquery' ),
			'0.1.0',
			false
		);

		if ( $this->is_archive() && ! genesis_get_option( 'general_show_archive', 'genesis_simple_share' ) ) {
			$this->archive = 'no-load';
			return;
		}

		// use wp_enqueue_script() and wp_enqueue_style() to load scripts and styles.
		wp_enqueue_script( 'genesis-simple-share-plugin-js' );
		wp_enqueue_style( 'genesis-simple-share-plugin-css' );
		wp_enqueue_style( 'genesis-simple-share-genericons-css' );
		wp_enqueue_script( 'genesis-simple-share-waypoint-js' );

	}

	/**
	 * Load the icon actions/filters only within the genesis_loop hook
	 *
	 * @since 0.2.0
	 */
	public function start_icon_actions() {

		add_filter( 'the_content', array( $this, 'icon_output' ), 15 );
		add_filter( 'the_excerpt', array( $this, 'icon_output' ), 15 );

		if ( genesis_get_option( 'content_archive_limit' ) && 'full' === genesis_get_option( 'content_archive' ) && $this->is_archive() ) {
			add_action( 'genesis_post_content', array( $this, 'before_entry_icons' ), 9 );
			add_action( 'genesis_entry_content', array( $this, 'before_entry_icons' ), 9 );
			add_action( 'genesis_post_content', array( $this, 'after_entry_icons' ), 11 );
			add_action( 'genesis_entry_content', array( $this, 'after_entry_icons' ), 11 );
		}

	}

	/**
	 * Remove the icon actions/filters after the loop has run
	 *
	 * @since 0.2.0
	 */
	public function end_icon_actions() {

		remove_filter( 'the_content', array( $this, 'icon_output' ), 15 );
		remove_filter( 'the_excerpt', array( $this, 'icon_output' ), 15 );

		remove_action( 'genesis_post_content', array( $this, 'before_entry_icons' ), 9 );
		remove_action( 'genesis_entry_content', array( $this, 'before_entry_icons' ), 9 );
		remove_action( 'genesis_post_content', array( $this, 'after_entry_icons' ), 11 );
		remove_action( 'genesis_entry_content', array( $this, 'after_entry_icons' ), 11 );

	}

	/**
	 * Conditionally outputs icon output
	 * Alters the_content if icons are available
	 *
	 * @since 0.1.0
	 *
	 * @param string $content the_content.
	 *
	 * @returns string $content conditionally modified $content or unmodified $content if icons not available.
	 */
	public function icon_output( $content ) {

		if ( 'no-load' === $this->archive ) {
			return $content;
		}

		if ( ! $this->icons ) {
			return $content; // return early if no icons available.
		}

		switch ( genesis_get_option( 'general_position', 'genesis_simple_share' ) ) {

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
	 */
	public function before_entry_icons() {

		if ( 'no-load' === $this->archive ) {
			return;
		}

		if ( ! $this->icons ) {
			return; // return early if no icons available.
		}

		$position = genesis_get_option( 'general_position', 'genesis_simple_share' );

		if ( 'before_content' === $position || 'both' === $position ) {

			// phpcs:ignore
			echo $this->get_icon_output( 'before', $this->icons );

		}

	}

	/**
	 * Conditionally outputs icon output
	 * Alters the_content if icons are available
	 *
	 * @since 0.1.0
	 */
	public function after_entry_icons() {

		if ( 'no-load' === $this->archive ) {
			return;
		}

		if ( ! $this->icons ) {
			return; // return early if no icons available.
		}

		$position = genesis_get_option( 'general_position', 'genesis_simple_share' );

		if ( 'after_content' === $position || 'both' === $position ) {

			// phpcs:ignore
			echo $this->get_icon_output( 'after', $this->icons );

		}

	}

	/**
	 * Sets the $icon_text property.
	 * If the $icon_text attribute it set it will use that,
	 * otherwise it will use the default value.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 *
	 * @param array $icon_text (default: array()).
	 *
	 * @return void
	 */
	public function set_icon_text( $icon_text = array() ) {
		$this->icon_text = $icon_text ? $icon_text : array(
			'googlePlus'  => array(
				'label' => __( 'Share on Google Plus', 'genesis-simple-share' ),
				// translators: Number of shares.
				'count' => __( '%s shares on Google Plus', 'genesis-simple-share' ),
			),
			'facebook'    => array(
				'label' => __( 'Share on Facebook', 'genesis-simple-share' ),
				// translators: Number of shares.
				'count' => __( '%s shares on Facebook', 'genesis-simple-share' ),
			),
			'twitter'     => array(
				'label' => __( 'Tweet this', 'genesis-simple-share' ),
				// translators: Number of shares.
				'count' => __( '%s Tweets', 'genesis-simple-share' ),
			),
			'pinterest'   => array(
				'label' => __( 'Pin this', 'genesis-simple-share' ),
				// translators: Number of shares.
				'count' => __( '%s Pins', 'genesis-simple-share' ),
			),
			'linkedin'    => array(
				'label' => __( 'Share on LinkedIn', 'genesis-simple-share' ),
				// translators: Number of shares.
				'count' => __( '%s shares on LinkedIn', 'genesis-simple-share' ),
			),
			'stumbleupon' => array(
				'label' => __( 'Share on StumbleUpon', 'genesis-simple-share' ),
				// translators: Number of shares.
				'count' => __( '%s shares on StumbleUpon', 'genesis-simple-share' ),
			),
		);
	}

	/**
	 * Sets the $appearance property.
	 * If the $appearance attribute it set it will use that,
	 * otherwise it will use the option value.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 * @param string $appearance (default: '').
	 * @return void
	 */
	public function set_appearance( $appearance = '' ) {
		$this->appearance = $appearance ? $appearance : genesis_get_option( 'general_appearance', 'genesis_simple_share' );
	}

	/**
	 * Sets the $size property.
	 * If the $size attribute it set it will use that,
	 * otherwise it will use the option value.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 * @param  string $size Size.
	 * @return void
	 */
	public function set_size( $size = '' ) {
		$this->size = $size ? $size : genesis_get_option( 'general_size', 'genesis_simple_share' );
	}

	/**
	 * Sets the $icons property.
	 * If the $icons attribute it set it will use that,
	 * otherwise it will check options then sort the order and get the icon display
	 * using the get_display_icons() method.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 * @param array $icons Icons.
	 * @return void
	 */
	public function set_icons( $icons = array() ) {

		if ( $icons ) {
			$this->icons = $icons;
			return;
		}

		$icons = get_option(
			'genesis_simple_share_sort',
			array(
				'main' => 'genesis_simple_share_google_plus,genesis_simple_share_facebook,genesis_simple_share_twitter,genesis_simple_share_pinterest,genesis_simple_share_linkedin,genesis_simple_share_stumbleupon',
			)
		);

		$icons = explode( ',', $icons['main'] );

		$icon_sort = array();

		foreach ( $icons as $icon ) {
			switch ( $icon ) {

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

		$this->icons = $this->get_display_icons( $icon_sort );

	}

	/**
	 * Check to see if any icons are set to show for the post type and return array of icons or false
	 *
	 * @since 0.1.0
	 *
	 * @param   string $icon_sort Icon position.
	 */
	public function get_display_icons( $icon_sort ) {

		$icons = array();

		foreach ( $icon_sort as $icon ) {
			if ( genesis_get_option( $icon, 'genesis_simple_share' ) ) {
				$icons[] = $icon;
			}
		}

		if ( ! empty( $icons ) ) {
			return $icons;
		}

			return false;

	}

	/**
	 * Gets the $icons property.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 */
	public function get_icons() {
		return $this->icons;
	}

	/**
	 * Build output for the icons based on position
	 *
	 * @since 0.1.0
	 *
	 * @param   string  $location   before or after location.
	 * @param   array   $icons      array of icons to use when building output.
	 * @param   boolean $force_show forces the output even if it is duplicate ID.
	 * @param   string  $url        Alternate URL to be used instead of post permalink. This value will be shared and also be the URL checked for social shares.
	 *
	 * @returns string           HTML and JS required to build the share icons.
	 */
	public function get_icon_output( $location, $icons = array(), $force_show = false, $url = '' ) {

		if ( is_feed() ) {
			return;
		}

		$filter = ( 'the_excerpt' === current_filter() ) ? 'excerpt' : '';

		if ( ! $force_show && in_array( $location . $filter . '-' . get_the_ID(), $this->locations, true ) ) {
			return '<!-- Genesis Simple Share error: This location ( ' . $location . ' ) has already been used. -->';
		}

		if ( empty( $icons ) ||
			(
				in_array( $location, array( 'before', 'after' ), true ) &&
				(
					! genesis_get_option( 'general_' . get_post_type(), 'genesis_simple_share' ) ||
					get_post_meta( get_the_ID(), '_disable_gss', true )
				)
			)
		) {
			return;
		}

		$icons = empty( $icons ) ? $this->icons : $icons;

		if ( empty( $icons ) ) {
			return;
		}

		$id = get_the_ID();

		$opt = genesis_get_custom_field( '_gss_alternate_url' );
		$url = ( empty( $url ) && $opt ) ? esc_url( $opt ) : $url;

		$url = ( empty( $url ) && genesis_get_custom_field( '_gss_alternate_url' ) === $opt ) ? esc_url( $opt ) : $url;

		$scripts = '';
		$buttons = array();

		foreach ( $icons as $icon ) {

			$shares[] = $icon . ': true';

			$div_id = strtolower( $icon . '-' . $location . '-' . $id );

			$image = genesis_get_image(
				array(
					'format' => 'url',
					'size'   => 'full',
				)
			);

			$image = ( $image ) ? $image : $this->get_first_image();

			$image       = $image ? $image : genesis_get_option( 'image_url', 'genesis_simple_share' );
			$description = the_title_attribute( array( 'echo' => false ) );

			// media.
			$via = genesis_get_option( 'twitter_id', 'genesis_simple_share' );

			$button = ( 'twitter' === $icon && $via ) ? " twitter: { via: '" . str_replace( '@', '', $via ) . "' }" : '';
			$button = 'pinterest' === $icon && $image ? " pinterest: { media: '$image', description: '$description' }" : $button;

			// Disable the counter if the option is set or is the Facebook.
			$disable_count = genesis_get_option( 'general_disable_count', 'genesis_simple_share' ) || ( 'facebook' === $icon ) ? 'disableCount: true,' : '';

			if ( $this->is_archive() ) {

				$scripts .= sprintf(
					'if ( $.fn.waypoint ) {
										$("#%1$s").waypoint( function() {
										$("#%1$s").sharrre({
										  share: {
										    %2$s: true
										  },
										  urlCurl: "%3$s",
										  enableHover: false,
										  enableTracking: true,
										  %4$s
										  buttons: { %5$s },
										  click: function(api, options){
										    api.simulateClick();
										    api.openPopup("%6$s");
										  }
										});
										},
										{ offset: "bottom-in-view" });
									} else {
										$("#%1$s").sharrre({
										  share: {
										    %2$s: true
										  },
										  urlCurl: "%3$s",
										  enableHover: false,
										  enableTracking: true,
										  %4$s
										  buttons: { %5$s },
										  click: function(api, options){
										    api.simulateClick();
										    api.openPopup("%6$s");
										  }
										});
									}%7$s',
					$div_id,
					$icon,
					GENESIS_SIMPLE_SHARE_URL . '/assets/js/sharrre/sharrre.php',
					$disable_count,
					$button,
					$icon,
					PHP_EOL
				);

			} else {

				$scripts .= sprintf(
					"$('#%s').sharrre({
										  share: {
										    %s: true
										  },
										  urlCurl: '%s',
										  enableHover: false,
										  enableTracking: true,
										  %s
										  buttons: { %s },
										  click: function(api, options){
										    api.simulateClick();
										    api.openPopup('%s');
										  }
										});\n",
					$div_id,
					$icon,
					GENESIS_SIMPLE_SHARE_URL . '/assets/js/sharrre/sharrre.php',
					$disable_count,
					$button,
					$icon
				);

			}

			$data_reader = '';

			switch ( $icon ) {

				case 'twitter':
					$data_title = __( 'Tweet', 'genesis-simple-share' );
					break;

				case 'pinterest':
					$data_title = __( 'Pin', 'genesis-simple-share' );
					break;

				default:
					$data_title = __( 'Share', 'genesis-simple-share' );

			}

			$buttons[] = sprintf(
				'<div class="%s" id="%s" data-url="%s" data-urlalt="%s" data-text="%s" data-title="%s" data-reader="%s" data-count="%s"></div>',
				$icon,
				$div_id,
				$url ? $url : get_permalink( $id ),
				wp_get_shortlink( $id ),
				$description,
				$data_title,
				$this->icon_text[ $icon ]['label'],
				$this->icon_text[ $icon ]['count']
			);

		}

		$divs = implode( '', $buttons );

		$div_id = 'share-' . $location . '-' . $id;

		$div = sprintf(
			'<div class="share-%s share-%s share-%s" id="%s">%s</div>',
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

		$this->locations[] = $location . $filter . '-' . get_the_ID();

		return $div . $script;

	}
	/**
	 * Build output for the icons based on position
	 *
	 * @since 0.1.0
	 *
	 * @param   string $location before or after location.
	 * @param   array  $icons    array of icons to use when building output.
	 *
	 * @returns string           HTML and JS required to build the share icons.
	 */
	public function get_hide_icon_output( $location, $icons = array() ) {
		$id = get_the_ID();

		$div_id = 'share-' . $location . '-' . $id;

		$div = sprintf(
			'<div class="share-%s share-%s share-%s" id="%s" data-url="%s" data-urlalt="%s" data-text="%s" data-title="share"></div>',
			$location,
			$this->appearance,
			$this->size,
			$div_id,
			get_permalink( $id ),
			wp_get_shortlink( $id ),
			the_title_attribute( array( 'echo' => false ) )
		);

		$shares  = array();
		$buttons = '';

		foreach ( $icons as $icon => $args ) {

			$shares[] = $icon . ': true';

		}

		$share = implode( ',', $shares );

		$script = "
				<script type='text/javascript'>
					jQuery(document).ready(function($) {
						$('#$div_id').share({
						  share: {
						    $share
						  },
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
	 */
	public function is_archive() {

		/**
		 * Allows plugins and themes to define archive pages which may not normally be caught by the plugin logic.
		 * Default is false, return a true value to cause the archive options, e.g. waypoints script, to load.
		 *
		 * @since 0.1.0
		 */
		if ( apply_filters( 'genesis_simple_share_is_archive', false ) ) {
			return true;
		}

		if ( is_home() || is_archive() || is_search() || is_page_template( 'page_blog.php' ) || is_front_page() || is_customize_preview() ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks content for <img> tags and returns the src value of the first image tag
	 *
	 * @since 0.1.0
	 *
	 * @returns mixed
	 */
	public function get_first_image() {

		$content = get_the_content();

		$output = preg_match_all( '/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $matches );

		if ( $output ) {
			return $matches[1][0];
		}

		return false;

	}


}

/**
 * Loads the Share Class
 *
 * @since 0.1.0
 */
function genesis_simple_share() {
	global $genesis_simple_share;

	if ( empty( $genesis_simple_share ) ) {

		$genesis_simple_share = new Genesis_Simple_Share_Front_End();

	}

	return $genesis_simple_share;

}

genesis_simple_share();

/**
 * Gets the Share Icon output.
 * Can specify the icons to use in the optional second param
 *
 * @since 0.1.0
 *
 * @param   string  $position   before or after location.
 * @param   array   $icons      array of icons to use when building output.
 * @param   boolean $force_show forces the output even if it is duplicate ID.
 * @param   string  $url        Alternate URL to be used instead of post permalink. This value will be shared and also be the URL checked for social shares.
 *
 * @returns string           HTML and JS required to build the share icons.
 */
function genesis_share_get_icon_output( $position, $icons = array(), $force_show = false, $url = '' ) {

	return genesis_simple_share()->get_icon_output( $position, $icons, $force_show, $url );

}

/**
 * Wrapper function for genesis_share_get_icon_output to echo output
 * Can specify the icons to use in the optional second param
 *
 * @since 0.1.0
 *
 * @param   string  $position   before or after location.
 * @param   array   $icons      array of icons to use when building output.
 * @param   boolean $force_show forces the output even if it is duplicate ID.
 * @param   string  $url        Alternate URL to be used instead of post permalink. This value will be shared and also be the URL checked for social shares.
 *
 * @returns null
 */
function genesis_share_icon_output( $position, $icons = array(), $force_show = false, $url = '' ) {

	// phpcs:ignore
	echo genesis_share_get_icon_output( $position, $icons, $force_show, $url );

}
