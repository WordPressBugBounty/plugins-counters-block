<?php
/**
 * Plugin Name: Counters Block
 * Description: Display Number as animated counter( start to end ).
 * Version: 1.1.2
 * Author: bPlugins
 * Author URI: https://bplugins.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: counters-block
 */

// ABS PATH
if ( !defined( 'ABSPATH' ) ) { exit; }

// Constant
define( 'CTRB_VERSION', isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '1.1.2' );
define( 'CTRB_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'CTRB_DIR_PATH', plugin_dir_path( __FILE__ ) );

if( !class_exists( 'CTRBPlugin' ) ){
	class CTRBPlugin{
		function __construct(){
			add_action( 'enqueue_block_assets', [$this, 'enqueueBlockAssets'] );
			add_action( 'init', [ $this, 'onInit' ] );
		}
	
		function enqueueBlockAssets(){
			wp_register_style( 'fontAwesome', CTRB_DIR_URL . 'public/css/font-awesome.min.css', [], '6.4.2' );
		}

		function onInit(){
			register_block_type( __DIR__ . '/build' );
		}
	}
	new CTRBPlugin();
}