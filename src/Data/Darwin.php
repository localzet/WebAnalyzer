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

class Darwin
{
    public static $OSX = [];
    public static $IOS = [];

    public static function getVersion($platform, $version)
    {
        require_once __DIR__ . '/../../../data/os-darwin.php';

        $version = implode('.', array_slice(explode('.', $version), 0, 3));

        switch ($platform) {
            case 'osx':
                if (isset(Darwin::$OSX[$version])) {
                    return Darwin::$OSX[$version];
                }
                break;
            case 'ios':
                if (isset(Darwin::$IOS[$version])) {
                    return Darwin::$IOS[$version];
                }
                break;
        }
    }
}
