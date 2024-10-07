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

use localzet\WebAnalyzer\Constants;

trait Corrections
{
    private function &applyCorrections()
    {
        if (isset($this->browser->name) && isset($this->browser->using)) {
            $this->hideBrowserBasedOnUsing();
        }

        if (isset($this->browser->name) && isset($this->os->name)) {
            $this->hideBrowserBasedOnOperatingSystem();
        }

        if (isset($this->browser->name) && $this->device->type == Constants\DeviceType::TELEVISION) {
            $this->hideBrowserOnDeviceTypeTelevision();
        }

        if (isset($this->browser->name) && $this->device->type == Constants\DeviceType::GAMING) {
            $this->hideBrowserOnDeviceTypeGaming();
        }

        if ($this->device->type == Constants\DeviceType::TELEVISION) {
            $this->hideOsOnDeviceTypeTelevision();
        }

        if (isset($this->browser->name) && isset($this->engine->name)) {
            $this->fixMidoriEngineName();
        }

        if (isset($this->browser->name) && isset($this->engine->name)) {
            $this->fixNineSkyEngineName();
        }

        if (isset($this->browser->name) && isset($this->browser->family)) {
            $this->hideFamilyIfEqualToBrowser();
        }

        return $this;
    }


    private function hideFamilyIfEqualToBrowser()
    {
        if ($this->browser->name == $this->browser->family->name) {
            unset($this->browser->family);
        }
    }

    private function fixMidoriEngineName()
    {
        if ($this->browser->name == 'Midori' && $this->engine->name != 'Webkit') {
            $this->engine->name = 'Webkit';
            $this->engine->version = null;
        }
    }

    private function fixNineSkyEngineName()
    {
        if ($this->browser->name == 'NineSky' && $this->engine->name != 'Webkit') {
            $this->engine->name = 'Webkit';
            $this->engine->version = null;
        }
    }

    private function hideBrowserBasedOnUsing()
    {
        if ($this->browser->name == 'Chrome') {
            if ($this->browser->isUsing('Electron') || $this->browser->isUsing('Qt')) {
                unset($this->browser->name);
                unset($this->browser->version);
            }
        }
    }

    private function hideBrowserBasedOnOperatingSystem()
    {
        if ($this->os->name == 'Series60' && $this->browser->name == 'Internet Explorer') {
            $this->browser->reset();
            $this->engine->reset();
        }

        if ($this->os->name == 'Series80' && $this->browser->name == 'Internet Explorer') {
            $this->browser->reset();
            $this->engine->reset();
        }

        if ($this->os->name == 'Lindows' && $this->browser->name == 'Internet Explorer') {
            $this->browser->reset();
            $this->engine->reset();
        }

        if ($this->os->name == 'Tizen' && $this->browser->name == 'Chrome') {
            $this->browser->reset([
                'family' => $this->browser->family ?? null
            ]);
        }

        if ($this->os->name == 'Ubuntu Touch' && $this->browser->name == 'Chromium') {
            $this->browser->reset([
                'family' => $this->browser->family ?? null
            ]);
        }

        if ($this->os->name == 'KaiOS' && $this->browser->name == 'Firefox Mobile') {
            $this->browser->reset([
                'family' => $this->browser->family ?? null
            ]);
        }
    }

    private function hideBrowserOnDeviceTypeGaming()
    {
        if (isset($this->device->model) && $this->device->model == 'PlayStation 2' && $this->browser->name == 'Internet Explorer') {
            $this->browser->reset();
        }
    }

    private function hideBrowserOnDeviceTypeTelevision()
    {
        switch ($this->browser->name) {
            case 'Firefox':
                if (!$this->isOs('Firefox OS')) {
                    unset($this->browser->name);
                    unset($this->browser->version);
                }
                break;

            case 'Internet Explorer':
                $valid = false;

                if (isset($this->device->model) && $this->device->model == 'WebTV') {
                    $valid = true;
                }

                if (!$valid) {
                    unset($this->browser->name);
                    unset($this->browser->version);
                }

                break;

            case 'Chrome':
            case 'Chromium':
                $valid = false;

                if (isset($this->os->name) && in_array($this->os->name, ['Google TV', 'Android'])) {
                    $valid = true;
                }
                if (isset($this->device->model) && $this->device->model == 'Chromecast') {
                    $valid = true;
                }

                if (!$valid) {
                    unset($this->browser->name);
                    unset($this->browser->version);
                }

                break;
        }
    }

    private function hideOsOnDeviceTypeTelevision()
    {
        if (isset($this->os->name) && !in_array($this->os->name, ['Aliyun OS', 'Tizen', 'Android', 'Android TV', 'FireOS', 'Google TV', 'Firefox OS', 'OpenTV', 'webOS'])) {
            $this->os->reset();
        }
    }
}
