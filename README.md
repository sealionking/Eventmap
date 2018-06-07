# UNCCD Event Map Drupal Module
The UNCCD Event Map Drupal Module display a map of events.
Users can add events, which after approval appear on the map.

## User Guide

### Installing the module
The module requires Drupal 8.x, and has no other module dependencies.
To install the module:
1. Create a new folder "unccd_eventmap" inside "docroot/modules/unccd"
2. Place all of the files from this repository inside the above folder.
3. Go to "Extend" in the Drupal admin panel.
4. Find UNCCD Event Map in the module list, and check the box next to it
5. Click "Install" at the bottom of the page
6. Clear the cache by running "drush cr" or go to "Configuration -> Performance" and click "Clear all caches"

### Updating the module
To update the module:
1. Update the contents of "docroot/modules/unccd/unccd_eventmap" with the contents of this repository
2. Clear the cache by running "drush cr" or go to "Configuration -> Performance" and click "Clear all caches"

### Usage

The module introduces two new blocks:
- Event Map Block: Displays approved events on a map
- Events Around the World Block: Displays approved events by country/continent
These blocks can be inserted into any page in Drupal settings (Structure -> Block layout).

The public form for adding events is acessible under /event-map/form, and also from a link included in the Event Map block.

The module also adds an admin panel page to manage events, which can be accessed at Structure -> UNCCD Event Map Events.

The permission "Manage UNCCD Event Map" can be given to users for them to be able to approve, edit and delete events.

## Development

### Database Schema
The module adds one additional database table called "unccd_event_map", it has the following fields:
- id (int) Primary Key: Unique event ID.
- title (varchar) Title of the event.
- organisation (varchar) Organisation running the event.
- url (varchar) URL of the event on an external website.
- email (varchar) Email of the contact person.
- city (varchar) City where the event takes place.
- country (varchar) Country where the event takes place.
- date (datetime) The date the event takes place on.
- description (text) Description of the event
- latitude (float) The latitude of the location of the event (for map display)
- longitude (float) The longitude of the location of the event (for map display)
- approved(tinyint) Has the event been approved?
- image_id (int) The Drupal file id for the uploaded image (needed to delete image when event is deleted)
- image (varchar) Url of the event image

### Geocoding
The module has two different geocoding services implemented to convert from the user entered city and country to a latitude/longitude on the map.

The module implements Geocoding with both the [GoogleMaps API](https://developers.google.com/maps/documentation/geocoding/intro) and the [MapQuest API](https://developer.mapquest.com/documentation/geocoding-api/).

The MapQuest implementation is enabled by default and the API key can be changed in "Settings -> Development -> UNCCD Event Map Settings"

### External libraries
The module uses three third-party JavaScript libraries:
- [Leaflet](https://leafletjs.com/): The interactive mapping library used to display the OpenStreetMaps tiles (Version: 1.3.1)
- [Leaflet MarkerCluster](https://github.com/Leaflet/Leaflet.markercluster/): Clusters nearby pins to reduce clutter and make the map cleaner (Version: 1.3.0)
- [Date Picker](https://fengyuanchen.github.io/datepicker/): A date picker for the user event submission form (Version: 0.6.5)