<?php

namespace Drupal\unccd_event_map\Utils;

class ContinentSpliter {

    private $africa_countries = [
        "Algeria", "Angola", "Benin", "Botswana", "Burkina Faso", "Burundi", "Cameroon", "Cape Verde", "Central African Republic",
        "Chad", "Comoros", "Congo", "The Democratic Republic of The Congo", "Cote D'ivoire", "Djibouti", "Egypt", "Equatorial Guinea",
        "Eritrea", "Ethiopia", "Gabon", "Gambia", "Ghana", "Guinea", "Guinea-bissau", "Kenya", "Lesotho", "Liberia", "Libya", 
        "Madagascar","Malawi", "Mali", "Mauritania", "Mauritius", "Mayotte", "Morocco", "Mozambique", "Namibia", "Niger", "Nigeria",
        "Reunion", "Rwanda", "Saint Helena", "Sao Tome and Principe", "Senegal", "Seychelles", "Sierra Leone", "Somalia", "South Africa",
        "Sudan", "Swaziland", "Tanzania", "Togo", "Tunisia", "Uganda", "Western Sahara", "Zambia", "Zimbabwe"
    ];

    private $asia_countries = [
        "Afghanistan", "Bahrain", "Bangladesh", "Bhutan", "British Indian Ocean Territory", "Brunei Darussalam", "Cambodia",
        "China", "Christmas Island", "Cocos (Keeling) Islands", "Hong Kong", "India", "Indonesia", "Iran", "Iraq", "Israel",
        "Japan", "Jordan", "Kazakhstan", "Democratic People's Republic of Korea", "Republic of Korea", "Kuwait", "Kyrgyzstan",
        "Lao People's Democratic Republic", "Lebanon", "Macao", "Malaysia", "Maldives", "Mongolia", "Myanmar", "Nepal", "Oman", "Pakistan",
        "Palestine", "Philippines", "Qatar", "Saudi Arabia", "Singapore", "Sri Lanka", "Syrian Arab Republic", "Taiwan", "Tajikistan",
        "Thailand", "Timor-leste", "Turkey", "Turkmenistan", "United Arab Emirates", "Uzbekistan", "Vietnam", "Yemen"
    ];

    private $europe_countries = [
        "Åland Islands", "Albania", "Andorra", "Armenia", "Austria", "Azerbaijan", "Belarus", "Belgium", "Bosnia and Herzegovina",
        "Bulgaria", "Croatia", "Czech Republic", "Denmark", "Estonia", "European Union", "Faroe Islands", "Finland", "France", "Germany", "Gibraltar",
        "Greece", "Greenland", "Guernsey", "Vatican", "Cyprus", "Georgia", "Hungary", "Iceland", "Ireland", "Isle of Man", "Italy",
        "Jersey", "Latvia",  "Liechtenstein", "Lithuania", "Luxembourg", "Macedonia", "Malta", "Moldova", "Monaco", "Montenegro",
        "Netherlands", "Norway", "Poland", "Portugal", "Romania", "Russian Federation", "San Marino", "Serbia", "Slovakia", "Slovenia",
        "Spain", "Svalbard and Jan Mayen", "Sweden", "Switzerland", "Ukraine", "United Kingdom"
    ];

    private $oceania_countries = [
        "American Samoa", "Australia", "Cook Islands", "Fiji", "French Polynesia", "Guam", "Kiribati", "Marshall Islands", "Micronesia",
        "Nauru", "New Caledonia", "New Zealand", "Niue", "Norfolk Island", "Northern Mariana Islands", "Palau", "Papua New Guinea", "Pitcairn",
        "Samoa", "Solomon Islands", "Tokelau", "Tonga", "Tuvalu", "United States Minor Outlying Islands", "Vanuatu", "Wallis and Futuna"
    ];

    private $north_america_countries = [
        "Anguilla", "Antigua and Barbuda", "Aruba", "Bahamas", "Barbados", "Belize", "Bermuda", "Cayman Islands", "Canada", "Costa Rica",
        "Cuba", "Dominica", "Dominican Republic", "El Salvador", "Grenada", "Guadeloupe", "Guatemala", "Haiti", "Honduras",
        "Jamaica", "Martinique", "Mexico", "Montserrat", "Netherlands Antilles", "Nicaragua", "Panama", "Puerto Rico",
        "Saint Kitts and Nevis", "Saint Lucia", "Saint Pierre and Miquelon", "Saint Vincent and The Grenadines", "Trinidad and Tobago", 
        "Turks and Caicos Islands", "United States", "Virgin Islands"
    ];

    private $south_america_countries = [
        "Argentina", "Bolivia", "Brazil", "Chile", "Colombia", "Ecuador", "Falkland Islands (Malvinas)", "French Guiana", "Guyana", "Paraguay",
        "Peru", "Suriname", "Uruguay", "Venezuela"
    ];

    public function determineContinent($country) {
        if (in_array($country, $this->africa_countries)) return "africa";
        if (in_array($country, $this->asia_countries)) return "asia";
        if (in_array($country, $this->europe_countries)) return "europe";
        if (in_array($country, $this->oceania_countries)) return "oceania";
        if (in_array($country, $this->north_america_countries)) return "north_america";
        if (in_array($country, $this->south_america_countries)) return "south_america";
        return "other";
    }

    public function test($events) {
        return in_array($events[0]->country, $africa_countries);
    }

    public function splitByContinent($events) {
        $classified = [ "africa" => [], "asia" => [], "europe" => [], "oceania" => [], "north_america" => [], "south_america" => []];

        foreach($events as $event) {
            $continent = $this->determineContinent($event->country);
            $classified[$continent][] = $event;
        }

        return $classified;
    }
}    

