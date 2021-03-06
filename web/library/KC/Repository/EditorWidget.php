<?php
namespace KC\Repository;
class EditorWidget extends \Core\Domain\Entity\EditorWidget
{
    public static function addEditorWidgetData($parameters)
    {
        $editorWidgetData = self::getEditorWidgetData($parameters['type']);
        if (empty($editorWidgetData)) {
            $entityManagerLocale = \Zend_Registry::get('emLocale');
            $editorWidget = new \Core\Domain\Entity\EditorWidget();
            $editorWidget->type = \FrontEnd_Helper_viewHelper::sanitize($parameters['type']);
            $editorWidget->description = \FrontEnd_Helper_viewHelper::sanitize($parameters['description']);
            $editorWidget->subtitle = \FrontEnd_Helper_viewHelper::sanitize($parameters['subtitle']);
            $editorWidget->created_at = new \DateTime('now');
            $editorWidget->updated_at = new \DateTime('now');
            $editorWidget->status = \FrontEnd_Helper_viewHelper::sanitize($parameters['actionType']);
            $editorWidget->editorId = \FrontEnd_Helper_viewHelper::sanitize($parameters['selecteditors']);
            $entityManagerLocale->persist($editorWidget);
            $entityManagerLocale->flush();
        } else {
            self::updateEditorWidgetData($parameters);
        }
        $cacheKey = \FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($parameters['type']);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($cacheKey.'_editor_data');
        return true;
    }

    public static function updateEditorWidgetData($parameters)
    {
        if (!empty($parameters['selecteditors'])) {
            $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $entityManagerLocale
                ->update('\Core\Domain\Entity\EditorWidget', 'ew')
                ->set('ew.editorId', $parameters['selecteditors'])
                ->set('ew.description', $entityManagerLocale->expr()->literal($parameters['description']))
                ->set('ew.subtitle', $entityManagerLocale->expr()->literal($parameters['subtitle']))
                ->set('ew.status', $parameters['actionType'])
                ->where('ew.type ='.$entityManagerLocale->expr()->literal($parameters['type']))
                ->getQuery()->execute();
        }
        return true;
    }

    public static function getEditorWidgetData($pageType)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
            ->select('ew')
            ->from('\Core\Domain\Entity\EditorWidget', 'ew')
            ->where('ew.type ='.$entityManagerLocale->expr()->literal($pageType));
        $editorWidgetData = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $editorWidgetData;
    }

}