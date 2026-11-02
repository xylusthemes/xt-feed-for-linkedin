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
        if ( ! is_array( $columns ) ) {
            $columns = array();
        }
        unset( $columns['xtfefoli_shared_on_linkedin'] );
        $columns['xtfefoli_shared_on_linkedin'] = esc_html__( 'XT Feed for LinkedIn', 'xt-feed-for-linkedin' );
        return $columns;
    }

    /**
     * Display LinkedIn shared column
     *
     * @param string $column
     * @param int $post_id
     * @return void
     */
    /**
     * Get HTML markup for the LinkedIn column in posts list table.
     *
     * @param int    $post_id            Post ID.
     * @param string $xtfefoli_post_type Post type (optional).
     * @return string Rendered HTML.
     */
    public function xtfefoli_get_column_html( $post_id, $xtfefoli_post_type = '' ) {
        global $xt_feed_for_linkedin;

        if ( empty( $xtfefoli_post_type ) ) {
            $xtfefoli_post_type = get_post_type( $post_id );
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            if ( ! $xtfefoli_post_type && isset( $_GET['post_type'] ) ) {
                $xtfefoli_post_type = sanitize_text_field( wp_unslash( $_GET['post_type'] ) );
            }
            if ( ! $xtfefoli_post_type ) {
                $xtfefoli_post_type = 'post';
            }
        }

        $get_shared_histories = get_post_meta( $post_id, '_xtfefoli_sended_on_linkedin', true );

        $valid_histories = array();
        if ( ! empty( $get_shared_histories ) && is_array( $get_shared_histories ) ) {
            foreach ( $get_shared_histories as $get_sh ) {
                if ( isset( $get_sh['id'] ) && ! is_array( $get_sh['id'] ) && ! empty( $get_sh['id'] ) ) {
                    $valid_histories[] = $get_sh;
                }
            }
        }

        $is_scheduled              = false;
        $schedule_datetime_display = '';
        if ( get_post_meta( $post_id, '_xtfefoli_schedule_enabled', true ) ) {
            $raw_dt = get_post_meta( $post_id, '_xtfefoli_schedule_datetime', true );
            if ( ! empty( $raw_dt ) ) {
                $is_scheduled = true;
                $ts = is_numeric( $raw_dt ) ? (int) $raw_dt : strtotime( $raw_dt );
                if ( $ts ) {
                    $schedule_datetime_display = gmdate( 'M j, H:i', $ts );
                }
            }
        }

        ob_start();
        ?>
        <div class="xtfefoli-column-card" data-post-id="<?php echo esc_attr( $post_id ); ?>">
            <input type="hidden" id="xtfefoli_linkedin_feedpress_lt_nonce" name="xtfefoli_linkedin_feedpress_lt_nonce" value="<?php echo esc_attr( wp_create_nonce( 'xtfefoli_linkedin_feedpress_lt_nonce' ) ); ?>" />

            <!-- Top Status Bar -->
            <div class="xtfefoli-status-bar">
                <?php if ( ! empty( $valid_histories ) ) : ?>
                    <span class="xtfefoli-badge xtfefoli-badge-shared">
                        <span class="xtfefoli-badge-dot"></span>
                        <span><?php esc_html_e( 'Shared', 'xt-feed-for-linkedin' ); ?></span>
                        <span class="xtfefoli-badge-count"><?php echo count( $valid_histories ); ?></span>
                    </span>
                <?php elseif ( $is_scheduled ) : ?>
                    <span class="xtfefoli-badge xtfefoli-badge-scheduled" title="<?php echo esc_attr( $schedule_datetime_display ); ?>">
                        <span class="dashicons dashicons-clock"></span>
                        <span><?php esc_html_e( 'Scheduled', 'xt-feed-for-linkedin' ); ?></span>
                        <?php if ( $schedule_datetime_display ) : ?>
                            <span class="xtfefoli-scheduled-dt"><?php echo esc_html( $schedule_datetime_display ); ?></span>
                        <?php endif; ?>
                    </span>
                <?php else : ?>
                    <span class="xtfefoli-badge xtfefoli-badge-not-shared xtfefoli_post_<?php echo esc_attr( $post_id ); ?>">
                        <span class="xtfefoli-badge-dot"></span>
                        <span><?php esc_html_e( 'Not Shared', 'xt-feed-for-linkedin' ); ?></span>
                    </span>
                <?php endif; ?>
            </div>

            <!-- History Section -->
            <?php if ( ! empty( $valid_histories ) ) : ?>
                <div class="xtfefoli-history-box">
                    <?php
                    // Latest share
                    $latest        = end( $valid_histories );
                    $latest_url    = esc_url( 'https://www.linkedin.com/feed/update/' . $latest['id'] );
                    $latest_suffix = $this->xtfefoli_history_account_suffix( $latest );
                    $account_label = ! empty( $latest['account'] ) ? $xt_feed_for_linkedin->common->xtfefoli_get_account_label( $latest['account'] ) : '';
                    if ( empty( $account_label ) && ! empty( $latest_suffix ) ) {
                        $account_label = ltrim( $latest_suffix, " \t\n\r\0\x0B–-" );
                    }
                    if ( empty( $account_label ) ) {
                        $account_label = esc_html__( 'LinkedIn Post', 'xt-feed-for-linkedin' );
                    }
                    $latest_date = ! empty( $latest['shared_post_datetime'] ) ? gmdate( 'Y-m-d H:i', $latest['shared_post_datetime'] ) : '';
                    ?>
                    <div class="xtfefoli-history-item xtfefoli-history-primary">
                        <div class="xtfefoli-history-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="#0a66c2"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        </div>
                        <div class="xtfefoli-history-details">
                            <a href="<?php echo esc_url( $latest_url ); ?>" target="_blank" class="xtfefoli-post-link" title="<?php esc_attr_e( 'View post on LinkedIn', 'xt-feed-for-linkedin' ); ?>">
                                <span><?php echo esc_html( $account_label ); ?></span>
                                <span class="dashicons dashicons-external"></span>
                            </a>
                            <?php if ( $latest_date ) : ?>
                                <span class="xtfefoli-history-time"><?php echo esc_html( $latest_date ); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ( count( $valid_histories ) > 1 ) : ?>
                        <button type="button" class="xtfefoli-history-toggle" aria-expanded="false">
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                            <span class="xtfefoli-toggle-text"><?php echo esc_html( sprintf( __( 'All shares (%d)', 'xt-feed-for-linkedin' ), count( $valid_histories ) ) ); ?></span>
                        </button>
                        <div class="xtfefoli-history-drawer" style="display: none;">
                            <?php
                            $older = array_reverse( array_slice( $valid_histories, 0, -1 ) );
                            foreach ( $older as $item ) :
                                $item_url     = esc_url( 'https://www.linkedin.com/feed/update/' . $item['id'] );
                                $item_suffix  = $this->xtfefoli_history_account_suffix( $item );
                                $item_account = ! empty( $item['account'] ) ? $xt_feed_for_linkedin->common->xtfefoli_get_account_label( $item['account'] ) : '';
                                if ( empty( $item_account ) && ! empty( $item_suffix ) ) {
                                    $item_account = ltrim( $item_suffix, " \t\n\r\0\x0B–-" );
                                }
                                if ( empty( $item_account ) ) {
                                    $item_account = esc_html__( 'LinkedIn Post', 'xt-feed-for-linkedin' );
                                }
                                $item_date = ! empty( $item['shared_post_datetime'] ) ? gmdate( 'Y-m-d H:i', $item['shared_post_datetime'] ) : '';
                                ?>
                                <div class="xtfefoli-history-item xtfefoli-history-secondary">
                                    <div class="xtfefoli-history-icon-sm" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" width="10" height="10" fill="#0a66c2"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                    </div>
                                    <div class="xtfefoli-history-details">
                                        <a href="<?php echo esc_url( $item_url ); ?>" target="_blank" class="xtfefoli-post-link">
                                            <span><?php echo esc_html( $item_account ); ?></span>
                                            <span class="dashicons dashicons-external"></span>
                                        </a>
                                        <?php if ( $item_date ) : ?>
                                            <span class="xtfefoli-history-time"><?php echo esc_html( $item_date ); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Share again button -->
                    <button type="button" class="xtfefoli-btn-share-again" data-target="#xtfefoli-action-drawer-<?php echo esc_attr( $post_id ); ?>">
                        <span class="dashicons dashicons-share-alt"></span>
                        <span><?php esc_html_e( 'Share again', 'xt-feed-for-linkedin' ); ?></span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Action Drawer (Share Now & Schedule) -->
            <div class="xtfefoli-action-drawer" id="xtfefoli-action-drawer-<?php echo esc_attr( $post_id ); ?>" style="<?php echo ! empty( $valid_histories ) ? 'display: none;' : ''; ?>">
                <div class="xtfefoli-share-column-container">
                    <div class="xtfefoli-accounts-wrapper">
                        <span class="xtfefoli-accounts-label"><?php esc_html_e( 'Select Account:', 'xt-feed-for-linkedin' ); ?></span>
                        <?php echo $xt_feed_for_linkedin->common->xtfefoli_get_accounts_select_html( '', 'xtfefoli_share_account_' . esc_attr( $post_id ), 'xtfefoli-share-account' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                    <div class="xtfefoli-btn-row">
                        <button type="button" class="xtfefoli_share_cpt_button_lt xtfefoli-btn-primary" data-post_id="<?php echo esc_attr( $post_id ); ?>" data-post_type="<?php echo esc_attr( $xtfefoli_post_type ); ?>">
                            <span class="xtfefoli-li-icon-btn" aria-hidden="true">in</span>
                            <span><?php esc_html_e( 'Share Now', 'xt-feed-for-linkedin' ); ?></span>
                        </button>
                    </div>
                    <div class="if_lt_notice"></div>
                </div>
                <?php
                do_action( 'xtfefoli_save_schedule_post', $post_id );
                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
                do_action( 'xtlf_save_schedule_post', $post_id );
                ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
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
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $xtfefoli_post_type = isset( $_GET['post_type'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) ) : '';
            echo $this->xtfefoli_get_column_html( $post_id, $xtfefoli_post_type ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
                add_filter( "manage_{$post_type}_posts_columns", array( $this, 'xtfefoli_add_linkedin_shared_column' ), 999 );
                add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'xtfefoli_display_linkedin_shared_column' ), 10, 2 );
            }
        }
    }

    /**
     * Get post content settings (description limit + HTML support).
     *
     * @since 1.0.6
     * @return array { total_limit:int, desc_limit:int (0 = auto), html_support:bool }
     */
    public function xtfefoli_get_content_settings() {
        $options     = xtfefoli_get_options();
        $total_limit = xtfefoli_is_pro() ? 2500 : 1000;
        $desc_limit  = 0;
        $html        = false;

        if ( is_array( $options ) ) {
            if ( isset( $options['xtfefoli_desc_limit'] ) ) {
                $desc_limit = absint( $options['xtfefoli_desc_limit'] );
            }
            if ( isset( $options['xtfefoli_html_support'] ) && 'on' === $options['xtfefoli_html_support'] ) {
                $html = true;
            }
        }

        if ( $desc_limit > $total_limit ) {
            $desc_limit = $total_limit;
        }

        return apply_filters(
            'xtfefoli_content_settings',
            array(
                'total_limit'  => $total_limit,
                'desc_limit'   => $desc_limit,
                'html_support' => $html,
            )
        );
    }

    /**
     * Convert HTML into LinkedIn friendly plain text.
     *
     * LinkedIn's posting API only accepts plain text, so real HTML can never be rendered.
     * This keeps the structure readable instead of flattening everything:
     * paragraphs/headings/line breaks => new lines, list items => "• item",
     * links => "text (url)", <strong>/<b> and <em>/<i> => Unicode bold / italic characters.
     *
     * @since 1.0.6
     * @param string $html HTML string.
     * @return string Plain text.
     */
    public function xtfefoli_html_to_text( $html ) {
        $text = (string) $html;
        if ( '' === trim( $text ) ) {
            return '';
        }

        // Drop script/style blocks and HTML comments (Gutenberg block comments included).
        $text = preg_replace( '#<(script|style)\b[^>]*>.*?</\1>#is', '', $text );
        $text = preg_replace( '/<!--.*?-->/s', '', $text );
        $text = str_replace( array( "\r\n", "\r" ), "\n", $text );

        // Links => "text (url)".
        $text = preg_replace_callback(
            '#<a\s[^>]*?href\s*=\s*(["\'])(.*?)\1[^>]*>(.*?)</a>#is',
            function ( $m ) {
                $url   = trim( html_entity_decode( $m[2], ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
                $label = trim( strip_tags( $m[3] ) );
                if ( '' === $url || '#' === $url[0] || preg_match( '#^(javascript|mailto|tel):#i', $url ) ) {
                    return $m[3];
                }
                if ( '' === $label || html_entity_decode( $label, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) === $url ) {
                    return $url;
                }
                return $m[3] . ' (' . $url . ')';
            },
            $text
        );

        // Structure => new lines / bullets.
        $text = preg_replace( '#<br\s*/?>#i', "\n", $text );
        $text = preg_replace( '#<hr\b[^>]*>#i', "\n\n", $text );
        $text = preg_replace( '#<(ul|ol)\b[^>]*>#i', "\n", $text );
        $text = preg_replace( '#<li\b[^>]*>#i', "\n• ", $text );
        $text = preg_replace( '#</li>#i', '', $text );
        $text = preg_replace( '#</(p|h[1-6]|ul|ol|blockquote|pre|table|figure)>#i', "\n\n", $text );
        $text = preg_replace( '#</(div|tr|section|article|header|footer)>#i', "\n", $text );

        // Keep only inline emphasis tags for the next step.
        $text = strip_tags( $text, '<b><strong><i><em>' );

        $text = preg_replace_callback(
            '#<(b|strong)\b[^>]*>(.*?)</\1>#is',
            function ( $m ) {
                return $this->xtfefoli_unicode_style( strip_tags( $m[2] ), 'bold' );
            },
            $text
        );
        $text = preg_replace_callback(
            '#<(i|em)\b[^>]*>(.*?)</\1>#is',
            function ( $m ) {
                return $this->xtfefoli_unicode_style( strip_tags( $m[2] ), 'italic' );
            },
            $text
        );

        $text = strip_tags( $text );
        $text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        $text = str_replace( "\xC2\xA0", ' ', $text );

        // Tidy whitespace.
        $text = preg_replace( "/[ \t]+\n/", "\n", $text );
        $text = preg_replace( "/\n[ \t]+/", "\n", $text );
        $text = preg_replace( "/\n{3,}/", "\n\n", $text );

        return trim( $text );
    }

    /**
     * Map ASCII letters/digits to Unicode sans-serif bold / italic characters.
     *
     * @since 1.0.6
     * @param string $text  Text.
     * @param string $style 'bold' or 'italic'.
     * @return string
     */
    private function xtfefoli_unicode_style( $text, $style ) {
        // Mathematical Sans-Serif: bold A/a/0, italic A/a (italic has no digits).
        $bases = ( 'bold' === $style ) ? array( 0x1D5D4, 0x1D5EE, 0x1D7EC ) : array( 0x1D608, 0x1D622, null );

        return preg_replace_callback(
            '/[A-Za-z0-9]/',
            function ( $m ) use ( $bases ) {
                $char = $m[0];
                $ord  = ord( $char );
                if ( $ord >= 65 && $ord <= 90 ) {
                    $cp = $bases[0] + ( $ord - 65 );
                } elseif ( $ord >= 97 && $ord <= 122 ) {
                    $cp = $bases[1] + ( $ord - 97 );
                } else {
                    if ( null === $bases[2] ) {
                        return $char;
                    }
                    $cp = $bases[2] + ( $ord - 48 );
                }
                return html_entity_decode( '&#' . $cp . ';', ENT_NOQUOTES, 'UTF-8' );
            },
            $text
        );
    }

    /**
     * Build the final share message from the template.
     * Applies the "Description character limit" and "HTML support" settings.
     *
     * @param string $sanitized_message Message template.
     * @param int    $post_id           Post ID.
     * @return string|false
     */
    public function xtfefoli_rander_share_shared_message( $sanitized_message, $post_id ) {

        $settings      = $this->xtfefoli_get_content_settings();
        $total_limit   = $settings['total_limit'];
        $html_support  = $settings['html_support'];

        $post_title    = get_the_title( $post_id );
        $post_link     = get_permalink( $post_id );
        $post_author   = get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) );
        $website_title = get_bloginfo( 'name' );

        // Base message without excerpt
        $base_message = str_replace(
            ['[POST_TITLE]', '[POST_LINK]', '[POST_EXCERPT]', '[POST_AUTHOR]', '[WEBSITE_TITLE]'],
            [$post_title, $post_link, '[POST_EXCERPT]', $post_author, $website_title],
            $sanitized_message
        );

        if ( $html_support ) {
            $base_message = $this->xtfefoli_html_to_text( $base_message );
        }

        // Room left for the description: total LinkedIn limit minus the rest of the message,
        // further capped by the "Description character limit" setting (0 = auto / use all room).
        $max_chars_for_excerpt = $total_limit - mb_strlen( $base_message );
        if ( $settings['desc_limit'] > 0 ) {
            $max_chars_for_excerpt = min( $max_chars_for_excerpt, $settings['desc_limit'] );
        }

        if ( $max_chars_for_excerpt <= 0 ) {
            return false; 
        }

        $excerpt_content = get_post_field( 'post_content', $post_id );

        if ( $html_support ) {
            $excerpt_content = $this->xtfefoli_html_to_text( $excerpt_content );
        } else {
            // Strip tags but keep line breaks
            $excerpt_content = strip_tags( $excerpt_content, '<br><p>' );

            // Replace <p> and <br> with actual newlines
            $excerpt_content = str_replace( ['<p>', '</p>', '<br>', '<br/>', '<br />'], "\n", $excerpt_content );
        }

        // Trim according to limit (the "..." is counted inside the limit)
        if ( mb_strlen( $excerpt_content ) > $max_chars_for_excerpt ) {
            $post_excerpt = rtrim( mb_substr( $excerpt_content, 0, max( 0, $max_chars_for_excerpt - 3 ) ) ) . '...';
        } else {
            $post_excerpt = $excerpt_content;
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
            return array('message' => $response->get_error_message());
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
            return array('message' => $response->get_error_message());
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
     * Friendly error text for a raw LinkedIn error message.
     *
     * @since 1.0.6
     * @param string $e_message Raw error.
     * @return string
     */
    public function xtfefoli_format_share_error( $e_message ) {
        if ( false !== strpos( (string) $e_message, 'Content is a duplicate' ) ) {
            return 'This Content is already shared on LinkedIn. You may share it again after a short wait.';
        }
        return 'An error occurred: ' . $e_message;
    }

    /**
     * Share one message to ONE LinkedIn account (profile or page) and log it.
     *
     * @since 1.0.6
     * @param string $message      Final message.
     * @param int    $post_id      Post ID.
     * @param string $sharing_type manual|scheduled|auto_publish.
     * @param array  $account      Account array from XT_Feed_Linkedin_Common::xtfefoli_resolve_target_accounts().
     * @return array { identifier, label, status (shared|failed), id, error }
     */
    public function xtfefoli_share_to_account( $message, $post_id, $sharing_type, $account ) {
        global $xt_feed_for_linkedin, $xt_feed_for_linkedin_pro;

        $access_token = isset( $account['token'] ) ? $account['token'] : '';
        $type         = isset( $account['type'] ) ? $account['type'] : '';
        $user_id      = isset( $account['user_id'] ) ? $account['user_id'] : '';
        $page_id      = isset( $account['page_id'] ) ? $account['page_id'] : '';
        $identifier   = isset( $account['identifier'] ) ? $account['identifier'] : '';

        $result = array(
            'identifier' => $identifier,
            'label'      => $identifier ? $xt_feed_for_linkedin->common->xtfefoli_get_account_label( $identifier ) : '',
            'status'     => 'failed',
            'id'         => null,
            'error'      => '',
        );

        if ( empty( $access_token ) || empty( $type ) ) {
            $result['error'] = 'Missing access token for this account. Please re-authorize it.';
            return $result;
        }

        $lf_get_option = xtfefoli_get_options();
        $image_upload  = ( is_array( $lf_get_option ) && isset( $lf_get_option['xtfefoli_postfimage'] ) );
        $has_pro_image = ! empty( $xt_feed_for_linkedin_pro->sharing_pro );

        if ( 'profile' === $type ) {
            $author_id = $user_id;
            if ( $image_upload && $has_pro_image && method_exists( $xt_feed_for_linkedin_pro->sharing_pro, 'xtfefoli_share_to_linkedin_profile_with_image' ) ) {
                $shared_post_id = $xt_feed_for_linkedin_pro->sharing_pro->xtfefoli_share_to_linkedin_profile_with_image( $message, $post_id, $access_token, $user_id );
            } else {
                $shared_post_id = $this->xtfefoli_share_to_linkedin_profile_only_text( $message, $post_id, $access_token, $user_id );
            }
        } else {
            $author_id = $page_id;
            if ( $image_upload && $has_pro_image && method_exists( $xt_feed_for_linkedin_pro->sharing_pro, 'xtfefoli_share_to_linkedin_page_with_image' ) ) {
                $shared_post_id = $xt_feed_for_linkedin_pro->sharing_pro->xtfefoli_share_to_linkedin_page_with_image( $message, $post_id, $access_token, $page_id );
            } else {
                $shared_post_id = $this->xtfefoli_share_to_linkedin_page_only_text( $message, $post_id, $access_token, $page_id );
            }
        }

        $shared_pid = null;
        if ( is_array( $shared_post_id ) && ! empty( $shared_post_id['message'] ) ) {
            $result['error'] = (string) $shared_post_id['message'];
        } elseif ( is_string( $shared_post_id ) && '' !== $shared_post_id ) {
            $result['status'] = 'shared';
            $result['id']     = $shared_post_id;
            $pure_id          = str_replace( 'urn:li:share:', '', $shared_post_id );
            $shared_pid       = ( is_numeric( $pure_id ) && $pure_id > 0 ) ? $pure_id : null;
        } else {
            $result['error'] = 'LinkedIn did not return a post ID.';
        }

        $xt_feed_for_linkedin->common->xtfefoli_insert_linkedin_share( array(
            'post_id'             => $post_id,
            'shared_linkedin_id'  => $shared_pid,
            'page_profile_id'     => $author_id,
            'sharing_type'        => $sharing_type,
            'shared_time'         => current_time( 'mysql' ),
            'status'              => $result['status'],
            'error_message'       => $result['error'],
            'message_content'     => $message,
        ) );

        return $result;
    }

    /**
     * Share a post to one OR MANY LinkedIn accounts.
     *
     * @since 1.0.6
     * @param string       $message        Final message.
     * @param int          $post_id        Post ID.
     * @param string       $sharing_type   manual|scheduled|auto_publish.
     * @param string|array $target_account Identifier(s) like "profile:abc"/"page:123". Empty/"default" = all active accounts.
     * @return array { no_account:bool, results:array, shared:int, failed:int }
     */
    public function xtfefoli_share_post_to_accounts( $message, $post_id, $sharing_type, $target_account = array() ) {
        global $xt_feed_for_linkedin;

        $accounts = $xt_feed_for_linkedin->common->xtfefoli_resolve_target_accounts( $target_account );
        $outcome  = array(
            'no_account' => false,
            'results'    => array(),
            'shared'     => 0,
            'failed'     => 0,
        );

        if ( empty( $accounts ) ) {
            $outcome['no_account'] = true;

            $xt_feed_for_linkedin->common->xtfefoli_insert_linkedin_share( array(
                'post_id'             => $post_id,
                'shared_linkedin_id'  => 0,
                'page_profile_id'     => 0,
                'sharing_type'        => $sharing_type,
                'shared_time'         => current_time( 'mysql' ),
                'status'              => 'failed',
                'error_message'       => 'Please select at least one account to proceed with sharing.',
                'message_content'     => $message,
            ) );

            return $outcome;
        }

        foreach ( $accounts as $account ) {
            $result                = $this->xtfefoli_share_to_account( $message, $post_id, $sharing_type, $account );
            $outcome['results'][]  = $result;
            if ( 'shared' === $result['status'] ) {
                $outcome['shared']++;
            } else {
                $outcome['failed']++;
            }
        }

        return $outcome;
    }

    /**
     * Save post meta (share history + flags) for every successful share of an outcome.
     *
     * @since 1.0.6
     * @param int    $post_id Post ID.
     * @param array  $outcome Result of xtfefoli_share_post_to_accounts().
     * @param string $message Message template to remember for this post.
     * @return array Successful history entries that were added.
     */
    public function xtfefoli_record_shared_results( $post_id, $outcome, $message ) {
        $added = array();

        if ( empty( $outcome['shared'] ) || empty( $outcome['results'] ) ) {
            return $added;
        }

        $existing_data = get_post_meta( $post_id, '_xtfefoli_sended_on_linkedin', true );
        if ( ! is_array( $existing_data ) ) {
            $existing_data = array();
        }

        foreach ( $outcome['results'] as $result ) {
            if ( 'shared' !== $result['status'] ) {
                continue;
            }
            $entry = array(
                'id'                   => $result['id'],
                'shared_post_datetime' => time(),
                'account'              => $result['identifier'],
            );
            $existing_data[] = $entry;
            $added[]         = $entry;
        }

        update_post_meta( $post_id, '_xtfefoli_sended_on_linkedin', $existing_data );
        update_post_meta( $post_id, '_xtfefoli_shared_on_linkedin', 1 );
        update_post_meta( $post_id, '_xtfefoli_dont_share_post_linkedin', 1 );
        update_post_meta( $post_id, '_xtfefoli_share_message', $message );

        return $added;
    }

    /**
     * Small " - Account Name" suffix for share history lines (empty for old entries).
     *
     * @since 1.0.6
     * @param array $entry History entry.
     * @return string Escaped HTML.
     */
    public function xtfefoli_history_account_suffix( $entry ) {
        global $xt_feed_for_linkedin;
        if ( empty( $entry['account'] ) ) {
            return '';
        }
        return ' &ndash; ' . esc_html( $xt_feed_for_linkedin->common->xtfefoli_get_account_label( $entry['account'] ) );
    }

    /**
     * Backward compatible single-result wrapper (kept for third party code).
     * Returns the first LinkedIn post ID, an array with 'message' on failure, or null when there is no account.
     *
     * @param string       $message        Final message.
     * @param int          $post_id        Post ID.
     * @param string       $sharing_type   manual|scheduled|auto_publish.
     * @param string|array $target_account Account identifier(s).
     * @return string|array|null
     */
    public function xtfefoli_share_cpts_in_selected_account( $message, $post_id, $sharing_type, $target_account = '' ){
        $outcome = $this->xtfefoli_share_post_to_accounts( $message, $post_id, $sharing_type, $target_account );

        if ( $outcome['no_account'] ) {
            return null;
        }

        foreach ( $outcome['results'] as $result ) {
            if ( 'shared' === $result['status'] ) {
                return $result['id'];
            }
        }

        $first = reset( $outcome['results'] );
        return array( 'message' => $first ? $first['error'] : '' );
    }
    
}
