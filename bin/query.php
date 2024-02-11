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

use localzet\WebAnalyzer;

$command = 'exec';
$options = [];
$payload = [];

array_shift($argv);

if (count($argv)) {
    foreach ($argv as $argument) {
        if ($argument == 'exec') {
            $command = $argument;
        } elseif (str_starts_with($argument, '--')) {
            $options[] = substr($argument, 2);
        } else {
            $payload[] = $argument;
        }
    }
}

$payload = implode(' ', $payload);


if ($command == 'exec') {
    if ($payload == '') {
        $payload = file_get_contents('php://stdin');
    }

    if ($payload != '') {
        echo "\n\033[0;32mInput:\033[0;0m\n" . trim($payload) . "\n";

        $result = new WebAnalyzer(trim($payload));
        echo "\n\033[0;32mHuman readable:\033[0;0m\n" . $result->toString() . "\n";
        echo "\n\033[0;32mData:\033[0;0m\n";
        echo json_encode($result, JSON_PRETTY_PRINT);
        echo "\n\n";
    }
}