<?php

function ctrbIsPremium()
{
    return CTRB_HAS_PRO ? cb_fs()->can_use_premium_code() : false;
}

/**
 * Check if WooCommerce HPOS (High-Performance Order Storage) is enabled.
 *
 * @return bool
 */
function ctrbIsHposEnabled()
{
	if ( ! class_exists( 'WooCommerce' ) ) {
		return false;
	}

	if ( class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) &&
	     method_exists( '\Automattic\WooCommerce\Utilities\OrderUtil', 'custom_orders_table_usage_is_enabled' ) ) {
		return \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
	}

	return false;
}

/**
 * Get total WooCommerce sales.
 *
 * @return float
 */
function ctrbGetWcSales()
{
	global $wpdb;
	$value = 0;

	if ( ctrbIsHposEnabled() ) {
		$table_name = $wpdb->prefix . 'wc_orders';
		$value      = $wpdb->get_var( "SELECT SUM(total_amount) FROM $table_name WHERE status IN ('wc-completed', 'wc-processing') AND type = 'shop_order'" );
	} else {
		$value = $wpdb->get_var( "SELECT SUM(pm.meta_value) FROM $wpdb->postmeta pm JOIN $wpdb->posts p ON pm.post_id = p.ID WHERE pm.meta_key = '_order_total' AND p.post_type = 'shop_order' AND p.post_status IN ('wc-completed', 'wc-processing')" );
	}

	return max( 0, (float) $value );
}


/**
 * Get WooCommerce orders count.
 *
 * @return int
 */
function ctrbGetWcOrdersCount()
{
	if ( ! class_exists( 'WooCommerce' ) ) {
		return 0;
	}

	return wc_orders_count( 'completed' ) + wc_orders_count( 'processing' );
}

/**
 * Get WooCommerce products count.
 *
 * @return int
 */
function ctrbGetWcProductsCount()
{
	$counts = wp_count_posts( 'product' );
	return isset( $counts->publish ) ? (int) $counts->publish : 0;
}

/**
 * Get WooCommerce customers count.
 *
 * @return int
 */
function ctrbGetWcCustomersCount()
{
	global $wpdb;

	if ( ! class_exists( 'WooCommerce' ) ) {
		return 0;
	}

	// Try to get count from WooCommerce customer lookup table (includes guests)
	$table_name = $wpdb->prefix . 'wc_customer_lookup';
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) === $table_name ) {
		$count = $wpdb->get_var( "SELECT COUNT(customer_id) FROM $table_name" );
		if ( null !== $count ) {
			return (int) $count;
		}
	}

	// Fallback to only registered customers if lookup table is not available
	$query = new WP_User_Query( array(
		'role'   => 'customer',
		'fields' => 'ID',
		'number' => 1
	) );

	return $query->get_total();
}

/**
 * Get WordPress posts count.
 *
 * @return int
 */
function ctrbGetWpPostsCount()
{
	$counts = wp_count_posts();
	return isset( $counts->publish ) ? (int) $counts->publish : 0;
}

/**
 * Get WordPress pages count.
 *
 * @return int
 */
function ctrbGetWpPagesCount()
{
	$counts = wp_count_posts( 'page' );
	return isset( $counts->publish ) ? (int) $counts->publish : 0;
}

/**
 * Get WordPress comments count.
 *
 * @return int
 */
function ctrbGetWpCommentsCount()
{
	$counts = wp_count_comments();
	return isset( $counts->approved ) ? (int) $counts->approved : 0;
}

/**
 * Get WordPress users count.
 *
 * @return int
 */
function ctrbGetWpUsersCount()
{
	$user_counts = count_users();
	return isset( $user_counts['total_users'] ) ? (int) $user_counts['total_users'] : 0;
}
