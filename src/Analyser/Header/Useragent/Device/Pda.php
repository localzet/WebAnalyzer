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
use localzet\WebAnalyzer\Data;
use localzet\WebAnalyzer\Model\Family;
use localzet\WebAnalyzer\Model\Version;

trait Pda
{
    private function detectPda($ua)
    {
        if (!preg_match('/(CASIO|Palm|Psion|pdQ|COM|airboard|sharp|pda|POCKET-E|OASYS|NTT\/PI)/ui', $ua)) {
            return;
        }

        $this->detectCasio($ua);
        $this->detectPalm($ua);
        $this->detectPsion($ua);
        $this->detectSonyMylo($ua);
        $this->detectSonyAirboard($ua);
        $this->detectSharpZaurus($ua);
        $this->detectSharpShoin($ua);
        $this->detectPanasonicPocketE($ua);
        $this->detectFujitsuOasys($ua);
        $this->detectNttPetitWeb($ua);
    }


    /* Casio */

    private function detectCasio($ua)
    {
        if (preg_match('/Product\=CASIO\/([^\);]+)[\);]/ui', $ua, $match)) {
            $this->device->manufacturer = 'Casio';
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->type = Constants\DeviceType::PDA;

            if ($match[1] == 'CASSIOPEIA BE') {
                $this->device->model = 'Cassiopeia';
            }

            if ($match[1] == 'PPP101') {
                $this->device->model = 'Pocket PostPet';
                $this->device->carrier = 'DoCoMo';
            }
        }
    }


    /* Palm */

    private function detectPalm($ua)
    {
        if (preg_match('/PalmOS/iu', $ua, $match)) {
            $this->os->name = 'Palm OS';
            $this->device->type = Constants\DeviceType::PDA;

            if (preg_match('/PalmOS ([0-9.]*)/iu', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }

            if (preg_match('/; ([^;)]+)\)/u', $ua, $match)) {
                $device = Data\DeviceModels::identify('palmos', $match[1]);

                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }

            if (preg_match('/PalmOS\/([a-z]+)\/model ([^\/]+)\//iu', $ua, $match)) {
                $device = Data\DeviceModels::identify('palmos', $match[1] . '-' . $match[2]);

                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }
        }

        if (preg_match('/Palm OS ([0-9.]*)/iu', $ua, $match)) {
            $this->os->name = 'Palm OS';
            $this->os->version = new Version(['value' => $match[1]]);
            $this->device->type = Constants\DeviceType::PDA;
        }

        if (preg_match('/PalmSource/u', $ua, $match)) {
            $this->os->name = 'Palm OS';
            $this->os->version = null;
            $this->device->type = Constants\DeviceType::PDA;

            if (preg_match('/PalmSource\/([^;]+)/u', $ua, $match)) {
                $this->device->model = $match[1];
                $this->device->identified = Constants\Id::PATTERN;
            }

            if (isset($this->device->model) && $this->device->model) {
                $device = Data\DeviceModels::identify('palmos', $this->device->model);

                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }
        }

        /* Some model markers */

        if (preg_match('/PalmPilot Pro/ui', $ua, $match)) {
            $this->device->manufacturer = 'Palm';
            $this->device->model = 'Pilot Professional';
            $this->device->identified |= Constants\Id::MATCH_UA;
        }

        if (preg_match('/pdQbrowser/ui', $ua, $match)) {
            $this->device->manufacturer = 'Kyocera';
            $this->device->model = 'QCP-6035';
            $this->device->identified |= Constants\Id::MATCH_UA;
        }
    }


    /* PSION */

    private function detectPsion($ua)
    {
        if (preg_match('/Psion Cpw\//iu', $ua, $match)) {
            $this->browser->name = 'WAP Browser';
            $this->browser->version = null;
            $this->browser->type = Constants\BrowserType::BROWSER;

            $this->os->name = 'EPOC';
            $this->os->family = new Family(['name' => 'Symbian']);

            $this->device->manufacturer = 'Psion';
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->type = Constants\DeviceType::PDA;

            if (preg_match('/\(([A-Z0-9]+)\)/u', $ua, $match)) {
                switch ($match[1]) {
                    case 'S5':
                        $this->device->model = 'Series 5mx';
                        break;
                    case 'S7':
                        $this->device->model = 'Series 7';
                        break;
                    case 'RV':
                        $this->device->model = 'Revo';
                        break;
                }
            }
        }
    }


    /* Sony Mylo */

    private function detectSonyMylo($ua)
    {
        if (preg_match('/SONY\/COM([0-9])/ui', $ua, $match)) {
            $this->device->manufacturer = 'Sony';
            $this->device->model = 'Mylo ' . $match[1];
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->type = Constants\DeviceType::PDA;

            $this->os->reset();

            if (preg_match('/Qt embedded/ui', $ua, $match)) {
                $this->os->name = 'Qtopia';
            }
        }
    }


    /* Sony Airboard */

    private function detectSonyAirboard($ua)
    {
        if (preg_match('/SONY\/airboard\/IDT-([A-Z0-9]+)/ui', $ua, $match)) {
            $this->device->manufacturer = 'Sony';
            $this->device->model = 'Airboard ' . $match[1];
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->type = Constants\DeviceType::PDA;
        }

        if (preg_match('/LocationFreeTV; Airboard\/(LF-[A-Z0-9]+)/ui', $ua, $match)) {
            $this->device->manufacturer = 'Sony';
            $this->device->model = 'Airboard ' . $match[1];
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->type = Constants\DeviceType::PDA;
        }
    }


    /* Sharp Zaurus */

    private function detectSharpZaurus($ua)
    {
        if (preg_match('/sharp pda browser\/([0-9\.]+)/ui', $ua, $match)) {
            $this->device->manufacturer = 'Sharp';
            $this->device->model = 'Zaurus';
            $this->device->type = Constants\DeviceType::PDA;

            if (preg_match('/\(([A-Z0-9\-]+)\/[0-9\.]+\)/ui', $ua, $match)) {
                $this->device->model = 'Zaurus ' . $match[1];
                $this->device->identified |= Constants\Id::MATCH_UA;
                $this->device->generic = false;
            }
        }

        if (preg_match('/\(PDA; (SL-[A-Z][0-9]+)\/[0-9\.]/ui', $ua, $match)) {
            $this->device->manufacturer = 'Sharp';
            $this->device->model = 'Zaurus ' . $match[1];
            $this->device->type = Constants\DeviceType::PDA;
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->generic = false;
        }
    }


    /* Sharp Shoin (Word Processor) */

    private function detectSharpShoin($ua)
    {
        if (preg_match('/sharp wd browser\/([0-9\.]+)/ui', $ua, $match)) {
            $this->device->manufacturer = 'Sharp';
            $this->device->model = 'Mobile Shoin';
            $this->device->type = Constants\DeviceType::PDA;

            if (preg_match('/\(([A-Z0-9\-]+)\/[0-9\.]+\)/ui', $ua, $match)) {
                $this->device->model = 'Mobile Shoin ' . $match[1];
                $this->device->identified |= Constants\Id::MATCH_UA;
                $this->device->generic = false;
            }
        }
    }


    /* Panasonic POCKET・E */

    private function detectPanasonicPocketE($ua)
    {
        if (preg_match('/Product\=Panasonic\/POCKET-E/ui', $ua, $match)) {
            $this->device->manufacturer = 'Panasonic';
            $this->device->model = 'POCKET・E';
            $this->device->type = Constants\DeviceType::PDA;
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->generic = false;
        }
    }


    /* Fujitsu OASYS */

    private function detectFujitsuOasys($ua)
    {
        if (preg_match('/Fujitsu; OASYS/ui', $ua, $match)) {
            $this->device->manufacturer = 'Fujitsu';
            $this->device->model = 'OASYS';
            $this->device->type = Constants\DeviceType::PDA;
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->generic = false;

            if (preg_match('/eNavigator/ui', $ua, $match)) {
                $this->browser->name = 'eNavigator';
                $this->browser->version = null;
                $this->browser->type = Constants\BrowserType::BROWSER;
            }
        }
    }


    /* PetitWeb */

    private function detectNttPetitWeb($ua)
    {
        if (preg_match('/Product\=NTT\/(PI-[0-9]+)/ui', $ua, $match)) {
            $this->device->manufacturer = 'NTT';
            $this->device->model = 'PetitWeb ' . $match[1];
            $this->device->type = Constants\DeviceType::PDA;
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->generic = false;
        }
    }
}
