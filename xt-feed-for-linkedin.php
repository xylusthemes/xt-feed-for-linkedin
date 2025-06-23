<?php
/**
 * Plugin Name:       XT Feed for LinkedIn
 * Plugin URI:        http://xylusthemes.com/plugins/xt-feed-for-linkedin/
 * Description:       XT Feed for LinkedIn allows you to seamlessly share your WordPress posts and custom post types on LinkedIn. Expand your reach, boost engagement, and automate content sharing with ease!
 * Version:           1.0.1
 * Author:            Xylus Themes
 * Author URI:        https://xylusthemes.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       xt-feed-for-linkedin
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 * @package    XT_Feed_Linkedin
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'XT_Feed_Linkedin' ) ) :

	/**
	 * Main XT Feed for LinkedIn class
	 */
	class XT_Feed_Linkedin {

		/** Singleton *************************************************************/
		/**
		 * XT_Feed_Linkedin The one true XT_Feed_Linkedin.
		 */
		private static $instance;
		public $common, $xt_feed_for_linkedin, $admin, $xtfefoli_authorize, $ajax, $sharing, $ucdata, $lfas;

		/**
		 * Main XT Feed for LinkedIn Instance.
		 *
		 * Insure that only one instance of XT_Feed_Linkedin exists in memory at any one time.
		 * Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 * @static object $instance
		 * @uses XT_Feed_Linkedin::setup_constants() Setup the constants needed.
		 * @uses XT_Feed_Linkedin::includes() Include the required files.
		 * @uses XT_Feed_Linkedin::laod_textdomain() load the language files.
		 * @see xtfefoli_xt_feed_for_linkedin()
		 * @return object| XT Feed for LinkedIn the one true XT Feed for LinkedIn.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof XT_Feed_Linkedin ) ) {
				self::$instance = new XT_Feed_Linkedin();
				self::$instance->setup_constants();

				add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
				add_action( 'plugins_loaded', array( self::$instance, 'load_xtfefoli_authorize_class' ), 20 );
				add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( self::$instance, 'xtfefoli_setting_doc_links' ) );

				self::$instance->includes();
				self::$instance->common     = new XT_Feed_Linkedin_Common();
				self::$instance->admin      = new XT_Feed_Linkedin_Admin();
				self::$instance->sharing    = new XT_Feed_Linkedin_Sharing();
				self::$instance->ajax       = new XT_Feed_Linkedin_Ajax();
				self::$instance->ucdata     = new XT_Feed_Linkedin_User_Company_Data();
				self::$instance->lfas       = new XT_Feed_Linkedin_Auto_Share();

			}
			return self::$instance;
		}

		/** Magic Methods *********************************************************/

		/**
		 * A dummy constructor to prevent XT_Feed_Linkedin from being loaded more than once.
		 *
		 * @since 1.0.0
		 * @see XT_Feed_Linkedin::instance()
		 * @see xtfefoli_xt_feed_for_linkedin()
		 */
		private function __construct() {
			/* Do nothing here */
		}

		/**
		 * A dummy magic method to prevent XT_Feed_Linkedin from being cloned.
		 *
		 * @since 1.0.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'xt-feed-for-linkedin' ), '1.0.1' );
		}

		/**
		 * A dummy magic method to prevent XT_Feed_Linkedin from being unserialized.
		 *
		 * @since 1.0.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'xt-feed-for-linkedin' ), '1.0.1' );
		}


		/**
		 * Setup plugins constants.
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function setup_constants() {

			// Plugin version.
			if ( ! defined( 'XTFEFOLI_VERSION' ) ) {
				define( 'XTFEFOLI_VERSION', '1.0.1' );
			}

			// Minimum Pro plugin version.
			if ( ! defined( 'XTFEFOLI_MIN_PRO_VERSION' ) ) {
				define( 'XTFEFOLI_MIN_PRO_VERSION', '1.0.0' );
			}

			// Plugin folder Path.
			if ( ! defined( 'XTFEFOLI_PLUGIN_DIR' ) ) {
				define( 'XTFEFOLI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin folder URL.
			if ( ! defined( 'XTFEFOLI_PLUGIN_URL' ) ) {
				define( 'XTFEFOLI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin root file.
			if ( ! defined( 'XTFEFOLI_PLUGIN_FILE' ) ) {
				define( 'XTFEFOLI_PLUGIN_FILE', __FILE__ );
			}

			// Options.
			if ( ! defined( 'XTFEFOLI_OPTIONS' ) ) {
				define( 'XTFEFOLI_OPTIONS', 'xtfefoli_linkedin_feedpress_options' );
			}

			// Pro plugin Buy now Link.
			if ( ! defined( 'XTFEFOLI_PLUGIN_BUY_NOW_URL' ) ) {
				define( 'XTFEFOLI_PLUGIN_BUY_NOW_URL', 'http://xylusthemes.com/plugins/xt-feed-for-linkedin/?utm_source=insideplugin&utm_medium=web&utm_content=sidebar&utm_campaign=freeplugin' );
			}
		}

		/**
		 * Include required files.
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function includes() {
			require_once XTFEFOLI_PLUGIN_DIR . 'includes/xt-feed-for-linkedin-scripts.php';
			require_once XTFEFOLI_PLUGIN_DIR . 'includes/admin/class-xt-feed-for-linkedin-common.php';
			require_once XTFEFOLI_PLUGIN_DIR . 'includes/admin/class-xt-feed-for-linkedin-admin.php';
			require_once XTFEFOLI_PLUGIN_DIR . 'includes/admin/class-xt-feed-for-linkedin-sharing.php';
			require_once XTFEFOLI_PLUGIN_DIR . 'includes/admin/class-xt-feed-for-linkedin-ajax-function.php';
			require_once XTFEFOLI_PLUGIN_DIR . 'includes/admin/class-xt-feed-for-linkedin-user-company-data.php';
			require_once XTFEFOLI_PLUGIN_DIR . 'includes/admin/class-xt-feed-for-linkedin-auto-share.php';		
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function load_textdomain() {

			load_plugin_textdomain(
				'xt-feed-for-linkedin',
				false,
				basename( dirname( __FILE__ ) ) . '/languages'
			);

		}

		/**
		 * Loads the linkedin authorize class
		 *
		 * @access public
		 * @since 1.5
		 * @return void
		 */
		public function load_xtfefoli_authorize_class() {

			if ( ! class_exists( 'LinkedIn_Feedpress_XTFEFOLI_Authorize', false ) ) {
				include_once XTFEFOLI_PLUGIN_DIR . 'includes/admin/class-xt-feed-for-linkedin-lf-authorize.php';
				global $xt_feed_for_linkedin;
				if ( class_exists( 'LinkedIn_Feedpress_XTFEFOLI_Authorize', false ) && ! empty( $xt_feed_for_linkedin ) ) {
					$xt_feed_for_linkedin->xtfefoli_authorize = new LinkedIn_Feedpress_XTFEFOLI_Authorize();
				}
			}
		}

		/**
		 * LF setting And docs link add in plugin page.
		 *
		 * @since 1.0
		 * @return void
		 */
		public function xtfefoli_setting_doc_links( $links ) {
			$xtfefoli_setting_doc_link = array(
				'lf-event-setting' => sprintf(
					'<a href="%s">%s</a>',
					esc_url( admin_url( 'admin.php?page=xt_feed_for_linkedin&tab=sharing_options' ) ),
					esc_html__( 'Setting', 'xt-feed-for-linkedin' )
				),
				'lf-event-docs' => sprintf(
					'<a target="_blank" href="%s">%s</a>',
					esc_url( 'https://docs.xylusthemes.com/docs/xt-feed-for-linkedin/' ),
					esc_html__( 'Docs', 'xt-feed-for-linkedin' )
				),
			);
			return array_merge( $links, $xtfefoli_setting_doc_link );
		}

	}

endif; // End If class exists check.

/**
 * The main function for that returns XT_Feed_Linkedin
 *
 * The main function responsible for returning the one true XT_Feed_Linkedin
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $xt_feed_for_linkedin = xtfefoli_xt_feed_for_linkedin(); ?>
 *
 * @since 1.0.0
 * @return object|XT_Feed_Linkedin The one true XT_Feed_Linkedin Instance.
 */
function xtfefoli_xt_feed_for_linkedin() {
	return XT_Feed_Linkedin::instance();
}

/**
 * Get Import events setting options
 *
 * @since 1.0
 * @param string $type Option type.
 * @return array|bool Options.
 */
function xtfefoli_get_options() {
	$xtfefoli_options = get_option( XTFEFOLI_OPTIONS );
	return $xtfefoli_options;
}

// Get XT_Feed_Linkedin Running.
global $xtfefoli_errors, $xtfefoli_success_msg, $xtfefoli_warnings, $xtfefoli_info_msg;
$xt_feed_for_linkedin = xtfefoli_xt_feed_for_linkedin();
$xtfefoli_errors = $xtfefoli_warnings = $xtfefoli_success_msg = $xtfefoli_info_msg = array();

