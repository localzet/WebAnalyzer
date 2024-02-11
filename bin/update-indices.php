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

use localzet\WebAnalyzer\Data\DeviceModels;
use ReverseRegex\Lexer;

$command = 'list';
$types = [];
$options = [];

array_shift($argv);

if (count($argv)) {
    foreach ($argv as $argument) {
        if ($argument == 'list') {
            $command = $argument;
        } elseif (str_starts_with($argument, '--')) {
            $options[] = substr($argument, 2);
        } else {
            $types[] = $argument;
        }
    }
}

if (in_array('all', $options)) {
    $types = [
        'android', 'asha', 'bada', 'brew', 'feature', 'firefoxos',
        'kddi', 'palmos', 's30plus', 's40', 'symbian', 'tizen',
        'touchwiz', 'wm', 'wp'
    ];
}


foreach ($types as $i => $type) {
    command($command, $type);
}


function command($command, $type)
{
    if ($command == 'list') {
        command_list($type);
    }
}

function command_list($type)
{
    echo "Creating index for 'data/models-{$type}.php'...\n";

    require_once __DIR__ . '/../data/models-' . $type . '.php';

    $name = strtoupper($type) . '_MODELS';
    $list = DeviceModels::$$name;

    $index = [];

    foreach ($list as $key => $value) {

        if (str_ends_with($key, '!!')) {
            $keys = getKeysFromRegexp(substr($key, 0, -2));
        } elseif (str_ends_with($key, '!')) {
            $keys = getKeysFromRegexp(substr($key, 0, -1));
        } else {
            $keys = [substr(strtoupper($key), 0, 2)];
        }

        foreach ($keys as $k => $v) {
            if ($v == '**') {
                $v = '';
            }

            if (!isset($index['@' . $v])) {
                $index['@' . $v] = [];
            }

            $index['@' . $v][] = $key;
        }
    }

    ksort($index);

    $file = "<" . "?php\n";
    $file .= "\n";
    $file .= "namespace localzet\\WebAnalyzer\\Data;\n";
    $file .= "\n";
    $file .= "DeviceModels::\$" . strtoupper($type) . "_INDEX = " . var_export($index, true) . ";\n";

    file_put_contents(__DIR__ . '/../data/indices/models-' . $type . '.php', $file);
}

function getKeysFromRegexp($regexp)
{
    $lexer = new Lexer($regexp);
    $lexer->moveNext();

    $keys = readKeysFromLexer($lexer);

    return array_unique($keys);
}


function readKeysFromLexer($lexer)
{
    $keys = [];

    if ($lexer->isNextTokenAny([Lexer::T_LITERAL_CHAR, Lexer::T_LITERAL_NUMERIC])) {
        $current = strtoupper($lexer->lookahead['value']);

        $lexer->moveNext();
        if ($lexer->isNextTokenAny([Lexer::T_LITERAL_CHAR, Lexer::T_LITERAL_NUMERIC])) {
            $keys[] = $current . strtoupper($lexer->lookahead['value']);
        } else {
            $keys[] = '**';
        }
    } else if ($lexer->isNextToken(Lexer::T_GROUP_OPEN)) {
        $current = '';
        $active = true;

        while ($lexer->moveNext()) {
            if ($lexer->isNextTokenAny([Lexer::T_LITERAL_CHAR, Lexer::T_LITERAL_NUMERIC])) {
                if ($active && strlen($current) < 2) {
                    $current .= strtoupper($lexer->lookahead['value']);
                }
            } else if ($lexer->isNextToken(Lexer::T_CHOICE_BAR)) {
                $keys[] = $current;
                $current = '';
                $active = true;
            } else if ($lexer->isNextToken(Lexer::T_GROUP_OPEN)) {
                if ($lexer->moveNext()) {
                    $more = readKeysFromLexer($lexer);

                    if (count($more)) {
                        $keys = array_merge($keys, $more);
                    } else {
                        break;
                    }
                }
            } else if ($lexer->isNextToken(Lexer::T_GROUP_CLOSE)) {
                if (strlen($current) == 2) {
                    $keys[] = $current;
                } else {
                    $keys[] = '**';
                }

                $current = '';
                $active = true;
                break;
            } else if ($lexer->isNextToken(Lexer::T_DOT)) {
                $keys[] = '**';
                $current = '';
                $active = false;
                break;
            } else {
                $active = false;
            }
        }

        while ($lexer->moveNext()) {
            if ($lexer->isNextToken(Lexer::T_QUANTIFIER_QUESTION)) {
                if ($lexer->moveNext()) {
                    $more = readKeysFromLexer($lexer);

                    if (count($more)) {
                        $keys = array_merge($keys, $more);
                    } else {
                        break;
                    }
                }
            } else {
                break;
            }
        }
    } else if ($lexer->isNextToken(Lexer::T_SET_OPEN)) {
        $keys[] = '**';
    } else if ($lexer->isNextToken(Lexer::T_DOT)) {
        $keys[] = '**';
    }

    return array_unique($keys);
}
