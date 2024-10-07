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

namespace localzet\WebAnalyzer\Analyser\Header;

trait Useragent
{
    use Useragent\Os,
        Useragent\Device,
        Useragent\Browser,
        Useragent\Application,
        Useragent\Using,
        Useragent\Engine,
        Useragent\Bot;

    public function analyseUserAgent($header)
    {
        $header = preg_replace("/^(Mozilla\/[0-9]\.[0-9].{20,})\s+Mozilla\/[0-9]\.[0-9].*$/iu", '$1', $header);

        $this->detectOperatingSystem($header)
            ->detectDevice($header)
            ->detectBrowser($header)
            ->detectApplication($header)
            ->detectUsing($header)
            ->detectEngine($header);

        /* Detect bots */

        if (!isset($this->options->detectBots) || $this->options->detectBots === true) {
            $this->detectBot($header);
        }

        /* Refine some of the information */

        $this->refineBrowser($header)
            ->refineOperatingSystem($header);
    }

    private function removeKnownPrefixes($ua)
    {
        $ua = preg_replace('/^OneBrowser\/[0-9.]+\//', '', $ua);
        $ua = preg_replace('/^MQQBrowser\/[0-9.]+\//', '', $ua);
        return $ua;
    }
}
