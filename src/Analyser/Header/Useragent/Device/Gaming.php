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

trait Gaming
{
    private function detectGaming($ua)
    {
        if (!preg_match('/(Nintendo|Nitro|PlayStation|PS[0-9]|Sega|Dreamcast|Xbox)/ui', $ua)) {
            return;
        }

        $this->detectNintendo($ua);
        $this->detectPlaystation($ua);
        $this->detectXbox($ua);
        $this->detectSega($ua);
    }

    /* Nintendo Wii and DS */

    private function detectNintendo($ua)
    {
        /* Switch */

        if (preg_match('/Nintendo Switch/u', $ua)) {
            $this->os->reset();
            $this->device->setIdentification([
                'manufacturer' => 'Nintendo',
                'model' => 'Switch',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::CONSOLE
            ]);
        }

        /* Wii */

        if (preg_match('/Nintendo Wii/u', $ua)) {
            $this->os->reset();
            $this->device->setIdentification([
                'manufacturer' => 'Nintendo',
                'model' => 'Wii',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::CONSOLE
            ]);
        }

        /* Wii U */

        if (preg_match('/Nintendo Wii ?U/u', $ua)) {
            $this->os->reset();
            $this->device->setIdentification([
                'manufacturer' => 'Nintendo',
                'model' => 'Wii U',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::CONSOLE
            ]);
        }

        /* DS */

        if (preg_match('/Nintendo DS/u', $ua) || preg_match('/Nitro.*Opera/u', $ua)) {
            $this->os->reset();
            $this->device->setIdentification([
                'manufacturer' => 'Nintendo',
                'model' => 'DS',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::PORTABLE
            ]);
        }

        /* DSi */

        if (preg_match('/Nintendo DSi/u', $ua)) {
            $this->os->reset();
            $this->device->setIdentification([
                'manufacturer' => 'Nintendo',
                'model' => 'DSi',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::PORTABLE
            ]);
        }

        /* 3DS */

        if (preg_match('/Nintendo 3DS/u', $ua)) {
            $this->os->reset();
            $this->os->identifyVersion('/Version\/([0-9.]*[0-9])/u', $ua);

            $this->engine->set([
                'name' => 'WebKit'
            ]);

            $this->device->setIdentification([
                'manufacturer' => 'Nintendo',
                'model' => '3DS',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::PORTABLE
            ]);
        }

        /* New 3DS */

        if (preg_match('/New Nintendo 3DS/u', $ua)) {
            $this->os->reset();
            $this->os->identifyVersion('/Version\/([0-9.]*[0-9])/u', $ua);

            $this->device->setIdentification([
                'manufacturer' => 'Nintendo',
                'model' => 'New 3DS',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::PORTABLE
            ]);
        }
    }


    /* Sony PlayStation */

    private function detectPlaystation($ua)
    {
        /* PlayStation Portable */

        if (preg_match('/PlayStation Portable/u', $ua)) {
            $this->os->reset();

            $this->engine->set([
                'name' => 'NetFront'
            ]);

            $this->device->setIdentification([
                'manufacturer' => 'Sony',
                'model' => 'PlayStation Portable',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::PORTABLE
            ]);
        }

        /* PlayStation Vita */

        if (preg_match('/PlayStation Vita/ui', $ua)) {
            $this->os->reset();
            $this->os->identifyVersion('/PlayStation Vita ([0-9.]*)/u', $ua);

            $this->device->setIdentification([
                'manufacturer' => 'Sony',
                'model' => 'PlayStation Vita',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::PORTABLE
            ]);

            if (preg_match('/VTE\//u', $ua)) {
                $this->device->model = 'PlayStation TV';
                $this->device->subtype = Constants\DeviceSubType::CONSOLE;
            }
        }

        /* PlayStation 2 */

        if (preg_match('/PlayStation2/ui', $ua) || preg_match('/\(PS2/u', $ua)) {
            $this->os->reset();

            $this->device->setIdentification([
                'manufacturer' => 'Sony',
                'model' => 'PlayStation 2',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::CONSOLE
            ]);
        }

        /* PlayStation 3 */

        if (preg_match('/PlayStation 3/ui', $ua) || preg_match('/\(PS3/u', $ua)) {
            $this->os->reset();
            $this->os->identifyVersion('/PLAYSTATION 3;? ([0-9.]*)/u', $ua);

            if (preg_match('/PLAYSTATION 3; [123]/', $ua)) {
                $this->engine->set([
                    'name' => 'NetFront'
                ]);
            }

            $this->device->setIdentification([
                'manufacturer' => 'Sony',
                'model' => 'PlayStation 3',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::CONSOLE
            ]);
        }

        /* PlayStation 4 */

        if (preg_match('/PlayStation 4/ui', $ua) || preg_match('/\(PS4/u', $ua)) {
            $this->os->reset();
            $this->os->identifyVersion('/PlayStation 4 ([0-9.]*)/u', $ua);

            $this->device->setIdentification([
                'manufacturer' => 'Sony',
                'model' => 'PlayStation 4',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::CONSOLE
            ]);
        }

        /* PlayStation 5 */

        if (preg_match('/PlayStation 5/ui', $ua) || preg_match('/\(PS5/u', $ua)) {
            $this->os->reset();
            $this->os->identifyVersion('/PlayStation 5 ([0-9.]*)/u', $ua);

            $this->device->setIdentification([
                'manufacturer' => 'Sony',
                'model' => 'PlayStation 5',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::CONSOLE
            ]);
        }
    }


    /* Microsoft Xbox */

    private function detectXbox($ua)
    {
        /* Xbox One */
        if (preg_match('/Xbox One\)/u', $ua, $match)) {
            if ($this->isOs('Windows Phone', '=', '10')) {
                $this->os->name = 'Windows';
                $this->os->version->alias = '10';
            }

            if (!$this->isOs('Windows', '=', '10')) {
                $this->os->reset();
            }

            $this->device->setIdentification([
                'manufacturer' => 'Microsoft',
                'model' => 'Xbox One',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::CONSOLE
            ]);

            /* Xbox Series X */
        } elseif (preg_match('/Xbox Series X\)/u', $ua, $match)) {
            $this->os->reset();
            $this->device->setIdentification([
                'manufacturer' => 'Microsoft',
                'model' => 'Xbox Series X',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::CONSOLE
            ]);

            /* Xbox 360 */
        } elseif (preg_match('/Xbox\)$/u', $ua, $match)) {
            $this->os->reset();
            $this->device->setIdentification([
                'manufacturer' => 'Microsoft',
                'model' => 'Xbox 360',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::CONSOLE
            ]);
        }
    }


    /* Sega */

    private function detectSega($ua)
    {
        /* Sega Saturn */

        if (preg_match('/SEGASATURN/u', $ua, $match)) {
            $this->os->reset();
            $this->device->setIdentification([
                'manufacturer' => 'Sega',
                'model' => 'Saturn',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::CONSOLE
            ]);
        }

        /* Sega Dreamcast */

        if (preg_match('/Dreamcast/u', $ua, $match)) {
            $this->os->reset();
            $this->device->setIdentification([
                'manufacturer' => 'Sega',
                'model' => 'Dreamcast',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::CONSOLE
            ]);
        }
    }
}
