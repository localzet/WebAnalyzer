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

use localzet\WebAnalyzer\Model\Browser;
use localzet\WebAnalyzer\Model\Version;

class Applications
{
    public static $BOTS = [];
    public static $BOTS_REGEX = '';

    public static $BROWSERS = [];
    public static $BROWSERS_REGEX = '';

    public static $OTHERS = [];
    public static $OTHERS_REGEX = '';


    public static function identifyBrowser($ua)
    {
        require_once __DIR__ . '/../../../data/regexes/applications-browsers.php';

        if (preg_match(self::$BROWSERS_REGEX, $ua)) {
            require_once __DIR__ . '/../../../data/applications-browsers.php';

            foreach (self::$BROWSERS as $type => $list) {
                foreach ($list as $i => $item) {
                    if (preg_match($item['regexp'], $ua, $match)) {
                        return [
                            'browser' => [
                                'name' => $item['name'],
                                'hidden' => $item['hidden'] ?? false,
                                'stock' => false,
                                'channel' => '',
                                'type' => $type,
                                'version' => isset($match[1]) && $match[1] ? new Version(['value' => $match[1], 'details' => $item['details'] ?? null]) : null
                            ],

                            'device' => isset($item['type']) ? [
                                'type' => $item['type']
                            ] : null
                        ];
                    }
                }
            }
        }
    }

    public static function identifyOther($ua)
    {
        require_once __DIR__ . '/../../../data/regexes/applications-others.php';

        if (preg_match(self::$OTHERS_REGEX, $ua)) {
            require_once __DIR__ . '/../../../data/applications-others.php';

            foreach (self::$OTHERS as $type => $list) {
                foreach ($list as $i => $item) {
                    if (preg_match($item['regexp'], $ua, $match)) {
                        return [
                            'browser' => [
                                'name' => $item['name'],
                                'hidden' => $item['hidden'] ?? false,
                                'stock' => false,
                                'channel' => '',
                                'type' => $type,
                                'version' => isset($match[1]) && $match[1] ? new Version(['value' => $match[1], 'details' => $item['details'] ?? null]) : null
                            ],

                            'device' => isset($item['type']) ? [
                                'type' => $item['type']
                            ] : null
                        ];
                    }
                }
            }
        }
    }

    public static function identifyBot($ua)
    {
        if (is_null($ua)) {
            return;
        }

        require_once __DIR__ . '/../../../data/regexes/applications-bots.php';

        if (preg_match(self::$BOTS_REGEX, $ua)) {
            require_once __DIR__ . '/../../../data/applications-bots.php';

            foreach (self::$BOTS as $i => $item) {
                if (preg_match($item['regexp'], $ua, $match)) {
                    return new Browser([
                        'name' => $item['name'],
                        'stock' => false,
                        'version' => isset($match[1]) && $match[1] ? new Version(['value' => $match[1], 'details' => $item['details'] ?? null]) : null
                    ]);
                }
            }
        }
    }
}
