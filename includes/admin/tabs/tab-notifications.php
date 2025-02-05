<?php
/**
 * Notifications Tab
 *
 * This file contains the code for the Notifications tab in the plugin settings.
 * It allows users to configure email and Slack notifications for review updates.
 */

function axioned_reviews_notifications_tab() {
    ?>
    <div class="notifications-container">
        <!-- Email Notifications Section -->
        <div class="notification-section">
            <div class="section-header">
                <div class="service-icon">
                    <span class="dashicons dashicons-email-alt"></span>
                </div>
                <h3>Email Notifications</h3>
            </div>
            <div class="section-content">
                <form method="post" action="options.php" class="email-settings-form">
                    <?php 
                    settings_fields('axioned_reviews_email_notifications');
                    ?>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">Enable Email Notifications</th>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" 
                                           name="axioned_email_notifications_enabled" 
                                           value="1" 
                                           <?php checked(get_option('axioned_email_notifications_enabled'), '1'); ?>>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Recipient Email(s)</th>
                            <td>
                                <input type="text" 
                                       name="axioned_notification_emails" 
                                       id="notification-emails"
                                       value="<?php echo esc_attr(get_option('axioned_notification_emails')); ?>" 
                                       class="regular-text"
                                       pattern="^(\s*[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}\s*,?\s*)*$"
                                       title="Enter valid email addresses separated by commas">
                                <p class="description">Enter email addresses separated by commas</p>
                                <div class="email-validation-message" style="display: none; color: #d63638; margin-top: 5px;"></div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">From Name</th>
                            <td>
                                <input type="text" 
                                       name="axioned_notification_from_name" 
                                       value="<?php echo esc_attr(get_option('axioned_notification_from_name', get_bloginfo('name'))); ?>" 
                                       class="regular-text">
                                <p class="description">Name that will appear in the From field (default: site name)</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">From Email</th>
                            <td>
                                <input type="email" 
                                       name="axioned_notification_from_email" 
                                       value="<?php echo esc_attr(get_option('axioned_notification_from_email', get_bloginfo('admin_email'))); ?>" 
                                       class="regular-text"
                                       required>
                                <p class="description">Email address that will appear in the From field (default: admin email)</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Notification Frequency</th>
                            <td>
                                <select name="axioned_email_notification_frequency">
                                    <option value="immediately" <?php selected(get_option('axioned_email_notification_frequency'), 'immediately'); ?>>
                                        Immediately on update
                                    </option>
                                    <option value="daily" <?php selected(get_option('axioned_email_notification_frequency'), 'daily'); ?>>
                                        Daily Summary
                                    </option>
                                    <option value="weekly" <?php selected(get_option('axioned_email_notification_frequency'), 'weekly'); ?>>
                                        Weekly Summary
                                    </option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button('Save Email Settings'); ?>
                </form>
            </div>
        </div>

        <!-- Slack Notifications Section -->
        <div class="notification-section">
            <div class="section-header">
                <div class="service-icon">
                    <svg width="24" height="24" viewBox="0 0 54 54" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#36C5F0" d="M19.712.133a5.381 5.381 0 0 0-5.376 5.387 5.381 5.381 0 0 0 5.376 5.386h5.376V5.52A5.381 5.381 0 0 0 19.712.133m0 14.365H5.376A5.381 5.381 0 0 0 0 19.884a5.381 5.381 0 0 0 5.376 5.387h14.336a5.381 5.381 0 0 0 5.376-5.387 5.381 5.381 0 0 0-5.376-5.386"/>
                        <path fill="#2EB67D" d="M53.76 19.884a5.381 5.381 0 0 0-5.376-5.386 5.381 5.381 0 0 0-5.376 5.386v5.387h5.376a5.381 5.381 0 0 0 5.376-5.387m-14.336 0V5.52A5.381 5.381 0 0 0 34.048.133a5.381 5.381 0 0 0-5.376 5.387v14.364a5.381 5.381 0 0 0 5.376 5.387 5.381 5.381 0 0 0 5.376-5.387"/>
                        <path fill="#ECB22E" d="M34.048 54a5.381 5.381 0 0 0 5.376-5.387 5.381 5.381 0 0 0-5.376-5.386h-5.376v5.386A5.381 5.381 0 0 0 34.048 54m0-14.365h14.336a5.381 5.381 0 0 0 5.376-5.386 5.381 5.381 0 0 0-5.376-5.387H34.048a5.381 5.381 0 0 0-5.376 5.387 5.381 5.381 0 0 0 5.376 5.386"/>
                        <path fill="#E01E5A" d="M0 34.249a5.381 5.381 0 0 0 5.376 5.386 5.381 5.381 0 0 0 5.376-5.386v-5.387H5.376A5.381 5.381 0 0 0 0 34.25m14.336-.001v14.364A5.381 5.381 0 0 0 19.712 54a5.381 5.381 0 0 0 5.376-5.387V34.25a5.381 5.381 0 0 0-5.376-5.387 5.381 5.381 0 0 0-5.376 5.387"/>
                    </svg>
                </div>
                <h3>Slack Notifications</h3>
            </div>
            <div class="section-content">
                <form method="post" action="options.php" class="slack-settings-form">
                    <?php 
                    settings_fields('axioned_reviews_slack_notifications');
                    ?>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">Enable Slack Notifications</th>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" 
                                           name="axioned_slack_notifications_enabled" 
                                           value="1" 
                                           <?php checked(get_option('axioned_slack_notifications_enabled'), '1'); ?>>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Webhook URL</th>
                            <td>
                                <input type="password" 
                                       name="axioned_slack_webhook_url" 
                                       value="<?php echo esc_attr(get_option('axioned_slack_webhook_url')); ?>" 
                                       class="regular-text"
                                       autocomplete="off">
                                <button type="button" class="button toggle-password">Show</button>
                                <p class="description">Enter your Slack Webhook URL</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Channel</th>
                            <td>
                                <input type="text" 
                                       name="axioned_slack_channel" 
                                       value="<?php echo esc_attr(get_option('axioned_slack_channel')); ?>" 
                                       class="regular-text"
                                       placeholder="#reviews">
                                <p class="description">Enter the Slack channel (e.g., #reviews)</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Test Connection</th>
                            <td>
                                <button type="button" class="button test-slack">
                                    Send Test Message
                                </button>
                                <div id="slack-test-result"></div>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button('Save Slack Settings'); ?>
                </form>
            </div>
        </div>
    </div>

    <style>
    .notifications-container {
        max-width: 1200px;
        margin: 20px 0;
    }

    .notification-section {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        transition: all 0.3s ease;
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

    .service-icon .dashicons {
        font-size: 24px;
        width: 24px;
        height: 24px;
        color: #2271b1;
    }

    .section-content {
        padding: 20px;
    }

    /* Switch styles */
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2271b1;
    }

    input:checked + .slider:before {
        transform: translateX(26px);
    }

    .slider.round {
        border-radius: 24px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    #slack-test-result {
        margin-top: 10px;
        padding: 10px;
        border-radius: 4px;
        display: none;
    }

    #slack-test-result.success {
        background: #f0f6e9;
        border: 1px solid #7ad03a;
        color: #1d2327;
    }

    #slack-test-result.error {
        background: #fcf0f1;
        border: 1px solid #d63638;
        color: #1d2327;
    }
    </style>

    <script>
    jQuery(document).ready(function($) {
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

        // Test Slack connection
        $('.test-slack').click(function(e) {
            e.preventDefault(); // Prevent any default action
            
            const $button = $(this);
            const $result = $('#slack-test-result');
            const webhook_url = $('input[name="axioned_slack_webhook_url"]').val().trim();
            
            if (!webhook_url) {
                $result.removeClass('success').addClass('error')
                       .html('✗ Webhook URL is required')
                       .slideDown();
                return;
            }
            
            $button.prop('disabled', true).text('Sending...');
            $result.hide();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'json', // Explicitly expect JSON response
                data: {
                    action: 'axioned_test_slack',
                    nonce: '<?php echo wp_create_nonce("axioned_test_slack"); ?>',
                    webhook_url: webhook_url,
                    channel: $('input[name="axioned_slack_channel"]').val().trim()
                },
                success: function(response) {
                    if (response.success) {
                        $result.removeClass('error').addClass('success')
                               .html('✓ ' + response.data)
                               .slideDown();
                    } else {
                        $result.removeClass('success').addClass('error')
                               .html('✗ ' + (response.data || 'Failed to send test message'))
                               .slideDown();
                    }
                },
                error: function(xhr, status, error) {
                    let errorMsg = 'Failed to send test message';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.data) {
                            errorMsg = response.data;
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                    $result.removeClass('success').addClass('error')
                           .html('✗ Error: ' + errorMsg)
                           .slideDown();
                },
                complete: function() {
                    $button.prop('disabled', false).text('Send Test Message');
                }
            });
        });

        // Email validation
        function validateEmails(emails) {
            const emailList = emails.split(',').map(email => email.trim());
            const emailRegex = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
            
            const invalidEmails = emailList.filter(email => {
                return email !== '' && !emailRegex.test(email);
            });
            
            return {
                valid: invalidEmails.length === 0,
                invalidEmails: invalidEmails
            };
        }

        // Real-time email validation
        $('#notification-emails').on('input', function() {
            const $input = $(this);
            const $message = $('.email-validation-message');
            const $submitButton = $input.closest('form').find(':submit');
            
            if ($input.val().trim() === '') {
                $message.hide();
                $submitButton.prop('disabled', false);
                return;
            }

            const validation = validateEmails($input.val());
            
            if (!validation.valid) {
                $message.html('Invalid email(s): ' + validation.invalidEmails.join(', '))
                       .show();
                $submitButton.prop('disabled', true);
            } else {
                $message.hide();
                $submitButton.prop('disabled', false);
            }
        });

        // Form submission validation
        $('.email-settings-form').on('submit', function(e) {
            const emails = $('#notification-emails').val();
            
            if (emails.trim() !== '') {
                const validation = validateEmails(emails);
                
                if (!validation.valid) {
                    e.preventDefault();
                    $('.email-validation-message')
                        .html('Please fix invalid email(s): ' + validation.invalidEmails.join(', '))
                        .show();
                    return false;
                }
            }
        });
    });
    </script>
    <?php
} 