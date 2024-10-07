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

class UCBrowserNew
{
    public function __construct($header, &$data)
    {
        $this->data =& $data;

        if (preg_match('/pr\(UCBrowser/u', $header)) {
            if (!$this->data->isBrowser('UC Browser')) {
                $this->data->browser->name = 'UC Browser';
                $this->data->browser->stock = false;
                $this->data->browser->version = null;

                if (preg_match('/pr\(UCBrowser(?:\/([0-9\.]+))/u', $header, $match)) {
                    $this->data->browser->version = new Version(['value' => $match[1], 'details' => 2]);
                }
            }
        }

        /* Find os */
        if (preg_match('/pf\(Java\)/u', $header)) {
            if (preg_match('/dv\(([^\)]*)\)/u', $header, $match)) {
                if ($this->identifyBasedOnModel($match[1])) {
                    return;
                }
            }
        }

        if (preg_match('/pf\(Linux\)/u', $header) && preg_match('/ov\((?:Android )?([0-9\.]+)/u', $header, $match)) {
            $this->data->os->name = 'Android';
            $this->data->os->version = new Version(['value' => $match[1]]);
        }

        if (preg_match('/pf\(Symbian\)/u', $header) && preg_match('/ov\(S60V([0-9])/u', $header, $match)) {
            if (!$this->data->isOs('Series60')) {
                $this->data->os->name = 'Series60';
                $this->data->os->version = new Version(['value' => $match[1]]);
            }
        }

        if (preg_match('/pf\(Windows\)/u', $header) && preg_match('/ov\(wds ([0-9]+\.[0-9]+)/u', $header, $match)) {
            if (!$this->data->isOs('Windows Phone')) {
                $this->data->os->name = 'Windows Phone';

                switch ($match[1]) {
                    case '7.1':
                        $this->data->os->version = new Version(['value' => '7.5']);
                        break;
                    case '8.0':
                        $this->data->os->version = new Version(['value' => '8.0']);
                        break;
                    case '8.1':
                        $this->data->os->version = new Version(['value' => '8.1']);
                        break;
                    case '10.0':
                        $this->data->os->version = new Version(['value' => '10.0']);
                        break;
                }
            }
        }

        if (preg_match('/pf\((?:42|44)\)/u', $header) && preg_match('/ov\((?:iPh OS )?(?:iOS )?([0-9\_]+)/u', $header, $match)) {
            if (!$this->data->isOs('iOS')) {
                $this->data->os->name = 'iOS';
                $this->data->os->version = new Version(['value' => str_replace('_', '.', $match[1])]);
            }
        }

        /* Find engine */
        if (preg_match('/re\(AppleWebKit\/([0-9\.]+)/u', $header, $match)) {
            $this->data->engine->name = 'Webkit';
            $this->data->engine->version = new Version(['value' => $match[1]]);
        }

        /* Find device */
        if ($this->data->isOs('Android')) {
            if (preg_match('/dv\((.*)\)/uU', $header, $match)) {
                $match[1] = preg_replace("/\s+Build/u", '', $match[1]);
                $device = Data\DeviceModels::identify('android', $match[1]);

                if ($device) {
                    $this->data->device = $device;
                }
            }
        }

        if ($this->data->isOs('Series60')) {
            if (preg_match('/dv\((?:Nokia)?([^\)]*)\)/iu', $header, $match)) {
                $device = Data\DeviceModels::identify('symbian', $match[1]);

                if ($device) {
                    $this->data->device = $device;
                }
            }
        }

        if ($this->data->isOs('Windows Phone')) {
            if (preg_match('/dv\(([^\)]*)\)/u', $header, $match)) {
                $device = Data\DeviceModels::identify('wp', substr(strstr($match[1], ' '), 1));

                if ($device) {
                    $this->data->device = $device;
                }
            }
        }

        if ($this->data->isOs('iOS')) {
            if (preg_match('/dv\(([^\)]*)\)/u', $header, $match)) {
                $device = Data\DeviceModels::identify('ios', $match[1]);

                if ($device) {
                    $this->data->device = $device;
                }
            }
        }
    }

    private function identifyBasedOnModel($model)
    {
        $model = preg_replace('/^Nokia/iu', '', $model);

        $device = Data\DeviceModels::identify('symbian', $model);
        if ($device->identified) {
            $device->identified |= $this->data->device->identified;
            $this->data->device = $device;

            if (!isset($this->data->os->name) || $this->data->os->name != 'Series60') {
                $this->data->os->name = 'Series60';
                $this->data->os->version = null;
                $this->data->os->family = new Family(['name' => 'Symbian']);
            }

            return true;
        }

        $device = Data\DeviceModels::identify('s40', $model);
        if ($device->identified) {
            $device->identified |= $this->data->device->identified;
            $this->data->device = $device;

            if (!isset($this->data->os->name) || $this->data->os->name != 'Series40') {
                $this->data->os->name = 'Series40';
                $this->data->os->version = null;
            }

            return true;
        }

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

        $device = Data\DeviceModels::identify('touchwiz', $model);
        if ($device->identified) {
            $device->identified |= $this->data->device->identified;
            $this->data->device = $device;

            if (!isset($this->data->os->name) || $this->data->os->name != 'Touchwiz') {
                $this->data->os->name = 'Touchwiz';
                $this->data->os->version = null;
            }

            return true;
        }
    }
}
