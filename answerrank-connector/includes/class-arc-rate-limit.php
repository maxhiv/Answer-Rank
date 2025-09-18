<?php
if (!defined('ABSPATH')) { exit; }

class ARC_Rate_Limit {

    const TRANSIENT_KEY = 'arc_rate_bucket';

    public static function init() { /* nothing needed */ }

    public static function allow() {
        $opts = AnswerRank_Connector::get_options();
        $win  = intval($opts['rate_limit']['window_sec']);
        $max  = intval($opts['rate_limit']['max_requests']);
        $now  = time();

        $bucket = get_transient(self::TRANSIENT_KEY);
        if (!is_array($bucket)) {
            $bucket = ['start'=>$now, 'count'=>0];
        }

        if (($now - $bucket['start']) > $win) {
            $bucket = ['start'=>$now, 'count'=>0];
        }

        if ($bucket['count'] >= $max) {
            return false;
        }

        $bucket['count']++;
        // ensure transient lives at least remaining window seconds
        $ttl = max(1, $win - ($now - $bucket['start']));
        set_transient(self::TRANSIENT_KEY, $bucket, $ttl);
        return true;
    }
}
