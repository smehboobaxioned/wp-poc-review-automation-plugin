<?php
/**
 * Cron Status Tab
 *
 * This file contains the code for the Cron Status tab in the plugin settings.
 * It allows users to view and manage the cron jobs for Google and Yelp reviews.
 */

function axioned_reviews_cron_status_tab() {
    $next_google_schedule = wp_next_scheduled('axioned_update_google_reviews');
    $next_yelp_schedule = wp_next_scheduled('axioned_update_yelp_reviews');
    $current_frequency = get_option('axioned_reviews_frequency', 'daily');
    $current_time = get_option('axioned_reviews_time', '00:00'); // Default to 12 AM UTC

    // Get enabled status
    $google_enabled = get_option('axioned_google_cron_enabled', '0');
    $yelp_enabled = get_option('axioned_yelp_cron_enabled', '0');

    // Check if WordPress cron is enabled
    $cron_enabled = !(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON);

    ?>
    <div class="cron-status-container">
        <?php if (!$cron_enabled): ?>
            <div class="notice notice-error">
                <p><strong>WordPress Cron is disabled!</strong></p>
                <p>The automatic review updates will not work until WordPress Cron is enabled. To enable it, you have two options:</p>
                <ol>
                    <li>Remove or set <code>DISABLE_WP_CRON</code> to <code>false</code> in your wp-config.php file.</li>
                    <li>Set up a system cron job to trigger WordPress cron events:</li>
                </ol>
                <div class="code-block">
                    <p>Add this line to your server's crontab:</p>
                    <code>*/15 * * * * wget -q -O /dev/null '<?php echo site_url('wp-cron.php?doing_wp_cron'); ?>' >/dev/null 2>&1</code>
                    <p class="description">This will trigger WordPress cron events every 15 minutes.</p>
                </div>
                <p>If you need help, please contact your hosting provider or system administrator.</p>
            </div>
        <?php else: ?>
            <div class="notice notice-success">
                <p><strong>WordPress Cron is enabled and working!</strong> You can configure your review update schedule below.</p>
            </div>
        <?php endif; ?>

        <div class="cron-section">
            <h2>Schedule Settings</h2>
            <form method="post" class="schedule-form" id="cron-schedule-form">
                <?php wp_nonce_field('axioned_update_schedule', 'schedule_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">Update Frequency</th>
                        <td>
                            <select name="axioned_reviews_frequency" class="frequency-select">
                                <option value="daily" <?php selected($current_frequency, 'daily'); ?>>Daily</option>
                                <option value="weekly" <?php selected($current_frequency, 'weekly'); ?>>Weekly (Every Monday)</option>
                                <option value="monthly" <?php selected($current_frequency, 'monthly'); ?>>Monthly (1st of Month)</option>
                            </select>
                            <p class="description">Select how often the reviews should be updated.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Update Time (UTC)</th>
                        <td>
                            <input type="time" 
                                   name="axioned_reviews_time" 
                                   value="<?php echo esc_attr($current_time); ?>" 
                                   class="time-select">
                            <p class="description">Select the time in UTC when updates should occur. Current UTC time is: <span id="current-utc"></span></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save Schedule Settings', 'primary', 'submit-schedule'); ?>
            </form>
        </div>

        <div class="cron-section">
            <h2>Cron Jobs Status</h2>
            <div class="cron-info">
                <p>All times shown are in UTC timezone to ensure consistent scheduling regardless of server configuration.</p>
            </div>
            <div class="cron-table-wrapper">
                <form method="post" action="" id="cron-toggle-form">
                    <?php wp_nonce_field('toggle_cron', 'cron_nonce'); ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Next Run</th>
                                <th>Frequency</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <span class="status-dot <?php echo $google_enabled === '1' ? 'active' : 'inactive'; ?>"></span>
                                    <strong>Google Reviews Update</strong>
                                    <div class="row-actions">
                                        <span class="last-run">Last run: <?php echo get_option('axioned_google_last_run', 'Never'); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($next_google_schedule && $google_enabled === '1'): ?>
                                        <span class="next-run">
                                            <?php echo date('Y-m-d H:i:s', $next_google_schedule); ?>
                                            <span class="time-diff">(<?php echo human_time_diff($next_google_schedule); ?> from now)</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="not-scheduled"><?php echo $google_enabled === '1' ? 'Scheduling...' : 'Not scheduled'; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo ucfirst($current_frequency); ?></td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" 
                                               name="cron_status[google]" 
                                               value="1"
                                               <?php checked($google_enabled, '1'); ?>>
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="status-text">
                                        <?php echo $google_enabled === '1' ? 'Enabled' : 'Disabled'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" 
                                            class="button run-now" 
                                            data-service="google" 
                                            <?php disabled($google_enabled, '0'); ?>>
                                        <span class="dashicons dashicons-update"></span>
                                        Run Now
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="status-dot <?php echo $yelp_enabled === '1' ? 'active' : 'inactive'; ?>"></span>
                                    <strong>Yelp Reviews Update</strong>
                                    <div class="row-actions">
                                        <span class="last-run">Last run: <?php echo get_option('axioned_yelp_last_run', 'Never'); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($next_yelp_schedule && $yelp_enabled === '1'): ?>
                                        <span class="next-run">
                                            <?php echo date('Y-m-d H:i:s', $next_yelp_schedule); ?>
                                            <span class="time-diff">(<?php echo human_time_diff($next_yelp_schedule); ?> from now)</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="not-scheduled"><?php echo $yelp_enabled === '1' ? 'Scheduling...' : 'Not scheduled'; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo ucfirst($current_frequency); ?></td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" 
                                               name="cron_status[yelp]" 
                                               value="1"
                                               <?php checked($yelp_enabled, '1'); ?>>
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="status-text">
                                        <?php echo $yelp_enabled === '1' ? 'Enabled' : 'Disabled'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" 
                                            class="button run-now" 
                                            data-service="yelp" 
                                            <?php disabled($yelp_enabled, '0'); ?>>
                                        <span class="dashicons dashicons-update"></span>
                                        Run Now
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php submit_button('Save Cron Changes', 'primary', 'submit', true); ?>
                </form>
            </div>
        </div>
    </div>
    <?php
}

// Update the AJAX handler for schedule settings
function axioned_handle_schedule_update() {
    // Clean any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Set JSON headers
    header('Content-Type: application/json');
    header('Cache-Control: no-cache');
    
    check_ajax_referer('axioned_update_schedule', 'schedule_nonce');
    
    if (!current_user_can('manage_options')) {
        axioned_set_admin_notice('Unauthorized access', 'error');
        wp_send_json_error('Unauthorized access');
        return;
    }

    $frequency = isset($_POST['frequency']) ? sanitize_text_field($_POST['frequency']) : '';
    $time = isset($_POST['time']) ? sanitize_text_field($_POST['time']) : '';

    // Validate frequency
    if (!in_array($frequency, array('daily', 'weekly', 'monthly'))) {
        axioned_set_admin_notice('Invalid frequency selected', 'error');
        wp_send_json_error('Invalid frequency');
        return;
    }

    // Validate time format (HH:mm)
    if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
        axioned_set_admin_notice('Invalid time format', 'error');
        wp_send_json_error('Invalid time format');
        return;
    }

    try {
        ob_start();
        
        // Update options
        update_option('axioned_reviews_frequency', $frequency);
        update_option('axioned_reviews_time', $time);

        // Clear existing schedules
        wp_clear_scheduled_hook('axioned_update_google_reviews');
        wp_clear_scheduled_hook('axioned_update_yelp_reviews');
        Axioned_Reviews_Logger::log("Cleared existing cron schedules due to schedule update");

        // Reschedule cron jobs if they're enabled
        if (get_option('axioned_google_cron_enabled', '0') === '1') {
            $next_run = axioned_calculate_next_run($frequency, $time);
            if ($next_run) {
                wp_schedule_event($next_run->getTimestamp(), $frequency, 'axioned_update_google_reviews');
                Axioned_Reviews_Logger::log("Rescheduled Google cron for: " . $next_run->format('Y-m-d H:i:s'));
            }
        }
        
        if (get_option('axioned_yelp_cron_enabled', '0') === '1') {
            $next_run = axioned_calculate_next_run($frequency, $time);
            if ($next_run) {
                wp_schedule_event($next_run->getTimestamp(), $frequency, 'axioned_update_yelp_reviews');
                Axioned_Reviews_Logger::log("Rescheduled Yelp cron for: " . $next_run->format('Y-m-d H:i:s'));
            }
        }

        ob_end_clean();
        axioned_set_admin_notice('Schedule updated successfully');
        wp_send_json_success('Schedule updated successfully');
    } catch (Exception $e) {
        // Clean buffer before sending error
        if (ob_get_level()) {
            ob_end_clean();
        }
        // Set error notice
        axioned_set_admin_notice('Failed to update schedule: ' . $e->getMessage(), 'error');
        wp_send_json_error('Failed to update schedule: ' . $e->getMessage());
    }
}
add_action('wp_ajax_axioned_update_schedule', 'axioned_handle_schedule_update');

// Add this after the existing schedule handler
function axioned_handle_toggle_cron() {
    // Clean any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Set JSON headers
    header('Content-Type: application/json');
    header('Cache-Control: no-cache');
    
    check_ajax_referer('toggle_cron', 'nonce');
    
    if (!current_user_can('manage_options')) {
        axioned_set_admin_notice('Unauthorized access', 'error');
        wp_send_json_error('Unauthorized access');
        return;
    }

    try {
        ob_start();

        $cron_status = isset($_POST['cron_status']) ? (array) $_POST['cron_status'] : array();
        Axioned_Reviews_Logger::log("Processing cron status update: " . print_r($cron_status, true));

        $changes_made = false;
        $frequency = get_option('axioned_reviews_frequency', 'daily');
        $time = get_option('axioned_reviews_time', '00:00');

        // Handle Google cron
        $google_new_status = isset($cron_status['google']) ? $cron_status['google'] : '0';
        $google_enabled = get_option('axioned_google_cron_enabled', '0');
        
        if ($google_new_status !== $google_enabled) {
            update_option('axioned_google_cron_enabled', $google_new_status);
            $changes_made = true;

            // Clear existing schedule
            wp_clear_scheduled_hook('axioned_update_google_reviews');
            Axioned_Reviews_Logger::log("Cleared Google cron schedule");

            // Only schedule if being enabled
            if ($google_new_status === '1') {
                $next_run = axioned_calculate_next_run($frequency, $time);
                if ($next_run) {
                    wp_schedule_event($next_run->getTimestamp(), $frequency, 'axioned_update_google_reviews');
                    Axioned_Reviews_Logger::log("Scheduled Google cron for: " . $next_run->format('Y-m-d H:i:s'));
                }
            }
        }

        Axioned_Reviews_Logger::log("Google cron status updated: " . $google_new_status);

        // Handle Yelp cron
        $yelp_new_status = isset($cron_status['yelp']) ? $cron_status['yelp'] : '0';
        $yelp_enabled = get_option('axioned_yelp_cron_enabled', '0');
        
        if ($yelp_new_status !== $yelp_enabled) {
            update_option('axioned_yelp_cron_enabled', $yelp_new_status);
            $changes_made = true;

            // Clear existing schedule
            wp_clear_scheduled_hook('axioned_update_yelp_reviews');
            Axioned_Reviews_Logger::log("Cleared Yelp cron schedule");

            // Only schedule if being enabled
            if ($yelp_new_status === '1') {
                $next_run = axioned_calculate_next_run($frequency, $time);
                if ($next_run) {
                    wp_schedule_event($next_run->getTimestamp(), $frequency, 'axioned_update_yelp_reviews');
                    Axioned_Reviews_Logger::log("Scheduled Yelp cron for: " . $next_run->format('Y-m-d H:i:s'));
                }
            }
        }

        ob_end_clean();

        if ($changes_made) {
            axioned_set_admin_notice('Cron settings updated successfully');
            wp_send_json_success('Cron settings updated successfully');
        } else {
            wp_send_json_error('No changes made');
        }

    } catch (Exception $e) {
        // Clean buffer before sending error
        if (ob_get_level()) {
            ob_end_clean();
        }
        // Set error notice
        axioned_set_admin_notice('Failed to update cron settings: ' . $e->getMessage(), 'error');
        wp_send_json_error('Failed to update cron settings: ' . $e->getMessage());
    }
}
add_action('wp_ajax_axioned_toggle_cron', 'axioned_handle_toggle_cron');

// Update the Run Now AJAX handler
function axioned_handle_run_cron_now() {
    // Clean any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Set JSON headers
    header('Content-Type: application/json');
    header('Cache-Control: no-cache');
    
    check_ajax_referer('axioned_run_cron_now', 'nonce');
    
    if (!current_user_can('manage_options')) {
        axioned_set_admin_notice('Unauthorized access', 'error');
        wp_send_json_error('Unauthorized access');
        return;
    }

    try {
        ob_start();
        $service = isset($_POST['service']) ? sanitize_text_field($_POST['service']) : '';
        
        if (!in_array($service, array('google', 'yelp'))) {
            wp_send_json_error('Invalid service');
            return;
        }

        if ($service === 'google') {
            if (get_option('axioned_google_cron_enabled', '0') === '1') {
                Axioned_Reviews_Logger::log("Manual execution of Google reviews update");
                axioned_fetch_google_reviews('manual');
                update_option('axioned_google_last_run', current_time('mysql'));
                axioned_set_admin_notice('Google reviews update completed successfully');
                ob_end_clean();
                wp_send_json_success('Google reviews update completed');
            } else {
                ob_end_clean();
                axioned_set_admin_notice('Google reviews cron is disabled', 'error');
                wp_send_json_error('Google reviews cron is disabled');
            }
        } else {
            if (get_option('axioned_yelp_cron_enabled', '0') === '1') {
                Axioned_Reviews_Logger::log("Manual execution of Yelp reviews update");
                axioned_fetch_yelp_reviews('manual');
                update_option('axioned_yelp_last_run', current_time('mysql'));
                axioned_set_admin_notice('Yelp reviews update completed successfully');
                ob_end_clean();
                wp_send_json_success('Yelp reviews update completed');
            } else {
                ob_end_clean();
                axioned_set_admin_notice('Yelp reviews cron is disabled', 'error');
                wp_send_json_error('Yelp reviews cron is disabled');
            }
        }
    } catch (Exception $e) {
        // Clean buffer before sending error
        if (ob_get_level()) {
            ob_end_clean();
        }
        axioned_set_admin_notice('Failed to run cron: ' . $e->getMessage(), 'error');
        wp_send_json_error($e->getMessage());
    }
}
add_action('wp_ajax_axioned_run_cron_now', 'axioned_handle_run_cron_now');

