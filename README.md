# Axioned Automated Reviews

A WordPress plugin that fetches reviews from Google Places and Yelp APIs, storing them in WordPress options. It uses ACF for field mapping and WordPress cron for automated updates.

## Changelog

### 1.x.x
- Added Yelp scraping fallback mechanism
- Improved error handling
- Added debug tools for testing scraping

## Requirements

- WordPress 5.0+
- PHP 7.4+
- Advanced Custom Fields (ACF) Pro
- Google Places API key
- Yelp API key
- cURL enabled

## Features

- Automated review fetching from Google Places API and Yelp API
- Fallback mechanism for Yelp reviews when API fails
- Custom scheduling for review updates
- Email and Slack notifications
- Debug tools and logging
- ACF field integration

## Yelp Reviews Integration

### Primary Method: Yelp API
- Uses Yelp Fusion API to fetch business ratings and review counts
- Requires valid API key and business details
- Automatically updates at scheduled intervals

### Fallback Method: Web Scraping
- Automatically activates when Yelp API fails
- Scrapes business page to extract:
  - Overall rating
  - Total review count
- No additional configuration needed
- Uses same format as API response

## Installation & Setup

### 1. Plugin Installation
- Download and extract to `wp-content/plugins/axioned-automated-reviews/`
- Activate plugin through WordPress admin

### 2. API Setup

#### Google Places API
1. Visit Google Cloud Console
2. Enable Places API
3. Create credentials
4. Get your Place ID:
   - Use [Google's Place ID Finder](https://developers.google.com/maps/documentation/places/web-service/place-id)
   - Or search your business on Google Maps and get ID from URL

#### Yelp API
1. Visit [Yelp Fusion](https://www.yelp.com/developers/v3/manage_app)
2. Create New App
3. Get API Key
4. Note your exact business name and location

### 3. WordPress Configuration

1. Go to "Axioned Reviews Settings"
2. Configure in this order:
   - Enter API keys in Configuration tab
   - Map ACF fields in ACF Mapping tab
   - Test APIs in Debug tab
   - Monitor in Cron Status tab

### 4. ACF Field Setup

1. Create ACF Options Page
2. Add fields for:
   - Google Rating (Text)
   - Google Review Count (Text)
   - Yelp Rating (Text)
   - Yelp Review Count (Text)
3. Note the field names for mapping

### 5. Notifications

The plugin supports both email and Slack notifications for review updates, providing real-time monitoring of your review collection process.

#### Email Notifications

Email notifications keep you informed about review updates and any potential issues.

##### Configuration
- **Enable/Disable**: Toggle email notifications
- **Recipients**: Multiple email addresses (comma-separated)
- **From Name**: Custom sender name (defaults to site name)
- **From Email**: Custom sender email (defaults to admin email)
- **Frequency**: Choose notification frequency
  - Immediately on update
  - Daily Summary
  - Weekly Summary

##### Email Types
1. **Success Notifications**
   - Subject: "[Service] Reviews Update (Success) - [Date]"
   - Contains:
     - Update type (Manual/Cron/Debug)
     - Service name (Google/Yelp)
     - Timestamp
     - Updated rating
     - Updated review count

2. **Failure Notifications**
   - Subject: "[Service] Reviews Update (Failed) - [Date]"
   - Contains:
     - Update type (Manual/Cron/Debug)
     - Service name (Google/Yelp)
     - Timestamp
     - Error details

#### Slack Notifications

Slack integration provides instant notifications in your team's Slack workspace.

##### Configuration
- **Enable/Disable**: Toggle Slack notifications
- **Webhook URL**: Your Slack app's webhook URL
- **Channel**: Optional custom channel name
- **Test Connection**: Verify your Slack setup

##### Notification Format
```
✅ *Google Reviews Update (Success) - 2024-02-05*
----------------------------------------
*Update Type:* MANUAL
*Service:* Google
*Time:* 2024-02-05 12:30:45

*Updated Values:*
Rating: *4.8/5*
Review Count: *7,383+ reviews*
----------------------------------------
```

##### Error Format
```
❌ *Yelp Reviews Update (Failed) - 2024-02-05*
----------------------------------------
*Update Type:* CRON
*Service:* Yelp
*Time:* 2024-02-05 12:30:45

*Error Details:*
API returned error: Invalid API key
----------------------------------------
```

##### Notification Triggers

Notifications are sent for:
1. Manual updates via admin dashboard
2. Scheduled cron updates
3. Debug test runs
4. Configuration errors
5. API communication issues

### Cache Management

The plugin supports automatic cache clearing for various hosting providers and CDNs after review updates.

#### Supported Providers
- WP Engine
- Cloudflare

#### Configuration
1. **WP Engine**
   - Enable/disable automatic cache clearing
   - No additional configuration needed

2. **Cloudflare**
   - Enable/disable automatic cache clearing
   - Requires API Token
   - Requires Zone ID

#### Best Practices
- Clear cache after review updates
- Test cache clearing functionality
- Monitor cache clearing logs
- Keep API credentials secure

## Technical Details

### Data Flow
1. API Request triggered (cron/manual)
2. Fetch data from APIs
3. Format values (e.g., "4.8/5", "2,175+ reviews")
4. Update WordPress options
5. Available in ACF fields

### Cron Jobs
- Schedule: Every 12 hours
- Hooks: 
  - `axioned_update_google_reviews`
  - `axioned_update_yelp_reviews`

### API Responses

#### Google Places
### API Responses

#### Google Places
```json
{
  "rating": 4.8,
  "userRatingCount": 2175
}
```

#### Yelp
```json
{
  "businesses": [{
    "rating": 4.6,
    "review_count": 213
  }]
}
```

### Usage
```php
// Get formatted ratings
$google_rating = get_option('your_google_rating_field'); // Returns "4.8/5"
$google_count = get_option('your_google_count_field'); // Returns "2,175+ reviews"
// Get Yelp ratings
$yelp_rating = get_option('your_yelp_rating_field'); // Returns "4.6/5"
$yelp_count = get_option('your_yelp_count_field'); // Returns "213+ reviews"
```

### Update
```php
// Trigger manual updates from code
do_action('axioned_update_google_reviews');
do_action('axioned_update_yelp_reviews');
```

### Folder Structure
```markdown
axioned-automated-reviews/
├── axioned-automated-reviews.php # Plugin initialization
├── includes/
│ ├── admin/
│ │ ├── class-admin.php # Admin UI & settings
│ │ ├── css/
│ │ │ └── admin-styles.php # Admin styles
│ │ └── tabs/ # Settings tabs
│ ├── google/
│ │ ├── google-fetch.php # Google API handler
│ │ └── google-cron-job.php # Scheduled tasks
│ └── yelp/
│ ├── yelp-fetch.php # Yelp API handler
│ └── yelp-cron-job.php # Scheduled tasks
└── logs/ # Debug logs
```
## Debugging

### Log Location
- Debug logs are stored in: `wp-content/uploads/axioned-reviews-logs/debug.log`
- Enable logging in Debug tab
- View logs in Log tab

### Common Issues

1. API Key Invalid
   - Check key permissions
   - Verify SSL certificate
   - Test in Debug tab

2. Cron Not Running
   - Check WP Cron status in Cron Status tab
   - Verify server timezone
   - Check error logs

3. ACF Fields Not Updating
   - Verify field names in ACF Mapping tab
   - Check API responses in Debug tab
   - Enable debug logging

## Support

- Email: mehboobs@axioned.com
- Report issues: [GitHub Issues](https://github.com/smehboobaxioned/wp-poc-review-automation-plugin/issues)

## License

GPL v2 or later

Developed by Axioned

## Screenshots

### Configuration Tab
![Configuration Tab](screenshots/configuration.png)
*Configure Google Places and Yelp API settings*

### ACF Mapping Tab
![ACF Mapping Tab](screenshots/acf-mapping.png)
*Map your ACF fields to store review data*

### Cron Status Tab
![Cron Status](screenshots/cron-status.png)
*Monitor and manually trigger review updates*

### Debug Tab
![Debug Tab](screenshots/debug.png)
*Test API connections and view responses*

### Logs Tab
![Logs Tab](screenshots/logs.png)
*View detailed plugin activity logs*


### Notifications Tab
![Notifications Tab](screenshots/notifications.png)
*Configure email and Slack notifications*

