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

use localzet\WebAnalyzer\Model\Version;

trait Engine
{
    private function &detectEngine($ua)
    {
        $this->detectWebkit($ua);
        $this->detectKHTML($ua);
        $this->detectGecko($ua);
        $this->detectServo($ua);
        $this->detectGoanna($ua);
        $this->detectPresto($ua);
        $this->detectTrident($ua);
        $this->detectEdgeHTMLUseragent($ua);
        $this->detectFlow($ua);

        return $this;
    }


    /* WebKit */

    private function detectWebkit($ua)
    {
        if (preg_match('/WebKit\/([0-9.]*)/iu', $ua, $match)) {
            $this->data->engine->name = 'Webkit';
            $this->data->engine->version = new Version(['value' => $match[1]]);

            if (preg_match('/(?:Chrome|Chromium)\/([0-9]*)/u', $ua, $match)) {
                if (intval($match[1]) >= 27) {
                    $this->data->engine->reset(['name' => 'Blink']);
                }
            }
        }

        if (preg_match('/Browser\/AppleWebKit\/?([0-9.]*)/iu', $ua, $match)) {
            $this->data->engine->name = 'Webkit';
            $this->data->engine->version = new Version(['value' => $match[1]]);
        }

        if (preg_match('/AppleWebkit\(like Gecko\)/iu', $ua, $match)) {
            $this->data->engine->name = 'Webkit';
        }

        if (preg_match('/CoralWebkit/iu', $ua, $match)) {
            $this->data->engine->version = null;
        }
    }


    /* KHTML */

    private function detectKHTML($ua)
    {
        if (preg_match('/KHTML\/([0-9.]*)/u', $ua, $match)) {
            $this->data->engine->name = 'KHTML';
            $this->data->engine->version = new Version(['value' => $match[1]]);
        }
    }


    /* Gecko */

    private function detectGecko($ua)
    {
        if (preg_match('/Gecko/u', $ua) && !preg_match('/like Gecko/iu', $ua)) {
            $this->data->engine->name = 'Gecko';

            if (preg_match('/; rv:([^\);]+)[\);]/u', $ua, $match)) {
                $this->data->engine->version = new Version(['value' => $match[1], 'details' => 3]);
            }
        }
    }


    /* Servo */

    private function detectServo($ua)
    {
        if (preg_match('/Servo\/([0-9.]*)/u', $ua, $match)) {
            $this->data->engine->name = 'Servo';
            $this->data->engine->version = new Version(['value' => $match[1]]);
        }
    }


    /* Goanna */

    private function detectGoanna($ua)
    {
        if (preg_match('/Goanna/u', $ua)) {
            $this->data->engine->name = 'Goanna';

            if (preg_match('/Goanna\/([0-9]\.[0-9.]+)/u', $ua, $match)) {
                $this->data->engine->version = new Version(['value' => $match[1]]);
            }

            if (preg_match('/Goanna\/20[0-9]{6,6}/u', $ua) && preg_match('/; rv:([^\);]+)[\);]/u', $ua, $match)) {
                $this->data->engine->version = new Version(['value' => $match[1]]);
            }
        }
    }


    /* Presto */

    private function detectPresto($ua)
    {
        if (preg_match('/Presto\/([0-9.]*)/u', $ua, $match)) {
            $this->data->engine->name = 'Presto';
            $this->data->engine->version = new Version(['value' => $match[1]]);
        }
    }


    /* Trident */

    private function detectTrident($ua)
    {
        if (preg_match('/Trident\/([0-9.]*)/u', $ua, $match)) {
            $this->data->engine->name = 'Trident';
            $this->data->engine->version = new Version(['value' => $match[1]]);


            if (isset($this->data->browser->version) && isset($this->data->browser->name) && $this->data->browser->name == 'Internet Explorer') {
                if ($this->data->engine->version->toNumber() >= 7 && $this->data->browser->version->toFloat() < 11) {
                    $this->data->browser->version = new Version(['value' => '11.0']);
                    $this->data->browser->mode = 'compat';
                }

                if ($this->data->engine->version->toNumber() == 6 && $this->data->browser->version->toFloat() < 10) {
                    $this->data->browser->version = new Version(['value' => '10.0']);
                    $this->data->browser->mode = 'compat';
                }

                if ($this->data->engine->version->toNumber() == 5 && $this->data->browser->version->toFloat() < 9) {
                    $this->data->browser->version = new Version(['value' => '9.0']);
                    $this->data->browser->mode = 'compat';
                }

                if ($this->data->engine->version->toNumber() == 4 && $this->data->browser->version->toFloat() < 8) {
                    $this->data->browser->version = new Version(['value' => '8.0']);
                    $this->data->browser->mode = 'compat';
                }
            }

            if (isset($this->data->os->version) && isset($this->data->os->name) && $this->data->os->name == 'Windows Phone' && isset($this->data->browser->name) && $this->data->browser->name == 'Mobile Internet Explorer') {
                if ($this->data->engine->version->toNumber() == 7 && $this->data->os->version->toFloat() < 8.1) {
                    $this->data->os->version = new Version(['value' => '8.1', 'details' => 2]);
                }

                if ($this->data->engine->version->toNumber() == 5 && $this->data->os->version->toFloat() < 7.5) {
                    $this->data->os->version = new Version(['value' => '7.5', 'details' => 2]);
                }
            }
        }
    }


    /* EdgeHTML */

    private function detectEdgeHTMLUseragent($ua)
    {
        if (preg_match('/Edge\/([0-9.]*)/u', $ua, $match)) {
            $this->data->engine->name = 'EdgeHTML';
            $this->data->engine->version = new Version(['value' => $match[1], 'hidden' => true]);
        }
    }

    /* Flow */

    private function detectFlow($ua)
    {
        if (preg_match('/EkiohFlow\/([0-9.]*)/u', $ua, $match)) {
            $this->data->engine->name = 'EkiohFlow';
            $this->data->engine->version = new Version(['value' => $match[1]]);
        }
    }
}
