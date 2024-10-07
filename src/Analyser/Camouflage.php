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
use localzet\WebAnalyzer\Data;
use localzet\WebAnalyzer\Model\Version;

trait Camouflage
{
    private function &detectCamouflage()
    {
        if ($ua = $this->getHeader('User-Agent')) {
            $this
                ->detectCamouflagedAndroidBrowser($ua)
                ->detectCamouflagedAndroidAsusBrowser($ua)
                ->detectCamouflagedAsSafari($ua)
                ->detectCamouflagedAsChrome($ua);
        }

        if (!empty($this->options->useragent)) {
            $this->detectCamouflagedUCBrowser($this->options->useragent);
        }

        if (isset($this->options->engine)) {
            $this->detectCamouflagedBasedOnEngines();
        }

        if (isset($this->options->features)) {
            $this->detectCamouflagedBasedOnFeatures();
        }

        return $this;
    }

    private function &detectCamouflagedAndroidBrowser($ua)
    {
        if (preg_match('/Mac OS X 10_6_3; ([^;]+); [a-z]{2}(?:-[a-z]{2})?\)/u', $ua, $match)) {
            $this->browser->name = 'Android Browser';
            $this->browser->version = null;
            $this->browser->mode = 'desktop';

            $this->os->name = 'Android';
            $this->os->alias = null;
            $this->os->version = null;

            $this->engine->name = 'Webkit';
            $this->engine->version = null;

            $this->device->type = 'mobile';

            $device = Data\DeviceModels::identify('android', $match[1]);
            if ($device->identified) {
                $device->identified |= $this->device->identified;
                $this->device = $device;
            }

            $this->features[] = 'foundDevice';
        }

        if (preg_match('/Mac OS X 10_5_7; [^\/\);]+\/([^\/\);]+)\//u', $ua, $match)) {
            $this->browser->name = 'Android Browser';
            $this->browser->version = null;
            $this->browser->mode = 'desktop';

            $this->os->name = 'Android';
            $this->os->alias = null;
            $this->os->version = null;

            $this->engine->name = 'Webkit';
            $this->engine->version = null;

            $this->device->type = 'mobile';

            $device = Data\DeviceModels::identify('android', $match[1]);
            if ($device->identified) {
                $device->identified |= $this->device->identified;
                $this->device = $device;
            }

            $this->features[] = 'foundDevice';
        }

        return $this;
    }

    private function &detectCamouflagedAndroidAsusBrowser($ua)
    {
        if (preg_match('/Linux Ventana; [a-z]{2}(?:-[a-z]{2})?; (.+) Build/u', $ua, $match)) {
            $this->browser->name = 'Android Browser';
            $this->browser->version = null;
            $this->browser->channel = null;
            $this->browser->mode = 'desktop';

            $this->engine->name = 'Webkit';
            $this->engine->version = null;

            $this->features[] = 'foundDevice';
        }

        return $this;
    }

    private function &detectCamouflagedAsSafari($ua)
    {
        if ($this->isBrowser('Safari') && !preg_match('/Darwin/u', $ua)) {
            if ($this->isOs('iOS') && !preg_match('/^Mozilla/u', $ua)) {
                $this->features[] = 'noMozillaPrefix';
                $this->camouflage = true;
            }

            if (!preg_match('/Version\/[0-9\.]+/u', $ua)) {
                $this->features[] = 'noVersion';
                $this->camouflage = true;
            }
        }

        return $this;
    }

    private function &detectCamouflagedAsChrome($ua)
    {
        if ($this->isBrowser('Chrome')) {
            if (preg_match('/(?:Chrome|CrMo|CriOS)\//u', $ua)
                && !preg_match('/(?:Chrome|CrMo|CriOS)\/([0-9]{1,3}\.[0-9]\.(0\.0|[0-9]{3,4}\.[0-9]+))/u', $ua)
            ) {
                $this->features[] = 'wrongVersion';
                $this->camouflage = true;
            }
        }

        return $this;
    }

    private function &detectCamouflagedUCBrowser($ua)
    {
        if ($ua == 'Mozilla/5.0 (X11; U; Linux i686; zh-CN; rv:1.2.3.4) Gecko/') {
            if (!$this->isBrowser('UC Browser')) {
                $this->browser->name = 'UC Browser';
                $this->browser->version = null;
                $this->browser->stock = false;
            }

            if ($this->isOs('Windows')) {
                $this->os->reset();
            }

            $this->engine->reset(['name' => 'Gecko']);
            $this->device->type = 'mobile';
        }

        if ($this->isBrowser('Chrome')) {
            if (preg_match('/UBrowser\/?([0-9.]*)/u', $ua, $match)) {
                $this->browser->stock = false;
                $this->browser->name = 'UC Browser';
                $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
                $this->browser->type = Constants\BrowserType::BROWSER;
                unset($this->browser->channel);
            }
        }

        return $this;
    }

    private function &detectCamouflagedBasedOnEngines()
    {
        if (isset($this->engine->name) && $this->browser->mode != 'proxy') {
            /* If it claims not to be Trident, but it is probably Trident running camouflage mode */
            if ($this->options->engine & Constants\EngineType::TRIDENT) {
                $this->features[] = 'trident';

                if ($this->engine->name && $this->engine->name != 'Trident') {
                    $this->camouflage = !isset($this->browser->name) || ($this->browser->name != 'Maxthon' && $this->browser->name != 'Motorola WebKit');
                }
            }

            /* If it claims not to be Opera, but it is probably Opera running camouflage mode */
            if ($this->options->engine & Constants\EngineType::PRESTO) {
                $this->features[] = 'presto';

                if ($this->engine->name && $this->engine->name != 'Presto') {
                    $this->camouflage = true;
                }

                if (isset($this->browser->name) && $this->browser->name == 'Internet Explorer') {
                    $this->camouflage = true;
                }
            }

            /* If it claims not to be Gecko, but it is probably Gecko running camouflage mode */
            if ($this->options->engine & Constants\EngineType::GECKO) {
                $this->features[] = 'gecko';

                if ($this->engine->name && $this->engine->name != 'Gecko') {
                    $this->camouflage = true;
                }

                if (isset($this->browser->name) && $this->browser->name == 'Internet Explorer') {
                    $this->camouflage = true;
                }
            }

            /* If it claims not to be Webkit, but it is probably Webkit running camouflage mode */
            if ($this->options->engine & Constants\EngineType::WEBKIT) {
                $this->features[] = 'webkit';

                if ($this->engine->name && ($this->engine->name != 'Blink' && $this->engine->name != 'Webkit')) {
                    $this->camouflage = true;
                }

                if (isset($this->browser->name) && $this->browser->name == 'Internet Explorer') {
                    $this->camouflage = true;
                }

                /* IE 11 on mobile now supports Webkit APIs */
                if (isset($this->browser->name) && $this->browser->name == 'Mobile Internet Explorer'
                    && isset($this->browser->version) && $this->browser->version->toFloat() >= 11
                    && isset($this->os->name) && $this->os->name == 'Windows Phone'
                ) {
                    $this->camouflage = false;
                }

                /* IE 11 Developer Preview now supports  Webkit APIs */
                if (isset($this->browser->name) && $this->browser->name == 'Internet Explorer'
                    && isset($this->browser->version) && $this->browser->version->toFloat() >= 11
                    && isset($this->os->name) && $this->os->name == 'Windows'
                ) {
                    $this->camouflage = false;
                }

                /* EdgeHTML rendering engine also appears to be WebKit */
                if (isset($this->engine->name) && $this->engine->name == 'EdgeHTML') {
                    $this->camouflage = false;
                }

                /* Firefox 48+ support certain Webkit features */
                if ($this->options->engine & Constants\EngineType::GECKO) {
                    $this->camouflage = false;
                }
            }

            if ($this->options->engine & Constants\EngineType::CHROMIUM) {
                $this->features[] = 'chrome';

                if ($this->engine->name && ($this->engine->name != 'EdgeHTML' && $this->engine->name != 'Blink' && $this->engine->name != 'Webkit')) {
                    $this->camouflage = true;
                }
            }

            /* If it claims to be Safari and uses V8, it is probably an Android device running camouflage mode */
            if ($this->engine->name == 'Webkit' && $this->options->engine & Constants\EngineType::V8) {
                $this->features[] = 'v8';

                if (isset($this->browser->name) && $this->browser->name == 'Safari') {
                    $this->camouflage = true;
                }
            }
        }

        return $this;
    }

    private function &detectCamouflagedBasedOnFeatures()
    {
        if (isset($this->browser->name) && isset($this->os->name)) {
            if ($this->os->name == 'iOS' && $this->browser->name != 'Opera Mini' && $this->browser->name != 'UC Browser' && isset($this->os->version)) {
                if ($this->os->version->toFloat() < 4.0 && $this->options->features & Constants\Feature::SANDBOX) {
                    $this->features[] = 'foundSandbox';
                    $this->camouflage = true;
                }

                if ($this->os->version->toFloat() < 4.2 && $this->options->features & Constants\Feature::WEBSOCKET) {
                    $this->features[] = 'foundSockets';
                    $this->camouflage = true;
                }

                if ($this->os->version->toFloat() < 5.0 && $this->options->features & Constants\Feature::WORKER) {
                    $this->features[] = 'foundWorker';
                    $this->camouflage = true;
                }
            }

            if ($this->os->name != 'iOS' && $this->browser->name == 'Safari' && isset($this->browser->version)) {
                if ($this->browser->version->toFloat() < 4.0 && $this->options->features & Constants\Feature::APPCACHE) {
                    $this->features[] = 'foundAppCache';
                    $this->camouflage = true;
                }

                if ($this->browser->version->toFloat() < 4.1 && $this->options->features & Constants\Feature::HISTORY) {
                    $this->features[] = 'foundHistory';
                    $this->camouflage = true;
                }

                if ($this->browser->version->toFloat() < 5.1 && $this->options->features & Constants\Feature::FULLSCREEN) {
                    $this->features[] = 'foundFullscreen';
                    $this->camouflage = true;
                }

                if ($this->browser->version->toFloat() < 5.2 && $this->options->features & Constants\Feature::FILEREADER) {
                    $this->features[] = 'foundFileReader';
                    $this->camouflage = true;
                }
            }
        }

        return $this;
    }
}
