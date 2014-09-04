<?php
$con = mysql_connect('localhost', 'root', 'password');
if (!$con) {
    echo "Cannot connect to the database ";
    die();
}
mysql_select_db('kc');
$result = mysql_query('show tables');
while ($tables = mysql_fetch_array($result)) {
    foreach ($tables as $key => $value) {
        mysql_query("ALTER TABLE $value CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
    }
}
echo "The collation of your database has been successfully changed!";
