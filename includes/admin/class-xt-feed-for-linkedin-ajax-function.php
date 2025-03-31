<?php
/**
 * Common functions class for XT Feed for LinkedIn
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

class XT_Feed_Linkedin_Ajax {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
        add_action('wp_ajax_xtfefoli_delete_user',  array( $this, 'xtfefoli_delete_user_callback' ) );
        add_action('wp_ajax_xtfefoli_update_user_status', array( $this, 'xtfefoli_update_user_status_callback' ) );
        add_action('wp_ajax_xtfefoli_share_to_linkedin', array( $this, 'xtfefoli_share_to_linkedin_function' ) );
        add_action('wp_ajax_xtfefoli_share_to_linkedin_lt', array( $this, 'xtfefoli_share_lt_to_linkedin_function' ) );
	}
    
    /**
     * Delete user
     * 
     * @since    1.0.0
     */
    public function xtfefoli_delete_user_callback() {
        // Verify nonce
        if ( !isset( $_POST['xtfefoli_security'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['xtfefoli_security'] ) ), 'xtfefoli_delete_user_nonce' ) ) {
            wp_send_json_error( "Invalid noncssse" );
        }
    
        // Get user details from AJAX request
        $user_sub   = isset( $_POST['user_sub'] ) ? sanitize_text_field( wp_unslash( $_POST['user_sub'] ) ) : '';
        $user_email = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
    
        if ( empty( $user_sub ) || empty( $user_email ) ) {
            wp_send_json_error( ['message' => 'Invalid request data'] );
            wp_die();
        }
    
        // Get existing LinkedIn users from options
        $linkedin_users = get_option('xtfefoli_linkedin_user_data');
    
        if ( !is_array( $linkedin_users ) || empty( $linkedin_users ) ) {
            wp_send_json_error( ['message' => 'No users found'] );
            wp_die();
        }
    
        // Find the selected user
        $selected_user = null;
        foreach ( $linkedin_users as $user ) {
            if ( isset($user['sub'] ) && $user['sub'] === $user_sub && isset( $user['email'] ) && $user['email'] === $user_email ) {
                $selected_user = $user;
                break;
            }
        }
    
        if ( !$selected_user ) {
            wp_send_json_error( ['message' => 'User not found'] );
            wp_die();
        }
    
        $has_token     = !empty( $selected_user['access_token'] );
        $updated_users = array_filter( $linkedin_users, function ( $user ) use ( $user_sub, $user_email, $has_token ) {
            if ( isset( $user['sub'] ) && $user['sub'] === $user_sub ) {
                return false;
            }
            if ( $has_token && isset( $user['email'] ) && $user['email'] === $user_email && empty( $user['access_token'] ) ) {
                return false;
            }
            return true;
        });
    
        // If no users left, delete the option
        if ( empty( $updated_users ) ) {
            delete_option('xtfefoli_linkedin_user_data');
            wp_send_json_success(['message' => 'User and associated LinkedIn pages were removed successfully']);
        } else {
            // Otherwise, update the option
            update_option('xtfefoli_linkedin_user_data', array_values( $updated_users ) );
            wp_send_json_success(['message' => 'User and associated LinkedIn pages were removed successfully']);
        }
        wp_die();
    }     
    
    /**
     * Update user status
     * 
     * @since    1.0.0
     */
    public function xtfefoli_update_user_status_callback() {
        
        // Verify nonce
        if (!isset($_POST['xtfefoli_security']) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['xtfefoli_security'] ) ), 'xtfefoli_update_status_nonce')) {
            wp_send_json_error(array('message' => 'Invalid nonce'));
        }
    
        // Get user details from AJAX request
        $user_sub   = isset($_POST['user_sub']) ? sanitize_text_field( wp_unslash( $_POST['user_sub'] ) ) : '';
        $user_email = isset($_POST['user_email']) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
        $is_active  = isset($_POST['is_active']) ? sanitize_text_field( wp_unslash( $_POST['is_active'] ) ) : 'no';
    
        if (empty($user_sub) || empty($user_email)) {
            wp_send_json_error(array('message' => 'Invalid request data'));
        }
    
        // Get existing users
        $linkedin_users = get_option('xtfefoli_linkedin_user_data');
    
        if (!is_array($linkedin_users) || empty($linkedin_users)) {
            wp_send_json_error(array('message' => 'No users found'));
        }
    
        // Update the users array
        foreach ($linkedin_users as &$user) {
            if (isset($user['sub']) && $user['sub'] === $user_sub && isset($user['email']) && $user['email'] === $user_email) {
                $user['is_active'] = $is_active;
            } else if ($is_active === 'yes') {
                $user['is_active'] = 'no';
            }
        }
    
        // Save updated data
        update_option('xtfefoli_linkedin_user_data', $linkedin_users );
        wp_send_json_success( array( 'message' => 'User status updated successfully' ) );
    }

    /**
     * Share to LinkedIn
     * 
     * @since    1.0.0
     */
    public function xtfefoli_share_to_linkedin_function() {
        global $xt_feed_for_linkedin;
        // Security check
        check_ajax_referer( 'xtfefoli_linkedin_feedpress_meta_box_nonce', 'xtfefoli_share_security' );

        // Get post ID and message
        $post_id           = isset( $_POST['post_id'] ) ? intval( wp_unslash( $_POST['post_id'] ) ) : 0;
        $xtfefoli_post_type      = isset( $_POST['xtfefoli_post_type'] ) ? sanitize_text_field( wp_unslash( $_POST['xtfefoli_post_type'] ) ) : 'post';
        $sanitized_message = isset( $_POST['share_message'] ) ? wp_kses_post( wp_unslash( $_POST['share_message'] ) ) : '';

        if ( !$post_id ) {
            $post_id = wp_insert_post( [
                'post_title'   => '',
                'post_status'  => 'publish',
                'post_type'    => $xtfefoli_post_type,
            ] );
        }

        if ( !$post_id || is_wp_error( $post_id ) ) {
            wp_send_json_error('Failed to create post.');
        }

        //Get shared message with proper layput
        $share_message  = $xt_feed_for_linkedin->sharing->xtfefoli_rander_share_shared_message( $sanitized_message, $post_id );
        
        if ( $share_message === false ) { 
            wp_send_json_error( 'Post title or description cannot be empty.' ); 
            exit;
        }

        // Share to LinkedIn Company Page
        $linkedin_result = $xt_feed_for_linkedin->sharing->xtfefoli_share_cpts_in_selected_account( $share_message, $post_id );

        if ( isset( $linkedin_result['message'] ) && !empty( $linkedin_result['message'] ) ) {
            $e_message = $linkedin_result['message'];
            if ( strpos( $e_message, 'Content is a duplicate' ) !== false ) {
                $clean_result = 'This Content is already shared on LinkedIn. You may share it again after a short wait.';
            } else {
                $clean_result = 'An error occurred: ' . $e_message;
            }
            wp_send_json_error( $clean_result );
            exit;
        }
        else{
            if ( $linkedin_result ) {
                
                $existing_data = get_post_meta( $post_id, '_xtfefoli_sended_on_linkedin', true );
                
                // Ensure it's an array
                if ( ! is_array( $existing_data ) ) { $existing_data = []; }
                $existing_data[] = array( 'id' => $linkedin_result, 'shared_post_datetime' => time() );

                $pshare_message = isset( $_POST['share_message'] ) ? wp_kses_post( wp_unslash( $_POST['share_message'] ) ) : '';
                //save post meta
                update_post_meta( $post_id, '_xtfefoli_sended_on_linkedin', $existing_data );
                update_post_meta( $post_id, '_xtfefoli_shared_on_linkedin', 1 );
                update_post_meta( $post_id, '_xtfefoli_dont_share_post_linkedin', 1 );
                update_post_meta( $post_id, '_xtfefoli_share_message', $pshare_message );

                wp_send_json_success( 'Post shared successfully on LinkedIn.' );
            } else {
                $activate_url = esc_url( admin_url( 'admin.php?page=xt_feed_for_linkedin&tab=general' ) );
                wp_send_json_error( 'No active account was found. Please select an authorized account <strong><a href="' . $activate_url . '" target="_blank">here</a></strong>.' );
            }
        }
    }

    /**
     * Share to LinkedIn
     * 
     * @since    1.0.0
     */
    public function xtfefoli_share_lt_to_linkedin_function() {
        global $xt_feed_for_linkedin;
        // Security check
        check_ajax_referer( 'xtfefoli_linkedin_feedpress_lt_nonce', 'xtfefoli_share_security' );
        
        // Get post ID and message
        $post_id            = isset( $_POST['post_id'] ) ? intval( wp_unslash( $_POST['post_id'] ) ) : 0;
        $xtfefoli_post_type = isset( $_POST['xtfefoli_post_type'] ) ? sanitize_text_field( wp_unslash( $_POST['xtfefoli_post_type'] ) ) : 'post';
        $check_post_shared  = get_post_meta( $post_id, '_xtfefoli_sended_on_linkedin', false );
        $post_share_message = get_post_meta( $post_id, '_xtfefoli_share_message', true );

        if( empty( $post_share_message ) ){
            $global_sharing_message = get_option( 'xtfefoli_global_sharing_message' );
            if( !empty( $global_sharing_message ) ){
                $sanitized_message  = wp_kses_post( $global_sharing_message );
            }else{
                $sanitized_message  = wp_kses_post( "[POST_TITLE]\n\n Read more: [POST_LINK]\n\n [POST_EXCERPT]\n\n By [POST_AUTHOR] |  [WEBSITE_TITLE]" );
            }
        }else{
            $sanitized_message  = $post_share_message;
        }

        //Get shared message with proper layput
        $share_message  = $xt_feed_for_linkedin->sharing->xtfefoli_rander_share_shared_message( $sanitized_message, $post_id );

        if ( empty( $share_message ) ) { 
            wp_send_json_error( 'Post description cannot be empty.' ); 
            exit;
        }

        // Share to LinkedIn Company Page
        $linkedin_result = $xt_feed_for_linkedin->sharing->xtfefoli_share_cpts_in_selected_account( $share_message, $post_id );

        if ( $linkedin_result ) {

            $existing_data = get_post_meta( $post_id, '_xtfefoli_sended_on_linkedin', true );

            // Ensure it's an array
            if ( ! is_array( $existing_data ) ) { $existing_data = []; }
            $existing_data[] = array( 'id' => $linkedin_result, 'shared_post_datetime' => time() );

            //save post meta
            update_post_meta( $post_id, '_xtfefoli_sended_on_linkedin', $existing_data );
            update_post_meta( $post_id, '_xtfefoli_shared_on_linkedin', 1 );
            update_post_meta( $post_id, '_xtfefoli_dont_share_post_linkedin', 1 );
            update_post_meta( $post_id, '_xtfefoli_share_message', $sanitized_message );

            wp_send_json_success( 'Post shared successfully on LinkedIn.' );

        } else {
            $activate_url = esc_url( admin_url( 'admin.php?page=xt_feed_for_linkedin&tab=general' ) );
            wp_send_json_error( 'No active account was found. Please select an authorized account <strong><a href="' . $activate_url . '" target="_blank">here</a></strong>.' );

        }
    }   
    
}