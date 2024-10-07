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

namespace localzet\WebAnalyzer\Model\Primitive;

class Base
{
    /**
     * Set the properties of the object the the values specified in the array
     *
     * @param array|null $defaults An array, the key of an element determines the name of the property
     */
    public function __construct($defaults = null)
    {
        if (is_array($defaults)) {
            $this->set($defaults);
        }
    }


    /**
     * Set the properties of the object the the values specified in the array
     *
     * @param array $properties An array, the key of an element determines the name of the property
     *
     */
    public function set($properties)
    {
        foreach ($properties as $k => $v) {
            $this->{$k} = $v;
        }
    }


    /**
     * Get a string containing a JavaScript representation of the object
     *
     * @return string
     */

    public function toJavaScript()
    {
        $lines = [];

        foreach (get_object_vars($this) as $key => $value) {
            if (!is_null($value)) {
                $line = $key . ": ";

                if ($key == 'version') {
                    $line .= 'new Version({ ' . $value->toJavaScript() . ' })';
                } elseif ($key == 'family') {
                    $line .= 'new Family({ ' . $value->toJavaScript() . ' })';
                } elseif ($key == 'using') {
                    $line .= 'new Using({ ' . $value->toJavaScript() . ' })';
                } else {
                    switch (gettype($value)) {
                        case 'boolean':
                            $line .= $value ? 'true' : 'false';
                            break;
                        case 'string':
                            $line .= '"' . addslashes($value) . '"';
                            break;
                        case 'integer':
                            $line .= $value;
                            break;
                    }
                }

                $lines[] = $line;
            }
        }

        return implode(", ", $lines);
    }
}
