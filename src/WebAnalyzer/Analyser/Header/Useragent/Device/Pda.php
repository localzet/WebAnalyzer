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
            $this->data->device->manufacturer = 'Casio';
            $this->data->device->identified |= Constants\Id::MATCH_UA;
            $this->data->device->type = Constants\DeviceType::PDA;

            if ($match[1] == 'CASSIOPEIA BE') {
                $this->data->device->model = 'Cassiopeia';
            }

            if ($match[1] == 'PPP101') {
                $this->data->device->model = 'Pocket PostPet';
                $this->data->device->carrier = 'DoCoMo';
            }
        }
    }


    /* Palm */

    private function detectPalm($ua)
    {
        if (preg_match('/PalmOS/iu', $ua, $match)) {
            $this->data->os->name = 'Palm OS';
            $this->data->device->type = Constants\DeviceType::PDA;

            if (preg_match('/PalmOS ([0-9.]*)/iu', $ua, $match)) {
                $this->data->os->version = new Version(['value' => $match[1]]);
            }

            if (preg_match('/; ([^;)]+)\)/u', $ua, $match)) {
                $device = Data\DeviceModels::identify('palmos', $match[1]);

                if ($device->identified) {
                    $device->identified |= $this->data->device->identified;
                    $this->data->device = $device;
                }
            }

            if (preg_match('/PalmOS\/([a-z]+)\/model ([^\/]+)\//iu', $ua, $match)) {
                $device = Data\DeviceModels::identify('palmos', $match[1] . '-' . $match[2]);

                if ($device->identified) {
                    $device->identified |= $this->data->device->identified;
                    $this->data->device = $device;
                }
            }
        }

        if (preg_match('/Palm OS ([0-9.]*)/iu', $ua, $match)) {
            $this->data->os->name = 'Palm OS';
            $this->data->os->version = new Version(['value' => $match[1]]);
            $this->data->device->type = Constants\DeviceType::PDA;
        }

        if (preg_match('/PalmSource/u', $ua, $match)) {
            $this->data->os->name = 'Palm OS';
            $this->data->os->version = null;
            $this->data->device->type = Constants\DeviceType::PDA;

            if (preg_match('/PalmSource\/([^;]+)/u', $ua, $match)) {
                $this->data->device->model = $match[1];
                $this->data->device->identified = Constants\Id::PATTERN;
            }

            if (isset($this->data->device->model) && $this->data->device->model) {
                $device = Data\DeviceModels::identify('palmos', $this->data->device->model);

                if ($device->identified) {
                    $device->identified |= $this->data->device->identified;
                    $this->data->device = $device;
                }
            }
        }

        /* Some model markers */

        if (preg_match('/PalmPilot Pro/ui', $ua, $match)) {
            $this->data->device->manufacturer = 'Palm';
            $this->data->device->model = 'Pilot Professional';
            $this->data->device->identified |= Constants\Id::MATCH_UA;
        }

        if (preg_match('/pdQbrowser/ui', $ua, $match)) {
            $this->data->device->manufacturer = 'Kyocera';
            $this->data->device->model = 'QCP-6035';
            $this->data->device->identified |= Constants\Id::MATCH_UA;
        }
    }


    /* PSION */

    private function detectPsion($ua)
    {
        if (preg_match('/Psion Cpw\//iu', $ua, $match)) {
            $this->data->browser->name = 'WAP Browser';
            $this->data->browser->version = null;
            $this->data->browser->type = Constants\BrowserType::BROWSER;

            $this->data->os->name = 'EPOC';
            $this->data->os->family = new Family(['name' => 'Symbian']);

            $this->data->device->manufacturer = 'Psion';
            $this->data->device->identified |= Constants\Id::MATCH_UA;
            $this->data->device->type = Constants\DeviceType::PDA;

            if (preg_match('/\(([A-Z0-9]+)\)/u', $ua, $match)) {
                switch ($match[1]) {
                    case 'S5':
                        $this->data->device->model = 'Series 5mx';
                        break;
                    case 'S7':
                        $this->data->device->model = 'Series 7';
                        break;
                    case 'RV':
                        $this->data->device->model = 'Revo';
                        break;
                }
            }
        }
    }


    /* Sony Mylo */

    private function detectSonyMylo($ua)
    {
        if (preg_match('/SONY\/COM([0-9])/ui', $ua, $match)) {
            $this->data->device->manufacturer = 'Sony';
            $this->data->device->model = 'Mylo ' . $match[1];
            $this->data->device->identified |= Constants\Id::MATCH_UA;
            $this->data->device->type = Constants\DeviceType::PDA;

            $this->data->os->reset();

            if (preg_match('/Qt embedded/ui', $ua, $match)) {
                $this->data->os->name = 'Qtopia';
            }
        }
    }


    /* Sony Airboard */

    private function detectSonyAirboard($ua)
    {
        if (preg_match('/SONY\/airboard\/IDT-([A-Z0-9]+)/ui', $ua, $match)) {
            $this->data->device->manufacturer = 'Sony';
            $this->data->device->model = 'Airboard ' . $match[1];
            $this->data->device->identified |= Constants\Id::MATCH_UA;
            $this->data->device->type = Constants\DeviceType::PDA;
        }

        if (preg_match('/LocationFreeTV; Airboard\/(LF-[A-Z0-9]+)/ui', $ua, $match)) {
            $this->data->device->manufacturer = 'Sony';
            $this->data->device->model = 'Airboard ' . $match[1];
            $this->data->device->identified |= Constants\Id::MATCH_UA;
            $this->data->device->type = Constants\DeviceType::PDA;
        }
    }


    /* Sharp Zaurus */

    private function detectSharpZaurus($ua)
    {
        if (preg_match('/sharp pda browser\/([0-9\.]+)/ui', $ua, $match)) {
            $this->data->device->manufacturer = 'Sharp';
            $this->data->device->model = 'Zaurus';
            $this->data->device->type = Constants\DeviceType::PDA;

            if (preg_match('/\(([A-Z0-9\-]+)\/[0-9\.]+\)/ui', $ua, $match)) {
                $this->data->device->model = 'Zaurus ' . $match[1];
                $this->data->device->identified |= Constants\Id::MATCH_UA;
                $this->data->device->generic = false;
            }
        }

        if (preg_match('/\(PDA; (SL-[A-Z][0-9]+)\/[0-9\.]/ui', $ua, $match)) {
            $this->data->device->manufacturer = 'Sharp';
            $this->data->device->model = 'Zaurus ' . $match[1];
            $this->data->device->type = Constants\DeviceType::PDA;
            $this->data->device->identified |= Constants\Id::MATCH_UA;
            $this->data->device->generic = false;
        }
    }


    /* Sharp Shoin (Word Processor) */

    private function detectSharpShoin($ua)
    {
        if (preg_match('/sharp wd browser\/([0-9\.]+)/ui', $ua, $match)) {
            $this->data->device->manufacturer = 'Sharp';
            $this->data->device->model = 'Mobile Shoin';
            $this->data->device->type = Constants\DeviceType::PDA;

            if (preg_match('/\(([A-Z0-9\-]+)\/[0-9\.]+\)/ui', $ua, $match)) {
                $this->data->device->model = 'Mobile Shoin ' . $match[1];
                $this->data->device->identified |= Constants\Id::MATCH_UA;
                $this->data->device->generic = false;
            }
        }
    }


    /* Panasonic POCKET・E */

    private function detectPanasonicPocketE($ua)
    {
        if (preg_match('/Product\=Panasonic\/POCKET-E/ui', $ua, $match)) {
            $this->data->device->manufacturer = 'Panasonic';
            $this->data->device->model = 'POCKET・E';
            $this->data->device->type = Constants\DeviceType::PDA;
            $this->data->device->identified |= Constants\Id::MATCH_UA;
            $this->data->device->generic = false;
        }
    }


    /* Fujitsu OASYS */

    private function detectFujitsuOasys($ua)
    {
        if (preg_match('/Fujitsu; OASYS/ui', $ua, $match)) {
            $this->data->device->manufacturer = 'Fujitsu';
            $this->data->device->model = 'OASYS';
            $this->data->device->type = Constants\DeviceType::PDA;
            $this->data->device->identified |= Constants\Id::MATCH_UA;
            $this->data->device->generic = false;

            if (preg_match('/eNavigator/ui', $ua, $match)) {
                $this->data->browser->name = 'eNavigator';
                $this->data->browser->version = null;
                $this->data->browser->type = Constants\BrowserType::BROWSER;
            }
        }
    }


    /* PetitWeb */

    private function detectNttPetitWeb($ua)
    {
        if (preg_match('/Product\=NTT\/(PI-[0-9]+)/ui', $ua, $match)) {
            $this->data->device->manufacturer = 'NTT';
            $this->data->device->model = 'PetitWeb ' . $match[1];
            $this->data->device->type = Constants\DeviceType::PDA;
            $this->data->device->identified |= Constants\Id::MATCH_UA;
            $this->data->device->generic = false;
        }
    }
}
