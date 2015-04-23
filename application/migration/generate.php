<style type="text/css">
p
{
    color : green;
    line-height: 25px;
    margin: 0px 15px;

}
</style>
<?php
require_once('../../library/Doctrine1/Doctrine.php');
spl_autoload_register(array('Doctrine', 'autoload'));
$manager = Doctrine_Manager::getInstance();

define("DB_HOST","localhost");
define("DB_USERNAME","root");
define("DB_PASSWORD","");
define("DB_DBNAME","test");

// set_include_path(implode(PATH_SEPARATOR, array(
// realpath("../application/models"),
// get_include_path(),
// )));

//$DMC = Doctrine_Manager::connection('mysql://root:mind@123@10.8.18.152:3307/kortingscode', 'doctrine');

$DMC = Doctrine_Manager::connection('mysql://root@localhost/test', 'doctrine');


spl_autoload_register(array('Doctrine', 'modelsAutoload'));
$manager = Doctrine_Manager::getInstance();


//$manager->setAttribute(Doctrine_Core::ATTR_TBLNAME_FORMAT, 'beta_%s');

$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
$manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

//echo "<b><pre>" ;

echo "<p>Start generating models...</p>";

//Doctrine_Core::generateModelsFromDb("C:/wamp/www/newmodels");

    //Doctrine_Core::loadModels(realpath("../application/models"));
    //Doctrine_Core::dumpData(realpath("../application/configs/data/fixture.yml"));


Doctrine_Core::generateModelsFromYaml(realpath("../application/configs/new.yml"),
                realpath('C:/wamp/www/newmodels'), array('generateTableClasses' => true));




/*
$tables = $DMC->import->listTables();
echo "<p>No. of existing tables " .count($tables) . ".</p>";
$i = 0;
while (count($tables) > 0 && $i < 10) {
    foreach ($tables as $tableName) {
        try {
            $DMC->export->dropTable($tableName);
        } catch(Doctrine_Exception $e) {

        }
    }
$i++ ;
$tables = $DMC->import->listTables();
}
*/
/*
echo "<p>No. of tables not deleted " .count($tables). "</p>";
echo "<p>Tables Deleted successfully.</p>";

*/


//Doctrine_Core::createTablesFromModels(realpath('C:/wamp/www/newmodels'));
$tables = $DMC->import->listTables();

echo "<p>".count($tables ). " tables added successfully.</P>";
