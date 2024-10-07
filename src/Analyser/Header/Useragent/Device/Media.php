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

namespace localzet\WebAnalyzer\Analyser\Header\Useragent\Device;

use localzet\WebAnalyzer\Constants;
use localzet\WebAnalyzer\Model\Version;

trait Media
{
    private function detectMedia($ua)
    {
        if (!preg_match('/(Archos|Zune|Walkman)/ui', $ua)) {
            return;
        }

        $this->detectArchos($ua);
        $this->detectZune($ua);
        $this->detectWalkman($ua);
    }


    /* Archos Generation 4, 5 and 6 */

    private function detectArchos($ua)
    {
        /* Generation 4 */

        if (preg_match('/Archos A([67]04)WIFI\//u', $ua, $match)) {
            $this->os->reset();
            $this->device->setIdentification([
                'manufacturer' => 'Archos',
                'model' => $match[1] . ' WiFi',
                'type' => Constants\DeviceType::MEDIA
            ]);
        }

        /* Generation 5 */

        if (preg_match('/ARCHOS; GOGI; a([67]05f?);/u', $ua, $match)) {
            $this->os->reset();
            $this->device->setIdentification([
                'manufacturer' => 'Archos',
                'model' => $match[1] . ' WiFi',
                'type' => Constants\DeviceType::MEDIA
            ]);
        }

        /* Generation 6 without Android */

        if (preg_match('/ARCHOS; GOGI; G6-?(S|H|L|3GP);/u', $ua, $match)) {
            $this->os->reset();
            $this->device->setIdentification([
                'manufacturer' => 'Archos',
                'type' => Constants\DeviceType::MEDIA
            ]);

            switch ($match[1]) {
                case '3GP':
                    $this->device->model = '5 3G+';
                    break;
                case 'S':
                case 'H':
                    $this->device->model = '5';
                    break;
                case 'L':
                    $this->device->model = '7';
                    break;
            }
        }

        /* Generation 6 with Android */

        if (preg_match('/ARCHOS; GOGI; A5[SH]; Version ([0-9]\.[0-9])/u', $ua, $match)) {
            $version = new Version(['value' => $match[1]]);

            $this->os->reset([
                'name' => 'Android',
                'version' => new Version(['value' => $version->is('<', '1.7') ? '1.5' : '1.6'])
            ]);

            $this->device->setIdentification([
                'manufacturer' => 'Archos',
                'model' => '5',
                'type' => Constants\DeviceType::MEDIA
            ]);
        }
    }


    /* Microsoft Zune */

    private function detectZune($ua)
    {
        if (preg_match('/Microsoft ZuneHD/u', $ua)) {
            $this->os->reset();
            $this->device->setIdentification([
                'manufacturer' => 'Microsoft',
                'model' => 'Zune HD',
                'type' => Constants\DeviceType::MEDIA
            ]);
        }
    }


    /* Sony Walkman */

    private function detectWalkman($ua)
    {
        if (preg_match('/Walkman\/(NW-[A-Z0-9]+)/u', $ua, $match)) {
            $this->device->setIdentification([
                'manufacturer' => 'Sony',
                'model' => $match[1] . ' Walkman',
                'type' => Constants\DeviceType::MEDIA
            ]);
        }
    }
}
