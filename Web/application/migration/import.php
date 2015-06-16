<?php
$dbh = new PDO('mysql:host=localhost;dbname=kortingscode_org', 'imbull', 'imbull2012dfr');

// Import categories
try {

  foreach($dbh->query('SELECT * from aaw_Categories') as $row) {
        //echo '<pre>'.print_r($row, true).'</pre>';
  }

} catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";
  die();
}

// get shops
try {
  foreach($dbh->query('SELECT * from pub_Publications WHERE Pub_cat_Id = 37') as $row) {
        echo '<pre>'.print_r($row, true).'</pre>';
  }

} catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";
  die();
}


// to generate clean urls for use in perma links
setlocale(LC_ALL, 'en_US.UTF8');
function toAscii($str, $replace=array(), $delimiter='-')
{
    if( !empty($replace) ) {
        $str = str_replace((array)$replace, ' ', $str);
    }

    $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
    $clean = strtolower(trim($clean, '-'));
    $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

    return $clean;
}

$dbh = null;
