<?php

/**
 * Plugin Name: Counters Block
 * Description: Boost your credibility with animated number counters, WooCommerce sales, and real-time stats.
 * Version: 2.1.9
 * Author: bPlugins
 * Author URI: https://bplugins.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: counters-block
 */
// ABS PATH
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( function_exists( 'cb_fs' ) ) {
    cb_fs()->set_basename( false, __FILE__ );
} else {
    // Constant
    define( 'CTRB_VERSION', ( isset( $_SERVER['HTTP_HOST'] ) && ('localhost' === $_SERVER['HTTP_HOST'] || 'plugins.local' === $_SERVER['HTTP_HOST']) ? time() : '2.1.9' ) );
    define( 'CTRB_DIR_URL', plugin_dir_url( __FILE__ ) );
    define( 'CTRB_DIR_PATH', plugin_dir_path( __FILE__ ) );
    define( 'CTRB_HAS_PRO', file_exists( CTRB_DIR_PATH . 'vendor/freemius/start.php' ) );
    if ( !function_exists( 'cb_fs' ) ) {
        function cb_fs() {
            global $cb_fs;
            if ( !isset( $cb_fs ) ) {
                $fsLitePath = CTRB_DIR_PATH . 'vendor/freemius-lite/start.php';
                $fsPath = CTRB_DIR_PATH . 'vendor/freemius/start.php';
                if ( CTRB_HAS_PRO ) {
                    require_once $fsPath;
                } else {
                    require_once $fsLitePath;
                }
                $config = array(
                    'id'                  => '22599',
                    'slug'                => 'counters-block',
                    'premium_slug'        => 'counters-block-pro',
                    'type'                => 'plugin',
                    'public_key'          => 'pk_6cbd9ad1f21c0e19b48fc1eb61413',
                    'is_premium'          => false,
                    'premium_suffix'      => 'Pro',
                    'has_premium_version' => true,
                    'has_addons'          => false,
                    'has_paid_plans'      => true,
                  
                    'menu'                => array(
                        'slug'       => 'edit.php?post_type=counters-block',
                        'first-path' => 'edit.php?post_type=counters-block&page=demo_page#/welcome',
                        'support'    => false,
                    ),
                );
                $cb_fs = ( CTRB_HAS_PRO && file_exists( $fsPath ) ? fs_dynamic_init( $config ) : fs_lite_dynamic_init( $config ) );
            }
            return $cb_fs;
        }

        // Init Freemius.
        cb_fs();
        // Signal that SDK was initiated.
        do_action( 'cb_fs_loaded' );
    }
    // ... Your plugin's main file logic ...
    require_once CTRB_DIR_PATH . "includes/class-ctrbPlugin.php";
    new CTRBPlugin();
    require_once plugin_dir_path( __FILE__ ) . 'includes/bplugins-bfcm2026-promo.php';
    add_action( 'admin_notices', function () {
        if ( function_exists( 'get_current_screen' ) ) {
            $screen = get_current_screen();
            // Write to the debug log (wp-content/debug.log) – you can view it via the Debug Log plugin or in the server file.
            error_log( '🛠️ Current admin screen ID: ' . $screen->id );
        }
    } );
    bplugins_bfcm2026_init( array(
        'plugin_file'          => __FILE__,
        'pricing_url'          => 'https://bplugins.com/products/counters-block/pricing/',
        'dashboard_screen_ids' => array('counters-block_page_demo_page', 'edit-counters-block'),
        'product_name'         => 'Counters Block Pro',
        'logo_url'             => 'https://ps.w.org/counters-block/assets/icon.svg?rev=3623366',
        'title'                => 'Biggest Black Friday Sale is Live!',
        'description'          => 'Make your achievements stand out! Upgrade to Counters Block Pro and unlock premium counter themes, advanced animations, WooCommerce integration, and powerful customization!',
        'badge_text'           => 'Flat 45% OFF',
        'features'             => array(
            '15+ Counter Themes',
            'Circular Progress Counters',
            'Advanced Animations',
            'WooCommerce Integration',
            'Multiple Trigger Options',
            'Advanced Styling Controls'
        ),
        'button_text'          => 'Upgrade to Pro Now',
        'action_text'          => 'Upgrade to Pro and get 45% off on this Black Friday',
        'primary_color'        => '#35A0A8',
        'secondary_color'      => '#101b55',
        'accent_color'         => '#ff6b35',
        'text_color'           => '#172033',
        'campaign_end'         => '2026-12-05 23:59:59',
    ) );
    if ( CTRB_HAS_PRO ) {
        require_once CTRB_DIR_PATH . 'includes/LicenseActivation.php';
    }
}