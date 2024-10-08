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

use localzet\WebAnalyzer\Constants\DeviceType;

DeviceModels::$FEATURE_MODELS = [
    'Amstrad Gamma' => ['Amstrad', 'E-m@iler Plus', DeviceType::DESKTOP],
    'Benefon Q' => ['Benefon', 'Q'],
    'EF71' => ['BenQ-Siemens', 'EF71'],
    'Lightpipe' => ['Kyocera', 'E1100 Neo'],
    'K612' => ['Kyocera', 'K612 Strobe'],
    'EX115' => ['Motorola', 'Starling EX115'],
    'EX118' => ['Motorola', 'MOTOKEY XT EX118'],
    'EX119' => ['Motorola', 'Brea EX119'],
    'EX128' => ['Motorola', 'Kingfisher EX128'],
    'EX225' => ['Motorola', 'MOTOKEY Social EX225'],
    'EX226' => ['Motorola', 'MOTOKEY Social EX226'],
    'EX430' => ['Motorola', 'MotoGo EX430'],
    'MOTOQA1' => ['Motorola', 'Karma QA1'],
    'KC910i' => ['LG', 'KC910i Renoir'],
    'KP500!' => ['LG', 'KP500 Cookie'],
    'KP570!' => ['LG', 'KP570 Cookie'],
    'LG-KU380' => ['LG', 'KU380'],
    'LG-KU580' => ['LG', 'KU580 Hero'],
    'LG-KU990' => ['LG', 'KU990 Viewty'],
    'KU990i' => ['LG', 'KU990 Viewty'],
    'GM360' => ['LG', 'GM360 Viewty Snap'],
    'GR700' => ['LG', 'GR700 Vu Plus'],
    'GS290' => ['LG', 'GS290 Cookie Fresh'],
    'GS500' => ['LG', 'GS500 Cookie Plus'],
    'GT500!' => ['LG', 'GT500 Puccini'],
    'GT550' => ['LG', 'GT550 Encore'],
    'COCOON' => ['O2', 'Cocoon'],
    'P7000' => ['Pantech', 'Impact'],
    'P9020' => ['Pantech', 'Pursuit'],
    'P9050' => ['Pantech', 'Laser'],
    'PM-8200' => ['Sanyo', 'PM-8200'],
    'SCP-5300' => ['Sanyo', 'SCP-5300'],
    'SCP-5500' => ['Sanyo', 'VM4500'],
    'SCP-6600' => ['Sanyo', 'Katana'],
    'PLS6600KJ' => ['Sanyo', 'Katana'],
    'GT-B2710' => ['Samsung', 'Xcover 271'],
    'GT-B3210' => ['Samsung', 'Corby TXT'],
    'GT-B3313' => ['Samsung', 'Corby Mate'],
    'GT-C3200' => ['Samsung', 'Monte Bar'],
    'GT-C3222' => ['Samsung', 'Ch@t 322'],
    'GT-C3322' => ['Samsung', 'GT-C3322 Duos'],
    'GT-C3500' => ['Samsung', 'Ch@t 350'],
    'GT-C5010!' => ['Samsung', 'C5010 Squash'],
    'GT-E1282!' => ['Samsung', 'Guru'],
    'GT-E2152' => ['Samsung', 'GT-E2152 Duos'],
    'GT-E2220' => ['Samsung', 'Ch@t 220'],
    'GT-E2222' => ['Samsung', 'Ch@t 222'],
    'GT-E2202' => ['Samsung', 'Metro'],
    'GT-E2250' => ['Samsung', 'Utica'],
    'GT-E2252' => ['Samsung', 'Metro 2252'],
    'GT-E3213' => ['Samsung', 'E3213 Hero'],
    'GT-E3309I' => ['Samsung', 'E3309 Manhattan'],
    'm3510c' => ['Samsung', 'M3510'],
    'GT-M8910' => ['Samsung', 'M8910 Pixon12'],
    'GT-S3332' => ['Samsung', 'Ch@t 333'],
    'GT-S33(50|53)!' => ['Samsung', 'Ch@t 335'],
    'GT-S35(70|72)!' => ['Samsung', 'Ch@t 357'],
    'GT-S5229' => ['Samsung', 'Tocco Lite 2'],
    'GT-S5270!' => ['Samsung', 'Ch@t 527'],
    'GT-S5610!' => ['Samsung', 'Primo'],
    'SCH-W169' => ['Samsung', 'W169 Duos'],
    'SCH-W279' => ['Samsung', 'Primo Duos'],
    'SGH-A667' => ['Samsung', 'A667 Evergreen'],
    'SGH-A697' => ['Samsung', 'A697 Sunburst'],
    'SGH-A877' => ['Samsung', 'A877 Impression'],
    'SGH-A927' => ['Samsung', 'A927 Flight II'],
    'SGH-A997' => ['Samsung', 'Rugby III'],
    'SGH-D880' => ['Samsung', 'D880 Duos'],
    'SGH-E250i' => ['Samsung', 'E250'],
    'SGH-E250V' => ['Samsung', 'E250'],
    'SGH-G600' => ['Samsung', 'G600'],
    'SGH-J700i' => ['Samsung', 'J700'],
    'SGH-J700V' => ['Samsung', 'J700'],
    'SGH-M200' => ['Samsung', 'M200'],
    'SGH-S150G' => ['Samsung', 'S150 TracFone'],
    'SGH-S390G' => ['Samsung', 'S390 TracFone'],
    'SGH-T189N' => ['Samsung', 'Freeform M'],
    'SGHX660V' => ['Samsung', 'X660'],
    'SGH-Z107!' => ['Samsung', 'Z107'],
    'SGH-Z130!' => ['Samsung', 'Z130'],
    'SGH-Z500!' => ['Samsung', 'Z500'],
    'SM-B313E' => ['Samsung', 'Metro 313'],
    'SM-B350E' => ['Samsung', 'Metro 350'],
    'SM-B360E' => ['Samsung', 'Metro 360'],
    'SM-B780(A|W)!' => ['Samsung', 'Rugby 4'],
    'S7350' => ['Samsung', 'S7350 Ultra S'],
    'sam-r560' => ['Samsung', 'Messenger II R560'],
    'Sendo Wap' => ['Sendo', 'Z100'],
    'CK13[ai]?$!' => ['Sony Ericsson', 'txt'],
    'CK15[ai]?$!' => ['Sony Ericsson', 'txt Pro'],
    'F100[ai]?$!' => ['Sony Ericsson', 'Jalou'],
    'J105[ai]?$!' => ['Sony Ericsson', 'Naite'],
    'J108[ai]?$!' => ['Sony Ericsson', 'Cedar'],
    'J10(i2?)?$!' => ['Sony Ericsson', 'Elm'],
    'J20[ai]?$!' => ['Sony Ericsson', 'Hazel'],
    'U100[ai]?$!' => ['Sony Ericsson', 'Yari'],
    'U10[ai]?$!' => ['Sony Ericsson', 'Aino'],
    'W100i?$!' => ['Sony Ericsson', 'Spiro'],
    'W150i?$!' => ['Sony Ericsson', 'Yendo'],
    'W20i?$!' => ['Sony Ericsson', 'Zylo'],
    'WT13i$!' => ['Sony Ericsson', 'Mix Walkman'],
    'X5i$!' => ['Sony Ericsson', 'Xperia Pureness X5'],
    'tecnot36' => ['Tecno', 'T36'],
    'Vodafone 575' => ['Vodafone', '575'],
    'GT-I6410!' => ['Vodafone', '360 M1'],
    'GT-I8320!' => ['Vodafone', '360 H1'],
    'GT-I8330!' => ['Vodafone', '360 H2'],
    'WIDETEL WCX150' => ['Widetel', 'WCX150'],
];
