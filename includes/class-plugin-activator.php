<?php
/**
 * Plugin Activator
 *
 * This file contains the code for the plugin activator.
 * It creates necessary options with default values and triggers initial fetches.
 */
class Axioned_Reviews_Activator {
    public static function activate() {
        // Create necessary options with default values
        add_option('axioned_google_api_key', '');
        add_option('axioned_google_place_id', '');
        add_option('axioned_yelp_api_key', '');
        add_option('axioned_yelp_business_id', '');
        
        // Trigger initial fetch
        do_action('axioned_update_google_reviews');
        do_action('axioned_update_yelp_reviews');
    }
    
    public static function deactivate() {
        // Clean up options if needed
        // delete_option('axioned_google_api_key');
        // delete_option('axioned_google_place_id');
        // delete_option('axioned_yelp_api_key');
        // delete_option('axioned_yelp_business_id');
    }
} 