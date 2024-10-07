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

trait Using
{
    private function &detectUsing($ua)
    {
        if (!preg_match('/(AdobeAIR|Awesomium|Embedded|bsalsa|Canvace|Ekioh|AtomShell|Electron|JavaFX|GFXe|luakit|Titanium|OpenWebKitSharp|Prism|Qt|Reqwireless|RhoSimulator|UWebKit|nw-tests|WebKit2)/ui', $ua)) {
            return $this;
        }

        $items = [
            ['name' => 'AdobeAIR', 'regexp' => '/AdobeAIR\/([0-9.]*)/u'],
            ['name' => 'Awesomium', 'regexp' => '/Awesomium\/([0-9.]*)/u'],
            ['name' => 'Delphi Embedded Web Browser', 'regexp' => '/EmbeddedWB ([0-9.]*)/u'],
            ['name' => 'Delphi Embedded Web Browser', 'regexp' => '/bsalsa\.com/u'],
            ['name' => 'Delphi Embedded Web Browser', 'regexp' => '/Embedded Web Browser/u'],
            ['name' => 'Canvace', 'regexp' => '/Canvace Standalone\/([0-9.]*)/u'],
            ['name' => 'Ekioh', 'regexp' => '/Ekioh\/([0-9.]*)/u'],
            ['name' => 'Electron', 'regexp' => '/AtomShell\/([0-9.]*)/u'],
            ['name' => 'Electron', 'regexp' => '/Electron\/([0-9.]*)/u'],
            ['name' => 'JavaFX', 'regexp' => '/JavaFX\/([0-9.]*)/u'],
            ['name' => 'GFXe', 'regexp' => '/GFXe\/([0-9.]*)/u'],
            ['name' => 'LuaKit', 'regexp' => '/luakit/u'],
            ['name' => 'Titanium', 'regexp' => '/Titanium\/([0-9.]*)/u'],
            ['name' => 'OpenWebKitSharp', 'regexp' => '/OpenWebKitSharp/u'],
            ['name' => 'Prism', 'regexp' => '/Prism\/([0-9.]*)/u'],
            ['name' => 'Qt', 'regexp' => '/Qt\/([0-9.]*)/u'],
            ['name' => 'Qt', 'regexp' => '/QtWebEngine\/([4-9][0-9.]*)?/u'],
            ['name' => 'Qt', 'regexp' => '/QtEmbedded/u'],
            ['name' => 'Qt', 'regexp' => '/QtEmbedded.*Qt\/([0-9.]*)/u'],
            ['name' => 'ReqwirelessWeb', 'regexp' => '/ReqwirelessWeb\/([0-9.]*)/u'],
            ['name' => 'RhoSimulator', 'regexp' => '/RhoSimulator/u'],
            ['name' => 'UWebKit', 'regexp' => '/UWebKit\/([0-9.]*)/u'],
            ['name' => 'Node-WebKit', 'regexp' => '/nw-tests\/([0-9.]*)/u'],
            ['name' => 'WebKit2.NET', 'regexp' => '/WebKit2.NET/u'],
        ];

        $count = count($items);
        for ($i = 0; $i < $count; $i++) {
            if (preg_match($items[$i]['regexp'], $ua, $match)) {
                $this->browser->using = new \localzet\WebAnalyzer\Model\Using([
                    'name' => $items[$i]['name']
                ]);

                if (isset($match[1]) && $match[1]) {
                    $this->browser->using->version = new Version(['value' => $match[1], 'details' => $items[$i]['details'] ?? null]);
                }

                break;
            }
        }

        return $this;
    }
}
