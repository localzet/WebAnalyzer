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

trait Wap
{
    public function analyseWapProfile($header)
    {
        $header = trim($header);

        if ($header[0] == '"') {
            $header = explode(",", $header);
            $header = trim($header[0], '"');
        }

        $result = Data\DeviceProfiles::identify($header);

        if ($result) {
            $this->device->manufacturer = $result[0];
            $this->device->model = $result[1];
            $this->device->identified |= Constants\Id::MATCH_PROF;

            if (!empty($result[2]) && (!isset($this->os->name) || $this->os->name != $result[2])) {
                $this->os->name = $result[2];
                $this->os->version = null;

                $this->engine->name = null;
                $this->engine->version = null;
            }

            if (isset($result[3])) {
                $this->device->type = $result[3];
            }
        }
    }
}
