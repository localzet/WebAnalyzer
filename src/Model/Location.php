<?php

namespace Triangle\WebAnalyzer\Model;

class Location
{
    public ?string $city = null;
    public ?string $country = null;
    public ?string $country_code = null;
    public ?string $timezone = null;
    public ?array $subdivisions = null;

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
            'country' => $this->country,
            'country_code' => $this->country_code,
            'timezone' => $this->timezone,
            'subdivisions' => $this->subdivisions,
        ];
    }
}
