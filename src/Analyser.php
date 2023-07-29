<?php

namespace Triangle\WebAnalyzer;

use MaxMind\Db\Reader;
use Throwable;
use Triangle\WebAnalyzer\Model\Main;

class Analyser
{
    use Analyser\Header, Analyser\Derive, Analyser\Corrections, Analyser\Camouflage;

    private $data;

    private $options;

    private $headers = [];

    public function __construct($headers, $options = [])
    {
        $this->headers = $headers;
        $this->options = (object)$options;
    }

    public function setData(&$data)
    {
        $this->data =& $data;
    }

    public function &getData()
    {
        return $this->data;
    }

    public function analyse()
    {
        if (!isset($this->data)) {
            $this->data = new Main();
        }

        /* Start the actual analysing steps */

        $this->analyseHeaders()
            ->analyseLocation()
            ->deriveInformation()
            ->applyCorrections()
            ->detectCamouflage()
            ->deriveDeviceSubType();
    }

    public function analyseLocation() {
        try {
            // $maxmind = new Reader(base_path() . '/resources/GeoLite2-Country.mmdb');
            $maxmind = new Reader(__DIR__ . '/../data/GeoLite2-City.mmdb');
            $record = $maxmind->get(getRequestIp());
        } catch (Throwable) {
            /* :) */
        }

        $this->data->location->city = $record['city']['names']['ru'] ?? $record['city']['names']['en'] ?? null;
        $this->data->location->country = $record['country']['names']['ru'] ?? $record['country']['names']['en'] ?? null;
        $this->data->location->country_code = $record['country']['iso_code'] ?? null;
        $this->data->location->timezone = $record['location']['time_zone'] ?? null;
        $this->data->location->subdivisions = $record['subdivisions'] ?? null;

        return $this;
    }
}
