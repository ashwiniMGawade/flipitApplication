<?php
namespace KC\Repository;
class EditorWidget Extends \KC\Entity\EditorWidget
{
    public static function addEditorWigetData($editorId, $description, $subTitle, $type, $status)
    {
        $editorWidgetData = self::getEditorWigetData($type);
        if (empty($editorWidgetData)) {
            $entityManagerLocale = \Zend_Registry::get('emLocale');
            $editorWidget = new \KC\Entity\EditorWidget();
            $editorWidget->type = $type;
            $editorWidget->description = $description;
            $editorWidget->subtitle = $subTitle;
            $editorWidget->created_at = new \DateTime('now');
            $editorWidget->updated_at = new \DateTime('now');
            $editorWidget->status = $status;
            $editorWidget->editorId = $editorId;
            $entityManagerLocale->persist($editorWidget);
            $entityManagerLocale->flush();
        } else {
            self::updateEditorWigetData($editorId, $description, $subTitle, $type, $status);
        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($type.'_editor_data');
        return true;
    }

    public static function updateEditorWigetData($editorId, $description, $subTitle, $type, $status)
    {
        if (!empty($editorId)) {
            $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $entityManagerLocale
                ->update('KC\Entity\EditorWidget', 'ew')
                ->set('ew.editorId', $editorId)
                ->set('ew.description', "'$description'")
                ->set('ew.subtitle', "'$subTitle'")
                ->set('ew.status', $status)
                ->where('ew.type ='.$entityManagerLocale->expr()->literal($type))
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