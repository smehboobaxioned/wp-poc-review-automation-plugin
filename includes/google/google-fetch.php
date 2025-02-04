<?php
/**
 * Google Fetch
 *
 * This file contains the code for fetching Google reviews.
 */

function axioned_fetch_google_reviews() {
    Axioned_Reviews_Logger::log("Starting Google Reviews Fetch Process");

    // Get settings
    $api_key = get_option('axioned_google_api_key');
    $place_id = get_option('axioned_google_place_id');
    
    if (!$api_key || !$place_id) {
        Axioned_Reviews_Logger::log('Google API configuration missing. API Key or Place ID not set.', 'error');
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
        $error_message = curl_error($curl);
        Axioned_Reviews_Logger::log("cURL Error: $error_message", 'error');
        curl_close($curl);
        return false;
    }
    
    // Get HTTP response code
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    Axioned_Reviews_Logger::log("API Response Code: $http_code");
    
    if ($http_code !== 200) {
        Axioned_Reviews_Logger::log("Google API returned error HTTP code: $http_code", 'error');
        Axioned_Reviews_Logger::log("API Response: $response", 'error');
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
        Axioned_Reviews_Logger::log("Successfully fetched Google reviews. Rating: {$data['rating']}, Count: {$data['userRatingCount']}");
        
        // Format the values
        $formatted_rating = $data['rating'] . '/5';
        $formatted_count = number_format($data['userRatingCount']) . '+ reviews';
        
        // Get field names
        $rating_field = get_option('axioned_google_rating_field');
        $count_field = get_option('axioned_google_count_field');
        
        if ($rating_field) {
            update_option($rating_field, $formatted_rating);
            Axioned_Reviews_Logger::log("Updated Google rating ACF field: {$rating_field} with value: {$formatted_rating}");
        }
        
        if ($count_field) {
            update_option($count_field, $formatted_count);
            Axioned_Reviews_Logger::log("Updated Google count ACF field: {$count_field} with value: {$formatted_count}");
        }
        
        return [
            'rating' => $formatted_rating,
            'count'  => $formatted_count
        ];
    } else {
        Axioned_Reviews_Logger::log('No Google business data found or missing fields.', 'error');
        Axioned_Reviews_Logger::log("Response data structure: " . print_r($data, true), 'error');
        return false;
    }
}
?>
