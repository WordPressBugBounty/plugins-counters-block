<?php

if (!class_exists('CTRBPlugin')) {
	class CTRBPlugin
	{
		public function __construct()
		{
			add_action('plugins_loaded', [$this, 'load_dependencies']);
			add_action('admin_enqueue_scripts', [$this, 'ctrbAdminScripts']);
			add_shortcode('counters-block', [$this, 'ctrbShortcode']);
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
				wp_enqueue_script('admin-post-js', CTRB_DIR_URL . 'build/admin-post.js', [], CTRB_VERSION, true);
				wp_enqueue_style('admin-post-css', CTRB_DIR_URL . 'build/admin-post.css', [], CTRB_VERSION);
				wp_enqueue_script('ctrb-admin-dashboard-js', CTRB_DIR_URL . 'build/admin-dashboard.js', ['react', 'react-dom'], CTRB_VERSION, true);
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
			$content = apply_filters('the_content', $post->post_content);
			return $content;
		}
	}
}
