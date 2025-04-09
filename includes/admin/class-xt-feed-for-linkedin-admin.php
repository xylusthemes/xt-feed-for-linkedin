<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package     XT_Feed_Linkedin
 * @subpackage  XT_Feed_Linkedin/admin
 * @copyright   Copyright (c) 2016, Rajat Patel
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @package     XT_Feed_Linkedin
 * @subpackage  XT_Feed_Linkedin/admin
 * @author     Rajat Patel <prajat21@gmail.com>
 */
class XT_Feed_Linkedin_Admin {

	/**
	 * Admin page URL
	 *
	 * @var string
	 */
	public $adminpage_url;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->adminpage_url  = admin_url( 'admin.php?page=xt_feed_for_linkedin' );

        add_action( 'admin_menu', array( $this, 'xtfefoli_add_menu_pages' ) );
		add_filter( 'submenu_file', array( $this, 'get_selected_tab_submenu_xtfefoli' ) );
        add_action( 'add_meta_boxes', array( $this, 'xtfefoli_add_linkedin_feedpres_meta_boxes' ) );
        add_action( 'admin_init', array( $this, 'xtfefoli_handle_so_settings_submit' ), 99 );
        add_action( 'xtfefoli_notice', array( $this, 'xtfefoli_display_notices' ) );
		
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$page    = isset( $_GET['page'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) : '';
		if( 'xt_feed_for_linkedin' == $page ){
			add_action( 'xtfefoli_notice', array( $this, 'xtfefoli_token_expiring_notice' ) );
		}else{
			add_action( 'admin_notices', array( $this, 'xtfefoli_token_expiring_notice' ) );
		}
	}

    /**
	 * Create the Admin menu and submenu and assign their links to global varibles.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function xtfefoli_add_menu_pages() {

		add_menu_page( __( 'XT Feed for LinkedIn', 'xt-feed-for-linkedin' ), __( 'XT Feed for LinkedIn', 'xt-feed-for-linkedin' ), 'manage_options', 'xt_feed_for_linkedin', array( $this, 'xtfefoli_admin_page' ), 'dashicons-linkedin', '24' );
		global $submenu;	
		$submenu['xt_feed_for_linkedin'][] = array( __( 'XT Feed for LinkedIn', 'xt-feed-for-linkedin' ), 'manage_options', admin_url( 'admin.php?page=xt_feed_for_linkedin&tab=general' ) );
		$submenu['xt_feed_for_linkedin'][] = array( __( 'Sharing Options', 'xt-feed-for-linkedin' ), 'manage_options', admin_url( 'admin.php?page=xt_feed_for_linkedin&tab=sharing_options' ) );
		$submenu['xt_feed_for_linkedin'][] = array( __( 'Support & Help', 'xt-feed-for-linkedin' ), 'manage_options', admin_url( 'admin.php?page=xt_feed_for_linkedin&tab=support' ) );
	}

	/**
	 * Load Admin page.
	 *
	 * @since 1.0
	 * @return void
	 */

	function xtfefoli_admin_page(){
        global $xt_feed_for_linkedin;
		
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $active_tab = isset( $_GET['tab'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) : 'general';
        $gettab     = ucwords( str_replace( '_', ' ', $active_tab ) );
        if( $active_tab == 'general' || $active_tab == 'support' || $active_tab == 'sharing_options' ){
            $gettab     = ucwords( str_replace( '_', ' ', $gettab ) );
            $page_title = $gettab;
        }
        
        $posts_header_result = $xt_feed_for_linkedin->common->xtfefoli_render_common_header( $page_title );
        ?>
        
        <div class="lf-container" >
            <div class="lf-wrap" >
                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">
                        <?php
                            do_action( 'xtfefoli_notice' ); 
                        ?>
                        <div class="ajax_xtfefoli_notice"></div>
                        <div id="postbox-container-2" class="postbox-container">
                            <div class="lf-app">
                                <div class="lf-tabs">
                                    <div class="tabs-scroller">
                                        <div class="var-tabs var-tabs--item-horizontal var-tabs--layout-horizontal-padding">
											<div class="var-tabs__tab-wrap var-tabs--layout-horizontal">
												<a href="<?php echo esc_url( admin_url( 'admin.php?page=xt_feed_for_linkedin&tab=general' ) ); ?>" class="var-tab <?php echo $active_tab == 'general' ? 'var-tab--active' : 'var-tab--inactive'; ?>">
													<span class="tab-label"><?php esc_attr_e( 'General', 'xt-feed-for-linkedin' ); ?></span>
												</a>
												<a href="<?php echo esc_url( admin_url( 'admin.php?page=xt_feed_for_linkedin&tab=sharing_options' ) ); ?>" class="var-tab <?php echo $active_tab == 'sharing_options' ? 'var-tab--active' : 'var-tab--inactive'; ?>">
													<span class="tab-label"><?php esc_attr_e( 'Sharing Options', 'xt-feed-for-linkedin' ); ?></span>
												</a>
												<a href="<?php echo esc_url( admin_url( 'admin.php?page=xt_feed_for_linkedin&tab=support' ) ); ?>" class="var-tab <?php echo $active_tab == 'support' ? 'var-tab--active' : 'var-tab--inactive'; ?>">
													<span class="tab-label"><?php esc_attr_e( 'Support & Help', 'xt-feed-for-linkedin' ); ?></span>
												</a>
											</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <?php
                            $valid_tabs = [ 'general', 'support', 'sharing_options' ];
                            if( $active_tab == 'general' ){
                                require_once XTFEFOLI_PLUGIN_DIR . '/templates/admin/xt-feed-for-linkedin-general.php';
                            }elseif( $active_tab == 'sharing_options' ){
                                require_once XTFEFOLI_PLUGIN_DIR . '/templates/admin/xt-feed-for-linkedin-sharing-options.php';
                            }elseif( $active_tab == 'support' ){
                                require_once XTFEFOLI_PLUGIN_DIR . '/templates/admin/xt-feed-for-linkedin-support.php';
                            }
                            ?>
                        </div>
                    </div>
                    <br class="clear">
                </div>
            </div>
        </div>
        <?php
        $posts_footer_result = $xt_feed_for_linkedin->common->xtfefoli_render_common_footer();
    }

	/**
	 * Tab Submenu got selected.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function get_selected_tab_submenu_xtfefoli( $submenu_file ) {
		global $xt_feed_for_linkedin;

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['page'] ) && esc_attr( sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) == 'xt_feed_for_linkedin' ) {
			$allowed_tabs = array( 'general', 'support', 'sharing_options' );

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$tab = isset( $_GET['tab'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) : 'general';
			if ( in_array( $tab, $allowed_tabs ) ) {
				$submenu_file = admin_url( 'admin.php?page=xt_feed_for_linkedin&tab=' . $tab );
			}
		}
		return $submenu_file;
	}
	

    /**
	 *  Add Meta box for team link meta box.
	 *
	 * @since 1.0.0
	 */
	public function xtfefoli_add_linkedin_feedpres_meta_boxes() {
		$if_get_option = xtfefoli_get_options();
		if ( is_array( $if_get_option ) && isset( $if_get_option['xtfefoli_linkedin_feedpress_cpts'] ) ) {
			$selected_cpts = $if_get_option['xtfefoli_linkedin_feedpress_cpts'];

			add_meta_box(
				'linkedin_feedpress_metabox',
				esc_attr__( 'XT Feed for LinkedIn', 'xt-feed-for-linkedin' ),
				array( $this, 'render_linkedin_feedpress_meta_boxes' ),
				$selected_cpts,
				'normal',
				'high'
			);
		}
	}
    
    /**
	 * Event meta box render
	 *
	 * @param object $post Post object.
	 * @return void
	 */
	public function render_linkedin_feedpress_meta_boxes( $post ) {
        global $xt_feed_for_linkedin;

		$post_id            = $post->ID;
		$xtfefoli_post_type = $post->post_type;
		$title              = $post->post_title;
		$description        = $post->post_content;

		if ( $title == '' || $description == '' ) {
			echo '<div style="margin: 5px 0 0 5px; color: #0073aa; font-weight: bold;">
				' . esc_html__( 'Title or Description should not be empty. Please save the post first before using this feature.', 'xt-feed-for-linkedin' ) . '
				</div>';
			return false;
		}
			
			
		$original_lfgsm    = esc_textarea("[POST_TITLE]\n\n Read more: [POST_LINK]\n\n [POST_EXCERPT]\n\n By [POST_AUTHOR] |  [WEBSITE_TITLE]");
		$lfgsm_option      = get_option( 'xtfefoli_global_sharing_message', $original_lfgsm );
		$xtfefoli_options  = get_option( XTFEFOLI_OPTIONS, true );
		$xtfefoli_bddspol  = isset( $xtfefoli_options['xtfefoli_bddspol'] ) ? $xtfefoli_options['xtfefoli_bddspol'] : '';

		$check_post_shared = get_post_meta( $post_id, '_xtfefoli_dont_share_post_linkedin', true );

		if( $check_post_shared == true ){
			$xtfefoli_bddspol = 'on';
		}

		// Use nonce for verification.
		wp_nonce_field('xtfefoli_linkedin_feedpress_meta_box_nonce', 'xtfefoli_linkedin_feedpress_meta_box_nonce');
		$get_shared_histories = get_post_meta( $post_id, '_xtfefoli_sended_on_linkedin', true );

		?>
		<div style="clear: both;"></div>
		<div style="margin:15px 0;">
			<input id="xtfefoli_dont_share_post_on_linkedin" type="checkbox" name="xtfefoli_dont_share_post_on_linkedin" value="yes" <?php if( $xtfefoli_bddspol == 'on' ){ echo "checked"; } ?> >
			<label for="xtfefoli_dont-sent-to-linkedin" style="font-size: 14px; color: #333;">
				<strong><?php esc_attr_e( 'Don\'t Share This Post', 'xt-feed-for-linkedin' ); ?></strong>
			</label>
		</div>
		<div class="xtfefoli_inside" style="margin:10px;">
			<input type="hidden" id="xtfefoli_post_type" name="xtfefoli_post_type" value="<?php echo esc_attr( $xtfefoli_post_type ); ?>">
			<div class="xtfefoli_custom-linkedin-metabox-setting" >
				<label for="xtfefoli_custom-share-message" style="display: block; font-weight: bold; margin-bottom: 10px;"><?php esc_attr_e( 'Share Message', 'xt-feed-for-linkedin' ); ?></label>
				<textarea rows="8" name="xtfefoli_global_sharing_message" id="xtfefoli_custom-share-message" style="width: 100%; padding: 5px;"><?php echo esc_attr( $lfgsm_option ); ?></textarea>
			</div>
			<div style="margin-top: 15px;" >
				<a href="javascript:void(0)" class="xtfefoli_button" id="xtfefoli_share_cpt_button" >
					<?php esc_attr_e( 'Share Now', 'xt-feed-for-linkedin' ); ?>
				</a>
			</div>
			<div id="if_meta_box_notice" style="margin-top:15px;"></div>
		</div>
		<div style="margin:10px;">
			<?php 
				if( !empty( $get_shared_histories ) ){
					?>
					<div style="margin-top: 15px;">
						<strong><?php esc_attr_e( 'Post Shared History:', 'xt-feed-for-linkedin' ); ?></strong>
						<?php
						foreach( $get_shared_histories as $get_sh ){
							$post_url = esc_url( 'https://www.linkedin.com/feed/update/' . $get_sh['id'] );
							$formatted_date = gmdate( 'Y-m-d H:i:s', $get_sh['shared_post_datetime'] );				
							echo '<div style="margin: 5px 0 0 5px;">
									<strong><a href="' . esc_url( $post_url ) . '" target="_blank" style="color: #0049b3; text-decoration: none;">' . esc_attr__( 'Check out the shared post ', 'xt-feed-for-linkedin' ) . '</a> ( ' . esc_html( $formatted_date ) . ' ) </strong>
									</div>';
						}
						?>
					</div>
					<?php
				}
			?>
		</div>
		<?php
	}


    /**
	 * Process Saving liknedin feedpress sharing option
	 *
	 * @since    1.0.0
	 */
	public function xtfefoli_handle_so_settings_submit() {
		global $xtfefoli_errors, $xtfefoli_success_msg;
		if ( isset( $_POST['xtfefoli_so_action'] ) && 'xtfefoli_save_so_settings' === sanitize_text_field( wp_unslash( $_POST['xtfefoli_so_action'] ) ) && check_admin_referer( 'xtfefoli_so_setting_form_nonce_action', 'xtfefoli_so_setting_form_nonce' ) ) { // input var okay.
            $xtfefoli_global_message = isset( $_POST['xtfefoli_global_sharing_message'] ) ? wp_kses_post( wp_unslash( $_POST['xtfefoli_global_sharing_message'] ) ) : '';
			$xtfefoli_so_options     = isset( $_POST['xtfefoli_sharing_option'] ) ? $this->sanitize_recursive( wp_unslash( $_POST['xtfefoli_sharing_option'] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$xtfefoli_gsm_is_update  = update_option( 'xtfefoli_global_sharing_message', $xtfefoli_global_message );
			$if_is_update            = update_option( XTFEFOLI_OPTIONS, $xtfefoli_so_options );
			if ( $xtfefoli_gsm_is_update || $if_is_update ) {
				$xtfefoli_success_msg[] = __( 'Save Setting Successfully.', 'xt-feed-for-linkedin' );
			} else {
				$xtfefoli_errors[] = __( 'Something went wrong! please try again.', 'xt-feed-for-linkedin' );
			}
		}
	}

	/**
	 * Sanitize recursive
	 *
	 * @since    1.0.0
	 */
	public function sanitize_recursive( $input ) {
		if ( is_array( $input ) ) {
			return array_map( array( $this, 'sanitize_recursive' ), $input );
		}
		return esc_attr( sanitize_text_field( $input ) );
	}
	

    /**
	 * Display notices in admin.
	 *
	 * @since    1.0.0
	 */
	public function xtfefoli_display_notices() {
		global $xtfefoli_errors, $xtfefoli_success_msg, $xtfefoli_warnings, $xtfefoli_info_msg;

		if ( ! empty( $xtfefoli_errors ) ) {
			foreach ( $xtfefoli_errors as $error ) :
				?>
				<div class="notice notice-error lf-notice is-dismissible">
					<p><?php echo wp_kses_post( $error ) ; ?></p>
				</div>
				<?php
			endforeach;
		}

		if ( ! empty( $xtfefoli_success_msg ) ) {
			foreach ( $xtfefoli_success_msg as $success ) :
				?>
				<div class="notice notice-success lf-notice is-dismissible">
					<p><?php echo wp_kses_post( $success ); ?></p>
				</div>
				<?php
			endforeach;
		}

		if ( ! empty( $xtfefoli_warnings ) ) {
			foreach ( $xtfefoli_warnings as $warning ) :
				?>
				<div class="notice notice-warning lf-notice is-dismissible">
					<p><?php echo wp_kses_post( $warning ); ?></p>
				</div>
				<?php
			endforeach;
		}

		if ( ! empty( $xtfefoli_info_msg ) ) {
			foreach ( $xtfefoli_info_msg as $info ) :
				?>
				<div class="notice notice-info lf-notice is-dismissible">
					<p><?php echo wp_kses_post( $info ); ?></p>
				</div>
				<?php
			endforeach;
		}
	}

	/**
	 * Display token expiring notice in admin.
	 *
	 * @since    1.0.0
	 */
	public function xtfefoli_token_expiring_notice() {
		$xtfefoli_linked_accounts = get_option( 'xtfefoli_linkedin_user_data', true );
	
		if ( empty( $xtfefoli_linked_accounts ) || !is_array( $xtfefoli_linked_accounts ) ) {
			return;
		}
	
		$current_timestamp = time();
		$notices = [];
	
		foreach ( $xtfefoli_linked_accounts as $account ) {
			if ( !isset( $account['token_expired'] ) ) {
				continue;
			}
	
			$token_expiry_timestamp = (int) $account['token_expired'];
			$days_left = floor( ( $token_expiry_timestamp - $current_timestamp ) / ( 24 * 60 * 60 ) );

			$first_name = isset( $account['given_name'] ) ? $account['given_name'] : '';
			$last_name  = isset( $account['family_name'] ) ? $account['family_name'] : '';
			$full_name  = trim( ucfirst( $first_name ) . ' ' . ucfirst( $last_name ) );

			// If token is already expired, show "expired X days ago"
			if ( $days_left < 0 ) {
				$notices[] = sprintf(
					esc_html( 'Hello %1$s, your XT Feed for LinkedIn authentication expired on %2$s (%3$d day%4$s ago). To continue autopublishing, please re-authenticate.', 'xt-feed-for-linkedin' ),
					'<strong>' . esc_html( $full_name ) . '</strong>',
					esc_html( gmdate( 'F j, Y', $token_expiry_timestamp ) ),
					esc_html( abs( $days_left ) ),
					esc_html( abs( $days_left ) > 1 ? 's' : '' )
				);
			} elseif ( $days_left <= 5 ) {
				$notices[] = sprintf(
					esc_html( 'Hello %1$s, your XT Feed for LinkedIn authentication is expiring soon! To continue autopublishing, please re-authenticate before %2$s (only %3$d day%4$s left).', 'xt-feed-for-linkedin' ),
					'<strong>' . esc_html( $full_name ) . '</strong>',
					esc_html( gmdate( 'F j, Y', $token_expiry_timestamp ) ),
					esc_html( $days_left ),
					esc_html( $days_left > 1 ? 's' : '' )
				);
			}

		}
	
		if (!empty( $notices ) ) {
			?>
			<div class="notice notice-error lf-notice is-dismissible">
				<h3><?php esc_html_e( 'XT Feed for LinkedIn', 'xt-feed-for-linkedin' ); ?></h3>
				<?php foreach ( $notices as $notice ) : ?>
					<p><?php echo wp_kses_post( $notice ); ?>
					<a style="font-weight:bold;" href="<?php echo esc_url( admin_url( 'admin.php?page=xt_feed_for_linkedin&tab=general' ) ); ?>">
						<?php esc_html_e('Click here', 'xt-feed-for-linkedin'); ?>
					</a>
					</p>
					<p></p>
				<?php endforeach; ?>
			</div>
			<?php
		}
	}	
}
