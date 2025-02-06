<?php
/**
 * Yelp Scraper
 * 
 * Fallback mechanism to scrape Yelp reviews when API fails
 */

class Axioned_Yelp_Scraper {
    private static $max_retries = 3;
    private static $retry_delay = 2; // seconds
    private static $cookie_file;

    private static function init() {
        // Create a unique cookie file in the WordPress uploads directory
        $upload_dir = wp_upload_dir();
        $cookie_dir = $upload_dir['basedir'] . '/axioned-reviews-cookies';
        if (!file_exists($cookie_dir)) {
            wp_mkdir_p($cookie_dir);
        }
        self::$cookie_file = $cookie_dir . '/yelp_cookies.txt';
    }

    private static function getRandomUserAgent() {
        $agents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.0.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2.1 Safari/605.1.15'
        ];
        return $agents[array_rand($agents)];
    }

    private static function makeRequest($url, $attempt = 1) {
        self::init();

        if ($attempt > self::$max_retries) {
            throw new Exception("Max retry attempts reached");
        }

        if ($attempt > 1) {
            Axioned_Reviews_Logger::log("Retry attempt {$attempt} after delay...");
            sleep(self::$retry_delay * ($attempt - 1)); // Exponential backoff
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => 'gzip, deflate',
            CURLOPT_USERAGENT => self::getRandomUserAgent(),
            CURLOPT_HTTPHEADER => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                'Accept-Language: en-US,en;q=0.9',
                'Cache-Control: max-age=0',
                'Connection: keep-alive',
                'Host: www.yelp.com',
                'Referer: https://www.yelp.com/',
                'Sec-Ch-Ua: "Not A(Brand";v="99", "Google Chrome";v="121", "Chromium";v="121"',
                'Sec-Ch-Ua-Mobile: ?0',
                'Sec-Ch-Ua-Platform: "Windows"',
                'Sec-Fetch-Dest: document',
                'Sec-Fetch-Mode: navigate',
                'Sec-Fetch-Site: same-origin',
                'Sec-Fetch-User: ?1',
                'Upgrade-Insecure-Requests: 1'
            ],
            CURLOPT_COOKIEJAR => self::$cookie_file,
            CURLOPT_COOKIEFILE => self::$cookie_file,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_errno($ch) ? curl_error($ch) : null;
        curl_close($ch);

        if ($curl_error) {
            Axioned_Reviews_Logger::log("cURL error on attempt {$attempt}: {$curl_error}", 'error');
            return self::makeRequest($url, $attempt + 1);
        }

        if ($http_code === 403) {
            Axioned_Reviews_Logger::log("403 error on attempt {$attempt}, retrying...", 'error');
            return self::makeRequest($url, $attempt + 1);
        }

        if ($http_code !== 200) {
            throw new Exception("HTTP request failed with status code: " . $http_code);
        }

        return $response;
    }

    /**
     * Scrape Yelp reviews for a business
     */
    public static function scrape_reviews($business_name, $location) {
        try {
            // Improved URL sanitization
            $business_name = strtolower($business_name);
            $location = strtolower($location);

            // Replace special characters and spaces
            $replacements = [
                '&' => 'and',
                '+' => 'and',
                '@' => 'at',
                '/' => '-',
                '.' => '',
                ',' => '',
                '\'' => '',
                '"' => '',
                '--' => '-',
                ' & ' => ' and ',
                ' + ' => ' and '
            ];

            // First replace special word cases
            $business_name = str_replace(array_keys($replacements), array_values($replacements), $business_name);
            
            // Then replace remaining spaces with hyphens
            $business_name = str_replace(' ', '-', trim($business_name));
            $location = str_replace(' ', '-', trim($location));

            // Remove any double hyphens that might have been created
            $business_name = preg_replace('/-+/', '-', $business_name);
            $location = preg_replace('/-+/', '-', $location);

            $url = "https://www.yelp.com/biz/{$business_name}-{$location}";
            
            Axioned_Reviews_Logger::log("Attempting to scrape URL: " . $url);

            // Make request with retry logic
            $response = self::makeRequest($url);

            if (empty($response)) {
                throw new Exception("Empty response received from Yelp");
            }

            Axioned_Reviews_Logger::log("Successfully fetched page content");
            

            // Use DOMDocument to parse the HTML
            $doc = new DOMDocument();
            @$doc->loadHTML($response, LIBXML_NOERROR);
            $xpath = new DOMXPath($doc);

            $rating = null;
            $count = null;

            // Method 1: Try banner section first
            Axioned_Reviews_Logger::log("Attempting Method 1: Banner section");
            $review_section = $xpath->query('//div[@data-testid="BizHeaderReviewCount"]');
            if ($review_section->length > 0) {
                Axioned_Reviews_Logger::log("Found BizHeaderReviewCount section");
                
                // Get spans within this section
                $spans = $xpath->query('.//span', $review_section->item(0));
                Axioned_Reviews_Logger::log("Found {$spans->length} spans in review section");
                
                if ($spans->length >= 2) {
                    // First span contains rating
                    $rating_text = $spans->item(0)->nodeValue;
                    Axioned_Reviews_Logger::log("Rating text found: " . $rating_text);
                    
                    if (preg_match('/(\d+\.?\d*)/', $rating_text, $matches)) {
                        $rating = floatval($matches[1]);
                        Axioned_Reviews_Logger::log("Extracted rating: " . $rating);
                    }

                    // Second span contains review count in anchor
                    $review_anchor = $xpath->query('.//a[contains(@href, "#reviews")]', $spans->item(1));
                    if ($review_anchor->length > 0) {
                        $count_text = $review_anchor->item(0)->nodeValue;
                        Axioned_Reviews_Logger::log("Count text found: " . $count_text);
                        
                        if (preg_match('/(\d+\.?\d*)k?\s*reviews?/i', $count_text, $matches)) {
                            $count_value = floatval($matches[1]);
                            if (stripos($count_text, 'k') !== false) {
                                $count_value *= 1000;
                            }
                            $count = round($count_value);
                            Axioned_Reviews_Logger::log("Extracted count: " . $count);
                        }
                    }
                }
            } else {
                Axioned_Reviews_Logger::log("Banner section not found");
            }

            // Method 2: Try overall rating section if banner failed
            if ($rating === null || $count === null) {
                Axioned_Reviews_Logger::log("Attempting Method 2: Overall rating section");
                
                // Find the reviews section and review summary
                $review_summary = $xpath->query('//div[@id="reviews"]//div[@data-testid="review-summary"]');
                if ($review_summary->length > 0) {
                    Axioned_Reviews_Logger::log("Found review summary section");
                    
                    // Look for rating in aria-label
                    $rating_div = $xpath->query('.//div[@role="img" and contains(@aria-label, "star rating")]', $review_summary->item(0));
                    if ($rating_div->length > 0) {
                        $rating_text = $rating_div->item(0)->getAttribute('aria-label');
                        Axioned_Reviews_Logger::log("Found rating text: " . $rating_text);
                        
                        if (preg_match('/(\d+\.?\d*)\s*star rating/', $rating_text, $matches)) {
                            $rating = floatval($matches[1]);
                            Axioned_Reviews_Logger::log("Extracted rating: " . $rating);
                        }
                    }

                    // Look for review count
                    $count_span = $xpath->query('.//span[contains(text(), "reviews")]', $review_summary->item(0));
                    if ($count_span->length > 0) {
                        $count_text = $count_span->item(0)->nodeValue;
                        Axioned_Reviews_Logger::log("Found count text: " . $count_text);
                        
                        if (preg_match('/(\d+)/', $count_text, $matches)) {
                            $count = intval($matches[1]);
                            Axioned_Reviews_Logger::log("Extracted count: " . $count);
                        }
                    }
                } else {
                    Axioned_Reviews_Logger::log("Review summary section not found");
                }
            }

            // Log the final results
            if ($rating !== null) {
                Axioned_Reviews_Logger::log("Final rating found: " . $rating);
            } else {
                Axioned_Reviews_Logger::log("No rating found in any method");
            }

            if ($count !== null) {
                Axioned_Reviews_Logger::log("Final count found: " . $count);
            } else {
                Axioned_Reviews_Logger::log("No count found in any method");
            }

            if ($rating !== null && $count !== null) {
                $result = [
                    'rating' => number_format($rating, 1) . '/5',
                    'count' => $count . '+ reviews'
                ];
                Axioned_Reviews_Logger::log("Successfully scraped Yelp reviews: " . print_r($result, true));
                return $result;
            }

            throw new Exception('Could not find rating or review count on page');

        } catch (Exception $e) {
            Axioned_Reviews_Logger::log('Yelp scraping failed: ' . $e->getMessage(), 'error');
            return false;
        }
    }
} 