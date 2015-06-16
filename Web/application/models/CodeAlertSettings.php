<?php

class CodeAlertSettings extends BaseCodeAlertSettings
{

    public static function saveCodeAlertSettings($codeAlertEmailSubject, $codeAlertEmailHeader)
    {
        $codeAlertInformation = Doctrine_Query::create()
            ->select("*")
            ->from("CodeAlertSettings")
            ->where('id = 1')
            ->fetchArray();
            
        if (empty($codeAlertInformation)) {
            $codeAlertQueue = new CodeAlertSettings();
            $codeAlertQueue->email_subject = $codeAlertEmailSubject;
            $codeAlertQueue->email_header = $codeAlertEmailHeader;
            $codeAlertQueue->save();
        }
        
        Doctrine_Query::create()->update('CodeAlertSettings')
            ->set('email_subject', "'".$codeAlertEmailSubject."'")
            ->set('email_header', "'".$codeAlertEmailHeader."'")
            ->where('id=1')
            ->execute();

        return true;
    }

    public static function saveCodeAlertEmailHeader($codeAlertSettingsParameters)
    {
        $codeAlertInformation = Doctrine_Query::create()
            ->select()
            ->from("CodeAlertSettings")
            ->where('id = 1')
            ->fetchArray();
            
        if (empty($codeAlertInformation)) {
            $codeAlertQueue = new CodeAlertSettings();
            $codeAlertQueue->email_header = $codeAlertSettingsParameters['val'];
            $codeAlertQueue->save();
        }
        Doctrine_Query::create()->update('CodeAlertSettings')
            ->set('email_header', "'".$codeAlertSettingsParameters['data']."'")
            ->where('id=1')
            ->execute();

        return true;
    }

    public static function getCodeAlertSettings()
    {
        $codeAlertInformation = Doctrine_Query::create()
            ->select('*')
            ->from('CodeAlertSettings')
            ->where('id=1')
            ->fetchArray();
        return $codeAlertInformation;
    }
}
