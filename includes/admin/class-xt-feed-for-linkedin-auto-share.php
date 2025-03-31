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
class XT_Feed_Linkedin_Auto_Share {

    public function __construct() {
        add_action( 'transition_post_status', array( $this, 'xtfefoli_auto_share_on_linkedin' ), 10, 3 );
    }

    /**
     * Auto share post on LinkedIn
     *
     * @since    1.0.0
     */
    public function xtfefoli_auto_share_on_linkedin( $new_status, $old_status, $post ) {
        global $xt_feed_for_linkedin;

        $post_id = $post->ID;
    
        // Ensure this is a valid post and not a revision or autosave
        if ( wp_is_post_autosave( $post->ID ) || wp_is_post_revision( $post->ID ) ) {
            return;
        }
    
        // Fetch plugin options
        $get_xtfefoli_options = xtfefoli_get_options();
        $xtfefoli_cpts        = isset( $get_xtfefoli_options['xtfefoli_linkedin_feedpress_cpts'] ) ? $get_xtfefoli_options['xtfefoli_linkedin_feedpress_cpts'] : array();
		$xtfefoli_bddspol     = isset( $get_xtfefoli_options['xtfefoli_bddspol'] ) ? $get_xtfefoli_options['xtfefoli_bddspol'] : '';
    
        // Check if the post type is allowed
        if ( ! in_array( $post->post_type, $xtfefoli_cpts ) ) {
            return;
        }

        // If the post is being published for the first time (not previously published)
        if ( $old_status !== 'publish' && $new_status === 'publish'  ) {

            if( empty( $xtfefoli_bddspol ) ){

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
                $share_message   = $xt_feed_for_linkedin->sharing->xtfefoli_rander_share_shared_message( $sanitized_message, $post_id );
                if ( $share_message === false ) { 
                    return false;
                }

                $linkedin_result = $xt_feed_for_linkedin->sharing->xtfefoli_share_cpts_in_selected_account( $share_message, $post_id );
            
                if ( $linkedin_result ) {
            
                    $existing_data = get_post_meta( $post_id, '_xtfefoli_sended_on_linkedin', true );
            
                    // Ensure it's an array
                    if ( ! is_array( $existing_data ) ) { $existing_data = []; }
                    $existing_data[] = array( 'id' => $linkedin_result, 'shared_post_datetime' => time() );
            
                    //save/update post meta
                    update_post_meta( $post_id, '_xtfefoli_sended_on_linkedin', $existing_data );
                    update_post_meta( $post_id, '_xtfefoli_shared_on_linkedin', 1 );
                    update_post_meta( $post_id, '_xtfefoli_dont_share_post_linkedin', 1 );
                    update_post_meta( $post_id, '_xtfefoli_share_message', $sanitized_message );
            
                }
            }
        }
    }
}

