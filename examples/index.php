<?php

require '../vendor/autoload.php';

if (!function_exists('log_message')) {
    function log_message($level, $message) {
        static $logger;
        if (!($logger instanceof \ialopezg\Services\Logger)) {
            $logger = new \ialopezg\Services\Logger([
                'log_path' => 'logs'
            ]);
        }

        $logger->write($level, $message);
    }
}

log_message('debug', 'Debug message');
log_message('error', 'Error message');
log_message('info', 'Informative message');
log_message('warning', 'Waring message');