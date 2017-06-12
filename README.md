# Liquid Outreach #
**Contributors:**      SurajPrGupta, LiquidChurch, Dave Mackey (@davidshq)  
**Donate link:**       https://liquidchurch.com  
**Tags:**              church, outreach, events  
**Requires at least:** 4.4  
**Tested up to:**      4.7.2  
**Stable tag:**        0.10.0  
**License:**           GPLv2  
**License URI:**       http://www.gnu.org/licenses/gpl-2.0.html  

## Description ##

This WordPress plugin provides a robust UI for viewing outreach events and organizations data pulled from Church 
Community Builder (CCB) via its API.

## Installation ##

### Manual Installation ###

1. Upload the entire `/lqd-outreach` directory to the `/wp-content/plugins/` directory.
2. Activate Liquid Outreach through the 'Plugins' menu in WordPress.

## Frequently Asked Questions ##


## Screenshots ##


## Changelog ##

## [0.10.0] - 2017-06-10
### Added
- Add Options Page for various page settings
- Header image option for event categories
- Event attendance sync using wp_cron
- Register url added when sync to wp_post
- Partner Organization Name Needs Link to Partner Org Page
- Auto Setting Categories for Events

### Changed
- Change Admin Menu Item "All Outreach" to "All Outreach Events"
- Break Out Single Dropdown for Filtering to Allow Filtering By Any Combination

### Fixed
- Register url meta name fix for ccb events
- Small Fix: Drop Downs Need More Blank Space
- Small Fix: Addition Info Not Lining Up
- Small Fix: Date & Time Customization

## [0.7.0] - 2017-05-27
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

## [0.5.0] - 2017-05-26
### Changed
- DB schema change for group_type filter
- Group type filter for event sync to wp_post

### Fixed
- Hide CCB Group ID meta field from Outreach Partner post type
- Update Outreach Partner posts when updating Outreach Event posts from sync page

## [0.4.5] - 2017-05-26
### Added
- Event Category Shortcode

### Changed
- Sync modified_since field is required now
- Confirm alert for sync buttons
- Sync filter for start_date and end_date

### Fixed
- Submenu auto open when hover
- Code Fixes

## [0.3.8] - 2017-05-22
### Added
- Stable release of the plugin functionality