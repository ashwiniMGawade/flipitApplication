<?php
class Settings extends BaseSettings
{

    const EMAIL_LIGHT_BOX = "email_light_box";
    const FOOTER = "footer";
    const SPECIAL = "special";
    const ABOUT_1 = "about_1";
    const ABOUT_2 = "about_2";
    const ABOUT_3 = "about_3";

    const SEENIN_1 = "seenin_1";
    const SEENIN_2 = "seenin_2";
    const SEENIN_3 = "seenin_3";
    const SEENIN_4 = "seenin_4";
    const SEENIN_5 = "seenin_5";
    const SEENIN_6 = "seenin_6";
    #####################################################
    ######### REFACTORED CODE ###########################
    #####################################################
    public static function getAboutSettings($settingsName)
    {
        $aboutPageSettings = Doctrine_Query::create()->select('s.name,s.value')
        ->from("Settings s")
        ->andWhere("s.name LIKE ?", "$settingsName%")
        ->fetchArray();
        return $aboutPageSettings ;
    }

    public static function getEmailSettings($sendersFieldName)
    {
        $emailSettings = Doctrine_Query::create()->select('s.value')
        ->from("Settings s")
        ->where("s.name = '".$sendersFieldName."'")
        ->fetchArray();
        return !empty($emailSettings) ? $emailSettings[0]['value'] : '';
    }

    public static function updateSendersSettings($sendersFieldName, $sendersValue)
    {
        $getSettings = self::getEmailSettings($sendersFieldName);
        if (!empty($getSettings)) {
            Doctrine_Query::create()
                ->update('Settings')
                ->set("value", '"'.$sendersValue.'"')
                ->where('name = "'.$sendersFieldName.'"')
                ->execute();
        } else {
            $setting = new Settings();
            $setting->name = $sendersFieldName;
            $setting->value = $sendersValue;
            $setting->save();
        }
        return true;
    }
 
    #####################################################
    ######### END REFACTORED CODE #######################
    #####################################################
    /**
     * to get a particular setting by its
     * @param string settings name
     * @return mixed setting value or false
     * @author Er.kundal
    */

    public static function getSettings($name)
    {

        $data = Doctrine_Core::getTable("Settings")->findOneBy('name',$name);
        if($data)
        return $data->value;
        // return $data ;

    }

    /**
     * delete about tab
     * @param integer $id
     * @param integer $position
     * @author Er.kundal
     * @version 1.0
     */
    public static function removesettingabouttab($id)
    {
        if ($id) {
            //delete about tab from list
            $name = "about_";
            $adel = Doctrine_Query::create()->delete('settings')
            ->where('value=' . $id)->andWhere("name LIKE ?", "$name%")->execute();
            //call cache function
            return true;
        }else{
            return false;
        }

    }

    /**
     * to gell the settings
     * @return array $data settings array
     * @author Er.kundal
     */

    public static function getAllSettings()
    {
        $getAll = Doctrine_Core::getTable("Settings")->findAll(Doctrine::HYDRATE_ARRAY);
        $data = array() ;
        foreach($getAll as $val) {
            $data[$val['name']] = $val['value'];
        }
        return $data ;
    }

    /**
     * to set a paticluar settings and its value
     * @param string $name
     * @param string $value
     * @author Er.kundal
     */
    public static function setSettings($name,$value)
    {
        $Q = Doctrine_Query::create()
                    ->update('Settings')->set("value",$value)->where('name = ?',$name);
        $Q->execute();
    }

}
