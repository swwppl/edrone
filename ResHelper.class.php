<?php

class ResHelper
{
    private static $lastException = null;
    private static $lastTimes = null;

    private function __construct()
    {
    }

    private static function loadCSV($filename)
    {
    }

    private static function sendData($data, $settings)
    {
    }

    public static function sendFromCSV($filename, $settings)
    {
        return self::sendData(self::loadCSV($filename), $settings);
    }

    public static function getLastException()
    {
        return self::$lastException;
    }

    public static function getLastTimes()
    {
        return self::$lastTimes;
    }
}