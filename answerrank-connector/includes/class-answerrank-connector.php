<?php
if (!defined('ABSPATH')) { exit; }

require_once AR_CONN_DIR . 'includes/class-arc-admin.php';
require_once AR_CONN_DIR . 'includes/class-arc-rest.php';
require_once AR_CONN_DIR . 'includes/class-arc-auth.php';
require_once AR_CONN_DIR . 'includes/class-arc-rate-limit.php';

class AnswerRank_Connector {

    const OPTS_KEY = 'arc_options';
    const NONCE_ACTION = 'arc_settings_action';
    const CAPABILITY = 'manage_options';

    public static function init() {
        ARC_Admin::init();
        ARC_Rest::init();
        ARC_Auth::init();
        ARC_Rate_Limit::init();
    }

    public static function activate() {
        $defaults = [
            'backend_base_url' => 'https://your-replit-backend.example/api',
            'site_api_key'     => '',
            'webhook_secret'   => '',
            'connection_status'=> 'disconnected',
            'rate_limit'       => ['window_sec' => 60, 'max_requests' => 30],
        ];
        add_option(self::OPTS_KEY, $defaults, '', false);
    }

    public static function deactivate() {
        // keep options by default; uncomment to remove on deactivate:
        // delete_option(self::OPTS_KEY);
    }

    public static function get_options() {
        $opts = get_option(self::OPTS_KEY, []);
        if (!is_array($opts)) $opts = [];
        $opts = wp_parse_args($opts, [
            'backend_base_url' => '',
            'site_api_key' => '',
            'webhook_secret' => '',
            'connection_status'=> 'disconnected',
            'rate_limit' => ['window_sec' => 60, 'max_requests' => 30],
        ]);
        return $opts;
    }

    public static function update_options($new) {
        if (!is_array($new)) return;
        $opts = self::get_options();
        $opts = array_merge($opts, $new);
        update_option(self::OPTS_KEY, $opts, false);
    }
}
