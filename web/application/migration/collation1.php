<?php
//$db = mysql_connect('localhost','root','');

    $execute_sql = false;

    $host = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'test';

    $db = new mysqli($host, $username, $password, $dbname);

    $mysqldatabase = 'test';
    $collation = 'CHARACTER SET utf8 COLLATE utf8_general_ci';

    echo '<div>';
    $db->query("ALTER DATABASE $mysqldatabase $collation");

    $result = $db->query("SHOW TABLES");

    $count = 0;
    while($row = $result->fetch_assoc()) {
        $table = $row['Tables_in_'.$mysqldatabase];
        if($execute_sql) $db->query("ALTER TABLE $table DEFAULT $collation");
        $result1 = $db->query("SHOW COLUMNS FROM $table");
        $alter = '';
        while($row1 = $result1->fetch_assoc()) {
            if (preg_match('~char|text|enum|set~', $row1["Type"])) {
                if(strpos($row1["Field"], 'uuid')){
                    // why does this not work
                }else{
                    $alter .= (strlen($alter)?", \n":" ") . "MODIFY `$row1[Field]` $row1[Type] $collation" . ($row1["Null"] ? "" : " NOT NULL") . ($row1["Default"] && $row1["Default"] != "NULL" ? " DEFAULT '$row1[Default]'" : "");
                }
            }
        }
        if(strlen($alter)){
            $sql = "ALTER TABLE $table".$alter.";";
            echo "<div>$sql\n\n</div><br>";
            if($execute_sql) mysql_query($sql);
        }
        $count++;
    }
    echo '</div>';
