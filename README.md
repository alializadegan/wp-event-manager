# WordPress Event Manager Plugin

This plugin allows you to manage and display events in a WordPress site. It includes features such as event types, filtering, and user notifications.

## Features
- Custom post type for "Events"
- Event taxonomies (e.g., "Event Type")
- Event filtering and search
- Custom meta boxes for event date, location, and RSVP
- REST API integration

## Installation

### Prerequisites
- A WordPress installation (version 5.0+)
- PHP 7.0 or higher.

### Steps
1. Download the plugin or clone the repository into your WordPress plugins directory:
   ```bash
   git clone https://github.com/alializadegan/wp-event-manager.git wp-content/plugins/event-manager
   
Activate the Plugin:

Go to the WordPress admin panel → Plugins → Installed Plugins, and activate the "Event Manager" plugin.

Create Events:

After activation, a new "Events" section will appear in your WordPress admin sidebar. Create new events by going to Events → Add New.
Use Shortcodes:

Use the following shortcode to display a list of events on any page or post:

```shortcode
[event_listing]
```
Event Filtering:

Users can filter events by type, date, or other criteria by navigating to the event archive page (e.g., yoursite.com/events).

Custom Post Type and Taxonomy
The plugin registers a custom post type Event and a taxonomy Event Type for categorizing events.
You can create event types like Conference, Webinar, Workshop to categorize your events.

Shortcode Usage
To display a list of events in any post or page, use:

```shortcode
[event_listing]
```
You can add filter options via the plugin settings to allow users to search by event type, date range, etc.
Event Meta Fields
Each event post includes custom meta fields:

Event Date: Specify the date of the event.
Event Location: Add the location where the event is happening.
RSVP Count: Track the number of RSVPs for the event.
These fields can be managed in the event editor in the WordPress admin dashboard.

Front-End Templates
Custom templates are included to display event details on the front end.
Single Event Template: single-event.php is used to display individual event details.
Event Archive Template: archive-event.php is used for the event listing page, with filter options.
If you want to customize the look and feel of these templates, you can copy them to your theme's directory and modify them.

Sample Data
To help you test the plugin, we’ve included sample event data. Follow the steps below to import the sample data into your WordPress installation:

Download the sample-data.xml file from this repository.
Go to your WordPress admin panel → Tools → Import → WordPress.
Upload the sample-data.xml file and follow the prompts to import the data.
Navigate to Events in your admin dashboard to view the imported events.


Sample Data Example (XML):

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0"
    xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:wp="http://wordpress.org/export/1.2/">
<channel>
    <title>Test Events</title>
    <wp:wxr_version>1.2</wp:wxr_version>
    <item>
        <title>Sample Event 1</title>
        <wp:post_date>2024-11-12 10:00:00</wp:post_date>
        <content:encoded><![CDATA[Event details for Sample Event 1]]></content:encoded>
        <wp:postmeta>
            <wp:meta_key>event_date</wp:meta_key>
            <wp:meta_value><![CDATA[2024-11-12]]></wp:meta_value>
        </wp:postmeta>
        <wp:postmeta>
            <wp:meta_key>event_location</wp:meta_key>
            <wp:meta_value><![CDATA[New York]]></wp:meta_value>
        </wp:postmeta>
        <wp:postmeta>
            <wp:meta_key>event_rsvp_count</wp:meta_key>
            <wp:meta_value><![CDATA[50]]></wp:meta_value>
        </wp:postmeta>
    </item>
    <item>
        <title>Sample Event 2</title>
        <wp:post_date>2024-12-05 10:00:00</wp:post_date>
        <content:encoded><![CDATA[Event details for Sample Event 2]]></content:encoded>
        <wp:postmeta>
            <wp:meta_key>event_date</wp:meta_key>
            <wp:meta_value><![CDATA[2024-12-05]]></wp:meta_value>
        </wp:postmeta>
        <wp:postmeta>
            <wp:meta_key>event_location</wp:meta_key>
            <wp:meta_value><![CDATA[Los Angeles]]></wp:meta_value>
        </wp:postmeta>
        <wp:postmeta>
            <wp:meta_key>event_rsvp_count</wp:meta_key>
            <wp:meta_value><![CDATA[100]]></wp:meta_value>
        </wp:postmeta>
    </item>
</channel>
</rss>
```
User Notifications
When events are created or updated, users who have subscribed will receive notifications via email. To enable email notifications:

Ensure your server or hosting environment supports email sending.
Customize the email templates located in the plugin’s folder for user notifications.

REST API
The plugin exposes the custom post type Event via the WordPress REST API. External applications can retrieve event data using the following endpoint:

```bash
/wp-json/wp/v2/event
```
Localization
The plugin is ready for translation. Use the .pot file provided in the /languages directory to translate the plugin into your language.

Place your translated .mo files in the /languages directory.

Security Considerations
All user inputs are validated and sanitized before saving.
Nonce verification is implemented for all data-modifying actions to prevent CSRF attacks.

License
This plugin is licensed under the MIT License. You are free to modify and distribute it as per the terms of the license.


