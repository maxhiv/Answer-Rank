<?php
if (!defined('ABSPATH')) { exit; }

class ARC_Admin {

    public static function init() {
        add_action('admin_menu', [__CLASS__, 'menu']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'assets']);
    }

    public static function menu() {
        add_options_page(
            __('AnswerRank', 'answerrank-connector'),
            __('AnswerRank', 'answerrank-connector'),
            AnswerRank_Connector::CAPABILITY,
            AR_CONN_SLUG,
            [__CLASS__, 'render_settings']
        );
    }

    public static function register_settings() {
        register_setting(
            'arc_settings_group',
            AnswerRank_Connector::OPTS_KEY,
            [
                'type' => 'array',
                'sanitize_callback' => [__CLASS__, 'sanitize'],
                'default' => AnswerRank_Connector::get_options(),
            ]
        );
    }

    public static function sanitize($input) {
        $out = AnswerRank_Connector::get_options();
        if (isset($input['backend_base_url'])) {
            $out['backend_base_url'] = esc_url_raw($input['backend_base_url']);
        }
        if (isset($input['site_api_key'])) {
            $out['site_api_key'] = sanitize_text_field($input['site_api_key']);
        }
        if (isset($input['webhook_secret'])) {
            $out['webhook_secret'] = sanitize_text_field($input['webhook_secret']);
        }
        if (isset($input['connection_status'])) {
            $allowed = ['connected','disconnected','error'];
            $status = sanitize_text_field($input['connection_status']);
            $out['connection_status'] = in_array($status, $allowed, true) ? $status : 'disconnected';
        }
        if (isset($input['rate_limit']) && is_array($input['rate_limit'])) {
            $win = isset($input['rate_limit']['window_sec']) ? intval($input['rate_limit']['window_sec']) : 60;
            $max = isset($input['rate_limit']['max_requests']) ? intval($input['rate_limit']['max_requests']) : 30;
            $out['rate_limit'] = ['window_sec' => max(10, $win), 'max_requests' => max(1, $max)];
        }
        return $out;
    }

    public static function assets($hook) {
        if ($hook !== 'settings_page_' . AR_CONN_SLUG) return;
        wp_enqueue_style('arc-admin', AR_CONN_URL . 'assets/css/admin.css', [], AR_CONN_VERSION);
        wp_enqueue_script('arc-admin', AR_CONN_URL . 'assets/js/admin.js', ['jquery'], AR_CONN_VERSION, true);
        wp_localize_script('arc-admin', 'ARC_ADMIN', [
            'nonce' => wp_create_nonce('wp_rest'),
            'rest_url' => esc_url_raw( rest_url('answerrank/v1') ),
        ]);
    }

    public static function render_settings() {
        $opts = AnswerRank_Connector::get_options();
        include AR_CONN_DIR . 'admin/views/settings-page.php';
    }
}
