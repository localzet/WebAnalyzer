<?php

namespace localzet\WebAnalyzer\Analyser\Header\Useragent\Device;

use localzet\WebAnalyzer\Constants;

trait Gps
{
    private function detectGps($ua)
    {
        if (!preg_match('/Nuvi/ui', $ua)) {
            return;
        }

        $this->detectGarmin($ua);
    }


    /* Garmin Nuvi */

    private function detectGarmin($ua)
    {
        if (preg_match('/Nuvi/u', $ua) && preg_match('/Qtopia/u', $ua)) {
            $this->data->device->setIdentification([
                'manufacturer' => 'Garmin',
                'model' => 'Nuvi',
                'type' => Constants\DeviceType::GPS
            ]);
        }
    }
}
