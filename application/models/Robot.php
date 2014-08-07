<?php

class Robot extends BaseRobot
{
    public function getRobotTextFileInformation($websiteId = '')
    {
        $fileInformation = Doctrine_Query::create()
            ->select('*')
            ->from('Robot r')
            ->where('r.id = '.$websiteId)
            ->fetchArray();
        return $fileInformation;
    }

    public function updateFileInformation($robotWebsiteId = '', $content = '')
    {
        Doctrine_Query::create()
            ->update('Robot')
            ->set('content', '"'.mysqli_real_escape_string(
                FrontEnd_Helper_viewHelper::getDbConnectionDetails(),
                $content
            ).'"')
            ->where('id = "'.$robotWebsiteId.'"')
            ->execute();
        return true;
    }
}
