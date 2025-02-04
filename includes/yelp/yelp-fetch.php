<?php
/**
 * Yelp Fetch
 *
 * This file contains the code for fetching Yelp reviews.
 */

function axioned_fetch_yelp_reviews() {
    // Get settings from WordPress options
    $api_key = get_option('axioned_yelp_api_key');
    $business_name = get_option('axioned_yelp_business_name');
    $location = get_option('axioned_yelp_location');
    
    // Validate settings
    if (!$api_key || !$business_name || !$location) {
        Axioned_Reviews_Logger::log('Yelp API configuration missing. Please check settings.', 'error');
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
        Axioned_Reviews_Logger::log('Yelp API cURL Error: ' . curl_error($curl), 'error');
        curl_close($curl);
        return false;
    }
    
    // Check HTTP response code
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    Axioned_Reviews_Logger::log("Yelp API Response Code: $http_code");

    if ($http_code !== 200) {
        Axioned_Reviews_Logger::log("Yelp API returned HTTP code: $http_code", 'error');
        curl_close($curl);
        return false;
    }
    
    curl_close($curl);

    // Decode API response
    $data = json_decode($response, true);

    // Validate response data
    if (empty($data['businesses'])) {
        error_log('No Yelp businesses found for the given name and location.');
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
        Axioned_Reviews_Logger::log("Successfully fetched Yelp reviews. Rating: {$exact_match['rating']}, Count: {$exact_match['review_count']}");
        
        // Format the values
        $formatted_rating = $exact_match['rating'] . '/5';
        $formatted_count = number_format($exact_match['review_count']) . '+ reviews';
        
        // Get field names
        $rating_field = get_option('axioned_yelp_rating_field');
        $count_field = get_option('axioned_yelp_count_field');
        
        if ($rating_field) {
            update_option($rating_field, $formatted_rating);
            Axioned_Reviews_Logger::log("Updated Yelp rating ACF field: {$rating_field} with value: {$formatted_rating}");
        }
        
        if ($count_field) {
            update_option($count_field, $formatted_count);
            Axioned_Reviews_Logger::log("Updated Yelp count ACF field: {$count_field} with value: {$formatted_count}");
        }
        
        return [
            'rating' => $formatted_rating,
            'count'  => $formatted_count
        ];
    } else {
        Axioned_Reviews_Logger::log("No exact match found for business name: $business_name", 'error');
        return false;
    }
}
