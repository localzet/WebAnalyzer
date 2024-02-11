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

class BrowserId
{
    public function __construct($header, &$data)
    {
        if ($header == 'XMLHttpRequest') {
            return;
        }

        $this->data =& $data;

        /* The X-Requested-With header is send by the WebView, so our browser name is Chrome it is probably the Chromium WebView which is sometimes misidentified. */

        if (isset($this->data->browser->name) && $this->data->browser->name == 'Chrome') {
            $version = $this->data->browser->getVersion();

            $this->data->browser->reset();
            $this->data->browser->using = new Using(['name' => 'Chromium WebView', 'version' => new Version(['value' => explode('.', $version)[0]])]);
        }

        /* Detect the correct browser based on the header */

        $browser = Data\BrowserIds::identify($header);
        if ($browser) {
            if (!isset($this->data->browser->name)) {
                $this->data->browser->name = $browser;
            } else {
                if (!str_starts_with($this->data->browser->name, $browser)) {
                    $this->data->browser->name = $browser;
                    $this->data->browser->version = null;
                    $this->data->browser->stock = false;
                } else {
                    $this->data->browser->name = $browser;
                }
            }
        }

        /* The X-Requested-With header is only send from Android devices */

        if (!isset($this->data->os->name) || ($this->data->os->name != 'Android' && (!isset($this->data->os->family) || $this->data->os->family->getName() != 'Android'))) {
            $this->data->os->name = 'Android';
            $this->data->os->alias = null;
            $this->data->os->version = null;

            $this->data->device->manufacturer = null;
            $this->data->device->model = null;
            $this->data->device->identified = Constants\Id::NONE;

            if ($this->data->device->type != Constants\DeviceType::MOBILE && $this->data->device->type != Constants\DeviceType::TABLET) {
                $this->data->device->type = Constants\DeviceType::MOBILE;
            }
        }

        /* The X-Requested-With header is send by the WebKit or Chromium Webview */

        if (!isset($this->data->engine->name) || ($this->data->engine->name != 'Webkit' && $this->data->engine->name != 'Blink')) {
            $this->data->engine->name = 'Webkit';
            $this->data->engine->version = null;
        }
    }
}
