<?php
class PageAttribute extends BasePageAttribute
{
    #########################################################
    ############### REFACTORED CODE         ##################
    #########################################################
    public static function getPageAttributeIdByName($attributeName)
    {
        $pageAttribute = Doctrine_Query::create()
        ->select('p.id')
        ->from('pageAttribute p')
        ->where('p.name="'.$attributeName.'"')
        ->fetchOne();
        return isset($pageAttribute->id) ? (int) $pageAttribute->id : 0;
    }

    public static function insertPageAttributes()
    {
        $pageAttributeObject = new Doctrine_Collection('PageAttribute');
        $pageAttributeObject[0]->name = "contact";
        $pageAttributeObject[1]->name = "faq";
        $pageAttributeObject[2]->name = "default";
        $pageAttributeObject->save();
        return true;
    }

    public static function deletePageAttributes()
    {
        $databaseConnection = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
        $databaseConnection->query('SET FOREIGN_KEY_CHECKS=0');
        $databaseConnection->query('TRUNCATE TABLE page_attribute');
        $databaseConnection->query('SET FOREIGN_KEY_CHECKS=1');
        unset($databaseConnection);
        return true;
    }
    #########################################################
    ############# END REFACTORED CODE     ###################
    #########################################################

    public function __contruct($connName = false)
    {
        if(! $connName) {
            $connName = "doctrine_site" ;
        }

        Doctrine_Manager::getInstance()->bindComponent($connName, $connName);

    }


    public function getPageAttributes()
    {
        $attrList = Doctrine_Query::create()
                    ->select()->from('pageAttribute')->fetchArray();
        return  $attrList;
    }

    /**
     *  Author Amit Sharma
     *  Get page id on name basis
     *	Version: 1.0
     */

    public static function getPageIdByName($name)
    {
        $data = Doctrine_Core::getTable('pageAttribute')->findOneBy('name', $name);
        return $data->id;

    }

    /**
     *  Author Amit Sharma
     *  Get insert Sign up page Attribute
     *	Version: 1.0
     */



}
