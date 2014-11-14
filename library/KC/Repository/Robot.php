<?php
namespace KC\Repository;
class Robot Extends \KC\Entity\Robot
{
    public function getRobotTextFileInformation($websiteId = '')
    {
        $entityManagerUser = \Zend_Registry::get('emUser')->createQueryBuilder();
            $query = $entityManagerUser
            ->select('r')
            ->from('KC\Entity\Robot', 'r')
            ->where('r.id = '.$websiteId);
        $robotsTextFileInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $robotsTextFileInformation;
    }

    public function updateFileInformation($robotWebsiteId = '', $robotsTextFileContent = '')
    {
        $robotFileContentExistsOrNot = self::getRobotTextFileInformation($robotWebsiteId);
        $entityManagerUser = \Zend_Registry::get('emUser')->createQueryBuilder();
        if (!empty($robotFileContentExistsOrNot)) {
            echo $query = $entityManagerUser
            ->update('KC\Entity\Robot', 'r')
            ->set('r.content', "'".mysqli_real_escape_string(
                \FrontEnd_Helper_viewHelper::getDbConnectionDetails(),
                $robotsTextFileContent
            )."'")
            ->where('r.id ='.$robotWebsiteId)
            ->getQuery();
            $query->execute();
        } else {
            $robot = new KC\Entity\Robot();
            $robot->website = $robotWebsiteId == 1 ? 'Flipit' : 'Kortingscode';
            $robot->content = mysqli_real_escape_string(
                \FrontEnd_Helper_viewHelper::getDbConnectionDetails(),
                $robotsTextFileContent
            );
            $robot->deleted = 0;
            $robot->created_at = new \DateTime('now');
            $robot->updated_at = new \DateTime('now');
            $entityManagerUser->persist($robot);
            $entityManagerUser->flush();
        }
        return true;
    }
}