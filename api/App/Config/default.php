<?php

$config['app']['debug'] = false;

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

$config['locale'] = array(
                        'at',
                        'au',
                        'be',
                        'br',
                        'ca',
                        'ch',
                        'de',
                        'dk',
                        'es',
                        'fi',
                        'fr',
                        'id',
                        'in',
                        'it',
                        'jp',
                        'my',
                        'no',
                        'pl',
                        'pt',
                        'se',
                        'sg',
                        'tr',
                        'uk',
                        'us',
                        'za',
                        'kr',
                        'ar',
                        'ru',
                        'hk',
                        'sk',
                        'nz',
                        'cl',
                        'ie',
                        'mx',
                        'cn'
                    );
