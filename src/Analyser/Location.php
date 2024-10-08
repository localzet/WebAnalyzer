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
            try {
                $GeoLite2_City = new Reader(__DIR__ . '/../../data/GeoLite2-City.mmdb', ['ru', 'en']);
                $location = $GeoLite2_City->city($ip) ?? null;

                $this->location->city = $location->city?->name ?? null;
                $this->location->continent = $location->continent?->name ?? null;
                $this->location->continent_code = $location->continent?->code ?? null;
                $this->location->country = $location->country?->name ?? null;
                $this->location->country_code = $location->country?->isoCode ?? null;
                $this->location->accuracy_radius = $location->location->accuracyRadius ?? null;
                $this->location->latitude = $location->location->latitude ?? null;
                $this->location->longitude = $location->location->longitude ?? null;
                $this->location->time_zone = $location->location->timeZone ?? null;
                $this->location->postal_code = $location->postal?->code ?? null;

                foreach ($location->subdivisions ?? [] as $subdivision) {
                    $this->location->subdivisions[] = (object)[
                        'name' => $subdivision->name ?? null,
                        'code' => $subdivision->isoCode ?? null,
                    ];
                }
            } catch (Throwable) {
                /* :) */
            }
        }

        return $this;
    }
}