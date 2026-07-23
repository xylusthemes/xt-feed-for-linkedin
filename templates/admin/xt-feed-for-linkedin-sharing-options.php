<?php
/**
 * Admin Sharing Option page
 *
 * @package     XT_Feed_Linkedin
 * @subpackage  Admin/Pages
 * @copyright   Copyright (c) 2025, Rajat Patel
 * @since       1.1.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

global $xt_feed_for_linkedin;
$xtfefoli_options     = get_option( XTFEFOLI_OPTIONS, true );
$xtfefoli_so_options  = isset( $xtfefoli_options ) ? $xtfefoli_options : array();
$original_lfgsm       = esc_textarea("[POST_TITLE]\n\n Read more: [POST_LINK]\n\n [POST_EXCERPT]\n\n By [POST_AUTHOR] |  [WEBSITE_TITLE]");
$lfgsm_option         = get_option( 'xtfefoli_global_sharing_message', $original_lfgsm );
$selected_cpts        = isset( $xtfefoli_so_options['xtfefoli_linkedin_feedpress_cpts'] ) ? $xtfefoli_so_options['xtfefoli_linkedin_feedpress_cpts'] : array() ;
$xtfefoli_bddspol     = isset( $xtfefoli_so_options['xtfefoli_bddspol'] ) ? $xtfefoli_so_options['xtfefoli_bddspol'] : 'no' ;
$xtfefoli_postfimage  = isset( $xtfefoli_so_options['xtfefoli_postfimage'] ) ? $xtfefoli_so_options['xtfefoli_postfimage'] : 'no' ;




?>
<form method="post" id="saving_xtfefoli_sop">
    <?php
        // Fetch demo post
        $demo_post_title = 'Demo Post Title';
        $demo_post_link = get_site_url() . '/demo-post';
        $demo_post_excerpt = 'This is a demo post excerpt showcasing how the post will look.';
        $demo_post_author = 'Admin';
        $demo_website_title = get_bloginfo('name');
        $demo_post_image = '';

        $allowed_post_types = isset($xtfefoli_so_options['xtfefoli_linkedin_feedpress_cpts']) && is_array($xtfefoli_so_options['xtfefoli_linkedin_feedpress_cpts']) ? $xtfefoli_so_options['xtfefoli_linkedin_feedpress_cpts'] : array('post');

        if (empty($allowed_post_types)) {
            $allowed_post_types = array('post');
        }

        $demo_posts = get_posts(array(
            'post_type' => $allowed_post_types,
            'numberposts' => 1,
            'post_status' => 'publish'
        ));

        if (!empty($demo_posts)) {
            $demo_post = $demo_posts[0];
            $demo_post_title = $demo_post->post_title;
            $demo_post_link = get_permalink($demo_post->ID);
            $demo_post_excerpt = has_excerpt($demo_post->ID) ? $demo_post->post_excerpt : wp_trim_words($demo_post->post_content, 20);
            $author_id = $demo_post->post_author;
            $demo_post_author = get_the_author_meta('display_name', $author_id);
            $demo_post_image = get_the_post_thumbnail_url($demo_post->ID, 'large');
        }

        $active_account_name = esc_html__('Your Page / Profile Name', 'xt-feed-for-linkedin');
        $active_account_pic = '';

        $linkedin_users = get_option('xtfefoli_linkedin_user_data');
        if (!empty($linkedin_users) && is_array($linkedin_users)) {
            foreach ($linkedin_users as $user) {
                if (isset($user['is_active']) && $user['is_active'] === 'yes') {
                    $given = isset($user['given_name']) ? $user['given_name'] : '';
                    $family = isset($user['family_name']) ? $user['family_name'] : '';
                    $name = trim(ucfirst($given) . ' ' . ucfirst($family));
                    
                    if (!empty($name)) {
                        $active_account_name = $name;
                    } elseif (!empty($user['id'])) {
                        $active_account_name = $user['id'];
                    }
                    
                    $active_account_pic = isset($user['picture']) ? $user['picture'] : '';
                    break; // Just use the first active one we find
                }
            }
        }
    ?>

    <div class="form-table">
        <div class="lf-card mt-2" >
            <div class="header" >
                <div class="text" >
                    <div class="header-icon" ></div>
                    <div class="header-title" >
                        <span><?php esc_attr_e( 'Sharing Options', 'xt-feed-for-linkedin' ); ?></span>
                    </div>
                </div>
            </div>
            <div class="content" >
                <div style="display: flex;flex-direction: row;gap:30px;flex-wrap:wrap;" >
                    <div style="flex: 1; min-width: 300px;" >
                        <div class="xtfefoli_custom-linkedin-metabox-setting" style="margin-bottom: 20px;">
                            <label for="xtfefoli_custom-share-message" style="display: block; font-weight: bold; margin-bottom: 8px;"><?php esc_attr_e( 'Default Share Message', 'xt-feed-for-linkedin' ); ?></label>
                            <textarea  rows="8" name="xtfefoli_global_sharing_message" id="xtfefoli_custom-share-message" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; font-family: inherit;"><?php echo esc_textarea( $lfgsm_option ); ?></textarea>
                        </div>
                        
                        <div style="margin-top: 15px;">
                            <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #555;"><?php esc_attr_e( 'Default Share Message Keywords', 'xt-feed-for-linkedin' ); ?></label>
                            <div style="display: flex;flex-direction: row;flex-wrap: wrap;gap: 10px;" >
                                <span class="lf-post-type-item xtfefoli_sp_keyword" value="[POST_TITLE]" style="padding: 5px 10px; font-size: 12px; background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; border-radius: 4px;"><?php esc_attr_e( '[POST_TITLE]', 'xt-feed-for-linkedin' ); ?></span>
                                <span class="lf-post-type-item xtfefoli_sp_keyword" value="[POST_LINK]" style="padding: 5px 10px; font-size: 12px; background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; border-radius: 4px;"><?php esc_attr_e( '[POST_LINK]', 'xt-feed-for-linkedin' ); ?></span>
                                <span class="lf-post-type-item xtfefoli_sp_keyword" value="[POST_EXCERPT]" style="padding: 5px 10px; font-size: 12px; background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; border-radius: 4px;"><?php esc_attr_e( '[POST_EXCERPT]', 'xt-feed-for-linkedin' ); ?></span>
                                <span class="lf-post-type-item xtfefoli_sp_keyword" value="[POST_AUTHOR]" style="padding: 5px 10px; font-size: 12px; background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; border-radius: 4px;"><?php esc_attr_e( '[POST_AUTHOR]', 'xt-feed-for-linkedin' ); ?></span>
                                <span class="lf-post-type-item xtfefoli_sp_keyword" value="[WEBSITE_TITLE]" style="padding: 5px 10px; font-size: 12px; background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; border-radius: 4px;"><?php esc_attr_e( '[WEBSITE_TITLE]', 'xt-feed-for-linkedin' ); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div style="flex: 1; min-width: 300px; display: flex; flex-direction: column;">
                        <label style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_attr_e( 'LinkedIn Preview', 'xt-feed-for-linkedin' ); ?></label>
                        <p style="margin: 0 0 10px 0; font-size: 13px; color: #6b7280;">
                            <?php esc_attr_e( 'Your sharing message will look like this on LinkedIn (This is only for preview purposes).', 'xt-feed-for-linkedin' ); ?>
                        </p>
                        
                        <div class="xtfefoli-preview-container" style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); padding: 20px; flex-grow: 1;">
                            
                            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                <?php if (!empty($active_account_pic)): ?>
                                    <img src="<?php echo esc_url($active_account_pic); ?>" alt="Profile Picture" style="width: 48px; height: 48px; border-radius: 50%; margin-right: 12px; object-fit: cover; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <?php else: ?>
                                    <div style="width: 48px; height: 48px; background: #e5e7eb; border-radius: 50%; margin-right: 12px; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div style="font-weight: 600; color: #111827; font-size: 14px;"><?php echo esc_html($active_account_name); ?></div>
                                    <div style="color: #6b7280; font-size: 12px;">Just now &bull; 🌐</div>
                                </div>
                            </div>
                            
                            <div id="xtfefoli_message_preview_content" style="font-size: 14px; color: #111827; line-height: 1.5; word-wrap: break-word;">
                                <!-- Live preview will appear here -->
                            </div>
                            
                            <!-- Placeholder for article card in linkedin -->
                            <div style="margin-top: 15px; border: 1px solid #e5e7eb; border-radius: 4px; overflow: hidden;">
                                <?php if (!empty($demo_post_image)) : ?>
                                    <div style="height: 150px; background-image: url('<?php echo esc_url($demo_post_image); ?>'); background-size: cover; background-position: center; border-bottom: 1px solid #e5e7eb;"></div>
                                <?php else : ?>
                                    <div style="height: 150px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; color: #9ca3af; border-bottom: 1px solid #e5e7eb;">
                                        <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                <?php endif; ?>
                                <div style="padding: 10px 15px; background: #f9fafb;">
                                    <div style="font-weight: 600; color: #111827; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo esc_html($demo_post_title); ?></div>
                                    <div style="color: #6b7280; font-size: 12px; margin-top: 2px;"><?php echo esc_html(parse_url($demo_post_link, PHP_URL_HOST)); ?></div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
        global $wp_post_types;
        $post_types = get_post_types( [ 'public' => true], 'objects' );
        unset( $post_types['attachment'] );
        unset( $post_types['xtlf_share_schedule'] );
    ?>
    <div class="form-table">
        <div class="lf-card mt-2">
            <div class="header">
                <div class="text">
                    <div class="header-icon"></div>
                    <div class="header-title">
                        <span><?php esc_attr_e( 'Allow To Share Post Type', 'xt-feed-for-linkedin' ); ?></span>
                    </div>
                </div>
            </div>
            <div class="content">
                <div style="display: flex;flex-direction: row;gap: 10px;flex-wrap: wrap;">
                    <?php foreach ( $post_types as $post_type ) : ?>
                        <div class="lf-post-type-item" >
                            <div class="lf-actions">
                                <span>
                                    <?php echo esc_html( $post_type->labels->singular_name ); ?>
                                </span>
                                <label class="lf-switch">
                                    <input type="checkbox" <?php if( in_array( $post_type->name, $selected_cpts ) ){ echo 'checked'; } ?> name="xtfefoli_sharing_option[xtfefoli_linkedin_feedpress_cpts][]"  id="post-type-<?php echo esc_attr( $post_type->name ); ?>" value="<?php echo esc_attr( $post_type->name ); ?>" >
                                    <span class="lf-slider round"></span>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-table">
        <div class="lf-card mt-2">
            <div class="header">
                <div class="text">
                    <div class="header-icon"></div>
                    <div class="header-title">
                        <span><?php esc_attr_e( 'Sharing Post Featured Image', 'xt-feed-for-linkedin' ); ?></span>
                    </div>
                </div>
            </div>
            <div class="content">
                <?php 
                    if ( function_exists( 'xtlf_is_pro' ) && xtlf_is_pro() ) { 
                        ?>
                        <span>
                            <input type="checkbox" <?php if( $xtfefoli_postfimage == 'on' ){ echo 'checked'; }; ?> name="xtfefoli_sharing_option[xtfefoli_postfimage]" />
                            <?php esc_attr_e( 'Share Post Featured Image', 'xt-feed-for-linkedin' ); ?>
                        </span>
                        <?php 
                    }else{ 
                        ?>
                        <div class="lf-card-center" style="color:#d9534f; font-weight:bold;">
                            <?php esc_attr_e( 'Upgrade to' ); ?><strong><?php esc_attr_e( ' Pro' ); ?></strong> <?php esc_attr_e( ' to unlock the Post Featured Image sharing option'); ?>
                        </div>
                        <?php 
                    } 
                ?>
            </div>
        </div>
    </div>

    <div class="form-table">
        <div class="lf-card mt-2">
            <div class="header">
                <div class="text">
                    <div class="header-icon"></div>
                    <div class="header-title">
                        <span><?php esc_attr_e( 'By default don\'t auto share posts on LinkedIn', 'xt-feed-for-linkedin' ); ?></span>
                    </div>
                </div>
            </div>
            <div class="content">
                <div>
                    <input type="checkbox" <?php if( $xtfefoli_bddspol == 'on' ){ echo 'checked'; }; ?> name="xtfefoli_sharing_option[xtfefoli_bddspol]"  ><?php esc_attr_e( 'By default don\'t share posts on LinkedIn', 'xt-feed-for-linkedin' ); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="xtfefoli_element">
        <input type="hidden" name="xtfefoli_so_action" value="xtfefoli_save_so_settings" />
        <?php wp_nonce_field( 'xtfefoli_so_setting_form_nonce_action', 'xtfefoli_so_setting_form_nonce' ); ?>
        <input type="submit" class="xtfefoli_button" style="display: flex;align-items: center;color: #fff;" value="<?php esc_attr_e( 'Save Settings', 'xt-feed-for-linkedin' ); ?>" >
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('xtfefoli_custom-share-message');
    const previewBox = document.getElementById('xtfefoli_message_preview_content');
    
    if (!textarea || !previewBox) return;

    const demoData = {
        '[POST_TITLE]': <?php echo wp_json_encode($demo_post_title); ?>,
        '[POST_LINK]': <?php echo wp_json_encode($demo_post_link); ?>,
        '[POST_EXCERPT]': <?php echo wp_json_encode($demo_post_excerpt); ?>,
        '[POST_AUTHOR]': <?php echo wp_json_encode($demo_post_author); ?>,
        '[WEBSITE_TITLE]': <?php echo wp_json_encode($demo_website_title); ?>
    };

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function updatePreview() {
        let text = textarea.value;
        for (const [key, value] of Object.entries(demoData)) {
            // Use split/join for all occurrences
            text = text.split(key).join(value);
        }
        
        // Escape HTML to prevent XSS in preview, then convert URLs to links, then newlines to <br>
        let htmlText = escapeHtml(text);
        
        // Convert URLs to clickable links (LinkedIn style)
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        htmlText = htmlText.replace(urlRegex, function(url) {
            return '<a href="' + url + '" target="_blank" style="color: #0a66c2; text-decoration: none; font-weight: 600;" onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'">' + url + '</a>';
        });

        previewBox.innerHTML = htmlText.replace(/\n/g, '<br>');
        
        if (text.trim() === '') {
            previewBox.innerHTML = '<span style="color: #9ca3af; font-style: italic;">Your message will appear here...</span>';
        }
    }

    textarea.addEventListener('input', updatePreview);
    updatePreview(); // Initial render
    
    // Make keywords clickable
    const keywords = document.querySelectorAll('.xtfefoli_sp_keyword');
    keywords.forEach(function(keyword) {
        keyword.style.cursor = 'pointer';
        keyword.addEventListener('click', function() {
            const val = this.getAttribute('value');
            const startPos = textarea.selectionStart;
            const endPos = textarea.selectionEnd;
            textarea.value = textarea.value.substring(0, startPos) + val + textarea.value.substring(endPos, textarea.value.length);
            textarea.focus();
            textarea.selectionStart = startPos + val.length;
            textarea.selectionEnd = startPos + val.length;
            updatePreview();
        });
    });
});
</script>
