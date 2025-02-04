<?php
/**
 * Configuration Tab
 *
 * This file contains the code for the Configuration tab in the plugin settings.
 * It allows users to configure the API keys and business details for Google and Yelp.
 */

 function axioned_reviews_configuration_tab() {
    ?>
    <div class="configuration-container">
        <!-- Google Section -->
        <div class="config-section google-section">
            <div class="section-header">
                <div class="service-icon">
                    <svg viewBox="0 0 24 24" width="24" height="24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                </div>
                <h3>Google Reviews Configuration</h3>
            </div>
            <div class="section-content">
                <form method="post" action="options.php" class="google-settings-form">
                    <?php settings_fields('axioned_reviews_settings'); ?>
                    <!-- Preserve Yelp settings -->
                    <input type="hidden" name="axioned_yelp_api_key" value="<?php echo esc_attr(get_option('axioned_yelp_api_key')); ?>">
                    <input type="hidden" name="axioned_yelp_business_name" value="<?php echo esc_attr(get_option('axioned_yelp_business_name')); ?>">
                    <input type="hidden" name="axioned_yelp_location" value="<?php echo esc_attr(get_option('axioned_yelp_location')); ?>">
                    <!-- Preserve logging setting -->
                    <input type="hidden" name="axioned_enable_logging" value="<?php echo esc_attr(get_option('axioned_enable_logging')); ?>">
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">API Key</th>
                            <td>
                                <input type="password" 
                                       name="axioned_google_api_key" 
                                       value="<?php echo esc_attr(get_option('axioned_google_api_key')); ?>" 
                                       class="regular-text"
                                       autocomplete="off">
                                <button type="button" class="button toggle-password">Show</button>
                                <p class="description">Enter your Google Places API key</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Place ID</th>
                            <td>
                                <input type="text" 
                                       name="axioned_google_place_id" 
                                       value="<?php echo esc_attr(get_option('axioned_google_place_id')); ?>" 
                                       class="regular-text">
                                <p class="description">Enter your Google Place ID. <a href="https://developers.google.com/maps/documentation/places/web-service/place-id" target="_blank">How to find your Place ID?</a></p>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button('Save Google Settings', 'primary', 'submit-google'); ?>
                </form>
            </div>
        </div>

        <!-- Yelp Section -->
        <div class="config-section yelp-section">
            <div class="section-header">
                <div class="service-icon">
                <svg viewBox="0 0 228.097 228.097" width="24" height="24">
                            <g>
                                <path fill="#C1272D" d="M173.22,68.06c8.204,6.784,30.709,25.392,27.042,38.455c-1.696,5.867-8.434,7.746-13.43,9.579
                                c-11.505,4.171-23.33,7.471-35.339,9.9c-9.717,1.971-30.48,6.279-26.63-10.909c1.512-6.646,6.875-12.284,11.184-17.28
                                c8.846-10.404,17.876-21.405,28.555-29.93c0.871-0.688,1.925-0.871,2.842-0.733C169.232,66.41,171.386,66.502,173.22,68.06z"/>
                                <path fill="#C1272D" d="M161.119,205.197c-7.196-5.821-12.284-14.942-16.684-22.917c-4.309-7.7-11.092-17.876-12.238-26.813
                                c-2.337-18.38,24.292-7.333,31.947-4.675c10.175,3.575,37.447,7.517,34.422,23.421c-2.521,12.971-18.151,28.784-31.213,30.801
                                c-0.137,0.046-0.321,0-0.504,0c-0.046,0.046-0.092,0.092-0.137,0.137c-0.367,0.183-0.779,0.413-1.192,0.596
                                C163.961,206.573,162.449,206.252,161.119,205.197z"/>
                                <path fill="#C1272D" d="M101.58,157.896c14.484-6.004,15.813,10.175,15.721,19.984c-0.137,11.688-0.504,23.421-1.375,35.063
                                c-0.321,4.721-0.137,10.405-4.629,13.384c-5.546,3.667-16.225,0.779-21.955-1.008c-0.183-0.092-0.367-0.183-0.55-0.229
                                c-12.054-2.108-26.767-7.654-28.188-18.792c-0.138-1.283,0.367-2.429,1.146-3.3c0.367-0.688,0.733-1.329,1.146-1.925
                                c1.788-2.475,3.85-4.675,5.913-6.921c3.483-5.179,7.242-10.175,11.229-14.988C85.813,172.197,92.917,161.471,101.58,157.896z"/>
                                <path fill="#C1272D" d="M103.689,107.661c-21.13-17.371-41.71-44.276-52.344-69.164c-8.113-18.93,12.513-30.48,28.417-35.705
                                c21.451-7.059,29.976-0.917,32.13,20.534c1.788,18.471,2.613,37.08,2.475,55.643c-0.046,7.838,2.154,20.488-2.429,27.547
                                c0.733,2.888-3.621,4.95-6.096,2.979c-0.367-0.275-0.733-0.642-1.146-0.963C104.33,108.303,104.009,108.028,103.689,107.661z"/>
                                <path fill="#C1272D" d="M101.397,134.566c1.696,7.517-3.621,10.542-9.854,13.384c-11.092,4.996-22.734,8.984-34.422,12.284
                                c-6.784,1.879-17.188,6.371-23.742,1.375c-4.95-3.758-5.271-11.596-5.729-17.28c-1.008-12.696,0.917-42.993,18.517-44.276
                                c8.617-0.596,19.388,7.104,26.447,11.138c9.396,5.409,19.48,11.596,26.492,20.076C100.159,131.862,101.03,132.916,101.397,134.566z"/>
                            </g>
                        </svg>
                </div>
                <h3>Yelp Reviews Configuration</h3>
            </div>
            <div class="section-content">
                <form method="post" action="options.php" class="yelp-settings-form">
                    <?php settings_fields('axioned_reviews_settings'); ?>
                    <!-- Preserve Google settings -->
                    <input type="hidden" name="axioned_google_api_key" value="<?php echo esc_attr(get_option('axioned_google_api_key')); ?>">
                    <input type="hidden" name="axioned_google_place_id" value="<?php echo esc_attr(get_option('axioned_google_place_id')); ?>">
                    <!-- Preserve logging setting -->
                    <input type="hidden" name="axioned_enable_logging" value="<?php echo esc_attr(get_option('axioned_enable_logging')); ?>">
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">API Key</th>
                            <td>
                                <input type="password" 
                                       name="axioned_yelp_api_key" 
                                       value="<?php echo esc_attr(get_option('axioned_yelp_api_key')); ?>" 
                                       class="regular-text"
                                       autocomplete="off">
                                <button type="button" class="button toggle-password">Show</button>
                                <p class="description">Enter your Yelp API key</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Business Name</th>
                            <td>
                                <input type="text" 
                                       name="axioned_yelp_business_name" 
                                       value="<?php echo esc_attr(get_option('axioned_yelp_business_name')); ?>" 
                                       class="regular-text">
                                <p class="description">Enter your business name exactly as it appears on Yelp</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Location</th>
                            <td>
                                <input type="text" 
                                       name="axioned_yelp_location" 
                                       value="<?php echo esc_attr(get_option('axioned_yelp_location')); ?>" 
                                       class="regular-text">
                                <p class="description">Enter your business location (e.g., "New York, NY")</p>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button('Save Yelp Settings', 'primary', 'submit-yelp'); ?>
                </form>
            </div>
        </div>

        <!-- Debug Logging Option -->
        <div class="config-section logging-section">
            <div class="section-content">
                <form method="post" action="options.php" class="logging-settings-form">
                    <?php settings_fields('axioned_reviews_settings'); ?>
                    <!-- Preserve Google settings -->
                    <input type="hidden" name="axioned_google_api_key" value="<?php echo esc_attr(get_option('axioned_google_api_key')); ?>">
                    <input type="hidden" name="axioned_google_place_id" value="<?php echo esc_attr(get_option('axioned_google_place_id')); ?>">
                    <!-- Preserve Yelp settings -->
                    <input type="hidden" name="axioned_yelp_api_key" value="<?php echo esc_attr(get_option('axioned_yelp_api_key')); ?>">
                    <input type="hidden" name="axioned_yelp_business_name" value="<?php echo esc_attr(get_option('axioned_yelp_business_name')); ?>">
                    <input type="hidden" name="axioned_yelp_location" value="<?php echo esc_attr(get_option('axioned_yelp_location')); ?>">
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">Debug Logging</th>
                            <td>
                                <label>
                                    <input type="checkbox" 
                                           name="axioned_enable_logging" 
                                           value="1" 
                                           <?php checked(get_option('axioned_enable_logging'), '1'); ?>>
                                    Enable debug logging
                                </label>
                                <p class="description">Enable this option to log plugin activities for debugging purposes</p>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button('Save Logging Settings', 'secondary', 'submit-logging'); ?>
                </form>
            </div>
        </div>
    </div>

    <style>
    .configuration-container {
        max-width: 1200px;
        margin: 20px 0;
    }

    .config-section {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        transition: all 0.3s ease;
    }

    .config-section:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .section-header {
        display: flex;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #e2e4e7;
        background: #f8f9fa;
        border-radius: 8px 8px 0 0;
    }

    .service-icon {
        margin-right: 15px;
        padding: 10px;
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .section-header h3 {
        margin: 0;
        color: #1d2327;
        font-size: 1.3em;
    }

    .section-content {
        padding: 20px;
    }

    .form-table th {
        width: 200px;
        padding: 20px 10px 20px 0;
    }

    .form-table td {
        padding: 15px 10px;
    }

    .regular-text {
        width: 100%;
        max-width: 400px;
    }

    .description {
        margin-top: 8px;
        color: #666;
    }

    .toggle-password {
        margin-left: 10px;
    }

    /* Responsive Design */
    @media screen and (max-width: 782px) {
        .form-table th {
            width: 100%;
            display: block;
            padding-bottom: 0;
        }
        
        .form-table td {
            display: block;
            padding-top: 8px;
        }
    }

    .google-settings-form,
    .yelp-settings-form,
    .logging-settings-form {
        margin: 0;
        padding: 0;
    }

    .submit {
        margin-top: 20px;
        padding: 0;
    }
    </style>

    <script>
    jQuery(document).ready(function($) {
        $('.toggle-password').click(function() {
            const input = $(this).prev('input');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                $(this).text('Hide');
            } else {
                input.attr('type', 'password');
                $(this).text('Show');
            }
        });
    });
    </script>
    <?php
}
