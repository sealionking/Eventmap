<?php

namespace Drupal\unccd_event_map\Utils;

/**
 * Class to retrieve the lattitude/longitude for an address
 * @todo Document
 */
class Geocoder {

    public function geocode($city, $country) {
        return $this->mapQuestGeocode($city, $country);
    }

    private function mapQuestGeocode($city, $country) {
        $coords = [];
        if (!empty($city)) $address = "$city, $country";
        else $address = $country;
        $address = urlencode($address);
        $key = "zBAHAkvKQrtd7m8KF4kk3uRnjUF0kQ2j";
        $json = file_get_contents("https://www.mapquestapi.com/geocoding/v1/address?key={$key}&inFormat=kvp&outFormat=json&location={$address}&thumbMaps=false&maxResults=1");
        $json = json_decode($json);

        $coords['lat'] = $json->results[0]->locations[0]->latLng->lat;
        $coords['long'] = $json->results[0]->locations[0]->latLng->lng;

        return $coords;
    }

    private function googleGeocode($city, $country) {
        $coords = [];
        $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address={$city}&sensor=false&region={$country}");
        $json = json_decode($json);

        $coords['lat'] = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
        $coords['long'] = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};

        return $coords;
    }
}