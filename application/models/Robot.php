<?php

class Robot extends BaseRobot
{
    public function getRobotTextFileInformation($websiteId = '')
    {
        $robotsTextFileInformation = Doctrine_Query::create()
            ->select('*')
            ->from('Robot r')
            ->where('r.id = '.$websiteId)
            ->fetchArray();
        return $robotsTextFileInformation;
    }

    public function updateFileInformation($robotWebsiteId = '', $robotsTextFileContent = '')
    {
        Doctrine_Query::create()
            ->update('Robot')
            ->set('content', '"'.mysqli_real_escape_string(
                FrontEnd_Helper_viewHelper::getDbConnectionDetails(),
                $robotsTextFileContent
            ).'"')
            ->where('id = "'.$robotWebsiteId.'"')
            ->execute();
        return true;
    }
}
