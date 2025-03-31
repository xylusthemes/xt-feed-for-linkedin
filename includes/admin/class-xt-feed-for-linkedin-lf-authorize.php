<?php
/**
 * Class for LinkedIn User Authorization
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    XT_Feed_Linkedin
 * @subpackage XT_Feed_Linkedin/includes/admin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for LinkedIn Account Authorize.
 *
 * @package     XT_Feed_Linkedin
 * @subpackage  XT_Feed_Linkedin/includes/admin
 * @author     Rajat Patel <prajat21@gmail.com>
 */
class LinkedIn_Feedpress_XTFEFOLI_Authorize {

	/**
	 * LinkedIn API version
	 *
	 * @var string
	 */
	private $api_version;

    /**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->api_version = 'v2';
        add_action('init', array( $this, 'handle_xtfefoli_linkedin_oauth_callback' ) );
	}

    /**
     * LinkedIn Connect Button
     *
     * @since    1.0.0
     */
    public function xtfefoli_linkedin_connect_button() {
        $client_id    = '77g53oc1evfzfg';
        $redirect_uri = "https://devlf.xylusthemes.com/redirectlinkedinfeeedpress";
        $site_url     = home_url( '/?linkedin_auth=1' );
        $state        = str_replace( ['https', 'http'], ['h12', 'h1'], $site_url );
    
        $url = "https://www.linkedin.com/oauth/{$this->api_version}/authorization";
        $params = [
            'response_type' => 'code',
            'client_id'     => $client_id,
            'redirect_uri'  => $redirect_uri,
            'state'         => $state,
            'scope'         => 'openid profile email w_organization_social r_organization_social w_member_social r_basicprofile rw_organization_admin',
        ];
    
        $login_url = $url . '?' . http_build_query($params);
    
        echo '<a class="xtfefoli_button" style="display: flex;align-items: center;color: #fff;" href="' . esc_url($login_url) . '" >Connect LinkedIn</a>';
    }
    
    /**
     * Handle LinkedIn OAuth Callback
     *
     * @since    1.0.0
     */
    public function handle_xtfefoli_linkedin_oauth_callback() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if ( isset( $_GET['linkedin_auth'] ) && esc_attr( sanitize_text_field( wp_unslash( $_GET['linkedin_auth'] ) ) ) == 1 && isset( $_GET['access_token'] ) ) {
            
            $timestamp      = time();
            $token_expiring = strtotime( '+58 days', $timestamp );

            $new_linkedin_user  = [
                'sub'           => isset( $_GET['sub'] ) ? sanitize_text_field( wp_unslash( $_GET['sub'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                'full_name'     => isset( $_GET['name'] ) ? sanitize_text_field( wp_unslash( $_GET['name'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                'given_name'    => isset( $_GET['given_name'] ) ? sanitize_text_field( wp_unslash( $_GET['given_name'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                'family_name'   => isset( $_GET['family_name'] ) ? sanitize_text_field( wp_unslash( $_GET['family_name'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                'picture'       => isset( $_GET['picture'] ) ? esc_url_raw( wp_unslash( $_GET['picture'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                'email'         => isset( $_GET['email'] ) ? sanitize_email( wp_unslash( $_GET['email'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                'access_token'  => isset( $_GET['access_token'] ) ? sanitize_text_field( wp_unslash( $_GET['access_token'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                'token_expired' => $token_expiring,
                'is_active'     => 'no',
            ];
            
            $xtfefoli_linked_accounts = get_option( 'xtfefoli_linkedin_user_data', true );

            // Ensure it's an array
            if (!is_array($xtfefoli_linked_accounts)) {
                $xtfefoli_linked_accounts = [];
            }

            $found = false;

            // Loop through existing accounts to check if the user already exists
            foreach ($xtfefoli_linked_accounts as $key => $account) {
                if ($account['full_name'] === $new_linkedin_user['full_name'] && $account['email'] === $new_linkedin_user['email'] ) {
                    // If found, update the existing user data
                    $xtfefoli_linked_accounts[$key] = $new_linkedin_user;
                    $found = true;
                    break;
                }
            }

            // If the user does not exist, add a new entry
            if ( !$found ) {
                $xtfefoli_linked_accounts[] = $new_linkedin_user;
            }

            // Save updated LinkedIn accounts list to wp_options
            update_option( 'xtfefoli_linkedin_user_data', $xtfefoli_linked_accounts );

            delete_transient( 'xtfefoli_linkedin_feedpress_page_sync_flag' );

            // Redirect to avoid resubmission issue
            $redirect_url = add_query_arg( 
                array(
                    'message' => urlencode( 'Account Authorize Successfully' ), 
                    'status'  => 1
                ), 
                admin_url('?page=xt_feed_for_linkedin&tab=general') 
            );
            wp_safe_redirect( $redirect_url );
            exit;
        }else{
             // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            if( isset( $_GET['xtfefoli_error'] ) && !empty( $_GET['xtfefoli_error'] ) ){
                $lferror      = esc_attr( sanitize_text_field( wp_unslash( $_GET['xtfefoli_error'] ) ) );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                $redirect_url = add_query_arg( 
                    array(
                        'message' => urlencode( $lferror ), 
                        'status'  => 0
                    ),
                    esc_url( admin_url( 'admin.php?page=xt_feed_for_linkedin&tab=general' ) )
                );
                wp_safe_redirect( $redirect_url );
                exit;
            }
        }
    }
}