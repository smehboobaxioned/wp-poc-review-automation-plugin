<?php
/**
 * Admin Class
 *
 * This file contains the code for the admin menu and settings for the plugin.
 */

// Create admin menu for settings
function axioned_reviews_admin_menu() {
    add_options_page(
        'Axioned Reviews Settings',
        'Axioned Reviews',
        'manage_options',
        'axioned-reviews-settings',
        'axioned_reviews_settings_page'
    );
}
add_action('admin_menu', 'axioned_reviews_admin_menu');

// Register settings
function axioned_reviews_register_settings() {
    // Configuration tab settings
    register_setting('axioned_reviews_settings', 'axioned_google_api_key');
    register_setting('axioned_reviews_settings', 'axioned_google_place_id');
    register_setting('axioned_reviews_settings', 'axioned_yelp_api_key');
    register_setting('axioned_reviews_settings', 'axioned_yelp_business_name');
    register_setting('axioned_reviews_settings', 'axioned_yelp_location');
    register_setting('axioned_reviews_settings', 'axioned_enable_logging');

    // ACF Mapping tab settings
    register_setting('axioned_reviews_acf_settings', 'axioned_google_rating_field');
    register_setting('axioned_reviews_acf_settings', 'axioned_google_count_field');
    register_setting('axioned_reviews_acf_settings', 'axioned_yelp_rating_field');
    register_setting('axioned_reviews_acf_settings', 'axioned_yelp_count_field');

    // Cron settings
    register_setting('axioned_reviews_cron_settings', 'axioned_reviews_frequency');
    register_setting('axioned_reviews_cron_settings', 'axioned_reviews_time');
    register_setting('axioned_reviews_cron_settings', 'axioned_google_cron_enabled');
    register_setting('axioned_reviews_cron_settings', 'axioned_yelp_cron_enabled');
}
add_action('admin_init', 'axioned_reviews_register_settings');

// Add settings sections
function axioned_reviews_settings_init() {
    // Configuration section
    add_settings_section(
        'axioned_reviews_settings_section',
        'API Configuration',
        function() {
            echo '<p>Enter your API keys and business IDs below:</p>';
        },
        'axioned_reviews_settings'
    );

    // ACF Mapping section
    add_settings_section(
        'axioned_reviews_acf_section',
        'ACF Field Mapping',
        function() {
            echo '<p>Map your ACF fields to review data:</p>';
        },
        'axioned_reviews_acf_settings'
    );

    // Cron section
    add_settings_section(
        'axioned_reviews_cron_section',
        'Cron Settings',
        function() {
            echo '<p>Configure the review update schedule:</p>';
        },
        'axioned_reviews_cron_settings'
    );
}
add_action('admin_init', 'axioned_reviews_settings_init');

// Add settings page callback function
function axioned_reviews_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Get current tab
    $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'configuration';
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <nav class="nav-tab-wrapper">
            <a href="?page=axioned-reviews-settings&tab=configuration" 
               class="nav-tab <?php echo $current_tab === 'configuration' ? 'nav-tab-active' : ''; ?>">
                Configuration
            </a>
            <a href="?page=axioned-reviews-settings&tab=acf-mapping" 
               class="nav-tab <?php echo $current_tab === 'acf-mapping' ? 'nav-tab-active' : ''; ?>">
                ACF Mapping
            </a>
            <a href="?page=axioned-reviews-settings&tab=cron-status" 
               class="nav-tab <?php echo $current_tab === 'cron-status' ? 'nav-tab-active' : ''; ?>">
                Cron Status
            </a>
            <a href="?page=axioned-reviews-settings&tab=debug" 
               class="nav-tab <?php echo $current_tab === 'debug' ? 'nav-tab-active' : ''; ?>">
                Debug
            </a>
            <a href="?page=axioned-reviews-settings&tab=logs" 
               class="nav-tab <?php echo $current_tab === 'logs' ? 'nav-tab-active' : ''; ?>">
                Log
            </a>
        </nav>

        <div class="tab-content">
            <?php
            switch ($current_tab) {
                case 'configuration':
                    axioned_reviews_configuration_tab();
                    break;
                case 'acf-mapping':
                    axioned_reviews_acf_mapping_tab();
                    break;
                case 'cron-status':
                    axioned_reviews_cron_status_tab();
                    break;
                case 'debug':
                    axioned_reviews_debug_tab();
                    break;
                case 'logs':
                    axioned_reviews_logs_tab();
                    break;
            }
            ?>
        </div>
    </div>
    <?php
}

// Store admin notices in transients
function axioned_set_admin_notice($message, $type = 'success') {
    set_transient('axioned_admin_notice', array(
        'message' => $message,
        'type' => $type
    ), 45);
}

// Display admin notices
function axioned_display_admin_notices() {
    $notice = get_transient('axioned_admin_notice');
    if ($notice) {
        ?>
        <div class="notice notice-<?php echo esc_attr($notice['type']); ?> is-dismissible">
            <p><?php echo esc_html($notice['message']); ?></p>
        </div>
        <?php
        delete_transient('axioned_admin_notice');
    }
}
add_action('admin_notices', 'axioned_display_admin_notices');
