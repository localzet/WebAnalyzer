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

trait Television
{
    private function detectTelevision($ua)
    {
        /* Detect the type based on some common markers */
        $this->detectGenericTelevision($ua);

        /* Try to parse some generic methods to store device information */
        $this->detectGenericTelevisionModels($ua);
        $this->detectGenericInettvBrowser($ua);
        $this->detectGenericHbbTV($ua);

        /* Look for specific manufacturers and models */
        $this->detectPanasonicTelevision($ua);
        $this->detectSharpTelevision($ua);
        $this->detectSamsungTelevision($ua);
        $this->detectSonyTelevision($ua);
        $this->detectPhilipsTelevision($ua);
        $this->detectLgTelevision($ua);
        $this->detectToshibaTelevision($ua);
        $this->detectSanyoTelevision($ua);

        /* Try to detect set top boxes from various manufacturers */
        $this->detectSettopboxes($ua);

        /* Improve model names */
        $this->improveModelsOnDeviceTypeTelevision();
    }


    /* Generic markers */

    private function detectGenericTelevision($ua)
    {
        if (preg_match('/CE-HTML/u', $ua)) {
            $this->device->type = Constants\DeviceType::TELEVISION;
        }

        if (preg_match('/SmartTvA\//u', $ua)) {
            $this->device->type = Constants\DeviceType::TELEVISION;
        }

        if (preg_match('/NETRANGEMMH/u', $ua)) {
            $this->device->type = Constants\DeviceType::TELEVISION;
        }
    }


    /* Toshiba */

    private function detectToshibaTelevision($ua)
    {
        if (preg_match('/Toshiba_?TP\//u', $ua) || preg_match('/TSBNetTV ?\//u', $ua)) {
            $this->device->manufacturer = 'Toshiba';
            $this->device->series = 'Smart TV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
        }

        if (preg_match('/TOSHIBA;[^;]+;([A-Z]+[0-9]+[A-Z]+);/u', $ua, $match)) {
            $this->device->manufacturer = 'Toshiba';
            $this->device->model = $match[1];
            $this->device->series = 'Smart TV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
        }
    }


    /* LG */

    private function detectLgTelevision($ua)
    {
        if (preg_match('/(LGSmartTV|LG smartTV)/u', $ua)) {
            $this->device->manufacturer = 'LG';
            $this->device->series = 'Smart TV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
        }

        if (preg_match('/UPLUSTVBROWSER/u', $ua)) {
            $this->device->manufacturer = 'LG';
            $this->device->series = 'U+ tv';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
        }


        /* NetCast */

        if (preg_match('/LG NetCast\.(TV|Media)-([0-9]*)/u', $ua, $match)) {
            $this->device->manufacturer = 'LG';
            $this->device->series = 'NetCast ' . $match[1] . ' ' . $match[2];
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;

            if (preg_match('/LG Browser\/[0-9.]+\([^;]+; LGE; ([^;]+);/u', $ua, $match)) {
                if (!str_starts_with($match[1], 'GLOBAL') && !str_starts_with($match[1], 'NETCAST')) {
                    $this->device->model = $match[1];
                }
            }
        }

        /* NetCast */

        if ($ua == "Mozilla/5.0 (X11; Linux; ko-KR) AppleWebKit/534.26+ (KHTML, like Gecko) Version/5.0 Safari/534.26+" ||
            $ua == "Mozilla/5.0 (DirectFB; Linux; ko-KR) AppleWebKit/534.26 (KHTML, like Gecko) Version/5.0 Safari/534.26" ||
            $ua == "Mozilla/5.0 (DirectFB; Linux; ko-KR) AppleWebKit/534.26+ (KHTML, like Gecko) Version/5.0 Safari/534.26+") {
            $this->device->manufacturer = 'LG';
            $this->device->series = 'NetCast TV 2012';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
        }


        /* NetCast or WebOS */

        if (preg_match('/NetCast/u', $ua) && preg_match('/SmartTV\/([0-9])/u', $ua, $match)) {
            $this->device->manufacturer = 'LG';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;

            if (intval($match[1]) < 5) {
                $this->device->series = 'NetCast TV';
            } else {
                $this->device->series = 'webOS TV';

                $this->os->reset([
                    'name' => 'webOS',
                    'hidden' => true
                ]);
            }
        }

        /* WebOS */

        if (preg_match('/Web[O0]S/u', $ua) && preg_match('/Large Screen/u', $ua)) {
            $this->device->manufacturer = 'LG';
            $this->device->series = 'webOS TV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;

            $this->os->reset([
                'name' => 'webOS',
                'hidden' => true
            ]);
        }

        if (preg_match('/Web[O0]S; Linux\/SmartTV/u', $ua)) {
            $this->device->manufacturer = 'LG';
            $this->device->series = 'webOS TV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;

            $this->os->reset([
                'name' => 'webOS',
                'hidden' => true
            ]);
        }

        if (preg_match('/webOS\.TV-([0-9]+)/u', $ua, $match)) {
            $this->device->manufacturer = 'LG';
            $this->device->series = 'webOS TV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;

            if (preg_match('/LG Browser\/[0-9.]+\(LGE; ([^;]+);/u', $ua, $match)) {
                if (strtoupper(substr($match[1], 0, 5)) != 'WEBOS') {
                    $this->device->model = $match[1];
                }
            }

            $this->os->reset([
                'name' => 'webOS',
                'hidden' => true
            ]);
        }

        if (preg_match('/PBRM\//u', $ua)) {
            $this->browser->name = "Pro:Centric";
            $this->browser->version = null;

            $this->device->manufacturer = 'LG';
            $this->device->series = 'webOS TV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;

            if (preg_match('/PBRM\/[0-9.]+ \( ;LGE ;([^;]+) ;/u', $ua, $match)) {
                if (strtoupper(substr($match[1], 0, 5)) != 'WEBOS') {
                    $this->device->model = $match[1];
                }
            }

            $this->os->reset([
                'name' => 'webOS',
                'hidden' => true
            ]);
        }
    }


    /* Philips */

    private function detectPhilipsTelevision($ua)
    {
        if (preg_match('/NETTV\//u', $ua)) {
            $this->device->manufacturer = 'Philips';
            $this->device->series = 'Net TV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;

            if (preg_match('/AquosTV/u', $ua)) {
                $this->device->manufacturer = 'Sharp';
                $this->device->series = 'Aquos TV';
            }

            if (preg_match('/BANGOLUFSEN/u', $ua)) {
                $this->device->manufacturer = 'Bang & Olufsen';
                $this->device->series = 'Smart TV';
            }

            if (preg_match('/PHILIPS-AVM/u', $ua)) {
                $this->device->series = 'Blu-ray Player';
            }
        }

        if (preg_match('/PHILIPS_OLS_20[0-9]+/u', $ua)) {
            $this->device->manufacturer = 'Philips';
            $this->device->series = 'Net TV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
        }
    }


    /* Sony */

    private function detectSonyTelevision($ua)
    {
        if (preg_match('/SonyCEBrowser/u', $ua)) {
            $this->device->manufacturer = 'Sony';
            $this->device->series = 'Smart TV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;

            if (preg_match('/SonyCEBrowser\/[0-9.]+ \((?:BDPlayer; |DTV[0-9]+\/)?([^;_]+)/u', $ua, $match)) {
                if ($match[1] != 'ModelName') {
                    $this->device->model = $match[1];
                }
            }
        }

        if (preg_match('/SonyDTV/u', $ua)) {
            $this->device->manufacturer = 'Sony';
            $this->device->series = 'Smart TV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;

            if (preg_match('/(KDL-?[0-9]+[A-Z]+[0-9]+)/u', $ua, $match)) {
                $this->device->model = $match[1];
                $this->device->generic = false;
            }

            if (preg_match('/(XBR-?[0-9]+[A-Z]+[0-9]+)/u', $ua, $match)) {
                $this->device->model = $match[1];
                $this->device->generic = false;
            }
        }

        if (preg_match('/SonyBDP/u', $ua)) {
            $this->device->manufacturer = 'Sony';
            $this->device->series = "Blu-ray Player";
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
        }

        if (preg_match('/SmartBD/u', $ua) && preg_match('/(BDP-[A-Z][0-9]+)/u', $ua, $match)) {
            $this->device->manufacturer = 'Sony';
            $this->device->model = $match[1];
            $this->device->series = 'Blu-ray Player';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
        }

        if (preg_match('/\s+([0-9]+)BRAVIA/u', $ua, $match)) {
            $this->device->manufacturer = 'Sony';
            $this->device->model = 'Bravia';
            $this->device->series = 'Smart TV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
        }
    }


    /* Samsung */

    private function detectSamsungTelevision($ua)
    {
        if (preg_match('/(SMART-TV;|SmartHub;)/u', $ua)) {
            $this->device->manufacturer = 'Samsung';
            $this->device->series = 'Smart TV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;

            if (preg_match('/Linux\/SmartTV\+([0-9]*)/u', $ua, $match)) {
                $this->device->series = 'Smart TV ' . $match[1];
            } elseif (preg_match('/Maple([0-9]*)/u', $ua, $match)) {
                $this->device->series = 'Smart TV ' . $match[1];
            }
        }

        if (preg_match('/Maple_?([0-9][0-9][0-9][0-9])/u', $ua, $match)) {
            $this->device->manufacturer = 'Samsung';
            $this->device->series = 'Smart TV ' . $match[1];
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;

            if (preg_match('/Linux\/(?:SmartTV)?\+([0-9]{4,4})/u', $ua, $match)) {
                $this->device->series = 'Smart TV ' . $match[1];
            }
        }

        if (preg_match('/Maple ([0-9]+\.[0-9]+)\.[0-9]+/u', $ua, $match)) {
            $this->device->manufacturer = 'Samsung';
            $this->device->series = 'Smart TV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;

            switch ($match[1]) {
                case '5.0':
                    $this->device->series = 'Smart TV 2009';
                    break;
                case '5.1':
                    $this->device->series = 'Smart TV 2010';
                    break;
                case '6.0':
                    $this->device->series = 'Smart TV 2011';
                    break;
            }
        }

        if (preg_match('/Model\/Samsung-(BD-[A-Z][0-9]+)/u', $ua, $match)) {
            $this->device->manufacturer = 'Samsung';
            $this->device->model = $match[1];
            $this->device->series = 'Blu-ray Player';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
        }

        if (preg_match('/olleh tv;/u', $ua)) {
            $this->device->manufacturer = 'Samsung';
            $this->device->model = null;
            $this->device->series = null;
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;

            if (preg_match('/(SMT-[A-Z0-9]+)/u', $ua, $match)) {
                $this->device->model = $match[1];
                $this->device->identifier = $match[1];
                $this->device->generic = false;
            }

            if ($this->device->model == "SMT-E5015") {
                $this->device->model = 'Olleh SkyLife Smart Settopbox';
            }
        }
    }


    /* Sanyo */

    private function detectSanyoTelevision($ua)
    {
        if (preg_match('/Aplix_SANYO_browser/u', $ua)) {
            $this->device->manufacturer = 'Sanyo';
            $this->device->series = 'Internet TV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
        }
    }


    /* Sharp */

    private function detectSharpTelevision($ua)
    {
        if (preg_match('/(AQUOSBrowser|AQUOS-(AS|DMP))/u', $ua)) {
            $this->device->manufacturer = 'Sharp';
            $this->device->series = 'Aquos TV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;

            if (preg_match('/LC\-([0-9]+[A-Z]+[0-9]+[A-Z]+)/u', $ua, $match)) {
                $this->device->model = $match[1];
                $this->device->generic = false;
            }
        }
    }


    /* Panasonic */

    private function detectPanasonicTelevision($ua)
    {
        if (preg_match('/Viera/u', $ua)) {
            $this->device->manufacturer = 'Panasonic';
            $this->device->series = 'Viera';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;

            if (preg_match('/Panasonic\.tv\.(?:mid\.|pro4\.)?([0-9]+)/u', $ua, $match)) {
                $this->device->series = 'Viera ' . $match[1];
            }

            if (preg_match('/\(Panasonic, ([0-9]+),/u', $ua, $match)) {
                $this->device->series = 'Viera ' . $match[1];
            }

            if (preg_match('/Viera\; rv\:34/u', $ua, $match)) {
                $this->device->series = 'Viera 2015';
            }
        }

        if (preg_match('/; Diga;/u', $ua)) {
            $this->device->manufacturer = 'Panasonic';
            $this->device->series = 'Diga';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
        }
    }


    /* Various set top boxes */

    private function detectSettopboxes($ua)
    {
        if (!preg_match('/(lacleTV|LOEWE|KreaTV|ADB|Mstar|TechniSat|Technicolor|Highway|CiscoBrowser|Sunniwell|Enseo|LocationFreeTV|Winbox|DuneHD|Roku|AppleTV|Apple TV|WebTV|OpenTV|MediStream)/ui', $ua)) {
            return;
        }

        /* Orange La clé TV */

        if (preg_match('/lacleTV\//u', $ua)) {
            $this->device->manufacturer = 'Orange';
            $this->device->series = 'La clé TV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
        }

        /* Loewe */

        if (preg_match('/LOEWE\/TV/u', $ua)) {
            $this->device->manufacturer = 'Loewe';
            $this->device->series = 'Smart TV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;

            if (preg_match('/((?:SL|ID)[0-9]+)/u', $ua, $match)) {
                $this->device->model = $match[1];
            }
        }

        /* KreaTV */

        if (preg_match('/KreaTV/u', $ua)) {
            $this->os->reset();

            $this->device->series = 'KreaTV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->generic = false;

            if (preg_match('/Motorola/u', $ua)) {
                $this->device->manufacturer = 'Motorola';
            }
        }

        /* ADB */

        if (preg_match('/\(ADB; ([^\)]+)\)/u', $ua, $match)) {
            $this->os->reset();

            $this->device->manufacturer = 'ADB';
            $this->device->model = ($match[1] != 'Unknown' ? str_replace('ADB', '', $match[1]) . ' ' : '') . 'IPTV receiver';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->generic = false;
        }

        /* MStar */

        if (preg_match('/Mstar;/u', $ua)) {
            $this->os->reset();

            $this->device->manufacturer = 'MStar';
            $this->device->model = 'PVR';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
        }

        /* TechniSat */

        if (preg_match('/TechniSat ([^;]+);/u', $ua, $match)) {
            $this->os->reset();

            $this->device->manufacturer = 'TechniSat';
            $this->device->model = $match[1];
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->generic = false;
        }

        /* Technicolor */

        if (preg_match('/Technicolor_([^;]+);/u', $ua, $match)) {
            $this->os->reset();

            $this->device->manufacturer = 'Technicolor';
            $this->device->model = $match[1];
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->generic = false;
        }

        /* Cisco MediaHighway */

        if (preg_match('/(Media-Highway Evolution|CiscoBrowser\/CI)/u', $ua, $match)) {
            $this->os->reset();

            $this->device->manufacturer = 'Cisco';
            $this->device->model = 'MediaHighway';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->generic = false;
        }

        /* Sunniwell */

        if (preg_match('/Sunniwell/u', $ua) && preg_match('/Resolution/u', $ua)) {
            $this->os->reset();

            $this->device->manufacturer = 'Sunniwell';
            $this->device->series = 'STB';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->generic = false;
        }

        /* Enseo */

        if (preg_match('/Enseo\/([A-Z0-9]+)/u', $ua, $match)) {
            $this->os->reset();

            $this->device->manufacturer = 'Enseo';
            $this->device->model = $match[1];
            $this->device->series = 'STB';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->generic = false;
        }

        /* Sony LocationFreeTV */

        if (preg_match('/LocationFreeTV\/([A-Z0-9\-]+)/u', $ua, $match)) {
            $this->os->reset();

            $this->device->manufacturer = 'Sony';
            $this->device->model = 'LocationFreeTV ' . $match[1];
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->generic = false;
        }

        /* Winbox Evo2 */

        if (preg_match('/Winbox Evo2/u', $ua)) {
            $this->os->reset();

            $this->device->manufacturer = 'Winbox';
            $this->device->model = 'Evo2';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->generic = false;
        }

        /* DuneHD */

        if (preg_match('/DuneHD\//u', $ua)) {
            $this->os->reset();

            $this->device->manufacturer = 'Dune HD';
            $this->device->model = '';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;

            if (preg_match('/DuneHD\/[0-9.]+ \(([^;]+);/u', $ua, $match)) {
                $this->device->model = $match[1];
            }
        }

        /* Roku  */

        if (preg_match('/Roku(?:([0-9]+)[A-Z]+)?\/DVP-(?:([0-9]+)[A-Z]+-)?[0-9\.]+/u', $ua, $match)) {
            $this->os->reset();

            $this->device->manufacturer = 'Roku';
            $this->device->type = Constants\DeviceType::TELEVISION;

            $models = [
                '2000' => 'HD',
                '2050' => 'XD',
                '2100' => 'XDS',
                '2400' => 'LT',
                '2450' => 'LT',
                '2500' => 'HD',
                '2700' => 'LT',
                '2710' => '1 SE',
                '2720' => '2',
                '3000' => '2 HD',
                '3050' => '2 XD',
                '3100' => '2 XS',
                '3400' => 'Streaming Stick, MHL',
                '3420' => 'Streaming Stick, MHL',
                '3500' => 'Streaming Stick, HDMI',
                '3600' => 'Streaming Stick',
                '3700' => 'Express',
                '3710' => 'Express+',
                '3800' => 'Streaming Stick',
                '3810' => 'Streaming Stick+',
                '3900' => 'Express',
                '3910' => 'Express+',
                '3920' => 'Premiere',
                '3921' => 'Premiere+',
                '3930' => 'Express',
                '3931' => 'Express+',
                '4200' => '3',
                '4210' => '2',
                '4230' => '3',
                '4400' => '4',
                '4620' => 'Premiere',
                '4630' => 'Premiere+',
                '4640' => 'Ultra',
                '4660' => 'Ultra',
                '4661' => 'Ultra',
                '4662' => 'Ultra LT',
                '4670' => 'Ultra',
                '4800' => 'Ultra',
            ];

            if (!empty($match[1]) || !empty($match[2])) {
                $model = !empty($match[1]) ? $match[1] : $match[2];

                if (isset($models[$model])) {
                    $this->device->model = $models[$model];
                    $this->device->generic = false;
                }
            }

            $this->device->identified |= Constants\Id::MATCH_UA;
        }

        if (preg_match('/Roku\/DVP-[0-9\.]+ \(([0-9A-Z]{2,2})[0-9]+\./u', $ua, $match)) {
            $this->os->reset();

            $this->device->manufacturer = 'Roku';
            $this->device->type = Constants\DeviceType::TELEVISION;

            $models = [
                '02' => '2 XS',
                '03' => 'LT',
                '04' => '3',
                '07' => 'LT',
                '09' => 'Streaming Stick',
                '29' => 'Ultra',
                '30' => ['TCL', '4K Roku TV'],
                '51' => 'Express',
                'AE' => 'Express',
            ];

            if (!empty($match[1])) {
                $model = $match[1];

                if (isset($models[$model])) {
                    if (is_array($models[$model])) {
                        $this->device->manufacturer = $models[$model][0];
                        $this->device->model = $models[$model][1];
                    } else {
                        $this->device->model = $models[$model];
                    }

                    $this->device->generic = false;
                }
            }

            $this->device->identified |= Constants\Id::MATCH_UA;
        }

        if (preg_match('/\(Roku/u', $ua)) {
            $this->device->manufacturer = 'Roku';
            $this->device->model = '';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
        }

        /* AppleTV */

        if (preg_match('/Apple ?TV/u', $ua)) {
            $this->os->reset();

            $this->device->manufacturer = 'Apple';
            $this->device->model = 'AppleTV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->generic = false;
        }

        /* WebTV */

        if (preg_match('/WebTV\/[0-9.]/u', $ua)) {
            $this->os->reset();

            $this->device->manufacturer = 'Microsoft';
            $this->device->model = 'WebTV';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->generic = false;
        }

        /* MediStream */

        if (preg_match('/MediStream/u', $ua)) {
            $this->os->reset();

            $this->device->manufacturer = 'Bewatec';
            $this->device->model = 'MediStream';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->generic = false;
        }
    }


    /* Generic model information */

    private function detectGenericTelevisionModels($ua)
    {
        if (preg_match('/\(([^,\(]+),\s*([^,\(]+),\s*(?:[Ww]ired|[Ww]ireless)\)/u', $ua, $match)) {
            $vendorName = Data\Manufacturers::identify(Constants\DeviceType::TELEVISION, $match[1]);
            $modelName = trim($match[2]);

            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::PATTERN;

            if (!isset($this->device->series)) {
                $this->device->series = 'Smart TV';
            }

            switch ($vendorName) {
                case 'ARRIS':
                    $this->device->manufacturer = 'Arris';
                    $this->device->model = $modelName;
                    break;

                case 'LG':
                    $this->device->manufacturer = 'LG';

                    switch ($modelName) {
                        case 'WEBOS1':
                        case 'webOS.TV':
                            $this->device->series = 'webOS TV';
                            break;
                        case 'NETCAST4':
                        case 'NetCast4.0':
                        case 'GLOBAL-PLAT4':
                            $this->device->series = 'NetCast TV 2013';
                            break;
                        default:
                            $this->device->model = $modelName;
                            break;
                    }

                    break;

                case 'Google Fiber':
                    $this->device->manufacturer = $vendorName;
                    $this->device->model = 'TV Box';
                    break;

                case 'Sagemcom':
                    $this->device->manufacturer = $vendorName;
                    $this->device->series = 'Settopbox';

                    if (preg_match('/^([A-Z]+[0-9]+)/ui', $modelName, $match)) {
                        $this->device->model = $match[1];
                        unset($this->device->series);
                    }

                    break;

                case 'TiVo':
                    $this->device->manufacturer = 'TiVo';
                    $this->device->series = 'DVR';
                    break;

                default:
                    $this->device->manufacturer = $vendorName;

                    if ($modelName != 'dvb') {
                        $this->device->model = $modelName;
                    }

                    break;
            }
        }
    }


    /* InettvBrowser model information */

    private function detectGenericInettvBrowser($ua)
    {
        if (preg_match('/(?:DTVNetBrowser|InettvBrowser|Hybridcast)\/[0-9\.]+[A-Z]? ?\(/u', $ua, $match)) {
            $this->device->type = Constants\DeviceType::TELEVISION;

            $vendorName = null;
            $modelName = null;
            $found = false;

            if (preg_match('/(?:DTVNetBrowser|InettvBrowser)\/[0-9\.]+[A-Z]? ?\(([^;]*)\s*;\s*([^;]*)\s*;/u', $ua, $match)) {
                $vendorName = trim($match[1]);
                $modelName = trim($match[2]);
                $found = true;
            }

            if (preg_match('/Hybridcast\/[0-9\.]+ ?\([^;]*;([^;]*)\s*;\s*([^;]*)\s*;/u', $ua, $match)) {
                $vendorName = trim($match[1]);
                $modelName = trim($match[2]);
                $found = true;
            }

            if ($found) {
                $this->device->identified |= Constants\Id::PATTERN;

                $data = [
                    '0003D5' => 'Advanced Communications',
                    '000024' => 'Connect AS',
                    '000087' => 'Hitachi',
                    '00A0B0' => 'I-O Data Device',
                    '00E091' => 'LG',
                    '0050C9' => 'Maspro Denkoh',
                    '002692' => 'Mitsubishi',
                    '38E08E' => 'Mitsubishi',
                    '008045' => 'Panasonic',
                    '00E036' => 'Pioneer',
                    '00E064' => 'Samsung',
                    '08001F' => 'Sharp',
                    '00014A' => 'Sony',
                    '000039' => 'Toshiba'
                ];

                if (isset($data[$vendorName])) {
                    $this->device->manufacturer = $data[$vendorName];

                    if ($this->device->manufacturer == 'LG') {
                        switch ($modelName) {
                            case 'LGE2D2012M':
                                $this->device->series = 'NetCast TV 2012';
                                break;
                            case 'LGE3D2012M':
                                $this->device->series = 'NetCast TV 2012';
                                break;
                            case 'LGwebOSTV':
                            case 'webOSTV3_0':
                                $this->device->series = 'webOS TV';
                                break;
                        }
                    }

                    if ($this->device->manufacturer == 'Panasonic') {
                        if (!str_starts_with($modelName, 'PANATV')) {
                            $this->device->model = $modelName;
                        }
                    }
                }

                if (!isset($this->device->series)) {
                    $this->device->series = 'Smart TV';
                }
            }
        }
    }


    /* HbbTV model information */

    private function detectGenericHbbTV($ua)
    {
        if (preg_match('/((HbbTV|OHTV|SmartTV)\/[0-9\.]+|CE-HTML)/iu', $ua)) {
            $this->device->type = Constants\DeviceType::TELEVISION;

            $vendorName = null;
            $modelName = null;
            $found = false;

            if (preg_match('/HbbTV\/[0-9\.]+;CE-HTML\/[0-9\.]+;([A-Z]+)\s([^;]+);/iu', $ua, $match)) {
                $vendorName = Data\Manufacturers::identify(Constants\DeviceType::TELEVISION, $match[1]);
                $modelName = trim($match[2]);
                $found = true;
            }

            if (preg_match('/UID\([a-f0-9:]+\/([^\/]+)\/([^\/]+)\/[0-9a-z\.]+\)\+CE-HTML/iu', $ua, $match)) {
                $vendorName = Data\Manufacturers::identify(Constants\DeviceType::TELEVISION, $match[2]);
                $modelName = trim($match[1]);
                $found = true;
            }

            if (preg_match('/(?:HbbTV|OHTV)\/[0-9\.]+ \(([^;]*);\s*([^;]*)\s*;\s*([^;]*)\s*;/u', $ua, $match)) {
                if (trim($match[1]) == "" || in_array(strtok($match[1], ' '), ['PVR', 'DL']) || str_contains($match[1], '+')) {
                    $vendorName = Data\Manufacturers::identify(Constants\DeviceType::TELEVISION, $match[2]);
                    $modelName = trim($match[3]);
                } else {
                    $vendorName = Data\Manufacturers::identify(Constants\DeviceType::TELEVISION, $match[1]);
                    $modelName = trim($match[2]);
                }

                $found = true;
            }

            if (preg_match('/(?:^|\s)SmartTV\/[0-9\.]+ \(([^;]*)\s*;\s*([^;]*)\s*;/u', $ua, $match)) {
                $vendorName = Data\Manufacturers::identify(Constants\DeviceType::TELEVISION, $match[1]);
                $modelName = trim($match[2]);
                $found = true;
            }

            if (in_array($vendorName, ['Access', 'ANT', 'EMSYS', 'Em-Sys', 'Ocean Blue Software', 'Opera', 'Opera Software', 'Seraphic', 'ST', 'Vendor'])) {
                $found = false;
            }

            if ($found) {
                $this->device->identified |= Constants\Id::PATTERN;

                switch ($vendorName) {
                    case 'LG':
                        $this->device->manufacturer = 'LG';

                        switch ($modelName) {
                            case 'NetCast 3.0':
                            case 'GLOBAL_PLAT3':
                                $this->device->series = 'NetCast TV 2012';
                                break;
                            case 'NetCast 4.0':
                            case 'GLOBAL-PLAT4':
                                $this->device->series = 'NetCast TV 2013';
                                break;
                            case 'WEBOS1':
                            case 'WEBOS2.0':
                            case 'WEBOS3':
                                $this->device->series = 'webOS TV';
                                break;
                        }

                        break;

                    case 'Samsung':
                        $this->device->manufacturer = 'Samsung';

                        switch ($modelName) {
                            case 'SmartTV2012':
                                $this->device->series = 'Smart TV 2012';
                                break;
                            case 'SmartTV2013':
                                $this->device->series = 'Smart TV 2013';
                                break;
                            case 'SmartTV2014':
                                $this->device->series = 'Smart TV 2014';
                                break;
                            case 'SmartTV2015':
                                $this->device->series = 'Smart TV 2015';
                                break;
                            case 'SmartTV2016':
                                $this->device->series = 'Smart TV 2016';
                                break;
                            case 'SmartTV2017':
                                $this->device->series = 'Smart TV 2017';
                                break;
                            case 'OTV-SMT-E5015':
                                $this->device->model = 'Olleh SkyLife Smart Settopbox';
                                unset($this->device->series);
                                break;
                        }

                        break;

                    case 'Panasonic':
                        $this->device->manufacturer = 'Panasonic';

                        switch ($modelName) {
                            case 'VIERA 2011':
                                $this->device->series = 'Viera 2011';
                                break;
                            case 'VIERA 2012':
                                $this->device->series = 'Viera 2012';
                                break;
                            case 'VIERA 2013':
                                $this->device->series = 'Viera 2013';
                                break;
                            case 'VIERA 2014':
                                $this->device->series = 'Viera 2014';
                                break;
                            case 'VIERA 2015':
                            case 'Viera2015.mid':
                                $this->device->series = 'Viera 2015';
                                break;
                            case 'VIERA 2016':
                                $this->device->series = 'Viera 2016';
                                break;
                            case 'VIERA 2017':
                                $this->device->series = 'Viera 2017';
                                break;
                            case 'SmartTV2018mid':
                                $this->device->series = 'Viera 2018';
                                break;
                            default:
                                $this->device->model = $modelName;

                                if (str_starts_with($modelName, 'DIGA')) {
                                    $this->device->series = 'Diga';
                                    $this->device->model = null;
                                }

                                break;
                        }

                        break;

                    case 'TV2N':
                        $this->device->manufacturer = 'TV2N';

                        if ($modelName == 'videoweb') {
                            $this->device->model = 'Videoweb';
                        }

                        break;

                    default:
                        if ($vendorName != '' && !in_array($vendorName, ['OEM', 'vendorName'])) {
                            $this->device->manufacturer = $vendorName;
                        }

                        if ($modelName != '' && !in_array($modelName, ['dvb', 'modelName', 'undefined-model-name', 'N/A'])) {
                            $this->device->model = $modelName;
                        }

                        break;
                }

                switch ($modelName) {
                    case 'hdr1000s':
                        $this->device->manufacturer = 'Humax';
                        $this->device->model = 'HDR-1000S';
                        $this->device->identified |= Constants\Id::MATCH_UA;
                        $this->device->generic = false;
                        break;

                    case 'hdr4000t':
                        $this->device->manufacturer = 'Humax';
                        $this->device->model = 'HDR-4000T';
                        $this->device->identified |= Constants\Id::MATCH_UA;
                        $this->device->generic = false;
                        break;

                    case 'hgs1000s':
                        $this->device->manufacturer = 'Humax';
                        $this->device->model = 'HGS-1000S';
                        $this->device->identified |= Constants\Id::MATCH_UA;
                        $this->device->generic = false;
                        break;

                    case 'hms1000s':
                    case 'hms1000sph2':
                        $this->device->manufacturer = 'Humax';
                        $this->device->model = 'HMS-1000S';
                        $this->device->identified |= Constants\Id::MATCH_UA;
                        $this->device->generic = false;
                        break;
                }
            }
        }

        if (preg_match('/HbbTV\/[0-9.]+;CE-HTML\/[0-9.]+;([^\s;]+)\s[^\s;]+;/u', $ua, $match)) {
            $this->device->manufacturer = Data\Manufacturers::identify(Constants\DeviceType::TELEVISION, $match[1]);
            if (!isset($this->device->series)) {
                $this->device->series = 'Smart TV';
            }
        }

        if (preg_match('/HbbTV\/[0-9.]+;CE-HTML\/[0-9.]+;Vendor\/([^\s;]+);/u', $ua, $match)) {
            $this->device->manufacturer = Data\Manufacturers::identify(Constants\DeviceType::TELEVISION, $match[1]);
            if (!isset($this->device->series)) {
                $this->device->series = 'Smart TV';
            }
        }
    }


    /* Try to reformat some of the detected generic models */

    private function improveModelsOnDeviceTypeTelevision()
    {
        if ($this->device->type != Constants\DeviceType::TELEVISION) {
            return;
        }


        if (isset($this->device->model) && isset($this->device->manufacturer)) {
            if ($this->device->manufacturer == 'Dune HD') {
                if (preg_match('/tv([0-9]+[a-z]?)/u', $this->device->model, $match)) {
                    $this->device->model = 'TV-' . strtoupper($match[1]);
                }

                if ($this->device->model == 'connect') {
                    $this->device->model = 'Connect';
                }
            }

            if ($this->device->manufacturer == 'Humax') {
                $this->device->series = "Digital Receiver";
            }

            if ($this->device->manufacturer == 'Inverto') {
                if (preg_match('/IDL[ -]?([0-9]+.*)/u', $this->device->model, $match)) {
                    $this->device->model = 'IDL ' . $match[1];
                }

                if (preg_match('/MBN([0-9]+)/u', $this->device->model, $match)) {
                    $this->device->model = 'MBN ' . $match[1];
                }
            }

            if ($this->device->manufacturer == 'HyperPanel') {
                $this->device->model = strtok(strtoupper($this->device->model), ' ');
            }

            if ($this->device->manufacturer == 'LG') {
                if (preg_match('/(?:ATSC|DVB)-(.*)/u', $this->device->model, $match)) {
                    $this->device->model = $match[1];
                    $this->device->generic = false;
                }

                if (preg_match('/[0-9][0-9]([A-Z][A-Z][0-9][0-9][0-9][0-9A-Z])/u', $this->device->model, $match)) {
                    $this->device->model = $match[1];
                    $this->device->generic = false;
                }

                if (preg_match('/Media\/(.*)/u', $this->device->model, $match)) {
                    $this->device->model = $match[1];
                    $this->device->generic = false;
                }
            }

            if ($this->device->manufacturer == 'Loewe') {
                $this->device->series = 'Smart TV';

                if (preg_match('/((?:ID|SL)[0-9]+)/u', $this->device->model, $match)) {
                    $this->device->model = 'Connect ' . $match[1];
                    $this->device->generic = false;
                }
            }

            if ($this->device->manufacturer == 'Philips') {
                if (preg_match('/[0-9][0-9]([A-Z][A-Z][A-Z][0-9][0-9][0-9][0-9])/u', $this->device->model, $match)) {
                    $this->device->model = $match[1];
                    $this->device->generic = false;
                }

                if (preg_match('/(MT[0-9]+)/u', $this->device->model, $match)) {
                    $this->device->model = $match[1];
                    $this->device->series = "Digital Receiver";
                    $this->device->generic = false;
                }

                if (preg_match('/(BDP[0-9]+)/u', $this->device->model, $match)) {
                    $this->device->model = $match[1];
                    $this->device->series = "Blu-ray Player";
                    $this->device->generic = false;
                }
            }

            if ($this->device->manufacturer == 'Toshiba') {
                if (preg_match('/DTV_(.*)/u', $this->device->model, $match)) {
                    $this->device->model = 'Regza ' . $match[1];
                    $this->device->generic = false;
                }

                if (preg_match('/[0-9][0-9]([A-Z][A-Z][0-9][0-9][0-9])/u', $this->device->model, $match)) {
                    $this->device->model = 'Regza ' . $match[1];
                    $this->device->generic = false;
                }

                if (preg_match('/[0-9][0-9](ZL[0-9])/u', $this->device->model, $match)) {
                    $this->device->model = $match[1] . ' Cevo';
                    $this->device->generic = false;
                }

                if (preg_match('/(BDX[0-9]+)/u', $this->device->model, $match)) {
                    $this->device->model = $match[1];
                    $this->device->series = "Blu-ray Player";
                    $this->device->generic = false;
                }
            }

            if ($this->device->manufacturer == 'Selevision') {
                $this->device->model = str_replace('Selevision ', '', $this->device->model);
            }

            if ($this->device->manufacturer == 'Sharp') {
                if (preg_match('/[0-9][0-9]([A-Z]+[0-9]+[A-Z]+)/u', $this->device->model, $match)) {
                    $this->device->model = $match[1];
                    $this->device->generic = false;
                }
            }

            if ($this->device->manufacturer == 'Sony') {
                if (preg_match('/(BDP[0-9]+G)/u', $this->device->model, $match)) {
                    $this->device->model = $match[1];
                    $this->device->series = "Blu-ray Player";
                    $this->device->generic = false;
                }

                if (preg_match('/KDL?-?[0-9]*([A-Z]+[0-9]+)[A-Z]*/u', $this->device->model, $match)) {
                    $this->device->model = 'Bravia ' . $match[1];
                    $this->device->series = 'Smart TV';
                    $this->device->generic = false;
                }
            }

            if ($this->device->manufacturer == 'Pioneer') {
                if (preg_match('/(BDP-[0-9]+)/u', $this->device->model, $match)) {
                    $this->device->model = $match[1];
                    $this->device->series = "Blu-ray Player";
                    $this->device->generic = false;
                }
            }
        }
    }
}
