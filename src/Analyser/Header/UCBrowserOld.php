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

use localzet\WebAnalyzer;
use localzet\WebAnalyzer\Constants;

trait UCBrowserOld
{
    public function analyseOldUCUserAgent($header)
    {
        if ($this->device->type == Constants\DeviceType::DESKTOP) {
            $this->device->type = Constants\DeviceType::MOBILE;

            $this->os->reset();
        }

        if (!isset($this->browser->name) || $this->browser->name != 'UC Browser') {
            $this->browser->name = 'UC Browser';
            $this->browser->version = null;
        }

        $this->browser->mode = 'proxy';
        $this->engine->reset(['name' => 'Gecko']);

        $extra = new WebAnalyzer(['User-Agent' => $header]);

        if ($extra->device->type != Constants\DeviceType::DESKTOP) {
            if ($extra->os->getName() !== '' && ($this->os->getName() === '' || $extra->os->getVersion() !== '')) {
                $this->os = $extra->os;
            }
            if ($extra->device->identified) {
                $this->device = $extra->device;
            }
        }
    }
}
