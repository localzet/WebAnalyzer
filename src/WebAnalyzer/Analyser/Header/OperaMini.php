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

class OperaMini
{
    public function __construct($header, &$data)
    {
        $this->data =& $data;

        $parts = explode(' # ', $header);
        $manufacturer = $parts[0] ?? '';
        $model = $parts[1] ?? '';

        if ($manufacturer != '?' && $model != '?') {
            if ($this->data->device->identified < Constants\Id::PATTERN) {
                if ($this->identifyBasedOnModel($model)) {
                    return;
                }

                $this->data->device->manufacturer = $manufacturer;
                $this->data->device->model = $model;
                $this->data->device->identified = true;
            }
        }
    }

    private function identifyBasedOnModel($model)
    {
        $device = Data\DeviceModels::identify('bada', $model);
        if ($device->identified) {
            $device->identified |= $this->data->device->identified;
            $this->data->device = $device;

            if (!isset($this->data->os->name) || $this->data->os->name != 'Bada') {
                $this->data->os->name = 'Bada';
                $this->data->os->version = null;
            }

            return true;
        }

        $device = Data\DeviceModels::identify('blackberry', $model);
        if ($device->identified) {
            $device->identified |= $this->data->device->identified;
            $this->data->device = $device;

            if (!isset($this->data->os->name) || $this->data->os->name != 'BlackBerry OS') {
                $this->data->os->name = 'BlackBerry OS';
                $this->data->os->version = null;
            }

            return true;
        }

        $device = Data\DeviceModels::identify('wm', $model);
        if ($device->identified) {
            $device->identified |= $this->data->device->identified;
            $this->data->device = $device;

            if (!isset($this->data->os->name) || $this->data->os->name != 'Windows Mobile') {
                $this->data->os->name = 'Windows Mobile';
                $this->data->os->version = null;
            }

            return true;
        }
    }
}
