<?php
/**
 * Scripts
 *
 * @package     XT_Feed_Linkedin
 * @subpackage  Functions
 * @copyright   Copyright (c) 2025, Rajat Patel
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Load Admin Scripts
 *
 * Enqueues the required admin scripts.
 *
 * @since 1.0
 * @param string $hook Page hook.
 * @return void
 */
function xtfefoli_enqueue_admin_scripts( $hook ) {

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$page   = isset( $_GET['page'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) : '';
	$js_dir = XTFEFOLI_PLUGIN_URL . 'assets/js/';
    if ( 'xt_feed_for_linkedin' == $page ) {
        wp_register_script( 'xt-feed-for-linkedin-admin', $js_dir . 'xt-feed-for-linkedin-admin.js', array('jquery', 'jquery-ui-core'), XTFEFOLI_VERSION, true );
        wp_enqueue_script( 'xt-feed-for-linkedin-admin' );

        wp_localize_script( 'xt-feed-for-linkedin-admin', 'xtfefoli_ajax', array(
            'ajax_url'            => admin_url( 'admin-ajax.php' ),
            'plugin_page_url'     => esc_url( admin_url( 'admin.php?page=xt_feed_for_linkedin&tab=general' ) ),
            'delete_user_nonce'   => wp_create_nonce( 'xtfefoli_delete_user_nonce' ),
            'update_status_nonce' => wp_create_nonce( 'xtfefoli_update_status_nonce' )
        ));
    }

    wp_register_script( 'xt-feed-for-linkedin', $js_dir . 'xt-feed-for-linkedin.js', array('jquery', 'jquery-ui-core'), XTFEFOLI_VERSION, true );
    wp_enqueue_script( 'xt-feed-for-linkedin' );
}



/**
 * Load Admin Styles.
 *
 * Enqueues the required admin styles.
 *
 * @since 1.0
 * @param string $hook Page hook.
 * @return void
 */
function xtfefoli_enqueue_admin_styles( $hook ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$page    = isset( $_GET['page'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) : '';
	$css_dir = XTFEFOLI_PLUGIN_URL . 'assets/css/';
	if( 'xt_feed_for_linkedin' == $page ){
		wp_enqueue_style('xt-feed-for-linkedin-admin-css', $css_dir . 'xt-feed-for-linkedin-admin.css', false, XTFEFOLI_VERSION );
	}
	wp_enqueue_style('xt-feed-for-linkedin-css', $css_dir . 'xt-feed-for-linkedin.css', false, XTFEFOLI_VERSION );
}

add_action( 'admin_enqueue_scripts', 'xtfefoli_enqueue_admin_scripts' );
add_action( 'admin_enqueue_scripts', 'xtfefoli_enqueue_admin_styles' );