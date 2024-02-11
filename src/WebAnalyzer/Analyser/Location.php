<?php
/*
 * @package     Localzet WebAnalyzer library
 * @link        https://github.com/localzet/WebAnalyzer
 *
 * @author      Ivan Zorin <creator@localzet.com>
 * @copyright   Copyright (c) 2018-2024 Zorin Projects S.P.
 * @license     https://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License v3.0
 *
 *              This program is free software: you can redistribute it and/or modify
 *              it under the terms of the GNU Affero General Public License as published
 *              by the Free Software Foundation, either version 3 of the License, or
 *              (at your option) any later version.
 *
 *              This program is distributed in the hope that it will be useful,
 *              but WITHOUT ANY WARRANTY; without even the implied warranty of
 *              MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *              GNU Affero General Public License for more details.
 *
 *              You should have received a copy of the GNU Affero General Public License
 *              along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 *              For any questions, please contact <creator@localzet.com>
 */

namespace localzet\WebAnalyzer\Analyser;

use GeoIp2\Database\Reader;
use Throwable;

trait Location
{
    public function &analyseLocation(): static
    {
        $ip = $this->headers['x-real-ip'] ??
            $this->headers['x-forwarded-for'] ??
            $this->headers['client-ip'] ??
            $this->headers['x-client-ip'] ??
            $this->headers['remote-addr'] ??
            $this->headers['via'] ?? null;


        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->analyseCity($ip);
            $this->analyseCountry($ip);
            $this->analyseASN($ip);
        }

        return $this;
    }

    private function analyseCity($ip)
    {
        try {
            $GeoLite2_City = new Reader(__DIR__ . '/../data/GeoLite2-City.mmdb', ['ru', 'en']);
            $city = $GeoLite2_City->city($ip);

            $this->data->location->city = $city->city->name ?? null;

            $this->data->location->latitude = $city->location->latitude ?? null;
            $this->data->location->longitude = $city->location->longitude ?? null;

            $this->data->location->timezone = $city->location->timeZone ?? null;
            $this->data->location->subdivision = $city->mostSpecificSubdivision->name ?? null;

            foreach ($city->subdivisions ?? [] as $subdivision) {
                $this->data->location->subdivisions[] = $subdivision?->jsonSerialize();
            }

            $this->data->location->city_traits = $city->traits->jsonSerialize() ?? null;
        } catch (Throwable) {
            /* :) */
        }
    }

    private function analyseCountry($ip)
    {
        try {
            $GeoLite2_Country = new Reader(__DIR__ . '/../data/GeoLite2-Country.mmdb', ['ru', 'en']);
            $country = $GeoLite2_Country->country($ip);

            $this->data->location->continent = $country->continent->name ?? null;
            $this->data->location->continent_code = $country->continent->code ?? null;

            $this->data->location->country = $country->country->name ?? null;
            $this->data->location->country_code = $country->country->isoCode ?? null;

            $this->data->location->country_traits = $country->traits->jsonSerialize() ?? null;
        } catch (Throwable) {
            /* :) */
        }
    }

    private function analyseASN($ip)
    {
        try {
            $GeoLite2_ASN = new Reader(__DIR__ . '/../data/GeoLite2-ASN.mmdb', ['ru', 'en']);
            $asn = $GeoLite2_ASN->asn($ip);

            $this->data->location->asn = $asn->autonomousSystemNumber ?? null;
            $this->data->location->aso = $asn->autonomousSystemOrganization ?? null;
            $this->data->location->network = $asn->network ?? null;
        } catch (Throwable) {
            /* :) */
        }
    }
}