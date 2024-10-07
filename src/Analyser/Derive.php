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
use localzet\WebAnalyzer\Model\Family;
use localzet\WebAnalyzer\Model\Using;
use localzet\WebAnalyzer\Model\Version;

trait Derive
{
    private function &deriveInformation()
    {
        if (isset($this->device->flag)) {
            $this->deriveBasedOnDeviceFlag();
        }

        if (isset($this->os->name)) {
            $this->deriveBasedOnOperatingSystem();
        }

        if (isset($this->browser->name)) {
            $this->deriveOperaDevices();
        }

        if (isset($this->browser->name)) {
            $this->deriveFirefoxOS();
        }

        if (isset($this->browser->name)) {
            $this->deriveTrident();
            $this->deriveOperaRenderingEngine();
            $this->deriveOmniWebRenderingEngine();
            $this->deriveNetFrontRenderingEngine();
        }

        return $this;
    }


    private function &deriveDeviceSubType()
    {
        if ($this->device->type == 'mobile' && empty($this->device->subtype)) {
            $this->device->subtype = 'feature';

            if (in_array($this->os->getName(), ['Android', 'Bada', 'BlackBerry', 'BlackBerry OS', 'Firefox OS', 'iOS', 'iPhone OS', 'Kin OS', 'Maemo', 'MeeGo', 'Palm OS', 'Sailfish', 'Series60', 'Series80', 'Tizen', 'Ubuntu Touch', 'Windows Mobile', 'Windows Phone', 'webOS'])) {
                $this->device->subtype = 'smart';
            }

            if (isset($this->os->name) && $this->os->name == 'Windows Phone') {
                $this->device->subtype = 'smart';
            }

            if (isset($this->os->family) && $this->os->family->getName() == 'Android') {
                $this->device->subtype = 'smart';
            }
        }

        return $this;
    }


    private function deriveOmniWebRenderingEngine()
    {
        if ($this->isBrowser('OmniWeb')) {
            $version = $this->browser->getVersion();

            if ($version < 5) {
                $this->engine->reset();
            }

            if ($version >= 5 && $version < 5.5 && !$this->isEngine('WebCore')) {
                $this->engine->reset(['name' => 'WebCore']);
            }

            if ($version >= 5.5 && !$this->isEngine('WebKit')) {
                $this->engine->reset(['name' => 'WebKit']);
            }
        }
    }


    private function deriveOperaRenderingEngine()
    {
        if ($this->isBrowser('Opera') || $this->isBrowser('Opera Mobile')) {
            $version = $this->browser->getVersion();

            if ($version >= 3.5 && $version < 7 && !$this->isEngine('Electra')) {
                $this->engine->reset(['name' => 'Electra']);
            }

            if ($version >= 7 && $version < 13 && !$this->isEngine('Presto')) {
                $this->engine->reset(['name' => 'Presto']);
            }
        }

        if ($this->isBrowser('Opera Mini') && !$this->isOs('iOS') && !$this->isEngine('Presto')) {
            $this->engine->reset(['name' => 'Presto']);
        }
    }


    private function deriveNetFrontRenderingEngine()
    {
        if ($this->isBrowser('NetFront') && !$this->isEngine('NetFront')) {
            $this->engine->reset(['name' => 'NetFront']);
        }
    }

    private function deriveTrident()
    {
        if ($this->isType('desktop') && $this->isBrowser('Internet Explorer') && !$this->engine->getName()) {
            if ($this->isBrowser('Internet Explorer', '>=', 4)) {
                $this->engine->set(['name' => 'Trident']);
            }
        }

        if ($this->isMobile() && $this->isBrowser('Mobile Internet Explorer') && !$this->engine->getName()) {
            if ($this->isBrowser('Mobile Internet Explorer', '=', 6)) {
                $this->engine->set(['name' => 'Trident']);
            }

            if ($this->isBrowser('Mobile Internet Explorer', '=', 7)) {
                $this->engine->set(['name' => 'Trident', 'version' => new Version(['value' => '3.1'])]);
            }
        }
    }


    private function deriveFirefoxOS()
    {
        if (in_array($this->browser->name, ['Firefox Mobile', 'Servo Nightly Build']) && !isset($this->os->name)) {
            $this->os->name = 'Firefox OS';
        }

        if (isset($this->os->name) && $this->os->name == 'Firefox OS' && $this->engine->name == 'Gecko') {
            switch ($this->engine->getVersion()) {
                case '18.0':
                    $this->os->version = new Version(['value' => '1.0.1']);
                    break;
                case '18.1':
                    $this->os->version = new Version(['value' => '1.1']);
                    break;
                case '26.0':
                    $this->os->version = new Version(['value' => '1.2']);
                    break;
                case '28.0':
                    $this->os->version = new Version(['value' => '1.3']);
                    break;
                case '30.0':
                    $this->os->version = new Version(['value' => '1.4']);
                    break;
                case '32.0':
                    $this->os->version = new Version(['value' => '2.0']);
                    break;
                case '34.0':
                    $this->os->version = new Version(['value' => '2.1']);
                    break;
                case '37.0':
                    $this->os->version = new Version(['value' => '2.2']);
                    break;
                case '44.0':
                    $this->os->version = new Version(['value' => '2.5']);
                    break;
            }
        }
    }


    private function deriveOperaDevices()
    {
        if ($this->browser->name == 'Opera' && $this->device->type == Constants\DeviceType::TELEVISION) {
            $this->browser->name = 'Opera Devices';
            $this->browser->version = null;

            if ($this->engine->getName() == 'Presto') {
                $data = [
                    '2.12' => '3.4',
                    '2.11' => '3.3',
                    '2.10' => '3.2',
                    '2.9' => '3.1',
                    '2.8' => '3.0',
                    '2.7' => '2.9',
                    '2.6' => '2.8',
                    '2.4' => '10.3',
                    '2.3' => '10',
                    '2.2' => '9.7',
                    '2.1' => '9.6'
                ];

                $key = implode('.', array_slice(explode('.', $this->engine->getVersion()), 0, 2));

                if (isset($data[$key])) {
                    $this->browser->version = new Version(['value' => $data[$key]]);
                } else {
                    unset($this->browser->version);
                }
            }

            $this->os->reset();
        }
    }


    private function deriveBasedOnDeviceFlag()
    {
        $flag = $this->device->flag;

        if ($flag == Constants\Flag::NOKIAX) {
            $this->os->name = 'Nokia X Platform';
            $this->os->family = new Family(['name' => 'Android']);

            unset($this->os->version);
            unset($this->device->flag);
        }

        if ($flag == Constants\Flag::FIREOS) {
            $this->os->name = 'FireOS';
            $this->os->family = new Family(['name' => 'Android']);

            if (isset($this->os->version->value)) {
                switch ($this->os->version->value) {
                    case '2.3.3':
                    case '2.3.4':
                        $this->os->version = new Version(['value' => '1']);
                        break;
                    case '4.0.3':
                        $this->os->version = new Version(['value' => '2']);
                        break;
                    case '4.2.2':
                        $this->os->version = new Version(['value' => '3']);
                        break;
                    case '4.4.2':
                        $this->os->version = new Version(['value' => '4']);
                        break;
                    case '4.4.3':
                        $this->os->version = new Version(['value' => '4.5']);
                        break;
                    case '5.1.1':
                        $this->os->version = new Version(['value' => '5']);
                        break;
                    default:
                        unset($this->os->version);
                        break;
                }
            }

            if ($this->isBrowser('Chrome')) {
                $this->browser->reset();
                $this->browser->using = new Using(['name' => 'Amazon WebView']);
            }

            if ($this->browser->isUsing('Chromium WebView')) {
                $this->browser->using = new Using(['name' => 'Amazon WebView']);
            }

            unset($this->device->flag);
        }

        if ($flag == Constants\Flag::GOOGLETV) {
            $this->os->name = 'Google TV';
            $this->os->family = new Family(['name' => 'Android']);

            unset($this->os->version);
            unset($this->device->flag);
        }

        if ($flag == Constants\Flag::ANDROIDTV) {
            $this->os->name = 'Android TV';
            $this->os->family = new Family(['name' => 'Android']);
            unset($this->device->flag);
            unset($this->device->series);
        }

        if ($flag == Constants\Flag::ANDROIDWEAR) {
            $this->os->name = 'Android Wear';
            $this->os->family = new Family(['name' => 'Android']);
            unset($this->os->version);
            unset($this->device->flag);

            if ($this->browser->isUsing('Chrome Content Shell')) {
                $this->browser->name = 'Wear Internet Browser';
                $this->browser->using = null;
            }
        }

        if ($flag == Constants\Flag::GOOGLEGLASS) {
            $this->os->family = new Family(['name' => 'Android']);
            unset($this->os->name);
            unset($this->os->version);
            unset($this->device->flag);
        }

        if ($flag == Constants\Flag::UIQ) {
            unset($this->device->flag);

            if (!$this->isOs('UIQ')) {
                $this->os->name = 'UIQ';
                unset($this->os->version);
            }
        }

        if ($flag == Constants\Flag::S60) {
            unset($this->device->flag);

            if (!$this->isOs('Series60')) {
                $this->os->name = 'Series60';
                unset($this->os->version);
            }
        }

        if ($flag == Constants\Flag::MOAPS) {
            unset($this->device->flag);
            $this->os->name = 'MOAP(S)';
            unset($this->os->version);
        }
    }

    private function deriveBasedOnOperatingSystem()
    {
        /* Derive the default browser on Windows Mobile */

        if ($this->os->name == 'Windows Mobile' && $this->isBrowser('Internet Explorer')) {
            $this->browser->name = 'Mobile Internet Explorer';
        }

        /* Derive the default browser on Android */

        if ($this->os->name == 'Android' && !isset($this->browser->using) && !isset($this->browser->name) && $this->browser->stock) {
            $this->browser->name = 'Android Browser';
        }

        /* Derive the default browser on Google TV */

        if ($this->os->name == 'Google TV' && !isset($this->browser->name) && $this->browser->stock) {
            $this->browser->name = 'Chrome';
        }

        /* Derive the default browser on BlackBerry */

        if ($this->os->name == 'BlackBerry' && !isset($this->browser->name) && $this->browser->stock) {
            $this->browser->name = 'BlackBerry Browser';
            $this->browser->hidden = true;
        }

        if ($this->os->name == 'BlackBerry OS' && !isset($this->browser->name) && $this->browser->stock) {
            $this->browser->name = 'BlackBerry Browser';
            $this->browser->hidden = true;
        }

        if ($this->os->name == 'BlackBerry Tablet OS' && !isset($this->browser->name) && $this->browser->stock) {
            $this->browser->name = 'BlackBerry Browser';
            $this->browser->hidden = true;
        }

        /* Derive the default browser on Tizen */

        if ($this->os->name == 'Tizen' && !isset($this->browser->name) && $this->browser->stock && in_array($this->device->type, [Constants\DeviceType::MOBILE, Constants\DeviceType::APPLIANCE])) {
            $this->browser->name = 'Samsung Browser';
        }

        /* Derive the default browser on Aliyun OS */

        if ($this->os->name == 'Aliyun OS' && !isset($this->browser->using) && !isset($this->browser->name) && $this->browser->stock) {
            $this->browser->name = 'Aliyun Browser';
        }

        if ($this->os->name == 'Aliyun OS' && $this->browser->isUsing('Chrome Content Shell')) {
            $this->browser->name = 'Aliyun Browser';
            $this->browser->using = null;
            $this->browser->stock = true;
        }

        if ($this->os->name == 'Aliyun OS' && $this->browser->stock) {
            $this->browser->hidden = true;
        }

        /* Derive OS/2 nickname */

        if ($this->os->name == 'OS/2') {
            if (!empty($this->os->version)) {
                if ($this->os->version->is('>', '2')) {
                    $this->os->version->nickname = 'Warp';
                }
            }
        }

        /* Derive HP TouchPad based on webOS and tablet */

        if ($this->os->name == 'webOS' && $this->device->type == Constants\DeviceType::TABLET) {
            $this->device->manufacturer = 'HP';
            $this->device->model = 'TouchPad';
            $this->device->identified |= Constants\Id::MATCH_UA;
        }

        /* Derive Windows 10 Mobile edition */

        if ($this->os->name == 'Windows Phone') {
            if (!empty($this->os->version)) {
                if ($this->os->version->is('=', '10')) {
                    $this->os->alias = 'Windows';
                    $this->os->edition = 'Mobile';
                    $this->os->version->alias = '10';
                }
            }
        }

        /* Derive manufacturer and model based on MacOS or OS X */

        if ($this->os->name == 'OS X' || $this->os->name == 'Mac OS') {
            if (empty($this->device->model)) {
                $this->device->manufacturer = 'Apple';
                $this->device->model = 'Macintosh';
                $this->device->identified |= Constants\Id::INFER;
                $this->device->hidden = true;
            }
        }

        /* Derive manufacturer and model based on MacOS or OS X */

        if ($this->os->name == 'iOS') {
            if (empty($this->device->model)) {
                $this->device->manufacturer = 'Apple';
                $this->device->identified |= Constants\Id::INFER;
                $this->device->hidden = true;
            }
        }

        /* Derive iOS and OS X aliases */

        if ($this->os->name == 'iOS') {
            if (!empty($this->os->version)) {
                if ($this->os->version->is('<', '4')) {
                    $this->os->alias = 'iPhone OS';
                }
            }
        }

        if ($this->os->name == 'OS X') {
            if (!empty($this->os->version)) {
                if ($this->os->version->is('<', '10.7')) {
                    $this->os->alias = 'Mac OS X';
                }

                if ($this->os->version->is('>=', '10.12')) {
                    $this->os->alias = 'macOS';
                }

                if ($this->os->version->is('10.7')) {
                    $this->os->version->nickname = 'Lion';
                }

                if ($this->os->version->is('10.8')) {
                    $this->os->version->nickname = 'Mountain Lion';
                }

                if ($this->os->version->is('10.9')) {
                    $this->os->version->nickname = 'Mavericks';
                }

                if ($this->os->version->is('10.10')) {
                    $this->os->version->nickname = 'Yosemite';
                }

                if ($this->os->version->is('10.11')) {
                    $this->os->version->nickname = 'El Capitan';
                }

                if ($this->os->version->is('10.12')) {
                    $this->os->version->nickname = 'Sierra';
                }

                if ($this->os->version->is('10.13')) {
                    $this->os->version->nickname = 'High Sierra';
                }

                if ($this->os->version->is('10.14')) {
                    $this->os->version->nickname = 'Mojave';
                }

                if ($this->os->version->is('10.15')) {
                    $this->os->version->nickname = 'Catalina';
                }

                if ($this->os->version->is('11')) {
                    $this->os->version->nickname = 'Big Sur';
                }

                if ($this->os->version->is('12')) {
                    $this->os->version->nickname = 'Monterey';
                }
            }
        }
    }
}
