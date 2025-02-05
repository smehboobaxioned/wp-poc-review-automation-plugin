<?php
/**
 * Yelp Fetch
 *
 * This file contains the code for fetching Yelp reviews.
 */

function axioned_fetch_yelp_reviews($trigger = 'cron') {
    // Get settings from WordPress options
    $api_key = get_option('axioned_yelp_api_key');
    $business_name = get_option('axioned_yelp_business_name');
    $location = get_option('axioned_yelp_location');
    
    // Validate settings
    if (!$api_key || !$business_name || !$location) {
        $error_message = 'Yelp API configuration missing. Please check settings.';
        Axioned_Reviews_Logger::log($error_message, 'error');
        Axioned_Reviews_Notifications::send_review_update_email(
            'yelp',
            [],
            false,
            $trigger,
            $error_message
        );
        return false;
    }
    
    Axioned_Reviews_Logger::log("Fetching Yelp reviews for: $business_name in $location");
    
    // URL encode the business name and location
    $encoded_term = urlencode($business_name);
    $encoded_location = urlencode($location);
    
    // Build the API URL
    $url = "https://api.yelp.com/v3/businesses/search?term={$encoded_term}&location={$encoded_location}";

    // Initialize cURL
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer $api_key",
        ),
    ));

    // Execute cURL request
    $response = curl_exec($curl);
    
    // Check for cURL errors
    if (curl_errno($curl)) {
        $error_message = "cURL Error: " . curl_error($curl);
        Axioned_Reviews_Logger::log($error_message, 'error');
        Axioned_Reviews_Notifications::send_review_update_email(
            'yelp',
            [],
            false,
            $trigger,
            $error_message
        );
        curl_close($curl);
        return false;
    }
    
    // Check HTTP response code
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    Axioned_Reviews_Logger::log("Yelp API Response Code: $http_code");

    if ($http_code !== 200) {
        $error_message = "Yelp API returned HTTP code: $http_code\nResponse: $response";
        Axioned_Reviews_Logger::log($error_message, 'error');
        Axioned_Reviews_Notifications::send_review_update_email(
            'yelp',
            [],
            false,
            $trigger,
            $error_message
        );
        curl_close($curl);
        return false;
    }
    
    curl_close($curl);

    // Decode API response
    $data = json_decode($response, true);

    // Validate response data
    if (empty($data['businesses'])) {
        $error_message = 'No Yelp businesses found for the given name and location.';
        Axioned_Reviews_Logger::log($error_message, 'error');
        Axioned_Reviews_Notifications::send_review_update_email(
            'yelp',
            [],
            false,
            $trigger,
            $error_message
        );
        return false;
    }

    // Find exact match for business name (case-insensitive)
    $exact_match = null;
    foreach ($data['businesses'] as $business) {
        if (strtolower($business['name']) === strtolower($business_name)) {
            $exact_match = $business;
            break;
        }
    }

    if ($exact_match) {
        $formatted_data = [
            'rating' => $exact_match['rating'] . '/5',
            'count' => number_format($exact_match['review_count']) . '+ reviews'
        ];

        // Send success notification
        Axioned_Reviews_Notifications::send_review_update_email(
            'yelp',
            $formatted_data,
            true,
            $trigger
        );
        
        // Get field names
        $rating_field = get_option('axioned_yelp_rating_field');
        $count_field = get_option('axioned_yelp_count_field');
        
        if ($rating_field) {
            update_option($rating_field, $formatted_data['rating']);
            Axioned_Reviews_Logger::log("Updated Yelp rating ACF field: {$rating_field} with value: {$formatted_data['rating']}");
        }
        
        if ($count_field) {
            update_option($count_field, $formatted_data['count']);
            Axioned_Reviews_Logger::log("Updated Yelp count ACF field: {$count_field} with value: {$formatted_data['count']}");
        }
        
        return $formatted_data;
    } else {
        $error_message = "No exact match found for business name: $business_name";
        Axioned_Reviews_Logger::log($error_message, 'error');
        Axioned_Reviews_Notifications::send_review_update_email(
            'yelp',
            [],
            false,
            $trigger,
            $error_message
        );
        return false;
    }
}
