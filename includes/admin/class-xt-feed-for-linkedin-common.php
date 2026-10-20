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
                        <span class="round" title="Documentation" >
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
            'xylus-events-calendar' => array( 'plugin_name' => esc_html__( 'Easy Events Calendar', 'xt-feed-for-linkedin' ), 'description' => 'Display upcoming events from multiple sources in a responsive calendar with customizable layouts like grid, row, calendar, and masonry.' ),
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
            if( !empty( $linkedin_users ) && is_array( $linkedin_users ) ){
                // Separate profiles and pages
                $profiles = array();
                $pages    = array();
                foreach ( $linkedin_users as $user ) {
                    if ( isset( $user['versionTag'] ) ) {
                        $pages[] = $user;
                    } else {
                        $profiles[] = $user;
                    }
                }
                if( empty( $profiles ) ){
                    return;
                }

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
                            foreach( $profiles as $profile ){

                                $given_name  = isset( $profile['given_name'] ) ? $profile['given_name'] : '';
                                $family_name = isset( $profile['family_name'] ) ? $profile['family_name'] : '';
                                $user_sub    = $profile['sub'];
                                $picture     = $profile['picture'] ?? '';
                                $full_name   = ucfirst( $given_name ) . ' ' . ucfirst( $family_name );
                                $is_active   = $profile['is_active'] ?? 'no';
                                $email       = $profile['email'] ?? '';
                                $access_token = isset( $profile['access_token'] ) ? $profile['access_token'] : '';
                                
                                ?>
                                <div class="lf-inner-main-section" id="user_<?php echo esc_attr( $user_sub ); ?>" style="display: block; width: 100%;">
                                    <div class="lf-card-single-row"  >
                                        <div class="lf-card-single-row-content">
                                            <div class="lf-profile-info">
                                                <?php if( !empty( $picture ) ){ ?>
                                                    <?php // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>
                                                    <img src="<?php echo esc_url( $picture ); ?>" alt="Profile Picture" class="lf-profile-pic" >
                                                <?php } else { ?>
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128" class="lf-profile-pic lf-profile-pic-placeholder">
                                                        <path fill="#e7e2dc" d="M0 0h128v128H0z"></path>
                                                        <path fill="#788fa5" d="M88.41 84.67a32 32 0 1 0-48.82 0 66.13 66.13 0 0 1 48.82 0"></path>
                                                        <path fill="#9db3c8" d="M88.41 84.67a32 32 0 0 1-48.82 0A66.79 66.79 0 0 0 0 128h128a66.79 66.79 0 0 0-39.59-43.33"></path>
                                                        <path fill="#56687a" d="M64 96a31.93 31.93 0 0 0 24.41-11.33 66.13 66.13 0 0 0-48.82 0A31.93 31.93 0 0 0 64 96"></path>
                                                    </svg>
                                                <?php } ?>
                                                <div class="lf-user-details">
                                                    <div>
                                                        <div class="lf-user-name"><?php echo esc_attr( $full_name ); ?></div>
                                                        <div class="lf-user-email"><?php echo esc_attr( $email ); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="lf-actions">
                                                <label class="lf-switch">
                                                    <input type="checkbox" class="lf-status-toggle" data-sub="<?php echo esc_attr( $user_sub ); ?>" data-email="<?php echo esc_attr( $email ); ?>" <?php if( $is_active == 'yes' ){ echo "checked"; } ?> >
                                                    <span class="lf-slider round" title="Activate/Deactivate Account"></span>
                                                </label>

                                                <button title="Delete Account" class="lf-delete-btn"  id="xtfefoli_user_deletion" data-sub="<?php echo esc_attr( $user_sub ); ?>" data-email="<?php echo esc_attr( $email ); ?>">
                                                    <svg fill="#ff0000" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" width="24px" height="24px">
                                                        <path d="M12,2C6.47,2,2,6.47,2,12c0,5.53,4.47,10,10,10s10-4.47,10-10C22,6.47,17.53,2,12,2z M16.707,15.293 c0.391,0.391,0.391,1.023,0,1.414C16.512,16.902,16.256,17,16,17s-0.512-0.098-0.707-0.293L12,13.414l-3.293,3.293 C8.512,16.902,8.256,17,8,17s-0.512-0.098-0.707-0.293c-0.391-0.391-0.391-1.023,0-1.414L10.586,12L7.293,8.707 c-0.391-0.391-0.391-1.023,0-1.414s1.023-0.391,1.414,0L12,10.586l3.293-3.293c0.391-0.391,1.023-0.391,1.414,0 s0.391,1.023,0,1.414L13.414,12L16.707,15.293z"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php
                                    // Find and render child pages associated with this profile
                                    $child_pages = array();
                                    foreach ( $pages as $page ) {
                                        $match = false;
                                        
                                        // Match by access token
                                        if ( ! empty( $access_token ) && isset( $page['access_token'] ) && $page['access_token'] === $access_token ) {
                                            $match = true;
                                        }
                                        
                                        // Match by email
                                        if ( !$match && ! empty( $email ) && ! empty( $page['email'] ) ) {
                                            $page_emails = array_map( 'trim', explode( ',', $page['email'] ) );
                                            if ( in_array( $email, $page_emails, true ) ) {
                                                $match = true;
                                            }
                                        }
                                        
                                        if ( $match ) {
                                            $child_pages[] = $page;
                                        }
                                    }

                                    if ( ! empty( $child_pages ) ) {
                                        ?>
                                        <div class="lf-child-accounts-list">
                                            <?php
                                            foreach ( $child_pages as $page ) {
                                                $page_id       = $page['id'];
                                                $page_name     = $page['given_name'] ?? '';
                                                $page_pic      = $page['picture'] ?? '';
                                                
                                                // Self-healing: if picture is empty, fetch it from LinkedIn API using parent's access token
                                                if ( empty( $page_pic ) && ! empty( $access_token ) ) {
                                                    $co_url = 'https://api.linkedin.com/v2/organizations/' . $page_id . '?projection=(id,logoV2(cropped~:playableStreams))';
                                                    $co_response = wp_remote_get( $co_url, array(
                                                        'headers' => array(
                                                            'Authorization'             => 'Bearer ' . $access_token,
                                                            'X-Restli-Protocol-Version' => '2.0.0',
                                                            'Accept'                    => 'application/json',
                                                        ),
                                                        'timeout' => 10,
                                                    ) );
                                                    if ( ! is_wp_error( $co_response ) ) {
                                                        $co_data = json_decode( wp_remote_retrieve_body( $co_response ), true );
                                                        if ( isset( $co_data['logoV2']['cropped~']['elements'][0]['identifiers'][0]['identifier'] ) ) {
                                                            $page_pic = $co_data['logoV2']['cropped~']['elements'][0]['identifiers'][0]['identifier'];
                                                            
                                                            // Update the option array
                                                            foreach ( $linkedin_users as $idx => $usr ) {
                                                                if ( isset( $usr['id'] ) && $usr['id'] === $page_id ) {
                                                                    $linkedin_users[$idx]['picture'] = $page_pic;
                                                                    update_option( 'xtfefoli_linkedin_user_data', $linkedin_users );
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                $page_active   = $page['is_active'] ?? 'no';
                                                $page_token    = $page['access_token'] ?? '';
                                                
                                                if ( xtlf_is_pro() || ( !xtlf_is_pro() && !empty( $page_token ) ) ) {
                                                    ?>
                                                    <div class="lf-child-account-row" id="user_<?php echo esc_attr( $page_id ); ?>">
                                                        <div class="lf-child-account-card">
                                                            <div class="lf-profile-info">
                                                                <?php if( !empty( $page_pic ) ){ ?>
                                                                    <img src="<?php echo esc_url( $page_pic ); ?>" alt="Page Logo" class="lf-profile-pic lf-profile-pic--page" >
                                                                <?php } else { ?>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128" class="lf-profile-pic lf-profile-pic--page lf-profile-pic-placeholder">
                                                                        <path fill="#e7e2dc" d="M0 0h128v128H0z"></path>
                                                                        <path fill="#9db3c8" d="M48 16h64v112H48z"></path>
                                                                        <path fill="#788fa5" d="M16 80h32v48H16z"></path>
                                                                        <path fill="#56687a" d="M48 80h32v48H48z"></path>
                                                                    </svg>
                                                                <?php } ?>
                                                                <div class="lf-user-details">
                                                                    <div>
                                                                        <div class="lf-user-name">
                                                                            <?php echo esc_attr( $page_name ); ?>
                                                                            <span class="lf-page-badge"><?php esc_html_e( 'Page', 'xt-feed-for-linkedin' ); ?></span>
                                                                        </div>
                                                                        <div class="lf-user-email">
                                                                            <a target="_blank" href="<?php echo esc_url( "https://www.linkedin.com/company/" . esc_attr( $page_id ) ); ?>" style="text-decoration: none;" >
                                                                                <?php echo esc_html( $page_id ); ?>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="lf-actions">
                                                                <label class="lf-switch">
                                                                    <input type="checkbox" class="lf-status-toggle" data-sub="<?php echo esc_attr( $page_id ); ?>" data-email="" <?php if( $page_active == 'yes' ){ echo "checked"; } ?> >
                                                                    <span class="lf-slider round" title="Activate/Deactivate Page"></span>
                                                                </label>

                                                                <button title="Delete Page" class="lf-delete-btn" id="xtfefoli_page_deletion" data-sub="<?php echo esc_attr( $page_id ); ?>" data-email="">
                                                                    <svg fill="#ff0000" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24px" height="24px">
                                                                        <path d="M12,2C6.47,2,2,6.47,2,12c0,5.53,4.47,10,10,10s10-4.47,10-10C22,6.47,17.53,2,12,2z M16.707,15.293 c0.391,0.391,0.391,1.023,0,1.414C16.512,16.902,16.256,17,16,17s-0.512-0.098-0.707-0.293L12,13.414l-3.293,3.293 C8.512,16.902,8.256,17,8,17s-0.512-0.098-0.707-0.293c-0.391-0.391-0.391-1.023,0-1.414L10.586,12L7.293,8.707 c-0.391-0.391-0.391-1.023,0-1.414s1.023-0.391,1.414,0L12,10.586l3.293-3.293c0.391-0.391,1.023-0.391,1.414,0 s0.391,1.023,0,1.414L13.414,12L16.707,15.293z"/>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                } else {
                                                    // Non-Pro display
                                                    ?>
                                                    <div class="lf-child-account-row">
                                                        <div class="lf-child-account-card" style="padding: 15px; display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                                            <div class="lf-card-left">
                                                                <a href="<?php echo esc_url( "https://www.linkedin.com/company/" . esc_attr( $page_id ) ); ?>" target="_blank" style="text-decoration:none; color:#0073b1; font-weight:bold;">
                                                                    <?php echo esc_html( $page_name ); ?>
                                                                </a>
                                                            </div>
                                                            <div class="lf-card-center" style="color:#d9534f; font-weight:bold; font-size:12px;">
                                                                <?php esc_attr_e( 'Upgrade to', 'xt-feed-for-linkedin' ); ?><strong><?php esc_attr_e( ' Pro', 'xt-feed-for-linkedin' ); ?></strong> <?php esc_attr_e( 'to unlock sharing on this page', 'xt-feed-for-linkedin' ); ?>
                                                            </div>
                                                            <div class="lf-card-right">
                                                                <a target="_blank" href="<?php echo esc_url( 'https://xylusthemes.com/plugins/xt-feed-for-linkedin/' ); ?>" class="xtfefoli_button" style="padding: 4px 10px; font-size:12px;">
                                                                    <?php esc_attr_e( 'Buy Pro', 'xt-feed-for-linkedin' ); ?>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
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
                        $page_token = isset($user['access_token']) ? $user['access_token'] : '';
                        if ( ! xtlf_is_pro() && empty( $page_token ) ) {
                            continue;
                        }

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

    /**
     * Inserts a new row in the xt_feed_for_linkedin table.
     * 
     * @param array $data {
     *     An associative array of data to be inserted into the table.
     *     The following keys are accepted:
     *     @type int    $post_id           The post ID associated with this share.
     *     @type string  $shared_linkedin_id The ID of the share on LinkedIn.
     *     @type string  $page_profile_id The ID of the LinkedIn page associated with this share.
     *     @type string  $sharing_type The type of share (scheduled or manual).
     *     @type string  $shared_time The time when the share was shared in the format 'Y-m-d H:i:s'.
     *     @type string  $status The status of the share (pending, shared, failed).
     *     @type string  $error_message The error message associated with the share.
     *     @type string  $message_content The content of the share.
     * }
     * @return int The ID of the newly inserted row.
     */
    public function xtfefoli_insert_linkedin_share( $data = array() ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'xt_feed_for_linkedin';

        // Prepare default values
        $defaults = array(
            'post_id'             => 0,
            'shared_linkedin_id'  => null,
            'page_profile_id'     => '',
            'sharing_type'        => 'manual',
            'shared_time'         => null,
            'status'              => 'pending',
            'error_message'       => null,
            'message_content'     => '',
        );

        $data = wp_parse_args( $data, $defaults );

        $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $table_name,
            $data,
            array(
                '%d',    // post_id
                '%s',    // shared_linkedin_id
                '%s',    // page_profile_id
                '%s',    // sharing_type
                '%s',    // shared_time
                '%s',    // status
                '%s',    // error_message
                '%s',    // message_content
            )
        );
        return $wpdb->insert_id;
    }

    /**
     * Update a record in the XT Feed for LinkedIn table.
     *
     * @param int $id The ID of the record to update.
     * @param array $data An associative array of data to update.
     *
     * @return bool True if the record was updated successfully, false otherwise.
     */
    public function xtfefoli_update_linkedin_share( $id, $data = array() ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'xt_feed_for_linkedin';

        if ( empty( $id ) ) {
            return false;
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        return $wpdb->update(
            $table_name,
            $data,
            array( 'id' => $id ),
            null,
            array( '%d' )
        );
    }

    /**
     * Get HTML for accounts dropdown
     *
     * @param string $selected_value Selected account identifier
     * @param string $name Name/ID of select input
     * @param string $class Additional CSS classes
     * @return string HTML select element
     */
    public function xtfefoli_get_accounts_select_html( $selected_value = '', $name = 'xtfefoli_share_account', $class = '' ) {
        $linkedin_users = get_option('xtfefoli_linkedin_user_data');
        
        $html = '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $name ) . '" class="xtfefoli-account-select ' . esc_attr( $class ) . '" style="max-width: 200px; min-width: 150px;">';
        $html .= '<option value="default">' . esc_html__( 'Default Active Account', 'xt-feed-for-linkedin' ) . '</option>';
        
        if ( !empty( $linkedin_users ) && is_array( $linkedin_users ) ) {
            foreach ( $linkedin_users as $user ) {
                $is_page = isset( $user['versionTag'] );
                
                if ( $is_page ) {
                    $page_token = isset($user['access_token']) ? $user['access_token'] : '';
                    if ( ! xtlf_is_pro() && empty( $page_token ) ) {
                        continue;
                    }
                }

                $type    = $is_page ? 'page' : 'profile';
                $id      = $is_page ? ( $user['id'] ?? '' ) : ( $user['sub'] ?? '' );
                $value   = $type . ':' . $id;
                
                if ( $is_page ) {
                    $label = ( $user['given_name'] ?? '' ) . ' (' . esc_html__( 'Page', 'xt-feed-for-linkedin' ) . ')';
                } else {
                    $given = $user['given_name'] ?? '';
                    $family = $user['family_name'] ?? '';
                    $label = trim( ucfirst( $given ) . ' ' . ucfirst( $family ) ) . ' (' . esc_html__( 'Profile', 'xt-feed-for-linkedin' ) . ')';
                }
                
                $selected = selected( $selected_value, $value, false );
                $html .= '<option value="' . esc_attr( $value ) . '" ' . $selected . '>' . esc_html( $label ) . '</option>';
            }
        }
        
        $html .= '</select>';
        return $html;
    }

    /**
     * Resolve account identifier to its active token and profile/page details
     *
     * @param string $identifier Format "profile:<sub>" or "page:<id>"
     * @return array|null Token details array, or null if not found
     */
    public function xtfefoli_get_account_token_by_identifier( $identifier ) {
        $get_xtfefoli_option = get_option('xtfefoli_linkedin_user_data', true);
    
        if (empty($get_xtfefoli_option) || !is_array($get_xtfefoli_option) || empty($identifier)) {
            return null;
        }
        
        $parts = explode( ':', $identifier, 2 );
        if ( count( $parts ) !== 2 ) {
            return null;
        }
        $target_type = $parts[0];
        $target_id   = $parts[1];
    
        $email_token_map = [];
        foreach ($get_xtfefoli_option as $user) {
            if (!empty($user['email']) && isset($user['access_token']) && !empty($user['access_token'])) {
                $email_token_map[$user['email']] = $user['access_token'];
            }
        }
    
        foreach ($get_xtfefoli_option as $user) {
            $is_page = isset($user['versionTag']);
            $current_type = $is_page ? 'page' : 'profile';
            $current_id   = $is_page ? ($user['id'] ?? '') : ($user['sub'] ?? '');
            
            if ( $current_type === $target_type && (string)$current_id === (string)$target_id ) {
                $token = !empty($user['access_token']) ? $user['access_token'] : ($email_token_map[$user['email']] ?? null);
                if ($token) {
                    if ($is_page) {
                        $page_token = isset($user['access_token']) ? $user['access_token'] : '';
                        if ( ! xtlf_is_pro() && empty( $page_token ) ) {
                            return null;
                        }

                        return [
                            'token'   => $token,
                            'type'    => 'page',
                            'page_id' => $user['id']
                        ];
                    } else {
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

/**
 * Check if the Pro version of XT Feed for LinkedIn is active.
 *
 * @return bool
 */
function xtfefoli_is_pro() {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	if ( is_plugin_active( 'xt-feed-for-linkedin-pro/xt-feed-for-linkedin-pro.php' ) ) {
		return true;
	}
	return false;
}

if ( ! function_exists( 'xtlf_is_pro' ) ) {
	/**
	 * Backward compatibility alias for xtfefoli_is_pro.
	 *
	 * @return bool
	 */
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
	function xtlf_is_pro() {
		return xtfefoli_is_pro();
	}
}