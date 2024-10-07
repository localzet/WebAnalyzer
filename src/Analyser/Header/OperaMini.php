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

namespace localzet\WebAnalyzer\Analyser\Header;

use localzet\WebAnalyzer\Constants;
use localzet\WebAnalyzer\Data;

trait OperaMini
{
    public function analyseOperaMiniPhone($header)
    {
        $parts = explode(' # ', $header);
        $manufacturer = $parts[0] ?? '';
        $model = $parts[1] ?? '';

        if ($manufacturer != '?' && $model != '?') {
            if ($this->device->identified < Constants\Id::PATTERN) {
                if ($this->identifyBasedOnModelOperaMini($model)) {
                    return;
                }

                $this->device->manufacturer = $manufacturer;
                $this->device->model = $model;
                $this->device->identified = true;
            }
        }
    }

    private function identifyBasedOnModelOperaMini($model)
    {
        $device = Data\DeviceModels::identify('bada', $model);
        if ($device->identified) {
            $device->identified |= $this->device->identified;
            $this->device = $device;

            if (!isset($this->os->name) || $this->os->name != 'Bada') {
                $this->os->name = 'Bada';
                $this->os->version = null;
            }

            return true;
        }

        $device = Data\DeviceModels::identify('blackberry', $model);
        if ($device->identified) {
            $device->identified |= $this->device->identified;
            $this->device = $device;

            if (!isset($this->os->name) || $this->os->name != 'BlackBerry OS') {
                $this->os->name = 'BlackBerry OS';
                $this->os->version = null;
            }

            return true;
        }

        $device = Data\DeviceModels::identify('wm', $model);
        if ($device->identified) {
            $device->identified |= $this->device->identified;
            $this->device = $device;

            if (!isset($this->os->name) || $this->os->name != 'Windows Mobile') {
                $this->os->name = 'Windows Mobile';
                $this->os->version = null;
            }

            return true;
        }
    }
}
