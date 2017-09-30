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
        $result = [];

        try {
            $header = [];

            array_filter(
                file($filename, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES),
                function ($value) use (&$header, &$result) {
                    $record = str_getcsv($value, ',', "'");

                    if ($header) {
                        $result[] = array_combine($header, $record);
                    } else {
                        $header = $record;
                    }

                    return false;
                }
            );
        } catch (\Exception $e) {
            self::$lastException = $e;
        }

        return $result;
    }

    private static function sendData($data, $settings)
    {
        self::$lastTimes = [];
        $ch = null;

        try {
            if(self::$lastException) {
                throw self::$lastException;
            }

            if(!function_exists('curl_init')) {
                throw new \ErrorException('CURL curl_init not exist.');
            }

            array_filter(
                $data,
                function ($value) use (&$ch, $settings) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $settings['action']);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_NOBODY, true);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $value);
                    curl_exec($ch);

                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    if (200 !== $httpCode) {
                        throw new \ErrorException("CURL http status {$httpCode}.");
                    }

                    $totalTime = number_format(curl_getinfo($ch, CURLINFO_TOTAL_TIME), 3);

                    self::$lastTimes[] = "{$totalTime}s";

                    return false;
                }
            );

            if (!self::$lastTimes) {
                throw new \ErrorException("CURL no tasks.");
            }
        } catch (\Exception $e) {
            self::$lastException = $e;
            return false;
        } finally {
            if ($ch) {
                curl_close($ch);
            }
        }

        return true;
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
