<?php

namespace Triangle\WebAnalyzer\Data;

class DeviceProfiles
{
    public static $PROFILES = [];

    public static function identify($url)
    {
        require_once __DIR__ . '/../../data/profiles.php';

        if (isset(self::$PROFILES[$url])) {
            return self::$PROFILES[$url];
        }

        return false;
    }
}
