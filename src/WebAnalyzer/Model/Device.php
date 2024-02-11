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

namespace localzet\WebAnalyzer\Model;

use localzet\WebAnalyzer\Constants;
use localzet\WebAnalyzer\Data;
use localzet\WebAnalyzer\Model\Primitive\Base;

class Device extends Base
{
    /** @var string */
    public $manufacturer;

    /** @var string */
    public $model;

    /** @var string */
    public $series;

    /** @var string */
    public $carrier;

    /** @var int */
    public $identifier;

    /** @var mixed */
    public $flag;


    /** @var string */
    public $type = '';

    /** @var string */
    public $subtype = '';

    /** @var int */
    public $identified = Constants\Id::NONE;

    /** @var boolean */
    public $generic = true;

    /** @var boolean */
    public $hidden = false;


    /**
     * Set the properties to the default values
     *
     * @param array|null $properties An optional array of properties to set after setting it to the default values
     *
     * @internal
     */

    public function reset($properties = null)
    {
        unset($this->manufacturer);
        unset($this->model);
        unset($this->series);
        unset($this->carrier);
        unset($this->identifier);

        $this->type = '';
        $this->subtype = '';
        $this->identified = Constants\Id::NONE;
        $this->generic = true;
        $this->hidden = false;

        if (is_array($properties)) {
            $this->set($properties);
        }
    }


    /**
     * Identify the manufacturer and model based on a pattern
     *
     * @param string $pattern The regular expression that defines the group that matches the model
     * @param string $subject The string the regular expression is matched with
     * @param array|null $defaults An optional array of properties to set together
     *
     * @return void
     */

    public function identifyModel($pattern, $subject, $defaults = [])
    {
        if (preg_match($pattern, $subject, $match)) {
            $this->manufacturer = !empty($defaults['manufacturer']) ? $defaults['manufacturer'] : null;
            $this->model = Data\DeviceModels::cleanup($match[1]);
            $this->identifier = preg_replace('/ (Mozilla|Opera|Obigo|AU.Browser|UP.Browser|Build|Java|PPC|AU-MIC.*)$/iu', '', $match[0]);
            $this->identifier = preg_replace('/_(TD|GPRS|LTE|BLEU|CMCC|CUCC)$/iu', '', $match[0]);

            if (isset($defaults['model'])) {
                if (is_callable($defaults['model'])) {
                    $this->model = call_user_func($defaults['model'], $this->model);
                } else {
                    $this->model = $defaults['model'];
                }
            }

            $this->generic = false;
            $this->identified |= Constants\Id::PATTERN;

            if (!empty($defaults['carrier'])) {
                $this->carrier = $defaults['carrier'];
            }

            if (!empty($defaults['type'])) {
                $this->type = $defaults['type'];
            }
        }
    }


    /**
     * Declare an positive identification
     *
     * @param array $properties An array, the key of an element determines the name of the property
     *
     * @return void
     */

    public function setIdentification($properties)
    {
        $this->reset($properties);

        if (!empty($this->model)) {
            $this->generic = false;
        }

        $this->identified |= Constants\Id::MATCH_UA;
    }


    /**
     * Get the name of the carrier in a human readable format
     *
     * @return string
     */

    public function getCarrier()
    {
        return $this->identified && !empty($this->carrier) ? $this->carrier : '';
    }


    /**
     * Get the name of the manufacturer in a human readable format
     *
     * @return string
     */

    public function getManufacturer()
    {
        return $this->identified && !empty($this->manufacturer) ? $this->manufacturer : '';
    }


    /**
     * Get the name of the model in a human readable format
     *
     * @return string
     */

    public function getModel()
    {
        if ($this->identified) {
            return trim((!empty($this->model) ? $this->model . ' ' : '') . (!empty($this->series) ? $this->series : ''));
        }

        return !empty($this->model) ? $this->model : '';
    }


    /**
     * Get the combined name of the manufacturer and model in a human readable format
     *
     * @return string
     */

    public function toString()
    {
        if ($this->hidden) {
            return '';
        }

        if ($this->identified) {
            $model = $this->getModel();
            $manufacturer = $this->getManufacturer();

            if ($manufacturer != '' && str_starts_with($model, $manufacturer)) {
                $manufacturer = '';
            }

            return trim($manufacturer . ' ' . $model);
        }

        return !empty($this->model) ? 'unrecognized device (' . $this->model . ')' : '';
    }


    /**
     * Check if device information is detected
     *
     * @return boolean
     */

    public function isDetected()
    {
        return !empty($this->type) || !empty($this->model) || !empty($this->manufacturer);
    }


    /**
     * Get an array of all defined properties
     *
     * @return array
     * @internal
     *
     */

    public function toArray()
    {
        $result = [];

        if (!empty($this->type)) {
            $result['type'] = $this->type;
        }

        if (!empty($this->subtype)) {
            $result['subtype'] = $this->subtype;
        }

        if (!empty($this->manufacturer)) {
            $result['manufacturer'] = $this->manufacturer;
        }

        if (!empty($this->model)) {
            $result['model'] = $this->model;
        }

        if (!empty($this->series)) {
            $result['series'] = $this->series;
        }

        if (!empty($this->carrier)) {
            $result['carrier'] = $this->carrier;
        }

        return $result;
    }
}
