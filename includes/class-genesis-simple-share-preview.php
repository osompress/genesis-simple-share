<?php
/**
 * Creates the plugin front end output.
 *
 * @category Genesis Simple Share
 * @package  Output
 * @author   copyblogger
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 */

// * Prevent direct access to the plugin
if ( ! defined( 'ABSPATH' ) ) {
	die( esc_html__( 'Sorry, you are not allowed to access this page directly.', 'genesis-simple-share' ) );
}

/**
 * Generates output on the front end of a Premise enabled site
 *
 * @category Genesis Simple Share
 * @package Output
 *
 * @since 0.1.0
 */
class Genesis_Simple_Share_Preview {

	/**
	 * Icons.
	 *
	 * @var array
	 */
	public $icons;

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
	 * Create front end output.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

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

		$this->icons      = $this->get_display_icons( $icon_sort );
		$this->appearance = genesis_get_option( 'general_appearance', 'genesis_simple_share' );
		$this->size       = genesis_get_option( 'general_size', 'genesis_simple_share' );

	}

	/**
	 * Check to see if any icons are set to show for the post type and return array of icons or false
	 *
	 * @since 0.1.0
	 *
	 * @param string $icon_sort post type to check against.
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
	 * Build output for the icons based on position
	 *
	 * @since 0.1.0
	 *
	 * @param   string $location before or after location.
	 * @param   array  $icons    array of icons to use when building output.
	 *
	 * @returns string           HTML and JS required to build the share icons.
	 */
	public function get_icon_output( $location, $icons = array() ) {

		$icons = empty( $icons ) ? $this->icons : $icons;

		if ( empty( $icons ) ) {
			return;
		}

		$id = 'preview';

		$scripts = '';
		$buttons = array();

		foreach ( $icons as $icon ) {

			$shares[] = $icon . ': true';

			$div_id = strtolower( $icon . '-' . $location . '-' . $id );

			// Disable the counter if the option is set or is the Facebook.
			$disable_count = genesis_get_option( 'general_disable_count', 'genesis_simple_share' ) || ( 'facebook' === $icon ) ? 'disableCount: true,' : '';

			// media.
			$button = '';

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
					// Translators: %s is the text the Share icon.
					$data_reader = sprintf( esc_html__( 'on %s', 'genesis-simple-share' ), $icon );

			}

			$buttons[] = sprintf(
				'<div class="%s" id="%s" data-url="%s" data-text="%s" data-title="%s" data-reader="%s"></div>',
				$icon,
				$div_id,
				get_site_url(),
				get_bloginfo( 'name' ),
				$data_title,
				$data_reader
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
				'<div class="share-%s share-%s share-%s" id="%s" data-url="%s" data-text="%s" data-title="share"></div>',
				$location,
				$this->appearance,
				$this->size,
				$div_id,
				get_permalink( $id ),
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

}

/**
 * Global object.
 */
function genesis_simple_share_preview() {
	global $genesis_simple_share;

	$genesis_simple_share = new Genesis_Simple_Share_Preview();

}

genesis_simple_share_preview();


/**
 * Output the icons.
 *
 * @param string $position Before or after location.
 * @param array  $icons    array of icons to use when building output.
 */
function genesis_share_get_icon_preview_output( $position, $icons = array() ) {
	global $genesis_simple_share;

	return $genesis_simple_share->get_icon_output( $position, $icons );

}

/**
 * Output the icons.
 *
 * @param string $position Before or after location.
 * @param array  $icons    array of icons to use when building output.
 */
function genesis_share_icon_preview_output( $position, $icons = array() ) {

	echo genesis_share_get_icon_preview_output( $position, $icons ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

}
