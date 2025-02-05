<?php
/**
 * Cache Management Tab
 *
 * Handles cache clearing for various hosting providers and CDNs
 */

function axioned_reviews_cache_tab() {
    ?>
    <div class="cache-container">
        <h2>Cache Management</h2>
        
        <!-- Cache Providers Section -->
        <div class="cache-section">
            <form method="post" action="options.php">
                <?php 
                settings_fields('axioned_reviews_cache_settings');
                ?>
                
                <!-- WP Engine Section -->
                <div class="provider-card">
                    <div class="provider-header">
                        <svg width="32" height="32" viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#40BAC8" d="M250 0C111.929 0 0 111.929 0 250s111.929 250 250 250 250-111.929 250-250S388.071 0 250 0zm146.825 321.432c-4.076 7.857-10.764 15.715-17.452 21.236-6.688 5.522-13.376 9.599-21.233 13.376-7.857 3.778-15.715 6.687-23.572 8.927-7.857 2.24-15.715 3.778-23.572 4.374-7.857.597-15.118.895-21.806.895-17.452 0-34.307-2.239-50.467-6.687-16.159-4.449-30.856-10.764-44.232-19.094-13.377-8.33-25.214-18.347-35.799-30.26-10.585-11.912-19.591-25.214-26.875-40.156-7.284-14.941-12.807-31.204-16.584-48.656-3.777-17.452-5.719-36.097-5.719-56.186 0-19.591 1.942-37.94 5.719-54.99 3.777-17.049 9.3-32.767 16.584-47.112 7.284-14.346 16.29-27.124 26.875-38.438 10.585-11.315 22.422-20.935 35.799-28.792 13.376-7.857 28.073-13.973 44.232-18.347 16.16-4.374 33.015-6.538 50.467-6.538 7.284 0 14.942.298 23.274.895 8.33.596 16.585 1.643 25.214 3.181 8.628 1.538 17.049 3.777 25.214 6.687 8.33 2.911 16.16 6.39 23.572 10.466l-24.765 47.112c-11.912-7.284-24.467-12.48-37.343-15.715-12.877-3.181-25.812-4.822-38.736-4.822-11.316 0-22.124 1.344-32.365 4.076-10.242 2.687-19.293 6.687-27.472 11.912-8.18 5.224-15.416 11.613-21.806 19.093-6.389 7.48-11.912 15.864-16.584 25.214-4.672 9.3-8.18 19.591-10.764 30.856-2.538 11.316-3.777 23.274-3.777 36.097 0 13.377 1.24 25.812 3.777 37.343 2.583 11.464 6.092 21.755 10.764 30.856 4.672 9.102 10.195 17.452 16.584 24.765 6.39 7.36 13.675 13.526 21.806 18.497 8.18 4.971 17.23 8.777 27.472 11.315 10.241 2.583 21.05 3.777 32.365 3.777 13.526 0 26.577-1.643 39.334-4.971 12.757-3.33 25.513-8.33 38.14-15.118l24.765 47.112z"/>
                        </svg>
                        <h3>WP Engine Cache</h3>
                    </div>
                    <div class="provider-content">
                        <label class="switch">
                            <input type="checkbox" 
                                   name="axioned_clear_wpengine_cache" 
                                   value="1" 
                                   <?php checked(get_option('axioned_clear_wpengine_cache'), '1'); ?>>
                            <span class="slider round"></span>
                        </label>
                        <p class="description">Clear WP Engine cache after review updates</p>
                        
                        <?php if (get_option('axioned_clear_wpengine_cache') === '1'): ?>
                            <button type="button" class="button test-cache" data-provider="wpengine">
                                Test WP Engine Cache Clear
                            </button>
                            <div class="cache-test-result"></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Cloudflare Section -->
                <div class="provider-card">
                    <div class="provider-header">
                        <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#F6821F" d="M8.16 23.96c.171.34.037.756-.303.927l-1.725.869c-.34.171-.756.037-.927-.303C3.767 22.829 3 19.999 3 17c0-7.18 5.82-13 13-13 5.595 0 10.366 3.534 12.207 8.49.134.363-.052.766-.415.9l-1.85.677c-.363.133-.766-.052-.9-.415C23.635 9.669 20.047 7 16 7c-5.523 0-10 4.477-10 10 0 2.252.744 4.33 2.001 6.003.171.34.037.756-.303.927zM23.84 8.04c-.171-.34-.037-.756.303-.927l1.725-.869c.34-.171.756-.037.927.303C28.233 9.171 29 12.001 29 15c0 7.18-5.82 13-13 13-5.595 0-10.366-3.534-12.207-8.49-.134-.363.052-.766.415-.9l1.85-.677c.363-.133.766.052.9.415C8.365 22.331 11.953 25 16 25c5.523 0 10-4.477 10-10 0-2.252-.744-4.33-2.001-6.003-.171-.34-.037-.756.303-.927z"/>
                        </svg>
                        <h3>Cloudflare Cache</h3>
                    </div>
                    <div class="provider-content">
                        <?php 
                        $cloudflare_plugin_exists = class_exists('\CF\WordPress\Hooks');
                        $cloudflare_plugin_active = $cloudflare_plugin_exists && is_plugin_active('cloudflare/cloudflare.php');
                        
                        if ($cloudflare_plugin_exists): 
                            if ($cloudflare_plugin_active): ?>
                                <div class="notice notice-success inline">
                                    <p>✓ Cloudflare plugin is active and configured!</p>
                                </div>
                            <?php else: ?>
                                <div class="notice notice-warning inline">
                                    <p>⚠️ Cloudflare plugin is installed but not activated. <a href="<?php echo admin_url('plugins.php'); ?>">Activate it here</a>.</p>
                                </div>
                            <?php endif;
                        endif; ?>

                        <label class="switch">
                            <input type="checkbox" 
                                   name="axioned_clear_cloudflare_cache" 
                                   value="1" 
                                   <?php checked(get_option('axioned_clear_cloudflare_cache'), '1'); ?>>
                            <span class="slider round"></span>
                        </label>
                        <p class="description">Clear Cloudflare cache after review updates</p>

                        <div id="cloudflare-settings" style="display: <?php echo get_option('axioned_clear_cloudflare_cache') === '1' ? 'block' : 'none'; ?>">
                            <table class="form-table">
                                <tr>
                                    <th scope="row">API Token</th>
                                    <td>
                                        <input type="password" 
                                               name="axioned_cloudflare_api_token" 
                                               value="<?php echo esc_attr(get_option('axioned_cloudflare_api_token')); ?>" 
                                               class="regular-text"
                                               <?php echo $cloudflare_plugin_active ? 'disabled' : ''; ?>>
                                        <button type="button" class="button toggle-password" <?php echo $cloudflare_plugin_active ? 'disabled' : ''; ?>>
                                            Show
                                        </button>
                                        <?php if ($cloudflare_plugin_active): ?>
                                            <p class="description">Using Cloudflare plugin configuration</p>
                                        <?php else: ?>
                                            <p class="description">Required for direct API access</p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Zone ID</th>
                                    <td>
                                        <input type="text" 
                                               name="axioned_cloudflare_zone_id" 
                                               value="<?php echo esc_attr(get_option('axioned_cloudflare_zone_id')); ?>" 
                                               class="regular-text"
                                               <?php echo $cloudflare_plugin_active ? 'disabled' : ''; ?>>
                                        <?php if ($cloudflare_plugin_active): ?>
                                            <p class="description">Using Cloudflare plugin configuration</p>
                                        <?php else: ?>
                                            <p class="description">Required for direct API access</p>
                                            <p class="description"><a href="https://developers.cloudflare.com/fundamentals/setup/find-account-and-zone-ids/" target="_blank">How to find your Zone ID?</a></p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                            <?php if ($cloudflare_plugin_active): ?>
                                <div class="notice notice-info inline" style="margin-top: 10px;">
                                    <p>These settings are managed by the Cloudflare plugin. To modify them, please use the <a href="<?php echo admin_url('options-general.php?page=cloudflare'); ?>">Cloudflare plugin settings</a>.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (get_option('axioned_clear_cloudflare_cache') === '1'): ?>
                            <button type="button" class="button test-cache" data-provider="cloudflare">
                                Test Cloudflare Cache Clear
                            </button>
                            <div class="cache-test-result"></div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php submit_button('Save Cache Settings'); ?>
            </form>

            <!-- Test All Cache Clearing -->
            <?php 
            $wpengine_enabled = get_option('axioned_clear_wpengine_cache') === '1';
            $cloudflare_enabled = get_option('axioned_clear_cloudflare_cache') === '1';

            if ($wpengine_enabled || $cloudflare_enabled): 
            ?>
                <div class="cache-testing">
                    <h3>Test All Cache Providers</h3>
                    <button class="button button-primary test-all-cache">
                        Clear <?php echo ($wpengine_enabled && $cloudflare_enabled) ? 'All' : ($wpengine_enabled ? 'WP Engine' : 'Cloudflare'); ?> Cache
                    </button>
                    <div id="cache-test-result" style="display: none;"></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
    .provider-card {
        background: #fff;
        border: 1px solid #ccd0d4;
        border-radius: 4px;
        margin-bottom: 20px;
        padding: 20px;
    }
    .provider-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    .provider-header svg {
        margin-right: 10px;
    }
    .provider-header h3 {
        margin: 0;
    }
    .provider-content {
        padding-left: 42px;
    }
    .cache-test-result {
        margin-top: 10px;
        padding: 10px;
        border-radius: 4px;
        display: none;
    }
    .cache-test-result.success {
        background: #f0f6e9;
        border: 1px solid #7ad03a;
    }
    .cache-test-result.error {
        background: #fcf0f1;
        border: 1px solid #d63638;
    }
    </style>

    <script>
    jQuery(document).ready(function($) {
        // Toggle Cloudflare settings and test button visibility
        $('input[name="axioned_clear_cloudflare_cache"]').change(function() {
            const isEnabled = $(this).prop('checked');
            $('#cloudflare-settings').slideToggle(isEnabled);
            
            // Toggle test button
            const $testButton = $(this).closest('.provider-content').find('.test-cache');
            const $testResult = $(this).closest('.provider-content').find('.cache-test-result');
            
            if (isEnabled) {
                if ($testButton.length === 0) {
                    const $buttonHtml = $('<button type="button" class="button test-cache" data-provider="cloudflare">Test Cloudflare Cache Clear</button>' +
                                        '<div class="cache-test-result"></div>');
                    $(this).closest('.provider-content').append($buttonHtml);
                } else {
                    $testButton.show();
                    $testResult.hide();
                }
            } else {
                $testButton.hide();
                $testResult.hide();
            }
            
            // Update "Clear All" section visibility
            updateClearAllSection();
        });

        // Similar for WP Engine
        $('input[name="axioned_clear_wpengine_cache"]').change(function() {
            const isEnabled = $(this).prop('checked');
            
            // Toggle test button
            const $testButton = $(this).closest('.provider-content').find('.test-cache');
            const $testResult = $(this).closest('.provider-content').find('.cache-test-result');
            
            if (isEnabled) {
                if ($testButton.length === 0) {
                    const $buttonHtml = $('<button type="button" class="button test-cache" data-provider="wpengine">Test WP Engine Cache Clear</button>' +
                                        '<div class="cache-test-result"></div>');
                    $(this).closest('.provider-content').append($buttonHtml);
                } else {
                    $testButton.show();
                    $testResult.hide();
                }
            } else {
                $testButton.hide();
                $testResult.hide();
            }
            
            // Update "Clear All" section visibility
            updateClearAllSection();
        });

        // Function to update "Clear All" section visibility
        function updateClearAllSection() {
            const $clearAllSection = $('.cache-testing');
            const wpengineEnabled = $('input[name="axioned_clear_wpengine_cache"]').prop('checked');
            const cloudflareEnabled = $('input[name="axioned_clear_cloudflare_cache"]').prop('checked');
            
            if (wpengineEnabled || cloudflareEnabled) {
                if ($clearAllSection.length === 0) {
                    const $sectionHtml = $('<div class="cache-testing">' +
                                         '<h3>Test All Cache Providers</h3>' +
                                         '<button class="button button-primary test-all-cache">Clear All Configured Caches</button>' +
                                         '<div id="cache-test-result" style="display: none;"></div>' +
                                         '</div>');
                    $('form').after($sectionHtml);
                } else {
                    $clearAllSection.show();
                }
                
                // Update button text
                const buttonText = (wpengineEnabled && cloudflareEnabled) 
                    ? 'Clear All Configured Caches'
                    : 'Clear ' + (wpengineEnabled ? 'WP Engine' : 'Cloudflare') + ' Cache';
                $('.test-all-cache').text(buttonText);
            } else {
                $clearAllSection.hide();
            }
        }

        // Toggle password visibility
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

        // Test individual cache provider
        $('.test-cache').click(function() {
            const $button = $(this);
            const $result = $button.siblings('.cache-test-result');
            const provider = $button.data('provider');
            
            $button.prop('disabled', true).text('Clearing...');
            $result.hide();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'axioned_test_cache_clear',
                    nonce: '<?php echo wp_create_nonce("axioned_test_cache"); ?>',
                    provider: provider
                },
                success: function(response) {
                    if (response.success && response.data && response.data.message) {
                        $result.removeClass('error').addClass('success')
                               .html('✓ ' + response.data.message)
                               .slideDown();
                    } else {
                        const errorMsg = response.data && response.data.message 
                            ? response.data.message 
                            : 'Failed to clear cache';
                        $result.removeClass('success').addClass('error')
                               .html('✗ ' + errorMsg)
                               .slideDown();
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to clear cache';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.data && response.data.message) {
                            errorMsg = response.data.message;
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                    $result.removeClass('success').addClass('error')
                           .html('✗ ' + errorMsg)
                           .slideDown();
                },
                complete: function() {
                    $button.prop('disabled', false)
                          .text('Test ' + (provider === 'wpengine' ? 'WP Engine' : 'Cloudflare') + ' Cache Clear');
                }
            });
        });

        // Test all cache providers
        $('.test-all-cache').click(function() {
            const $button = $(this);
            const $result = $('#cache-test-result');
            
            $button.prop('disabled', true).text('Clearing All Caches...');
            $result.hide();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'axioned_test_cache_clear',
                    nonce: '<?php echo wp_create_nonce("axioned_test_cache"); ?>',
                    provider: 'all'
                },
                success: function(response) {
                    if (response.success && response.data && response.data.message) {
                        $result.removeClass('error').addClass('success')
                               .html('✓ ' + response.data.message)
                               .slideDown();
                    } else {
                        const errorMsg = response.data && response.data.message 
                            ? response.data.message 
                            : 'Failed to clear caches';
                        $result.removeClass('success').addClass('error')
                               .html('✗ ' + errorMsg)
                               .slideDown();
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to clear caches';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.data && response.data.message) {
                            errorMsg = response.data.message;
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                    $result.removeClass('success').addClass('error')
                           .html('✗ ' + errorMsg)
                           .slideDown();
                },
                complete: function() {
                    $button.prop('disabled', false).text('Clear All Configured Caches');
                }
            });
        });
    });
    </script>
    <?php
} 