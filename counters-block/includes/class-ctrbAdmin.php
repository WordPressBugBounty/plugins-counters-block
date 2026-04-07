<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('CTRBAdmin')) {
    class CTRBAdmin
    {
        public function __construct()
        {

            add_action('init', [$this, 'ctrbRegisterPostType']);
            add_action('admin_enqueue_scripts', [$this, 'enqueueDashboardAssets']);
            add_action('admin_menu', [$this, 'ctrbAdminSubmenu']);
            add_filter('manage_counters-block_posts_columns', [$this, 'ctrb_setCustomColumn_edit']);
            add_action('manage_counters-block_posts_custom_column', [$this, 'ctrb_manageCustomColumn'], 10, 2);
        }

        public function  ctrbRegisterPostType()
        {
            $icon = "<svg xmlns='http://www.w3.org/2000/svg' className='bPlBlockIcon' viewBox='0 0 448 512' fill='currentColor' stroke='currentColor' stroke-width='0' color='#fff'>
    <path d='M176 0c-17.7 0-32 14.3-32 32s14.3 32 32 32h16V98.4C92.3 113.8 16 200 16 304c0 114.9 93.1 208 208 208s208-93.1 208-208c0-41.8-12.3-80.7-33.5-113.2l24.1-24.1c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L355.7 143c-28.1-23-62.2-38.8-99.7-44.6V64h16c17.7 0 32-14.3 32-32s-14.3-32-32-32H176zM288 204c28.7 0 52 23.3 52 52v96c0 28.7-23.3 52-52 52s-52-23.3-52-52V256c0-28.7 23.3-52 52-52zm-12 52v96c0 6.6 5.4 12 12 12s12-5.4 12-12V256c0-6.6-5.4-12-12-12s-12 5.4-12 12zM159.5 244c-5.4 0-10.2 3.5-11.9 8.6l-.6 1.7c-3.5 10.5-14.8 16.1-25.3 12.6s-16.1-14.8-12.6-25.3l.6-1.7c7.2-21.5 27.2-35.9 49.8-35.9c29 0 52.5 23.5 52.5 52.5v2.2c0 13.4-4.9 26.4-13.8 36.4l-39 43.9c-6.2 7-10 15.7-10.9 24.9H192c11 0 20 9 20 20s-9 20-20 20H128c-11 0-20-9-20-20V368.3c0-20.6 7.5-40.4 21.2-55.8l39-43.9c2.4-2.7 3.7-6.2 3.7-9.8v-2.2c0-6.9-5.6-12.5-12.5-12.5z' />
</svg>";


            register_post_type(
                'counters-block',
                [
                    'label'               => 'Counters Block',
                    'labels'              => [
                        'add_new' => 'Add New Shortcode',
                        'add_new_item' => 'Add New Shortcode',
                        'edit_item' => 'Edit Shortcode',
                        'not_found' => 'No Shortcode found',
                    ],
                    'supports' => ['title', 'editor', 'revisions'],
                    'show_in_rest' => true,
                    'public' => true,
                    'publicly_queryable' => false,
                    'menu_icon' => 'data:image/svg+xml;base64,' . base64_encode($icon),
                    'item_published' => 'Counters Block Published',
                    'item_updated' => 'Counters Block Updated',
                    'template' => [['ctrb/counters']],
                    'template_lock' => 'all',

                ]
            );
        }


        public function ctrbAdminSubmenu()
        {
            $parent_slug = 'tools.php';

            add_submenu_page(
                'edit.php?post_type=counters-block',
                'Demo and Help',
                'Demo & Help',
                'manage_options',
                'demo_page',
                [$this, 'ctrb_render_demo_page']
            );
        }

        // pass dashboard data through inline script

        public function enqueueDashboardAssets()
        {
            $data = [
                'version' => CTRB_VERSION,
                'isPremium' => ctrbIsPremium(),
                'hasPro' => CTRB_HAS_PRO,
            ];
            wp_add_inline_script(
                'ctrb-admin-dashboard',
                'const dashboardData = ' . wp_json_encode($data) . ';',
                'before'
            );
        }
        // render dashboard and  pass its data through data attributes

        public function ctrb_render_demo_page()
        {

?>
            <div id='ctrb-admin-dashboard'
                data-info='<?php echo esc_attr(wp_json_encode([
                                'version' => CTRB_VERSION,
                                'isPremium' => ctrbIsPremium(),
                                'hasPro' => CTRB_HAS_PRO,
                                'licenseActiveNonce' => wp_create_nonce('ctrbLicenseActivation'),

                            ])); ?>'></div>
<?php
        }
        // set custom columns for edit page

        public function ctrb_setCustomColumn_edit($column)
        {
            unset($column['date']);
            $column['shortcode'] = 'ShortCode';
            $column['date'] = 'Date';
            $column['publisher'] = 'Publisher';
            return $column;
        }

        public function ctrb_manageCustomColumn($column_name, $post_id)
        {

            if ($column_name == 'shortcode') {
                echo '<div class="bPlAdminShortcode" id="bPlAdminShortcode-' . esc_attr($post_id) . '">
						<input value="[counters-block id=' . esc_attr($post_id) . ']" onclick="copyBPlAdminShortcode(\'' . esc_attr($post_id) . '\')" readonly>
						<span class="tooltip">Copy To Clipboard</span>
					  </div>';
            }
            if ($column_name == 'publisher') {
                echo 'Counters Block';
            }
        }
    }

    new CTRBAdmin();
}
