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

use localzet\WebAnalyzer\Model\Version;

class NameVersion extends Base
{
    /**
     * @var string $name The name
     */
    public $name;

    /**
     * @var string $alias An alternative name that is used for readable strings
     */
    public $alias;

    /**
     * @var Version $version Version information
     */
    public $version;

    /**
     * Set the properties to the default values
     *
     * @param array|null $properties An optional array of properties to set after setting it to the default values
     */

    public function reset($properties = null)
    {
        unset($this->name);
        unset($this->alias);
        unset($this->version);

        if (is_array($properties)) {
            $this->set($properties);
        }
    }


    /**
     * Identify the version based on a pattern
     *
     * @param string $pattern The regular expression that defines the group that matches the version string
     * @param string $subject The string the regular expression is matched with
     * @param array|null $defaults An optional array of properties to set together with the value
     *
     * @return void
     */

    public function identifyVersion($pattern, $subject, $defaults = [])
    {
        if (preg_match($pattern, $subject, $match)) {
            $version = $match[1];

            if (isset($defaults['type'])) {
                switch ($defaults['type']) {
                    case 'underscore':
                        $version = str_replace('_', '.', $version);
                        break;
                    case 'legacy':
                        $version = preg_replace("/([0-9])([0-9])/", '$1.$2', $version);
                        break;
                }
            }


            $this->version = new Version(array_merge($defaults, ['value' => $version]));
        }
    }


    /**
     * Get the name in a human readable format
     *
     * @return string
     */

    public function getName()
    {
        return !empty($this->alias) ? $this->alias : (!empty($this->name) ? $this->name : '');
    }


    /**
     * Get the version in a human readable format
     *
     * @return string
     */

    public function getVersion()
    {
        return !empty($this->version) ? $this->version->toString() : '';
    }


    /**
     * Is a name detected?
     *
     * @return boolean
     */

    public function isDetected()
    {
        return !empty($this->name);
    }


    /**
     * Get the name and version in a human readable format
     *
     * @return string
     */

    public function toString()
    {
        return trim($this->getName() . ' ' . (!empty($this->version) && !$this->version->hidden ? $this->getVersion() : ''));
    }
}
