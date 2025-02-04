<?php
// Update the admin styles function
function axioned_reviews_admin_styles() {
    if (isset($_GET['page']) && $_GET['page'] === 'axioned-reviews-settings') {
        ?>
        <style>
            /* Main Container Styles */
            .wrap {
                max-width: 1200px;
                margin: 20px 20px 0 0;
                background: #fff;
                border: 1px solid #c3c4c7;
                border-radius: 4px;
                box-shadow: 0 1px 2px rgba(0,0,0,0.05);
                padding: 20px;
            }

            /* Header Styles */
            .wrap h1 {
                color: #1d2327;
                font-size: 24px;
                font-weight: 500;
                margin: 0 0 1.5rem 0;
                padding: 0;
                line-height: 1.3;
            }

            /* Tab Navigation */
            .nav-tab-wrapper {
                border-bottom: 1px solid #c3c4c7;
                margin-bottom: 30px;
                padding: 0;
                position: relative;
                background: #fff;
                box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            }

            .nav-tab {
                float: left;
                border: none;
                border-bottom: 3px solid transparent;
                margin: 0;
                padding: 12px 20px;
                font-size: 14px;
                line-height: 1.71428571;
                font-weight: 500;
                background: transparent;
                color: #646970;
                text-decoration: none;
                white-space: nowrap;
                transition: all 0.2s ease;
            }

            .nav-tab:hover {
                background-color: #f0f0f1;
                color: #1d2327;
                border-bottom-color: #646970;
            }

            .nav-tab-active,
            .nav-tab-active:focus,
            .nav-tab-active:focus:active,
            .nav-tab-active:hover {
                border-bottom: 3px solid #2271b1;
                color: #1d2327;
                background: #fff;
                outline: none;
                box-shadow: none;
            }

            /* Tab Content Container */
            .tab-content {
                background: #fff;
                border-radius: 4px;
                box-shadow: 0 1px 4px rgba(0,0,0,0.05);
                margin-top: 20px;
            }

            /* Section Styles */
            .debug-section {
                background: #fff;
                border: 1px solid #dcdcde;
                border-radius: 4px;
                margin-bottom: 24px;
                padding: 20px;
            }

            .debug-section h3 {
                color: #1d2327;
                font-size: 16px;
                font-weight: 500;
                margin: 0;
            }
            .debug-section .api-section {
                margin-top: 12px;
            }

            .debug-section.collapsed .api-section {
                margin-top: 0;
            }


            /* Form Styles */
            .form-table {
                margin-top: 0;
            }

            .form-table th {
                padding-left: 0;
            }

            /* Button Styles */
            .button.test-api {
                background: #2271b1;
                border-color: #2271b1;
                color: #fff;
                padding: 6px 16px;
                height: auto;
                line-height: 1.4;
                font-size: 13px;
                font-weight: 500;
                transition: all 0.2s ease;
                box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            }

            .button.test-api:hover {
                background: #135e96;
                border-color: #135e96;
                transform: translateY(-1px);
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }

            /* Debug Info Panel */
            .debug-info {
                background: #f6f7f7;
                border: 1px solid #dcdcde;
                border-radius: 4px;
                padding: 16px;
                margin: 16px 0;
            }

            /* Results Styling */
            .debug-results {
                margin-top: 16px;
                padding: 16px;
                border-radius: 4px;
                border-left: 4px solid transparent;
            }

            .debug-results.success {
                background: #f0f6e9;
                border-left-color: #00a32a;
            }

            .debug-results.error {
                background: #fcf0f1;
                border-left-color: #d63638;
            }

            /* Responsive Adjustments */
            @media screen and (max-width: 782px) {
                .nav-tab-wrapper {
                    border: none;
                    background: transparent;
                    box-shadow: none;
                }

                .nav-tab {
                    width: 100%;
                    border: 1px solid #c3c4c7;
                    border-bottom: none;
                    margin: 0;
                    padding: 15px;
                    text-align: left;
                    background: #fff;
                }

                .nav-tab:last-child {
                    border-bottom: 1px solid #c3c4c7;
                }

                .nav-tab-active,
                .nav-tab-active:focus,
                .nav-tab-active:focus:active,
                .nav-tab-active:hover {
                    border-left: 3px solid #2271b1;
                    border-bottom: 1px solid #c3c4c7;
                }

                .wrap {
                    margin: 10px;
                    padding: 15px;
                }

                .form-table th {
                    padding-bottom: 10px;
                }

                .form-table td {
                    padding: 15px 10px;
                }
            }

            /* Additional Polish */
            .debug-section:last-child {
                margin-bottom: 0;
            }

            .debug-info p:first-child {
                margin-top: 0;
            }

            .debug-info p:last-child {
                margin-bottom: 0;
            }

            /* Status Badge Styles */
            .status-ok, .status-error {
                display: inline-block;
                padding: 2px 8px;
                border-radius: 3px;
                font-size: 12px;
                font-weight: 500;
            }

            .status-ok {
                background: #edf8ee;
                color: #00a32a;
            }

            .status-error {
                background: #fcf0f1;
                color: #d63638;
            }
            
            /* Collapsible Styles */
            .collapsible-header {
                cursor: pointer;
                position: relative;
                padding-right: 30px;
                user-select: none;
            }

            .collapsible-header:hover {
                color: #2271b1;
            }

            .toggle-indicator {
                position: relative;
                right: 0;
                /* top: 50%; */
                /* transform: translateY(-50%); */
                width: 20px;
                height: 20px;
                display: inline-block;
                vertical-align: middle;
            }

            .toggle-indicator::before {
                content: '';
                position: absolute;
                top: 50%;
                /* left: 50%; */
                right: 0;
                transform: translate(-50%, -50%);
                border-left: 5px solid transparent;
                border-right: 5px solid transparent;
                border-top: 5px solid #50575e;
                transition: transform 0.2s ease;
            }

            .collapsed .toggle-indicator::before {
                transform: translate(-50%, -50%) rotate(-90deg);
            }

            .collapsible-content {
                overflow: hidden;
                max-height: 2000px;
                transition: all 0.3s ease-in-out;
            }

            .collapsed .collapsible-content {
                max-height: 0;
            }

            /* API Section Styles */
            .api-section {
                padding: 0;
                margin: 0 0 20px;
                background: #fff;
                border: 1px solid #dcdcde;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
            }

            .api-section:hover {
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            }

            .api-section:last-child {
                margin-bottom: 0;
            }

            .api-section h4.collapsible-header {
                margin: 0;
                padding: 20px;
                border-bottom: 1px solid transparent;
                font-size: 15px;
                font-weight: 600;
                color: #1d2327;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .api-section:not(.collapsed) h4.collapsible-header {
                border-bottom-color: #dcdcde;
            }

            .api-section .collapsible-content {
                padding: 20px;
            }

            .api-section.collapsed .collapsible-content {
                padding: 0 20px;
            }

            /* Debug Info Spacing */
            .debug-info + .debug-info {
                margin-top: 20px;
            }

            /* Animation for smooth collapse */
            .collapsible {
                transition: margin 0.3s ease;
            }

            /* Enhanced API Section Styles */
            .api-section {
                padding: 0;
                margin: 0 0 20px;
                background: #fff;
                border: 1px solid #dcdcde;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
            }

            .api-section:hover {
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            }

            /* Enhanced Header Styles */
            .api-section h4.collapsible-header {
                margin: 0;
                padding: 20px;
                border-bottom: 1px solid transparent;
                font-size: 15px;
                font-weight: 600;
                color: #1d2327;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            /* Enhanced Debug Info Styles */
            .debug-info {
                background: #f8f9fa;
                border: 1px solid #e2e4e7;
                border-radius: 6px;
                padding: 20px;
                margin: 20px 0;
            }

            .debug-info p {
                margin: 8px 0;
                color: #1e1e1e;
                font-size: 13px;
                line-height: 1.5;
            }

            /* Enhanced Button Styles */
            .button.test-api {
                background: #2271b1;
                border: 1px solid #2271b1;
                color: #fff;
                padding: 8px 20px;
                height: auto;
                line-height: 1.4;
                font-size: 13px;
                font-weight: 500;
                border-radius: 4px;
                transition: all 0.2s ease;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            .button.test-api:hover:not(:disabled) {
                background: #fff;
                border: 1px solid #135e96;
                color: #135e96;
                transform: translateY(-1px);
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.12);

            }

            .button.test-api:disabled {
                background: #a7aaad;
                cursor: not-allowed;
                transform: none;
                box-shadow: none;
            }

            /* Enhanced Progress Indicator */
            .debug-progress {
                margin: 15px 0;
                padding: 15px;
                background: #f0f6fc;
                border-radius: 4px;
                display: flex;
                align-items: center;
                color: #1d2327;
                font-size: 13px;
                animation: pulse 2s infinite;
            }

            @keyframes pulse {
                0% { background-color: #f0f6fc; }
                50% { background-color: #e5effa; }
                100% { background-color: #f0f6fc; }
            }

            .debug-progress .spinner {
                margin: 0 10px 0 0;
                background-size: 16px 16px;
                opacity: 0.8;
            }

            /* Enhanced Results Styling */
            .debug-results {
                margin: 15px 0;
                opacity: 0;
                transform: translateY(10px);
                transition: all 0.3s ease;
            }

            .debug-results.show {
                opacity: 1;
                transform: translateY(0);
            }

            .debug-results .success,
            .debug-results .error {
                padding: 20px;
                border-radius: 6px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            }

            .debug-results .success {
                background: #f0f7ed;
                border-left: 4px solid #00a32a;
            }

            .debug-results .error {
                background: #fcf0f1;
                border-left: 4px solid #d63638;
            }

            .debug-results h4 {
                margin: 0 0 12px;
                font-size: 14px;
                font-weight: 600;
                color: #1d2327;
            }

            .debug-results p {
                margin: 8px 0;
                font-size: 13px;
                line-height: 1.5;
                color: #1e1e1e;
            }

            /* Masked Key Styling */
            .masked-key {
                background: #f0f0f1;
                padding: 3px 8px;
                border-radius: 3px;
                font-family: monospace;
                font-size: 12px;
                color: #1d2327;
            }

            /* Cron Info Styles */
            .time-select {
                padding: 5px;
                border: 1px solid #8c8f94;
                border-radius: 4px;
                width: auto;
            }

            .cron-info {
                background: #f0f6fc;
                border-left: 4px solid #2271b1;
                padding: 12px 16px;
                margin-bottom: 20px;
            }

            .cron-info p {
                margin: 0;
                color: #1d2327;
            }

            #current-utc {
                font-family: monospace;
                background: #f0f0f1;
                padding: 2px 6px;
                border-radius: 3px;
            }

            /* New styles from the code block */
            .code-block {
                background: #f6f7f7;
                border: 1px solid #dcdcde;
                border-radius: 4px;
                padding: 15px;
                margin: 15px 0;
            }

            .code-block code {
                display: block;
                padding: 10px;
                background: #2c3338;
                color: #000;
                border-radius: 3px;
                font-family: monospace;
                margin: 10px 0;
                word-break: break-all;
            }

            .code-block .description {
                font-style: italic;
                color: #646970;
                margin-top: 5px;
            }

            .notice ol {
                margin: 10px 0 10px 20px;
                list-style-type: decimal;
            }

            .notice li {
                margin: 5px 0;
            }

            .notice code {
                background: rgba(0, 0, 0, 0.07);
                padding: 3px 5px;
                border-radius: 3px;
            }

            /* Toggle Switch */
            .switch {
                position: relative;
                display: inline-block;
                width: 50px;
                height: 24px;
                margin-right: 10px;
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

            input:focus + .slider {
                box-shadow: 0 0 1px #2271b1;
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

            .status-text {
                vertical-align: middle;
                margin-left: 5px;
            }

            /* Add these styles for the status dots */
            .status-dot {
                display: inline-block;
                width: 10px;
                height: 10px;
                border-radius: 50%;
                margin-right: 10px;
                vertical-align: middle;
            }

            .status-dot.inactive {
                background-color: #dc3232; /* WordPress red */
            }

            .status-dot.active {
                background-color: #46b450; /* WordPress green */
                animation: pulse 2s infinite;
            }

            @keyframes pulse {
                0% {
                    opacity: 1;
                }
                50% {
                    opacity: 0.4;
                }
                100% {
                    opacity: 1;
                }
            }

            /* Run Now Button Styles */
            .button.run-now {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 4px 8px;
            }

            .button.run-now .dashicons {
                font-size: 16px;
                width: 16px;
                height: 16px;
            }

            .button.run-now:disabled {
                cursor: not-allowed;
            }

            .button.run-now.running {
                pointer-events: none;
            }

            .button.run-now.running .dashicons {
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                100% {
                    transform: rotate(360deg);
                }
            }

            /* Plugin Title */
            .axioned-reviews-title {
                font-size: 23px;
                font-weight: 400;
                margin: 0;
                padding: 20px 0;
                line-height: 1.3;
                color: #1d2327;
            }

            /* Wrapper for entire plugin admin area */
            .axioned-reviews-wrap {
                margin: 20px 20px 0 2px;
            }

            /* Add these to your existing configuration styles */
            .config-section {
                margin-top: 0;
            }

            .config-section:first-child {
                margin-top: 20px;
            }
        </style>
        <?php
    }
}
add_action('admin_head', 'axioned_reviews_admin_styles');
