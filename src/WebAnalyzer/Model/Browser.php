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

class Browser extends NameVersion
{
    /**
     * @var Using $using Information about web views the browser is using
     * @var Family $family To which browser family does this browser belong
     */
    public $using;
    public $family;


    /** @var string */
    public $channel;

    /** @var boolean */
    public $stock = true;

    /** @var boolean */
    public $hidden = false;

    /** @var string */
    public $mode = '';

    /** @var string */
    public $type = '';


    /**
     * Set the properties to the default values
     *
     * @param array|null $properties An optional array of properties to set after setting it to the default values
     *
     */

    public function reset($properties = null)
    {
        parent::reset();

        unset($this->channel);
        unset($this->using);
        unset($this->family);

        $this->stock = true;
        $this->hidden = false;
        $this->mode = '';
        $this->type = '';

        if (is_array($properties)) {
            $this->set($properties);
        }
    }


    /**
     * Get the name in a human readable format
     *
     * @return string
     */

    public function getName()
    {
        $name = !empty($this->alias) ? $this->alias : (!empty($this->name) ? $this->name : '');
        return $name ? $name . (!empty($this->channel) ? ' ' . $this->channel : '') : '';
    }


    /**
     * Is the browser from the specified family
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
     * Is the browser using the specified webview
     *
     * @param string $name The name of the webview
     *
     * @return boolean
     */

    public function isUsing($name)
    {
        if (isset($this->using)) {
            if ($this->using->getName() == $name) {
                return true;
            }
        }

        return false;
    }


    /**
     * Get a combined name and version number in a human readable format
     *
     * @return string
     */

    public function toString()
    {
        if ($this->hidden) {
            return '';
        }

        $result = trim($this->getName() . ' ' . (!empty($this->version) && !$this->version->hidden ? $this->getVersion() : ''));

        if (empty($result) && isset($this->using)) {
            return $this->using->toString();
        }

        return $result;
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

        if (!empty($this->name)) {
            $result['name'] = $this->name;
        }

        if (!empty($this->alias)) {
            $result['alias'] = $this->alias;
        }

        if (!empty($this->using)) {
            $result['using'] = $this->using->toArray();
        }

        if (!empty($this->family)) {
            $result['family'] = $this->family->toArray();
        }

        if (!empty($this->version)) {
            $result['version'] = $this->version->toArray();
        }

        if (!empty($this->type)) {
            $result['type'] = $this->type;
        }

        if (isset($result['version']) && empty($result['version'])) {
            unset($result['version']);
        }

        return $result;
    }
}
