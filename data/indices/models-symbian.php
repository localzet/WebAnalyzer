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

DeviceModels::$SYMBIAN_INDEX = array(
    '@' =>
        array(
            0 => '(?i)U1(a|i|iv)?$!',
            1 => '(?i)U5(a|i|iv)?$!',
            2 => '(?i)U8(a|i)?$!',
        ),
    '@32' =>
        array(
            0 => 3230,
            1 => 3250,
        ),
    '@36' =>
        array(
            0 => 3650,
            1 => 3660,
        ),
    '@50' =>
        array(
            0 => 500,
        ),
    '@52' =>
        array(
            0 => 5228,
            1 => '5233!',
            2 => '5230!',
            3 => 5232,
            4 => '5235!',
            5 => 5236,
            6 => 5238,
            7 => 5250,
        ),
    '@53' =>
        array(
            0 => '5320!',
        ),
    '@55' =>
        array(
            0 => '5500d!',
            1 => '5530!',
        ),
    '@56' =>
        array(
            0 => '5630!',
        ),
    '@57' =>
        array(
            0 => '5700!',
            1 => '5730!',
        ),
    '@58' =>
        array(
            0 => '5800!',
        ),
    '@60' =>
        array(
            0 => 600,
            1 => 603,
        ),
    '@61' =>
        array(
            0 => '6110Navigator',
            1 => '6120c!',
            2 => '6121c!',
            3 => '6122c!',
            4 => '6124c',
        ),
    '@62' =>
        array(
            0 => '6210 ?Navigator!',
            1 => '6220c!',
            2 => 6260,
            3 => '6290!',
        ),
    '@66' =>
        array(
            0 => 6600,
            1 => 6630,
            2 => '6650d!',
            3 => 6670,
            4 => 6680,
            5 => 6681,
        ),
    '@67' =>
        array(
            0 => '6700s',
            1 => '6700s-1c',
            2 => 6708,
            3 => '6710s',
            4 => '6720c!',
            5 => '6730c!',
            6 => '6760s!',
            7 => 6788,
            8 => '6788i',
            9 => '6790s-1b!',
            10 => '6790s-1c!',
        ),
    '@70' =>
        array(
            0 => 700,
            1 => 701,
            2 => '702T',
        ),
    '@76' =>
        array(
            0 => 7610,
            1 => 7650,
            2 => 7660,
        ),
    '@80' =>
        array(
            0 => '801T',
            1 => 808,
            2 => '808PureView',
            3 => '808 PureView',
        ),
    '@A1' =>
        array(
            0 => 'A1000',
        ),
    '@A9' =>
        array(
            0 => 'A920',
            1 => 'A925',
        ),
    '@BE' =>
        array(
            0 => 'BenQ P30',
            1 => 'BenQ P31',
        ),
    '@C5' =>
        array(
            0 => 'C5-00!',
            1 => 'C5-01',
            2 => 'C5-03!',
            3 => 'C5-04',
            4 => 'C5-05',
            5 => 'C5-06',
        ),
    '@C6' =>
        array(
            0 => 'C6-00!',
            1 => 'C6-01!',
        ),
    '@C7' =>
        array(
            0 => 'C7-00!',
        ),
    '@CO' =>
        array(
            0 => 'ConstellationT',
            1 => 'ConstellationQuest',
        ),
    '@E-' =>
        array(
            0 => 'E-90-1',
        ),
    '@E5' =>
        array(
            0 => 'E5-00!',
            1 => 'E50(-[1-9])?$!',
            2 => 'E51(-[1-9])?$!',
            3 => 'E52(-[1-9])?$!',
            4 => 'E55(-[1-9])?$!',
        ),
    '@E6' =>
        array(
            0 => 'E6',
            1 => 'E6-00',
            2 => 'E60(-[1-9])?$!',
            3 => 'E61i!',
            4 => 'E61(-[1-9])?$!',
            5 => 'E62(-[1-9])?$!',
            6 => 'E63(-[1-9])?$!',
            7 => 'E65(-[1-9])?$!',
            8 => 'E66(-[1-9])?$!',
        ),
    '@E7' =>
        array(
            0 => 'E7-00',
            1 => 'E70(-[1-9])?$!',
            2 => 'E71x',
            3 => 'E71(-[1-9])?$!',
            4 => 'E72(-[1-9])?$!',
            5 => 'E73(-[1-9])?$!',
            6 => 'E75(-[1-9])?$!',
        ),
    '@E9' =>
        array(
            0 => 'E90(-[1-9])?$!',
        ),
    '@G7' =>
        array(
            0 => 'G700',
        ),
    '@G9' =>
        array(
            0 => 'G900',
        ),
    '@I7' =>
        array(
            0 => 'I7710',
        ),
    '@I8' =>
        array(
            0 => 'I8510',
            1 => 'I8910',
        ),
    '@LG' =>
        array(
            0 => 'LG KS10',
            1 => 'LGKT610',
            2 => 'LGKT615',
        ),
    '@M1' =>
        array(
            0 => 'M1000',
        ),
    '@M6' =>
        array(
            0 => 'M600i',
        ),
    '@N-' =>
        array(
            0 => 'N-Gage',
            1 => 'N-GageQD',
        ),
    '@N5' =>
        array(
            0 => 'N5233!',
        ),
    '@N7' =>
        array(
            0 => 'N70(-[1-9])?$!',
            1 => 'N71(-[1-9])?$!',
            2 => 'N72(-[1-9])?$!',
            3 => 'N73(-[1-9])?$!',
            4 => 'N75(-[1-9])?$!',
            5 => 'N76(-[1-9])?$!',
            6 => 'N77(-[1-9])?$!',
            7 => 'N78(-[1-9])?$!',
            8 => 'N79(-[1-9])?$!',
        ),
    '@N8' =>
        array(
            0 => 'N8-00',
            1 => 'N80(-[1-9])?$!',
            2 => 'N81(-[1-9])?$!',
            3 => 'N82(-[1-9])?$!',
            4 => 'N85(-[1-9])?$!',
            5 => 'N86(-[1-9])?$!',
            6 => 'N86 ?8MP$!',
        ),
    '@N9' =>
        array(
            0 => 'N90(-[1-9])?$!',
            1 => 'N91(-[1-9])?$!',
            2 => 'N92(-[1-9])?$!',
            3 => 'N93(-[1-9])?$!',
            4 => 'N93i',
            5 => 'N95(-[1-9])?$!',
            6 => 'N95[ -]8GB(-[1-9])?!',
            7 => 'N96(-[1-9])?$!',
            8 => 'N97(-[1-3])?$!',
            9 => 'N97i',
            10 => 'N97(-[4-5])?$!',
            11 => 'N97 ?mini!',
        ),
    '@NO' =>
        array(
            0 => 'Nokia N81',
            1 => 'Nokia N81 8GB',
        ),
    '@OR' =>
        array(
            0 => 'Oro',
        ),
    '@P1' =>
        array(
            0 => 'P1i',
        ),
    '@P9' =>
        array(
            0 => 'P910i',
            1 => 'P990i',
        ),
    '@PA' =>
        array(
            0 => 'Panasonic-X700',
            1 => 'Panasonic-X800',
        ),
    '@RI' =>
        array(
            0 => 'RIZR-Z8',
            1 => 'RIZR-Z10',
        ),
    '@SG' =>
        array(
            0 => 'SGH-D720',
            1 => 'SGH-D728',
            2 => 'SGH-D730',
            3 => 'SGH-i400!',
            4 => 'SGH-i408!',
            5 => 'SGH-i450!',
            6 => 'SGH-i455!',
            7 => 'SGH-i458!',
            8 => 'SGH-i520!',
            9 => 'SGH-i550!',
            10 => 'SGH-i560!',
            11 => 'SGH-i568!',
            12 => 'SGH-i570!',
            13 => 'SGH-G810',
        ),
    '@T7' =>
        array(
            0 => 'T7-00',
        ),
    '@U' =>
        array(
            0 => 'U',
        ),
    '@W9' =>
        array(
            0 => 'W950i',
            1 => 'W960i',
        ),
    '@X5' =>
        array(
            0 => 'X5-00',
            1 => 'X5-01',
        ),
    '@X6' =>
        array(
            0 => 'X6-00!',
        ),
    '@X7' =>
        array(
            0 => 'X7-00!',
        ),
);
