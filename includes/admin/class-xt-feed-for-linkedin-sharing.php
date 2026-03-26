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
class XT_Feed_Linkedin_Sharing {

    /**
	 * LinkedIn API version
	 *
	 * @var string
	 */
	private $api_version;

    public function __construct() {
        $this->api_version = 'v2';
        add_action( 'admin_init', array( $this, 'xtfefoli_add_linkedin_shared_column_to_cpts' ) );
    }

    /**
     * Share post to LinkedIn
     *
     * @param int $post_id
     * @return void
     */
    public function xtfefoli_add_linkedin_shared_column( $columns ) {
        $columns['xtfefoli_shared_on_linkedin'] = 'XT Feed for LinkedIn';
        return $columns;
    }

    /**
     * Display LinkedIn shared column
     *
     * @param string $column
     * @param int $post_id
     * @return void
     */
    public function xtfefoli_display_linkedin_shared_column( $column, $post_id ) {
        if ( 'xtfefoli_shared_on_linkedin' === $column ) {

            $shared_status = get_post_meta( $post_id, '_xtfefoli_shared_on_linkedin', true );
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $xtfefoli_post_type  = isset( $_GET['post_type'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) ) : '';
            $status_text   = $shared_status ? '<span><strong>Shared</strong></span>' : '<span class="xtfefoli_post_'.$post_id.'">Not Shared</span>';
            wp_nonce_field('xtfefoli_linkedin_feedpress_lt_nonce', 'xtfefoli_linkedin_feedpress_lt_nonce');
            $get_shared_histories = get_post_meta( $post_id, '_xtfefoli_sended_on_linkedin', true );

            if( !empty( $get_shared_histories ) ){
                foreach( $get_shared_histories as $get_sh ){
                    if( isset( $get_sh['id'] ) && !is_array( $get_sh['id'] ) && !empty( $get_sh ) ){
                        $post_url = esc_url( 'https://www.linkedin.com/feed/update/' . $get_sh['id'] );
                        $formatted_date = gmdate( 'Y-m-d H:i:s', $get_sh['shared_post_datetime'] );
                        echo '<div style="margin: 5px 0;" ><strong><a href="' . esc_url( $post_url ) . '" target="_blank" style="color: #0049b3; text-decoration: none;">' . esc_attr__( 'Check out the shared post ', 'xt-feed-for-linkedin' ) . '</a><br> ( ' . esc_html( $formatted_date ) . ' ) </strong></div>';
                    }
                }
            }else{
                ?>
                <div>
                    <?php echo wp_kses_post( $status_text ); ?> |
                    <a class="xtfefoli_share_cpt_button_lt" href="javascript:void(0);" data-post_id="<?php echo esc_attr( $post_id ); ?>" data-post_type="<?php echo esc_attr( $xtfefoli_post_type ); ?>" >
                        <?php esc_attr_e( 'Share Now', 'xt-feed-for-linkedin' ); ?>
                    </a>
                    <div class="if_lt_notice" style="margin-top:15px;"></div>
                </div>
                <?php
                do_action( 'xtlf_save_schedule_post' , $post_id );
            }
        }
    }    

    /**
     * Add LinkedIn shared column to selected CPTs
     *
     * @return void
     */
    public function xtfefoli_add_linkedin_shared_column_to_cpts() {
        $xtfefoli_options     = get_option( XTFEFOLI_OPTIONS, true );
        $xtfefoli_so_options  = isset( $xtfefoli_options ) ? $xtfefoli_options : array();
        $selected_cpts  = isset( $xtfefoli_so_options['xtfefoli_linkedin_feedpress_cpts'] ) ? $xtfefoli_so_options['xtfefoli_linkedin_feedpress_cpts'] : array();

        if ( ! empty( $selected_cpts ) && is_array( $selected_cpts ) ) {
            foreach ( $selected_cpts as $post_type ) {
                add_filter( "manage_{$post_type}_posts_columns", array( $this, 'xtfefoli_add_linkedin_shared_column' ) );
                add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'xtfefoli_display_linkedin_shared_column' ), 10, 2 );
            }
        }
    }

    /**
     * Share post to LinkedIn
     *
     * @param int $post_id
     * @return void
     */
    public function xtfefoli_rander_share_shared_message( $sanitized_message, $post_id ) {
        
        $post_title    = get_the_title( $post_id );
        $post_link     = get_permalink( $post_id );
        $post_author   = get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) );
        $website_title = get_bloginfo( 'name' );

        $total_limit = xtlf_is_pro() ? 2500 : 1000;

        // Base message without excerpt
        $base_message = str_replace(
            ['[POST_TITLE]', '[POST_LINK]', '[POST_EXCERPT]', '[POST_AUTHOR]', '[WEBSITE_TITLE]'],
            [$post_title, $post_link, '[POST_EXCERPT]', $post_author, $website_title],
            $sanitized_message
        );

        $max_chars_for_excerpt = $total_limit - mb_strlen( $base_message );

        if ( $max_chars_for_excerpt <= 0 ) {
            return false; 
        }

        $excerpt_content = get_post_field( 'post_content', $post_id );

        // Strip tags but keep line breaks
        $excerpt_content = strip_tags( $excerpt_content, '<br><p>' );

        // Replace <p> and <br> with actual newlines
        $excerpt_content = str_replace( ['<p>', '</p>', '<br>', '<br/>', '<br />'], "\n", $excerpt_content );

        // Trim according to limit
        $post_excerpt = mb_substr( $excerpt_content, 0, $max_chars_for_excerpt );

        // Optional ellipsis
        if ( mb_strlen( $excerpt_content ) > $max_chars_for_excerpt ) {
            $post_excerpt .= '...';
        }

        $share_message = str_replace( '[POST_EXCERPT]', $post_excerpt, $base_message );

        return $share_message;
    }

    /**
     * Share post to LinkedIn
     *
     * @param int $post_id
     * @return void
     */
    public function xtfefoli_share_to_linkedin_page_only_text( $message, $post_id, $access_token, $page_id ) {

        // Clean and decode message
        $message      = wp_strip_all_tags( $message );
        $message      = html_entity_decode( $message, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        $message      = mb_substr( $message, 0, 2995, 'UTF-8' );
        $linkedin_url = "https://api.linkedin.com/v2/ugcPosts"; // Ensure correct API version
    
        $linkedin_payload = [
            'author' => "urn:li:organization:$page_id",
            'lifecycleState' => 'PUBLISHED',
            'specificContent' => [
                'com.linkedin.ugc.ShareContent' => [
                    'shareCommentary' => [
                        'text' => $message
                    ],
                    'shareMediaCategory' => 'NONE' // No media
                ]
            ],
            'visibility' => [
                'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'
            ]
        ];
    
        $response = wp_remote_post( $linkedin_url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type'  => 'application/json',
                'X-Restli-Protocol-Version' => '2.0.0'
            ],
            'body'    => json_encode($linkedin_payload, JSON_UNESCAPED_UNICODE),
            'method'  => 'POST'
        ]);
    
        if ( is_wp_error( $response ) ) {
            return false;
        }
    
        $response_body = json_decode( wp_remote_retrieve_body( $response ), true );

        // Check if LinkedIn responded with an error
        if ( isset( $response_body['id'] ) ) {
            return $response_body['id']; // Return LinkedIn post ID
        } else {
            if( isset( $response_body['message'] ) && !empty( $response_body['message'] ) ){
                $error_message = $response_body;
                return $error_message;
            }else{
                // error_log( 'LinkedIn API Error: ' . print_r( $response_body, true ) );
                return false;
            }
        }
    }
    
    
    /**
     * Share post to LinkedIn
     *
     * @param int $post_id
     * @return void
     */
    public function xtfefoli_share_to_linkedin_profile_only_text( $message, $post_id, $access_token, $user_id ) {
        
        $message          = wp_strip_all_tags( $message );
        $message          = html_entity_decode( $message, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        $message          = mb_substr( $message, 0, 2995, 'UTF-8' );
        $linkedin_url     = "https://api.linkedin.com/{$this->api_version}/ugcPosts"; 
        $linkedin_payload = [
            'author' => "urn:li:person:$user_id",
            'lifecycleState' => 'PUBLISHED',
            'specificContent' => [
                'com.linkedin.ugc.ShareContent' => [
                    'shareCommentary' => [
                        'text' => $message
                    ],
                    'shareMediaCategory' => 'NONE'
                ]
            ],
            'visibility' => [
                'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'
            ]
        ];
    
        $response = wp_remote_post($linkedin_url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type'  => 'application/json',
                'X-Restli-Protocol-Version' => '2.0.0'
            ],
            'body'    => json_encode($linkedin_payload),
            'method'  => 'POST'
        ]);
    
        if ( is_wp_error( $response ) ) {
            return false;
        }
    
        $response_body = json_decode(wp_remote_retrieve_body( $response ), true);
        if ( isset( $response_body['id'] ) ) {
            return $response_body['id']; // Return LinkedIn post ID
        } else {
            if( isset( $response_body['message'] ) && !empty( $response_body['message'] ) ){
                $error_message = $response_body;
                return $error_message;
            }else{
                return false;
            }
        }
    }
    
    /**
     * Share post to LinkedIn
     *
     * @param int $post_id
     * @return void
     */
    public function xtfefoli_share_cpts_in_selected_account( $message, $post_id, $sharing_type ){
        global $xt_feed_for_linkedin, $xt_feed_for_linkedin_pro;
        $selected_account = $xt_feed_for_linkedin->common->xtfefoli_get_active_account_token();

        if ( empty( $selected_account ) || !is_array( $selected_account ) ){
            
            $insert_args = array(
                'post_id'             => $post_id,
                'shared_linkedin_id'  => 0,
                'page_profile_id'     => 0,
                'sharing_type'        => $sharing_type,
                'shared_time'         => current_time('mysql'),
                'status'              => 'failed',
                'error_message'       => 'Please select at least one account to proceed with sharing.',
                'message_content'     => $message,
            );

            $xt_feed_for_linkedin->common->xtfefoli_insert_linkedin_share( $insert_args );

            return null;
        }

        $access_token = isset( $selected_account['token'] ) ? $selected_account['token'] : '';
        $type         = isset( $selected_account['type'] ) ? $selected_account['type'] : '';
        $user_id      = isset( $selected_account['user_id'] ) ? $selected_account['user_id'] : '';
        $page_id      = isset( $selected_account['page_id'] ) ? $selected_account['page_id'] : '';

        if ( empty( $access_token ) || empty( $type ) ){
            return null;
        }

        $lf_get_option = xtfefoli_get_options();
        $image_upload  = false;
        if ( is_array( $lf_get_option ) && isset( $lf_get_option['xtfefoli_postfimage'] ) ) {
            $image_upload  = true;
        }

        $shared_post_id  = null;
        $status          = 'failed';
        $shared_pid      = null;
        if ( $type == 'profile' ) {
            if ( $image_upload && method_exists( $xt_feed_for_linkedin_pro->sharing_pro, 'xtfefoli_share_to_linkedin_profile_with_image' ) ) {
                $shared_post_id = $xt_feed_for_linkedin_pro->sharing_pro->xtfefoli_share_to_linkedin_profile_with_image( $message, $post_id, $access_token, $user_id );
            } else {
                $shared_post_id = $this->xtfefoli_share_to_linkedin_profile_only_text( $message, $post_id, $access_token, $user_id );
            }

            if ( isset( $shared_post_id['message'] ) && !empty( $shared_post_id['message'] ) ) {
                $e_message      = $shared_post_id['message'];
            }else{
                $pure_id        = str_replace( 'urn:li:share:', '', $shared_post_id );
                if( is_numeric( $pure_id ) && $pure_id > 0 ) {
                    $e_message  = '';
                    $status     = 'shared';
                    $shared_pid = $pure_id;
                }
            }

            $insert_args = array(
                'post_id'             => $post_id,
                'shared_linkedin_id'  => $shared_pid,
                'page_profile_id'     => $user_id,
                'sharing_type'        => $sharing_type,
                'shared_time'         => current_time('mysql'),
                'status'              => $status,
                'error_message'       => $e_message,
                'message_content'     => $message,
            );

            $xt_feed_for_linkedin->common->xtfefoli_insert_linkedin_share( $insert_args );

        } elseif ( $type == 'page' ) {
            if ( $image_upload &&method_exists( $xt_feed_for_linkedin_pro->sharing_pro, 'xtfefoli_share_to_linkedin_page_with_image' ) ) {
                $shared_post_id = $xt_feed_for_linkedin_pro->sharing_pro->xtfefoli_share_to_linkedin_page_with_image( $message, $post_id, $access_token, $page_id );
            } else {
                $shared_post_id = $this->xtfefoli_share_to_linkedin_page_only_text( $message, $post_id, $access_token, $page_id );
            }

            if ( isset( $shared_post_id['message'] ) && !empty( $shared_post_id['message'] ) ) {
                $e_message      = $shared_post_id['message'];
            }else{
                $pure_id        = str_replace( 'urn:li:share:', '', $shared_post_id );
                if( is_numeric($pure_id) && $pure_id > 0 ) {
                    $e_message  = '';
                    $status     = 'shared';
                    $shared_pid = $pure_id;
                }
            }

            $insert_args = array(
                'post_id'             => $post_id,
                'shared_linkedin_id'  => $shared_pid,
                'page_profile_id'     => $page_id,
                'sharing_type'        => $sharing_type,
                'shared_time'         => current_time('mysql'),
                'status'              => $status,
                'error_message'       => $e_message,
                'message_content'     => $message,
            );

            $xt_feed_for_linkedin->common->xtfefoli_insert_linkedin_share( $insert_args );

        }
        return $shared_post_id;
    }
    
}