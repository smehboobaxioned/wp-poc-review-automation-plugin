<?php
/**
 * Helper functions for the plugin
 */

// Helper function to reschedule cron jobs
function axioned_reschedule_cron_jobs($frequency, $time) {
    // Clear existing schedules
    wp_clear_scheduled_hook('axioned_google_reviews_cron');
    wp_clear_scheduled_hook('axioned_yelp_reviews_cron');

    // Parse time
    list($hours, $minutes) = explode(':', $time);
    
    // Calculate next run time in UTC
    $now = new DateTime('now', new DateTimeZone('UTC'));
    $next_run = new DateTime('now', new DateTimeZone('UTC'));
    $next_run->setTime($hours, $minutes, 0);

    if ($next_run <= $now) {
        // If the time has passed today, start from tomorrow
        $next_run->modify('+1 day');
    }

    // Calculate interval based on frequency
    switch ($frequency) {
        case 'weekly':
            // Set to next Monday if not already Monday
            while ($next_run->format('N') != 1) {
                $next_run->modify('+1 day');
            }
            $interval = WEEK_IN_SECONDS;
            break;
        case 'monthly':
            // Set to first day of next month if not already
            if ($next_run->format('j') != 1) {
                $next_run->modify('first day of next month');
            }
            $interval = 30 * DAY_IN_SECONDS;
            break;
        default: // daily
            $interval = DAY_IN_SECONDS;
    }

    // Schedule the cron jobs
    wp_schedule_event($next_run->getTimestamp(), $frequency, 'axioned_google_reviews_cron');
    wp_schedule_event($next_run->getTimestamp(), $frequency, 'axioned_yelp_reviews_cron');
}

/**
 * Get ACF fields as options for select dropdown
 * 
 * @param string $selected_option The currently selected option
 * @return string HTML options for select dropdown
 */
function axioned_get_acf_fields_options($selected_option) {
    $output = '<option value="">Select a field...</option>';
    
    // Return early if ACF is not active
    if (!function_exists('acf_get_field_groups')) {
        return $output;
    }

    // Get all ACF field groups
    $field_groups = acf_get_field_groups();
    
    if (!empty($field_groups)) {
        foreach ($field_groups as $field_group) {
            // Get fields for this group
            $fields = acf_get_fields($field_group['key']);
            
            if (!empty($fields)) {
                // Add group as optgroup
                $output .= '<optgroup label="' . esc_attr($field_group['title']) . '">';
                
                foreach ($fields as $field) {
                    // Only include number and text fields
                    if (in_array($field['type'], array('number', 'text'))) {
                        $selected = selected(get_option($selected_option), $field['key'], false);
                        $output .= sprintf(
                            '<option value="%s" %s>%s (%s)</option>',
                            esc_attr($field['key']),
                            $selected,
                            esc_html($field['label']),
                            esc_html($field['type'])
                        );
                    }
                }
                
                $output .= '</optgroup>';
            }
        }
    }

    return $output;
}

// Register settings for ACF mapping
add_action('admin_init', 'axioned_register_acf_mapping_settings');
function axioned_register_acf_mapping_settings() {
    register_setting('axioned_reviews_acf_mapping', 'axioned_google_rating_field');
    register_setting('axioned_reviews_acf_mapping', 'axioned_google_count_field');
    register_setting('axioned_reviews_acf_mapping', 'axioned_yelp_rating_field');
    register_setting('axioned_reviews_acf_mapping', 'axioned_yelp_count_field');
}

/**
 * Calculate the next run time for a cron job based on frequency and time
 * 
 * @param string $frequency The frequency (daily, weekly, monthly)
 * @param string $time The time in HH:mm format
 * @return DateTime|false Returns DateTime object of next run or false on error
 */
function axioned_calculate_next_run($frequency, $time) {
    try {
        // Parse time
        list($hours, $minutes) = explode(':', $time);
        
        // Calculate next run time in UTC
        $now = new DateTime('now', new DateTimeZone('UTC'));
        $next_run = new DateTime('now', new DateTimeZone('UTC'));
        $next_run->setTime($hours, $minutes, 0);

        // If the time has passed today, start from tomorrow
        if ($next_run <= $now) {
            $next_run->modify('+1 day');
        }

        // Adjust based on frequency
        switch ($frequency) {
            case 'weekly':
                // Set to next Monday if not already Monday
                while ($next_run->format('N') != 1) {
                    $next_run->modify('+1 day');
                }
                break;
                
            case 'monthly':
                // Set to first day of next month if not already
                if ($next_run->format('j') != 1) {
                    $next_run->modify('first day of next month');
                }
                break;
                
            case 'daily':
                // Already handled above
                break;
                
            default:
                Axioned_Reviews_Logger::log("Invalid frequency provided: " . $frequency);
                return false;
        }

        Axioned_Reviews_Logger::log("Calculated next run time: " . $next_run->format('Y-m-d H:i:s') . " UTC for frequency: " . $frequency);
        return $next_run;
        
    } catch (Exception $e) {
        Axioned_Reviews_Logger::log("Error calculating next run time: " . $e->getMessage());
        return false;
    }
}
