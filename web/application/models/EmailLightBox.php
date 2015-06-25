<?php

/**
 * EmailLightBox
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class EmailLightBox extends BaseEmailLightBox
{


    /**
     *  return mixed lightbox content array or false if doesn't exists!
     *  @author blal
     */

    public static function getLigthBoxContent()
    {

        $retVal = self::checkLightBoxSetting() ;
        if($retVal) {
            # create object of previous data
            $lightBox = Doctrine_Core::getTable("EmailLightBox")
                       ->findOneBy( "id" , $retVal)->toArray() ;

             //print_r($lightBox);die;
             return $lightBox ;
        }
        return false ;

    }

    /**
     *
     * @param array request array  saving emailigth box
     * @return integer id
     * @author blal
     */
    public static function update($params)
    {
        $retVal = self::checkLightBoxSetting() ;
        # check if it has integer id of email light box
        if($retVal) {
            # create object of previous data
            $lightBox = Doctrine_Core::getTable("EmailLightBox")->find($retVal) ;

        } else {
            # new object
            $lightBox = new EmailLightBox() ;
        }
        //$lightBox->status = ( isset($params['status'] ) && $params['status']== 'on' )  ? 1 : 0 ;

        if( isset( $params['status'])) {

            if( $params['status'] == 1) {
                $lightBox->status = 1;

            } else {

                $lightBox->status = 0;
           }

        } else 	{
            $this->status = 1 ;
        }
        $lightBox->title = $params['title'] ;
        $lightBox->content = $params['content'] ;
        $lightBox->save();

        if(! $retVal) {
            self::newLightBboxSetting($lightBox->id);
        }
        //call cache function
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_emaillightbox_list');
        return $lightBox->id ;

    }

    /**
     * check email lightbox is exist or not
     * by getings its setting value
     * @return mixed setting value or false
     * @author blal
     */

    public static function checkLightBoxSetting()
    {
        return Settings::getSettings(Settings::EMAIL_LIGHT_BOX ) ;
    }

    /**
     *  save new email lightbox settings
     *  @param $id integer lightbox id
     *  @author blal
     */

    public static function newLightBboxSetting($id)
    {

        $settings =  new Settings();
        $settings->name =  Settings::EMAIL_LIGHT_BOX ;
        $settings->value = $id ;
        $settings->save();
        //call cache function
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_emaillightbox_list');
    }

    /**
     * change status of email lightbox
     * @param array $params
     * @author blal
     * @version 1.0
     */

    public static function changeStatus($params)
    {
        $status = $params['status'] == 'offline' ? '0' : '1';
        $q = Doctrine_Query::create()->update('EmailLightBox')
                                     ->set('status', $status)
                                     ->where('id=?', $params['id'])
                                     ->execute();
    }
}