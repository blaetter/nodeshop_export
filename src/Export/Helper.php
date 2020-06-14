<?php

namespace Drupal\nodeshop_export\Export;

use Drupal\nodeshop\Entity\Order;

class Helper
{
    /**
     * This returns a value out of the order object if it is present
     *
     * @param Order  $entity A reference of the entity data
     * @param string $key  The key of the requested value
     *
     * @return mixed
     *  The requested key if present, empty string if not
     */
    public static function getValue($entity, $key = '')
    {
        if (!empty($key) && $entity->hasField($key)) {
            return $entity->get($key)->value;
        }
        return '';
    }

    /**
     * Validates the county
     *
     * @param string $value The Value of the country field
     *
     * @return string
     *  DE if value is missing or eq Deutschland, else the
     *  appropriate country code
     */
    public static function getCountryCode($value)
    {
        // trim the value so that no whitespace can cause problems
        $value = trim($value);

        // if there is no value given or its "deutschland", return "DE" as default
        if (mb_strtolower($value, 'UTF-8') == 'deutschland' || empty($value)) {
            return 'DE';
        }

        //
        $countries = array(
            "afghanistan"                                   =>  "AF",
            "ägypten"                                       =>  "EG",
            "albanien"                                      =>  "AL",
            "algerien"                                      =>  "DZ",
            "amerikanische jungferninseln"                  =>  "VI",
            "amerikanisch-samoa"                            =>  "AS",
            "andorra"                                       =>  "AD",
            "angola"                                        =>  "AO",
            "anguilla"                                      =>  "AI",
            "antarktika"                                    =>  "AQ",
            "antigua und barbu"                             =>  "da",
            "äquatorialguinea"                              =>  "GQ",
            "argentinien"                                   =>  "AR",
            "armenien"                                      =>  "AM",
            "aruba"                                         =>  "AW",
            "ascension"                                     =>  "AC",
            "aserbaidschan"                                 =>  "AZ",
            "äthiopien"                                     =>  "ET",
            "australien"                                    =>  "AU",
            "bahamas"                                       =>  "BS",
            "bahrain"                                       =>  "BH",
            "bangladesch"                                   =>  "BD",
            "barbados"                                      =>  "BB",
            "belarus"                                       =>  "BY",
            "belgien"                                       =>  "BE",
            "belize"                                        =>  "BZ",
            "benin"                                         =>  "BJ",
            "bermuda"                                       =>  "BM",
            "bhutan"                                        =>  "BT",
            "bolivien"                                      =>  "BO",
            "bonaire, st. eustatius, saba"                  =>  "BQ",
            "bosnien herzegowina"                           =>  "BA",
            "botswana"                                      =>  "BW",
            "bouvetinsel"                                   =>  "BV",
            "brasilien"                                     =>  "BR",
            "britische jungferninseln"                      =>  "VG",
            "britisches territorium im indischen ozean"     =>  "IO",
            "brunei darussalam"                             =>  "BN",
            "bulgarien"                                     =>  "BG",
            "burkina faso"                                  =>  "BF",
            "burundi"                                       =>  "BI",
            "ceuta, melilla"                                =>  "EA",
            "chile"                                         =>  "CL",
            "china, volksrepublik"                          =>  "CN",
            "clipperton"                                    =>  "CP",
            "cookinseln"                                    =>  "CK",
            "costa rica"                                    =>  "CR",
            "côte d'ivoire (elfenbeinküste)"                =>  "CI",
            "curaçao"                                       =>  "CW",
            "dänemark"                                      =>  "DK",
            "deutschland"                                   =>  "DE",
            "diego garcia"                                  =>  "DG",
            "dominica"                                      =>  "DM",
            "dominikanische republik"                       =>  "DO",
            "dschibuti"                                     =>  "DJ",
            "ecuador"                                       =>  "EC",
            "el salvador"                                   =>  "SV",
            "eritrea"                                       =>  "ER",
            "estland"                                       =>  "EE",
            "falklandinseln"                                =>  "FK",
            "färöer"                                        =>  "FO",
            "fidschi"                                       =>  "FJ",
            "finnland"                                      =>  "FI",
            "frankreich"                                    =>  "FR",
            "französische süd- und antarktisgebiete"        =>  "TF",
            "französisch-guayana"                           =>  "GF",
            "französisch-polynesien"                        =>  "PF",
            "gabun"                                         =>  "GA",
            "gambia"                                        =>  "GM",
            "georgien"                                      =>  "GE",
            "ghana"                                         =>  "GH",
            "gibraltar"                                     =>  "GI",
            "grenada"                                       =>  "GD",
            "griechenland"                                  =>  "GR",
            "grönland"                                      =>  "GL",
            "großbritannien"                                =>  "GB",
            "guadeloupe"                                    =>  "GP",
            "guam"                                          =>  "GU",
            "guatemala"                                     =>  "GT",
            "guernsey (kanalinsel)"                         =>  "GG",
            "guinea"                                        =>  "GN",
            "guinea-bissau"                                 =>  "GW",
            "guyana"                                        =>  "GY",
            "haiti"                                         =>  "HT",
            "heard und mcdonaldinseln"                      =>  "HM",
            "honduras"                                      =>  "HN",
            "hongkong"                                      =>  "HK",
            "indien"                                        =>  "IN",
            "indonesien"                                    =>  "ID",
            "insel man"                                     =>  "IM",
            "irak"                                          =>  "IQ",
            "iran, islamische republik"                     =>  "IR",
            "irland"                                        =>  "IE",
            "island"                                        =>  "IS",
            "israel"                                        =>  "IL",
            "italien"                                       =>  "IT",
            "jamaika"                                       =>  "JM",
            "japan"                                         =>  "JP",
            "jemen"                                         =>  "YE",
            "jersey (kanalinsel)"                           =>  "JE",
            "jordanien"                                     =>  "JO",
            "kaimaninseln"                                  =>  "KY",
            "kambodscha"                                    =>  "KH",
            "kamerun"                                       =>  "CM",
            "kanada"                                        =>  "CA",
            "kanarische inseln"                             =>  "IC",
            "kap verde"                                     =>  "CV",
            "kasachstan"                                    =>  "KZ",
            "katar"                                         =>  "QA",
            "kenia"                                         =>  "KE",
            "kirgisistan"                                   =>  "KG",
            "kiribati"                                      =>  "KI",
            "kokosinseln"                                   =>  "CC",
            "kolumbien"                                     =>  "CO",
            "komoren"                                       =>  "KM",
            "kongo, demokratische republik"                 =>  "CD",
            "korea, demokratische volksrepublik (nordkorea)"=>  "KP",
            "korea, republik (südkorea)"                    =>  "KR",
            "kroatien"                                      =>  "HR",
            "kuba"                                          =>  "CU",
            "kuwait"                                        =>  "KW",
            "laos, demokratische volksrepublik"             =>  "LA",
            "lesotho"                                       =>  "LS",
            "lettland"                                      =>  "LV",
            "libanon"                                       =>  "LB",
            "liberia"                                       =>  "LR",
            "libyen"                                        =>  "LY",
            "liechtenstein"                                 =>  "LI",
            "litauen"                                       =>  "LT",
            "lland"                                         =>  "AX",
            "luxemburg"                                     =>  "LU",
            "macao"                                         =>  "MO",
            "madagaskar"                                    =>  "MG",
            "malawi"                                        =>  "MW",
            "malaysia"                                      =>  "MY",
            "malediven"                                     =>  "MV",
            "mali"                                          =>  "ML",
            "malta"                                         =>  "MT",
            "marokko"                                       =>  "MA",
            "marshallinseln"                                =>  "MH",
            "martinique"                                    =>  "MQ",
            "mauretanien"                                   =>  "MR",
            "mauritius"                                     =>  "MU",
            "mayotte"                                       =>  "YT",
            "mazedonien"                                    =>  "MK",
            "mexiko"                                        =>  "MX",
            "mikronesien"                                   =>  "FM",
            "moldawien (republik moldau)"                   =>  "MD",
            "monaco"                                        =>  "MC",
            "mongolei"                                      =>  "MN",
            "montenegro"                                    =>  "ME",
            "montserrat"                                    =>  "MS",
            "mosambik"                                      =>  "MZ",
            "myanmar (burma)"                               =>  "MM",
            "namibia"                                       =>  "NA",
            "nauru"                                         =>  "NR",
            "nepal"                                         =>  "NP",
            "neukaledonien"                                 =>  "NC",
            "neuseeland"                                    =>  "NZ",
            "nicaragua"                                     =>  "NI",
            "niederlande"                                   =>  "NL",
            "niger"                                         =>  "NE",
            "nigeria"                                       =>  "NG",
            "niue"                                          =>  "NU",
            "nördliche marianen"                            =>  "MP",
            "norfolkinsel"                                  =>  "NF",
            "norwegen"                                      =>  "NO",
            "oman"                                          =>  "OM",
            "österreich"                                    =>  "AT",
            "osttimor (timor-leste)"                        =>  "TL",
            "pakistan"                                      =>  "PK",
            "palau"                                         =>  "PW",
            "panama"                                        =>  "PA",
            "papua-neuguinea"                               =>  "PG",
            "paraguay"                                      =>  "PY",
            "peru"                                          =>  "PE",
            "philippinen"                                   =>  "PH",
            "pitcairninseln"                                =>  "PN",
            "polen"                                         =>  "PL",
            "portugal"                                      =>  "PT",
            "puerto rico"                                   =>  "PR",
            "republik china (taiwan)"                       =>  "TW",
            "réunion"                                       =>  "RE",
            "ruanda"                                        =>  "RW",
            "rumänien"                                      =>  "RO",
            "russische föderation"                          =>  "RU",
            "saint-barthélemy"                              =>  "BL",
            "saint-martin (franz. teil)"                    =>  "MF",
            "saint-pierre und miquelon"                     =>  "PM",
            "salomonen"                                     =>  "SB",
            "sambia"                                        =>  "ZM",
            "samoa"                                         =>  "WS",
            "san marino"                                    =>  "SM",
            "sao tomé und príncipe"                         =>  "ST",
            "saudi-arabien"                                 =>  "SA",
            "schweden"                                      =>  "SE",
            "schweiz"                                       =>  "CH",
            "senegal"                                       =>  "SN",
            "serbien"                                       =>  "RS",
            "seychellen"                                    =>  "SC",
            "sierra leone"                                  =>  "SL",
            "simbabwe"                                      =>  "ZW",
            "singapur"                                      =>  "SG",
            "slowakei"                                      =>  "SK",
            "slowenien"                                     =>  "SI",
            "somalia"                                       =>  "SO",
            "spanien"                                       =>  "ES",
            "sri lanka"                                     =>  "LK",
            "st. helena"                                    =>  "SH",
            "st. kitts und nevis"                           =>  "KN",
            "st. lucia"                                     =>  "LC",
            "st. vincent und die grenadinen"                =>  "VC",
            "staat palästina"                               =>  "PS",
            "südafrika"                                     =>  "ZA",
            "sudan"                                         =>  "SD",
            "südgeorgien, südliche sandwichinseln"          =>  "GS",
            "südsudan"                                      =>  "SS",
            "suriname"                                      =>  "SR",
            "svalbard und jan mayen"                        =>  "SJ",
            "swasiland"                                     =>  "SZ",
            "syrien, arabische republik"                    =>  "SY",
            "tadschikistan"                                 =>  "TJ",
            "tansania, vereinigte republik"                 =>  "TZ",
            "thailand"                                      =>  "TH",
            "togo"                                          =>  "TG",
            "tokelau"                                       =>  "TK",
            "tonga"                                         =>  "TO",
            "trinidad und tobago"                           =>  "TT",
            "tschad"                                        =>  "TD",
            "tschechische republik"                         =>  "CZ",
            "tunesien"                                      =>  "TN",
            "türkei"                                        =>  "TR",
            "turkmenistan"                                  =>  "TM",
            "turks- und caicosinseln"                       =>  "TC",
            "tuvalu"                                        =>  "TV",
            "uganda"                                        =>  "UG",
            "ukraine"                                       =>  "UA",
            "ungarn"                                        =>  "HU",
            "uruguay"                                       =>  "UY",
            "usbekistan"                                    =>  "UZ",
            "vanuatu"                                       =>  "VU",
            "vatikanstadt"                                  =>  "VA",
            "venezuela"                                     =>  "VE",
            "vereinigte arabische emirate"                  =>  "AE",
            "vereinigte staaten von amerika"                =>  "US",
            "vietnam"                                       =>  "VN",
            "wallis und futuna"                             =>  "WF",
            "weihnachtsinsel"                               =>  "CX",
            "westsahara"                                    =>  "EH",
            "zentralafrikanische republik"                  =>  "CF",
            "zypern"                                        =>  "CY",
        );

        // now look for the id of the given country - if there is one
        // return 14 in case of no match
        if (isset($countries[mb_strtolower($value, 'UTF-8')])) {
            return $countries[mb_strtolower($value, 'UTF-8')];
        } else {
            return "DE";
        }
    }

    /**
     * Validates the "Anrede" and extracts the male or female title. Everything
     * else like Dr. or Prof. is ignored and exported via the title.
     *
     * @param string $value The value of the field for the persons title
     *
     * @return string
     *  The validated value or an empty string
     */
    public static function getSalutation($value)
    {
        if (false !== strpos($value, 'Frau')) {
            return 'Frau';
        } elseif (false !== strpos($value, 'Herr')) {
            return 'Herr';
        } else {
            return '';
        }
    }

    /**
     * Validates the title to extract titles like Prof. or Dr.
     *
     * @param string $value The value of the field for the persons title
     *
     * @return string
     *  The validated value or an empty string
     */
    public static function getTitle($value)
    {
        if (false !== strpos($value, 'Dr')) {
            return 'Dr.';
        } elseif (false !== strpos($value, 'Prof')) {
            return 'Prof.';
        } else {
            return '';
        }
    }
}
