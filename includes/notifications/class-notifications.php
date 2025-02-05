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
    
    public static function send_slack_notification($data) {
        if (get_option('axioned_slack_notifications_enabled') !== '1') {
            return false;
        }

        $webhook_url = get_option('axioned_slack_webhook_url');
        $channel = get_option('axioned_slack_channel');
        
        if (empty($webhook_url)) {
            return false;
        }

        $message = "*Review Updates - " . get_bloginfo('name') . "*\n\n";
        
        if (isset($data['google'])) {
            $message .= "*Google Reviews:*\n";
            $message .= "Rating: " . $data['google']['rating'] . "\n";
            $message .= "Reviews: " . $data['google']['count'] . "\n\n";
        }
        
        if (isset($data['yelp'])) {
            $message .= "*Yelp Reviews:*\n";
            $message .= "Rating: " . $data['yelp']['rating'] . "\n";
            $message .= "Reviews: " . $data['yelp']['count'] . "\n";
        }

        $payload = array(
            'channel' => $channel,
            'text' => $message,
        );

        $args = array(
            'body' => json_encode($payload),
            'headers' => array('Content-Type' => 'application/json'),
            'timeout' => 30,
        );

        $response = wp_remote_post($webhook_url, $args);
        
        return !is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200;
    }

    public static function test_slack_connection() {
        $webhook_url = get_option('axioned_slack_webhook_url');
        $channel = get_option('axioned_slack_channel');
        
        if (empty($webhook_url)) {
            return new WP_Error('missing_webhook', 'Webhook URL is required');
        }

        $message = "*Test Message from " . get_bloginfo('name') . "*\n";
        $message .= "Your Slack notifications are configured correctly! 🎉";

        $payload = array(
            'channel' => $channel,
            'text' => $message,
        );

        $args = array(
            'body' => json_encode($payload),
            'headers' => array('Content-Type' => 'application/json'),
            'timeout' => 30,
        );

        $response = wp_remote_post($webhook_url, $args);
        
        if (is_wp_error($response)) {
            return $response;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        
        if ($response_code !== 200) {
            return new WP_Error(
                'slack_error',
                'Slack returned error: ' . wp_remote_retrieve_response_message($response)
            );
        }

        return true;
    }
} 