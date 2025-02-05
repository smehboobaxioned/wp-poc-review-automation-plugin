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
                        <svg width="32" height="32" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
                            <circle cx="512" cy="512" r="512" style="fill:#0ecad4"/>
                            <path d="M443.4 723.7h135.9v-103l-32.9-32.9h-70.1l-32.9 32.9v103zm175.9-278.9-32.9 32.9v71.5l32.9 32.9h103V446.2h-103v-1.4zm-40.1-144.5H443.4v103l32.9 32.9h70.1l32.9-32.9v-103zm144.5 423.4v-103l-32.9-32.9h-103v135.9h135.9zM333.2 300.3l-32.9 32.9v103h135.9V300.3h-103zm254.6 0v103l32.9 32.9h103V300.3H587.8zM512 532c-10 0-18.6-8.6-18.6-18.6s8.6-18.6 18.6-18.6c10 0 18.6 8.6 18.6 18.6S522 532 512 532zm-75.8-87.2H300.3v135.9h103l32.9-32.9v-103zm0 175.9-32.9-32.9h-103v135.9h103l32.9-32.9v-70.1z" style="fill:#fff"/>
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
                        <p class="description radio-description">Clear WP Engine cache after review updates</p>
                        
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
                        <svg width="32" height="32" xmlns="http://www.w3.org/2000/svg" aria-label="Cloudflare" role="img" viewBox="0 0 512 512">
                            <rect width="512" height="512" rx="15%" fill="#ffffff"/>
                            <path fill="#f38020" d="M331 326c11-26-4-38-19-38l-148-2c-4 0-4-6 1-7l150-2c17-1 37-15 43-33 0 0 10-21 9-24a97 97 0 0 0-187-11c-38-25-78 9-69 46-48 3-65 46-60 72 0 1 1 2 3 2h274c1 0 3-1 3-3z"/>
                            <path fill="#faae40" d="M381 224c-4 0-6-1-7 1l-5 21c-5 16 3 30 20 31l32 2c4 0 4 6-1 7l-33 1c-36 4-46 39-46 39 0 2 0 3 2 3h113l3-2a81 81 0 0 0-78-103"/>
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
                        <p class="description radio-description">Clear Cloudflare cache after review updates</p>

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
    p.description.radio-description {
        display: inline-block;
        vertical-align: middle;
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