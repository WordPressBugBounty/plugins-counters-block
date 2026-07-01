<?php

if (!class_exists('CTRBPlugin')) {
	class CTRBPlugin
	{
		public function __construct()
		{
			add_action('plugins_loaded', [$this, 'load_dependencies']);
			add_action('admin_enqueue_scripts', [$this, 'ctrbAdminScripts']);
			add_shortcode('counters-block', [$this, 'ctrbShortcode']);
			add_action('rest_api_init', [$this, 'register_rest_endpoints']);
		}

		public function register_rest_endpoints()
		{
			register_rest_route('counters-block/v1', '/stats', [
				'methods'             => 'GET',
				'callback'            => [$this, 'get_stats_callback'],
				'permission_callback' => function () {
					return current_user_can('edit_posts');
				}
			]);
		}

		public function get_stats_callback($request)
		{
			$type  = $request->get_param('type');
			$stat  = $request->get_param('stat');
			$value = 0;

			if ($type === 'wp_stats') {
				switch ($stat) {
					case 'posts':
						$value = ctrbGetWpPostsCount();
						break;
					case 'pages':
						$value = ctrbGetWpPagesCount();
						break;
					case 'comments':
						$value = ctrbGetWpCommentsCount();
						break;
					case 'users':
						$value = ctrbGetWpUsersCount();
						break;
				}
			} elseif ($type === 'wc_stats' && class_exists('WooCommerce')) {
				switch ($stat) {
					case 'sales':
						$value = ctrbGetWcSales();
						break;
					case 'orders':
						$value = ctrbGetWcOrdersCount();
						break;
					case 'products':
						$value = ctrbGetWcProductsCount();
						break;
					case 'customers':
						$value = ctrbGetWcCustomersCount();
						break;
				}
			}

			return new WP_REST_Response(['value' => (float) $value], 200);
		}

		public function load_dependencies()
		{
			require_once CTRB_DIR_PATH . 'includes/functions.php';
			require_once CTRB_DIR_PATH . 'counters-block.php';
			require_once CTRB_DIR_PATH . 'includes/class-ctrbAdmin.php';
		}

		public function ctrbAdminScripts($screen): void
		{
			global $typenow;


			if ('counters-block' == $typenow) {
				$assets = include CTRB_DIR_PATH . 'build/admin-dashboard.asset.php';
				wp_enqueue_script('admin-post-js', CTRB_DIR_URL . 'build/admin-post.js', [], CTRB_VERSION, true);
				wp_enqueue_style('admin-post-css', CTRB_DIR_URL . 'build/admin-post.css', [], CTRB_VERSION);
				wp_enqueue_script('ctrb-admin-dashboard-js', CTRB_DIR_URL . 'build/admin-dashboard.js', array_merge($assets['dependencies'], ['wp-util']), CTRB_VERSION, true);
				wp_enqueue_style('ctrb-admin-dashboard-css', CTRB_DIR_URL . 'build/admin-dashboard.css', [], CTRB_VERSION);
			}
		}


		function ctrbShortcode($atts)
		{
			$post_id = $atts['id'];
			$post = get_post($post_id);

			if (!$post) {
				return '';
			}

			if (post_password_required($post)) {
				return get_the_password_form($post);
			}

			switch ($post->post_status) {
				case 'publish':
					return $this->ctrbDisplayContent($post);

				case 'private':
					if (current_user_can('read_private_posts')) {
						return $this->ctrbDisplayContent($post);
					}
					return '';

				case 'draft':
				case 'pending':
				case 'future':
					if (current_user_can('edit_post', $post_id)) {
						return $this->ctrbDisplayContent($post);
					}
					return '';

				default:
					return '';
			}
		}

		function ctrbDisplayContent($post)
		{
			$content = $post->post_content;
			if (function_exists('do_blocks')) {
				$content = do_blocks($content);
			}
			if (function_exists('wp_filter_content_tags')) {
				$content = wp_filter_content_tags($content);
			}
			$content = do_shortcode($content);
			return $content;
		}
	}
}
