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

use localzet\WebAnalyzer\Testrunner;
use localzet\WebAnalyzer\Tests;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Clover;

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});


$all = false;
$command = 'compare';
$files = [];
$options = [];

array_shift($argv);

if (count($argv)) {
    foreach ($argv as $argument) {
        if (in_array($argument, ['compare', 'check', 'rebase', 'list'])) {
            $command = $argument;
        } elseif (str_starts_with($argument, '--')) {
            $options[] = substr($argument, 2);
        } else {
            if (fnmatch("*.yaml", $argument)) {
                $files[] = $argument;
            } else {
                $files = array_merge($files, Tests::getFromCategory($argument));
            }
        }
    }
}

if (count($files) === 0) {
    $files = Tests::getAll();
}

switch ($command) {
    case 'list':
        Testrunner::search($files);
        break;

    case 'check':
        if (in_array('coverage', $options)) {
            $coverage = new CodeCoverage;
            $coverage->filter()->addDirectoryToWhitelist('src');
            $coverage->start('Testrunner');
        }

        $result = Testrunner::compare($files, false);

        if (in_array('coverage', $options)) {
            $coverage->stop();

            $writer = new Clover;
            $writer->process($coverage, 'runner.xml');

            echo "\nCoverage saved as runner.xml\n\n";
        }

        if (!$result) {
            echo "\033[0;31mTestrunner failed, please fix or rebase before building or deploying!\033[0m\n\n";

            if (in_array('show', $options)) {
                echo file_get_contents('runner.log') . "\n\n";
                echo "Done!\n\n";
            }

            exit(1);
        }

        break;

    case 'compare':
        $result = Testrunner::compare($files, false);

        if (!$result) {
            echo "\033[0;31mTestrunner failed, please look at runner.log for the details!\033[0m\n\n";

            if (in_array('show', $options)) {
                echo file_get_contents('runner.log') . "\n\n";
                echo "Done!\n\n";
            }

            exit(1);
        }

        break;

    case 'rebase':
        Testrunner::rebase($files, false);
        break;
}
