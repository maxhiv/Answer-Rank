<?php
/**
 * Plugin Name: AnswerRank Connector
 * Plugin URI: https://251seo.com/answerrank
 * Description: Lightweight UI & auth connector that links your WordPress site to the AnswerRank backend (Replit). Stores keys securely, exposes a minimal REST proxy with nonce/capability checks, and adds an admin settings page.
 * Version: 1.0.0
 * Author: 251SEO
 * Author URI: https://251seo.com
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * Text Domain: answerrank-connector
 */

if (!defined('ABSPATH')) { exit; }

define('AR_CONN_VERSION', '1.0.0');
define('AR_CONN_SLUG', 'answerrank-connector');
define('AR_CONN_DIR', plugin_dir_path(__FILE__));
define('AR_CONN_URL', plugin_dir_url(__FILE__));

require_once AR_CONN_DIR . 'includes/class-answerrank-connector.php';

register_activation_hook(__FILE__, ['AnswerRank_Connector', 'activate']);
register_deactivation_hook(__FILE__, ['AnswerRank_Connector', 'deactivate']);

add_action('plugins_loaded', function() {
    AnswerRank_Connector::init();
});
