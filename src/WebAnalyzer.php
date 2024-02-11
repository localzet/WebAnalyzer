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
use localzet\WebAnalyzer\Model\Main;
use Psr\Cache\InvalidArgumentException;

class WebAnalyzer extends Main
{
    use Cache;

    /**
     * Create a new object that contains all the detected information
     *
     * @param array|null $headers Optional, an array with all the headers or a string with just the User-Agent header
     * @param array $options Optional, an array with configuration options
     * @throws InvalidArgumentException
     */

    public function __construct(?array $headers = null, array $options = [])
    {
        parent::__construct();
        $this->analyse($headers, $options);
    }

    /**
     * Analyse the provided headers or User-Agent string
     *
     * @param array|null $headers An array with all the headers or a string with just the User-Agent header
     * @param array $options
     * @throws InvalidArgumentException
     */

    public function analyse(?array $headers = null, array $options = []): void
    {
        if ($this->analyseWithCache($headers, $options)) {
            return;
        }

        $analyser = new Analyser($headers, $options);
        $analyser->setdata($this);
        $analyser->analyse();
    }
}
