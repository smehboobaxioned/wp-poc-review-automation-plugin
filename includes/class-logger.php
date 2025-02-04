<?php
/**
 * Logger Class
 *
 * This file contains the code for the logger class.
 * It allows users to log messages to the debug.log file.
 */

class Axioned_Reviews_Logger {
    private static $initialized = false;
    private static $log_file = null;

    public static function init() {
        if (!self::$initialized) {
            // Get WordPress upload directory
            $upload_dir = wp_upload_dir();
            $log_dir = $upload_dir['basedir'] . '/axioned-reviews-logs';
            self::$log_file = $log_dir . '/debug.log';

            // Create directory if it doesn't exist
            if (!file_exists($log_dir)) {
                wp_mkdir_p($log_dir);
            }

            // Create log file if it doesn't exist
            if (!file_exists(self::$log_file)) {
                file_put_contents(self::$log_file, '');
            }

            self::$initialized = true;
        }
    }

    public static function log($message, $level = 'INFO') {
        if (!self::$initialized) {
            self::init();
        }

        if (!get_option('axioned_reviews_logging_enabled', true)) {
            return;
        }

        $timestamp = current_time('Y-m-d H:i:s');
        $formatted_message = sprintf("[%s] [%s] %s\n", $timestamp, strtoupper($level), $message);

        error_log($formatted_message, 3, self::$log_file);
    }

    public static function clear_logs() {
        if (!self::$initialized) {
            self::init();
        }

        if (file_exists(self::$log_file)) {
            file_put_contents(self::$log_file, '');
            self::log('Log file cleared');
        }
    }

    public static function get_logs() {
        if (!self::$initialized) {
            self::init();
        }

        if (file_exists(self::$log_file)) {
            return file_get_contents(self::$log_file);
        }

        return '';
    }

    public static function get_log_file_path() {
        if (!self::$initialized) {
            self::init();
        }

        return self::$log_file;
    }
} 