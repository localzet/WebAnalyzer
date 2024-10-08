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

use localzet\WebAnalyzer\Data\Chrome;

include_once __DIR__ . '/bootstrap.php';
include __DIR__ . '/../data/browsers-chrome.php';

echo "Updating chrome versions...\n";

$channels = ['Stable', 'Beta', 'Dev', 'Canary', 'Canary_asan'];
$platforms = [
    'Windows' => 'DESKTOP',
    'Win32' => 'DESKTOP',
    'Mac' => 'DESKTOP',
    'Linux' => 'DESKTOP',
    'Android' => 'MOBILE',
    'iOS' => 'MOBILE'
];

$versions = [
    'desktop' => [],
    'mobile' => []
];

foreach ($platforms as $platform => $type) {
    $url = "https://chromiumdash.appspot.com/fetch_releases?platform={$platform}";
    $chromiumDash = json_decode(file_get_contents($url), true);
    foreach ($chromiumDash as $release) {
        $version = implode('.', array_slice(explode('.', $release['version']), 0, 3));
        Chrome::$$type[$version] = strtolower($release['channel']);
    }
}

$desktop = array_unique(Chrome::$DESKTOP);
$mobile = array_unique(Chrome::$MOBILE);

ksort($desktop);
ksort($mobile);

Chrome::$DESKTOP = $desktop;
Chrome::$MOBILE = $mobile;

$result = <<<PHP_INS
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

/* This file is automatically generated, do not edit manually! */

namespace localzet\WebAnalyzer\Data;


PHP_INS;

$result .= "Chrome::\$DESKTOP = [\n";
foreach (Chrome::$DESKTOP as $version => $channel) $result .= "    '{$version}' => '{$channel}',\n";
$result .= "];\n\n";

$result .= "Chrome::\$MOBILE = [\n";
foreach (Chrome::$MOBILE as $version => $channel) $result .= "    '{$version}' => '{$channel}',\n";
$result .= "];\n";

file_put_contents(__DIR__ . '/../data/browsers-chrome.php', $result);
