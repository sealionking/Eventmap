<?php
namespace Drupal\unccd_event_map\Utils;

/**
 * Class to map countries to continents and other related functionality
 * @author Vladimir Metelitsa <me@greencat.io>
 */
class ContinentSpliter {

    private $africa_countries = [
        "Algeria", "Angola", "Benin", "Botswana", "Burkina Faso", "Burundi", "Cameroon", "Cabo Verde", "Central African Republic",
        "Chad", "Comoros", "Republic of Congo", "Democratic Republic of The Congo", "Côte d'Ivoire", "Djibouti", "Egypt",
        "Equatorial Guinea", "Eritrea", "Ethiopia", "Gabon", "Gambia", "Ghana", "Guinea", "Guinea-bissau", "Kenya", "Lesotho", "Liberia",
        "Libya",  "Madagascar", "Malawi", "Mali", "Mauritania", "Mauritius", "Mayotte", "Morocco", "Mozambique", "Namibia", "Niger", "Nigeria",
        "Reunion", "Rwanda", "Saint Helena", "Sao Tome and Principe", "Senegal", "Seychelles", "Sierra Leone", "Somalia", "South Africa",
        "Sudan", "South Sudan", "Eswatini", "United Republic of Tanzania", "Togo", "Tunisia", "Uganda", "Western Sahara", "Zambia", "Zimbabwe"
    ];

    private $asia_countries = [
        "Afghanistan", "Bahrain", "Bangladesh", "Bhutan", "British Indian Ocean Territory", "Brunei Darussalam", "Cambodia",
        "China", "Christmas Island", "Cocos (Keeling) Islands", "Hong Kong", "India", "Indonesia", "Iran", "Iraq", "Israel",
        "Japan", "Jordan", "Kazakhstan", "Democratic People's Republic of Korea", "Republic of Korea", "Kuwait", "Kyrgyzstan",
        "Lao People's Democratic Republic", "Lebanon", "Macao", "Malaysia", "Maldives", "Mongolia", "Myanmar", "Nepal", "Oman",
        "Pakistan", "State of Palestine", "Philippines", "Qatar", "Saudi Arabia", "Singapore", "Sri Lanka", "Syrian Arab Republic", "Taiwan",
        "Tajikistan", "Thailand", "Timor-Leste", "Turkmenistan", "United Arab Emirates", "Uzbekistan", "Viet Nam", "Yemen",
        "American Samoa", "Cook Islands", "Fiji", "French Polynesia", "Guam", "Kiribati", "Marshall Islands", "Micronesia",
        "Nauru", "New Caledonia", "Niue", "Norfolk Island", "Northern Mariana Islands", "Palau", "Papua New Guinea", "Pitcairn",
        "Samoa", "Solomon Islands", "Tokelau", "Tonga", "Tuvalu", "United States Minor Outlying Islands", "Vanuatu", "Wallis and Futuna"
    ];

    private $europe_countries = [
        "Åland Islands", "Albania", "Andorra", "Armenia", "Austria", "Azerbaijan", "Belarus", "Belgium", "Bosnia and Herzegovina",
        "Bulgaria", "Croatia", "Czech Republic", "Denmark", "Estonia", "European Union", "Faroe Islands", "Finland", "France", "Germany", 
        "Gibraltar", "Greece", "Greenland", "Guernsey", "Vatican", "Cyprus", "Georgia", "Hungary", "Iceland", "Ireland", "Isle of Man",
        "Italy", "Jersey", "Latvia",  "Liechtenstein", "Lithuania", "Luxembourg", "The former Yugoslav Republic of Macedonia", "Malta", "Republic of Moldova", "Monaco", "Montenegro",
        "Netherlands", "Norway", "Poland", "Portugal", "Romania", "Russian Federation", "San Marino", "Serbia", "Slovakia",
        "Slovenia", "Spain", "Svalbard and Jan Mayen", "Sweden", "Switzerland", "Turkey", "Ukraine", "United Kingdom of Great Britain and Northern Ireland"
    ];

    private $lac_countries = [
        "Anguilla", "Antigua and Barbuda", "Aruba", "Bahamas", "Barbados", "Belize", "Bermuda", "Cayman Islands",
        "Costa Rica", "Cuba", "Dominica", "Dominican Republic", "El Salvador", "Grenada", "Guadeloupe", "Guatemala", "Haiti", "Honduras",
        "Jamaica", "Martinique", "Mexico", "Montserrat", "Netherlands Antilles", "Nicaragua", "Panama", "Puerto Rico",
        "St. Kitts and Nevis", "St. Lucia", "Saint Pierre and Miquelon", "St. Vincent and the Grenadines", "Trinidad and Tobago", 
        "Turks and Caicos Islands", "Virgin Islands", "Argentina", "Bolivia", "Brazil", "Chile", "Colombia", "Ecuador", "Falkland Islands (Malvinas)",
        "French Guiana", "Guyana", "Paraguay", "Peru", "Suriname", "Uruguay", "Venezuela"
    ];

    /**
     * Returns the continent a country is in
     * @param string $country
     * @return string The continent the country is located in
     */
    public function determineContinent($country) {
        if (in_array($country, $this->africa_countries)) return "africa";
        if (in_array($country, $this->asia_countries)) return "asia";
        if (in_array($country, $this->europe_countries)) return "europe";
        if (in_array($country, $this->lac_countries)) return "lac";
        return "other";
    }

    /**
     * Puts events into arrays based on their continent
     * @param array $events An array of events to classify
     * @return array Classified events
     */
    public function splitByContinent($events) {
        $classified = [ "africa" => [], "asia" => [], "europe" => [], "lac" => []];

        foreach($events as $event) {
            $continent = $this->determineContinent($event->country);
            $classified[$continent][] = $event;
        }

        return $classified;
    }

    /**
     * Makes a list of list of countries split by continent
     * For use in form select inputs
     */
    public function generateCountryOptionList() {
        sort($this->africa_countries);
        sort($this->asia_countries);
        sort($this->europe_countries);
        sort($this->lac_countries);

        return [
            "Africa" => array_combine($this->africa_countries, $this->africa_countries),
            "Asia/Pacific" => array_combine($this->asia_countries, $this->asia_countries),
            "Europe" => array_combine($this->europe_countries, $this->europe_countries),
            "LAC" => array_combine($this->lac_countries, $this->lac_countries),
            'Other' => 'Other'
        ];
    }
}    

