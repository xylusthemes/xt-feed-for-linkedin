<?php
/**
 * Class for Get User's Page Data
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
 * Class for Get User's Page Data
 *
 * @package     XT_Feed_Linkedin
 * @subpackage  XT_Feed_Linkedin/includes/admin
 * @author     Rajat Patel <prajat21@gmail.com>
 */
class XT_Feed_Linkedin_User_Company_Data {

    /**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
        add_action( 'admin_init', array( $this, 'xtfefoli_get_users_pages_accounts' ) );   
	}

    /**
     * Get User's Page Data
     *
     * @since    1.0.0
     */
    public function xtfefoli_get_users_pages_accounts(){

        $xtfefoli_page_sync_status = get_transient( 'xtfefoli_linkedin_feedpress_page_sync_flag' );

        if ( $xtfefoli_page_sync_status === false ) {

            $organizers_ids     = array();
            $xtfefoli_linked_accounts = get_option( 'xtfefoli_linkedin_user_data', true );

            // Ensure it's an array
            if ( !is_array( $xtfefoli_linked_accounts ) ) { 
                $xtfefoli_linked_accounts = []; 
            }

            $filtered_accounts = array_values( array_filter( $xtfefoli_linked_accounts, function ( $account ) { 
                return !isset( $account['versionTag'] ); 
            } ) );

            foreach( $filtered_accounts as $xtfefoli_linked_account ){

                $xtfefoli_users_companies = get_option( 'xtfefoli_users_companies', true );

                if ( !is_array( $xtfefoli_users_companies ) ) {
                    $xtfefoli_users_companies = [];
                }

                $user_companies = [];
                $user_email     = $xtfefoli_linked_account['email'];
                $access_token   = $xtfefoli_linked_account['access_token'];
                $get_lfc_url    = 'https://api.linkedin.com/rest/organizationAcls?q=roleAssignee&role=ADMINISTRATOR&count=100';
                $response       = wp_remote_get( $get_lfc_url, [
                    'headers'   => [
                        'Authorization' => 'Bearer ' . $access_token,
                        'LinkedIn-Version' => '202501',
                        'X-Restli-Protocol-Version' => '2.0.0',
                        'Accept' => 'application/json',
                    ],
                ]);
            
                if ( is_wp_error( $response ) ) {
                    return ['error' => $response->get_error_message()];
                }
            
                $page_response        = json_decode( wp_remote_retrieve_body( $response ), true );
                $get_companies_ids    = $page_response['elements'];
                
                foreach( $get_companies_ids as $get_companies_id ){

                    $organization     = $get_companies_id['organization'];
                    $lfc_parts        = explode( ":", $organization );
                    $lfc_id           = end( $lfc_parts );
                    $organizers_ids[] = $lfc_id;
                    $user_companies[] = $lfc_id;
                }

                $xtfefoli_users_companies[$user_email] = [
                    'email'       => $user_email,
                    'company_ids' => $user_companies,
                ];

                update_option( 'xtfefoli_users_companies', $xtfefoli_users_companies );

                $this->xtfefoli_get_users_companies( $organizers_ids, $access_token );
            }
        }

    }

    /**
     * Get User's Companies Data
     *
     * @since    1.0.0
     */
    public function xtfefoli_get_users_companies( $xtfefoli_companies_list_ids, $access_token ){
        
        $xtfefoli_companies_ids   = implode( ',', $xtfefoli_companies_list_ids );
        $companiesurl       = 'https://api.linkedin.com/rest/organizations?ids=List('.$xtfefoli_companies_ids.')';
        $lfc_response       = wp_remote_get( $companiesurl, array( 
            'headers'       => array( 
                'Authorization' => 'Bearer '.$access_token, 
                'Linkedin-Version' => '202501', 
                'X-Restli-Protocol-Version' => '2.0.0' 
            ) 
       ) );
        
        if ( is_wp_error( $lfc_response ) ) {
            return ['error' => $lfc_response->get_error_message()];
        }
        $company_response  = json_decode( wp_remote_retrieve_body( $lfc_response ), true );
        $this->xtfefoli_format_linkedin_companies( $company_response, $access_token );

    }

    /**
     * Format LinkedIn Companies Data
     *
     * @since    1.0.0
     */
    public function xtfefoli_format_linkedin_companies( $company_response, $access_token ) {
        $formatted_companies = [];
    
        if ( !isset( $company_response['results'] ) || empty( $company_response['results'] ) ) {
            return ['error' => 'No company data found'];
        }
    
        // Retrieve the existing users and their associated company IDs
        $xtfefoli_users_companies = get_option( 'xtfefoli_users_companies', true );
    
        // Ensure it's an array
        if ( !is_array( $xtfefoli_users_companies ) ) {
            $xtfefoli_users_companies = [];
        }

        // Retrieve existing linked accounts data
        $xtfefoli_linked_accounts = get_option( 'xtfefoli_linkedin_user_data', true );

        // Ensure it's an array
        if ( !is_array( $xtfefoli_linked_accounts ) ) {
            $xtfefoli_linked_accounts = [];
        }

        foreach ( $company_response['results'] as $company ) {
            $id = $company['id'] ?? null;
            $associated_emails = [];
    
            if ( $id ) {
                // Check if this company ID exists in the users' company lists
                foreach ( $xtfefoli_users_companies as $user_data ) {
                    if (in_array($id, $user_data['company_ids'])) {
                        $associated_emails[] = $user_data['email'];
                    }
                }
                
                $xtfefoli_linked_accounts[$id] = [
                    'id'           => $id,
                    'sub'          => $company['versionTag'] ?? '',
                    'vanityName'   => $company['vanityName'] ?? '',
                    'given_name'   => $company['localizedName'] ?? '',
                    'versionTag'   => $company['versionTag'] ?? '',
                    'picture'      => '',
                    'email'        => implode(', ', $associated_emails ),
                    'is_active'    => 'no',
                    'token'        => '',
                ];
            }
        }

        $unique_accounts = [];
        foreach ( $xtfefoli_linked_accounts as $account ) {
            if ( isset($account['sub'] ) ) {
                $unique_accounts[$account['sub']] = $account;
            } elseif ( isset($account['id'] ) ) {
                $unique_accounts[$account['id']] = $account;
            }
        }

        // Re-index to get sequential numeric keys
        $xtfefoli_linked_accounts = array_values( $unique_accounts );

        //save transient
        set_transient( 'xtfefoli_linkedin_feedpress_page_sync_flag', 1, 86400 );

        // Update the option
        update_option( 'xtfefoli_linkedin_user_data', $xtfefoli_linked_accounts );
    } 
}