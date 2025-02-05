<?php
/**
 * Google Fetch
 *
 * This file contains the code for fetching Google reviews.
 */

function axioned_fetch_google_reviews($trigger = 'cron') {
    Axioned_Reviews_Logger::log("Starting Google Reviews Fetch Process via " . strtoupper($trigger));

    // Get settings
    $api_key = get_option('axioned_google_api_key');
    $place_id = get_option('axioned_google_place_id');
    
    if (!$api_key || !$place_id) {
        $error_message = 'Google API configuration missing. API Key or Place ID not set.';
        Axioned_Reviews_Logger::log($error_message, 'error');
        Axioned_Reviews_Notifications::send_review_update_email(
            'google', 
            [], 
            false, 
            $trigger, 
            $error_message
        );
        Axioned_Reviews_Notifications::send_slack_notification('google', [], false, $trigger, $error_message);
        return false;
    }
    
    Axioned_Reviews_Logger::log("Fetching Google reviews for Place ID: $place_id");
    
    $url = "https://places.googleapis.com/v1/places/{$place_id}?fields=rating,userRatingCount&key={$api_key}";

    // Initialize cURL
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
        ),
    ));

    // Execute cURL request
    $response = curl_exec($curl);
    
    // Check for cURL errors
    if (curl_errno($curl)) {
        $error_message = "cURL Error: " . curl_error($curl);
        Axioned_Reviews_Logger::log($error_message, 'error');
        Axioned_Reviews_Notifications::send_review_update_email(
            'google', 
            [], 
            false, 
            $trigger, 
            $error_message
        );
        Axioned_Reviews_Notifications::send_slack_notification('google', [], false, $trigger, $error_message);
        curl_close($curl);
        return false;
    }
    
    // Get HTTP response code
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    Axioned_Reviews_Logger::log("API Response Code: $http_code");
    
    if ($http_code !== 200) {
        $error_message = "Google API returned error HTTP code: $http_code\nResponse: $response";
        Axioned_Reviews_Logger::log($error_message, 'error');
        Axioned_Reviews_Notifications::send_review_update_email(
            'google', 
            [], 
            false, 
            $trigger, 
            $error_message
        );
        Axioned_Reviews_Notifications::send_slack_notification('google', [], false, $trigger, $error_message);
        curl_close($curl);
        return false;
    }

    curl_close($curl);

    // Log raw response for debugging
    Axioned_Reviews_Logger::log("Raw API Response: $response");

    // Decode API response
    $data = json_decode($response, true);
    
    // Check JSON decode errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        Axioned_Reviews_Logger::log("JSON Decode Error: " . json_last_error_msg(), 'error');
        return false;
    }

    // Check if the necessary data exists in the response
    if (!empty($data['rating']) && !empty($data['userRatingCount'])) {
        $formatted_data = [
            'rating' => $data['rating'] . '/5',
            'count' => number_format($data['userRatingCount']) . '+ reviews'
        ];

        // Send notifications
        Axioned_Reviews_Notifications::send_review_update_email(
            'google',
            $formatted_data,
            true,
            $trigger
        );
        Axioned_Reviews_Notifications::send_slack_notification('google', $formatted_data, true, $trigger);

        // Get field names
        $rating_field = get_option('axioned_google_rating_field');
        $count_field = get_option('axioned_google_count_field');
        
        if ($rating_field) {
            update_option($rating_field, $formatted_data['rating']);
            Axioned_Reviews_Logger::log("Updated Google rating ACF field: {$rating_field} with value: {$formatted_data['rating']}");
        }
        
        if ($count_field) {
            update_option($count_field, $formatted_data['count']);
            Axioned_Reviews_Logger::log("Updated Google count ACF field: {$count_field} with value: {$formatted_data['count']}");
        }
        
        // Clear caches if configured
        try {
            Axioned_Reviews_Cache_Handler::clear_all_caches();
        } catch (Exception $e) {
            Axioned_Reviews_Logger::log('Cache clearing failed: ' . $e->getMessage(), 'error');
        }

        return $formatted_data;
    } else {
        Axioned_Reviews_Logger::log('No Google business data found or missing fields.', 'error');
        Axioned_Reviews_Logger::log("Response data structure: " . print_r($data, true), 'error');
        return false;
    }
}
?>
