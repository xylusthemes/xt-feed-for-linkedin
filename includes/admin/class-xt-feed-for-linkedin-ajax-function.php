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

        // Remove deleted accounts from scheduled posts; cancel the schedule only when no selected account is left.
        if ( ! empty( $identifiers_to_cancel ) ) {
            global $xt_feed_for_linkedin;

            $scheduled_posts = get_posts( array(
                'post_type'      => 'any',
                'post_status'    => 'any',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
                    array(
                        'key'     => '_xtfefoli_schedule_share_account',
                        'compare' => 'EXISTS'
                    )
                )
            ) );

            foreach ( $scheduled_posts as $p_id ) {
                $selected  = $xt_feed_for_linkedin->common->xtfefoli_normalize_account_identifiers( get_post_meta( $p_id, '_xtfefoli_schedule_share_account', true ) );
                $remaining = array_values( array_diff( $selected, $identifiers_to_cancel ) );

                if ( count( $remaining ) === count( $selected ) ) {
                    continue; // This schedule doesn't use the removed account(s).
                }

                if ( ! empty( $remaining ) ) {
                    update_post_meta( $p_id, '_xtfefoli_schedule_share_account', $remaining );
                    continue;
                }

                delete_post_meta( $p_id, '_xtfefoli_schedule_enabled' );
                delete_post_meta( $p_id, '_xtfefoli_schedule_datetime' );
                delete_post_meta( $p_id, '_xtfefoli_schedule_share_account' );

                if ( function_exists( 'as_unschedule_all_actions' ) ) {
                    as_unschedule_all_actions( 'xtfefoli_share_post_cron', array( $p_id ), 'xtfefoli_share_group' );
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
            
            // Multiple accounts can be active at the same time; only touch the toggled one.
            if ( $match ) {
                $user['is_active'] = ( 'yes' === $is_active ) ? 'yes' : 'no';
            }
        }
        unset( $user );
    
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

        // Share to every selected account (none selected = all active accounts).
        $share_account = isset( $_POST['share_account'] ) ? $xt_feed_for_linkedin->common->xtfefoli_normalize_account_identifiers( wp_unslash( $_POST['share_account'] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $outcome       = $xt_feed_for_linkedin->sharing->xtfefoli_share_post_to_accounts( $share_message, $post_id, 'manual', $share_account );
        $pshare_message = isset( $_POST['share_message'] ) ? wp_kses_post( wp_unslash( $_POST['share_message'] ) ) : '';

        $this->xtfefoli_send_share_response( $outcome, $post_id, $pshare_message, 'metabox' );
    }

    /**
     * Record a multi-account share outcome and answer the AJAX request.
     *
     * @since 1.0.6
     * @param array  $outcome      Result of XT_Feed_Linkedin_Sharing::xtfefoli_share_post_to_accounts().
     * @param int    $post_id      Post ID.
     * @param string $stored_message Message template saved on the post.
     * @param string $context      'metabox' (post editor) or 'list' (posts list column): returns the shared-post link HTML for that screen.
     * @return void
     */
    private function xtfefoli_send_share_response( $outcome, $post_id, $stored_message, $context = '' ) {
        global $xt_feed_for_linkedin;

        if ( ! empty( $outcome['no_account'] ) ) {
            $activate_url = esc_url( admin_url( 'admin.php?page=xt_feed_for_linkedin&tab=general' ) );
            wp_send_json_error( 'No active account was found. Please select an authorized account <strong><a href="' . $activate_url . '" target="_blank">here</a></strong>.' );
        }

        $errors = array();
        foreach ( $outcome['results'] as $result ) {
            if ( 'shared' !== $result['status'] ) {
                $label    = $result['label'] ? $result['label'] . ': ' : '';
                $errors[] = esc_html( $label ) . esc_html( $xt_feed_for_linkedin->sharing->xtfefoli_format_share_error( $result['error'] ) );
            }
        }

        if ( empty( $outcome['shared'] ) ) {
            wp_send_json_error( implode( '<br>', $errors ) );
        }

        $added = $xt_feed_for_linkedin->sharing->xtfefoli_record_shared_results( $post_id, $outcome, $stored_message );

        $total   = count( $outcome['results'] );
        $message = ( 1 === $total ) ? 'Post shared successfully on LinkedIn.' : sprintf( 'Post shared successfully on %1$d of %2$d accounts.', $outcome['shared'], $total );
        if ( ! empty( $errors ) ) {
            $message .= '<br>' . implode( '<br>', $errors );
        }

        $response = array( 'message' => $message );

        if ( in_array( $context, array( 'metabox', 'list' ), true ) ) {
            $history_html = '';
            foreach ( $added as $entry ) {
                $post_url       = esc_url( 'https://www.linkedin.com/feed/update/' . $entry['id'] );
                $formatted_date = gmdate( 'Y-m-d H:i:s', $entry['shared_post_datetime'] );
                $suffix         = $xt_feed_for_linkedin->sharing->xtfefoli_history_account_suffix( $entry );
                if ( 'metabox' === $context ) {
                    $history_html .= '<div style="margin: 5px 0 0 5px;"><strong><a href="' . $post_url . '" target="_blank" style="color: #0049b3; text-decoration: none;">' . esc_html__( 'Check out the shared post ', 'xt-feed-for-linkedin' ) . '</a>' . $suffix . ' ( ' . esc_html( $formatted_date ) . ' ) </strong></div>';
                }
            }

            if ( 'list' === $context ) {
                $response['history_html'] = $xt_feed_for_linkedin->sharing->xtfefoli_get_column_html( $post_id, $post_type );
            } else {
                $response['history_html'] = $history_html;
            }
        }

        wp_send_json_success( $response );
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

        // Share to every selected account (none selected = all active accounts).
        $share_account = isset( $_POST['share_account'] ) ? $xt_feed_for_linkedin->common->xtfefoli_normalize_account_identifiers( wp_unslash( $_POST['share_account'] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $outcome       = $xt_feed_for_linkedin->sharing->xtfefoli_share_post_to_accounts( $share_message, $post_id, 'manual', $share_account );

        $this->xtfefoli_send_share_response( $outcome, $post_id, $sanitized_message, 'list' );
    }
    
    /**
     * Get sharing history HTML
     * 
     * @since    1.0.0
     */
    public function xtfefoli_get_sharing_history_callback() {
        global $xt_feed_for_linkedin;
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
                                <strong><a href="' . esc_url( $post_url ) . '" target="_blank" style="color: #0049b3; text-decoration: none;">' . esc_attr__( 'Check out the shared post ', 'xt-feed-for-linkedin' ) . '</a>' . $xt_feed_for_linkedin->sharing->xtfefoli_history_account_suffix( $get_sh ) . ' ( ' . esc_html( $formatted_date ) . ' ) </strong>
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
            $table_name = esc_sql( $wpdb->prefix . 'xt_feed_for_linkedin' );
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter
            $error_log  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE post_id = %d ORDER BY id DESC LIMIT 1", $post_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            if ( $error_log && $error_log->status === 'failed' ) {
                wp_send_json_error( array( 'message' => 'Auto share failed: ' . $error_log->error_message ) );
            } else {
                wp_send_json_error();
            }
        }
    }
    
}