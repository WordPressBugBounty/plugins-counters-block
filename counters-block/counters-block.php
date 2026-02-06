<?php


if (!class_exists('CTRBPBlock')) {
    class CTRBPBlock
    {
        function __construct()
        {
            add_action('enqueue_block_assets', [$this, 'enqueueBlockAssets']);
            add_action('init', [$this, 'onInit']);
        }

        function enqueueBlockAssets()
        {
            wp_register_style('font-awesome-7', CTRB_DIR_URL . 'public/css/font-awesome.min.css', [], '7.1.0');
            wp_enqueue_style('font-awesome-7');

            $handle = 'ctrb-inline-premium';
            wp_register_script($handle, false, [], CTRB_VERSION, true);
            $data = [
                'isPremium' => function_exists('ctrbIsPremium') ? ctrbIsPremium() : false,
                'hasPro' => defined('CTRB_HAS_PRO') ? CTRB_HAS_PRO : false,
                'version' => defined('CTRB_VERSION') ? CTRB_VERSION : '',
            ];
            $inline = 'window.ctrbPrimiumProps = Object.assign(window.ctrbPrimiumProps || {}, ' . wp_json_encode($data) . ');';
            wp_add_inline_script($handle, $inline, 'before');
            wp_enqueue_script($handle);
        }

        function onInit()
        {
            register_block_type(__DIR__ . '/build');
        }
    }
    new CTRBPBlock();
}
