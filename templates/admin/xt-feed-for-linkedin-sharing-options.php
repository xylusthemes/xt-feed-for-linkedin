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




?>
<form method="post" id="saving_xtfefoli_sop">
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
                <div style="display: flex;flex-direction: row;gap:20px;"     >
                    <div style="width: 50%;" >
                        <div class="xtfefoli_custom-linkedin-metabox-setting" style="margin-bottom: 10px;">
                            <label for="xtfefoli_custom-share-message" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_attr_e( 'Default Share Message', 'xt-feed-for-linkedin' ); ?></label>
                            <textarea  rows="7" name="xtfefoli_global_sharing_message" id="xtfefoli_custom-share-message" style="width: 100%; padding: 5px;"><?php echo esc_attr( $lfgsm_option ); ?></textarea>
                        </div>
                    </div>
                    <div style="width: 50%;">
                        <label for="xtfefoli_custom-share-message" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_attr_e( 'Default Share Message Keyword', 'xt-feed-for-linkedin' ); ?></label>
                        <div style="display: flex;flex-direction: row;flex-wrap: wrap;gap: 10px;" >
                            <span class="lf-post-type-item xtfefoli_sp_keyword" value="[POST_TITLE]" ><?php esc_attr_e( '[POST_TITLE]', 'xt-feed-for-linkedin' ); ?></span>
                            <span class="lf-post-type-item xtfefoli_sp_keyword" value="[POST_LINK]" ><?php esc_attr_e( '[POST_LINK]', 'xt-feed-for-linkedin' ); ?></span>
                            <span class="lf-post-type-item xtfefoli_sp_keyword" value="[POST_EXCERPT]" ><?php esc_attr_e( '[POST_EXCERPT]', 'xt-feed-for-linkedin' ); ?></span>
                            <span class="lf-post-type-item xtfefoli_sp_keyword" value="[POST_AUTHOR]" ><?php esc_attr_e( '[POST_AUTHOR]', 'xt-feed-for-linkedin' ); ?></span>
                            <span class="lf-post-type-item xtfefoli_sp_keyword" value="[WEBSITE_TITLE]" ><?php esc_attr_e( '[WEBSITE_TITLE]', 'xt-feed-for-linkedin' ); ?></span>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
        $post_types = get_post_types( [ 'public' => true], 'objects' );
        unset( $post_types['attachment'] );
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


