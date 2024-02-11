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

namespace localzet\WebAnalyzer\Model;

class Location
{
    public ?string $city = null;

    public ?float $latitude = null;
    public ?float $longitude = null;

    public ?string $timezone = null;
    public ?string $subdivision = null;
    public ?array $subdivisions = null;

    public ?array $city_traits = null;

    public ?string $continent = null;
    public ?string $continent_code = null;

    public ?string $country = null;
    public ?string $country_code = null;

    public ?array $country_traits = null;

    public ?int $asn = null;
    public ?string $aso = null;
    public ?string $network = null;

    /**
     * Get an array of all defined properties
     *
     * @return array
     * @internal
     *
     */

    public function toArray()
    {
        return [
            'city' => $this->city,

            'latitude' => $this->latitude,
            'longitude' => $this->longitude,

            'timezone' => $this->timezone,
            'subdivision' => $this->subdivision,
            'subdivisions' => $this->subdivisions,

            'city_traits' => $this->city_traits,

            'continent' => $this->continent,
            'continent_code' => $this->continent_code,

            'country' => $this->country,
            'country_code' => $this->country_code,

            'country_traits' => $this->country_traits,

            'asn' => $this->asn,
            'aso' => $this->aso,
            'network' => $this->network,
        ];
    }
}
