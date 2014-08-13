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
        $robotFileContentExistsOrNot = self::getRobotTextFileInformation($robotWebsiteId);
        if (!empty($robotFileContentExistsOrNot)) {
            Doctrine_Query::create()
            ->update('Robot')
            ->set('content', '"'.mysqli_real_escape_string(
                FrontEnd_Helper_viewHelper::getDbConnectionDetails(),
                $robotsTextFileContent
            ).'"')
            ->where('id = "'.$robotWebsiteId.'"')
            ->execute();
        } else {
            $this->website = $robotWebsiteId == 1 ? 'Flipit' : 'Kortingscode';
            $this->content = $robotsTextFileContent;
            $this->deleted = 0;
            $this->save();
        }
         return true;
    }
}
