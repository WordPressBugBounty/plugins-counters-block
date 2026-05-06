<?php

/**
 * Plugin Name: Counters Block
 * Description: Boost your credibility with animated number counters, WooCommerce sales, and real-time stats.
 * Version: 2.0.7
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
    define( 'CTRB_VERSION', ( isset( $_SERVER['HTTP_HOST'] ) && ('localhost' === $_SERVER['HTTP_HOST'] || 'plugins.local' === $_SERVER['HTTP_HOST']) ? time() : '2.0.7' ) );
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
    if ( CTRB_HAS_PRO ) {
        require_once CTRB_DIR_PATH . 'includes/LicenseActivation.php';
    }
}