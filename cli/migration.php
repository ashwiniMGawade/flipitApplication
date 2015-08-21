<?php

namespace migration;

$appSettings = array();
try {
    $appSettings = parse_ini_file('web/application/configs/application.ini');
} catch (\Exception $e) {
    die('applications.ini settings file not available.');
}

$dsn = array();
$dbname = '';
$user = '';
$password = '';
$host = '';

$commandOptions = array(
                    'migrate' => array('--dry-run','--query-time','--write-sql'),
                    'generate' => array('--editor-cmd'),
                    'execute' => array('--dry-run', '--query-time', '--write-sql', '--up', '--down'),
                    'version' => array('--add','--delete','--all', '--range-from', '--range-to'),
                    'status' => array('--show-versions'),
                );

foreach ($appSettings as $setting => $value) {
    if (substr($setting, -4) == '.dsn' || $setting == 'doctrine.imbull') {
        $dsn = parse_url($value);

        $dbName = trim($dsn['path'], '/');
        $user = $dsn['user'];
        $password = $dsn['pass'];
        $host = $dsn['host'];

        //for testing run it for kortingscode_user database

        if ($dbName != 'kortingscode_user') {
            continue;
        } else {
            echo $dbName;
        }

        $connectionCode = "<?php
		return array('dbname'   => '$dbName',
	        'user'     => '$user',
	    	'password' => '$password',
	    	'host'     => '$host',
	    	'driver'   => 'pdo_mysql');";

        //set the config directories based on the database name
        $dbconfig = 'SiteDatabaseConfig';
        $migrationConfig = 'SiteConfig';

        if ($dbName == strtolower('kortingscode_user')) {
            $dbconfig = 'UserDatabaseConfig';
            $migrationConfig = 'UserConfig';
        }

        $fp = fopen("cli/$dbconfig/".$dbName.'.php', 'w+');
        fwrite($fp, $connectionCode);
        fclose($fp);

        $versionNumber = '';
        $command = strtolower(trim($argv[1]));

        $commandOption = isset($argv[2]) ? strtolower(trim($argv[2])) : '';

        if (!array_key_exists($command, $commandOptions)) {
            die("Invalid command, your command should be: \n".implode(array_keys($commandOptions), ' ')."\n");
        }

        //validate options
        // e.g. migrate 100 --dry-run

        if (!empty($commandOption)) {
            if (!in_array($commandOption, $commandOptions[$command])) {
                if (!in_array($command, array('migrate', 'execute', 'version'))) {
                    die("Invalid command options for command: $command. The options should be : \n".implode($commandOptions[$command], ' ').'\n');
                } else {
                    if (count($argv) > 2) {
                        $tempOptionCollector = array();
                        for ($argvOptionIndex = 3; $argvOptionIndex < count($argv); ++$argvOptionIndex) {
                            if (!in_array($argv[$argvOptionIndex], $commandOptions[$command])) {
                                die("Invalid command options for command: $command. The options should be : \n".implode($commandOptions[$command], ' ').'\n');
                            } else {
                                $tempOptionCollector[] = trim($argv[$argvOptionIndex]);
                            }
                        }

                        $commandOptions = implode(' ', $tempOptionCollector);
                    } else {
                        $versionNumber = is_numeric(trim($argv[2])) ? trim($argv[2]) : '';
                    }
                }
            }
        }

        //for generate command, create migrations for two DBs only site & user
        if ($command == 'generate' && !in_array($dbName, array('kortingscode_user', 'kortingscode_site'))) {
            continue;
        }

        // mapping for enum

         // $params = include "cli/$dbconfig/".$dbName.'.php';

        // if (!is_array($params)) {
        //     throw new \InvalidArgumentException('The connection file has to return an array.');
        // }

        //$connection = DriverManager::getConnection($params);
        //$connection = \Doctrine\DBAL\DriverManager::getConnection($params);
        //$platform = $connection->getDatabasePlatform();
        //$platform->registerDoctrineTypeMapping('enum', 'varchar');


        system("php cli/console.php  migration:$command  $versionNumber $commandOption --db-configuration cli/$dbconfig/$dbName.php --configuration cli/$migrationConfig/migrations.yml --no-interaction ", $output);

        echo $output;

        //break;
    }
}
