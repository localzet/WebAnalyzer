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

namespace localzet\WebAnalyzer\Analyser;

use localzet\WebAnalyzer;
use localzet\WebAnalyzer\Constants;

trait Header
{
    private function &analyseHeaders()
    {
        /* Analyse the main useragent header */

        if ($header = $this->getHeader('User-Agent')) {
            $this->analyseUserAgent($header);
        }


        /* Analyse secondary useragent headers */

        if ($header = $this->getHeader('X-Original-User-Agent')) {
            $this->additionalUserAgent($header);
        }

        if ($header = $this->getHeader('X-Device-User-Agent')) {
            $this->additionalUserAgent($header);
        }

        if ($header = $this->getHeader('Device-Stock-UA')) {
            $this->additionalUserAgent($header);
        }

        if ($header = $this->getHeader('X-OperaMini-Phone-UA')) {
            $this->additionalUserAgent($header);
        }

        if ($header = $this->getHeader('X-UCBrowser-Device-UA')) {
            $this->additionalUserAgent($header);
        }


        /* Analyse browser specific headers */

        if ($header = $this->getHeader('X-OperaMini-Phone')) {
            $this->analyseOperaMiniPhone($header);
        }

        if ($header = $this->getHeader('X-UCBrowser-Phone-UA')) {
            $this->analyseOldUCUserAgent($header);
        }

        if ($header = $this->getHeader('X-UCBrowser-UA')) {
            $this->analyseNewUCUserAgent($header);
        }

        if ($header = $this->getHeader('X-Puffin-UA')) {
            $this->analysePuffinUserAgent($header);
        }

        if ($header = $this->getHeader('Baidu-FlyFlow')) {
            $this->analyseBaiduHeader($header);
        }


        /* Analyse Android WebView browser ids */

        if ($header = $this->getHeader('X-Requested-With')) {
            $this->analyseBrowserId($header);
        }


        /* Analyse WAP profile header */

        if ($header = $this->getHeader('X-Wap-Profile')) {
            $this->analyseWapProfile($header);
        }

        return $this;
    }


    private function analyseUserAgent($header)
    {
        new Header\Useragent($header, $this->data, $this->options);
    }

    private function analyseBaiduHeader($header)
    {
        new Header\Baidu($header, $this->data);
    }

    private function analyseOperaMiniPhone($header)
    {
        new Header\OperaMini($header, $this->data);
    }

    private function analyseBrowserId($header)
    {
        new Header\BrowserId($header, $this->data);
    }

    private function analysePuffinUserAgent($header)
    {
        new Header\Puffin($header, $this->data);
    }

    private function analyseNewUCUserAgent($header)
    {
        new Header\UCBrowserNew($header, $this->data);
    }

    private function analyseOldUCUserAgent($header)
    {
        new Header\UCBrowserOld($header, $this->data);
    }

    private function analyseWapProfile($header)
    {
        new Header\Wap($header, $this->data);
    }


    private function additionalUserAgent($ua)
    {
        $extra = new WebAnalyzer($ua);

        if ($extra->device->type != Constants\DeviceType::DESKTOP) {
            if (isset($extra->os->name)) {
                $this->data->os = $extra->os;
            }

            if ($extra->device->identified) {
                $this->data->device = $extra->device;
            }
        }
    }


    private function getHeader($h)
    {
        /* Find the header that matches */
        foreach ($this->headers as $k => $v) {
            if (strtolower($h) == strtolower($k)) {
                /* And return the first 1024 bytes */
                return substr($v, 0, 1024);
            }
        }
    }
}
