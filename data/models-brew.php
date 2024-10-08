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

namespace localzet\WebAnalyzer\Data;

DeviceModels::$BREW_MODELS = [
    'Coolpad D508' => ['Coolpad', 'D508'],
    'Coolpad D510' => ['Coolpad', 'D510'],
    'Coolpad E600' => ['Coolpad', 'E600'],
    'HUAWEI U526' => ['Huawei', 'U526'],
    'HUAWEI U528' => ['Huawei', 'U528'],
    'HUAWEI U2801!' => ['Huawei', 'U2801'],
    'HUAWEI U5200!' => ['Huawei', 'U5200'],
    'HUAWEI U5300!' => ['Huawei', 'U5300'],
    'HUAWEI U5310!' => ['Huawei', 'U5310'],
    'HUAWEI U6150!' => ['Huawei', 'U6150'],
    'E4255' => ['Kyocera', 'DuraMax E4255'],
    'S2151!' => ['Kyocera', 'Coast S2151'],
    'EX200!' => ['Motorola', 'EX200'],
    'NOKIA 7705' => ['Nokia', '7705 Twist'],
    'M260!' => ['Samsung', 'Factor'],
    'M350!' => ['Samsung', 'Seek'],
    'M370!' => ['Samsung', 'M370'],
    'M380!' => ['Samsung', 'Trender'],
    'M390!' => ['Samsung', 'Array'],
    'M550!' => ['Samsung', 'Exclaim'],
    'M560!' => ['Samsung', 'Reclaim'],
    'SGH-A937!' => ['Samsung', 'SCH-A937'],
    'SGH-A947!' => ['Samsung', 'SCH-A947'],
    'SCH-B309!' => ['Samsung', 'SCH-B309'],
    'SCH-F839' => ['Samsung', 'SCH-F839'],
    'SCH-M519!' => ['Samsung', 'Metro TV'],
    'SCH-S559!' => ['Samsung', 'SCH-S559'],
    'SCH-S579!' => ['Samsung', 'SCH-S579'],
    'SCH-U380!' => ['Samsung', 'Brightside'],
    'SCH-U485' => ['Samsung', 'Intensity III'],
    'SCH-U640!' => ['Samsung', 'Convoy'],
    'SCH-U660!' => ['Samsung', 'Convoy II'],
    'SCH-U680!' => ['Samsung', 'Convoy 3'],
    'SCH-U750!' => ['Samsung', 'Alias 2'],
    'SCH-U820!' => ['Samsung', 'Reality'],
    'SCH-U960!' => ['Samsung', 'Rogue'],
    'SCH-W709!' => ['Samsung', 'SCH-W709'],
    'SCH-W799!' => ['Samsung', 'SCH-W799'],
    'sam-r631' => ['Samsung', 'Messenger Touch R631'],
    'sam-r640' => ['Samsung', 'Character R640'],
    'sam-r900' => ['Samsung', 'Craft R900'],
    'SM-B690V' => ['Samsung', 'Convoy 4'],
    'SPH M330' => ['Samsung', 'SPH-M330'],
    'SPH-M570' => ['Samsung', 'Restore'],
    'PLS M330' => ['Samsung', 'PLS-M330'],
    'Sprint M850' => ['Samsung', 'Instinct HD'],
    'SCP-3810' => ['Sanyo', 'SCP-3810'],
    'SCP3810' => ['Sanyo', 'SCP-3810'],
    'SCP-6750' => ['Sanyo', 'Katana Eclipse X'],
    'SCP6760' => ['Sanyo', 'Incognito'],
    'SCP-6760' => ['Sanyo', 'Incognito'],
    'SCP6780' => ['Sanyo', 'Innuendo'],
    'HS-E316!' => ['Hisense', 'E316'],
    'VX5600!' => ['LG', 'Accolade'],
    'VX9200!' => ['LG', 'Env3'],
    'VX9600!' => ['LG', 'Versa'],
    'VX11000!' => ['LG', 'Env Touch'],
    'VN170!' => ['LG', 'Revere 3'],
    'VN250!' => ['LG', 'Cosmos'],
    'VN271!' => ['LG', 'Extravert'],
    'VN280!' => ['LG', 'Extravert 2'],
    'VN360!' => ['LG', 'Exalt'],
    'VN370!' => ['LG', 'Exalt II'],
    'VN530' => ['LG', 'Octane'],
    'LG272' => ['LG', 'Rumor Reflex'],
    'LG510' => ['LG', 'Rumor Touch'],
    'LN240' => ['LG', 'Remarq'],
    'LN510' => ['LG', 'Rumor Touch'],
    'LX610' => ['LG', 'Lotus Elite'],
    'AX8575' => ['LG', 'Chocolate Touch'],
    'LGE AX840' => ['LG', 'Tritan'],
    'LGE LG700' => ['LG', 'Bliss'],
    'LGE LG840' => ['LG', 'Spyder II'],
    'LGE UX700' => ['LG', 'Bliss'],
    'LGE UX840' => ['LG', 'Tritan'],
    'LGE VX11K' => ['LG', 'Env Touch'],
    'LGE VX8575' => ['LG', 'Chocolate Touch'],
    'LGE VX9700' => ['LG', 'Dare'],
    'P5000' => ['Pantech', 'Link II'],
    'P6020' => ['Pantech', 'Persuit II'],
    'P6030' => ['Pantech', 'Reneu'],
    'CDM8992' => ['Pantech', 'Hotshot'],
    'CDM8999' => ['Pantech', 'Crux'],
    'TXT8045' => ['Pantech', 'Jest 2'],
    'Pantech CDM8992!' => ['Pantech', 'Hotshot'],
    'Pantech CDM8999!' => ['Pantech', 'Crux'],
    'Pantech TXT8045!' => ['Pantech', 'Jest 2'],
    'ZTE F-450!' => ['ZTE', 'Adamant'],
    'ZTE R516!' => ['ZTE', 'R516'],
    'ZTE R518!' => ['ZTE', 'R518'],
];
