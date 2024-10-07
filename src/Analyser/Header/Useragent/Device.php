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

namespace localzet\WebAnalyzer\Analyser\Header\Useragent;

trait Device
{
    use Device\Appliance, Device\Cars, Device\Gps, Device\Gaming, Device\Ereader,
        Device\Mobile, Device\Media, Device\Television, Device\Signage,
        Device\Printer, Device\Tablet, Device\Phone, Device\Pda;

    private function &detectDevice($ua)
    {
        $this->detectAppliance($ua);
        $this->detectCars($ua);
        $this->detectGps($ua);
        $this->detectEreader($ua);
        $this->detectGaming($ua);
        $this->detectTelevision($ua);
        $this->detectSignage($ua);
        $this->detectMedia($ua);
        $this->detectPda($ua);
        $this->detectPrinter($ua);
        $this->detectTablet($ua);
        $this->detectPhone($ua);
        $this->detectMobile($ua);

        return $this;
    }
}
