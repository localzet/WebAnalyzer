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

DeviceModels::$TIZEN_MODELS = [
    'Baltic' => ['Samsung', '"Baltic"'],
    'SM-HIGGS' => ['Samsung', '"Higgs"'],
    'KIRAN' => ['Samsung', 'Z1'],
    'GT-I8800!' => ['Samsung', '"Melius"'],
    'GT-I8805!' => ['Samsung', '"Redwood"'],
    'GT-I9500!' => ['Samsung', 'GT-I9500 prototype'],
    'SGH-N099' => ['Samsung', 'SGH-N099 prototype'],
    '(ARMV7 )?SM-Z9005!' => ['Samsung', 'SM-Z9005 prototype'],
    'Mobile-RD-PQ' => ['Samsung', 'RD-PQ prototype'],
    'TM1' => ['Samsung', 'TM1 prototype'],
    'SM-Z130!' => ['Samsung', 'Z1'],
    'TIZEN SM-Z130!' => ['Samsung', 'Z1'],
    'SM-Z200!' => ['Samsung', 'Z2'],
    'SM-Z250!' => ['Samsung', '"Pride"'],
    'SM-Z300!' => ['Samsung', 'Z3'],
    'TIZEN SM-Z300!' => ['Samsung', 'Z3'],
    'SM-Z400!' => ['Samsung', 'Z4'],
    'SM-Z500!' => ['Samsung', 'SM-Z500'],
    'SM-Z700!' => ['Samsung', 'SM-Z700'],
    'SM-Z900!' => ['Samsung', 'Z'],
    'SM-Z910!' => ['Samsung', 'Z'],
    'Z3 Z910F' => ['Samsung', 'Z'],
    'SEC SC-001' => ['Samsung', 'SC-001 prototype'],
    'SEC SC-03F' => ['Samsung', 'ZeQ'],                        // Unreleased version for DoCoMo
    'SC-03F' => ['Samsung', 'ZeQ'],                        // Unreleased version for DoCoMo

    'SM-G870F0' => ['Samsung', 'Galaxy S5 Active'],

    'SM-R360!' => ['Samsung', 'Gear Fit2', DeviceType::WATCH],
    'SM-R600!' => ['Samsung', 'Gear Sport', DeviceType::WATCH],
    'SM-R720!' => ['Samsung', 'Gear S2', DeviceType::WATCH],
    'SM-R730!' => ['Samsung', 'Gear S2', DeviceType::WATCH],
    'SM-R732!' => ['Samsung', 'Gear S2 Classic', DeviceType::WATCH],
    'SM-R735!' => ['Samsung', 'Gear S2 Classic', DeviceType::WATCH],
    'SM-R750!' => ['Samsung', 'Gear S', DeviceType::WATCH],
    'SM-R760!' => ['Samsung', 'Gear S3', DeviceType::WATCH],
    'SM-R765!' => ['Samsung', 'Gear S3', DeviceType::WATCH],
    'SM-R770!' => ['Samsung', 'Gear S3 Classic', DeviceType::WATCH],
    'SM-R805!' => ['Samsung', 'Gear S4', DeviceType::WATCH],

    'NX300' => ['Samsung', 'NX300', DeviceType::CAMERA],

    'FamilyHub' => ['Samsung', 'Family Hub', DeviceType::APPLIANCE],
    'RF10M9995!' => ['Samsung', 'Family Hub', DeviceType::APPLIANCE],
    'RF23M8590!' => ['Samsung', 'Family Hub', DeviceType::APPLIANCE],
    'RF265BEAE!' => ['Samsung', 'Family Hub', DeviceType::APPLIANCE],
    'RF28M9580!' => ['Samsung', 'Family Hub', DeviceType::APPLIANCE],
    'RF56M9540!' => ['Samsung', 'Family Hub', DeviceType::APPLIANCE],
    'RF85K9993!' => ['Samsung', 'Family Hub', DeviceType::APPLIANCE],
    'RF85M95A2!' => ['Samsung', 'Family Hub', DeviceType::APPLIANCE],
    'RH81M8090!' => ['Samsung', 'Family Hub', DeviceType::APPLIANCE],


    'hawkp' => ['Samsung', '"Hawkp"', DeviceType::TELEVISION],

    'xu3' => ['Hardkernel', 'ODROID-XU3 developer board'],

    'sdk' => [null, null, DeviceType::EMULATOR],
    'Emulator' => [null, null, DeviceType::EMULATOR],
    'Mobile-Emulator' => [null, null, DeviceType::EMULATOR],
    'TIZEN Emulator' => [null, null, DeviceType::EMULATOR],
];
