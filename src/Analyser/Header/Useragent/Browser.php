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
use localzet\WebAnalyzer\Model\Using;
use localzet\WebAnalyzer\Model\Version;

trait Browser
{
    private function &detectBrowser($ua)
    {
        /* Detect major browsers */
        $this->detectSafari($ua);
        $this->detectExplorer($ua);
        $this->detectChrome($ua);
        $this->detectFirefox($ua);
        $this->detectEdge($ua);
        $this->detectOpera($ua);

        /* Detect WAP browsers */
        $this->detectWapBrowsers($ua);

        /* Detect other various mobile browsers */
        $this->detectNokiaBrowser($ua);
        $this->detectSilk($ua);
        $this->detectSailfishBrowser($ua);
        $this->detectWebOSBrowser($ua);
        $this->detectDolfin($ua);
        $this->detectIris($ua);

        /* Detect other browsers */
        $this->detectUC($ua);
        $this->detectObigo($ua);
        $this->detectNetfront($ua);

        /* Detect other specific desktop browsers */
        $this->detectSeamonkey($ua);
        $this->detectModernNetscape($ua);
        $this->detectMosaic($ua);
        $this->detectKonqueror($ua);
        $this->detectOmniWeb($ua);

        /* Detect other various television browsers */
        $this->detectEspial($ua);
        $this->detectMachBlue($ua);
        $this->detectAnt($ua);
        $this->detectSraf($ua);

        /* Detect other browsers */
        $this->detectDesktopBrowsers($ua);
        $this->detectMobileBrowsers($ua);
        $this->detectTelevisionBrowsers($ua);
        $this->detectRemainingBrowsers($ua);

        return $this;
    }

    private function &refineBrowser($ua)
    {
        $this->detectUCEngine($ua);
        $this->detectLegacyNetscape($ua);

        return $this;
    }


    /* Safari */

    private function detectSafari($ua)
    {
        if (preg_match('/Safari/u', $ua)) {
            $falsepositive = false;

            if (preg_match('/Qt/u', $ua)) {
                $falsepositive = true;
            }

            if (!$falsepositive) {
                if (isset($this->os->name) && $this->os->name == 'iOS') {
                    $this->browser->name = 'Safari';
                    $this->browser->type = Constants\BrowserType::BROWSER;
                    $this->browser->version = null;
                    $this->browser->stock = true;

                    if (preg_match('/Version\/([0-9\.]+)/u', $ua, $match)) {
                        $this->browser->version = new Version(['value' => $match[1], 'hidden' => true]);
                    }
                }

                if (isset($this->os->name) && ($this->os->name == 'OS X' || $this->os->name == 'Windows')) {
                    $this->browser->name = 'Safari';
                    $this->browser->type = Constants\BrowserType::BROWSER;
                    $this->browser->stock = $this->os->name == 'OS X';

                    if (preg_match('/Version\/([0-9\.]+)/u', $ua, $match)) {
                        $this->browser->version = new Version(['value' => $match[1]]);
                    }

                    if (preg_match('/AppleWebKit\/[0-9\.]+\+/u', $ua)) {
                        $this->browser->name = 'WebKit Nightly Build';
                        $this->browser->version = null;
                    }
                }
            }
        }

        if (preg_match('/(?:Apple-PubSub|AppleSyndication)\//u', $ua)) {
            $this->browser->name = 'Safari RSS';
            $this->browser->type = Constants\BrowserType::APP_FEEDREADER;
            $this->browser->version = null;
            $this->browser->stock = true;

            $this->os->name = 'OS X';
            $this->os->version = null;

            $this->device->type = Constants\DeviceType::DESKTOP;
        }
    }


    /* Chrome */

    private function detectChrome($ua)
    {
        if (preg_match('/(?:Chrome|CrMo|CriOS)\/[0-9]/u', $ua) || preg_match('/Browser\/Chrome[0-9]/u', $ua)) {
            $this->browser->name = 'Chrome';
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->stock = false;

            $reduced = false;
            $version = '';

            if (preg_match('/(?:Chrome|CrMo|CriOS)\/([0-9.]*)/u', $ua, $match)) {
                $version = $match[1];
            }
            if (preg_match('/Browser\/Chrome([0-9.]*)/u', $ua, $match)) {
                $version = $match[1];
            }

            if (preg_match('/Chrome\/([789][0-9]|[1-9][0-9][0-9])\.0\.0\.0 /u', $ua)) {
                $reduced = true;
            }

            $this->browser->version = new Version(['value' => $version]);

            if (isset($this->os->name) && $this->os->name == 'Android') {
                if ($reduced) {
                    $this->browser->version->details = 1;
                } else {
                    $channel = Data\Chrome::getChannel('mobile', $this->browser->version->value);

                    if ($channel == 'stable') {
                        $this->browser->version->details = 1;
                    } elseif ($channel == 'beta') {
                        $this->browser->channel = 'Beta';
                    } else {
                        $this->browser->channel = 'Dev';
                    }
                }


                /* Webview for Android 4.4 and higher */
                if (implode('.', array_slice(explode('.', $version), 1, 2)) == '0.0' && (preg_match('/Version\//u', $ua) || preg_match('/Release\//u', $ua))) {
                    $this->browser->using = new Using(['name' => 'Chromium WebView', 'version' => new Version(['value' => explode('.', $version)[0]])]);
                    $this->browser->type = Constants\BrowserType::UNKNOWN;
                    $this->browser->stock = true;
                    $this->browser->name = null;
                    $this->browser->version = null;
                    $this->browser->channel = null;
                }

                /* Webview for Android 5 */
                if (preg_match('/; wv\)/u', $ua)) {
                    $this->browser->using = new Using(['name' => 'Chromium WebView', 'version' => new Version(['value' => explode('.', $version)[0]])]);
                    $this->browser->type = Constants\BrowserType::UNKNOWN;
                    $this->browser->stock = true;
                    $this->browser->name = null;
                    $this->browser->version = null;
                    $this->browser->channel = null;
                }

                /* LG Chromium based browsers */
                if (isset($this->device->manufacturer) && $this->device->manufacturer == 'LG') {
                    if (in_array($version, ['30.0.1599.103', '34.0.1847.118', '38.0.2125.0', '38.0.2125.102']) && preg_match('/Version\/4/u', $ua) && !preg_match('/; wv\)/u', $ua)) {
                        $this->browser->name = "LG Browser";
                        $this->browser->channel = null;
                        $this->browser->stock = true;
                        $this->browser->version = null;
                    }
                }

                /* Samsung Chromium based browsers */
                if (isset($this->device->manufacturer) && $this->device->manufacturer == 'Samsung') {
                    /* Version 1.0 */
                    if ($version == '18.0.1025.308' && preg_match('/Version\/1.0/u', $ua)) {
                        $this->browser->name = "Samsung Internet";
                        $this->browser->channel = null;
                        $this->browser->stock = true;
                        $this->browser->version = new Version(['value' => '1.0']);
                    }

                    /* Version 1.5 */
                    if ($version == '28.0.1500.94' && preg_match('/Version\/1.5/u', $ua)) {
                        $this->browser->name = "Samsung Internet";
                        $this->browser->channel = null;
                        $this->browser->stock = true;
                        $this->browser->version = new Version(['value' => '1.5']);
                    }

                    /* Version 1.6 */
                    if ($version == '28.0.1500.94' && preg_match('/Version\/1.6/u', $ua)) {
                        $this->browser->name = "Samsung Internet";
                        $this->browser->channel = null;
                        $this->browser->stock = true;
                        $this->browser->version = new Version(['value' => '1.6']);
                    }

                    /* Version 2.0 */
                    if ($version == '34.0.1847.76' && preg_match('/Version\/2.0/u', $ua)) {
                        $this->browser->name = "Samsung Internet";
                        $this->browser->channel = null;
                        $this->browser->stock = true;
                        $this->browser->version = new Version(['value' => '2.0']);
                    }

                    /* Version 2.1 */
                    if ($version == '34.0.1847.76' && preg_match('/Version\/2.1/u', $ua)) {
                        $this->browser->name = "Samsung Internet";
                        $this->browser->channel = null;
                        $this->browser->stock = true;
                        $this->browser->version = new Version(['value' => '2.1']);
                    }
                }

                /* Samsung Chromium based browsers */
                if (preg_match('/SamsungBrowser\/([0-9.]*)/u', $ua, $match)) {
                    $this->browser->name = "Samsung Internet";
                    $this->browser->channel = null;
                    $this->browser->stock = true;
                    $this->browser->version = new Version(['value' => $match[1]]);

                    if (str_contains($ua, 'Mobile VR')) {
                        $this->device->manufacturer = 'Samsung';
                        $this->device->model = 'Gear VR';
                        $this->device->type = Constants\DeviceType::HEADSET;
                    }
                }

                /* Oculus Chromium based browsers */
                if (preg_match('/OculusBrowser\/([0-9.]*)/u', $ua, $match)) {
                    $this->browser->name = "Oculus Browser";
                    $this->browser->channel = null;
                    $this->browser->stock = true;
                    $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);

                    if (str_contains($ua, 'Mobile VR')) {
                        $this->device->manufacturer = 'Samsung';
                        $this->device->model = 'Gear VR';
                        $this->device->type = Constants\DeviceType::HEADSET;
                    }

                    if (str_contains($ua, 'Pacific')) {
                        $this->device->manufacturer = 'Oculus';
                        $this->device->model = 'Go';
                        $this->device->type = Constants\DeviceType::HEADSET;
                    }
                }
            } elseif (isset($this->os->name) && $this->os->name == 'Linux' && preg_match('/SamsungBrowser\/([0-9.]*)/u', $ua, $match)) {
                $this->browser->name = "Samsung Internet";
                $this->browser->channel = null;
                $this->browser->stock = true;
                $this->browser->version = new Version(['value' => $match[1]]);

                $this->os->name = 'Android';
                $this->os->version = null;

                $this->device->manufacturer = 'Samsung';
                $this->device->model = 'DeX';
                $this->device->identifier = '';
                $this->device->identified |= Constants\Id::PATTERN;
                $this->device->type = Constants\DeviceType::DESKTOP;
            } else {
                if ($reduced) {
                    $this->browser->version->details = 1;
                } else {
                    $channel = Data\Chrome::getChannel('desktop', $version);

                    if ($channel == 'stable') {
                        if (explode('.', $version)[1] == '0') {
                            $this->browser->version->details = 1;
                        } else {
                            $this->browser->version->details = 2;
                        }
                    } elseif ($channel == 'beta') {
                        $this->browser->channel = 'Beta';
                    } else {
                        $this->browser->channel = 'Dev';
                    }
                }
            }

            if ($this->device->type == '') {
                $this->device->type = Constants\DeviceType::DESKTOP;
            }
        }

        /* Google Chromium */

        if (preg_match('/Chromium/u', $ua)) {
            $this->browser->stock = false;
            $this->browser->channel = '';
            $this->browser->name = 'Chromium';
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/Chromium\/([0-9.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1]]);
            }

            if ($this->device->type == '') {
                $this->device->type = Constants\DeviceType::DESKTOP;
            }
        }

        /* Chrome Content Shell */

        if (preg_match('/Chrome\/[0-9]+\.77\.34\.5/u', $ua)) {
            $this->browser->using = new Using(['name' => 'Chrome Content Shell']);

            $this->browser->type = Constants\BrowserType::UNKNOWN;
            $this->browser->stock = false;
            $this->browser->name = null;
            $this->browser->version = null;
            $this->browser->channel = null;
        }

        /* Chromium WebView by Amazon */

        if (preg_match('/AmazonWebAppPlatform\//u', $ua)) {
            $this->browser->using = new Using(['name' => 'Amazon WebView']);

            $this->browser->type = Constants\BrowserType::UNKNOWN;
            $this->browser->stock = false;
            $this->browser->name = null;
            $this->browser->version = null;
            $this->browser->channel = null;
        }

        /* Chromium WebView by Crosswalk */

        if (preg_match('/Crosswalk\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->using = new Using(['name' => 'Crosswalk WebView', 'version' => new Version(['value' => $match[1], 'details' => 1])]);

            $this->browser->type = Constants\BrowserType::UNKNOWN;
            $this->browser->stock = false;
            $this->browser->name = null;
            $this->browser->version = null;
            $this->browser->channel = null;
        }

        /* Set the browser family */

        if ($this->isBrowser('Chrome') || $this->isBrowser('Chromium')) {
            $this->browser->family = new Family([
                'name' => 'Chrome',
                'version' => !empty($this->browser->version) ? new Version(['value' => $this->browser->version->getMajor()]) : null
            ]);
        }
    }


    /* Internet Explorer */

    private function detectExplorer($ua)
    {
        if (preg_match('/\(IE ([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Internet Explorer';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        if (preg_match('/Browser\/IE([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Internet Explorer';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        if (preg_match('/MSIE/u', $ua)) {
            $this->browser->name = 'Internet Explorer';
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/IEMobile/u', $ua) || preg_match('/Windows CE/u', $ua) || preg_match('/Windows Phone/u', $ua) || preg_match('/WP7/u', $ua) || preg_match('/WPDesktop/u', $ua)) {
                $this->browser->name = 'Mobile Internet Explorer';

                if (isset($this->device->model) && ($this->device->model == 'Xbox 360' || $this->device->model == 'Xbox One' || $this->device->model == 'Xbox Series X')) {
                    $this->browser->name = 'Internet Explorer';
                }
            }

            if (preg_match('/MSIE ([0-9.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => preg_replace("/\.([0-9])([0-9])/", '.$1.$2', $match[1])]);
            }

            if (preg_match('/Mac_/u', $ua)) {
                $this->os->name = 'Mac OS';
                $this->engine->name = 'Tasman';
                $this->device->type = Constants\DeviceType::DESKTOP;

                if (!empty($this->browser->version)) {
                    if ($this->browser->version->is('>=', '5.1.1') && $this->browser->version->is('<=', '5.1.3')) {
                        $this->os->name = 'OS X';
                    }

                    if ($this->browser->version->is('>=', '5.2')) {
                        $this->os->name = 'OS X';
                    }
                }
            }
        }

        if (preg_match('/Trident\/[789][^\)]+; rv:([0-9.]*)\)/u', $ua, $match)) {
            $this->browser->name = 'Internet Explorer';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        if (preg_match('/Trident\/[789][^\)]+; Touch; rv:([0-9.]*);\s+IEMobile\//u', $ua, $match)) {
            $this->browser->name = 'Mobile Internet Explorer';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        if (preg_match('/Trident\/[789][^\)]+; Touch; rv:([0-9.]*); WPDesktop/u', $ua, $match)) {
            $this->browser->mode = 'desktop';
            $this->browser->name = 'Mobile Internet Explorer';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        /* Old versions of Pocket Internet Explorer */

        if ($this->isBrowser('Mobile Internet Explorer', '<', 6)) {
            $this->browser->name = 'Pocket Internet Explorer';
        }

        if (preg_match('/Microsoft Pocket Internet Explorer\//u', $ua)) {
            $this->browser->name = 'Pocket Internet Explorer';
            $this->browser->version = new Version(['value' => '1.0']);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->device->type = Constants\DeviceType::MOBILE;
        }

        if (preg_match('/MSPIE ([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Pocket Internet Explorer2';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->device->type = Constants\DeviceType::MOBILE;
        }

        /* Microsoft Mobile Explorer */

        if (preg_match('/MMEF([0-9])([0-9])/u', $ua, $match)) {
            $this->browser->name = 'Microsoft Mobile Explorer';
            $this->browser->version = new Version(['value' => $match[1] . '.' . $match[2]]);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->device->type = Constants\DeviceType::MOBILE;

            if (preg_match('/MMEF[0-9]+; ([^;]+); ([^\)\/]+)/u', $ua, $match)) {
                $device = Data\DeviceModels::identify('feature', $match[1] == 'CellPhone' ? $match[2] : $match[1] . ' ' . $match[2]);
                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }
        }

        /* Microsoft Open Live Writer */

        if (preg_match('/Open Live Writer ([0-9.]*)/u', $ua, $match)) {
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->stock = false;
            $this->browser->name = 'Open Live Writer';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->channel = null;

            if (preg_match('/MSIE ([0-9.]*)/u', $ua, $match)) {
                $this->browser->using = new Using(['name' => 'Internet Explorer', 'version' => new Version(['value' => $match[1]])]);
            }
        }

        /* Set the browser family */

        if ($this->isBrowser('Internet Explorer') || $this->isBrowser('Mobile Internet Explorer') || $this->isBrowser('Pocket Internet Explorer')) {
            unset($this->browser->family);
        }
    }


    /* Edge */

    private function detectEdge($ua)
    {
        if (preg_match('/Edge\/([0-9]+)/u', $ua, $match)) {
            $this->browser->name = 'Edge';
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->channel = '';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 1]);

            unset($this->browser->family);
        }

        if (preg_match('/Edg(iOS|A)\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Edge';
            $this->browser->version = new Version(['value' => $match[2], 'details' => 1, 'hidden' => true]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        if (preg_match('/Edg\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Edge';
            $this->browser->channel = '';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 1]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }
    }


    /* Opera */

    private function detectOpera($ua)
    {
        if (!preg_match('/(OPR|OMI|Opera|OPiOS|OPT|Coast|Oupeng|OPRGX|MMS)/ui', $ua)) {
            return;
        }

        if (preg_match('/OPR\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->stock = false;
            $this->browser->channel = '';
            $this->browser->name = 'Opera';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/Edition Developer/iu', $ua)) {
                $this->browser->channel = 'Developer';
            }

            if (preg_match('/Edition Next/iu', $ua)) {
                $this->browser->channel = 'Next';
            }

            if (preg_match('/Edition Beta/iu', $ua)) {
                $this->browser->channel = 'Beta';
            }

            if ($this->device->type == Constants\DeviceType::MOBILE) {
                $this->browser->name = 'Opera Mobile';
            }
        }

        if (preg_match('/OMI\/([0-9]+\.[0-9]+)/u', $ua, $match)) {
            $this->browser->name = 'Opera Devices';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            $this->device->type = Constants\DeviceType::TELEVISION;

            if (!$this->isOs('Android')) {
                unset($this->os->name);
                unset($this->os->version);
            }
        }

        if ((preg_match('/Opera[\/\-\s]/iu', $ua) || preg_match('/Browser\/Opera/iu', $ua)) && !preg_match('/Opera Software/iu', $ua)) {
            $this->browser->stock = false;
            $this->browser->name = 'Opera';
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/Opera[\/| ]?([0-9.]+)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1]]);
            }

            if (preg_match('/Version\/([0-9.]+)/u', $ua, $match)) {
                if (floatval($match[1]) >= 10) {
                    $this->browser->version = new Version(['value' => $match[1]]);
                }
            }

            if (isset($this->browser->version) && preg_match('/Edition Labs/u', $ua)) {
                $this->browser->channel = 'Labs';
            }

            if (isset($this->browser->version) && preg_match('/Edition Next/u', $ua)) {
                $this->browser->channel = 'Next';
            }

            if (preg_match('/Opera Tablet/u', $ua)) {
                $this->browser->name = 'Opera Mobile';
                $this->device->type = Constants\DeviceType::TABLET;
            }

            if (preg_match('/Opera Mobi/u', $ua)) {
                $this->browser->name = 'Opera Mobile';
                $this->device->type = Constants\DeviceType::MOBILE;
            }

            if (preg_match('/Opera Mini;/u', $ua)) {
                $this->browser->name = 'Opera Mini';
                $this->browser->version = null;
                $this->browser->mode = 'proxy';
                $this->device->type = Constants\DeviceType::MOBILE;
            }

            if (preg_match('/Opera Mini\/(?:att\/)?([0-9.]+)/u', $ua, $match)) {
                $this->browser->name = 'Opera Mini';
                $this->browser->version = new Version(['value' => $match[1], 'details' => (intval(substr(strrchr($match[1], '.'), 1)) > 99 ? -1 : null)]);
                $this->browser->mode = 'proxy';
                $this->device->type = Constants\DeviceType::MOBILE;
            }

            if ($this->browser->name == 'Opera' && $this->device->type == Constants\DeviceType::MOBILE) {
                $this->browser->name = 'Opera Mobile';
            }

            if (preg_match('/InettvBrowser/u', $ua)) {
                $this->device->type = Constants\DeviceType::TELEVISION;
            }

            if (preg_match('/Opera[ -]TV/u', $ua)) {
                $this->browser->name = 'Opera';
                $this->device->type = Constants\DeviceType::TELEVISION;
            }

            if (preg_match('/Linux zbov/u', $ua)) {
                $this->browser->name = 'Opera Mobile';
                $this->browser->mode = 'desktop';

                $this->device->type = Constants\DeviceType::MOBILE;

                $this->os->name = null;
                $this->os->version = null;
            }

            if (preg_match('/Linux zvav/u', $ua)) {
                $this->browser->name = 'Opera Mini';
                $this->browser->version = null;
                $this->browser->mode = 'desktop';

                $this->device->type = Constants\DeviceType::MOBILE;

                $this->os->name = null;
                $this->os->version = null;
            }

            if ($this->device->type == '') {
                $this->device->type = Constants\DeviceType::DESKTOP;
            }

            if (isset($this->browser->family)) {
                unset($this->browser->family);
            }
        }

        if (preg_match('/OPiOS\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->stock = false;
            $this->browser->name = 'Opera Mini';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        if (preg_match('/OPT\/([0-9]\.[0-9.]+)?/u', $ua, $match)) {
            $this->browser->stock = false;
            $this->browser->name = 'Opera Touch';
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (isset($match[1])) {
                $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            }
        }

        if (preg_match('/Coast\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->stock = false;
            $this->browser->name = 'Coast by Opera';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 3]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        if (preg_match('/Oupeng(?:HD)?[\/-]([0-9.]*)/u', $ua, $match)) {
            $this->browser->stock = false;
            $this->browser->name = 'Opera Oupeng';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        if (preg_match('/\sMMS\/([0-9.]*)$/u', $ua, $match)) {
            $this->browser->stock = false;
            $this->browser->name = 'Opera Neon';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        if (preg_match('/OPRGX\/([0-9.]*)$/u', $ua, $match)) {
            $this->browser->stock = false;
            $this->browser->name = 'Opera GX';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }
    }


    /* Firefox */

    private function detectFirefox($ua)
    {
        if (!preg_match('/(Firefox|Lorentz|GranParadiso|Namoroka|Shiretoko|Minefield|BonEcho|Fennec|Phoenix|Firebird|Minimo|FxiOS|Focus)/ui', $ua)) {
            return;
        }

        if (preg_match('/Firefox/u', $ua)) {
            $this->browser->stock = false;
            $this->browser->name = 'Firefox';
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/Firefox\/([0-9ab.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1]]);

                if (preg_match('/a/u', $match[1])) {
                    $this->browser->channel = 'Aurora';
                }

                if (preg_match('/b/u', $match[1])) {
                    $this->browser->channel = 'Beta';
                }
            }

            if (preg_match('/Aurora\/([0-9ab.]*)/u', $ua, $match)) {
                $this->browser->channel = 'Aurora';
                $this->browser->version = new Version(['value' => $match[1]]);
            }

            if (preg_match('/Fennec/u', $ua)) {
                $this->device->type = Constants\DeviceType::MOBILE;
            }

            if (preg_match('/Mobile;(?: ([^;]+);)? rv/u', $ua, $match)) {
                $this->device->type = Constants\DeviceType::MOBILE;

                if (isset($match[1])) {
                    $device = Data\DeviceModels::identify('firefoxos', $match[1]);
                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;

                        if (!$this->isOs('KaiOS')) {
                            $this->os->reset(['name' => 'Firefox OS']);
                        }
                    }
                }
            }

            if (preg_match('/Tablet;(?: ([^;]+);)? rv/u', $ua, $match)) {
                $this->device->type = Constants\DeviceType::TABLET;
            }

            if (preg_match('/Viera;(?: ([^;]+);)? rv/u', $ua, $match)) {
                $this->device->type = Constants\DeviceType::TELEVISION;
                $this->os->reset(['name' => 'Firefox OS']);
            }

            if ($this->device->type == Constants\DeviceType::MOBILE || $this->device->type == Constants\DeviceType::TABLET) {
                $this->browser->name = 'Firefox Mobile';
            }

            if ($this->device->type == '') {
                $this->device->type = Constants\DeviceType::DESKTOP;
            }
        }

        if (preg_match('/(Lorentz|GranParadiso|Namoroka|Shiretoko|Minefield|BonEcho)/u', $ua, $match)) {
            $this->browser->stock = false;
            $this->browser->name = 'Firefox';
            $this->browser->channel = str_replace('GranParadiso', 'Gran Paradiso', $match[1]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/' . $match[1] . '\/([0-9ab.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1]]);
            }
        }

        if (preg_match('/Fennec/u', $ua)) {
            $this->browser->stock = false;
            $this->browser->name = 'Firefox Mobile';
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/Fennec\/([0-9ab.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1]]);
            }

            $this->browser->channel = 'Fennec';
        }

        if (preg_match('/(Phoenix|Firebird|Minimo)/u', $ua, $match)) {
            $this->browser->stock = false;
            $this->browser->name = $match[1];
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/' . $match[1] . '\/([0-9ab.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1]]);
            }
        }

        if (preg_match('/FxiOS\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Firefox';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        if (preg_match('/Focus\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Firefox Focus';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        if (preg_match('/Servo\/1.0 Firefox\//u', $ua)) {
            $this->browser->name = 'Servo Nightly Build';
            $this->browser->version = null;
        }


        /* Set the browser family */

        if ($this->isBrowser('Firefox') || $this->isBrowser('Firefox Mobile') || $this->isBrowser('Firebird')) {
            $this->browser->family = new Family(['name' => 'Firefox', 'version' => $this->browser->version]);
        }

        if ($this->isBrowser('Minimo')) {
            $this->browser->family = new Family(['name' => 'Firefox']);
        }
    }


    /* Seamonkey */

    private function detectSeamonkey($ua)
    {
        if (preg_match('/SeaMonkey/u', $ua)) {
            $this->browser->stock = false;
            $this->browser->name = 'SeaMonkey';
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/SeaMonkey\/([0-9ab.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1]]);
            }
        }

        if (preg_match('/PmWFx\/([0-9ab.]*)/u', $ua, $match)) {
            $this->browser->stock = false;
            $this->browser->name = 'SeaMonkey';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }
    }


    /* Netscape */

    private function detectLegacyNetscape($ua)
    {
        if ($this->device->type == Constants\DeviceType::DESKTOP && $this->browser->getName() == '') {
            if (!preg_match('/compatible;/u', $ua)) {
                if (preg_match('/Mozilla\/([123].[0-9]+)/u', $ua, $match)) {
                    $this->browser->name = 'Netscape Navigator';
                    $this->browser->version = new Version(['value' => preg_replace("/([0-9])([0-9])/", '$1.$2', $match[1])]);
                    $this->browser->type = Constants\BrowserType::BROWSER;
                }

                if (preg_match('/Mozilla\/(4.[0-9]+)/u', $ua, $match)) {
                    $this->browser->name = 'Netscape Communicator';
                    $this->browser->version = new Version(['value' => preg_replace("/([0-9])([0-9])/", '$1.$2', $match[1])]);
                    $this->browser->type = Constants\BrowserType::BROWSER;

                    if (preg_match('/Nav\)/u', $ua)) {
                        $this->browser->name = 'Netscape Navigator';
                    }
                }
            }
        }
    }

    private function detectModernNetscape($ua)
    {
        if (preg_match('/Netscape/u', $ua)) {
            $this->browser->stock = false;
            $this->browser->name = 'Netscape';
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/Netscape[0-9]?\/([0-9.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1]]);
            }
        }

        if (preg_match('/ Navigator\/(9\.[0-9.]*)/u', $ua, $match)) {
            $this->browser->stock = false;
            $this->browser->name = 'Netscape Navigator';
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->version = new Version(['value' => $match[1], 'details' => 3]);
        }
    }


    /* Mosaic */

    private function detectMosaic($ua)
    {
        if (!preg_match('/Mosaic/ui', $ua)) {
            return;
        }

        if (preg_match('/(?:NCSA[ _])?Mosaic(?:\(tm\))?(?: for the X Window System| for Windows)?\/(?:Version )?([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'NCSA Mosaic';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->family = new Family(['name' => 'Mosaic']);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->stock = false;
        }

        if (preg_match('/AIR_Mosaic(?:\(16bit\))?\/v([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'AIR Mosaic';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->family = new Family(['name' => 'Mosaic']);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->stock = false;
        }

        if (preg_match('/(?:MosaicView|Spyglass[ _]Mosaic)\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Spyglass Mosaic';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->family = new Family(['name' => 'Mosaic']);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->stock = false;
        }

        if (preg_match('/SPRY_Mosaic(?:\(16bit\))?\/v([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'SPRY Mosaic';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->family = new Family(['name' => 'Mosaic']);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->stock = false;
        }

        if (preg_match('/DCL SuperMosaic\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'SuperMosaic';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->family = new Family(['name' => 'Mosaic']);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->stock = false;
        }

        if (preg_match('/VMS_Mosaic\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'VMS Mosaic';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->family = new Family(['name' => 'Mosaic']);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->stock = false;
        }

        if (preg_match('/mMosaic\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'mMosaic';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->family = new Family(['name' => 'Mosaic']);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->stock = false;
        }

        if (preg_match('/Quarterdeck Mosaic Version ([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Quarterdeck Mosaic';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->family = new Family(['name' => 'Mosaic']);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->stock = false;
        }

        if (preg_match('/WinMosaic\/Version ([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'WinMosaic';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->family = new Family(['name' => 'Mosaic']);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->stock = false;
        }

        if (preg_match('/Device Mosaic ([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Device Mosaic';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->family = new Family(['name' => 'Mosaic']);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->stock = false;

            $this->device->type = Constants\DeviceType::TELEVISION;
        }
    }


    /* UC Browser */

    private function detectUC($ua)
    {
        if (!preg_match('/(UC|UBrowser)/ui', $ua)) {
            return;
        }

        if (preg_match('/UCWEB/u', $ua)) {
            $this->browser->stock = false;
            $this->browser->name = 'UC Browser';
            $this->browser->type = Constants\BrowserType::BROWSER;

            unset($this->browser->channel);

            if (preg_match('/UCWEB\/?([0-9]*[.][0-9]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1], 'details' => 3]);
            }

            if (!$this->device->type) {
                $this->device->type = Constants\DeviceType::MOBILE;
            }

            if (isset($this->os->name) && $this->os->name == 'Linux') {
                $this->os->reset();
            }

            if (preg_match('/^IUC ?\(U; ?iOS ([0-9\._]+);/u', $ua, $match)) {
                $this->os->name = 'iOS';
                $this->os->version = new Version(['value' => str_replace('_', '.', $match[1])]);
            }

            if (preg_match('/^JUC ?\(Linux; ?U; ?(?:Android)? ?([0-9\.]+)[^;]*; ?[^;]+; ?([^;]*[^\s])\s*; ?[0-9]+\*[0-9]+;?\)/u', $ua, $match)) {
                $this->os->name = 'Android';
                $this->os->version = new Version(['value' => $match[1]]);

                $this->device = Data\DeviceModels::identify('android', $match[2]);
            }

            if (preg_match('/\(MIDP-2.0; U; [^;]+; ([^;]*[^\s])\)/u', $ua, $match)) {
                $this->os->name = 'Android';

                $this->device->model = $match[1];
                $this->device->identified |= Constants\Id::PATTERN;

                $device = Data\DeviceModels::identify('android', $match[1]);

                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }

            if (preg_match('/\((?:Linux|MIDP-2.0); U; Adr ([0-9\.]+)(?:-update[0-9])?; [^;]+; ([^;]*[^\s])\)/u', $ua, $match)) {
                $this->os->name = 'Android';
                $this->os->version = new Version(['value' => $match[1]]);

                $this->device->model = $match[2];
                $this->device->identified |= Constants\Id::PATTERN;

                $device = Data\DeviceModels::identify('android', $match[2]);

                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }

            if (preg_match('/\((?:iOS|iPhone);/u', $ua)) {
                $this->os->name = 'iOS';
                $this->os->version = new Version(['value' => '1.0']);

                if (preg_match('/OS[_ ]([0-9_]*);/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => str_replace('_', '.', $match[1])]);
                }

                if (preg_match('/; ([^;]+)\)/u', $ua, $match)) {
                    $device = Data\DeviceModels::identify('ios', $match[1]);

                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }
                }
            }

            if (preg_match('/\(Symbian;/u', $ua)) {
                $this->os->name = 'Series60';
                $this->os->version = null;
                $this->os->family = new Family(['name' => 'Symbian']);

                if (preg_match('/S60 V([0-9])/u', $ua, $match)) {
                    $this->os->version = new Version(['value' => $match[1]]);
                }

                if (preg_match('/; Nokia([^;]+)\)/iu', $ua, $match)) {
                    $this->device->model = $match[1];
                    $this->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('symbian', $match[1]);

                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }
                }
            }

            if (preg_match('/\(Windows;/u', $ua)) {
                $this->os->name = 'Windows Phone';
                $this->os->version = null;

                if (preg_match('/wds ([0-9]+\.[0-9])/u', $ua, $match)) {
                    switch ($match[1]) {
                        case '7.1':
                            $this->os->version = new Version(['value' => '7.5']);
                            break;
                        case '8.0':
                            $this->os->version = new Version(['value' => '8.0']);
                            break;
                        case '8.1':
                            $this->os->version = new Version(['value' => '8.1']);
                            break;
                        case '10.0':
                            $this->os->version = new Version(['value' => '10.0']);
                            break;
                    }
                }

                if (preg_match('/; ([^;]+); ([^;]+)\)/u', $ua, $match)) {
                    $this->device->manufacturer = $match[1];
                    $this->device->model = $match[2];
                    $this->device->identified |= Constants\Id::PATTERN;

                    $device = Data\DeviceModels::identify('wp', $match[2]);

                    if ($device->identified) {
                        $device->identified |= $this->device->identified;
                        $this->device = $device;
                    }
                }
            }
        }

        if (preg_match('/Ucweb\/([0-9]*[.][0-9]*)/u', $ua, $match)) {
            $this->browser->stock = false;
            $this->browser->name = 'UC Browser';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 3]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        if (preg_match('/ucweb-squid/u', $ua)) {
            $this->browser->stock = false;
            $this->browser->name = 'UC Browser';
            $this->browser->type = Constants\BrowserType::BROWSER;

            unset($this->browser->channel);
        }

        if (preg_match('/\) ?UC /u', $ua)) {
            $this->browser->stock = false;
            $this->browser->name = 'UC Browser';
            $this->browser->type = Constants\BrowserType::BROWSER;

            unset($this->browser->version);
            unset($this->browser->channel);
            unset($this->browser->mode);

            if ($this->device->type == Constants\DeviceType::DESKTOP) {
                $this->device->type = Constants\DeviceType::MOBILE;
                $this->browser->mode = 'desktop';
            }
        }

        if (preg_match('/UC ?Browser\/?([0-9.]*)/u', $ua, $match)) {
            $this->browser->stock = false;
            $this->browser->name = 'UC Browser';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            unset($this->browser->channel);

            if (!$this->device->type) {
                $this->device->type = Constants\DeviceType::MOBILE;
            }
        }

        if (preg_match('/UBrowser\/?([0-9.]*)/u', $ua, $match)) {
            $this->browser->stock = false;
            $this->browser->name = 'UC Browser';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            unset($this->browser->channel);
        }

        if (preg_match('/UCLite\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->stock = false;
            $this->browser->name = 'UC Browser';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            unset($this->browser->channel);
        }

        /* U2 is the Proxy service used by UC Browser on low-end phones */
        if (preg_match('/U2\//u', $ua)) {
            $this->browser->stock = false;
            $this->browser->name = 'UC Browser';
            $this->browser->mode = 'proxy';

            $this->engine->name = 'Gecko';

            /* UC Browser running on Windows 8 is identifing itself as U2, but instead its a Trident Webview */
            if (isset($this->os->name) && isset($this->os->version)) {
                if ($this->os->name == 'Windows Phone' && $this->os->version->toFloat() >= 8) {
                    $this->engine->name = 'Trident';
                    $this->browser->mode = '';
                }
            }

            if ($this->device->identified < Constants\Id::MATCH_UA && preg_match('/; ([^;]*)\) U2\//u', $ua, $match)) {
                $device = Data\DeviceModels::identify('android', $match[1]);
                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;

                    if (!isset($this->os->name) || ($this->os->name != 'Android' && (!isset($this->os->family) || $this->os->family->getName() != 'Android'))) {
                        $this->os->name = 'Android';
                    }
                }
            }
        }

        /* U3 is the Webkit based Webview used on Android phones */
        if (preg_match('/U3\//u', $ua)) {
            $this->engine->name = 'Webkit';
        }
    }

    private function detectUCEngine($ua)
    {
        if (isset($this->browser->name)) {
            if ($this->browser->name == 'UC Browser') {
                if (!preg_match("/UBrowser\//", $ua) && ($this->device->type == 'desktop' || (isset($this->os->name) && ($this->os->name == 'Windows' || $this->os->name == 'OS X')))) {
                    $this->device->type = Constants\DeviceType::MOBILE;
                    $this->browser->mode = 'desktop';
                    $this->engine->reset();
                    $this->os->reset();
                } elseif (!isset($this->os->name) || ($this->os->name != 'iOS' && $this->os->name != 'Windows Phone' && $this->os->name != 'Windows' && $this->os->name != 'Android' && (!isset($this->os->family) || $this->os->family->getName() != 'Android'))) {
                    $this->engine->name = 'Gecko';
                    unset($this->engine->version);
                    $this->browser->mode = 'proxy';
                }

                if (isset($this->engine->name) && $this->engine->name == 'Presto') {
                    $this->engine->name = 'Webkit';
                    unset($this->engine->version);
                }
            }
        }
    }


    /* Netfront */

    private function detectNetfront($ua)
    {
        if (!preg_match('/(CNF|NF|NetFront|NX|Ave|COM2)/ui', $ua)) {
            return;
        }

        /* Compact NetFront */

        if (preg_match('/CNF\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Compact NetFront';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->device->type = Constants\DeviceType::MOBILE;
        }

        /* NetFront */

        if (preg_match('/Net[fF]ront/u', $ua) && !preg_match('/NetFrontNX/u', $ua)) {
            $this->browser->name = 'NetFront';
            $this->browser->type = Constants\BrowserType::BROWSER;
            unset($this->browser->channel);

            if (preg_match('/NetFront[ \/]?([0-9.]*)/ui', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1]]);
            }

            /* Detect device type based on NetFront identifier */

            if (preg_match('/MobilePhone/u', $ua)) {
                $this->device->type = Constants\DeviceType::MOBILE;
            }

            if (preg_match('/DigitalMediaPlayer/u', $ua)) {
                $this->device->type = Constants\DeviceType::MEDIA;
            }

            if (preg_match('/PDA/u', $ua)) {
                $this->device->type = Constants\DeviceType::PDA;
            }

            if (preg_match('/MFP/u', $ua)) {
                $this->device->type = Constants\DeviceType::PRINTER;
            }

            if (preg_match('/(InettvBrowser|HbbTV|DTV|NetworkAVTV|BDPlayer)/u', $ua)) {
                $this->device->type = Constants\DeviceType::TELEVISION;
            }

            if (preg_match('/VCC/u', $ua)) {
                $this->device->type = Constants\DeviceType::CAR;
            }

            if (preg_match('/Kindle/u', $ua)) {
                $this->device->type = Constants\DeviceType::EREADER;
            }

            if (empty($this->device->type)) {
                $this->device->type = Constants\DeviceType::MOBILE;
            }

            /* Detect OS based on NetFront identifier */

            if (preg_match('/NF[0-9][0-9](?:WMPRO|PPC)\//ui', $ua, $match)) {
                if (!$this->isOs('Windows Mobile')) {
                    $this->os->reset([
                        'name' => 'Windows Mobile'
                    ]);
                }
            }
        }

        if (preg_match('/(?:Browser\/(?:NF|NetFr?ont-)|NF-Browser\/|ACS-NF\/|NetFront FullBrowser\/)([0-9.]*)/ui', $ua, $match)) {
            $this->browser->name = 'NetFront';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;
            unset($this->browser->channel);

            $this->device->type = Constants\DeviceType::MOBILE;
        }

        /* AVE-Front */

        if (preg_match('/(?:AVE-Front|AveFront)\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'NetFront';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/Category=([^\);]+)[\);]/u', $ua, $match)) {
                switch ($match[1]) {
                    case 'WebPhone':
                        $this->device->type = Constants\DeviceType::MOBILE;
                        $this->device->subtype = Constants\DeviceSubType::DESKTOP;
                        break;
                    case 'WP':
                    case 'Home Mail Tool':
                    case 'PDA':
                        $this->device->type = Constants\DeviceType::PDA;
                        break;
                    case 'STB':
                        $this->device->type = Constants\DeviceType::TELEVISION;
                        break;
                    case 'GAME':
                        $this->device->type = Constants\DeviceType::GAMING;
                        $this->device->subtype = Constants\DeviceSubType::CONSOLE;
                        break;
                }
            }

            if (preg_match('/Product=([^\);]+)[\);]/u', $ua, $match)) {
                if (in_array($match[1], ['ACCESS/NFPS', 'SUNSOFT/EnjoyMagic'])) {
                    $this->device->setIdentification([
                        'manufacturer' => 'Sony',
                        'model' => 'PlayStation 2',
                        'type' => Constants\DeviceType::GAMING,
                        'subtype' => Constants\DeviceSubType::CONSOLE
                    ]);
                }
            }
        }

        /* Netfront NX */

        if (preg_match('/NX[\/ ]([0-9.]+)/u', $ua, $match)) {
            $this->browser->name = 'NetFront NX';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::BROWSER;
            unset($this->browser->channel);

            if (empty($this->device->type) || $this->isType('desktop')) {
                if (preg_match('/(DTV|HbbTV)/iu', $ua)) {
                    $this->device->type = Constants\DeviceType::TELEVISION;
                } else {
                    $this->device->type = Constants\DeviceType::DESKTOP;
                }
            }

            $this->os->reset();
        }

        /* The Sony Mylo 2 identifies as Firefox 2, but is NetFront */

        if (preg_match('/Sony\/COM2/u', $ua, $match)) {
            $this->browser->reset([
                'name' => 'NetFront',
                'type' => Constants\BrowserType::BROWSER
            ]);
        }
    }


    /* Obigo */

    private function detectObigo($ua)
    {
        $processObigoVersion = function ($version) {
            $result = [
                'value' => $version
            ];

            if (preg_match('/[0-9.]+/', $version, $match)) {
                $result['details'] = 2;
            }

            if (preg_match('/([0-9])[A-Z]/', $version, $match)) {
                $result['value'] = intval($match[1]);
                $result['alias'] = $version;
            }

            return $result;
        };

        if (preg_match('/(?:Obigo|Teleca|AU-MIC|MIC\/)/ui', $ua)) {
            $this->browser->name = 'Obigo';
            $this->browser->version = null;
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/Obigo\/0?([0-9.]+)/iu', $ua, $match)) {
                $this->browser->version = new Version($processObigoVersion($match[1]));
            } elseif (preg_match('/(?:MIC|TelecaBrowser)\/(WAP|[A-Z])?0?([0-9.]+[A-Z]?)/iu', $ua, $match)) {
                $this->browser->version = new Version($processObigoVersion($match[2]));
                if (!empty($match[1])) {
                    $this->browser->name = 'Obigo ' . strtoupper($match[1]);
                }
            } elseif (preg_match('/(?:Obigo(?:InternetBrowser|[- ]Browser)?|Teleca)\/(WAP|[A-Z])?[0O]?([0-9.]+[A-Z]?)/ui', $ua, $match)) {
                $this->browser->version = new Version($processObigoVersion($match[2]));
                if (!empty($match[1])) {
                    $this->browser->name = 'Obigo ' . strtoupper($match[1]);
                }
            } elseif (preg_match('/(?:Obigo|Teleca)[- ]([WAP|[A-Z])?0?([0-9.]+[A-Z]?)(?:[0-9])?(?:[\/;]|$)/ui', $ua, $match)) {
                $this->browser->version = new Version($processObigoVersion($match[2]));
                if (!empty($match[1])) {
                    $this->browser->name = 'Obigo ' . strtoupper($match[1]);
                }
            } elseif (preg_match('/Browser\/(?:Obigo|Teleca)[_-]?(?:Browser\/)?(WAP|[A-Z])?0?([0-9.]+[A-Z]?)/ui', $ua, $match)) {
                $this->browser->version = new Version($processObigoVersion($match[2]));
                if (!empty($match[1])) {
                    $this->browser->name = 'Obigo ' . strtoupper($match[1]);
                }
            } elseif (preg_match('/Obigo Browser (WAP|[A-Z])?0?([0-9.]+[A-Z]?)/ui', $ua, $match)) {
                $this->browser->version = new Version($processObigoVersion($match[2]));
                if (!empty($match[1])) {
                    $this->browser->name = 'Obigo ' . strtoupper($match[1]);
                }
            }
        }

        if (preg_match('/[^A-Z](Q)0?([0-9][A-Z])/u', $ua, $match)) {
            $this->browser->name = 'Obigo ' . $match[1];
            $this->browser->version = new Version($processObigoVersion($match[2]));
            $this->browser->type = Constants\BrowserType::BROWSER;
        }
    }


    /* ANT Galio and ANT Fresco */

    private function detectAnt($ua)
    {
        if (preg_match('/ANTFresco\/([0-9.]+)/iu', $ua, $match)) {
            $this->browser->name = 'ANT Fresco';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        if (preg_match('/ANTGalio\/([0-9.]+)/iu', $ua, $match)) {
            $this->browser->name = 'ANT Galio';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 3]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }
    }


    /* Seraphic Sraf */

    private function detectSraf($ua)
    {
        if (preg_match('/sraf_tv_browser/u', $ua)) {
            $this->browser->name = 'Seraphic Sraf';
            $this->browser->version = null;
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->device->type = Constants\DeviceType::TELEVISION;
        }

        if (preg_match('/SRAF\/([0-9.]+)/iu', $ua, $match)) {
            $this->browser->name = 'Seraphic Sraf';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->device->type = Constants\DeviceType::TELEVISION;
        }
    }


    /* MachBlue */

    private function detectMachBlue($ua)
    {
        if (preg_match('/mbxtWebKit\/([0-9.]*)/u', $ua, $match)) {
            $this->os->name = '';
            $this->browser->name = 'MachBlue XT';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->device->type = Constants\DeviceType::TELEVISION;
        }

        if ($ua == 'MachBlue') {
            $this->os->name = '';
            $this->browser->name = 'MachBlue XT';
            $this->device->type = Constants\DeviceType::TELEVISION;
            $this->browser->type = Constants\BrowserType::BROWSER;
        }
    }


    /* Espial */

    private function detectEspial($ua)
    {
        if (preg_match('/Espial/u', $ua)) {
            $this->browser->name = 'Espial';
            $this->browser->channel = null;
            $this->browser->type = Constants\BrowserType::BROWSER;

            $this->os->name = '';
            $this->os->version = null;

            if ($this->device->type != Constants\DeviceType::TELEVISION) {
                $this->device->type = Constants\DeviceType::TELEVISION;
                $this->device->manufacturer = null;
                $this->device->model = null;
            }

            if (preg_match('/Espial(?: Browser|TVBrowser)?\/(?:sig)?([0-9.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1]]);
            }

            if (preg_match('/;(L6200|L7200)/u', $ua, $match)) {
                $this->device->manufacturer = 'Toshiba';
                $this->device->model = 'Regza ' . $match[1];
                $this->device->series = 'Smart TV';
                $this->device->identified |= Constants\Id::MATCH_UA;
                $this->device->generic = false;
            }
        }
    }


    /* Iris */

    private function detectIris($ua)
    {
        if (preg_match('/Iris\//u', $ua)) {
            $this->browser->name = 'Iris';
            $this->browser->hidden = false;
            $this->browser->stock = false;
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/Iris\/([0-9.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1]]);
            }

            if (preg_match('/ WM([0-9]) /u', $ua, $match)) {
                $this->device->reset();
                $this->device->type = Constants\DeviceType::MOBILE;

                $this->os->reset();
                $this->os->name = 'Windows Mobile';
                $this->os->version = new Version(['value' => $match[1] . '.0']);
            }

            if (preg_match('/Windows NT/u', $ua, $match)) {
                $this->browser->mode = 'desktop';

                $this->device->reset();
                $this->device->type = Constants\DeviceType::MOBILE;

                $this->os->reset();
                $this->os->name = 'Windows Mobile';
            }
        }
    }


    /* Dolfin */

    private function detectDolfin($ua)
    {
        if (preg_match('/(Dolfin|Jasmine)/u', $ua)) {
            $this->browser->name = 'Dolfin';
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/(?:Dolfin|Jasmine)\/([0-9.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1]]);
            }

            if (preg_match('/Browser\/Dolfin([0-9.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1]]);
            }
        }
    }


    /* WebOS */

    private function detectWebOSBrowser($ua)
    {
        if (preg_match('/wOSBrowser/u', $ua)) {
            $this->browser->name = 'webOS Browser';
            $this->browser->type = Constants\BrowserType::BROWSER;

            if ($this->os->name != 'webOS') {
                $this->os->name = 'webOS';
            }

            if (isset($this->device->manufacturer) && $this->device->manufacturer == 'Apple') {
                unset($this->device->manufacturer);
                unset($this->device->model);
                unset($this->device->identifier);
                $this->device->identified = Constants\Id::NONE;
            }
        }
    }


    /* Sailfish */

    private function detectSailfishBrowser($ua)
    {
        if (preg_match('/Sailfish ?Browser/u', $ua)) {
            $this->browser->name = 'Sailfish Browser';
            $this->browser->stock = true;
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/Sailfish ?Browser\/([0-9.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            }
        }
    }


    /* Silk */

    private function detectSilk($ua)
    {
        if (preg_match('/Silk/u', $ua)) {
            if (preg_match('/Silk-Accelerated/u', $ua) || !preg_match('/PlayStation/u', $ua)) {
                $this->browser->name = 'Silk';
                $this->browser->channel = null;
                $this->browser->type = Constants\BrowserType::BROWSER;

                if (preg_match('/Silk\/([0-9.]*)/u', $ua, $match)) {
                    $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
                }

                if (preg_match('/; ([^;]*[^;\s])\s+Build/u', $ua, $match)) {
                    $this->device = Data\DeviceModels::identify('android', $match[1]);
                }

                if (!$this->device->identified) {
                    $this->device->manufacturer = 'Amazon';
                    $this->device->model = 'Kindle Fire';
                    $this->device->type = Constants\DeviceType::TABLET;
                    $this->device->identified |= Constants\Id::INFER;

                    if (isset($this->os->name) && ($this->os->name != 'Android' || $this->os->name != 'FireOS')) {
                        $this->os->name = 'FireOS';
                        $this->os->family = new Family(['name' => 'Android']);
                        $this->os->alias = null;
                        $this->os->version = null;
                    }
                }
            }
        }
    }


    /* Nokia */

    private function detectNokiaBrowser($ua)
    {
        if (!preg_match('/(BrowserNG|Nokia|OSRE|Ovi|Maemo)/ui', $ua)) {
            return;
        }

        /* Nokia Browser */

        if (preg_match('/BrowserNG/u', $ua)) {
            $this->browser->name = 'Nokia Browser';
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/BrowserNG\/([0-9.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1], 'details' => 3, 'builds' => false]);
            }
        }

        if (preg_match('/NokiaBrowser/u', $ua)) {
            $this->browser->name = 'Nokia Browser';
            $this->browser->channel = null;
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/NokiaBrowser\/([0-9.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1], 'details' => 3]);
            }
        }

        if (preg_match('/Nokia-Communicator-WWW-Browser/u', $ua)) {
            $this->browser->name = 'Nokia Browser';
            $this->browser->channel = null;
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/Nokia-Communicator-WWW-Browser\/([0-9.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1], 'details' => 3]);
            }
        }


        /* Nokia Xpress for S30+, S40 and Windows Phone */

        if (preg_match('/OSRE/u', $ua)) {
            $this->browser->name = 'Nokia Xpress';
            $this->browser->mode = 'proxy';
            $this->browser->type = Constants\BrowserType::BROWSER;

            $this->device->type = Constants\DeviceType::MOBILE;

            $this->os->name = null;
            $this->os->version = null;
        }

        if (preg_match('/S40OviBrowser/u', $ua)) {
            $this->browser->name = 'Nokia Xpress';
            $this->browser->mode = 'proxy';
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/S40OviBrowser\/([0-9.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1], 'details' => 3]);
            }

            if (preg_match('/Nokia([^\/]+)\//u', $ua, $match)) {
                $this->device->manufacturer = 'Nokia';
                $this->device->model = $match[1];
                $this->device->identified |= Constants\Id::PATTERN;

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


                        if (preg_match('/java_runtime_version=Nokia_Asha_([0-9_]+);/u', $ua, $match)) {
                            $this->os->version = new Version(['value' => str_replace('_', '.', $match[1])]);
                        }
                    }
                }
            }

            if (preg_match('/NOKIALumia([0-9]+)/u', $ua, $match)) {
                $this->device->manufacturer = 'Nokia';
                $this->device->model = $match[1];
                $this->device->identified |= Constants\Id::PATTERN;

                $device = Data\DeviceModels::identify('wp', $this->device->model);
                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                    $this->os->name = 'Windows Phone';
                }
            }
        }


        /* MicroB - the default browser for maemo */

        if (preg_match('/Maemo[ |_]Browser/u', $ua)) {
            $this->browser->name = 'MicroB';
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/Maemo[ |_]Browser[ |_]([0-9.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1], 'details' => 3]);
            }
        }
    }


    /* Konqueror */

    private function detectKonqueror($ua)
    {
        if (preg_match('/[k|K]onqueror\//u', $ua)) {
            $this->browser->name = 'Konqueror';
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/[k|K]onqueror\/([0-9.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1]]);
            }

            if ($this->device->type == '') {
                $this->device->type = Constants\DeviceType::DESKTOP;
            }
        }
    }


    /* OmniWeb */

    private function detectOmniWeb($ua)
    {
        if (preg_match('/OmniWeb/u', $ua)) {
            $this->browser->name = 'OmniWeb';
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->version = null;

            if (preg_match('/OmniWeb\/v?([0-9])[0-9][0-9]/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1], 'details' => 1]);
            }

            if (preg_match('/OmniWeb\/([0-9]\.[0-9\.]+)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1], 'details' => 3]);
            }

            $this->device->reset([
                'type' => Constants\DeviceType::DESKTOP
            ]);

            if (!empty($this->browser->version)) {
                if ($this->browser->version->is('<', 3)) {
                    $this->os->name = 'NextStep';
                    $this->os->version = null;
                }

                if ($this->browser->version->is('>=', 4)) {
                    if (empty($this->os->name) || $this->os->name != 'OS X') {
                        $this->os->name = 'OS X';
                        $this->os->version = null;
                    }
                }
            }
        }
    }


    /* Other browsers */

    private function detectDesktopBrowsers($ua)
    {
        if (!preg_match('/(WebPositive|WebExplorer|WorldWideweb|Midori|Maxthon|Browse|Flow)/ui', $ua)) {
            return;
        }

        /* WebPositive */

        if (preg_match('/WebPositive/u', $ua, $match)) {
            $this->browser->name = 'WebPositive';
            $this->browser->channel = '';
            $this->browser->version = null;
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/WebPositive\/([0-9]\.[0-9.]+)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1], 'details' => 3]);
            }
        }

        /* IBM WebExplorer */

        if (preg_match('/IBM[- ]WebExplorer[ -]?(DLL ?|Window API ?)?/u', $ua)) {
            $this->browser->name = 'IBM WebExplorer';
            $this->browser->channel = '';
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/IBM[- ]WebExplorer[ -]?(?:DLL ?|Window API ?)?\/v([0-9]\.[0-9\.]+)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1]]);
            }

            $this->os->name = 'OS/2';
            $this->device->type = 'desktop';
        }

        /* WorldWideweb */

        if (preg_match('/WorldWideweb \(NEXT\)/u', $ua, $match)) {
            $this->browser->name = 'WorldWideWeb';
            $this->browser->channel = '';
            $this->browser->version = null;
            $this->browser->type = Constants\BrowserType::BROWSER;

            $this->os->name = 'NextStep';
            $this->device->type = 'desktop';
        }

        /* Midori */

        if (preg_match('/Midori\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Midori';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            $this->device->manufacturer = null;
            $this->device->model = null;
            $this->device->type = Constants\DeviceType::DESKTOP;

            if (isset($this->os->name) && $this->os->name == 'OS X') {
                $this->os->name = null;
                $this->os->version = null;
            }
        }

        if (preg_match('/midori(?:\/[0-9.]*)?$/u', $ua)) {
            $this->browser->name = 'Midori';
            $this->browser->type = Constants\BrowserType::BROWSER;

            $this->device->type = Constants\DeviceType::DESKTOP;

            if (preg_match('/midori\/([0-9.]*)$/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1]]);
            }
        }

        /* Maxthon */

        if (preg_match('/Maxthon/iu', $ua, $match)) {
            $this->browser->name = 'Maxthon';
            $this->browser->channel = '';
            $this->browser->version = null;
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/Maxthon[\/\' ]\(?([0-9.]*)\)?/iu', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1], 'details' => 3]);
            }

            if (isset($this->os->name) && $this->browser->version && $this->os->name == 'Windows' && $this->browser->version->toFloat() < 4) {
                $this->browser->version->details = 1;
            }
        }

        /* Browse for Remix OS */

        if (preg_match('/^Browse\/([0-9.]+)/u', $ua, $match)) {
            $this->browser->name = 'Browse';
            $this->browser->channel = '';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        /* Browse for Flow */

        if (preg_match('/ Flow\/([0-9.]+)/u', $ua, $match)) {
            $this->browser->name = 'Flow';
            $this->browser->channel = '';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;
            unset($this->browser->family);

            if (preg_match('/EkiohFlow\/[0-9\.]+M/u', $ua)) {
                $this->browser->name = 'Flow Nightly Build';
                $this->browser->version = null;
            }
        }
    }

    private function detectMobileBrowsers($ua)
    {
        if (!preg_match('/(Huawei|Ninesky|Skyfire|Dolphin|QQ|360|QHBrowser|Mercury|iBrowser|Puffin|MiniB|MxNitro|Sogou|Xiino|Palmscape|WebPro|Vision|MiuiBrowser)/ui', $ua)) {
            return;
        }

        /* Huawei Browser */

        if (preg_match('/HuaweiBrowser\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Huawei Browser';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        /* Xiaomi MIUI Browser */

        if (preg_match('/MiuiBrowser\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'MIUI Browser';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (!$this->os->isFamily('Android')) {
                $this->os->reset();
                $this->os->name = 'Android';

                $this->device->manufacturer = 'Xiaomi';
                $this->device->model = null;
                $this->device->type = Constants\DeviceType::MOBILE;
            }
        }

        /* NineSky */

        if (preg_match('/Ninesky(?:-android-mobile(?:-cn)?)?\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->reset();
            $this->browser->name = 'NineSky';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (isset($this->device->manufacturer) && $this->device->manufacturer == 'Apple') {
                $this->device->reset();
            }

            if (!$this->os->isFamily('Android')) {
                $this->os->reset();
                $this->os->name = 'Android';
            }

            $this->device->type = Constants\DeviceType::MOBILE;
        }

        /* Skyfire */

        if (preg_match('/Skyfire\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Skyfire';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            $this->device->type = Constants\DeviceType::MOBILE;

            $this->os->name = 'Android';
            $this->os->version = null;
        }

        /* Dolphin HD */

        if (preg_match('/Dolphin(?:HD|Browser)?(?:INT|CN)?\/(?:INT|CN)?-?([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Dolphin';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            $this->device->type = Constants\DeviceType::MOBILE;
        }

        /* QQ Browser */

        if (preg_match('/(M?QQBrowser)\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'QQ Browser';

            $version = $match[2];
            if (preg_match('/^[0-9][0-9]$/u', $version)) {
                $version = $version[0] . '.' . $version[1];
            }

            $this->browser->version = new Version(['value' => $version, 'details' => 2]);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->channel = '';

            if (!isset($this->os->name) && $match[1] == 'QQBrowser') {
                $this->os->name = 'Windows';
            }

            if (preg_match('/MQQBrowser\/[0-9\.]+\/Adr \(Linux; U; ([0-9\.]+); [^;]+; (.+) Build/u', $ua, $match)) {
                $this->os->reset([
                    'name' => 'Android',
                    'version' => new Version(['value' => $match[1]])
                ]);

                $this->device->type = Constants\DeviceType::MOBILE;
                $this->device->model = $match[2];
                $this->device->identified |= Constants\Id::PATTERN;

                $device = Data\DeviceModels::identify('android', $match[2]);
                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }

            if (preg_match('/MQQBrowser\/[0-9\.]+\/WP7 \([^;]+;WPOS:([0-9]\.[0-9])[0-9\.]*;([^;]+); ([^\)]+)\)/u', $ua, $match)) {
                $this->os->reset([
                    'name' => 'Windows Phone',
                    'version' => new Version(['value' => $match[1]])
                ]);

                $this->device->type = Constants\DeviceType::MOBILE;
                $this->device->manufacturer = $match[2];
                $this->device->model = $match[3];
                $this->device->identified |= Constants\Id::PATTERN;

                $device = Data\DeviceModels::identify('wp', $match[3]);
                if ($device->identified) {
                    $device->identified |= $this->device->identified;
                    $this->device = $device;
                }
            }
        }

        if (preg_match('/MQQBrowser\/Mini([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'QQ Browser Mini';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->channel = '';
        }

        if (preg_match('/QQ\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'QQ Browser';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->channel = '';
        }

        /* 360 Phone Browser */

        if (preg_match('/360 (?:Aphone|Android Phone) Browser/u', $ua, $match)) {
            $this->browser->name = 'Qihoo 360 Browser';
            $this->browser->family = null;
            $this->browser->channel = '';
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        if (preg_match('/360 (?:Aphone|Android Phone) Browser \((?:Version |V)?([0-9.]*)(?:beta)?\)/u', $ua, $match)) {
            $this->browser->name = 'Qihoo 360 Browser';
            $this->browser->family = null;
            $this->browser->channel = '';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (!$this->os->isFamily('Android')) {
                $this->device->type = Constants\DeviceType::MOBILE;
                $this->os->reset([
                    'name' => 'Android'
                ]);
            }
        }

        if (preg_match('/360%20(?:Browser|Lite)\/([0-9\.]+)/u', $ua, $match)) {
            $this->browser->name = 'Qihoo 360 Browser';
            $this->browser->family = null;
            $this->browser->channel = '';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        if (preg_match('/QHBrowser\/([0-9\.]+)/u', $ua, $match)) {
            $version = $match[1];
            if (preg_match('/^[0-9][0-9][0-9]$/u', $version)) {
                $version = $version[0] . '.' . $version[1] . '.' . $version[2];
            }

            $this->browser->name = 'Qihoo 360 Browser';
            $this->browser->channel = '';
            $this->browser->version = new Version(['value' => $version]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (!$this->isOs('iOS')) {
                $this->device->type = Constants\DeviceType::MOBILE;
                $this->os->reset([
                    'name' => 'iOS'
                ]);
            }
        }

        /* Mercury */

        if (preg_match('/(?:^| )Mercury\/([0-9\.]+)/u', $ua, $match)) {
            $version = $match[1];
            if (preg_match('/^[0-9][0-9][0-9]$/u', $version)) {
                $version = $version[0] . '.' . $version[1] . '.' . $version[2];
            }

            $this->browser->name = 'Mercury Browser';
            $this->browser->channel = '';
            $this->browser->version = new Version(['value' => $version]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        /* iBrowser */

        if (preg_match('/(?:^| )iBrowser\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'iBrowser';

            $version = $match[1];
            if (preg_match('/^[0-9][0-9]$/u', $version)) {
                $version = $version[0] . '.' . $version[1];
            }

            $this->browser->version = new Version(['value' => $version, 'details' => 2]);
            $this->browser->channel = '';
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        if (preg_match('/iBrowser\/Mini([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'iBrowser Mini';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->channel = '';
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        /* Puffin */

        if (preg_match('/Puffin\/([0-9.]+)([IA])?([PT])?/u', $ua, $match)) {
            $this->browser->name = 'Puffin';
            $this->browser->version = new Version(['value' => $match[1], 'details' => (intval(substr(strrchr($match[1], '.'), 1)) > 99 ? -1 : null)]);
            $this->browser->mode = 'proxy';
            $this->browser->channel = '';
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (isset($match[2])) {
                switch ($match[2]) {
                    case 'A':
                        if (!$this->isOs('Android')) {
                            $this->os->reset(['name' => 'Android']);
                        }
                        break;

                    case 'I':
                        if (!$this->isOs('iOS')) {
                            $this->os->reset(['name' => 'iOS']);
                        }
                        break;
                }
            }

            if (isset($match[3])) {
                switch ($match[3]) {
                    case 'P':
                        $this->device->type = Constants\DeviceType::MOBILE;
                        if ($this->os->name == 'iOS' && empty($this->device->model)) {
                            $this->device->manufacturer = 'Apple';
                            $this->device->model = 'iPhone';
                            $this->device->identified = Constants\Id::MATCH_UA;
                        }
                        break;

                    case 'T':
                        $this->device->type = Constants\DeviceType::TABLET;
                        if ($this->os->name == 'iOS' && empty($this->device->model)) {
                            $this->device->manufacturer = 'Apple';
                            $this->device->model = 'iPad';
                            $this->device->identified = Constants\Id::MATCH_UA;
                        }
                        break;
                }
            }
        }

        /* MiniBrowser Mobile */

        if (preg_match('/MiniBr?owserM(?:obile)?\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'MiniBrowser';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            $this->device->type = Constants\DeviceType::MOBILE;

            if (!$this->isOs('Series60')) {
                $this->os->name = 'Series60';
                $this->os->version = null;
            }
        }

        /* Maxthon */

        if (preg_match('/MxNitro/iu', $ua, $match)) {
            $this->browser->name = 'Maxthon Nitro';
            $this->browser->channel = '';
            $this->browser->version = null;
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/MxNitro\/([0-9.]*)/iu', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1], 'details' => 3]);
            }
        }

        /* Sogou Mobile */

        if (preg_match('/SogouAndroidBrowser\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Sogou Mobile';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (isset($this->device->manufacturer) && $this->device->manufacturer == 'Apple') {
                unset($this->device->manufacturer);
                unset($this->device->model);
                unset($this->device->identifier);
                $this->device->identified = Constants\Id::NONE;
            }
        }

        /* Xiino */

        if (preg_match('/Xiino\/([0-9.]+)/u', $ua, $match)) {
            $this->browser->name = 'Xiino';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            $this->device->type = Constants\DeviceType::PDA;

            $this->os->name = 'Palm OS';

            if (preg_match('/\(v. ([0-9.]+)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }
        }

        /* Palmscape */

        if (preg_match('/Palmscape\/(?:PR)?([0-9.]+)/u', $ua, $match)) {
            $this->browser->name = 'Palmscape';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            $this->device->type = Constants\DeviceType::PDA;

            $this->os->name = 'Palm OS';

            if (preg_match('/\(v. ([0-9.]+)/u', $ua, $match)) {
                $this->os->version = new Version(['value' => $match[1]]);
            }
        }

        /* Novarra WebPro */

        if (preg_match('/WebPro/u', $ua) && preg_match('/PalmOS/u', $ua)) {
            $this->browser->name = 'WebPro';
            $this->browser->version = null;
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/WebPro\/?([0-9.]*)/u', $ua, $match)) {
                $this->browser->version = new Version(['value' => $match[1]]);
            }
        }

        /* Novarra Vision */

        if (preg_match('/(?:Vision-Browser|Novarra-Vision)\/?([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Novarra Vision';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->family = null;
            $this->browser->mode = 'proxy';
            $this->browser->type = Constants\BrowserType::BROWSER;

            if ($this->device->type != Constants\DeviceType::MOBILE) {
                $this->os->reset();
                $this->device->type = Constants\DeviceType::MOBILE;
            }
        }
    }

    private function detectTelevisionBrowsers($ua)
    {
        if (!preg_match('/(Roku|LG Browser|NetCast|SonyBrowserCore|Dream|Planetweb)/ui', $ua)) {
            return;
        }

        /* Web on Roku */

        if (preg_match('/Roku/u', $ua) && preg_match('/Web\/([0-9.]+)/u', $ua, $match)) {
            $this->browser->name = 'Web';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::BROWSER;
        }

        /* LG Browser */

        if (preg_match('/LG Browser\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'LG Browser';
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->device->type = Constants\DeviceType::TELEVISION;
        }

        if (preg_match('/NetCast/u', $ua) && preg_match('/SmartTV\//u', $ua)) {
            unset($this->browser->name);
            unset($this->browser->version);
        }

        /* Sony Browser */

        if (preg_match('/SonyBrowserCore\/([0-9.]*)/u', $ua, $match)) {
            unset($this->browser->name);
            unset($this->browser->version);
            $this->device->type = Constants\DeviceType::TELEVISION;
        }

        /* Dreamkey */

        if (preg_match('/DreamKey\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Dreamkey';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            $this->device->setIdentification([
                'manufacturer' => 'Sega',
                'model' => 'Dreamcast',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::CONSOLE
            ]);
        }

        /* Dream Passport */

        if (preg_match('/DreamPassport\/([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Dream Passport';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            $this->device->setIdentification([
                'manufacturer' => 'Sega',
                'model' => 'Dreamcast',
                'type' => Constants\DeviceType::GAMING,
                'subtype' => Constants\DeviceSubType::CONSOLE
            ]);
        }

        /* Planetweb */

        if (preg_match('/Planetweb\/v?([0-9.]*)/u', $ua, $match)) {
            $this->browser->name = 'Planetweb';
            $this->browser->version = new Version(['value' => $match[1]]);
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (preg_match('/Dreamcast/u', $ua, $match)) {
                $this->device->setIdentification([
                    'manufacturer' => 'Sega',
                    'model' => 'Dreamcast',
                    'type' => Constants\DeviceType::GAMING,
                    'subtype' => Constants\DeviceSubType::CONSOLE
                ]);
            }

            if (preg_match('/SPS/u', $ua, $match)) {
                $this->device->setIdentification([
                    'manufacturer' => 'Sony',
                    'model' => 'PlayStation 2',
                    'type' => Constants\DeviceType::GAMING,
                    'subtype' => Constants\DeviceSubType::CONSOLE
                ]);
            }
        }
    }

    private function detectRemainingBrowsers($ua)
    {
        if ($data = Data\Applications::identifyBrowser($ua)) {
            $this->browser->set($data['browser']);

            if (!empty($data['device'])) {
                $this->device->set($data['device']);
            }
        }
    }

    private function detectWapBrowsers($ua)
    {
        if (!preg_match('/(Dorado|MAUI)/ui', $ua, $match)) {
            return;
        }

        if (preg_match('/Browser\/Dorado([0-9.]*)/ui', $ua, $match)) {
            $this->browser->name = 'Dorado WAP';
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
        }

        if (preg_match('/Dorado WAP-Browser\/([0-9.]*)/ui', $ua, $match)) {
            $this->browser->name = 'Dorado WAP';
            $this->browser->type = Constants\BrowserType::BROWSER;
            $this->browser->version = new Version(['value' => $match[1], 'details' => 2]);
        }

        if (preg_match('/MAUI[ _]WAP[ _]Browser(?:\/([0-9.]*))?/ui', $ua, $match)) {
            $this->browser->name = 'MAUI WAP';
            $this->browser->type = Constants\BrowserType::BROWSER;

            if (isset($match[1])) {
                $this->browser->version = new Version(['value' => $match[1]]);
            }
        }

        if (preg_match('/WAP Browser\/MAUI/ui', $ua, $match)) {
            $this->browser->name = 'MAUI WAP';
            $this->browser->type = Constants\BrowserType::BROWSER;
        }
    }
}
