<?php
/**
 * Logs Tab
 *
 * This file contains the code for the Logs tab in the plugin settings.
 * It allows users to view and manage the logs for the plugin.
 */

function axioned_reviews_logs_tab() {
    // Get WordPress upload directory
    $upload_dir = wp_upload_dir();
    $log_dir = $upload_dir['basedir'] . '/axioned-reviews-logs';
    $log_file = $log_dir . '/debug.log';

    // Create directory if it doesn't exist
    if (!file_exists($log_dir)) {
        wp_mkdir_p($log_dir);
    }

    $logging_enabled = get_option('axioned_reviews_logging_enabled', true);
    ?>
    <div class="logs-container">
        <!-- Header Section -->
        <div class="logs-header">
            <div class="logs-controls">
                <div class="toggle-wrapper">
                    <label class="toggle-switch">
                        <input type="checkbox" 
                               id="logging-toggle" 
                               <?php checked($logging_enabled); ?>>
                        <span class="toggle-slider"></span>
                        <span class="toggle-label">Enable Logging</span>
                    </label>
                </div>
                <div class="logs-actions">
                    <button type="button" id="refresh-logs" class="button">
                        <span class="dashicons dashicons-update"></span>
                        Refresh
                    </button>
                    <button type="button" id="clear-logs" class="button button-secondary">
                        <span class="dashicons dashicons-trash"></span>
                        Clear Logs
                    </button>
                    <button type="button" id="download-logs" class="button button-primary">
                        <span class="dashicons dashicons-download"></span>
                        Download Logs
                    </button>
                </div>
            </div>
        </div>

        <!-- Logs Viewer -->
        <div class="logs-viewer">
            <div class="logs-stats">
                <div class="stat-item">
                    <span class="stat-label">Total Entries</span>
                    <span class="stat-value" id="total-entries">0</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Info</span>
                    <span class="stat-value info-count" id="info-count">0</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Errors</span>
                    <span class="stat-value error-count" id="error-count">0</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Warnings</span>
                    <span class="stat-value warning-count" id="warning-count">0</span>
                </div>
            </div>
            <div class="logs-content" id="logs-content">
                <?php
                if (file_exists($log_file)) {
                    $logs = file_get_contents($log_file);
                    if ($logs) {
                        echo '<pre id="log-entries">' . esc_html($logs) . '</pre>';
                    } else {
                        echo '<div class="empty-logs">No logs available</div>';
                    }
                } else {
                    echo '<div class="empty-logs">Log file not found</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <style>
    .logs-container {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin: 20px 0;
        padding: 20px;
    }

    .logs-header {
        border-bottom: 1px solid #e2e4e7;
        margin-bottom: 20px;
        padding-bottom: 20px;
    }

    .logs-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .toggle-wrapper {
        display: flex;
        align-items: center;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        margin-right: 10px;
    }

    .toggle-label {
        font-weight: 500;
        margin-left: 8px;
    }

    .logs-actions {
        display: flex;
        gap: 10px;
    }

    .logs-actions button {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .logs-stats {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 6px;
    }

    .stat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .stat-label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 600;
        color: #1e1e1e;
    }

    .info-count { color: #0073aa; }
    .error-count { color: #dc3232; }
    .warning-count { color: #ffc107; }

    .logs-content {
        background: #272822;
        border-radius: 6px;
        padding: 20px;
        max-height: 600px;
        overflow-y: auto;
    }

    #log-entries {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Consolas', monospace;
        font-size: 13px;
        line-height: 1.5;
        color: #f8f8f2;
        margin: 0;
        white-space: pre-wrap;
    }

    .empty-logs {
        text-align: center;
        padding: 40px;
        color: #666;
        font-style: italic;
    }

    /* Log entry colors */
    .log-info { color: #a6e22e; }
    .log-error { color: #f92672; }
    .log-warning { color: #fd971f; }

    /* Toggle Switch Styles */
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
        background-color: #ccc;
        border-radius: 34px;
        transition: .4s;
        cursor: pointer;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        border-radius: 50%;
        transition: .4s;
    }

    input:checked + .toggle-slider {
        background-color: #2196F3;
    }

    input:checked + .toggle-slider:before {
        transform: translateX(26px);
    }

    label.toggle-switch span,
    .logs-actions span {
        vertical-align: middle;
    }

    .dashicons.is-spinning {
        animation: spin 1s infinite linear;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .logs-actions button:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .logs-actions button:disabled .dashicons {
        opacity: 0.7;
    }

    #log-entries {
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    </style>

    <script>
    jQuery(document).ready(function($) {
        // Function to count log entries by type
        function updateLogStats() {
            const logContent = $('#log-entries').text();
            const lines = logContent.split('\n').filter(line => line.trim());
            
            $('#total-entries').text(lines.length);
            $('#info-count').text(lines.filter(line => line.includes('[INFO]')).length);
            $('#error-count').text(lines.filter(line => line.includes('[ERROR]')).length);
            $('#warning-count').text(lines.filter(line => line.includes('[WARNING]')).length);
        }

        // Color code log entries
        function colorCodeLogs() {
            const logContent = $('#log-entries');
            let html = logContent.html();
            
            if (html) {
                html = html.replace(/\[INFO\].*$/gm, match => `<span class="log-info">${match}</span>`);
                html = html.replace(/\[ERROR\].*$/gm, match => `<span class="log-error">${match}</span>`);
                html = html.replace(/\[WARNING\].*$/gm, match => `<span class="log-warning">${match}</span>`);
                
                logContent.html(html);
            }
        }

        // Initialize
        updateLogStats();
        colorCodeLogs();

        // Refresh Logs
        $('#refresh-logs').on('click', function() {
            const $button = $(this);
            const $icon = $button.find('.dashicons');
            
            // Disable button and add spinning effect
            $button.prop('disabled', true);
            $icon.addClass('is-spinning');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'axioned_refresh_logs',
                    nonce: '<?php echo wp_create_nonce('axioned_logs_actions'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        const logsContent = $('#logs-content');
                        if (response.data.logs) {
                            // Update logs content
                            if ($('#log-entries').length) {
                                $('#log-entries').html(response.data.logs);
                            } else {
                                logsContent.html('<pre id="log-entries">' + response.data.logs + '</pre>');
                            }
                            // Update stats and colors
                            updateLogStats();
                            colorCodeLogs();
                        } else {
                            logsContent.html('<div class="empty-logs">No logs available</div>');
                        }
                    }
                },
                error: function() {
                    alert('Failed to refresh logs. Please try again.');
                },
                complete: function() {
                    // Re-enable button and stop spinning
                    $button.prop('disabled', false);
                    $icon.removeClass('is-spinning');
                }
            });
        });

        // Clear Logs
        $('#clear-logs').on('click', function() {
            if (!confirm('Are you sure you want to clear all logs?')) {
                return;
            }

            const $button = $(this);
            $button.prop('disabled', true);
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'axioned_clear_logs',
                    nonce: '<?php echo wp_create_nonce('axioned_logs_actions'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $('#log-entries').html('');
                        updateLogStats();
                    }
                },
                complete: function() {
                    $button.prop('disabled', false);
                }
            });
        });

        // Download Logs
        $('#download-logs').on('click', function() {
            const downloadUrl = ajaxurl + '?' + $.param({
                action: 'axioned_download_logs',
                nonce: '<?php echo wp_create_nonce('axioned_logs_actions'); ?>'
            });
            
            // Create temporary link and trigger download
            const link = document.createElement('a');
            link.href = downloadUrl;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

        // Toggle Logging
        $('#logging-toggle').on('change', function() {
            const enabled = $(this).prop('checked');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'axioned_toggle_logging',
                    enabled: enabled ? 1 : 0,
                    nonce: '<?php echo wp_create_nonce('axioned_logs_actions'); ?>'
                }
            });
        });
    });
    </script>
    <?php
}

// Add handler for logging controls
function axioned_handle_logging_controls() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Handle logging toggle
    if (isset($_POST['logging_nonce']) && wp_verify_nonce($_POST['logging_nonce'], 'toggle_logging')) {
        if (isset($_POST['enable_logging'])) {
            update_option('axioned_enable_logging', '1');
            Axioned_Reviews_Logger::log('Logging enabled');
        } else {
            update_option('axioned_enable_logging', '0');
        }

        // Handle clear logs
        if (isset($_POST['clear_logs'])) {
            Axioned_Reviews_Logger::clear_logs();
        }
    }
}
add_action('admin_init', 'axioned_handle_logging_controls');

// AJAX handler for refreshing logs
function axioned_handle_refresh_logs() {
    check_ajax_referer('axioned_logs_actions', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized access');
        return;
    }

    $logs = Axioned_Reviews_Logger::get_logs();
    
    // Clean the response
    ob_clean(); // Clean any existing output
    
    if ($logs) {
        // Clean and prepare logs
        $logs = trim($logs); // Remove extra whitespace
        $logs = str_replace('\\', '', $logs); // Remove escaped slashes
        $logs = str_replace(array("\\r\\n", "\\n", "\\r"), "\n", $logs); // Normalize line endings
        
        wp_send_json_success(array(
            'logs' => $logs
        ));
    } else {
        wp_send_json_success(array(
            'logs' => '<div class="empty-logs">No logs available</div>'
        ));
    }
}
add_action('wp_ajax_axioned_refresh_logs', 'axioned_handle_refresh_logs');

// AJAX handler for clearing logs
function axioned_handle_clear_logs() {
    check_ajax_referer('axioned_logs_actions', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized access');
        return;
    }

    ob_clean(); // Clean any existing output
    Axioned_Reviews_Logger::clear_logs();
    wp_send_json_success('Logs cleared successfully');
}
add_action('wp_ajax_axioned_clear_logs', 'axioned_handle_clear_logs');

// AJAX handler for downloading logs
function axioned_handle_download_logs() {
    check_ajax_referer('axioned_logs_actions', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized access');
        return;
    }

    $logs = Axioned_Reviews_Logger::get_logs();
    $filename = 'axioned-reviews-' . date('Y-m-d-H-i-s') . '.log';
    
    // Clean any existing output
    ob_clean();
    
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache');
    
    echo $logs;
    exit;
}
add_action('wp_ajax_axioned_download_logs', 'axioned_handle_download_logs');
