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

trait Os
{
    private function &detectOperatingSystem($ua)
    {
        $this->detectUnix($ua);
        $this->detectLinux($ua);
        $this->detectBsd($ua);
        $this->detectDarwin($ua);
        $this->detectWindows($ua);
        $this->detectAndroid($ua);
        $this->detectChromeos($ua);
        $this->detectBlackberry($ua);
        $this->detectWebos($ua);
        $this->detectKaiOS($ua);
        $this->detectSymbian($ua);
        $this->detectNokiaOs($ua);
        $this->detectTizen($ua);
        $this->detectSailfish($ua);
        $this->detectBada($ua);
        $this->detectBrew($ua);
        $this->detectQtopia($ua);
        $this->detectOpenTV($ua);
        $this->detectRemainingOperatingSystems($ua);

        return $this;
    }


    private function &refineOperatingSystem($ua)
    {
        $this->determineAndroidVersionBasedOnBuild($ua);

        return $this;
    }


    /* Darwin */

    private function detectDarwin($ua)
    {
        /* iOS */

        if (preg_match('/\(iOS;/u', $ua)) {
            $this->os->name = 'iOS';
            $this->device->type = Constants\DeviceType::MOBILE;
        }

        if (preg_match('/(iPhone|iPad|iPod)/u', $ua) && !preg_match('/like iPhone/u', $ua)) {
            $this->os->name = 'iOS';

            if (preg_match('/CPU like Mac OS X/u', $ua, $match)) {
                $this->os->version = new Version(['value' => '1.0']);
            }

            if (preg_match('/OS (.*) like Mac OS X/u', $ua, $match)) {
                $this->os->version = new Version(['value' => str_replace('_', '.', $match[1])]);
            }

            if (preg_match('/iPhone OS ([0-9._]*);/u', $ua, $match)) {
                $this->os->version = new Version(['value' => str_replace('_', '.', $match[1])]);
            }

            if (preg_match('/iOS ([0-9.]*);/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }

            if (preg_match('/iPhone Simulator;/u', $ua)) {
                $this->device->type = Constants\DeviceType::EMULATOR;
            } else {
                if (preg_match('/(iPad|iPhone( 3GS| 3G| 4S| 4| 5)?|iPod( touch)?)/u', $ua, $match)) {
                    $device = Data\DeviceModels::identify('ios', $match[0]);

                    if ($device) {
                        $this->device = $device;
                    }
                }

                if (preg_match('/(iPad|iPhone|iPod)1?[0-9],[0-9][0-9]?/u', $ua, $match)) {
                    $device = Data\DeviceModels::identify('ios', $match[0]);

                    if ($device) {
                        $this->device = $device;
                    }
                }
            }
        } elseif (preg_match('/Mac OS X/u', $ua) || preg_match('/;os=Mac/u', $ua)) {
            /* OS X */

            $this->os->name = 'OS X';

            if (preg_match('/Mac OS X (1[0-9][0-9\._]*)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => str_replace('_', '.', $match[1]), 'details' => 2]);
            }

            if (preg_match('/;os=Mac (1[0-9][0-9[\.,]*)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => str_replace(',', '.', $match[1]), 'details' => 2]);
            }

            if ($this->os->version && $this->os->version->is('10.16')) {
                $this->os->version = new Version(['value' => '11.0', 'details' => 2]);
            }

            $this->device->type = Constants\DeviceType::DESKTOP;
        }

        /* Darwin */

        if (preg_match('/Darwin(?:\/([0-9]+).[0-9]+)?/u', $ua, $match)) {
            if (preg_match('/\(X11;/u', $ua)) {
                /* Darwin */
                $this->os->name = 'Darwin';
                $this->device->type = Constants\DeviceType::DESKTOP;
            } elseif (preg_match('/\((?:x86_64|i386|Power%20Macintosh)\)/u', $ua)) {
                /* OS X */
                $this->os->name = 'OS X';
                $this->device->type = Constants\DeviceType::DESKTOP;

                if (isset($match[1])) {
                    $version = Data\Darwin::getVersion('osx', $match[1]);
                    if ($version) {
                        $this->os->version = new Version($version);
                    }

                    if (preg_match('/CFNetwork\/([0-9\.]+)/u', $ua, $match)) {
                        $version = Data\CFNetwork::getVersion('osx', $match[1]);
                        if ($version) {
                            $this->os->version = new Version($version);
                        }
                    }
                }
            } else {
                /* iOS */
                $this->os->name = 'iOS';
                $this->device->type = Constants\DeviceType::MOBILE;

                if (isset($match[1])) {
                    $version = Data\Darwin::getVersion('ios', $match[1]);
                    if ($version) {
                        $this->os->version = new Version($version);
                    }

                    if (preg_match('/CFNetwork\/([0-9\.]+)/u', $ua, $match)) {
                        $version = Data\CFNetwork::getVersion('ios', $match[1]);
                        if ($version) {
                            $this->os->version = new Version($version);
                        }
                    }
                }
            }
        }

        /* Mac OS */

        if (preg_match('/(; |\()Macintosh;/u', $ua) && !preg_match('/OS X/u', $ua)) {
            $this->os->name = 'Mac OS';
            $this->device->type = Constants\DeviceType::DESKTOP;
        }
    }


    /* Android */

    private function detectAndroid($ua)
    {
        /* Android */

        if (preg_match('/Andr[0o]id/ui', $ua)) {
            $falsepositive = false;

            /* Prevent the Mobile IE 11 Franken-UA from matching Android */
            if (preg_match('/IEMobile\/1/u', $ua)) {
                $falsepositive = true;
            }
            if (preg_match('/Windows Phone 10/u', $ua)) {
                $falsepositive = true;
            }

            /* Prevent Windows 10 IoT Core from matching Android */
            if (preg_match('/Windows IoT/u', $ua)) {
                $falsepositive = true;
            }

            /* Prevent from OSes that claim to be 'like' Android from matching */
            if (preg_match('/like Android/u', $ua)) {
                $falsepositive = true;
            }
            if (preg_match('/COS like Android/u', $ua)) {
                $falsepositive = false;
            }

            if (!$falsepositive) {
                $this->os->name = 'Android';
                $this->os->version = new Version();

                if (preg_match('/Andr[0o]id(?: )?(?:AllPhone_|CyanogenMod_|OUYA )?(?:\/)?v?([0-9.]+)/ui', str_replace('-update', ',', $ua), $match)) {
                    $this->os->version = new Version(['value' => $match[1], 'details' => 3]);
                }

                if (preg_match('/Android [0-9][0-9].[0-9][0-9].[0-9][0-9]\(([^)]+)\);/u', str_replace('-update', ',', $ua), $match)) {
                    $this->os->version = new Version(['value' => $match[1], 'details' => 3]);
                }

                if (preg_match('/Android Eclair/u', $ua)) {
                    $this->os->version = new Version(['value' => '2.0', 'details' => 3]);
                }

                if (preg_match('/Android KeyLimePie/u', $ua)) {
                    $this->os->version = new Version(['value' => '4.4', 'details' => 3]);
                }

                if (preg_match('/Android (?:L|4.4.99);/u', $ua)) {
                    $this->os->version = new Version(['value' => '5', 'details' => 3, 'alias' => 'L']);
                }

                if (preg_match('/Android (?:M|5.[01].99);/u', $ua)) {
                    $this->os->version = new Version(['value' => '6', 'details' => 3, 'alias' => 'M']);
                }

                if (preg_match('/Android (?:N|6.0.99);/u', $ua)) {
                    $this->os->version = new Version(['value' => '7', 'details' => 3, 'alias' => 'N']);
                }

                $this->device->type = Constants\DeviceType::MOBILE;

                if ($this->os->version->toFloat() >= 3) {
                    $this->device->type = Constants\DeviceType::TABLET;
                }

                if ($this->os->version->toFloat() >= 4 && preg_match('/Mobile/u', $ua)) {
                    $this->device->type = Constants\DeviceType::MOBILE;
                }

                $candidates = [];

                if (preg_match('/Build/ui', $ua) && (!preg_match('/AppleWebKit.*Build/ui', $ua) || preg_match('/Build.*AppleWebKit/ui', $ua))) {
                    /* Normal Android useragent strings */

                    if (preg_match('/; [a-z][a-zA-Z][-_][a-zA-Z][a-zA-Z] ([^;]*[^;\s])\s+(?:BUILD|Build|build)/u', $ua, $match)) {
                        $candidates[] = $match[1];
                    }

                    if (preg_match('/Android [A-Za-z]+; (?:[a-zA-Z][a-zA-Z](?:[-_][a-zA-Z][a-zA-Z])?) Build\/([^\/]*)\//u', $ua, $match)) {
                        $candidates[] = $match[1];
                    }

                    if (preg_match('/;\+? ?(?:\*\*)?([^;]*[^;\s]);?\s+(?:BUILD|Build|build)/u', $ua, $match)) {
                        $candidates[] = $match[1];
                    }
                } elseif (preg_match('/\(Linux; Android [0-9\.]+; ([^\/]+)(; wv)?\) AppleWebKit/u', $ua, $match)) {
                    /* New style minimal Android useragent string */

                    $candidates[] = $match[1];
                } elseif (preg_match('/Release\//ui', $ua)) {
                    /* WAP style useragent strings */

                    if (preg_match('/^(?U)([^\/]+)(?U)(?:(?:_CMCC_TD|_CMCC|_TD|_TDLTE|_LTE)?\/[^\/]*)? Linux\/[0-9.+]+ Android\/[0-9.]+/u', $this->removeKnownPrefixes($ua), $match)) {
                        $candidates[] = $match[1];
                    } elseif (preg_match('/^(?U)([^\/]+)(?U)(?:(?:_CMCC_TD|_CMCC|_TD|_TDLTE|_LTE)?\/[^\/]*)? Android(_OS)?\/[0-9.]+/u', $this->removeKnownPrefixes($ua), $match)) {
                        $candidates[] = $match[1];
                    } elseif (preg_match('/^(?U)([^\/]+)(?U)(?:(?:_CMCC_TD|_CMCC|_TD|_TDLTE|_LTE)?\/[^\/]*)? Release\/[0-9.]+/u', $this->removeKnownPrefixes($ua), $match)) {
                        $candidates[] = $match[1];
                    }
                } elseif (preg_match('/Mozilla\//ui', $ua)) {
                    /* Old Android useragent strings */

                    if (preg_match('/Linux; (?:arm; |arm_64; )?(?:U; )?Android [^;]+; (?:[a-zA-Z][a-zA-Z](?:[-_][a-zA-Z][a-zA-Z])?; )?(?:[^;]+; ?)?([^\/;]+)\) /u', $ua, $match)) {
                        $candidates[] = $match[1];
                    } elseif (preg_match('/\(([^;]+);U;Android\/[^;]+;[0-9]+\*[0-9]+;CTC\/2.0\)/u', $ua, $match)) {
                        $candidates[] = $match[1];
                    }
                } else {
                    /* Other applications */

                    if (preg_match('/[34]G Explorer\/[0-9.]+ \(Linux;Android [0-9.]+,([^\)]+)\)/u', $ua, $match)) {
                        $candidates[] = $match[1];
                    }

                    if (preg_match('/GetJarSDK\/.*android\/[0-9.]+ \([^;]+; [^;]+; ([^\)]+)\)$/u', $ua, $match)) {
                        $candidates[] = $match[1];
                    }
                }

                $candidates = array_unique($candidates);

                for ($c = 0; $c < count($candidates); $c++) {
                    if (preg_match('/^[a-zA-Z][a-zA-Z](?:[-_][a-zA-Z][a-zA-Z])?$/u', $candidates[$c])) {
                        unset($candidates[$c]);
                        continue;
                    }

                    if (preg_match('/^Android [0-9\.]+$/u', $candidates[$c])) {
                        unset($candidates[$c]);
                        continue;
                    }

                    /* Ignore "K" or "Unspecified Device" as a device, as it is a dummy value used by Chrome UA reduction */

                    if ($candidates[$c] == 'K' || $candidates[$c] == 'Unspecified Device') {
                        unset($candidates[$c]);
                        continue;
                    }

                    $candidates[$c] = preg_replace('/^[a-zA-Z][a-zA-Z][-_][a-zA-Z][a-zA-Z]\s+/u', '', $candidates[$c]);
                    $candidates[$c] = preg_replace('/(.*) - [0-9\.]+ - (?:with Google Apps - )?API [0-9]+ - [0-9]+x[0-9]+/', '\\1', $candidates[$c]);
                    $candidates[$c] = preg_replace('/^sprd-/u', '', $candidates[$c]);
                    $candidates[$c] = preg_replace('/^HarmonyOS; /u', '', $candidates[$c]);
                    $candidates[$c] = preg_replace('/; GMSCore.*/u', '', $candidates[$c]);
                    $candidates[$c] = preg_replace('/; HMSCore.*/u', '', $candidates[$c]);
                }

                $candidates = array_unique($candidates);

                if (count($candidates)) {
                    $this->device->model = $candidates[0];
                    $this->device->identified |= Constants\Id::PATTERN;

                    for ($c = 0; $c < count($candidates); $c++) {
                        $device = Data\DeviceModels::identify('android', $candidates[$c]);
                        if ($device->identified) {
                            $device->identified |= $this->device->identified;
                            $this->device = $device;
                            break;
                        }
                    }
                }

                if (preg_match('/HP eStation/u', $ua)) {
                    $this->device->manufacturer = 'HP';
                    $this->device->model = 'eStation';
                    $this->device->type = Constants\DeviceType::PRINTER;
                    $this->device->identified |= Constants\Id::MATCH_UA;
                    $this->device->generic = false;
                }
            }
        }

        if (preg_match('/\(Linux; (?:U; )?(?:([0-9.]+); )?(?:[a-zA-Z][a-zA-Z](?:[-_][a-zA-Z][a-zA-Z])?; )?([^;]+) Build/u', $ua, $match)) {
            $falsepositive = false;

            if ($match[2] == 'OpenTV') {
                $falsepositive = true;
            }

            if (!$falsepositive) {
                $this->device->type = Constants\DeviceType::MOBILE;
                $this->device->model = $match[2];

                $this->os->name = 'Android';

                if (!empty($match[1])) {
                    $this->os->version = new Version(['value' => $match[1], 'details' => 3]);
                }

                $device = Data\DeviceModels::identify('android', $match[2]);
                if ($device->identified) {
                    $device->identified |= Constants\Id::PATTERN;
                    $device->identified |= $this->device->identified;

                    $this->device = $device;
                }
            }
        }

        if (preg_match('/Linux x86_64; ([^;\)]+)(?:; [a-zA-Z][a-zA-Z](?:[-_][a-zA-Z][a-zA-Z])?)?\) AppleWebKit\/534.24 \(KHTML, like Gecko\) Chrome\/11.0.696.34 Safari\/534.24/u', $ua, $match)) {
            $device = Data\DeviceModels::identify('android', $match[1]);
            if ($device->identified) {
                $device->identified |= Constants\Id::PATTERN;
                $device->identified |= $this->device->identified;

                $this->os->name = 'Android';
                $this->device = $device;
            }
        }

        if (preg_match('/\(Linux; U; Linux Ventana; [^;]+; ([^;]+) Build/u', $ua, $match)) {
            $this->device->type = Constants\DeviceType::MOBILE;
            $this->device->model = $match[1];

            $device = Data\DeviceModels::identify('android', $match[1]);
            if ($device->identified) {
                $device->identified |= Constants\Id::PATTERN;
                $device->identified |= $this->device->identified;

                $this->os->name = 'Android';
                $this->device = $device;
            }
        }


        /* Harmony OS */

        if (preg_match('/HarmonyOS/u', $ua)) {
            $this->os->name = 'Harmony OS';
            $this->os->version = new Version();


            if (preg_match('/; Android ([0-9\.]+);/u', $ua, $match)) {
                $this->os->family = new Family([
                    'name' => 'Android',
                    'version' => new Version(['value' => $match[1], 'details' => 3])
                ]);
            }
        }


        /* Aliyun OS */

        if (preg_match('/Aliyun/u', $ua) || preg_match('/YunOs/ui', $ua)) {
            $this->os->name = 'Aliyun OS';
            $this->os->family = new Family(['name' => 'Android']);
            $this->os->version = new Version();

            if (preg_match('/YunOs[ \/]([0-9.]+)/iu', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1], 'details' => 3]);
            }

            if (preg_match('/AliyunOS ([0-9.]+)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1], 'details' => 3]);
            }

            $this->device->type = Constants\DeviceType::MOBILE;

            if (preg_match('/; ([^;]*[^;\s])\s+Build/u', $ua, $match)) {
                $this->device->model = $match[1];
            }

            if (isset($this->device->model)) {
                $this->device->identified |= Constants\Id::PATTERN;

                $device = Data\DeviceModels::identify('android', $this->device->model);
                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }
        }

        if (preg_match('/Android/u', $ua)) {
            if (preg_match('/Android v(1.[0-9][0-9])_[0-9][0-9].[0-9][0-9]-/u', $ua, $match)) {
                $this->os->name = 'Aliyun OS';
                $this->os->family = new Family(['name' => 'Android']);
                $this->os->version = new Version(['value' => $match[1], 'details' => 3]);
            }

            if (preg_match('/Android[ \/](1.[0-9].[0-9].[0-9]+)-R?T/u', $ua, $match)) {
                $this->os->name = 'Aliyun OS';
                $this->os->family = new Family(['name' => 'Android']);
                $this->os->version = new Version(['value' => $match[1], 'details' => 3]);
            }

            if (preg_match('/Android ([12].[0-9].[0-9]+)-R-20[0-9]+/u', $ua, $match)) {
                $this->os->name = 'Aliyun OS';
                $this->os->family = new Family(['name' => 'Android']);
                $this->os->version = new Version(['value' => $match[1], 'details' => 3]);
            }

            if (preg_match('/Android 20[0-9]+\./u', $ua, $match)) {
                $this->os->name = 'Aliyun OS';
                $this->os->family = new Family(['name' => 'Android']);
                $this->os->version = null;
            }
        }

        /* Baidu Yi */

        if (preg_match('/Baidu Yi/u', $ua)) {
            $this->os->name = 'Baidu Yi';
            $this->os->family = new Family(['name' => 'Android']);
            $this->os->version = null;
        }

        /* Google TV */

        if (preg_match('/GoogleTV/u', $ua)) {
            $this->os->name = 'Google TV';
            $this->os->family = new Family(['name' => 'Android']);

            $this->device->type = Constants\DeviceType::TELEVISION;

            if (preg_match('/GoogleTV [0-9\.]+; ?([^;]*[^;\s])\s+Build/u', $ua, $match)) {
                $this->device->model = $match[1];
            }

            if (isset($this->device->model) && $this->device->model) {
                $this->device->identified |= Constants\Id::PATTERN;

                $device = Data\DeviceModels::identify('android', $this->device->model);
                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }
        }

        /* LeOS */

        if (preg_match('/LeOS/u', $ua)) {
            $this->os->name = 'LeOS';
            $this->os->family = new Family(['name' => 'Android']);

            if (preg_match('/LeOS([0-9\.]*)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }

            $this->device->type = Constants\DeviceType::TABLET;

            if (preg_match('/LeOS[0-9\.]+; [^;]+; (.*) Build/u', $ua, $match)) {
                $this->device->model = $match[1];
            }

            if (isset($this->device->model) && $this->device->model) {
                $this->device->identified |= Constants\Id::PATTERN;

                $device = Data\DeviceModels::identify('android', $this->device->model);
                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }
        }

        /* WoPhone */

        if (preg_match('/WoPhone/u', $ua)) {
            $this->os->name = 'WoPhone';
            $this->os->family = new Family(['name' => 'Android']);

            if (preg_match('/WoPhone\/([0-9\.]*)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }

            $this->device->type = Constants\DeviceType::MOBILE;
        }

        /* COS */

        if (preg_match('/(COS|(China|Chinese) Operating System)/ui', $ua)) {
            if (preg_match('/COS[\/ ]?([0-9]\.[0-9.]+)/ui', $ua, $match)) {
                $this->os->name = 'COS';
                $this->os->family = new Family(['name' => 'Android']);
                $this->os->version = new Version(['value' => $match[1], 'details' => 2]);
            } elseif (preg_match('/(?:\(|; )(?:China|Chinese) Operating System ([0-9]\.[0-9.]*);/ui', $ua, $match)) {
                $this->os->name = 'COS';
                $this->os->family = new Family(['name' => 'Android']);
                $this->os->version = new Version(['value' => $match[1], 'details' => 2]);
            } elseif (preg_match('/COS like Android/ui', $ua, $match)) {
                $this->os->name = 'COS';
                $this->os->family = new Family(['name' => 'Android']);
                $this->os->version = null;
                $this->device->type = Constants\DeviceType::MOBILE;
            } elseif (preg_match('/(COS like Android|COSBrowser\/|\(COS;|\(COS 998;)/ui', $ua, $match)) {
                $this->os->name = 'COS';
                $this->os->family = new Family(['name' => 'Android']);
            }
        }

        /* RemixOS */

        if (preg_match('/RemixOS/u', $ua)) {
            $this->os->name = 'Remix OS';
            $this->os->version = null;
            $this->os->family = new Family(['name' => 'Android']);

            if (preg_match('/RemixOS ([0-9]\.[0-9])/u', $ua, $match)) {
                switch ($match[1]) {
                    case '5.1':
                        $this->os->version = new Version(['value' => '1.0']);
                        break;
                    case '6.0':
                        $this->os->version = new Version(['value' => '2.0']);
                        break;
                }
            }

            $this->device->type = Constants\DeviceType::DESKTOP;
        }
    }

    private function determineAndroidVersionBasedOnBuild($ua)
    {
        if ($this->isOs('Android')) {
            if (preg_match('/Build\/([^\);]+)/u', $ua, $match)) {
                $version = Data\BuildIds::identify($match[1]);
                if ($version) {
                    if (!isset($this->os->version) || $this->os->version == null || $this->os->version->value == null || $version->toFloat() < $this->os->version->toFloat()) {
                        $this->os->version = $version;
                    }

                    /* Special case for Android L */
                    if ($version->toFloat() == 5) {
                        $this->os->version = $version;
                    }
                }

                $this->os->build = $match[1];
            }
        }
    }


    /* Windows */

    private function detectWindows($ua)
    {
        if (preg_match('/(Windows|WinNT|WinCE|WinMobile|Win ?[9MX]|Win(16|32))/u', $ua)) {
            $this->os->name = 'Windows';
            $this->device->type = Constants\DeviceType::DESKTOP;


            /* Windows NT */

            if (preg_match('/Windows 2000/u', $ua)) {
                $this->os->version = new Version(['value' => '5.0', 'alias' => '2000']);
            }

            if (preg_match('/(Windows XP|WinXP)/u', $ua)) {
                $this->os->version = new Version(['value' => '5.1', 'alias' => 'XP']);
            }

            if (preg_match('/Windows Vista/u', $ua)) {
                $this->os->version = new Version(['value' => '6.0', 'alias' => 'Vista']);
            }

            if (preg_match('/(?:Windows NT |WinNT)([0-9][0-9]?\.[0-9])/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);

                switch ($match[1]) {
                    case '10.1':
                    case '10.0':
                    case '6.4':
                        $this->os->version = new Version(['value' => $match[1], 'alias' => '10']);
                        break;

                    case '6.3':
                        if (preg_match('/; ARM;/u', $ua)) {
                            $this->os->version = new Version(['value' => $match[1], 'alias' => 'RT 8.1']);
                        } else {
                            $this->os->version = new Version(['value' => $match[1], 'alias' => '8.1']);
                        }
                        break;

                    case '6.2':
                        if (preg_match('/; ARM;/u', $ua)) {
                            $this->os->version = new Version(['value' => $match[1], 'alias' => 'RT']);
                        } else {
                            $this->os->version = new Version(['value' => $match[1], 'alias' => '8']);
                        }
                        break;

                    case '6.1':
                        $this->os->version = new Version(['value' => $match[1], 'alias' => '7']);
                        break;
                    case '6.0':
                        $this->os->version = new Version(['value' => $match[1], 'alias' => 'Vista']);
                        break;
                    case '5.2':
                        $this->os->version = new Version(['value' => $match[1], 'alias' => 'Server 2003']);
                        break;
                    case '5.1':
                        $this->os->version = new Version(['value' => $match[1], 'alias' => 'XP']);
                        break;
                    case '5.0':
                        $this->os->version = new Version(['value' => $match[1], 'alias' => '2000']);
                        break;
                    default:
                        $this->os->version = new Version(['value' => $match[1], 'alias' => 'NT ' . $match[1]]);
                        break;
                }

                $this->detectWindowsOemManufacturer($ua);
            }


            /* Windows 10 IoT Core */

            if (preg_match('/Windows IoT (1[0-9]\.[0-9]);/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1], 'alias' => '10 IoT Core']);
            }


            /* Windows */

            if (preg_match('/(Windows 95|Win95)/u', $ua)) {
                $this->os->version = new Version(['value' => '4.0', 'alias' => '95']);
            }

            if (preg_match('/(Windows 98|Win98)/u', $ua)) {
                $this->os->version = new Version(['value' => '4.1', 'alias' => '98']);
            }

            if (preg_match('/(Windows M[eE]|WinME)/u', $ua)) {
                $this->os->version = new Version(['value' => '4.9', 'alias' => 'ME']);
            }

            if (preg_match('/(?:Windows|Win 9x) (([1234]\.[0-9])[0-9\.]*)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);

                switch ($match[2]) {
                    case '4.0':
                        $this->os->version = new Version(['value' => '4.0', 'alias' => '95']);
                        break;
                    case '4.1':
                        $this->os->version = new Version(['value' => '4.1', 'alias' => '98']);
                        break;
                    case '4.9':
                        $this->os->version = new Version(['value' => '4.9', 'alias' => 'ME']);
                        break;
                }
            }


            /* Windows Mobile and Windows Phone */

            if (preg_match('/WPDesktop/u', $ua)) {
                $this->os->name = 'Windows Phone';
                $this->os->version = new Version(['value' => '8.0', 'details' => 2]);
                $this->device->type = Constants\DeviceType::MOBILE;
                $this->browser->mode = 'desktop';
            }

            if (preg_match('/WP7/u', $ua)) {
                $this->os->name = 'Windows Phone';
                $this->os->version = new Version(['value' => '7', 'details' => 1]);
                $this->device->type = Constants\DeviceType::MOBILE;
                $this->browser->mode = 'desktop';
            }

            if (preg_match('/WinMobile/u', $ua)) {
                $this->os->name = 'Windows Mobile';
                $this->device->type = Constants\DeviceType::MOBILE;

                if (preg_match('/WinMobile\/([0-9.]*)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1], 'details' => 2]);
                }
            }

            if (preg_match('/(Windows CE|WindowsCE|WinCE)/u', $ua)) {
                $this->device->type = Constants\DeviceType::MOBILE;

                if (preg_match('/ IEMobile/u', $ua)) {
                    $this->os->name = 'Windows Mobile';

                    if (preg_match('/ IEMobile\/9/u', $ua)) {
                        $this->os->name = 'Windows Phone';
                        $this->os->version = new Version(['value' => '7.5', 'details' => 2]);
                    }

                    if (preg_match('/ IEMobile 8/u', $ua)) {
                        $this->os->version = new Version(['value' => '6.5', 'details' => 2]);
                    }

                    if (preg_match('/ IEMobile 7/u', $ua)) {
                        $this->os->version = new Version(['value' => '6.1', 'details' => 2]);
                    }

                    if (preg_match('/ IEMobile 6/u', $ua)) {
                        $this->os->version = new Version(['value' => '6.0', 'details' => 2]);
                    }
                } else {
                    $this->os->name = 'Windows CE';

                    if (preg_match('/WindowsCEOS\/([0-9.]*)/u', $ua, $match)) {
                        $this->os->version = new Version(['value' => $match[1], 'details' => 2]);
                    }

                    if (preg_match('/Windows CE ([0-9.]*)/u', $ua, $match)) {
                        $this->os->version = new Version(['value' => $match[1], 'details' => 2]);
                    }
                }


                $model = null;

                if (empty($model) && preg_match('/IEMobile [0-9.]+\)  ?(?:PPC; |SP; |Smartphone; )?(?:[0-9]+[Xx][0-9]+;? )?(?:VZW; )?([^;\(]+)/u', $ua, $match)) {
                    if (!preg_match('/(Profile\/MIDP|UNTRUSTED)/u', $match[1])) {
                        $model = $match[1];
                    }
                }

                if (empty($model) && preg_match('/IEMobile [0-9.]+\) (?:PPC|SP|Smartphone); (?:[0-9]+[Xx][0-9]+;? )([^;]+) Profile\/MIDP/u', $ua, $match)) {
                    $model = $match[1];
                }

                if (empty($model) && preg_match('/MSIE [0-9.]+; Windows CE; (?:PPC|SP|Smartphone); [0-9]+x[0-9]+; ([^;\)]+)\)$/u', $ua, $match)) {
                    $model = $match[1];
                }

                if (empty($model) && preg_match('/MSIE [0-9.]+; Windows CE; (?:PPC|SP|Smartphone); [0-9]+x[0-9]+; ([^;]+); (?:PPC|OpVer)/u', $ua, $match)) {
                    $model = $match[1];
                }

                if (empty($model) && preg_match('/MSIE [0-9.]+; Windows CE; (?:PPC|SP|Smartphone); ([^;]+) Profile\/MIDP/u', $ua, $match)) {
                    $model = $match[1];
                }

                if (empty($model) && preg_match('/MSIE [0-9.]+; Windows CE; (?:PPC|SP|Smartphone) ([^;\(]+)[;\/] [0-9]+x[0-9]+/u', $ua, $match)) {
                    $model = $match[1];
                }

                if (empty($model) && preg_match('/MSIE [0-9.]+; Windows CE; ([^;\(]+); [0-9]+x[0-9]+\)/u', $ua, $match)) {
                    if (!preg_match('/^(Smartphone|PPC$)/u', $match[1])) {
                        $model = $match[1];
                    }
                }

                if (empty($model) && preg_match('/MSIE [0-9.]+; Windows CE; ([^;\(]+);? ?(?:PPC|SP|Smartphone); ?[0-9]+x[0-9]+/u', $ua, $match)) {
                    if (!preg_match('/^(MIDP-2.0)/u', $match[1])) {
                        $model = $match[1];
                    }
                }

                if (empty($model) && preg_match('/MSIE [0-9.]+; Windows CE; ([^;\)]+)(?:; (?:PPC|SP|Smartphone); [0-9]+x[0-9]+)?\)( \[[a-zA-Z\-]+\])?$/u', $ua, $match)) {
                    if (!preg_match('/^(IEMobile|MIDP-2.0|Smartphone|PPC$)/u', $match[1])) {
                        $model = $match[1];
                    }
                }

                if (!empty($model)) {
                    $model = preg_replace('/(HTC\/|Toshiba\/)/', '', $model);

                    $this->device->model = $model;
                    $this->device->identified |= Constants\Id::PATTERN;
                    $this->os->name = 'Windows Mobile';

                    $device = Data\DeviceModels::identify('wm', $model);
                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }
                } else {
                    if (empty($model) && preg_match('/Windows CE [^;]+; Trident\/[^;]+; IEMobile[\/ ][^;]+[\);] ([A-Z\s]+); ?([^\/\),]+)/ui', $ua, $match)) {
                        $model = $match[2];
                    }

                    if (!empty($model)) {
                        $this->device->model = $model;
                        $this->device->identified |= Constants\Id::PATTERN;
                        $this->os->name = 'Windows Phone';

                        $device = Data\DeviceModels::identify('wp', $model);
                        if ($device->identified) {
                            $device->identified |= $this->device->identified;
                            $this->device = $device;
                        }
                    }
                }
            }

            if (preg_match('/Microsoft Windows; (PPC|Smartphone)/u', $ua)) {
                $this->os->name = 'Windows Mobile';
                $this->device->type = Constants\DeviceType::MOBILE;
            }

            if (preg_match('/Windows CE; (PPC|Smartphone)/u', $ua)) {
                $this->os->name = 'Windows Mobile';
                $this->device->type = Constants\DeviceType::MOBILE;
            }


            /* Detect models in common places */

            if (preg_match('/Windows ?Mobile/u', $ua)) {
                $this->os->name = 'Windows Mobile';
                $this->device->type = Constants\DeviceType::MOBILE;

                if (preg_match('/Windows ?Mobile[\/ ]([0-9.]*)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1], 'details' => 2]);
                }

                if (preg_match('/Windows Mobile; (?:SHARP\/)?([^;]+); (?:PPC|Smartphone);/u', $ua, $match)) {
                    $this->device->model = $match[1];
                    $this->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wm', $match[1]);
                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }
                }

                if (preg_match('/\(([^;]+); U; Windows Mobile/u', $ua, $match)) {
                    $this->device->model = $match[1];
                    $this->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wm', $match[1]);
                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }
                }
            }

            if (preg_match('/(Windows Phone|Windows NT 1[0-9]\.[0-9]; ARM|WPDesktop|ZuneWP7)/u', $ua)) {
                $this->os->name = 'Windows Phone';
                $this->device->type = Constants\DeviceType::MOBILE;

                if (preg_match('/Windows Phone(?: OS)?[ \/]([0-9.]*)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1], 'details' => 2]);

                    if (intval($match[1]) < 7) {
                        $this->os->name = 'Windows Mobile';
                    }
                }

                /* Windows Mobile 6.5 */
                if (preg_match('/Windows NT 5.1; ([^;]+); Windows Phone/u', $ua, $match)) {
                    $this->device->model = $match[1];
                    $this->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wm', $match[1]);
                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }
                }

                /* Windows Phone 7 (buggy) */
                if (preg_match('/Windows Phone OS [^;]+; Trident\/[^;]+; IEMobile[\/ ][^;]+[\);] ([A-Z\s]+); ?([^\/\),]+)/ui', $ua, $match)) {
                    $this->device->manufacturer = $match[1];
                    $this->device->model = $match[2];
                    $this->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wp', $match[2]);
                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }
                }

                /* Windows Phone 7 and 8 */
                if (preg_match('/IEMobile\/[^;]+;(?: ARM; Touch; )?(?:rv:[0-9]+; )?(?: WpsLondonTest; )?\s*([^;\s][^;\)]*);\s*([^;\)\s][^;\)]*)[;|\)]/u', $ua, $match)) {
                    $this->device->manufacturer = $match[1];
                    $this->device->model = $match[2];
                    $this->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wp', $match[2]);
                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }
                }

                /* Windows Phone 10 */
                if (preg_match('/Windows Phone 1[0-9]\.[0-9]; Android [0-9\.]+; (?:WebView\/[0-9\.]+; )?([^;\s][^;]*);\s*([^;\)\s][^;\)]*)[;|\)]/u', $ua, $match)) {
                    $this->device->manufacturer = $match[1];
                    $this->device->model = $match[2];
                    $this->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wp', $match[2]);
                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }
                }

                /* Windows Phone 10 Continuum */
                if (preg_match('/Windows NT 1[0-9]\.[0-9]; ARM; ([^;\)\s][^;\)]*)\)/u', $ua, $match)) {
                    $this->device->model = $match[1];
                    $this->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wp', $match[1]);
                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }

                    $this->device->type = Constants\DeviceType::DESKTOP;
                }

                /* Third party browsers */
                if (preg_match('/IEMobile\/[^;]+;(?: ARM; Touch; )?\s*(?:[^\/]+\/[^\/]+);\s*([^;\s][^;]*);\s*([^;\)\s][^;\)]*)[;|\)]/u', $ua, $match)) {
                    $this->device->manufacturer = $match[1];
                    $this->device->model = $match[2];
                    $this->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wp', $match[2]);
                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }
                }

                if (preg_match('/\(Windows Phone OS\/[0-9\.]+; ([^:]+):([^;]+); [a-z]+(?:\-[a-z]+)?\)/iu', $ua, $match)) {
                    $this->device->manufacturer = $match[1];
                    $this->device->model = $match[2];
                    $this->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wp', $match[2]);
                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }
                }

                /* Desktop mode of WP 8.1 */
                if (preg_match('/WPDesktop;\s*([^;\)]*)(?:;\s*([^;\)]*))?(?:;\s*([^;\)]*))?\) like Gecko/u', $ua, $match)) {
                    if (preg_match("/^[A-Z]+$/", $match[1]) && isset($match[2])) {
                        $this->device->manufacturer = $match[1];
                        $this->device->model = $match[2];
                    } else {
                        $this->device->model = $match[1];
                    }

                    $this->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wp', $this->device->model);
                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }
                }

                /* Desktop mode of WP 7 */
                if (preg_match('/XBLWP7; ZuneWP7; ([^\)]+)\)/u', $ua, $match)) {
                    $this->device->model = $match[1];
                    $this->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wp', $match[1]);
                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }
                }

                /* Desktop mode of WP 8.0 and 8.1 Update (buggy version) */
                if (preg_match('/Touch; WPDesktop;\s*([^;\)]*)(?:;\s*([^;\)]*))?(?:;\s*([^;\)]*))?\)/u', $ua, $match)) {
                    if (preg_match("/^[A-Z]+$/", $match[1]) && isset($match[2])) {
                        $this->device->manufacturer = $match[1];
                        $this->device->model = $match[2];
                    } else {
                        $this->device->model = $match[1];
                    }

                    $this->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wp', $this->device->model);
                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }
                }

                if (isset($this->device->manufacturer) && isset($this->device->model)) {
                    if ($this->device->manufacturer == 'ARM' && $this->device->model == 'Touch') {
                        $this->device->manufacturer = null;
                        $this->device->model = null;
                        $this->device->identified = Constants\Id::NONE;
                    }

                    if ($this->device->model == 'XDeviceEmulator') {
                        $this->device->manufacturer = null;
                        $this->device->model = null;
                        $this->device->type = Constants\DeviceType::EMULATOR;
                        $this->device->identified |= Constants\Id::MATCH_UA;
                    }
                }
            }
        } elseif (preg_match('/WMPRO/u', $ua)) {
            $this->os->name = 'Windows Mobile';
            $this->device->type = Constants\DeviceType::MOBILE;
        }
    }

    private function detectWindowsOemManufacturer($ua)
    {
        $manufacturers = [
            'MAAR' => 'Acer',
            'ASJB' => 'Asus',
            'ASU2' => 'Asus',
            'MAAU' => 'Asus',
            'NP06' => 'Asus',
            'NP07' => 'Asus',
            'NP08' => 'Asus',
            'NP09' => 'Asus',
            'CMNTDF' => 'Compaq',
            'CPDTDF' => 'Compaq',
            'CPNTDF' => 'Compaq',
            'MDDR' => 'Dell',
            'MDDC' => 'Dell',
            'MDDS' => 'Dell',
            'FSJB' => 'Fujitsu',
            'MAFS' => 'Fujitsu',
            'MAGW' => 'Gateway',
            'HPCMHP' => 'HP',
            'HPDTDF' => 'HP',
            'HPNTDF' => 'HP',
            'MANM' => 'Hyrican',
            'LCJB' => 'Lenovo',
            'LEN2' => 'Lenovo',
            'MALC' => 'Lenovo',
            'MALE' => 'Lenovo',
            'MALN' => 'Lenovo',
            'MAMD' => 'Medion',
            'MAMI' => 'MSI',
            'MAM3' => 'MSI',
            'MASM' => 'Samsung',
            'SMJB' => 'Samsung',
            'MASA' => 'Sony',
            'MASE' => 'Sony',
            'MASP' => 'Sony',
            'MATB' => 'Toshiba',
            'MATM' => 'Toshiba',
            'MATP' => 'Toshiba',
            'TAJB' => 'Toshiba',
            'TNJB' => 'Toshiba',
        ];

        $keys = array_keys($manufacturers);

        if (preg_match('/; (' . implode('|', $keys) . ')(?:JS)?[\);]/u', $ua, $match)) {
            $this->device->manufacturer = $manufacturers[$match[1]];
            $this->device->hidden = true;
            $this->device->identified |= Constants\Id::INFER;
        }
    }


    /* Jolla Sailfish */

    private function detectSailfish($ua)
    {
        if (preg_match('/Sailfish;/u', $ua)) {
            $this->os->name = 'Sailfish';
            $this->os->version = null;

            if (preg_match('/Jolla;/u', $ua)) {
                $this->device->manufacturer = 'Jolla';
            }

            if (preg_match('/Mobile/u', $ua)) {
                $this->device->model = 'Phone';
                $this->device->type = Constants\DeviceType::MOBILE;
                $this->device->identified = Constants\Id::PATTERN;
            }

            if (preg_match('/Tablet/u', $ua)) {
                $this->device->model = 'Tablet';
                $this->device->type = Constants\DeviceType::TABLET;
                $this->device->identified = Constants\Id::PATTERN;
            }
        }
    }


    /* Bada */

    private function detectBada($ua)
    {
        if (preg_match('/[b|B]ada/u', $ua)) {
            $this->os->name = 'Bada';

            if (preg_match('/[b|B]ada[\/ ]([0-9.]*)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1], 'details' => 2]);
            }

            $this->device->type = Constants\DeviceType::MOBILE;

            if (preg_match('/\(([^;]+); ([^\/]+)\//u', $ua, $match)) {
                if ($match[1] != 'Bada') {
                    $this->device->manufacturer = $match[1];
                    $this->device->model = $match[2];
                    $this->device->identified = Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('bada', $match[2]);

                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }
                }
            }
        }
    }


    /* Tizen */

    private function detectTizen($ua)
    {
        if (preg_match('/Tizen/u', $ua)) {
            $this->os->name = 'Tizen';

            if (preg_match('/Tizen[\/ ]?([0-9.]*[0-9])/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }

            if (preg_match('/\(([^;]+); ([^\/]+)\//u', $ua, $match)) {
                $falsepositive = false;
                if (strtoupper($match[1]) == 'SMART-TV') {
                    $falsepositive = true;
                }
                if ($match[1] == 'TV') {
                    $falsepositive = true;
                }
                if ($match[1] == 'Linux') {
                    $falsepositive = true;
                }
                if ($match[1] == 'Tizen') {
                    $falsepositive = true;
                }

                if (!$falsepositive) {
                    $this->device->manufacturer = $match[1];
                    $this->device->model = $match[2];
                    $this->device->identified = Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('tizen', $match[2]);

                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }
                }
            }

            if (preg_match('/\s*([^;]+);\s+([^;\)]+)\)/u', $ua, $match)) {
                $falsepositive = false;
                if ($match[1] == 'U') {
                    $falsepositive = true;
                }
                if (str_starts_with($match[2], 'Tizen')) {
                    $falsepositive = true;
                }
                if (str_starts_with($match[2], 'AppleWebKit')) {
                    $falsepositive = true;
                }
                if (preg_match("/^[a-z]{2,2}(?:\-[a-z]{2,2})?$/", $match[2])) {
                    $falsepositive = true;
                }

                if (!$falsepositive) {
                    $this->device->model = $match[2];
                    $this->device->identified = Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('tizen', $match[2]);

                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }
                }
            }


            if (!$this->device->type && preg_match('/Mobile/iu', $ua, $match)) {
                $this->device->type = Constants\DeviceType::MOBILE;
            }


            if (preg_match('/\((SMART[ -])?TV;/iu', $ua, $match)) {
                $this->device->type = Constants\DeviceType::TELEVISION;
                $this->device->manufacturer = 'Samsung';
                $this->device->series = 'Smart TV';
                $this->device->identified = Constants\Id::PATTERN;
            }


            if (preg_match('/(?:Samsung|Tizen ?)Browser\/([0-9.]*)/u', $ua, $match)) {
                $this->browser->name = "Samsung Browser";
                $this->browser->channel = null;
                $this->browser->stock = true;
                $this->browser->version = new Version(['value' => $match[1]]);
                $this->browser->channel = null;
            }
        }

        if (preg_match('/Linux\; U\; Android [0-9.]+\; ko\-kr\; SAMSUNG\; (NX[0-9]+[^\)]]*)/u', $ua, $match)) {
            $this->os->name = 'Tizen';
            $this->os->version = null;

            $this->device->type = Constants\DeviceType::CAMERA;
            $this->device->manufacturer = 'Samsung';
            $this->device->model = $match[1];
            $this->device->identified = Constants\Id::PATTERN;
        }
    }


    /* Symbian */

    private function detectSymbian($ua)
    {
        if (!preg_match('/(EPOC|Series|Symbian|S60|UIQ)/ui', $ua)) {
            return;
        }

        /* EPOC */

        if (preg_match('/EPOC(?:32)?[;\-\)]/u', $ua, $match)) {
            $this->os->name = 'EPOC';
            $this->os->family = new Family(['name' => 'Symbian']);
            $this->device->type = Constants\DeviceType::PDA;

            if (preg_match('/Crystal\/([0-9.]*)/u', $ua, $match)) {
                $this->os->name = 'Series80';
                $this->os->version = new Version(['value' => '1.0']);
                $this->os->family->version = new Version(['value' => $match[1]]);
                $this->device->type = Constants\DeviceType::MOBILE;

                $this->device->manufacturer = 'Nokia';
                $this->device->model = '9210';
                $this->device->identified |= Constants\Id::PATTERN;
            }

            if (preg_match('/Nokia\/Series-9200/u', $ua)) {
                $this->os->name = 'Series80';
                $this->os->version = new Version(['value' => '1.0']);
                $this->device->type = Constants\DeviceType::MOBILE;

                $this->device->manufacturer = 'Nokia';
                $this->device->model = '9210i';
                $this->device->identified |= Constants\Id::PATTERN;
            }
        }

        /* Series 80 */

        if (preg_match('/Series80\/([0-9.]*)/u', $ua, $match)) {
            $this->os->name = 'Series80';
            $this->os->version = new Version(['value' => $match[1]]);
            $this->os->family = new Family(['name' => 'Symbian']);
            $this->device->type = Constants\DeviceType::MOBILE;
        }

        /* Series 60 */

        if (preg_match('/Symbian\/3/u', $ua)) {
            $this->os->name = 'Series60';
            $this->os->version = new Version(['value' => '5.2']);
            $this->os->family = new Family(['name' => 'Symbian']);
            $this->device->type = Constants\DeviceType::MOBILE;
        }

        if (preg_match('/Series[ ]?60/u', $ua) || preg_match('/S60[V\/;]/u', $ua) || preg_match('/S60 Symb/u', $ua)) {
            $this->os->name = 'Series60';
            $this->os->family = new Family(['name' => 'Symbian']);
            $this->device->type = Constants\DeviceType::MOBILE;

            if (preg_match('/Series60\/([0-9.]*)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }

            if (preg_match('/S60\/([0-9.]*)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }

            if (preg_match('/S60V([0-9.]*)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }
        }

        /* UIQ */

        if (preg_match('/UIQ\/([0-9.]*)/u', $ua, $match)) {
            $this->os->name = 'UIQ';
            $this->os->version = new Version(['value' => $match[1]]);
            $this->os->family = new Family(['name' => 'Symbian']);
            $this->device->type = Constants\DeviceType::MOBILE;
        }

        /* Symbian */

        if (preg_match('/Symbian/u', $ua)) {
            $this->os->family = new Family(['name' => 'Symbian']);
            $this->device->type = Constants\DeviceType::MOBILE;

            if (preg_match('/SymbianOS\/([0-9.]*)/u', $ua, $match)) {
                $this->os->family->version = new Version(['value' => $match[1]]);
            }
        }


        if ($this->os->isFamily('Symbian')) {
            if (preg_match('/Nokia-?([^\/;\)\s]+)[\s|\/|;|\)]/u', $ua, $match)) {
                if ($match[1] != 'Browser') {
                    $this->device->manufacturer = 'Nokia';
                    $this->device->model = Data\DeviceModels::cleanup($match[1]);
                    $this->device->identified |= Constants\Id::PATTERN;
                }
            }

            if (preg_match('/Symbian(?:\/3)?; U; (?:Nokia)?([^;]+); [a-z][a-z](?:\-[a-z][a-z])?/u', $ua, $match)) {
                $this->device->manufacturer = 'Nokia';
                $this->device->model = Data\DeviceModels::cleanup($match[1]);
                $this->device->identified |= Constants\Id::PATTERN;
            }

            if (preg_match('/Vertu([^\/;]+)[\/|;]/u', $ua, $match)) {
                $this->device->manufacturer = 'Vertu';
                $this->device->model = Data\DeviceModels::cleanup($match[1]);
                $this->device->identified |= Constants\Id::PATTERN;
            }

            if (preg_match('/Samsung\/([^;]*);/u', $ua, $match)) {
                $this->device->manufacturer = 'Samsung';
                $this->device->model = Data\DeviceModels::cleanup($match[1]);
                $this->device->identified |= Constants\Id::PATTERN;
            }

            if (isset($this->device->model)) {
                $device = Data\DeviceModels::identify('symbian', $this->device->model);
                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }
        }
    }

    private function detectNokiaOs($ua)
    {
        if (!preg_match('/(Series|MeeGo|Maemo|Geos)/ui', $ua)) {
            return;
        }

        /* Series 40 */

        if (preg_match('/Series40/u', $ua)) {
            $this->os->name = 'Series40';

            if (preg_match('/Nokia([^\/]+)\//u', $ua, $match)) {
                $this->device->manufacturer = 'Nokia';
                $this->device->model = Data\DeviceModels::cleanup($match[1]);
                $this->device->identified |= Constants\Id::PATTERN;
            }

            if (isset($this->device->model)) {
                $device = Data\DeviceModels::identify('s40', $this->device->model);
                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }

            if (isset($this->device->model)) {
                $device = Data\DeviceModels::identify('asha', $this->device->model);
                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->os->name = 'Nokia Asha Platform';
                    $this->os->version = new Version(['value' => '1.0']);
                    $this->device = $device;
                }

                if (preg_match('/java_runtime_version=Nokia_Asha_([0-9_]+);/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => str_replace('_', '.', $match[1])]);
                }
            }

            $this->device->type = Constants\DeviceType::MOBILE;
        }

        /* Series 30+ */

        if (preg_match('/Series30Plus/u', $ua)) {
            $this->os->name = 'Series30+';

            if (preg_match('/Nokia([^\/]+)\//u', $ua, $match)) {
                $this->device->manufacturer = 'Nokia';
                $this->device->model = Data\DeviceModels::cleanup($match[1]);
                $this->device->identified |= Constants\Id::PATTERN;
            }

            if (isset($this->device->model)) {
                $device = Data\DeviceModels::identify('s30plus', $this->device->model);
                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }

            $this->device->type = Constants\DeviceType::MOBILE;
        }

        /* Meego */

        if (preg_match('/MeeGo/u', $ua)) {
            $this->os->name = 'MeeGo';
            $this->device->type = Constants\DeviceType::MOBILE;

            if (preg_match('/Nokia([^\);]+)\)/u', $ua, $match)) {
                $this->device->manufacturer = 'Nokia';
                $this->device->model = Data\DeviceModels::cleanup($match[1]);
                $this->device->identified |= Constants\Id::PATTERN;
                $this->device->generic = false;
            }
        }

        /* Maemo */

        if (preg_match('/Maemo/u', $ua)) {
            $this->os->name = 'Maemo';
            $this->device->type = Constants\DeviceType::MOBILE;

            if (preg_match('/(N[0-9]+)/u', $ua, $match)) {
                $this->device->manufacturer = 'Nokia';
                $this->device->model = $match[1];
                $this->device->identified |= Constants\Id::PATTERN;
                $this->device->generic = false;
            }
        }

        /* GEOS */

        if (preg_match('/Geos ([0-9.]+)/u', $ua, $match)) {
            $this->os->name = 'GEOS';
            $this->os->version = new Version(['value' => $match[1]]);
            $this->device->type = Constants\DeviceType::MOBILE;

            if (preg_match('/Nokia-([0-9]{4,4}[a-z]?)/u', $ua, $match)) {
                $this->device->manufacturer = 'Nokia';
                $this->device->model = $match[1];
                $this->device->identified |= Constants\Id::PATTERN;
                $this->device->generic = false;
            }
        }
    }


    /* WebOS */

    private function detectWebos($ua)
    {
        if (preg_match('/(?:web|hpw)OS\/(?:HP webOS )?([0-9.]*)/u', $ua, $match)) {
            $this->os->name = 'webOS';
            $this->os->version = new Version(['value' => $match[1]]);
            $this->device->type = preg_match('/Tablet/iu', $ua) ? Constants\DeviceType::TABLET : Constants\DeviceType::MOBILE;
            $this->device->generic = false;
        }

        if (preg_match('/(?:Spark|elite)\/fzz/u', $ua, $match) || preg_match('/webOSBrowser/u', $ua, $match)) {
            $this->os->name = 'webOS';
            $this->device->type = preg_match('/Tablet/iu', $ua) ? Constants\DeviceType::TABLET : Constants\DeviceType::MOBILE;
            $this->device->generic = false;
        }

        if (preg_match('/ (Pre|Pixi|TouchPad|P160UN?A?)\/[0-9\.]+$/u', $ua, $match)) {
            $this->os->name = 'webOS';
            $this->device->type = $match[1] == 'TouchPad' ? Constants\DeviceType::TABLET : Constants\DeviceType::MOBILE;
            $this->device->generic = false;
        }

        if ($this->isOs('webOS')) {
            if (preg_match('/Pre\/1.0/u', $ua)) {
                $this->device->manufacturer = 'Palm';
                $this->device->model = 'Pre';
            }
            if (preg_match('/Pre\/1.1/u', $ua)) {
                $this->device->manufacturer = 'Palm';
                $this->device->model = 'Pre Plus';
            }
            if (preg_match('/Pre\/1.2/u', $ua)) {
                $this->device->manufacturer = 'Palm';
                $this->device->model = 'Pre 2';
            }
            if (preg_match('/Pre\/3.0/u', $ua)) {
                $this->device->manufacturer = 'Palm';
                $this->device->model = 'Pre 3';
            }
            if (preg_match('/Pixi\/1.0/u', $ua)) {
                $this->device->manufacturer = 'Palm';
                $this->device->model = 'Pixi';
            }
            if (preg_match('/Pixi\/1.1/u', $ua)) {
                $this->device->manufacturer = 'Palm';
                $this->device->model = 'Pixi Plus';
            }
            if (preg_match('/P160UN?A?\/1.0/u', $ua)) {
                $this->device->manufacturer = 'HP';
                $this->device->model = 'Veer';
            }
            if (preg_match('/TouchPad\/1.0/u', $ua)) {
                $this->device->manufacturer = 'HP';
                $this->device->model = 'TouchPad';
            }

            if (preg_match('/Emulator\//u', $ua) || preg_match('/Desktop\//u', $ua)) {
                $this->device->type = Constants\DeviceType::EMULATOR;
                $this->device->manufacturer = null;
                $this->device->model = null;
            }

            $this->device->identified |= Constants\Id::MATCH_UA;
        }
    }


    /* Kai OS */

    private function detectKaiOS($ua)
    {
        if (preg_match('/Kai(OS)?\/([0-9.]+)/i', $ua, $match)) {
            $this->os->reset(['name' => 'KaiOS', 'version' => new Version(['value' => $match[2]])]);
            $this->os->family = new Family(['name' => 'Firefox OS']);
        }
    }


    /* BlackBerry */

    private function detectBlackberry($ua)
    {
        /* BlackBerry OS */

        if (preg_match('/RIM([0-9]{3,3})/u', $ua, $match)) {
            $this->os->name = 'BlackBerry OS';
            $this->device->manufacturer = 'RIM';
            $this->device->model = $match[1];
            $this->device->type = Constants\DeviceType::MOBILE;
            $this->device->identified = Constants\Id::INFER;
        }

        if (preg_match('/BlackBerry/u', $ua) && !preg_match('/BlackBerry Runtime for Android Apps/u', $ua)) {
            $this->os->name = 'BlackBerry OS';

            $this->device->model = 'BlackBerry';
            $this->device->manufacturer = 'RIM';
            $this->device->type = Constants\DeviceType::MOBILE;
            $this->device->identified = Constants\Id::INFER;

            if (!preg_match('/Opera/u', $ua)) {
                if (preg_match('/BlackBerry([0-9]+[ei]?)\/([0-9.]*)/u', $ua, $match)) {
                    $this->device->model = $match[1];
                    $this->os->version = new Version(['value' => $match[2], 'details' => 2]);
                }

                if (preg_match('/; BlackBerry ([0-9]*);/u', $ua, $match)) {
                    $this->device->model = $match[1];
                }

                if (preg_match('/; ([0-9]+)[^;\)]+\)/u', $ua, $match)) {
                    $this->device->model = $match[1];
                }

                if (preg_match('/Version\/([0-9.]*)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1], 'details' => 2]);
                }

                if (isset($this->os->version) && $this->os->version->toFloat() >= 10) {
                    $this->os->name = 'BlackBerry';
                }

                if ($this->device->model) {
                    $device = Data\DeviceModels::identify('blackberry', $this->device->model);

                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }
                }
            }
        }

        /* BlackBerry 10 */

        if (preg_match('/\(BB(1[^;]+); ([^\)]+)\)/u', $ua, $match)) {
            $this->os->name = 'BlackBerry';
            $this->os->version = new Version(['value' => $match[1], 'details' => 2]);

            $this->device->manufacturer = 'BlackBerry';
            $this->device->model = $match[2];

            if ($this->device->model == 'Kbd') {
                $this->device->model = 'Q series or Passport';
            }

            if ($this->device->model == 'Touch') {
                $this->device->model = 'A or Z series';
            }

            if ($this->device->model == 'STL100-2') {
                $this->device->model = 'Z10';
            }

            $this->device->type = preg_match('/Mobile/u', $ua) ? Constants\DeviceType::MOBILE : Constants\DeviceType::TABLET;
            $this->device->identified |= Constants\Id::MATCH_UA;

            if (preg_match('/Version\/([0-9.]+)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1], 'details' => 2]);
            }
        }

        /* BlackBerry Tablet OS */

        if (preg_match('/RIM Tablet OS ([0-9.]*)/u', $ua, $match)) {
            $this->os->name = 'BlackBerry Tablet OS';
            $this->os->version = new Version(['value' => $match[1], 'details' => 2]);

            $this->device->manufacturer = 'RIM';
            $this->device->model = 'BlackBerry PlayBook';
            $this->device->type = Constants\DeviceType::TABLET;
            $this->device->identified |= Constants\Id::MATCH_UA;
        } elseif (preg_match('/\(PlayBook;/u', $ua) && preg_match('/PlayBook Build\/([0-9.]*)/u', $ua, $match)) {
            $this->os->name = 'BlackBerry Tablet OS';
            $this->os->version = new Version(['value' => $match[1], 'details' => 2]);

            $this->device->manufacturer = 'RIM';
            $this->device->model = 'BlackBerry PlayBook';
            $this->device->type = Constants\DeviceType::TABLET;
            $this->device->identified |= Constants\Id::MATCH_UA;
        } elseif (preg_match('/PlayBook/u', $ua) && !preg_match('/Android/u', $ua)) {
            if (preg_match('/Version\/([0-9.]*)/u', $ua, $match)) {
                $this->os->name = 'BlackBerry Tablet OS';
                $this->os->version = new Version(['value' => $match[1], 'details' => 2]);

                $this->device->manufacturer = 'RIM';
                $this->device->model = 'BlackBerry PlayBook';
                $this->device->type = Constants\DeviceType::TABLET;
                $this->device->identified |= Constants\Id::MATCH_UA;
            }
        }

        /* Internal versions of BlackBerry 10 running on the Playbook */

        if ($this->isOs('BlackBerry Tablet OS', '>=', 10)) {
            $this->os->name = 'BlackBerry';
        }
    }


    /* Chrome OS */

    private function detectChromeos($ua)
    {
        /* ChromeCast */

        if (preg_match('/CrKey/u', $ua) && !preg_match('/Espial/u', $ua)) {
            $this->device->manufacturer = 'Google';
            $this->device->model = 'Chromecast';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->device->identified |= Constants\Id::MATCH_UA;
            $this->device->generic = false;
        }

        /* Chrome OS */

        if (preg_match('/CrOS/u', $ua)) {
            $this->os->name = 'Chrome OS';
            $this->device->type = Constants\DeviceType::DESKTOP;
        }
    }


    /* Open TV */

    private function detectOpenTV($ua)
    {
        if (preg_match('/OpenTV/ui', $ua, $match)) {
            $this->device->type = Constants\DeviceType::TELEVISION;

            $this->os->name = 'OpenTV';
            $this->os->version = null;

            if (preg_match('/OpenTV Build\/([0-9\.]+)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }

            if (preg_match('/OpenTV ([0-9\.]+)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }

            if (preg_match('/Opentv([0-9]+)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }

            if (preg_match('/OTV([0-9\.]+)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }
        }
    }


    /* Qtopia */

    private function detectQtopia($ua)
    {
        if (preg_match('/Qtopia/u', $ua)) {
            $this->os->name = 'Qtopia';

            if (preg_match('/Qtopia\/([0-9.]+)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }
        }
    }


    /* Unix */

    private function detectUnix($ua)
    {
        if (!preg_match('/(UNIX|OSF|ULTRIX|HP-UX|SunOS|Solaris|AIX|IRIX|NEWS-OS|GENIX)/ui', $ua)) {
            return;
        }

        /* Unix */

        if (preg_match('/Unix/iu', $ua)) {
            $this->os->name = 'Unix';
        }

        /* Unix System V */

        if (preg_match('/(?:UNIX_System_V|UNIX_SV) ([0-9.]*)/u', $ua, $match)) {
            $this->os->name = 'UNIX System V';
            $this->os->family = new Family(['name' => 'UNIX']);
            $this->os->version = new Version(['value' => $match[1]]);
            $this->device->type = Constants\DeviceType::DESKTOP;
        }

        /* Digital Unix */

        if (preg_match('/OSF1?[ _]/u', $ua)) {
            $this->os->name = 'Digital Unix';
            $this->os->family = new Family(['name' => 'UNIX']);

            if (preg_match('/OSF1?[ _]V?([0-9.]*)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }

            $this->device->type = Constants\DeviceType::DESKTOP;
        }

        /* Digital ULTRIX */

        if (preg_match('/ULTRIX/u', $ua)) {
            $this->os->name = 'ULTRIX';
            $this->os->family = new Family(['name' => 'BSD']);

            if (preg_match('/ULTRIX ([0-9.]*)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }

            $this->device->type = Constants\DeviceType::DESKTOP;
        }

        /* HP-UX */

        if (preg_match('/HP-UX/u', $ua)) {
            $this->os->name = 'HP-UX';
            $this->os->family = new Family(['name' => 'UNIX']);

            if (preg_match('/HP-UX [A-Z].0?([1-9][0-9.]*)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }

            $this->device->type = Constants\DeviceType::DESKTOP;
        }

        /* Solaris */

        if (preg_match('/SunOS/u', $ua)) {
            $this->os->name = 'Solaris';
            $this->os->family = new Family(['name' => 'UNIX']);

            if (preg_match('/SunOS ([1234]\.[0-9\.]+)/u', $ua, $match)) {
                $this->os->name = 'SunOS';
                $this->os->version = new Version(['value' => $match[1]]);
                $this->os->family = new Family(['name' => 'BSD']);

                if (preg_match('/SunOS 4\.1\.([1234])/u', $ua, $match)) {
                    $this->os->name = 'Solaris';

                    switch ($match[1]) {
                        case '1':
                            $this->os->version = new Version(['value' => '1.0']);
                            break;
                        case '2':
                            $this->os->version = new Version(['value' => '1.0.1']);
                            break;
                        case '3':
                            $this->os->version = new Version(['value' => '1.1']);
                            break;
                        case '4':
                            $this->os->version = new Version(['value' => '1.1.2']);
                            break;
                    }
                }
            }

            if (preg_match('/SunOS 5\.([123456](?:\.[0-9\.]*)?) /u', $ua, $match)) {
                $this->os->version = new Version(['value' => '2.' . $match[1]]);
            } elseif (preg_match('/SunOS 5\.([0-9\.]*)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }

            $this->device->type = Constants\DeviceType::DESKTOP;
        }

        if (preg_match('/Solaris(?: ([0-9\.]+))?;/u', $ua, $match)) {
            $this->os->name = 'Solaris';
            $this->os->family = new Family(['name' => 'UNIX']);

            if (preg_match('/Solaris ([0-9\.]+);/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }

            $this->device->type = Constants\DeviceType::DESKTOP;
        }

        /* AIX */

        if (preg_match('/AIX/u', $ua)) {
            $this->os->name = 'AIX';
            $this->os->family = new Family(['name' => 'UNIX']);

            if (preg_match('/AIX ([0-9.]*)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }

            $this->device->type = Constants\DeviceType::DESKTOP;
        }

        /* IRIX */

        if (preg_match('/IRIX/u', $ua)) {
            $this->os->name = 'IRIX';
            $this->os->family = new Family(['name' => 'UNIX']);

            if (preg_match('/IRIX ([0-9.]*)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }

            if (preg_match('/IRIX;?(?:64|32) ([0-9.]*)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }

            $this->device->type = Constants\DeviceType::DESKTOP;
        }

        /* Sony NEWS OS */

        if (preg_match('/NEWS-OS ([0-9\.]+)/u', $ua, $match)) {
            $this->os->name = 'NEWS OS';
            $this->os->version = new Version(['value' => $match[1]]);
            $this->os->family = new Family(['name' => 'BSD']);


            if (preg_match('/NEWS-OS [56]/u', $ua)) {
                $this->os->family = new Family(['name' => 'UNIX']);
            }

            $this->device->type = Constants\DeviceType::DESKTOP;
        }

        /* NEC EWS-UX */

        if (preg_match('/EWS-UNIX rev ([0-9\.]+)/u', $ua, $match)) {
            $this->os->name = 'EWS-UX';
            $this->os->version = new Version(['value' => $match[1]]);
            $this->os->family = new Family(['name' => 'UNIX']);

            $this->device->type = Constants\DeviceType::DESKTOP;
        }

        /* National Semiconductors GENIX */

        if (preg_match('/GENIX ([0-9\.]+)/u', $ua, $match)) {
            $this->os->name = 'GENIX';
            $this->os->version = new Version(['value' => $match[1]]);
            $this->os->family = new Family(['name' => 'BSD']);

            $this->device->type = Constants\DeviceType::DESKTOP;
        }
    }


    /* BSD */

    private function detectBsd($ua)
    {
        if (!preg_match('/(BSD|DragonFly)/ui', $ua)) {
            return;
        }

        if (preg_match('/X11/u', $ua)) {
            $this->device->type = Constants\DeviceType::DESKTOP;
        }

        /* BSD/OS */

        if (preg_match('/BSD\/386/u', $ua)) {
            $this->os->name = 'BSD/OS';
            $this->os->family = new Family(['name' => 'BSD']);
        }

        if (preg_match('/BSD\/OS/u', $ua)) {
            $this->os->name = 'BSD/OS';
            $this->os->family = new Family(['name' => 'BSD']);

            if (preg_match('/BSD\/OS ([0-9.]*)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }
        }

        /* FreeBSD */

        if (preg_match('/FreeBSD/iu', $ua)) {
            $this->os->name = 'FreeBSD';
            $this->os->family = new Family(['name' => 'BSD']);

            if (preg_match('/FreeBSD[ -\/]?([0-9.]*)/iu', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }
        }

        /* OpenBSD */

        if (preg_match('/OpenBSD/iu', $ua)) {
            $this->os->name = 'OpenBSD';
            $this->os->family = new Family(['name' => 'BSD']);

            if (preg_match('/OpenBSD ?([0-9.]*)/iu', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }
        }

        /* NetBSD */

        if (preg_match('/NetBSD/iu', $ua)) {
            $this->os->name = 'NetBSD';
            $this->os->family = new Family(['name' => 'BSD']);

            if (preg_match('/NetBSD ?([0-9.]*)/iu', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }
        }

        /* DragonFly */

        if (preg_match('/DragonFly/iu', $ua)) {
            $this->os->name = 'DragonFly BSD';
            $this->os->family = new Family(['name' => 'BSD']);
        }
    }


    /* Linux */

    private function detectLinux($ua)
    {
        if (preg_match('/Linux/u', $ua)) {
            $this->os->name = 'Linux';

            if (preg_match('/X11/u', $ua)) {
                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/Antergos Linux/u', $ua)) {
                $this->os->name = 'Antergos Linux';
                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/Arch ?Linux/u', $ua)) {
                $this->os->name = 'Arch Linux';
                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/Black Lab Linux/u', $ua)) {
                $this->os->name = 'Black Lab Linux';
                if (preg_match('/Black Lab Linux ([0-9\.]+)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1]]);
                }

                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/CentOS/u', $ua)) {
                $this->os->name = 'CentOS';
                if (preg_match('/CentOS\/[0-9\.\-]+el([0-9_]+)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => str_replace('_', '.', $match[1])]);
                }

                if (preg_match('/CentOS Linux release ([0-9\.]+)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1], 'details' => 2]);
                }

                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/Debian/u', $ua)) {
                $this->os->name = 'Debian';
                if (preg_match('/Debian\/([0-9.]*)/iu', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1]]);
                }

                if (preg_match('/Debian GNU\/Linux ([0-9\.]+)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1]]);
                }

                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/Fedora/u', $ua)) {
                $this->os->name = 'Fedora';
                if (preg_match('/Fedora\/[0-9\.\-]+fc([0-9]+)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => str_replace('_', '.', $match[1])]);
                }

                if (preg_match('/Fedora release ([0-9\.]+)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1]]);
                }

                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/Gentoo/u', $ua)) {
                $this->os->name = 'Gentoo';
                if (preg_match('/Gentoo Base System release ([0-9\.]+)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1]]);
                }

                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/gNewSense/u', $ua)) {
                $this->os->name = 'gNewSense';
                if (preg_match('/gNewSense\/[^\(]+\(([0-9\.]+)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1]]);
                }

                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/Kubuntu/u', $ua)) {
                $this->os->name = 'Kubuntu';
                if (preg_match('/Kubuntu[ \/]([0-9.]*)/iu', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1]]);
                }

                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/Linux Mint/u', $ua)) {
                $this->os->name = 'Linux Mint';
                if (preg_match('/Linux Mint ([0-9\.]+)/iu', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1]]);
                }

                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/Mandriva Linux/u', $ua)) {
                $this->os->name = 'Mandriva';
                if (preg_match('/Mandriva Linux\/[0-9\.\-]+mdv([0-9]+)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1]]);
                }

                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/Mageia/u', $ua)) {
                $this->os->name = 'Mageia';
                if (preg_match('/Mageia\/[0-9\.\-]+mga([0-9]+)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1]]);
                }

                if (preg_match('/Mageia ([0-9\.]+)/iu', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1]]);
                }

                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/Mandriva/u', $ua)) {
                $this->os->name = 'Mandriva';
                if (preg_match('/Mandriva\/[0-9\.\-]+mdv([0-9]+)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1]]);
                }

                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/moonOS/u', $ua)) {
                $this->os->name = 'moonOS';
                if (preg_match('/moonOS\/([0-9.]+)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1]]);
                }

                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/Red Hat/u', $ua)) {
                $this->os->name = 'Red Hat';
                if (preg_match('/Red Hat[^\/]*\/[0-9\.\-]+el([0-9_]+)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => str_replace('_', '.', $match[1])]);
                }

                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/Slackware/u', $ua)) {
                $this->os->name = 'Slackware';
                if (preg_match('/Slackware[ \/](1[0-9.]+)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1]]);
                }

                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/SUSE/u', $ua)) {
                $this->os->name = 'SUSE';
                if (preg_match('/SUSE\/([0-9]\.[0-9]+)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1]]);
                }

                if (preg_match('/openSUSE ([0-9\.]+)/iu', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1]]);
                }

                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/Turbolinux/u', $ua)) {
                $this->os->name = 'Turbolinux';
                if (preg_match('/Turbolinux\/([0-9]\.[0-9]+)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1]]);
                }

                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/Ubuntu/u', $ua)) {
                $this->os->name = 'Ubuntu';
                if (preg_match('/Ubuntu\/([0-9.]*)/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1]]);
                }

                if (preg_match('/Ubuntu ([0-9\.]+)/iu', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1], 'details' => 2]);
                }

                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/《붉은별》\/([0-9.]*)/iu', $ua, $match)) {
                $this->os->name = 'Red Star';
                $this->os->version = new Version(['value' => $match[1]]);
                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/Fedora\/[0-9\.\-]+rs([0-9\.]+)/u', $ua, $match)) {
                $this->os->name = 'Red Star';
                $this->os->version = new Version(['value' => str_replace('_', '.', $match[1])]);
                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (preg_match('/Linux\/X2\/R1/u', $ua)) {
                $this->os->name = 'LiMo';
                $this->device->type = Constants\DeviceType::MOBILE;
            }


            if (preg_match('/Linux\/SLP\/([0-9.]+)/u', $ua, $match)) {
                $this->os->name = 'Linux SLP';
                $this->os->version = new Version(['value' => $match[1]]);
                $this->device->type = Constants\DeviceType::MOBILE;
            }


            if (preg_match('/LinuxOS\//u', $ua) && preg_match('/Software\/R5/u', $ua)) {
                $this->os->name = 'EZX Linux';
                $this->device->type = Constants\DeviceType::MOBILE;
            }
        }

        if (preg_match('/elementary OS/u', $ua)) {
            $this->os->name = 'elementary OS';
            if (preg_match('/elementary OS ([A-Za-z]+)/u', $ua, $match)) {
                $this->os->version = new Version(['alias' => $match[1]]);
            }

            $this->device->type = Constants\DeviceType::DESKTOP;
        }

        if (preg_match('/\(Ubuntu; (Mobile|Tablet)/u', $ua)) {
            $this->os->name = 'Ubuntu Touch';

            if (preg_match('/\(Ubuntu; Mobile/u', $ua)) {
                $this->device->type = Constants\DeviceType::MOBILE;
            }
            if (preg_match('/\(Ubuntu; Tablet/u', $ua)) {
                $this->device->type = Constants\DeviceType::TABLET;
            }
        }

        if (preg_match('/(?:\(|; )Ubuntu ([0-9.]+) like Android/u', $ua, $match)) {
            $this->os->name = 'Ubuntu Touch';
            $this->os->version = new Version(['value' => $match[1]]);
            $this->device->type = Constants\DeviceType::MOBILE;
        }

        if (preg_match('/Lindows ([0-9.]+)/u', $ua, $match)) {
            $this->os->name = 'Lindows';
            $this->os->version = new Version(['value' => $match[1]]);
            $this->device->type = Constants\DeviceType::DESKTOP;
        }
    }


    /* Brew */

    private function detectBrew($ua)
    {
        if (preg_match('/REX; U/ui', $ua) || preg_match('/REXL4/ui', $ua)) {
            $this->os->name = 'REX';

            $this->device->type = Constants\DeviceType::MOBILE;

            if (preg_match('/REX; U; [^;]+; ([^;]+); ([^;\/]+)[^;]*; NetFront/u', $ua, $match)) {
                $this->device->manufacturer = Data\Manufacturers::identify(Constants\DeviceType::MOBILE, $match[1]);
                $this->device->model = $match[2];
                $this->device->identified = Constants\Id::PATTERN;

                $device = Data\DeviceModels::identify('brew', $match[2]);

                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }
        }

        if (preg_match('/[\(\s\-;]BREW[\s\/\-;]/ui', $ua) || preg_match('/BMP( [0-9.]*)?; U/u', $ua) || preg_match('/B(?:rew)?MP\/([0-9.]*)/u', $ua)) {
            $this->os->name = 'Brew';

            if (preg_match('/BREW MP/iu', $ua) || preg_match('/B(rew)?MP/iu', $ua)) {
                $this->os->name = 'Brew MP';
            }

            if (preg_match('/; Brew ([0-9.]+);/iu', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            } elseif (preg_match('/BREW; U; ([0-9.]+)/iu', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            } elseif (preg_match('/[\(;]BREW[\/ ]([0-9.]+)/iu', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            } elseif (preg_match('/BREW MP ([0-9.]*)/iu', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            } elseif (preg_match('/BMP ([0-9.]*); U/iu', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            } elseif (preg_match('/B(?:rew)?MP\/([0-9.]*)/iu', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }

            $this->device->type = Constants\DeviceType::MOBILE;

            if (preg_match('/(?:Brew MP|BREW|BMP) [^;]+; U; [^;]+; ([^;]+); NetFront[^\)]+\) [^\s]+ ([^\s]+)/u', $ua, $match)) {
                $this->device->manufacturer = Data\Manufacturers::identify(Constants\DeviceType::MOBILE, $match[1]);
                $this->device->model = $match[2];
                $this->device->identified = Constants\Id::PATTERN;

                $device = Data\DeviceModels::identify('brew', $match[2]);

                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }

            if (preg_match('/\(([^;]+);U;REX\/[^;]+;BREW\/[^;]+;(?:.*;)?[0-9]+\*[0-9]+(?:;CTC\/2.0)?\)/u', $ua, $match)) {
                $this->device->model = $match[1];
                $this->device->identified = Constants\Id::PATTERN;

                $device = Data\DeviceModels::identify('brew', $match[1]);

                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }

            if (preg_match('/\(BREW [^;]+; U; [^;]+; [^;]+; ([^;]+); (Polaris|Netfront)\/[0-9\.]+\/(WAP|AMB|INT)\)/ui', $ua, $match)) {
                $this->device->model = $match[1];
                $this->device->identified = Constants\Id::PATTERN;

                $device = Data\DeviceModels::identify('brew', $match[1]);

                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }

            if (preg_match('/\(BREW [^;]+; U; [^;]+; [^;]+; Opera Mobi; Presto\/[0-9\.]+\/(?:WAP|AMB|INT)\) ([^\/]+) [^\/]+\//ui', $ua, $match)) {
                $this->device->model = $match[1];
                $this->device->identified = Constants\Id::PATTERN;

                $device = Data\DeviceModels::identify('brew', $match[1]);

                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }
        }
    }


    /* Remaining operating systems */

    private function detectRemainingOperatingSystems($ua)
    {
        if (!preg_match('/(BeOS|Haiku|AmigaOS|MorphOS|AROS|VMS|RISC|Joli|OS\/2|Inferno|Syllable|Grid|MTK|MRE|MAUI|Nucleus|QNX|VRE|SpreadTrum|ThreadX)/ui', $ua)) {
            return;
        }

        $patterns = [
            ['name' => 'BeOS', 'regexp' => ['/BeOS/iu'], 'type' => Constants\DeviceType::DESKTOP],
            ['name' => 'Haiku', 'regexp' => ['/Haiku/iu'], 'type' => Constants\DeviceType::DESKTOP],
            ['name' => 'AmigaOS', 'regexp' => ['/AmigaOS ?([0-9.]+)/iu', '/AmigaOS/iu'], 'type' => Constants\DeviceType::DESKTOP],
            ['name' => 'MorphOS', 'regexp' => ['/MorphOS(?: ([0-9.]*))?/iu'], 'type' => Constants\DeviceType::DESKTOP],
            ['name' => 'AROS', 'regexp' => ['/AROS/iu'], 'type' => Constants\DeviceType::DESKTOP],
            ['name' => 'OpenVMS', 'regexp' => ['/OpenVMS V([0-9.]+)/iu', '/OpenVMS/iu'], 'type' => Constants\DeviceType::DESKTOP],
            ['name' => 'RISC OS', 'regexp' => ['/RISC OS(?:-NC)? ([0-9.]*)/iu', '/RISC OS/iu'], 'type' => Constants\DeviceType::DESKTOP],
            ['name' => 'Joli OS', 'regexp' => ['/Joli OS\/([0-9.]*)/iu'], 'type' => Constants\DeviceType::DESKTOP],
            ['name' => 'OS/2', 'regexp' => ['/OS\/2;(?: (?:U; )?Warp ([0-9.]*))?/iu'], 'type' => Constants\DeviceType::DESKTOP],
            ['name' => 'Inferno', 'regexp' => ['/Inferno/iu'], 'type' => Constants\DeviceType::DESKTOP],
            ['name' => 'Syllable', 'regexp' => ['/Syllable/iu'], 'type' => Constants\DeviceType::DESKTOP],

            ['name' => 'Grid OS', 'regexp' => ['/Grid OS ([0-9.]*)/iu'], 'type' => Constants\DeviceType::TABLET],

            ['name' => 'MRE', 'regexp' => ['/\(MTK;/iu', '/\/MTK /iu'], 'type' => Constants\DeviceType::MOBILE],
            ['name' => 'MRE', 'regexp' => ['/MRE\\\\/iu'], 'type' => Constants\DeviceType::MOBILE],
            ['name' => 'MRE', 'regexp' => ['/MAUI[-_ ](?:Browser|Runtime)/iu'], 'type' => Constants\DeviceType::MOBILE],
            ['name' => 'MRE', 'regexp' => ['/Browser\/MAUI/iu'], 'type' => Constants\DeviceType::MOBILE],
            ['name' => 'MRE', 'regexp' => ['/Nucleus RTOS\//iu'], 'type' => Constants\DeviceType::MOBILE],
            ['name' => 'MRE', 'regexp' => ['/\/Nucleus/iu'], 'type' => Constants\DeviceType::MOBILE],
            ['name' => 'MRE', 'regexp' => ['/Nucleus\//iu'], 'type' => Constants\DeviceType::MOBILE],

            ['name' => 'QNX', 'regexp' => ['/QNX/iu'], 'type' => Constants\DeviceType::MOBILE],
            ['name' => 'VRE', 'regexp' => ['/\(VRE;/iu'], 'type' => Constants\DeviceType::MOBILE],
            ['name' => 'SpreadTrum', 'regexp' => ['/\(SpreadTrum;/iu'], 'type' => Constants\DeviceType::MOBILE],

            ['name' => 'ThreadX', 'regexp' => ['/ThreadX(?:_OS)?\/([0-9.]*)/iu']],
        ];

        $count = count($patterns);
        for ($b = 0; $b < $count; $b++) {
            for ($r = 0; $r < count($patterns[$b]['regexp']); $r++) {
                if (preg_match($patterns[$b]['regexp'][$r], $ua, $match)) {
                    $this->os->name = $patterns[$b]['name'];

                    if (isset($match[1]) && $match[1]) {
                        $this->os->version = new Version(['value' => $match[1], 'details' => $patterns[$b]['details'] ?? null]);
                    } else {
                        $this->os->version = null;
                    }

                    if (isset($patterns[$b]['type'])) {
                        $this->device->type = $patterns[$b]['type'];
                    }

                    break;
                }
            }
        }
    }
}
