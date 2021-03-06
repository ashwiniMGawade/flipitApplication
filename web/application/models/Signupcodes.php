<?php

/**
 * Signupcodes
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class Signupcodes extends BaseSignupcodes
{
    /**
     * Search to account setting
     * @param string $keyword
     * @param boolean $flag
     * @version 1.0
     * @return array $data
     * @author sunny patial
     */

    public static function getfreeCodelogin()
    {
        $data = Doctrine_Query::create()
        ->select('p.id,p.entered_uid,p.code,p.created_at')
        ->from('Signupcodes p')
        ->orderBy('p.code ASC')->fetchArray();
        return $data;
    }
    public static function deletecodebyid($id)
    {
        if($id){
            //delete particular code from list
            $pc = Doctrine_Query::create()->delete('Signupcodes')
            ->where('id=' . $id)->execute();
            //call cache function
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_signupcode_list');
        }
    }
    public static function addcode($codetext,$userid)
    {
            $code = new Signupcodes(); // A Doctrine Model with timestampable behavior enabled
            $code->entered_uid =$userid;
            $code->code = "$codetext";
            $code->save();
            //call cache function
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_signupcode_list');
    }
    public static function getcodebytxt($txt)
    {
        if($txt){
            //delete particular code from list
            $pc = Doctrine_Query::create()->select('p.id')
            ->from('Signupcodes p')
            ->where('code=' . "'$txt'")->fetchArray();
            if(count($pc)>0){
                return $pc[0]['id'];
            } else{
                return 0;
            }
            //call cache function
            //FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_signupcode_list');
        }
    }
}
