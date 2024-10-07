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

use localzet\WebAnalyzer\Constants;
use localzet\WebAnalyzer\Data;
use localzet\WebAnalyzer\Model\Family;
use localzet\WebAnalyzer\Model\Version;

trait Application
{
    private function &detectApplication($ua)
    {
        /* Detect applications */
        $this->detectSpecificApplications($ua);
        $this->detectRemainingApplications($ua);

        return $this;
    }


    private function detectSpecificApplications($ua)
    {
        /* Sony Updatecenter */

        if (preg_match('/^(.*) Build\/.* (?:com.sonyericsson.updatecenter|UpdateCenter)\/[A-Z0-9\.]+$/iu', $ua, $match)) {
            $this->browser->name = 'Sony Update Center';
            $this->browser->version = null;
            $this->browser->type = Constants\BrowserType::APP;

            $this->os->reset([
                'name' => 'Android'
            ]);

            $this->device->model = $match[1];
            $this->device->identified |= Constants\Id::PATTERN;
            $this->device->type = Constants\DeviceType::MOBILE;

            $device = Data\DeviceModels::identify('android', $match[1]);
            if ($device->identified) {
                $device->identified |= $this->device->identified;
                $this->device = $device;
            }
        }

        /* Sony Select SDK */

        if (preg_match('/Android [0-9\.]+; (.*) Sony\/.*SonySelectSDK\/([0-9\.]+)/iu', $ua, $match)) {
            $this->browser->reset();
            $this->browser->type = Constants\BrowserType::APP;
            $this->browser->using = new \localzet\WebAnalyzer\Model\Using([
                'name' => 'Sony Select SDK',
                'version' => new Version(['value' => $match[2], 'details' => 2])
            ]);

            $this->device->model = $match[1];
            $this->device->identified |= Constants\Id::PATTERN;
            $this->device->type = Constants\DeviceType::MOBILE;

            $device = Data\DeviceModels::identify('android', $match[1]);
            if ($device->identified) {
                $device->identified |= $this->device->identified;
                $this->device = $device;
            }
        }

        /* Samsung Mediahub */

        if (preg_match('/^Stamhub [^\/]+\/([^;]+);.*:([0-9\.]+)\/[^\/]+\/[^:]+:user\/release-keys$/iu', $ua, $match)) {
            $this->browser->name = 'Mediahub';
            $this->browser->version = null;
            $this->browser->type = Constants\BrowserType::APP_MEDIAPLAYER;

            $this->os->reset([
                'name' => 'Android',
                'version' => new Version(['value' => $match[2]])
            ]);

            $this->device->model = $match[1];
            $this->device->identified |= Constants\Id::PATTERN;
            $this->device->type = Constants\DeviceType::MOBILE;

            $device = Data\DeviceModels::identify('android', $match[1]);
            if ($device->identified) {
                $device->identified |= $this->device->identified;
                $this->device = $device;
            }
        }

        /* "Android Application" */

        if (preg_match('/Android Application/iu', $ua)) {
            if (preg_match('/^(.+) Android Application \([0-9]+, .+ v[0-9\.]+\) - [a-z-]+ (.*) [a-z_-]+ - [0-9A-F]{8,8}-[0-9A-F]{4,4}-[0-9A-F]{4,4}-[0-9A-F]{4,4}-[0-9A-F]{12,12}$/iu', $ua, $match)) {
                $this->browser->name = $match[1];
                $this->browser->version = null;
                $this->browser->type = Constants\BrowserType::APP;

                $this->os->reset([
                    'name' => 'Android'
                ]);

                $this->device->model = $match[2];
                $this->device->identified |= Constants\Id::PATTERN;
                $this->device->type = Constants\DeviceType::MOBILE;

                $device = Data\DeviceModels::identify('android', $match[2]);
                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }

            if (preg_match('/^(.+) Android Application - (.*) Build\/(.+)  - [0-9A-F]{8,8}-[0-9A-F]{4,4}-[0-9A-F]{4,4}-[0-9A-F]{4,4}-[0-9A-F]{12,12}$/iu', $ua, $match)) {
                $this->browser->name = $match[1];
                $this->browser->version = null;
                $this->browser->type = Constants\BrowserType::APP;

                $this->os->reset([
                    'name' => 'Android'
                ]);

                $version = Data\BuildIds::identify($match[3]);
                if ($version) {
                    $this->os->version = $version;
                }

                $this->device->model = $match[2];
                $this->device->identified |= Constants\Id::PATTERN;
                $this->device->type = Constants\DeviceType::MOBILE;

                $device = Data\DeviceModels::identify('android', $match[2]);
                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }

            if (preg_match('/^(.+) Android Application - [a-z-]+ (.*) [a-z_-]+$/iu', $ua, $match)) {
                $this->browser->name = $match[1];
                $this->browser->version = null;
                $this->browser->type = Constants\BrowserType::APP;

                $this->os->reset([
                    'name' => 'Android'
                ]);

                $this->device->model = $match[2];
                $this->device->identified |= Constants\Id::PATTERN;
                $this->device->type = Constants\DeviceType::MOBILE;

                $device = Data\DeviceModels::identify('android', $match[2]);
                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }
        }

        /* AiMeiTuan */

        if (preg_match('/^AiMeiTuan \/[^\-]+\-([0-9\.]+)\-(.*)\-[0-9]+x[0-9]+\-/iu', $ua, $match)) {
            $this->browser->name = 'AiMeiTuan';
            $this->browser->version = null;
            $this->browser->type = Constants\BrowserType::APP;

            $this->os->reset([
                'name' => 'Android',
                'version' => new Version(['value' => $match[1]])
            ]);

            $this->device->model = $match[2];
            $this->device->identified |= Constants\Id::PATTERN;
            $this->device->type = Constants\DeviceType::MOBILE;

            $device = Data\DeviceModels::identify('android', $match[2]);
            if ($device->identified) {
                $device->identified |= $this->device->identified;
                $this->device = $device;
            }
        }

        /* Instagram */

        if (preg_match('/^Instagram ([0-9\.]+) Android (?:IC )?\([0-9]+\/([0-9\.]+); [0-9]+dpi; [0-9]+x[0-9]+; [^;]+; ([^;]*);/iu', $ua, $match)) {
            $this->browser->name = 'Instagram';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::APP_SOCIAL;

            $this->os->reset([
                'name' => 'Android',
                'version' => new Version(['value' => $match[2]])
            ]);

            $this->device->model = $match[3];
            $this->device->identified |= Constants\Id::PATTERN;
            $this->device->type = Constants\DeviceType::MOBILE;

            $device = Data\DeviceModels::identify('android', $match[3]);
            if ($device->identified) {
                $device->identified |= $this->device->identified;
                $this->device = $device;
            }
        }

        /* Pinterest */

        if (preg_match('/^Pinterest for Android( Tablet)?\/([0-9\.]+) \(([^;]+); ([0-9\.]+)\)/iu', $ua, $match)) {
            $this->browser->name = 'Pinterest';
            $this->browser->version = new Version(['value' => $match[2]]);
            $this->browser->type = Constants\BrowserType::APP_SOCIAL;

            $this->os->reset([
                'name' => 'Android',
                'version' => new Version(['value' => $match[4]])
            ]);

            $this->device->model = $match[3];
            $this->device->identified |= Constants\Id::PATTERN;
            $this->device->type = $match[1] == ' Tablet' ? Constants\DeviceType::TABLET : Constants\DeviceType::MOBILE;

            $device = Data\DeviceModels::identify('android', $match[3]);
            if ($device->identified) {
                $device->identified |= $this->device->identified;
                $this->device = $device;
            }
        }

        /* Dr. Web Anti-Virus */

        if (preg_match('/Dr\.Web anti\-virus Light Version: ([0-9\.]+) Device model: (.*) Firmware version: ([0-9\.]+)/u', $ua, $match)) {
            $this->browser->name = 'Dr. Web Light';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::APP_ANTIVIRUS;

            $this->os->reset([
                'name' => 'Android',
                'version' => new Version(['value' => $match[3]])
            ]);

            $this->device->type = Constants\DeviceType::MOBILE;

            $device = Data\DeviceModels::identify('android', $match[2]);
            if ($device->identified) {
                $device->identified |= $this->device->identified;
                $this->device = $device;
            }
        }

        /* Google Earth */

        if (preg_match('/GoogleEarth\/([0-9\.]+)\(Android;Android \((.+)\-[^\-]+\-user-([0-9\.]+)\);/u', $ua, $match)) {
            $this->browser->name = 'Google Earth';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::APP;

            $this->os->reset([
                'name' => 'Android',
                'version' => new Version(['value' => $match[3]])
            ]);

            $this->device->type = Constants\DeviceType::MOBILE;

            $device = Data\DeviceModels::identify('android', $match[2]);
            if ($device->identified) {
                $device->identified |= $this->device->identified;
                $this->device = $device;
            }
        }

        /* Groupon */

        if (preg_match('/Groupon\/([0-9\.]+) \(Android ([0-9\.]+); [^\/]+ \/ [A-Z][a-z]+ ([^;]*);/u', $ua, $match)) {
            $this->browser->name = 'Groupon';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::APP_SHOPPING;

            $this->os->reset([
                'name' => 'Android',
                'version' => new Version(['value' => $match[2]])
            ]);

            $this->device->type = Constants\DeviceType::MOBILE;
            $this->device->model = $match[3];

            $device = Data\DeviceModels::identify('android', $match[3]);
            if ($device->identified) {
                $device->identified |= $this->device->identified;
                $this->device = $device;
            }
        }

        /* Whatsapp */

        if (preg_match('/WhatsApp\+?\/([0-9\.]+) (Android|S60Version|WP7)\/([0-9\.\_]+) Device\/([^\-]+)\-(.*)(?:-\([0-9]+\.[0-9]+\))?(?:\-H[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)?$/uU', $ua, $match)) {
            $this->browser->name = 'WhatsApp';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::APP_CHAT;

            $this->device->type = Constants\DeviceType::MOBILE;
            $this->device->manufacturer = $match[4];
            $this->device->model = $match[5];
            $this->device->identified |= Constants\Id::PATTERN;

            if ($match[2] == 'Android') {
                $this->os->reset([
                    'name' => 'Android',
                    'version' => new Version(['value' => str_replace('_', '.', $match[3])])
                ]);

                $device = Data\DeviceModels::identify('android', $match[5]);
                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }

            if ($match[2] == 'WP7') {
                $this->os->reset([
                    'name' => 'Windows Phone',
                    'version' => new Version(['value' => $match[3], 'details' => 2])
                ]);

                $device = Data\DeviceModels::identify('wp', $match[5]);
                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }

            if ($match[2] == 'S60Version') {
                $this->os->reset([
                    'name' => 'Series60',
                    'version' => new Version(['value' => $match[3]]),
                    'family' => new Family(['name' => 'Symbian'])
                ]);

                $device = Data\DeviceModels::identify('symbian', $match[5]);
                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }

            if ($match[2] == 'WP7') {
                $this->os->reset([
                    'name' => 'Windows Phone',
                    'version' => new Version(['value' => $match[3], 'details' => 2])
                ]);

                $device = Data\DeviceModels::identify('wp', $match[5]);
                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }
        }

        /* Yahoo */

        if (preg_match('/YahooMobile(?:Messenger|Mail|Weather)\/1.0 \(Android (Messenger|Mail|Weather); ([0-9\.]+)\) \([^;]+; ?[^;]+; ?([^;]+); ?([0-9\.]+)\/[^\;\)\/]+\)/u', $ua, $match)) {
            $this->browser->name = 'Yahoo ' . $match[1];
            $this->browser->version = new Version(['value' => $match[2], 'details' => 3]);

            switch ($match[1]) {
                case 'Messenger':
                    $this->browser->type = Constants\BrowserType::APP_CHAT;
                    break;
                case 'Mail':
                    $this->browser->type = Constants\BrowserType::APP_EMAIL;
                    break;
                case 'Weather':
                    $this->browser->type = Constants\BrowserType::APP_NEWS;
                    break;
            }

            $this->os->reset([
                'name' => 'Android',
                'version' => new Version(['value' => $match[4]])
            ]);

            $this->device->type = Constants\DeviceType::MOBILE;

            $device = Data\DeviceModels::identify('android', $match[3]);
            if ($device->identified) {
                $device->identified |= $this->device->identified;
                $this->device = $device;
            }
        }

        /* Yahoo Mobile App */

        if (preg_match('/YahooJMobileApp\/[0-9\.]+ \(Android [a-z]+; ([0-9\.]+)\) \([^;]+; ?[^;]+; ?[^;]+; ?([^;]+); ?([0-9\.]+)\/[^\;\)\/]+\)/u', $ua, $match)) {
            $this->browser->name = 'Yahoo Mobile';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 3]);
            $this->browser->type = Constants\BrowserType::APP_SEARCH;

            $this->os->reset([
                'name' => 'Android',
                'version' => new Version(['value' => $match[3]])
            ]);

            $this->device->type = Constants\DeviceType::MOBILE;

            $device = Data\DeviceModels::identify('android', $match[2]);
            if ($device->identified) {
                $device->identified |= $this->device->identified;
                $this->device = $device;
            }
        }

        /* ICQ */

        if (preg_match('/ICQ_Android\/([0-9\.]+) \(Android; [0-9]+; ([0-9\.]+); [^;]+; ([^;]+);/u', $ua, $match)) {
            $this->browser->name = 'ICQ';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 3]);
            $this->browser->type = Constants\BrowserType::APP_CHAT;

            $this->os->reset([
                'name' => 'Android',
                'version' => new Version(['value' => $match[2]])
            ]);

            $this->device->type = Constants\DeviceType::MOBILE;

            $device = Data\DeviceModels::identify('android', $match[3]);
            if ($device->identified) {
                $device->identified |= $this->device->identified;
                $this->device = $device;
            }
        }

        /* Facebook for Android */

        if (preg_match('/^\[FBAN\/(FB4A|PAAA);.*FBDV\/([^;]+);.*FBSV\/([0-9\.]+);/u', $ua, $match)) {
            if ($match[1] == 'FB4A') {
                $this->browser->name = 'Facebook';
                $this->browser->version = null;
                $this->browser->type = Constants\BrowserType::APP_SOCIAL;
            }

            if ($match[1] == 'PAAA') {
                $this->browser->name = 'Facebook Pages';
                $this->browser->version = null;
                $this->browser->type = Constants\BrowserType::APP_SOCIAL;
            }

            $this->os->reset([
                'name' => 'Android',
                'version' => new Version(['value' => $match[3]])
            ]);

            $this->device->type = Constants\DeviceType::MOBILE;

            $device = Data\DeviceModels::identify('android', $match[2]);
            if ($device->identified) {
                $device->identified |= $this->device->identified;
                $this->device = $device;
            }
        }

        /* VK */

        if (preg_match('/^VKAndroidApp\/([0-9\.]+)-[0-9]+ \(Android ([^;]+); SDK [^;]+; [^;]+; [a-z]+ ([^;]+);/iu', $ua, $match)) {
            $this->browser->name = 'VK';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::APP_SOCIAL;

            $this->os->reset([
                'name' => 'Android',
                'version' => new Version(['value' => $match[2]])
            ]);

            $this->device->model = $match[3];
            $this->device->identified |= Constants\Id::PATTERN;
            $this->device->type = Constants\DeviceType::MOBILE;

            $device = Data\DeviceModels::identify('android', $match[3]);
            if ($device->identified) {
                $device->identified |= $this->device->identified;
                $this->device = $device;
            }
        }
    }

    private function detectRemainingApplications($ua)
    {
        if ($data = Data\Applications::identifyOther($ua)) {
            $this->browser->set($data['browser']);

            if (!empty($data['device'])) {
                $this->device->set($data['device']);
            }
        }
    }
}
