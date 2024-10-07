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

use localzet\WebAnalyzer\Data;
use localzet\WebAnalyzer\Model\Family;
use localzet\WebAnalyzer\Model\Version;

trait UCBrowserNew
{
    public function analyseNewUCUserAgent($header)
    {
        if (preg_match('/pr\(UCBrowser/u', $header)) {
            if (!$this->isBrowser('UC Browser')) {
                $this->browser->name = 'UC Browser';
                $this->browser->stock = false;
                $this->browser->version = null;

                if (preg_match('/pr\(UCBrowser(?:\/([0-9\.]+))/u', $header, $match)) {
                    $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
                }
            }
        }

        /* Find os */
        if (preg_match('/pf\(Java\)/u', $header)) {
            if (preg_match('/dv\(([^\)]*)\)/u', $header, $match)) {
                if ($this->identifyBasedOnModelUCBrowserNew($match[1])) {
                    return;
                }
            }
        }

        if (preg_match('/pf\(Linux\)/u', $header) && preg_match('/ov\((?:Android )?([0-9\.]+)/u', $header, $match)) {
            $this->os->name = 'Android';
            $this->os->version = new Version(['value' => $match[1]]);
        }

        if (preg_match('/pf\(Symbian\)/u', $header) && preg_match('/ov\(S60V([0-9])/u', $header, $match)) {
            if (!$this->isOs('Series60')) {
                $this->os->name = 'Series60';
                $this->os->version = new Version(['value' => $match[1]]);
            }
        }

        if (preg_match('/pf\(Windows\)/u', $header) && preg_match('/ov\(wds ([0-9]+\.[0-9]+)/u', $header, $match)) {
            if (!$this->isOs('Windows Phone')) {
                $this->os->name = 'Windows Phone';

                switch ($match[1]) {
                    case '7.1':
                        $this->os->version = new Version(['value' => '7.5']);
                        break;
                    case '8.0':
                        $this->os->version = new Version(['value' => '8.0']);
                        break;
                    case '8.1':
                        $this->os->version = new Version(['value' => '8.1']);
                        break;
                    case '10.0':
                        $this->os->version = new Version(['value' => '10.0']);
                        break;
                }
            }
        }

        if (preg_match('/pf\((?:42|44)\)/u', $header) && preg_match('/ov\((?:iPh OS )?(?:iOS )?([0-9\_]+)/u', $header, $match)) {
            if (!$this->isOs('iOS')) {
                $this->os->name = 'iOS';
                $this->os->version = new Version(['value' => str_replace('_', '.', $match[1])]);
            }
        }

        /* Find engine */
        if (preg_match('/re\(AppleWebKit\/([0-9\.]+)/u', $header, $match)) {
            $this->engine->name = 'Webkit';
            $this->engine->version = new Version(['value' => $match[1]]);
        }

        /* Find device */
        if ($this->isOs('Android')) {
            if (preg_match('/dv\((.*)\)/uU', $header, $match)) {
                $match[1] = preg_replace("/\s+Build/u", '', $match[1]);
                $device = Data\DeviceModels::identify('android', $match[1]);

                if ($device) {
                    $this->device = $device;
                }
            }
        }

        if ($this->isOs('Series60')) {
            if (preg_match('/dv\((?:Nokia)?([^\)]*)\)/iu', $header, $match)) {
                $device = Data\DeviceModels::identify('symbian', $match[1]);

                if ($device) {
                    $this->device = $device;
                }
            }
        }

        if ($this->isOs('Windows Phone')) {
            if (preg_match('/dv\(([^\)]*)\)/u', $header, $match)) {
                $device = Data\DeviceModels::identify('wp', substr(strstr($match[1], ' '), 1));

                if ($device) {
                    $this->device = $device;
                }
            }
        }

        if ($this->isOs('iOS')) {
            if (preg_match('/dv\(([^\)]*)\)/u', $header, $match)) {
                $device = Data\DeviceModels::identify('ios', $match[1]);

                if ($device) {
                    $this->device = $device;
                }
            }
        }
    }

    private function identifyBasedOnModelUCBrowserNew($model)
    {
        $model = preg_replace('/^Nokia/iu', '', $model);

        $device = Data\DeviceModels::identify('symbian', $model);
        if ($device->identified) {
            $device->identified |= $this->device->identified;
            $this->device = $device;

            if (!isset($this->os->name) || $this->os->name != 'Series60') {
                $this->os->name = 'Series60';
                $this->os->version = null;
                $this->os->family = new Family(['name' => 'Symbian']);
            }

            return true;
        }

        $device = Data\DeviceModels::identify('s40', $model);
        if ($device->identified) {
            $device->identified |= $this->device->identified;
            $this->device = $device;

            if (!isset($this->os->name) || $this->os->name != 'Series40') {
                $this->os->name = 'Series40';
                $this->os->version = null;
            }

            return true;
        }

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

        $device = Data\DeviceModels::identify('touchwiz', $model);
        if ($device->identified) {
            $device->identified |= $this->device->identified;
            $this->device = $device;

            if (!isset($this->os->name) || $this->os->name != 'Touchwiz') {
                $this->os->name = 'Touchwiz';
                $this->os->version = null;
            }

            return true;
        }
    }
}
