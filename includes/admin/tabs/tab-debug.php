<?php
/**
 * Debug Tab
 *
 * This file contains the code for the Debug tab in the plugin settings.
 * It allows users to view and test the API connections for Google and Yelp.
 */

function axioned_reviews_debug_tab() {
    $logging_enabled = get_option('axioned_enable_logging') === '1';
    $upload_dir = wp_upload_dir();
    $log_file_path = $upload_dir['basedir'] . '/axioned-reviews-logs/debug.log';
    $log_url = $upload_dir['baseurl'] . '/axioned-reviews-logs/debug.log';
    $log_dir = dirname($log_file_path);
    ?>
    <div class="debug-container">
        <h2>API Debug Tools</h2>
        
        <!-- Log Information Section (Collapsed by default) -->
        <div class="debug-section collapsible collapsed">
            <h3 class="collapsible-header">
                <span class="section-title">Log Information</span>
                <span class="toggle-indicator"></span>
            </h3>
            <div class="collapsible-content">
                <!-- Logging Information -->
                <div class="debug-info">
                    <h4>Logging Status</h4>
                    <p><strong>Status:</strong> 
                        <?php echo $logging_enabled ? 
                            '<span class="status-ok">Enabled</span>' : 
                            '<span class="status-error">Disabled</span>'; ?>
                    </p>
                    <?php if ($logging_enabled): ?>
                        <p><strong>Log File Location:</strong> 
                            <code><?php echo esc_html($log_file_path); ?></code>
                        </p>
                        <?php if (file_exists($log_file_path)): ?>
                            <p><strong>Log File Size:</strong> 
                                <?php echo size_format(filesize($log_file_path)); ?>
                            </p>
                            <p><a href="<?php echo esc_url($log_url); ?>" 
                                  class="button button-secondary" 
                                  target="_blank">Download Log File</a>
                            </p>
                        <?php else: ?>
                            <p><em>Log file will be created when first log entry is written.</em></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p><em>Enable logging in the Configuration tab to start collecting debug information.</em></p>
                    <?php endif; ?>
                </div>

                <!-- Logger Diagnostics -->
                <?php if ($logging_enabled): ?>
                    <div class="debug-info">
                        <h4>Logger Diagnostics</h4>
                        <ul>
                            <li>Log Directory Exists: <?php echo file_exists($log_dir) ? '✅ Yes' : '❌ No'; ?></li>
                            <li>Log Directory Writable: <?php echo is_writable($log_dir) ? '✅ Yes' : '❌ No'; ?></li>
                            <li>Log File Exists: <?php echo file_exists($log_file_path) ? '✅ Yes' : '❌ No'; ?></li>
                            <?php if (file_exists($log_file_path)): ?>
                                <li>Log File Writable: <?php echo is_writable($log_file_path) ? '✅ Yes' : '❌ No'; ?></li>
                                <li>Log File Permissions: <?php echo decoct(fileperms($log_file_path) & 0777); ?></li>
                            <?php endif; ?>
                            <li>Log Directory Path: <code><?php echo esc_html($log_dir); ?></code></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- API Testing Section (Open by default) -->
        <div class="debug-section collapsible">
            <h3 class="collapsible-header">
                <span class="section-title">Test APIs</span>
                <span class="toggle-indicator"></span>
            </h3>
            <div class="collapsible-content">
                <!-- Google API Section -->
                <div class="api-section collapsible">
                    <h4 class="collapsible-header">
                        <span class="section-title">Google Reviews API</span>
                        <span class="toggle-indicator"></span>
                    </h4>
                    <div class="collapsible-content">
                        <div class="debug-info">
                            <p><strong>API Key:</strong> <span class="masked-key"><?php echo esc_html(substr(get_option('axioned_google_api_key', ''), 0, 6) . '...'); ?></span></p>
                            <p><strong>Place ID:</strong> <?php echo esc_html(get_option('axioned_google_place_id', '')); ?></p>
                        </div>
                        <button class="button test-api" data-api="google">Test Google API Connection</button>
                        <div class="debug-progress" id="google-progress" style="display: none;">
                            <span class="spinner is-active"></span> Testing API connection...
                        </div>
                        <div class="debug-results" id="google-results"></div>
                    </div>
                </div>

                <!-- Yelp API Section -->
                <div class="api-section collapsible">
                    <h4 class="collapsible-header">
                        <span class="section-title">Yelp Reviews API</span>
                        <span class="toggle-indicator"></span>
                    </h4>
                    <div class="collapsible-content">
                        <div class="debug-info">
                            <p><strong>API Key:</strong> <span class="masked-key"><?php echo esc_html(substr(get_option('axioned_yelp_api_key', ''), 0, 6) . '...'); ?></span></p>
                            <p><strong>Business Name:</strong> <?php echo esc_html(get_option('axioned_yelp_business_name', '')); ?></p>
                            <p><strong>Location:</strong> <?php echo esc_html(get_option('axioned_yelp_location', '')); ?></p>
                        </div>
                        <button class="button test-api" data-api="yelp">Test Yelp API Connection</button>
                        <div class="debug-progress" id="yelp-progress" style="display: none;">
                            <span class="spinner is-active"></span> Testing API connection...
                        </div>
                        <div class="debug-results" id="yelp-results"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
}

// Update the AJAX handler function
function axioned_handle_test_api() {
    // Prevent any output before JSON response
    ob_clean();
    
    // Set JSON headers
    header('Content-Type: application/json');
    header('Cache-Control: no-cache');
    
    // Verify nonce
    check_ajax_referer('axioned_test_api', 'nonce');
    
    if (!current_user_can('manage_options')) {
        echo json_encode(array(
            'success' => false,
            'data' => 'Unauthorized access'
        ));
        exit;
    }

    $api = isset($_POST['api']) ? sanitize_text_field($_POST['api']) : '';
    
    try {
        switch ($api) {
            case 'google':
                $result = axioned_fetch_google_reviews('debug');
                break;
            case 'yelp':
                $result = axioned_fetch_yelp_reviews('debug');
                break;
            default:
                echo json_encode(array(
                    'success' => false,
                    'data' => 'Invalid API specified'
                ));
                exit;
        }

        if ($result && is_array($result)) {
            // Log successful response
            Axioned_Reviews_Logger::log("API Test successful for $api. Response: " . print_r($result, true));
            
            $response = array(
                'success' => true,
                'data' => array(
                    'rating' => number_format((float)$result['rating'], 1),
                    'count' => number_format((int)$result['count'])
                )
            );
        } else {
            // Get the last error from logs
            $logs = Axioned_Reviews_Logger::get_logs();
            $log_lines = explode("\n", $logs);
            $last_error = '';
            
            foreach (array_reverse($log_lines) as $line) {
                if (strpos($line, '[ERROR]') !== false) {
                    $last_error = preg_replace('/^\[[^\]]+\]\s*\[ERROR\]\s*/', '', $line);
                    break;
                }
            }
            
            $response = array(
                'success' => false,
                'data' => $last_error ?: 'Failed to fetch reviews'
            );
        }
        
        // Clean any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Send JSON response
        echo json_encode($response);
        exit;

    } catch (Exception $e) {
        Axioned_Reviews_Logger::log('API Test Exception: ' . $e->getMessage(), 'error');
        
        // Clean any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        echo json_encode(array(
            'success' => false,
            'data' => 'Error: ' . $e->getMessage()
        ));
        exit;
    }
}
add_action('wp_ajax_axioned_test_api', 'axioned_handle_test_api');
