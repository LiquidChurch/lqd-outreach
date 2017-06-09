# Change Log
All notable changes to this project will be documented in this file.

## [Unreleased] - 0.9.4
### Added
- Add Options Page for various page settings
- Header image option for event categories
- Event attendance sync using wp_cron
- Register url added when sync to wp_post
- Partner Organization Name Needs Link to Partner Org Page

### Changed
- Change Admin Menu Item "All Outreach" to "All Outreach Events"
- Break Out Single Dropdown for Filtering to Allow Filtering By Any Combination

### Fixed
- Register url meta name fix for ccb events
- Small Fix: Drop Downs Need More Blank Space

### Removed

### Deprecated

### Security

## [0.7.0] - 2015-05-27
### Added
- Event categories shortcode btn fields
- Event categories single shortcode btn fields
- Event partner list shortcode btn fields
- Event search shortcode btn fields
- Event single btn fields
- Event categories single shortcode btn field event_category
- New shortcode for some elements (header, nav, categories list large)

### Changed
- Project menu item in nav menu
- Attributes for shortcodes to hide different parts in the front-end
- Conditions for shortcode atts events_category
- Conditions for shortcode atts events_category_single
- Conditions for shortcode atts events_partner_list
- Conditions for shortcode atts events_search
- Conditions for shortcode atts events_single

### Fixed
- Single event page time display format fix
- Nav menu css fix

## [0.5.0] - 2015-05-26
### Changed
- DB schema change for group_type filter
- Group type filter for event sync to wp_post

### Fixed
- Hide CCB Group ID meta field from Outreach Partner post type
- Update Outreach Partner posts when updating Outreach Event posts from sync page

## [0.4.5] - 2015-05-26
### Added
- Event Category Shortcode

### Changed
- Sync modified_since field is required now
- Confirm alert for sync buttons
- Sync filter for start_date and end_date

### Fixed
- Submenu auto open when hover
- Code Fixes

## [0.3.8] - 2015-05-22
### Added
- Stable release of the plugin functionality

[Unreleased]: https://github.com/LiquidChurch/lqd-outreach/compare/v0.7.0...HEAD
[0.7.0]: https://github.com/LiquidChurch/lqd-outreach/compare/v0.5.0...v0.7.0
[0.5.0]: https://github.com/LiquidChurch/lqd-outreach/compare/v0.4.5...v0.5.0
[0.4.5]: https://github.com/LiquidChurch/lqd-outreach/compare/v0.3.8...v0.4.5
[0.3.8]: https://github.com/LiquidChurch/lqd-outreach/compare/0.0.0...v0.3.8