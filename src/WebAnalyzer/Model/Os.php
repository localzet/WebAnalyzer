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

use localzet\WebAnalyzer\Model\Primitive\NameVersion;

class Os extends NameVersion
{
    /**
     * @var Family $family To which family does this operating system belong
     */
    public $family;

    /** @var string */
    public $edition;

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
        parent::reset();

        unset($this->family);
        unset($this->edition);

        $this->hidden = false;

        if (is_array($properties)) {
            $this->set($properties);
        }
    }


    /**
     * Return the name of the operating system family
     *
     * @return string
     */

    public function getFamily()
    {
        if (isset($this->family)) {
            return $this->family->getName();
        }

        return $this->getName();
    }


    /**
     * Is the operating from the specified family
     *
     * @param string $name The name of the family
     *
     * @return boolean
     */

    public function isFamily($name)
    {
        if ($this->getName() == $name) {
            return true;
        }

        if (isset($this->family)) {
            if ($this->family->getName() == $name) {
                return true;
            }
        }

        return false;
    }


    /**
     * Get the name and version in a human readable format
     *
     * @return string
     */

    public function toString()
    {
        if ($this->hidden) {
            return '';
        }

        return trim($this->getName() .
                (!empty($this->version) && !$this->version->hidden ? ' ' . $this->getVersion() : '')) .
            (!empty($this->edition) ? ' ' . $this->edition : '');
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

        if (!empty($this->name)) {
            $result['name'] = $this->name;
        }

        if (!empty($this->family)) {
            $result['family'] = $this->family->toArray();
        }

        if (!empty($this->alias)) {
            $result['alias'] = $this->alias;
        }

        if (!empty($this->edition)) {
            $result['edition'] = $this->edition;
        }

        if (!empty($this->version)) {
            $result['version'] = $this->version->toArray();
        }

        if (isset($result['version']) && empty($result['version'])) {
            unset($result['version']);
        }

        return $result;
    }
}
