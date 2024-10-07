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

namespace localzet\WebAnalyzer\Analyser\Header\Useragent\Device;

use localzet\WebAnalyzer\Constants;

trait Tablet
{
    private function detectTablet($ua)
    {
        $this->detectWebTab($ua);
    }


    /* WeTab */

    private function detectWebTab($ua)
    {
        if (preg_match('/WeTab-Browser /ui', $ua, $match)) {
            $this->device->manufacturer = 'WeTab';
            $this->device->model = 'WeTab';
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->type = Constants\DeviceType::TABLET;

            $this->browser->name = 'WebTab Browser';
            $this->browser->version = null;

            $this->os->name = 'MeeGo';
            $this->os->version = null;
        }
    }
}
