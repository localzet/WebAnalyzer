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

namespace localzet;

use localzet\WebAnalyzer\Analyser;
use localzet\WebAnalyzer\Cache;
use localzet\WebAnalyzer\Model;
use Psr\Cache\InvalidArgumentException;

class WebAnalyzer
{
    use Cache;
    use Analyser\Header,
        Analyser\Derive,
        Analyser\Corrections,
        Analyser\Camouflage,
        Analyser\Location,
        Analyser\Network;

    /**
     * @var Model\Browser $browser Information about the browser
     */
    public $browser;

    /**
     * @var Model\Engine $engine Information about the rendering engine
     */
    public $engine;

    /**
     * @var Model\Os $os Information about the operating system
     */
    public $os;

    /**
     * @var Model\Device $device Information about the device
     */
    public $device;

    /**
     * @var Model\Location $location Information about the location
     */
    public $location;

    /**
     * @var Model\Network $location
     */
    public $network;

    /**
     * @var boolean $camouflage Is the browser camouflaged as another browser
     */
    public $camouflage = false;

    /**
     * @var int[] $features
     */
    public $features = [];

    public $options;

    /**
     * @param array|null $headers
     * @param array|null $options ['cache', 'cacheExpires', 'detectBots']
     * @throws InvalidArgumentException
     */
    public function __construct(?array $headers = null, ?array $options = [])
    {
        $this->browser = new Model\Browser();
        $this->engine = new Model\Engine();
        $this->os = new Model\Os();
        $this->device = new Model\Device();
        $this->location = new Model\Location();
        $this->network = new Model\Network();

        if ($headers) {
            $cache = $this->analyseWithCache($headers, $options);

            if ($cache && $cache->isHit()) {
                $this->applyCachedData($cache->get());
            } else {
                $this->options = (object)$options;

                $this
                    ->analyseHeaders()
                    ->analyseLocation()
                    ->deriveInformation()
                    ->applyCorrections()
                    ->detectCamouflage()
                    ->deriveDeviceSubType();

                if ($cache) {
                    $this->cache->save(
                        $cache
                            ->expiresAfter($this->expires)
                            ->set($this->retrieveCachedData())
                    );
                }
            }
        }
    }

    private function isX()
    {
        $arguments = func_get_args();
        $x = $arguments[0];

        if (count($arguments) < 2) {
            return false;
        }

        if (empty($this->$x->name)) {
            return false;
        }

        if ($this->$x->name != $arguments[1]) {
            return false;
        }

        if (count($arguments) >= 4) {
            if (empty($this->$x->version)) {
                return false;
            }

            if (!$this->$x->version->is($arguments[2], $arguments[3])) {
                return false;
            }
        }

        return true;
    }

    public function isBrowser()
    {
        $arguments = func_get_args();
        array_unshift($arguments, 'browser');
        return call_user_func_array([$this, 'isX'], $arguments);
    }

    public function isEngine()
    {
        $arguments = func_get_args();
        array_unshift($arguments, 'engine');
        return call_user_func_array([$this, 'isX'], $arguments);
    }

    public function isOs()
    {
        $arguments = func_get_args();
        array_unshift($arguments, 'os');
        return call_user_func_array([$this, 'isX'], $arguments);
    }

    public function isDevice($model)
    {
        return (!empty($this->device->series) && $this->device->series == $model) || (!empty($this->device->model) && $this->device->model == $model);
    }

    public function getType()
    {
        return $this->device->type . (!empty($this->device->subtype) ? ':' . $this->device->subtype : '');
    }

    public function isType()
    {
        $arguments = func_get_args();

        $count = count($arguments);
        for ($a = 0; $a < $count; $a++) {
            if (str_contains($arguments[$a], ':')) {
                list($type, $subtype) = explode(':', $arguments[$a]);
                if ($type == $this->device->type && $subtype == $this->device->subtype) {
                    return true;
                }
            } else {
                if ($arguments[$a] == $this->device->type) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isMobile()
    {
        return $this->isType('mobile', 'tablet', 'ereader', 'media', 'watch', 'camera', 'gaming:portable');
    }

    public function isDetected()
    {
        return $this->browser->isDetected() || $this->os->isDetected() || $this->engine->isDetected() || $this->device->isDetected();
    }

    public function toString()
    {
        $prefix = $this->camouflage ? 'an unknown browser that imitates ' : '';
        $browser = $this->browser->toString();
        $os = $this->os->toString();
        $engine = $this->engine->toString();
        $device = $this->device->toString();


        if (empty($device) && empty($os) && $this->device->type == 'television') {
            $device = 'television';
        }

        if (empty($device) && $this->device->type == 'emulator') {
            $device = 'emulator';
        }


        if (!empty($browser) && !empty($os) && !empty($device)) {
            return $prefix . $browser . ' on ' . $device . ' running ' . $os;
        }

        if (!empty($browser) && empty($os) && !empty($device)) {
            return $prefix . $browser . ' on ' . $device;
        }

        if (!empty($browser) && !empty($os) && empty($device)) {
            return $prefix . $browser . ' on ' . $os;
        }

        if (empty($browser) && !empty($os) && !empty($device)) {
            return $prefix . $device . ' running ' . $os;
        }

        if (!empty($browser) && empty($os) && empty($device)) {
            return $prefix . $browser;
        }

        if (empty($browser) && empty($os) && !empty($device)) {
            return $prefix . $device;
        }

        if ($this->device->type == 'desktop' && !empty($os) && !empty($engine) && empty($device)) {
            return 'an unknown browser based on ' . $engine . ' running on ' . $os;
        }

        if ($this->browser->stock && !empty($os) && empty($device)) {
            return $os;
        }

        if ($this->browser->stock && !empty($engine) && empty($device)) {
            return 'an unknown browser based on ' . $engine;
        }

        if ($this->device->type == 'bot') {
            return 'an unknown bot';
        }

        return 'an unknown browser';
    }

    public function toJavaScript()
    {
        return "this.browser = new Browser({ " . $this->browser->toJavaScript() . " });\n" .
            "this.engine = new Engine({ " . $this->engine->toJavaScript() . " });\n" .
            "this.os = new Os({ " . $this->os->toJavaScript() . " });\n" .
            "this.device = new Device({ " . $this->device->toJavaScript() . " });\n" .
            "this.camouflage = " . ($this->camouflage ? 'true' : 'false') . ";\n" .
            "this.features = " . json_encode($this->features) . ";\n";
    }

    public function toArray()
    {
        return [
            'browser' => $this->browser->toArray(),
            'engine' => $this->engine->toArray(),
            'os' => $this->os->toArray(),
            'device' => $this->device->toArray(),
            'location' => $this->location->toArray(),
            'network' => $this->network->toArray(),
            'camouflage' => $this->camouflage,
        ];
    }
}
