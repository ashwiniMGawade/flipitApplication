<?php
namespace KC\Repository;
class EditorBallontext Extends \KC\Entity\EditorBallontext
{
    public static function deletetext($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->delete('KC\Entity\EditorBallontext', 'ebt')
            ->where('ebt.id ='.$id)
            ->getQuery();
        $query->execute();
        return true;
    }

    public static function getEditorText($shopId)
    {
        if (!empty($shopId)) {
            $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $entityManagerLocale
                ->select('ebt.ballontext')
                ->from('KC\Entity\EditorBallontext', 'ebt')
                ->where('ebt.shop ='.$shopId);
            $editorTextInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            return $editorTextInformation;
        } else {
            $editorTextInformation = array();
        }
        return $editorTextInformation;
    }
}