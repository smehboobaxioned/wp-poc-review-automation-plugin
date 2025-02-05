<?php
/**
 * Cache Handler
 *
 * Handles cache clearing for various providers
 */

class Axioned_Reviews_Cache_Handler {
    
    /**
     * Clear specific or all configured caches
     * 
     * @param string $provider Optional. Specific provider to clear ('wpengine', 'cloudflare', or 'all')
     * @return array Array of cleared providers and errors
     */
    public static function clear_all_caches($provider = 'all') {
        Axioned_Reviews_Logger::log("Starting cache clearing process for provider: {$provider}");
        
        $cleared = array();
        $errors = array();

        // WP Engine Cache
        if (($provider === 'all' || $provider === 'wpengine') && 
            get_option('axioned_clear_wpengine_cache') === '1') {
            Axioned_Reviews_Logger::log("Attempting to clear WP Engine cache...");
            try {
                self::clear_wpengine_cache();
                $cleared[] = 'WP Engine';
                Axioned_Reviews_Logger::log("Successfully cleared WP Engine cache", 'success');
            } catch (Exception $e) {
                $errors[] = 'WP Engine: ' . $e->getMessage();
                Axioned_Reviews_Logger::log("Failed to clear WP Engine cache: " . $e->getMessage(), 'error');
            }
        } else if ($provider === 'wpengine') {
            Axioned_Reviews_Logger::log("WP Engine cache clearing is disabled", 'info');
        }

        // Cloudflare Cache
        if (($provider === 'all' || $provider === 'cloudflare') && 
            get_option('axioned_clear_cloudflare_cache') === '1') {
            Axioned_Reviews_Logger::log("Attempting to clear Cloudflare cache...");
            try {
                self::clear_cloudflare_cache();
                $cleared[] = 'Cloudflare';
                Axioned_Reviews_Logger::log("Successfully cleared Cloudflare cache", 'success');
            } catch (Exception $e) {
                $errors[] = 'Cloudflare: ' . $e->getMessage();
                Axioned_Reviews_Logger::log("Failed to clear Cloudflare cache: " . $e->getMessage(), 'error');
            }
        } else if ($provider === 'cloudflare') {
            Axioned_Reviews_Logger::log("Cloudflare cache clearing is disabled", 'info');
        }

        // Log summary
        if (!empty($cleared)) {
            Axioned_Reviews_Logger::log("Successfully cleared cache for: " . implode(', ', $cleared), 'success');
        }
        if (!empty($errors)) {
            Axioned_Reviews_Logger::log("Cache clearing errors: " . implode('; ', $errors), 'error');
        }
        if (empty($cleared) && empty($errors)) {
            Axioned_Reviews_Logger::log("No cache providers were configured to clear", 'info');
        }

        return array(
            'cleared' => $cleared,
            'errors' => $errors
        );
    }

    /**
     * Clear WP Engine cache
     */
    private static function clear_wpengine_cache() {
        // Check if WpeCommon class exists and has purge_varnish_cache method
        if (!class_exists('WpeCommon') || !method_exists( 'WpeCommon', 'purge_varnish_cache' )) {
            Axioned_Reviews_Logger::log("WP Engine functions not available - this might not be a WP Engine site", 'error');
            throw new Exception('WP Engine functions not available');
        }

        try {
            Axioned_Reviews_Logger::log("Clearing WP Engine memcached...");
            WpeCommon::purge_memcached();
            Axioned_Reviews_Logger::log("Successfully cleared WP Engine memcached", 'success');

            Axioned_Reviews_Logger::log("Clearing WP Engine varnish cache...");
            WpeCommon::purge_varnish_cache();
            Axioned_Reviews_Logger::log("Successfully cleared WP Engine varnish cache", 'success');

            Axioned_Reviews_Logger::log("All WP Engine caches cleared successfully", 'success');
        } catch (Exception $e) {
            $error_msg = "Failed to clear WP Engine cache: " . $e->getMessage();
            Axioned_Reviews_Logger::log($error_msg, 'error');
            throw new Exception($error_msg);
        }
    }

    /**
     * Clear Cloudflare cache
     */
    private static function clear_cloudflare_cache() {
        // First try using the Cloudflare plugin if available
        if (class_exists('\CF\WordPress\Hooks')) {
            try {
                Axioned_Reviews_Logger::log("Cloudflare plugin detected, attempting to clear cache via plugin...");
                // Purge Everything using Cloudflare plugin function
                $cf_hooks = new CF\WordPress\Hooks();
                $cf_hooks->purgeCacheEverything();
                Axioned_Reviews_Logger::log("Successfully cleared Cloudflare cache via plugin", 'success');
                return;
            } catch (Exception $e) {
                $warning_msg = "Failed to clear Cloudflare cache via plugin: " . $e->getMessage();
                Axioned_Reviews_Logger::log($warning_msg, 'warning');
                Axioned_Reviews_Logger::log("Falling back to direct API method...", 'info');
            }
        } else {
            Axioned_Reviews_Logger::log("Cloudflare plugin not detected, using direct API method...", 'info');
        }

        // Fallback to direct API if plugin not available or failed
        $api_token = get_option('axioned_cloudflare_api_token');
        $zone_id = get_option('axioned_cloudflare_zone_id');

        if (!$api_token || !$zone_id) {
            $error_msg = 'Cloudflare API token or Zone ID not configured. Please configure the Cloudflare plugin or provide API credentials.';
            Axioned_Reviews_Logger::log($error_msg, 'error');
            throw new Exception($error_msg);
        }

        Axioned_Reviews_Logger::log("Making Cloudflare API request to clear cache...");
        
        $response = wp_remote_post(
            "https://api.cloudflare.com/client/v4/zones/{$zone_id}/purge_cache",
            array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $api_token,
                    'Content-Type' => 'application/json',
                ),
                'body' => json_encode(array('purge_everything' => true))
            )
        );

        if (is_wp_error($response)) {
            $error_msg = "Cloudflare API error: " . $response->get_error_message();
            Axioned_Reviews_Logger::log($error_msg, 'error');
            throw new Exception($error_msg);
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        $response_code = wp_remote_retrieve_response_code($response);
        
        Axioned_Reviews_Logger::log("Cloudflare API Response Code: " . $response_code);
        Axioned_Reviews_Logger::log("Cloudflare API Response: " . print_r($body, true));
        
        if (!$body['success']) {
            $error = isset($body['errors'][0]['message']) ? $body['errors'][0]['message'] : 'Unknown error';
            $error_msg = "Cloudflare cache clear failed: " . $error;
            Axioned_Reviews_Logger::log($error_msg, 'error');
            throw new Exception($error_msg);
        }

        Axioned_Reviews_Logger::log("Successfully cleared Cloudflare cache via API", 'success');
    }
} 