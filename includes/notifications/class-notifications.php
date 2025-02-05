<?php
/**
 * Notifications Handler
 *
 * This file contains the code for handling email and Slack notifications.
 */

class Axioned_Reviews_Notifications {
    
    /**
     * Send review update notification email
     * 
     * @param string $service 'google' or 'yelp'
     * @param array $data Review data
     * @param bool $success Whether the update was successful
     * @param string $trigger 'manual' or 'cron'
     * @param string $error_message Error message if any
     * @return bool Whether email was sent successfully
     */
    public static function send_review_update_email($service, $data = [], $success = true, $trigger = 'cron', $error_message = '') {
        // Log start of email sending process
        Axioned_Reviews_Logger::log("Starting email notification process for {$service} reviews ({$trigger})");

        // Check if notifications are enabled
        if (get_option('axioned_email_notifications_enabled') !== '1') {
            Axioned_Reviews_Logger::log('Email notifications are disabled, skipping...', 'info');
            return false;
        }

        // Get recipient emails
        $emails = get_option('axioned_notification_emails');
        if (empty($emails)) {
            Axioned_Reviews_Logger::log('No recipient emails configured, skipping...', 'error');
            return false;
        }

        $to = array_map('trim', explode(',', $emails));
        Axioned_Reviews_Logger::log("Sending email to: " . implode(', ', $to));

        // Get From settings
        $from_name = get_option('axioned_notification_from_name', get_bloginfo('name'));
        $from_email = get_option('axioned_notification_from_email', get_bloginfo('admin_email'));
        
        Axioned_Reviews_Logger::log("Using From: {$from_name} <{$from_email}>");

        $date = current_time('Y-m-d H:i:s');
        $service_name = ucfirst($service);
        $trigger_prefix = strtoupper($trigger);
        $status = $success ? 'Success' : 'Failed';

        // Build subject line
        $subject = sprintf(
            '%s Reviews Update (%s) - %s',
            $service_name,
            $status,
            date('Y-m-d', strtotime($date))
        );

        // Log email content for debugging
        Axioned_Reviews_Logger::log("Email Subject: {$subject}");

        // Build HTML message
        $site_name = get_bloginfo('name');
        $message = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { margin-bottom: 30px; }
        .content { background: #f9f9f9; padding: 20px; border-radius: 5px; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 12px; }
        .status { font-weight: bold; color: ' . ($success ? '#28a745' : '#dc3545') . '; }
        .data-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .data-table td { padding: 8px; border-bottom: 1px solid #eee; }
        .data-table td:first-child { color: #666; width: 120px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>' . $site_name . ' Review Updates</h2>
        </div>
        <div class="content">';

        if ($success) {
            $message .= '<p>The ' . $service_name . ' reviews have been <span class="status">successfully updated</span>.</p>
                <table class="data-table">
                    <tr>
                        <td>Update Type:</td>
                        <td>' . $trigger_prefix . '</td>
                    </tr>
                    <tr>
                        <td>Service:</td>
                        <td>' . $service_name . '</td>
                    </tr>
                    <tr>
                        <td>Date & Time:</td>
                        <td>' . $date . '</td>
                    </tr>
                    <tr>
                        <td>Rating:</td>
                        <td><strong>' . $data['rating'] . '</strong></td>
                    </tr>
                    <tr>
                        <td>Review Count:</td>
                        <td><strong>' . $data['count'] . '</strong></td>
                    </tr>
                </table>';
        } else {
            $message .= '<p>The ' . $service_name . ' reviews update has <span class="status">failed</span>.</p>
                <table class="data-table">
                    <tr>
                        <td>Update Type:</td>
                        <td>' . $trigger_prefix . '</td>
                    </tr>
                    <tr>
                        <td>Service:</td>
                        <td>' . $service_name . '</td>
                    </tr>
                    <tr>
                        <td>Date & Time:</td>
                        <td>' . $date . '</td>
                    </tr>
                    <tr>
                        <td>Error Details:</td>
                        <td>' . nl2br($error_message) . '</td>
                    </tr>
                </table>';
        }

        $message .= '</div>
            <div class="footer">
                <p>This is an automated message from ' . $site_name . ' Review Management System.</p>
                <p>If you have any questions, please contact your site admin.</p>
            </div>
        </div>
    </body>
</html>';

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            sprintf('From: %s <%s>', $from_name, $from_email)
        );

        // Attempt to send email
        $sent = wp_mail($to, $subject, $message, $headers);

        if ($sent) {
            Axioned_Reviews_Logger::log("Email notification sent successfully");
        } else {
            Axioned_Reviews_Logger::log("Failed to send email notification", 'error');
            // Log WordPress mail error if available
            global $phpmailer;
            if (isset($phpmailer) && is_wp_error($phpmailer->ErrorInfo)) {
                Axioned_Reviews_Logger::log("Mail error: " . $phpmailer->ErrorInfo, 'error');
            }
        }
        
        return $sent;
    }
    
    /**
     * Send Slack notification for review updates
     * 
     * @param string $service 'google' or 'yelp'
     * @param array $data Review data
     * @param bool $success Whether the update was successful
     * @param string $trigger 'manual', 'cron', or 'debug'
     * @param string $error_message Error message if any
     * @return bool Whether notification was sent successfully
     */
    public static function send_slack_notification($service, $data = [], $success = true, $trigger = 'cron', $error_message = '') {
        // Log start of Slack notification process
        Axioned_Reviews_Logger::log("Starting Slack notification process for {$service} reviews ({$trigger})");

        if (get_option('axioned_slack_notifications_enabled') !== '1') {
            Axioned_Reviews_Logger::log('Slack notifications are disabled, skipping...', 'info');
            return false;
        }

        $webhook_url = get_option('axioned_slack_webhook_url');
        $channel = get_option('axioned_slack_channel');
        
        if (empty($webhook_url)) {
            Axioned_Reviews_Logger::log('Slack webhook URL not configured, skipping...', 'error');
            return false;
        }

        $date = current_time('Y-m-d H:i:s');
        $service_name = ucfirst($service);
        $trigger_prefix = strtoupper($trigger);
        $status = $success ? 'Success' : 'Failed';
        $icon = $success ? '✅' : '❌';

        // Build message
        $message = sprintf(
            "%s *%s Reviews Update (%s) - %s*\n",
            $icon,
            $service_name,
            $status,
            date('Y-m-d', strtotime($date))
        );
        
        $message .= "----------------------------------------\n";
        $message .= sprintf("*Update Type:* %s\n", $trigger_prefix);
        $message .= sprintf("*Service:* %s\n", $service_name);
        $message .= sprintf("*Time:* %s\n\n", $date);

        if ($success) {
            $message .= "*Updated Values:*\n";
            $message .= sprintf("Rating: *%s*\n", $data['rating']);
            $message .= sprintf("Review Count: *%s*\n", $data['count']);
        } else {
            $message .= "*Error Details:*\n";
            $message .= $error_message . "\n";
        }

        $message .= "----------------------------------------\n";

        $payload = array(
            'channel' => $channel,
            'text' => $message,
            'mrkdwn' => true
        );

        Axioned_Reviews_Logger::log("Sending Slack message to channel: {$channel}");

        $args = array(
            'body' => json_encode($payload),
            'headers' => array('Content-Type' => 'application/json'),
            'timeout' => 30,
        );

        $response = wp_remote_post($webhook_url, $args);
        
        if (is_wp_error($response)) {
            Axioned_Reviews_Logger::log("Slack API Error: " . $response->get_error_message(), 'error');
            return false;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        
        if ($response_code !== 200) {
            Axioned_Reviews_Logger::log("Slack API returned non-200 status code: {$response_code}", 'error');
            return false;
        }

        Axioned_Reviews_Logger::log("Slack notification sent successfully");
        return true;
    }

    public static function test_slack_connection() {
        Axioned_Reviews_Logger::log("Starting Slack test connection");

        $webhook_url = get_option('axioned_slack_webhook_url');
        $channel = get_option('axioned_slack_channel');
        
        if (empty($webhook_url)) {
            Axioned_Reviews_Logger::log("Slack test failed: Webhook URL is empty", 'error');
            return new WP_Error('missing_webhook', 'Webhook URL is required');
        }

        Axioned_Reviews_Logger::log("Testing Slack webhook: " . substr($webhook_url, 0, 30) . '...');

        $message = "✅ *Test Message from " . get_bloginfo('name') . "*\n";
        $message .= "----------------------------------------\n";
        $message .= "Your Slack notifications are configured correctly! 🎉\n";
        $message .= "• Webhook URL: Connected\n";
        $message .= "• Channel: " . ($channel ? $channel : 'Default') . "\n";
        $message .= "----------------------------------------\n";
        $message .= "_Sent on: " . current_time('Y-m-d H:i:s') . "_";

        $payload = array(
            'channel' => $channel,
            'text' => $message,
            'mrkdwn' => true
        );

        $args = array(
            'body' => json_encode($payload),
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'timeout' => 30,
            'sslverify' => false // Try this if having SSL issues
        );

        Axioned_Reviews_Logger::log("Sending Slack test message...");

        $response = wp_remote_post($webhook_url, $args);
        
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            Axioned_Reviews_Logger::log("Slack test failed: " . $error_message, 'error');
            return new WP_Error('request_failed', $error_message);
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        
        Axioned_Reviews_Logger::log("Slack API Response Code: " . $response_code);
        Axioned_Reviews_Logger::log("Slack API Response Body: " . $response_body);

        if ($response_code !== 200) {
            $error = "Slack API returned error code: " . $response_code;
            if ($response_body) {
                $error .= " - " . $response_body;
            }
            Axioned_Reviews_Logger::log($error, 'error');
            return new WP_Error('api_error', $error);
        }

        Axioned_Reviews_Logger::log("Slack test message sent successfully");
        return true;
    }
} 