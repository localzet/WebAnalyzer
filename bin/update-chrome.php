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

include_once __DIR__ . '/bootstrap.php';
include __DIR__ . '/../data/browsers-chrome.php';

echo "Updating chrome versions...\n";

$stable = [
    'desktop' => [],
    'mobile' => []
];


$omaha = explode("\n", file_get_contents("http://omahaproxy.appspot.com/history"));
foreach ($omaha as $i => $line) {
    $items = explode(",", $line);

    if ($items[0] == 'mac' && $items[1] == 'stable') {
        $stable['desktop'][] = implode('.', array_slice(explode('.', $items[2]), 0, 3));
    }

    if ($items[0] == 'android' && $items[1] == 'stable') {
        $stable['mobile'][] = implode('.', array_slice(explode('.', $items[2]), 0, 3));
    }
}

$stable['desktop'] = array_unique($stable['desktop']);
$stable['mobile'] = array_unique($stable['mobile']);

sort($stable['desktop']);
sort($stable['mobile']);


foreach ($stable['desktop'] as $i => $version) {
    if (!isset(localzet\WebAnalyzer\Data\Chrome::$DESKTOP[$version])) {
        localzet\WebAnalyzer\Data\Chrome::$DESKTOP[$version] = 'stable';
    }
}

foreach ($stable['mobile'] as $i => $version) {
    if (!isset(localzet\WebAnalyzer\Data\Chrome::$MOBILE[$version])) {
        localzet\WebAnalyzer\Data\Chrome::$MOBILE[$version] = 'stable';
    }
}


$result = "<?php\n\n";
$result .= "namespace localzet\WebAnalyzer\Data;\n\n";
$result .= "Chrome::\$DESKTOP = [\n";
foreach (localzet\WebAnalyzer\Data\Chrome::$DESKTOP as $version => $channel) $result .= "    '{$version}' => '{$channel}',\n";
$result .= "];\n\n";
$result .= "Chrome::\$MOBILE = [\n";
foreach (localzet\WebAnalyzer\Data\Chrome::$MOBILE as $version => $channel) $result .= "    '{$version}' => '{$channel}',\n";
$result .= "];\n";


file_put_contents(__DIR__ . '/../data/browsers-chrome.php', $result);
