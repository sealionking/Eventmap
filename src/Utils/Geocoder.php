<?php

namespace Drupal\unccd_event_map\Utils;

/**
 * Class to retrieve the lattitude/longitude for an address
 * @author Vladimir Metelitsa <me@greencat.io>
 */
class Geocoder {

    /**
     * Uses one of the available implementations to turn an address (city + country) into a latitude/longitude
     * To be uses as a pin on a map
     * 
     * @param city string The city of the location to geocode
     * @param country string The country the location is in
     * @return coords array An array with the latitude (lat) and longitude (long) returned by the Geocoder
     */
    public function geocode($city, $country) {
        return $this->openStreetMapNominatimGeocode($city, $country);
    }

    /**
     * Use the Map Quest Geocoding API to turn the address into a geocode
     * @see https://developer.mapquest.com/documentation/geocoding-api/
     * 
     * @param city string The city of the location to geocode
     * @param country string The country the location is in
     * @return coords array An array with the latitude (lat) and longitude (long) returned by the Geocoder
     */
    private function mapQuestGeocode($city, $country) {
        $coords = [];
        if (!empty($city)) $address = "$city, $country";
        else $address = $country;
        $address = urlencode($address);
        $config = \Drupal::config('unccd_event_map.config');
        $key = $config->get('mapquestapikey');
        $json = file_get_contents("https://www.mapquestapi.com/geocoding/v1/address?key={$key}&inFormat=kvp&outFormat=json&location={$address}&thumbMaps=false&maxResults=1");
        $json = json_decode($json);

        $coords['lat'] = $json->results[0]->locations[0]->latLng->lat;
        $coords['long'] = $json->results[0]->locations[0]->latLng->lng;

        return $coords;
    }

    /**
     * Use the Google Maps API to Geocode a location
     * @see https://developers.google.com/maps/documentation/geocoding/intro
     * 
     * @param city string The city of the location to geocode
     * @param country string The country the location is in
     * @return coords array An array with the latitude (lat) and longitude (long) returned by the Geocoder
     */
    private function googleGeocode($city, $country) {
        $coords = [];
        $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address={$city}&sensor=false&region={$country}");
        $json = json_decode($json);

        $coords['lat'] = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
        $coords['long'] = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};

        return $coords;
    }

    /**
     * Use the OpenStreetMap Nominatim API to Geocode
     * @see https://wiki.openstreetmap.org/wiki/Nominatim
     * 
     * @param city string The city of the location to geocode
     * @param country string The country the location is in
     * @return coords array An array with the latitude (lat) and longitude (long) returned by the Geocoder
     */
    private function openStreetMapNominatimGeocode($city, $country) {
        $city = urlencode($city);
        $country = urlencode($country);

        $json = file_get_contents("https://nominatim.openstreetmap.org/search/?q={$city}&country={$country}&format=json&email=jchoo@unccd.int");
        $json = json_decode($json);

        if(count($json) === 0) {
            $json = file_get_contents("https://nominatim.openstreetmap.org/search/?q={$country}&format=json&email=jchoo@unccd.int");
            $json = json_decode($json);
        }
        
        if(count($json) === 0) {
            $coords['lat'] = 0;
            $coords['long'] = 0;
        } else
        {
            $coords['lat'] = $json[0]->{'lat'};
            $coords['long'] = $json[0]->{'lon'};    
        }

        return $coords;        
    }
}
