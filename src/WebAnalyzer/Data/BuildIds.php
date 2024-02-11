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

namespace localzet\WebAnalyzer\Data;

use localzet\WebAnalyzer\Model\Version;

class BuildIds
{
    public static $ANDROID_BUILDS = [];

    public static function identify($id)
    {
        require_once __DIR__ . '/../../data/build-android.php';

        if (isset(BuildIds::$ANDROID_BUILDS[$id])) {
            if (is_array(BuildIds::$ANDROID_BUILDS[$id])) {
                return new Version(BuildIds::$ANDROID_BUILDS[$id]);
            } else {
                return new Version(['value' => BuildIds::$ANDROID_BUILDS[$id]]);
            }
        }
    }
}
