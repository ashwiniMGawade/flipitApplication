<?php
namespace KC\Repository;
class EditorWidget Extends \KC\Entity\EditorWidget
{
    public static function addEditorWidgetData($parameters)
    {
        $editorWidgetData = self::getEditorWigetData($type);
        if (empty($editorWidgetData)) {
            $entityManagerLocale = \Zend_Registry::get('emLocale');
            $editorWidget = new \KC\Entity\EditorWidget();
            $editorWidget->type = $parameters['type'];
            $editorWidget->description = $parameters['description'];
            $editorWidget->subtitle = $parameters['subtitle'];
            $editorWidget->created_at = new \DateTime('now');
            $editorWidget->updated_at = new \DateTime('now');
            $editorWidget->status = $parameters['actionType'];
            $editorWidget->editorId = $parameters['selecteditors'];
            $entityManagerLocale->persist($editorWidget);
            $entityManagerLocale->flush();
        } else {
            self::updateEditorWidgetData($parameters);
        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($type.'_editor_data');
        return true;
    }

    public static function updateEditorWidgetData($parameters)
    {
        if (!empty($editorId)) {
            $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $entityManagerLocale
                ->update('KC\Entity\EditorWidget', 'ew')
                ->set('ew.editorId', $parameters['selecteditors'])
                ->set('ew.description', $entityManagerLocale->expr()->literal($parameters['description']))
                ->set('ew.subtitle', $entityManagerLocale->expr()->literal($parameters['subtitle']))
                ->set('ew.status', $parameters['actionType'])
                ->where('ew.type ='.$entityManagerLocale->expr()->literal($parameters['type']))
                ->getQuery()->execute();
        }
        return true;
    }

    public static function getEditorWidgetData($type)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
            ->select('ew')
            ->from('KC\Entity\EditorWidget', 'ew')
            ->where('ew.type ='.$entityManagerLocale->expr()->literal($type));
        $editorWidgetData = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $editorWidgetData;
    }

}