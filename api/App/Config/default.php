<?php

$config['app']['debug'] = false;

// Cache TTL in seconds
$config['app']['cache.ttl'] = 60;

// Max requests per hour
$config['app']['rate.limit'] = 1000;

$config['app']['log.writer'] = new \Flynsarmy\SlimMonolog\Log\MonologWriter(
    array(
        'name' => '',
        'handlers' => array(
            new \Monolog\Handler\StreamHandler(
                realpath(__DIR__ . '/../../logs').'/'.getenv('APPLICATION_ENV') . '_' .date('Y-m-d').'.log'
            ),
        ),
    )
);
