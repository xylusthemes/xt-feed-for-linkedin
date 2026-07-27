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
        add_action('wp_ajax_xtfefoli_get_sharing_history', array( $this, 'xtfefoli_get_sharing_history_callback' ) );
	}
    
    /**
     * Delete user
     * 
     * @since    1.0.0
     */
    public function xtfefoli_delete_user_callback() {
        // Verify nonce
        if ( !isset( $_POST['xtfefoli_security'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['xtfefoli_security'] ) ), 'xtfefoli_delete_user_nonce' ) ) {
            wp_send_json_error( "Invalid nonce" );
        }
    
        // Get user/page details from AJAX request
        $user_sub   = isset( $_POST['user_sub'] ) ? sanitize_text_field( wp_unslash( $_POST['user_sub'] ) ) : '';
    
        if ( empty( $user_sub ) ) {
            wp_send_json_error( ['message' => 'Invalid request data'] );
            wp_die();
        }
    
        // Get existing LinkedIn users from options
        $linkedin_users = get_option('xtfefoli_linkedin_user_data');
    
        if ( !is_array( $linkedin_users ) || empty( $linkedin_users ) ) {
            wp_send_json_error( ['message' => 'No users found'] );
            wp_die();
        }
    
        // Find the selected user or page
        $selected_item = null;
        foreach ( $linkedin_users as $user ) {
            if ( isset( $user['sub'] ) && $user['sub'] === $user_sub ) {
                $selected_item = $user;
                break;
            }
            if ( isset( $user['id'] ) && (string) $user['id'] === $user_sub ) {
                $selected_item = $user;
                break;
            }
        }
    
        if ( !$selected_item ) {
            wp_send_json_error( ['message' => 'Account not found'] );
            wp_die();
        }
    
        $token_to_remove = !empty( $selected_item['access_token'] ) ? $selected_item['access_token'] : '';
        $email_to_remove = !empty( $selected_item['email'] ) ? $selected_item['email'] : '';
        $is_profile      = isset( $selected_item['access_token'] ) && ! isset( $selected_item['versionTag'] );
    
        $identifiers_to_cancel = array();
        if ( $is_profile ) {
            $identifiers_to_cancel[] = 'profile:' . $user_sub;
        } else {
            $identifiers_to_cancel[] = 'page:' . $user_sub;
        }
    
        $updated_users = array_filter( $linkedin_users, function ( $user ) use ( $user_sub, $token_to_remove, $email_to_remove, $is_profile, &$identifiers_to_cancel ) {
            // Remove target profile
            if ( isset( $user['sub'] ) && $user['sub'] === $user_sub ) {
                return false;
            }
            // Remove target page
            if ( isset( $user['id'] ) && (string) $user['id'] === $user_sub ) {
                return false;
            }
            // If deleting a parent profile, cascade-delete all child pages linked by email
            if ( $is_profile && ! empty( $email_to_remove ) && isset( $user['email'] ) && $user['email'] === $email_to_remove && isset( $user['versionTag'] ) ) {
                $identifiers_to_cancel[] = 'page:' . $user['id'];
                return false;
            }
            return true;
        });

        // Cancel scheduled posts for removed accounts
        if ( ! empty( $identifiers_to_cancel ) ) {
            $scheduled_posts = get_posts( array(
                'post_type'      => 'any',
                'post_status'    => 'any',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'meta_query'     => array(
                    array(
                        'key'     => '_xtfefoli_schedule_share_account',
                        'value'   => $identifiers_to_cancel,
                        'compare' => 'IN'
                    )
                )
            ) );

            if ( ! empty( $scheduled_posts ) ) {
                foreach ( $scheduled_posts as $p_id ) {
                    delete_post_meta( $p_id, '_xtfefoli_schedule_enabled' );
                    delete_post_meta( $p_id, '_xtfefoli_schedule_datetime' );
                    delete_post_meta( $p_id, '_xtfefoli_schedule_share_account' );
                    
                    if ( function_exists( 'as_unschedule_all_actions' ) ) {
                        as_unschedule_all_actions( 'xtfefoli_share_post_cron', array( $p_id ), 'xtfefoli_share_group' );
                    }
                }
            }
        }
    
        // If no users left, delete the option
        if ( empty( $updated_users ) ) {
            delete_option('xtfefoli_linkedin_user_data');
            wp_send_json_success(['message' => 'Account and associated pages were removed successfully']);
        } else {
            // Otherwise, update the option
            update_option('xtfefoli_linkedin_user_data', array_values( $updated_users ) );
            wp_send_json_success(['message' => 'Account and associated pages were removed successfully']);
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
    
        // Get user/page details from AJAX request
        $user_sub   = isset($_POST['user_sub']) ? sanitize_text_field( wp_unslash( $_POST['user_sub'] ) ) : '';
        $is_active  = isset($_POST['is_active']) ? sanitize_text_field( wp_unslash( $_POST['is_active'] ) ) : 'no';
    
        if (empty($user_sub)) {
            wp_send_json_error(array('message' => 'Invalid request data'));
        }
    
        // Get existing users
        $linkedin_users = get_option('xtfefoli_linkedin_user_data');
    
        if (!is_array($linkedin_users) || empty($linkedin_users)) {
            wp_send_json_error(array('message' => 'No users found'));
        }
    
        // Update the users array
        foreach ($linkedin_users as &$user) {
            $match = false;
            if ( isset( $user['sub'] ) && $user['sub'] === $user_sub ) {
                $match = true;
            } elseif ( isset( $user['id'] ) && (string) $user['id'] === $user_sub ) {
                $match = true;
            }
            
            if ( $match ) {
                $user['is_active'] = $is_active;
            } else if ($is_active === 'yes') {
                $user['is_active'] = 'no';
            }
        }
    
        // Save updated data
        update_option('xtfefoli_linkedin_user_data', $linkedin_users );
        wp_send_json_success( array( 'message' => 'Account status updated successfully' ) );
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

        $post = get_post( $post_id );
        if ( !$post_id || !$post || $post->post_status === 'auto-draft' || ( empty( $post->post_title ) && empty( $post->post_content ) ) ) {
            wp_send_json_error( 'Title or Description should not be empty. Please save the post first before using this feature.' );
            exit;
        }

        //Get shared message with proper layput
        $share_message  = $xt_feed_for_linkedin->sharing->xtfefoli_rander_share_shared_message( $sanitized_message, $post_id );
        
        if ( $share_message === false ) { 
            wp_send_json_error( 'Post title or description cannot be empty.' ); 
            exit;
        }

        // Share to LinkedIn Company Page
        $sharing_type    = 'manual';
        $share_account   = isset( $_POST['share_account'] ) ? sanitize_text_field( wp_unslash( $_POST['share_account'] ) ) : '';
        $linkedin_result = $xt_feed_for_linkedin->sharing->xtfefoli_share_cpts_in_selected_account( $share_message, $post_id, $sharing_type, $share_account );

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

                $post_url = esc_url( 'https://www.linkedin.com/feed/update/' . $linkedin_result );
                $formatted_date = gmdate( 'Y-m-d H:i:s', time() );
                $history_html = '<div style="margin: 5px 0 0 5px;"><strong><a href="' . $post_url . '" target="_blank" style="color: #0049b3; text-decoration: none;">Check out the shared post </a> ( ' . $formatted_date . ' ) </strong></div>';

                wp_send_json_success( array( 'message' => 'Post shared successfully on LinkedIn.', 'history_html' => $history_html ) );
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
        $sharing_type    = 'manual';
        $share_account   = isset( $_POST['share_account'] ) ? sanitize_text_field( wp_unslash( $_POST['share_account'] ) ) : '';
        $linkedin_result = $xt_feed_for_linkedin->sharing->xtfefoli_share_cpts_in_selected_account( $share_message, $post_id, $sharing_type, $share_account );

        if ( isset( $linkedin_result['message'] ) && !empty( $linkedin_result['message'] ) ) {
            $e_message = $linkedin_result['message'];
            if ( strpos( $e_message, 'Content is a duplicate' ) !== false ) {
                $clean_result = 'This Content is already shared on LinkedIn. You may share it again after a short wait.';
            } else {
                $clean_result = 'An error occurred: ' . $e_message;
            }
            wp_send_json_error( $clean_result );
            exit;
        } else if ( $linkedin_result ) {

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
        wp_die();
    }
    
    /**
     * Get sharing history HTML
     * 
     * @since    1.0.0
     */
    public function xtfefoli_get_sharing_history_callback() {
        check_ajax_referer( 'xtfefoli_linkedin_feedpress_meta_box_nonce', 'xtfefoli_share_security' );
        $post_id = isset( $_POST['post_id'] ) ? intval( wp_unslash( $_POST['post_id'] ) ) : 0;
        if ( !$post_id ) {
            wp_send_json_error();
        }
        $get_shared_histories = get_post_meta( $post_id, '_xtfefoli_sended_on_linkedin', true );
        if( !empty( $get_shared_histories ) ){
            ob_start();
            ?>
            <div id="xtfefoli_shared_history_container" style="margin-top: 15px;">
                <strong><?php esc_attr_e( 'Post Shared History:', 'xt-feed-for-linkedin' ); ?></strong>
                <?php
                foreach( $get_shared_histories as $get_sh ){
                    if( isset( $get_sh['id'] ) && !is_array( $get_sh['id'] ) && !empty( $get_sh['id'] ) ){
                        $post_url = esc_url( 'https://www.linkedin.com/feed/update/' . $get_sh['id'] );
                        $formatted_date = gmdate( 'Y-m-d H:i:s', $get_sh['shared_post_datetime'] );				
                        echo '<div style="margin: 5px 0 0 5px;">
                                <strong><a href="' . esc_url( $post_url ) . '" target="_blank" style="color: #0049b3; text-decoration: none;">' . esc_attr__( 'Check out the shared post ', 'xt-feed-for-linkedin' ) . '</a> ( ' . esc_html( $formatted_date ) . ' ) </strong>
                                </div>';
                    }
                }
                ?>
            </div>
            <?php
            $html = ob_get_clean();
            wp_send_json_success( array( 'html' => $html ) );
        } else {
            global $wpdb;
            $table_name = $wpdb->prefix . 'xt_feed_for_linkedin';
            $error_log = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE post_id = %d ORDER BY id DESC LIMIT 1", $post_id ) );
            if ( $error_log && $error_log->status === 'failed' ) {
                wp_send_json_error( array( 'message' => 'Auto share failed: ' . $error_log->error_message ) );
            } else {
                wp_send_json_error();
            }
        }
    }
    
}