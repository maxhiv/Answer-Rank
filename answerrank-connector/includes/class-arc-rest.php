<?php
if (!defined('ABSPATH')) { exit; }

class ARC_Rest {

    public static function init() {
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
    }

    public static function register_routes() {
        register_rest_route('answerrank/v1', '/ping', [
            'methods'  => 'GET',
            'callback' => [__CLASS__, 'ping'],
            'permission_callback' => function(){ return current_user_can('manage_options'); },
        ]);

        register_rest_route('answerrank/v1', '/proxy', [
            'methods'  => 'POST',
            'callback' => [__CLASS__, 'proxy'],
            'permission_callback' => function(){ return current_user_can('manage_options'); },
            'args' => [
                'path' => [
                    'required' => true,
                    'type' => 'string',
                ],
                'payload' => [
                    'required' => false,
                ]
            ]
        ]);
    }

    private static function opts() { return AnswerRank_Connector::get_options(); }

    public static function ping($req) {
        check_ajax_referer('wp_rest', false, true);
        $opts = self::opts();
        $base = isset($opts['backend_base_url']) ? rtrim($opts['backend_base_url'], '/') : '';
        $apiKey = isset($opts['site_api_key']) ? $opts['site_api_key'] : '';

        if (!$base) {
            return new WP_Error('arc_no_base', 'Backend base URL not set.', ['status'=>400]);
        }
        // call backend health endpoint if available
        $url = $base . '/health';
        $args = [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Connector-Key' => $apiKey,
                'User-Agent' => 'AnswerRank-Connector/' . AR_CONN_VERSION . '; ' . get_bloginfo('url')
            ],
            'timeout' => 10,
        ];
        $resp = wp_remote_get($url, $args);
        if (is_wp_error($resp)) {
            return new WP_Error('arc_backend_error', $resp->get_error_message(), ['status'=>500]);
        }
        $code = wp_remote_retrieve_response_code($resp);
        $body = wp_remote_retrieve_body($resp);
        $json = json_decode($body, true);
        if ($code >= 200 && $code < 300) {
            return [
                'ok' => true,
                'backend' => isset($json['status']) ? $json['status'] : 'ok',
                'time' => time()
            ];
        }
        return new WP_Error('arc_backend_bad', 'Backend not OK', ['status'=>$code ?: 502]);
    }

    public static function proxy($req) {
        check_ajax_referer('wp_rest', false, true);
        if (!ARC_Rate_Limit::allow()) {
            return new WP_Error('arc_rate_limited', 'Too many requests. Please try again shortly.', ['status'=>429]);
        }

        $path = trim($req->get_param('path'));
        $payload = $req->get_param('payload');
        $opts = self::opts();
        $base = isset($opts['backend_base_url']) ? rtrim($opts['backend_base_url'], '/') : '';
        $apiKey = isset($opts['site_api_key']) ? $opts['site_api_key'] : '';

        if (!$base) return new WP_Error('arc_no_base', 'Backend base URL not set.', ['status'=>400]);
        if (strpos($path, '..') !== false) return new WP_Error('arc_bad_path', 'Invalid path', ['status'=>400]);

        // allowlist backend paths (tighten as needed)
        $allow = ['/install', '/exchange', '/sites/me', '/faq/sync', '/schema/push', '/connect/status'];
        if (!in_array($path, $allow, true)) {
            return new WP_Error('arc_forbidden', 'Path not allowed', ['status'=>403]);
        }

        $url = $base . $path;
        $args = [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Connector-Key' => $apiKey,
                'X-WP-Site' => home_url(),
                'User-Agent' => 'AnswerRank-Connector/' . AR_CONN_VERSION
            ],
            'timeout' => 20,
            'body' => is_null($payload) ? null : wp_json_encode($payload),
            'method' => 'POST'
        ];
        $resp = wp_remote_request($url, $args);
        if (is_wp_error($resp)) {
            return new WP_Error('arc_backend_error', $resp->get_error_message(), ['status'=>500]);
        }
        $code = wp_remote_retrieve_response_code($resp);
        $body = wp_remote_retrieve_body($resp);
        $json = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_REST_Response(['raw' => $body], $code ?: 200);
        }
        return new WP_REST_Response($json, $code ?: 200);
    }
}
