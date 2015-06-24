<?php

ini_set('memory_limit', '-1');
set_time_limit(0);

defined('APPLICATION_PATH')
|| define(
    'APPLICATION_PATH',
    dirname(
        dirname(__FILE__)
    )
);

defined('LIBRARY_PATH')
|| define('LIBRARY_PATH', realpath(dirname(dirname(dirname(__FILE__))). '/library'));

defined('DOCTRINE_PATH') || define('DOCTRINE_PATH', LIBRARY_PATH . '/Doctrine1');

defined('APPLICATION_ENV')
|| define(
    'APPLICATION_ENV',
    (
        getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production')
);

set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(APPLICATION_PATH . '/../library'),
                get_include_path(),)
    )
);
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(DOCTRINE_PATH),
            get_include_path(),)
    )
);

defined('UPLOAD_DATA_FOLDER_EXCEL_PATH')
|| define(
    'UPLOAD_DATA_FOLDER_EXCEL_PATH',
    APPLICATION_PATH. '/../data/'
);

defined('UPLOAD_EXCEL_TMP_PATH')
|| define(
    'UPLOAD_EXCEL_TMP_PATH',
    APPLICATION_PATH. '/../public/tmp/'
);

defined('PUBLIC_PATH')
        || define(
            'PUBLIC_PATH',
            dirname(
                dirname(
                    dirname(__FILE__)
                )
            )."/public/"
        );
require_once(LIBRARY_PATH.'/FrontEnd/Helper/viewHelper-v1.php');
require_once(LIBRARY_PATH.'/BackEnd/Helper/MandrillHelper-v1.php');
require_once(DOCTRINE_PATH . '/Doctrine.php');


//Ensure library/ is on include_path
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(APPLICATION_PATH . '/../library'),
            realpath(APPLICATION_PATH . '/../vendor/zendframework/zendframework1/library')
        )
    )
);
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(realpath(DOCTRINE_PATH), get_include_path())
    )
);
require_once realpath(APPLICATION_PATH . '/../vendor/autoload.php');
/** Zend_Application */
require_once 'Zend/Application.php';
require_once 'PHPExcel/PHPExcel.php';