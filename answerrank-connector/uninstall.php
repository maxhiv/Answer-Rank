<?php
// If uninstall not called from WordPress, exit.
if (!defined('WP_UNINSTALL_PLUGIN')) { exit; }

delete_option('arc_options');
delete_transient('arc_rate_bucket');
