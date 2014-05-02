<?php
class RemoveColumnVisitor extends Doctrine_Migration_Base
{

    protected $_db = null;
    protected $_locale = '';
    protected $_connInstance = null;


    public function up()
    {
        $this->removeColumn( 'visitor', 'locale');
    }


    # get db name and connection as well before down
    public function preDown()
    {
        # get connected db name
        foreach(Doctrine_Manager::getInstance()->getConnections() as $connection){

            $this->_connInstance  = $connection ;
            $conn = $connection->getOptions();
            preg_match('/dbname=(.*)/', $conn['dsn'], $db);
            $this->_db = $db[1];
        }

        # get the locale of db
        $parts = array_reverse( explode('_' , $this->_db) );

        # check locale length for flipit
        if(strlen($parts[0]) == 2) {
            # set the locale
            $this->_locale = $parts[0];
        }

    }

    public function down()
    {
        $this->addColumn( 'visitor', 'locale', 'string', 5 ,
                array('default' => $this->_locale ,
                        'notnull' => true,
                        'collation' => 'utf8_general_ci' ));
    }


    # update locale value for all existing visitors
    public function postDown()
    {

        echo $this->_locale ;
        $v = Doctrine_Query::create($this->_connInstance)
                ->update('Visitor')->set('locale' , "'". $this->_locale ."'" )	;
        $v->execute();
    }

}
