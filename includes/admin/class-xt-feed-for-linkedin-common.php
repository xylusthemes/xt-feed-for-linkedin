<?php
/**
 * Common functions class for XT Feed for LinkedIn
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    XT_Feed_Linkedin
 * @subpackage XT_Feed_Linkedin/includes
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class XT_Feed_Linkedin_Common {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
        add_action( 'xtfefoli_notice', array( $this, 'xtfefoli_render_common_notice' ) );
	}

    /**
     * Render Page header Section
     *
     * @since 1.1
     * @return void
     */
    public function xtfefoli_render_common_header( $page_title  ){
        ?>
        <div class="lf-header" >
            <div class="lf-container" >
                <div class="lf-header-content" >
                    <span style="font-size:18px;"><?php esc_html_e('Dashboard','xt-feed-for-linkedin'); ?></span>
                    <span class="spacer"></span>
                    <span class="page-name"><?php echo esc_attr( $page_title ); ?></span></span>
                    <div class="header-actions" >
                        <span class="round">
                            <a href="<?php echo esc_url( 'https://docs.xylusthemes.com/docs/xt-feed-for-linkedin/' ); ?>" target="_blank">
                                <svg viewBox="0 0 20 20" fill="#000000" height="20px" xmlns="http://www.w3.org/2000/svg" class="lf-circle-question-mark">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.6665 10.0001C1.6665 5.40008 5.39984 1.66675 9.99984 1.66675C14.5998 1.66675 18.3332 5.40008 18.3332 10.0001C18.3332 14.6001 14.5998 18.3334 9.99984 18.3334C5.39984 18.3334 1.6665 14.6001 1.6665 10.0001ZM10.8332 13.3334V15.0001H9.1665V13.3334H10.8332ZM9.99984 16.6667C6.32484 16.6667 3.33317 13.6751 3.33317 10.0001C3.33317 6.32508 6.32484 3.33341 9.99984 3.33341C13.6748 3.33341 16.6665 6.32508 16.6665 10.0001C16.6665 13.6751 13.6748 16.6667 9.99984 16.6667ZM6.6665 8.33341C6.6665 6.49175 8.15817 5.00008 9.99984 5.00008C11.8415 5.00008 13.3332 6.49175 13.3332 8.33341C13.3332 9.40251 12.6748 9.97785 12.0338 10.538C11.4257 11.0695 10.8332 11.5873 10.8332 12.5001H9.1665C9.1665 10.9824 9.9516 10.3806 10.6419 9.85148C11.1834 9.43642 11.6665 9.06609 11.6665 8.33341C11.6665 7.41675 10.9165 6.66675 9.99984 6.66675C9.08317 6.66675 8.33317 7.41675 8.33317 8.33341H6.6665Z" fill="currentColor"></path>
                                </svg>
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <?php
        
    }

    /**
     * Render Page Footer Section
     *
     * @since 1.1
     * @return void
     */
    public function xtfefoli_render_common_footer(){
        ?>
            <div id="lf-footer-links" >
                <div class="lf-footer">
                    <div><?php esc_attr_e( 'Made with ♥ by the Xylus Themes','xt-feed-for-linkedin'); ?></div>
                    <div class="lf-links" >
                        <a href="<?php echo esc_url( 'https://xylusthemes.com/support/' ); ?>" target="_blank" ><?php esc_attr_e( 'Support','xt-feed-for-linkedin'); ?></a>
                        <span>/</span>
                        <a href="<?php echo esc_url( 'https://docs.xylusthemes.com/docs/xt-feed-for-linkedin/' ); ?>" target="_blank" ><?php esc_attr_e( 'Docs','xt-feed-for-linkedin'); ?></a>
                        <span>/</span>
                        <a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=xylus&tab=search&type=term' ) ); ?>" ><?php esc_attr_e( 'Free Plugins','xt-feed-for-linkedin'); ?></a>
                    </div>
                    <div class="lf-social-links">
                        <a href="<?php echo esc_url( 'https://www.facebook.com/xylusinfo/' ); ?>" target="_blank" >
                            <svg class="lf-facebook">
                                <path fill="currentColor" d="M16 8.05A8.02 8.02 0 0 0 8 0C3.58 0 0 3.6 0 8.05A8 8 0 0 0 6.74 16v-5.61H4.71V8.05h2.03V6.3c0-2.02 1.2-3.15 3-3.15.9 0 1.8.16 1.8.16v1.98h-1c-1 0-1.31.62-1.31 1.27v1.49h2.22l-.35 2.34H9.23V16A8.02 8.02 0 0 0 16 8.05Z"></path>
                            </svg>
                        </a>
                        <a href="<?php echo esc_url( 'https://www.linkedin.com/company/xylus-consultancy-service-xcs-/' ); ?>" target="_blank" >
                            <svg class="lf-linkedin">
                                <path fill="currentColor" d="M14 1H1.97C1.44 1 1 1.47 1 2.03V14c0 .56.44 1 .97 1H14a1 1 0 0 0 1-1V2.03C15 1.47 14.53 1 14 1ZM5.22 13H3.16V6.34h2.06V13ZM4.19 5.4a1.2 1.2 0 0 1-1.22-1.18C2.97 3.56 3.5 3 4.19 3c.65 0 1.18.56 1.18 1.22 0 .66-.53 1.19-1.18 1.19ZM13 13h-2.1V9.75C10.9 9 10.9 8 9.85 8c-1.1 0-1.25.84-1.25 1.72V13H6.53V6.34H8.5v.91h.03a2.2 2.2 0 0 1 1.97-1.1c2.1 0 2.5 1.41 2.5 3.2V13Z"></path>
                            </svg>
                        </a>
                        <a href="<?php echo esc_url( 'https://x.com/XylusThemes" target="_blank' ); ?>" target="_blank" >
                            <svg class="lf-twitter" width="24" height="24" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="12" fill="currentColor"></circle>
                                <g>
                                    <path d="M13.129 11.076L17.588 6H16.5315L12.658 10.4065L9.5665 6H6L10.676 12.664L6 17.9865H7.0565L11.1445 13.332L14.41 17.9865H17.9765L13.129 11.076ZM11.6815 12.7225L11.207 12.0585L7.4375 6.78H9.0605L12.1035 11.0415L12.576 11.7055L16.531 17.2445H14.908L11.6815 12.7225Z" fill="white"></path>
                                </g>
                            </svg>
                        </a>
                        <a href="<?php echo esc_url( 'https://www.youtube.com/@xylussupport7784' ); ?>" target="_blank" >
                            <svg class="lf-youtube">
                                <path fill="currentColor" d="M16.63 3.9a2.12 2.12 0 0 0-1.5-1.52C13.8 2 8.53 2 8.53 2s-5.32 0-6.66.38c-.71.18-1.3.78-1.49 1.53C0 5.2 0 8.03 0 8.03s0 2.78.37 4.13c.19.75.78 1.3 1.5 1.5C3.2 14 8.51 14 8.51 14s5.28 0 6.62-.34c.71-.2 1.3-.75 1.49-1.5.37-1.35.37-4.13.37-4.13s0-2.81-.37-4.12Zm-9.85 6.66V5.5l4.4 2.53-4.4 2.53Z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        <?php   
    }

    /**
     * Get Plugin array
     *
     * @since 1.1.0
     * @return array
     */
    public function xtfefoli_get_xyuls_themes_plugins(){
        return array(
            'wp-event-aggregator' => array( 'plugin_name' => esc_html__( 'WP Event Aggregator', 'xt-feed-for-linkedin' ), 'description' => 'WP Event Aggregator: Easy way to import Facebook Events, Eventbrite events, MeetUp events into your WordPress Event Calendar.' ),
            'import-facebook-events' => array( 'plugin_name' => esc_html__( 'Import Social Events', 'xt-feed-for-linkedin' ), 'description' => 'Import Facebook events into your WordPress website and/or Event Calendar. Nice Display with shortcode & Event widget.' ),
            'import-eventbrite-events' => array( 'plugin_name' => esc_html__( 'Import Eventbrite Events', 'xt-feed-for-linkedin' ), 'description' => 'Import Eventbrite Events into WordPress website and/or Event Calendar. Nice Display with shortcode & Event widget.' ),
            'import-meetup-events' => array( 'plugin_name' => esc_html__( 'Import Meetup Events', 'xt-feed-for-linkedin' ), 'description' => 'Import Meetup Events allows you to import Meetup (meetup.com) events into your WordPress site effortlessly.' ),
            'event-schema' => array( 'plugin_name' => esc_html__( 'Event Schema / Structured Data', 'xt-feed-for-linkedin' ), 'description' => 'Automatically Google Event Rich Snippet Schema Generator. This plug-in generates complete JSON-LD based schema (structured data for Rich Snippet) for events.' ),
            'wp-smart-import' => array( 'plugin_name' => esc_html__( 'WP Smart Import : Import any XML File to WordPress', 'xt-feed-for-linkedin' ), 'description' => 'The most powerful solution for importing any CSV files to WordPress. Create Posts and Pages any Custom Posttype with content from any CSV file.' ),
            'wp-bulk-delete' => array( 'plugin_name' => esc_html__( 'WP Bulk Delete', 'xt-feed-for-linkedin' ), 'description' => 'Delete posts, pages, comments, users, taxonomy terms and meta fields in bulk with different powerful filters and conditions.' ),
        );
    }

    /**
     * Display Admin Notices
     *
     * @since 1.0
     * @param array $notice_result Status array
     * @return void
     */
    public function xtfefoli_display_admin_notice( $notice_result = array() ) {

        if ( ! empty( $notice_result ) && $notice_result['status'] == 1 ){
            if( !empty( $notice_result['messages'] ) ){
                foreach ( $notice_result['messages'] as $smessages ) {
                    ?>
                    <div class="notice notice-success lf-notice is-dismissible">
                        <p><strong><?php echo esc_attr( $smessages ); ?></strong></p>
                    </div>
                    <?php
                }
            }  
        } elseif ( ! empty( $notice_result ) && $notice_result['status'] == 0 ){

            if( !empty( $notice_result['messages'] ) ){
                foreach ( $notice_result['messages'] as $emessages ) {
                    ?>
                    <div class="notice notice-error lf-notice is-dismissible">
                        <p><strong><?php echo esc_attr( $emessages ); ?></strong></p>
                    </div>
                    <?php
                }
            }
        }
    }


    /**
     * Display Admin Common Notices
     *
     * @since 1.0
     * @return void
     */
    public function xtfefoli_render_common_notice(){
         // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if( isset( $_GET['message'] ) && !empty( $_GET['message'] ) ){
            $status         = isset( $_GET['status'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['status'] ) ) ) : 1;  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $get_message    = sanitize_text_field( wp_unslash( $_GET['message'] ) );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $notice_message = array( 'status' => $status, 'messages' => array( esc_attr( $get_message ) ) );
            $this->xtfefoli_display_admin_notice( $notice_message );
        }
    }

    /**
     * Get Linkedin User Data
     *
     * @since 1.0
     * @return array
     */
    public function xtfefoli_show_acuthorized_accounts(){
            $linkedin_users = get_option('xtfefoli_linkedin_user_data');
            if( !empty( $linkedin_users ) ){
            ?> 
                <div class="lf-card">
                    <div class="header">
                        <div class="text">
                            <div class="header-icon"></div>
                            <div class="header-title">
                                <span><?php esc_attr_e( 'Authorized Accounts', 'xt-feed-for-linkedin' ); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="content">
                        <?php
                            foreach( $linkedin_users as $linkedin_user ){

                                $given_name  = isset( $linkedin_user['given_name'] ) ? $linkedin_user['given_name'] : '';
                                $family_name = isset( $linkedin_user['family_name'] ) ? $linkedin_user['family_name'] : '';
                                $user_sub    = $linkedin_user['sub'];
                                $picture     = $linkedin_user['picture'] ?? '';
                                $company_id  = $linkedin_user['id'] ?? '';
                                $full_name   = ucfirst( $given_name ) . ' ' . ucfirst( $family_name );
                                $is_active   = $linkedin_user['is_active'];
                                $email       = $linkedin_user['email'];
                                $token_expired = isset( $linkedin_user['token_expired'] ) ? (int) $linkedin_user['token_expired'] : '';
                                $company_id  = isset( $linkedin_user['id'] ) ? $linkedin_user['id'] : '';
                                $c_timestamp = time();
                                $is_page     = '';
                                if ( !empty( $company_id ) ) {
                                    $is_page = ' ( Page ) ';
                                }

                                ?>
                                <div class="lf-inner-main-section" id="user_<?php echo esc_attr( $user_sub ); ?>">
                                    <div class="lf-card-single-row"  >
                                        <div class="lf-card-single-row-content">
                                            <div class="lf-profile-info">
                                                <?php if( !empty( $picture ) ){ ?>
                                                    <?php // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>
                                                    <img src="<?php echo esc_url( $picture ); ?>" alt="Profile Picture" class="lf-profile-pic" >
                                                <?php } ?>
                                                <div class="lf-user-details">
                                                    <div>
                                                        <div class="lf-user-name"><?php echo esc_attr( $full_name ) . esc_attr( $is_page ); ?></div>
                                                        <div class="lf-user-email"><?php echo esc_attr( $email ); ?></div>
                                                        <?php
                                                            if ( !empty( $company_id ) ) {
                                                                $page_url = "https://www.linkedin.com/company/" . esc_attr( $company_id );
                                                                ?>
                                                                <div>
                                                                    <strong>
                                                                        <a target="_blank" href="<?php echo esc_url( $page_url ); ?>" style="text-decoration: none;" >
                                                                            <?php echo esc_html( $company_id ); ?>
                                                                        </a>
                                                                    </strong>
                                                                </div>
                                                                <?php
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="lf-actions">
                                                <label class="lf-switch">
                                                    <input type="checkbox" class="lf-status-toggle" data-sub="<?php echo esc_attr( $user_sub ); ?>" data-email="<?php echo esc_attr( $email ); ?>" <?php if( $is_active == 'yes' ){ echo "checked"; } ?> >
                                                    <span class="lf-slider round"></span>
                                                </label>

                                                <button title="Delete Account" class="lf-delete-btn"  id="xtfefoli_user_deletion" data-sub="<?php echo esc_attr( $user_sub ); ?>" data-email="<?php echo esc_attr( $email ); ?>">
                                                    <svg fill="#ff0000" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" width="24px" height="24px">
                                                        <path d="M12,2C6.47,2,2,6.47,2,12c0,5.53,4.47,10,10,10s10-4.47,10-10C22,6.47,17.53,2,12,2z M16.707,15.293 c0.391,0.391,0.391,1.023,0,1.414C16.512,16.902,16.256,17,16,17s-0.512-0.098-0.707-0.293L12,13.414l-3.293,3.293 C8.512,16.902,8.256,17,8,17s-0.512-0.098-0.707-0.293c-0.391-0.391-0.391-1.023,0-1.414L10.586,12L7.293,8.707 c-0.391-0.391-0.391-1.023,0-1.414s1.023-0.391,1.414,0L12,10.586l3.293-3.293c0.391-0.391,1.023-0.391,1.414,0 s0.391,1.023,0,1.414L13.414,12L16.707,15.293z"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                            }
                        ?>
                    </div>
                </div>               
            <?php
            }
            ?>
        <?php
    }

    /**
     * Get Linkedin User Data
     *
     * @since 1.0
     * @return array
     */
    public function xtfefoli_get_active_account_token() {
        $get_xtfefoli_option = get_option('xtfefoli_linkedin_user_data', true);
    
        if (empty($get_xtfefoli_option) || !is_array($get_xtfefoli_option)) {
            return null;
        }
    
        $email_token_map = [];
    
        // Step 1: Store tokens for active users & map emails to tokens
        foreach ($get_xtfefoli_option as $user) {
            if (!empty($user['email']) && isset($user['access_token']) && !empty($user['access_token'])) {
                $email_token_map[$user['email']] = $user['access_token'];
            }
        }
    
        // Step 2: Find the first valid token for an active user
        foreach ($get_xtfefoli_option as $user) {
            if (isset($user['is_active']) && $user['is_active'] === 'yes') {
                $token = !empty($user['access_token']) ? $user['access_token'] : ($email_token_map[$user['email']] ?? null);
    
                if ($token) {
                    if (isset($user['versionTag'])) {
                        // It's a page
                        return [
                            'token'   => $token,
                            'type'    => 'page',
                            'page_id' => $user['id']
                        ];
                    } else {
                        // It's a profile
                        return [
                            'token'   => $token,
                            'type'    => 'profile',
                            'user_id' => $user['sub']
                        ];
                    }
                }
            }
        }
    
        return null;
    }
}