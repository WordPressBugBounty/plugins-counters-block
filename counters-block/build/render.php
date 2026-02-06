<?php
$id = wp_unique_id( 'ctrbCounters-' );

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