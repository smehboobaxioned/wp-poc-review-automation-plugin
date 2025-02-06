# Changelog
All notable changes to the Axioned Automated Reviews plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2025-02-06
### Added
- Yelp scraping fallback mechanism when API fails
- Debug tool to test Yelp scraping functionality
- Informational notes about fallback mechanism in admin UI
- Enhanced logging for scraping attempts

### Changed
- Improved error handling for Yelp review fetching
- Updated admin UI to include scraping test button
- Enhanced debug logging with more detailed information

### Fixed
- Issue with Yelp API 400 status handling
- Multiple retry attempts on API failure

## [1.0.0] - 2025-02-05
### Added
- Initial release
- Google Places API integration
- Yelp API integration
- ACF field mapping
- Email notifications
- Slack notifications
- Debug tools
- Logging system
- Cache management for WP Engine and Cloudflare
- WordPress cron scheduling
- Admin interface with configuration tabs