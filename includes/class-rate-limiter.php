<?php
/**
 * Rate Limiter Class
 *
 * This file contains the code for the rate limiter class.
 * It allows users to limit the number of requests to the API.
 */

class Axioned_Reviews_Rate_Limiter {
    public static function can_fetch($service) {
        $last_fetch = get_option("axioned_{$service}_last_fetch");
        $min_interval = 6 * HOUR_IN_SECONDS; // 6 hours
        
        if (!$last_fetch || (time() - $last_fetch) >= $min_interval) {
            update_option("axioned_{$service}_last_fetch", time());
            return true;
        }
        return false;
    }
}