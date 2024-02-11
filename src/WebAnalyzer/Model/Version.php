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

use localzet\WebAnalyzer\Model\Primitive\Base;

class Version extends Base
{
    /** @var string|null */
    public $value = null;

    /** @var boolean */
    public $hidden = false;

    /** @var string */
    public $nickname;

    /** @var string */
    public $alias;

    /** @var int */
    public $details;

    /** @var boolean */
    public $builds;


    /**
     * Determine if the version is lower, equal or higher than the specified value
     *
     * @param string   The operator, must be <, <=, =, >= or >
     * @param mixed    The value, can be an integer, float or string with a version number
     *
     * @return boolean
     */

    public function is()
    {
        $valid = false;

        $arguments = func_get_args();
        if (count($arguments)) {
            $operator = '=';
            $compare = null;

            if (count($arguments) == 1) {
                $compare = $arguments[0];
            }

            if (count($arguments) >= 2) {
                $operator = $arguments[0];
                $compare = $arguments[1];
            }

            if (!is_null($compare)) {
                $min = min(substr_count($this->value, '.'), substr_count($compare, '.')) + 1;

                $v1 = $this->toValue($this->value, $min);
                $v2 = $this->toValue($compare, $min);

                switch ($operator) {
                    case '<':
                        $valid = $v1 < $v2;
                        break;
                    case '<=':
                        $valid = $v1 <= $v2;
                        break;
                    case '=':
                        $valid = $v1 == $v2;
                        break;
                    case '>':
                        $valid = $v1 > $v2;
                        break;
                    case '>=':
                        $valid = $v1 >= $v2;
                        break;
                }
            }
        }

        return $valid;
    }


    /**
     * Return an object with each part of the version number
     *
     * @return object
     */

    public function getParts()
    {
        $parts = !is_null($this->value) ? explode('.', $this->value) : [];

        return (object)[
            'major' => !empty($parts[0]) ? intval($parts[0]) : 0,
            'minor' => !empty($parts[1]) ? intval($parts[1]) : 0,
            'patch' => !empty($parts[2]) ? intval($parts[2]) : 0,
        ];
    }


    /**
     * Return the major version as an integer
     *
     * @return integer
     */

    public function getMajor()
    {
        return $this->getParts()->major;
    }


    /**
     * Return the minor version as an integer
     *
     * @return integer
     */

    public function getMinor()
    {
        return $this->getParts()->minor;
    }


    /**
     * Return the patch number as an integer
     *
     * @return integer
     */

    public function getPatch()
    {
        return $this->getParts()->patch;
    }


    /**
     * Convert a version string seperated by dots into a float that can be compared
     *
     * @param string $value Version string, with elements seperated by a dot
     * @param int $count The maximum precision
     *
     * @return float
     *
     */

    private function toValue($value = null, $count = null)
    {
        if (is_null($value)) {
            $value = $this->value;
        }

        $parts = explode('.', $value);
        if (!is_null($count)) {
            $parts = array_slice($parts, 0, $count);
        }

        $result = $parts[0];

        if (count($parts) > 1) {
            $result .= '.';

            $count = count($parts);
            for ($p = 1; $p < $count; $p++) {
                $result .= substr('0000' . $parts[$p], -4);
            }
        }

        return floatval($result);
    }


    /**
     * Return the version as a float
     *
     * @return float
     */

    public function toFloat()
    {
        return floatval($this->value);
    }


    /**
     * Return the version as an integer
     *
     * @return int
     */

    public function toNumber()
    {
        return intval($this->value);
    }


    /**
     * Return the version as a human readable string
     *
     * @return string
     */

    public function toString()
    {
        if (!empty($this->alias)) {
            return $this->alias;
        }

        $version = '';

        if (!empty($this->nickname)) {
            $version .= $this->nickname . ' ';
        }

        if (!empty($this->value)) {
            if (preg_match("/([0-9]+)(?:\.([0-9]+))?(?:\.([0-9]+))?(?:\.([0-9]+))?(?:([ab])([0-9]+))?/", $this->value, $match)) {
                $v = [$match[1]];

                if (array_key_exists(2, $match) && strlen($match[2])) {
                    $v[] = $match[2];
                }

                if (array_key_exists(3, $match) && strlen($match[3])) {
                    $v[] = $match[3];
                }

                if (array_key_exists(4, $match) && strlen($match[4])) {
                    $v[] = $match[4];
                }

                if (!empty($this->details)) {
                    if ($this->details < 0) {
                        array_splice($v, $this->details, 0 - $this->details);
                    }

                    if ($this->details > 0) {
                        array_splice($v, $this->details, count($v) - $this->details);
                    }
                }

                if (isset($this->builds) && !$this->builds) {
                    $count = count($v);
                    for ($i = 0; $i < $count; $i++) {
                        if ($v[$i] > 999) {
                            array_splice($v, $i, 1);
                        }
                    }
                }

                $version .= implode('.', $v);

                if (array_key_exists(5, $match) && strlen($match[5])) {
                    $version .= $match[5] . (!empty($match[6]) ? $match[6] : '');
                }
            }
        }

        return $version;
    }


    /**
     * Get an array of all defined properties
     *
     * @return array
     *
     */

    public function toArray()
    {
        $result = [];

        if (!empty($this->value)) {
            if (!empty($this->details)) {
                $parts = explode('.', $this->value);
                $result['value'] = join('.', array_slice($parts, 0, $this->details));
            } else {
                $result['value'] = $this->value;
            }
        }

        if (!empty($this->alias)) {
            $result['alias'] = $this->alias;
        }

        if (!empty($this->nickname)) {
            $result['nickname'] = $this->nickname;
        }

        if (isset($result['value']) && !isset($result['alias']) && !isset($result['nickname'])) {
            return $result['value'];
        }

        return $result;
    }
}
