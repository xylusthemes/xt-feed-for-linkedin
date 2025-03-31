<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $xt_feed_for_linkedin;
?>
<div class="form-table">
    <div class="lf-card mt-2" >
        <div class="header" >
            <div class="text" >
                <div class="header-icon" ></div>
                <div class="header-title" >
                    <span><?php esc_attr_e( 'Connect Accounts', 'xt-feed-for-linkedin' ); ?></span>
                </div>
            </div>
        </div>
        <div class="content" >
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

<?php
$xt_feed_for_linkedin->common->xtfefoli_show_acuthorized_accounts();