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
        return isset($pageAttribute->id) ? $pageAttribute->id : 0;
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

    public static function insertPageAttribute()
    {
            for($i=1; $i<5; $i++) {

                $sname = "Sign Up Step ".$i;
                $findRecordFromDatabase = $data = Doctrine_Core::getTable('pageAttribute')->findOneBy('name', $sname);
                if(!isset($findRecordFromDatabase)) {
                    $pageAttributeObject = new PageAttribute();
                    $pageAttributeObject->name = $sname;
                    $pageAttributeObject->save();
                    return true;
                }else{

                    return false;

                }

            }

        }

}
