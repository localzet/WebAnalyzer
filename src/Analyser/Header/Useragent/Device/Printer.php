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

trait Printer
{
    private function detectPrinter($ua)
    {
        if (!preg_match('/(TASKalfa|CanonIJCL|IR-S|PrintSmart|EpsonHello)/ui', $ua)) {
            return;
        }

        /* TASKalfa */

        if (preg_match('/TASKalfa ([0-9A-Z]+)/iu', $ua, $match)) {
            $this->device->setIdentification([
                'manufacturer' => 'Kyocera',
                'model' => 'TASKalfa ' . $match[1],
                'type' => Constants\DeviceType::PRINTER
            ]);
        }


        /* Canon IJ */

        if (preg_match('/CanonIJCL/iu', $ua, $match)) {
            $this->device->setIdentification([
                'manufacturer' => 'Canon',
                'model' => 'IJ Printer',
                'type' => Constants\DeviceType::PRINTER
            ]);
        }

        /* Canon iR S */

        if (preg_match('/IR-S/iu', $ua, $match)) {
            $this->device->setIdentification([
                'manufacturer' => 'Canon',
                'model' => 'imageRUNNER',
                'type' => Constants\DeviceType::PRINTER
            ]);
        }

        /* HP Web PrintSmart */

        if (preg_match('/HP Web PrintSmart/iu', $ua, $match)) {
            $this->device->setIdentification([
                'manufacturer' => 'HP',
                'model' => 'Web PrintSmart',
                'type' => Constants\DeviceType::PRINTER
            ]);
        }

        /* Epson Hello */

        if (preg_match('/EpsonHello\//iu', $ua, $match)) {
            $this->device->setIdentification([
                'manufacturer' => 'Epson',
                'model' => 'Hello',
                'type' => Constants\DeviceType::PRINTER
            ]);
        }
    }
}
