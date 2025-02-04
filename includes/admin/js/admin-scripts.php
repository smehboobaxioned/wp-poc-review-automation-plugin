<?php
function axioned_reviews_admin_scripts() {
    if (isset($_GET['page']) && $_GET['page'] === 'axioned-reviews-settings') {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                /* DEBUG TAB JS */
                // API Testing AJAX functionality
                $('.test-api').on('click', function() {
                    var api = $(this).data('api');
                    var $progress = $('#' + api + '-progress');
                    var $results = $('#' + api + '-results');
                    var $button = $(this);
                    var $section = $button.closest('.api-section');

                    // Reset and show progress with animation
                    $results.removeClass('show').fadeOut(300, function() {
                        $(this).removeClass('success error').empty();
                        $progress.fadeIn(300);
                        $button.prop('disabled', true);
                        
                        // Expand section if collapsed
                        if ($section.hasClass('collapsed')) {
                            $section.removeClass('collapsed');
                        }

                        // Make AJAX call
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                action: 'axioned_test_api',
                                api: api,
                                nonce: '<?php echo wp_create_nonce('axioned_test_api'); ?>'
                            },
                            success: function(response) {
                                $progress.fadeOut(300, function() {
                                    $button.prop('disabled', false);
                                    
                                    if (response && response.success === true && response.data) {
                                        var html = '<div class="success">';
                                        html += '<h4>API Response:</h4>';
                                        html += '<p><strong>Rating:</strong> ' + response.data.rating + '/5</p>';
                                        html += '<p><strong>Review Count:</strong> ' + response.data.count + '</p>';
                                        html += '</div>';
                                    } else {
                                        var errorMessage = response && response.data ? response.data : 'Unknown error occurred';
                                        var html = '<div class="error">';
                                        html += '<h4>Error:</h4>';
                                        html += '<p>' + errorMessage + '</p>';
                                        html += '</div>';
                                    }
                                    
                                    $results.html(html).fadeIn(300).addClass('show');
                                });
                            },
                            error: function(xhr, status, error) {
                                $progress.fadeOut(300, function() {
                                    $button.prop('disabled', false);
                                    
                                    var errorMessage = 'Request failed';
                                    try {
                                        var response = JSON.parse(xhr.responseText);
                                        if (response && response.data) {
                                            errorMessage = response.data;
                                        }
                                    } catch (e) {
                                        errorMessage += ': ' + error;
                                    }
                                    
                                    var html = '<div class="error">';
                                    html += '<h4>Error:</h4>';
                                    html += '<p>' + errorMessage + '</p>';
                                    html += '</div>';
                                    
                                    $results.html(html).fadeIn(300).addClass('show');
                                });
                            }
                        });
                    });
                });

                // Existing collapsible functionality...
                $('.collapsible-header').on('click', function(e) {
                    e.stopPropagation(); // Prevent event bubbling
                    var $section = $(this).closest('.collapsible');
                    $section.toggleClass('collapsed');
                    
                    // Save state to localStorage
                    if (typeof localStorage !== 'undefined') {
                        var sectionId = $(this).find('.section-title').text().trim();
                        localStorage.setItem('axioned_debug_' + sectionId, $section.hasClass('collapsed') ? 'collapsed' : 'expanded');
                    }
                });

                // Restore collapsed states from localStorage
                if (typeof localStorage !== 'undefined') {
                    $('.collapsible').each(function() {
                        var sectionId = $(this).find('.section-title').text().trim();
                        var state = localStorage.getItem('axioned_debug_' + sectionId);
                        if (state === 'expanded') {
                            $(this).removeClass('collapsed');
                        }
                    });
                }


                /* CRON JOB TAB JS */
                // Update current UTC time
                function updateUTCTime() {
                    var now = new Date();
                    var hours = now.getUTCHours();
                    var minutes = now.getUTCMinutes();
                    var seconds = now.getUTCSeconds();
                    var ampm = hours >= 12 ? 'PM' : 'AM';
                    
                    // Convert to 12-hour format
                    hours = hours % 12;
                    hours = hours ? hours : 12; // the hour '0' should be '12'
                    
                    // Add leading zeros
                    minutes = minutes < 10 ? '0' + minutes : minutes;
                    seconds = seconds < 10 ? '0' + seconds : seconds;
                    
                    var timeString = hours + ':' + minutes + ':' + seconds + ' ' + ampm + ' UTC';
                    $('#current-utc').text(timeString);
                }
                
                // Update time every second
                updateUTCTime();
                setInterval(updateUTCTime, 1000);

                // Form submission handler
                $('#cron-schedule-form').on('submit', function(e) {
                    e.preventDefault();
                    
                    var $form = $(this);
                    var $submitButton = $form.find('#submit-schedule');
                    
                    // Disable submit button
                    $submitButton.prop('disabled', true);
                    
                    // Get form data
                    var frequency = $form.find('select[name="axioned_reviews_frequency"]').val();
                    var time = $form.find('input[name="axioned_reviews_time"]').val();
                    var nonce = $form.find('#schedule_nonce').val();
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'axioned_update_schedule',
                            schedule_nonce: nonce,
                            frequency: frequency,
                            time: time,
                            show_notice: true
                        },
                        success: function(response) {
                            console.log('Raw Response:', response);
                            
                            if (response && response.success) {
                                // Reload the page to show the admin notice
                                window.location.reload();
                            } else {
                                // Error will be shown as admin notice after reload
                                window.location.reload();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', {
                                status: status,
                                error: error,
                                response: xhr.responseText
                            });
                            // Reload to show error notice
                            window.location.reload();
                        }
                    });
                });


                // Update the cron toggle form handler
                $('#cron-toggle-form').on('submit', function(e) {
                    e.preventDefault();
                    
                    var $form = $(this);
                    var $submitButton = $form.find('button[type="submit"]');
                    
                    // Disable submit button
                    $submitButton.prop('disabled', true);
                    
                    // Get status of both checkboxes
                    var googleStatus = $('input[name="cron_status[google]"]').is(':checked') ? '1' : '0';
                    var yelpStatus = $('input[name="cron_status[yelp]"]').is(':checked') ? '1' : '0';
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'axioned_toggle_cron',
                            nonce: $('#cron_nonce').val(),
                            cron_status: {
                                google: googleStatus,
                                yelp: yelpStatus
                            }
                        },
                        success: function(response) {
                            // Just reload to show admin notice
                            location.reload();
                        },
                        error: function() {
                            // Just reload to show admin notice
                            location.reload();
                        },
                        complete: function() {
                            $submitButton.prop('disabled', false);
                        }
                    });
                });

                // Add this to your existing jQuery document ready function
                $('.run-now').on('click', function() {
                    var $button = $(this);
                    var service = $button.data('service');
                    
                    // Disable button and show spinner
                    $button.prop('disabled', true).addClass('running');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'axioned_run_cron_now',
                            nonce: '<?php echo wp_create_nonce("axioned_run_cron_now"); ?>',
                            service: service
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                location.reload();
                            }
                        },
                        error: function() {
                            location.reload();
                        }
                    });
                });
            });
        </script>
        <?php
    }
}
add_action('admin_footer', 'axioned_reviews_admin_scripts');
