<?php
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}
global $xt_feed_for_linkedin;

$linkedin_users = get_option('xtfefoli_linkedin_user_data');
$has_accounts   = !empty($linkedin_users) && is_array($linkedin_users);

$ssl_active     = is_ssl();
$cron_active    = !defined('DISABLE_WP_CRON') || !DISABLE_WP_CRON;
$curl_active    = function_exists('curl_version');
$php_version    = phpversion();
$php_compatible = version_compare($php_version, '7.4.0', '>=');
?>
<div class="form-table" <?php if ( !$has_accounts ) { echo 'style="display: none;"'; } ?>>
    <?php
    $xt_feed_for_linkedin->common->xtfefoli_show_acuthorized_accounts();
    ?>
</div>
<div class="form-table" id="xtfefoli-connect-section">
    <div class="lf-card mt-2">
        <div class="header">
            <div class="text">
                <div class="header-icon"></div>
                <div class="header-title">
                    <span><?php esc_attr_e('Connect Accounts', 'xt-feed-for-linkedin'); ?></span>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="lf-inner-main-section mt-2">
                <?php
                $xt_feed_for_linkedin->xtfefoli_authorize->xtfefoli_linkedin_connect_button();
                ?>
                <div class="lf-inner-section-1"></div>
                <div class="lf-inner-section-2"></div>
            </div>
        </div>
    </div>
</div>