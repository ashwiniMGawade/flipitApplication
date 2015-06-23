<?php

function init_db($host, $user, $pw, $db, $port, $socket)
{
    $mysqli = array();

    foreach ($db as $db_index => $db_name) {
        $tmp_mysqli = new mysqli(
            $host,
            $user,
            $pw,
            $db_name,
            $port,
            $socket
        );

        if ($tmp_mysqli->connect_errno) {
            printf("%s: Connect failed: %s\n",
                $db_index, $tmp_mysqli->connect_error
            );
            exit();
        }

        $mysqli[$db_index] = $tmp_mysqli;
    }

    echo "Connected to all DBs\n";

    return $mysqli;
}

function close_db($db)
{
    foreach ($db as $the_db) {
        $the_db->close();
    }

    echo "All DB connections closed\n";
}
