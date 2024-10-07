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

namespace localzet\WebAnalyzer;

use localzet\WebAnalyzer;

class Analyser
{
    use Analyser\Header, Analyser\Location, Analyser\Derive, Analyser\Corrections, Analyser\Camouflage;

    private $data;

    private $options;

    private $headers = [];

    public function __construct($headers, $options = [])
    {
        $this->headers = $headers;
        $this->options = (object)$options;
    }

    public function setData(&$data)
    {
        $this->data =& $data;
    }

    public function &getData()
    {
        return $this->data;
    }

    public function analyse()
    {
        if (!isset($this->data)) {
            $this->data = new WebAnalyzer();
        }

        /* Start the actual analysing steps */

        $this->analyseHeaders()
            ->analyseLocation()
            ->deriveInformation()
            ->applyCorrections()
            ->detectCamouflage()
            ->deriveDeviceSubType();
    }
}
