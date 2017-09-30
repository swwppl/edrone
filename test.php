<?php

set_error_handler(
    function (
        int $errno,
        string $errstr,
        string $errfile,
        int $errline
    ) {
        throw new \ErrorException($errstr, $errno, 1, $errfile, $errline);
    }
);

set_exception_handler(
    function ($e) {
        echo "Exception : {$e->getMessage()}" . PHP_EOL;
    }
);

(function (array $includes = []) {
    foreach ($includes as $include) {
        if (file_exists($include) && is_readable($include)) {
            require_once $include;
        } else {
            throw new \ErrorException("Exception: Include file {$include} does not exists or is not readable.");
        }
    }
})([
    'ResHelper.class.php',
]);

if (isset($argv[1])) {
    if (ResHelper::sendFromCSV($argv[1], array(
        "action" => '127.0.0.1/recive.php'
    ))) {
        echo 'Done' . PHP_EOL;
        print_r(ResHelper::getLastTimes());
    } else {
        echo 'Exception : ' . (ResHelper::getLastException()->getMessage()) . PHP_EOL;
    }
} else {
    echo 'argument of csv path required' . PHP_EOL;
}
