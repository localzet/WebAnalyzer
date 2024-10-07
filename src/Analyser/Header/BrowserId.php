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
use localzet\WebAnalyzer\Model\Using;
use localzet\WebAnalyzer\Model\Version;

trait BrowserId
{
    public function analyseBrowserId($header)
    {
        if ($header == 'XMLHttpRequest') {
            return;
        }

        /* The X-Requested-With header is send by the WebView, so our browser name is Chrome it is probably the Chromium WebView which is sometimes misidentified. */

        if (isset($this->browser->name) && $this->browser->name == 'Chrome') {
            $version = $this->browser->getVersion();

            $this->browser->reset();
            $this->browser->using = new Using(['name' => 'Chromium WebView', 'version' => new Version(['value' => explode('.', $version)[0]])]);
        }

        /* Detect the correct browser based on the header */

        $browser = Data\BrowserIds::identify($header);
        if ($browser) {
            if (!isset($this->browser->name)) {
                $this->browser->name = $browser;
            } else {
                if (!str_starts_with($this->browser->name, $browser)) {
                    $this->browser->name = $browser;
                    $this->browser->version = null;
                    $this->browser->stock = false;
                } else {
                    $this->browser->name = $browser;
                }
            }
        }

        /* The X-Requested-With header is only send from Android devices */

        if (!isset($this->os->name) || ($this->os->name != 'Android' && (!isset($this->os->family) || $this->os->family->getName() != 'Android'))) {
            $this->os->name = 'Android';
            $this->os->alias = null;
            $this->os->version = null;

            $this->device->manufacturer = null;
            $this->device->model = null;
            $this->device->identified = Constants\Id::NONE;

            if ($this->device->type != Constants\DeviceType::MOBILE && $this->device->type != Constants\DeviceType::TABLET) {
                $this->device->type = Constants\DeviceType::MOBILE;
            }
        }

        /* The X-Requested-With header is send by the WebKit or Chromium Webview */

        if (!isset($this->engine->name) || ($this->engine->name != 'Webkit' && $this->engine->name != 'Blink')) {
            $this->engine->name = 'Webkit';
            $this->engine->version = null;
        }
    }
}
