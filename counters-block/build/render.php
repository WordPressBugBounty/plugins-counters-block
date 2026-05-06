<?php
$id = wp_unique_id( 'ctrbCounters-' );

// Process dynamic data sources
$is_premium = function_exists( 'ctrbIsPremium' ) ? ctrbIsPremium() : false;

if ( $is_premium && isset( $attributes['counters'] ) && is_array( $attributes['counters'] ) ) {
	foreach ( $attributes['counters'] as $index => $counter ) {
		$number_settings = isset( $counter['number'] ) ? $counter['number'] : array();
		$data_source     = isset( $attributes['dataSource'] ) ? $attributes['dataSource'] : 'static';

		if ( 'wp_stats' === $data_source ) {
			$stat_type     = isset( $number_settings['statType'] ) ? $number_settings['statType'] : 'posts';
			$dynamic_value = 0;

				switch ( $stat_type ) {
					case 'posts':
						$dynamic_value = ctrbGetWpPostsCount();
						break;
					case 'pages':
						$dynamic_value = ctrbGetWpPagesCount();
						break;
					case 'comments':
						$dynamic_value = ctrbGetWpCommentsCount();
						break;
					case 'users':
						$dynamic_value = ctrbGetWpUsersCount();
						break;
				}

			$attributes['counters'][ $index ]['number']['end'] = (int) $dynamic_value;
		} elseif ( 'wc_stats' === $data_source ) {
			$stat_type     = isset( $number_settings['statType'] ) ? $number_settings['statType'] : 'sales';
			$dynamic_value = 0;

			if ( class_exists( 'WooCommerce' ) ) {
				switch ( $stat_type ) {
					case 'sales':
						$dynamic_value = ctrbGetWcSales();
						break;
					case 'orders':
						$dynamic_value = ctrbGetWcOrdersCount();
						break;
					case 'products':
						$dynamic_value = ctrbGetWcProductsCount();
						break;
					case 'customers':
						$dynamic_value = ctrbGetWcCustomersCount();
						break;
				}
			}

			$attributes['counters'][ $index ]['number']['end'] = (float) $dynamic_value;
		}
	}
}

extract( $attributes );

// if( isset( $icon['class'] ) && !empty( $icon['class'] ) ){
// 	wp_enqueue_style( 'font-awesome-7' );
// }
	// wp_enqueue_style( 'font-awesome-7' );
?>
<div
    <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() is properly escaped ?>
	<?php echo get_block_wrapper_attributes(); ?>
    id='<?php echo esc_attr( $id ); ?>'
    data-attributes='<?php echo esc_attr( wp_json_encode( $attributes ) ); ?>'
></div>