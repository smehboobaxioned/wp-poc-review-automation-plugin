<?php
/**
 * Plugin Name: Axioned Automated Reviews
 * Description: Fetches Yelp and Google reviews automatically and stores them in WP options.
 * Version: 1.0
 * Author: Axioned
 * Text Domain: axioned-automated-reviews
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin path
define('AXIONED_REVIEWS_PATH', plugin_dir_path(__FILE__));

// Include necessary files
require_once AXIONED_REVIEWS_PATH . 'includes/class-logger.php';
require_once AXIONED_REVIEWS_PATH . 'includes/helpers.php';
require_once AXIONED_REVIEWS_PATH . 'includes/notifications/class-notifications.php';
require_once AXIONED_REVIEWS_PATH . 'includes/class-cache-handler.php';
require_once AXIONED_REVIEWS_PATH . 'includes/yelp/yelp-scraper.php';

// Include admin files
require_once AXIONED_REVIEWS_PATH . 'includes/admin/class-admin.php';
require_once AXIONED_REVIEWS_PATH . 'includes/admin/css/admin-styles.php';
require_once AXIONED_REVIEWS_PATH . 'includes/admin/js/admin-scripts.php';

// Include tab files
require_once AXIONED_REVIEWS_PATH . 'includes/admin/tabs/tab-configuration.php';
require_once AXIONED_REVIEWS_PATH . 'includes/admin/tabs/tab-acf-mapping.php';
require_once AXIONED_REVIEWS_PATH . 'includes/admin/tabs/tab-cron-status.php';
require_once AXIONED_REVIEWS_PATH . 'includes/admin/tabs/tab-debug.php';
require_once AXIONED_REVIEWS_PATH . 'includes/admin/tabs/tab-logs.php';
require_once AXIONED_REVIEWS_PATH . 'includes/admin/tabs/tab-notifications.php';
require_once AXIONED_REVIEWS_PATH . 'includes/admin/tabs/tab-cache.php';

// Include API files
require_once AXIONED_REVIEWS_PATH . 'includes/yelp/yelp-fetch.php';
require_once AXIONED_REVIEWS_PATH . 'includes/google/google-fetch.php';

// Initialize logger
add_action('init', array('Axioned_Reviews_Logger', 'init'));

// Plugin activation: Just set default options, no cron scheduling
function axioned_activate_plugin() {
    // Clear any existing crons
    wp_clear_scheduled_hook('axioned_update_google_reviews');
    wp_clear_scheduled_hook('axioned_update_yelp_reviews');
    
    // Set default options if they don't exist
    if (get_option('axioned_reviews_frequency') === false) {
        update_option('axioned_reviews_frequency', 'daily');
    }
    
    if (get_option('axioned_reviews_time') === false) {
        update_option('axioned_reviews_time', '00:00');
    }
    
    // Ensure crons are disabled by default
    update_option('axioned_google_cron_enabled', '0');
    update_option('axioned_yelp_cron_enabled', '0');
    
    Axioned_Reviews_Logger::log("Plugin activated: All crons cleared and disabled by default");
}
register_activation_hook(__FILE__, 'axioned_activate_plugin');

// Plugin deactivation: Clear all crons
function axioned_cleanup_cron_jobs() {
    wp_clear_scheduled_hook('axioned_update_google_reviews');
    wp_clear_scheduled_hook('axioned_update_yelp_reviews');
    Axioned_Reviews_Logger::log("Plugin deactivated: Cleared all cron jobs");
}
register_deactivation_hook(__FILE__, 'axioned_cleanup_cron_jobs');

// Add these after the deactivation hook
function axioned_execute_google_cron() {
    if (get_option('axioned_google_cron_enabled', '0') === '1') {
        Axioned_Reviews_Logger::log("Executing scheduled Google reviews update");
        axioned_update_google_reviews();
        update_option('axioned_google_last_run', current_time('mysql'));
    }
}
add_action('axioned_update_google_reviews', 'axioned_execute_google_cron');

function axioned_execute_yelp_cron() {
    if (get_option('axioned_yelp_cron_enabled', '0') === '1') {
        Axioned_Reviews_Logger::log("Executing scheduled Yelp reviews update");
        axioned_update_yelp_reviews();
        update_option('axioned_yelp_last_run', current_time('mysql'));
    }
}
add_action('axioned_update_yelp_reviews', 'axioned_execute_yelp_cron');
