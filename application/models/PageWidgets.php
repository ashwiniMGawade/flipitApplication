<?php
class PageWidgets extends BasePageWidgets
{
    public static function savePageWidgets($widgetsList)
    {
        self::truncatePageWidgets();
        $widgetsCategories = self::widgetCategories();
        foreach ($widgetsCategories as $widgetType => $widgetsCategory) {
            $position = 1;
            foreach ($widgetsList as $widget) {
                $pageWidgets = new PageWidgets();
                $pageWidgets->widget_type = FrontEnd_Helper_viewHelper::sanitize($widgetType);
                $pageWidgets->position = FrontEnd_Helper_viewHelper::sanitize($position);
                $pageWidgets->widgetId = FrontEnd_Helper_viewHelper::sanitize($widget['id']);
                $pageWidgets->save();
                $position++;
            }
        }
    }

    public static function truncatePageWidgets()
    {
        $databaseConnection = Doctrine_Manager::getInstance()->getConnection('doctrine_site')->getDbh();
        $databaseConnection->query('SET FOREIGN_KEY_CHECKS = 0;');
        $databaseConnection->query('TRUNCATE TABLE page_widgets');
        $databaseConnection->query('SET FOREIGN_KEY_CHECKS = 1;');
        unset($databaseConnection);
    }

    public static function widgetCategories()
    {
        $widgetCategories = array();
        $widgetCategories['money-shops'] = 'Money shops';
        $widgetCategories['no-money-shops'] = 'No money shops';
        $widgetCategories['categories'] = 'Categories';
        $widgetCategories['special-page'] = 'Special page';
        $widgetCategories['plus-page'] = 'Plus Page';
        $widgetCategories['faq-pages'] = 'FAQ pages';
        $widgetCategories['info-pages'] = 'Info pages';
        $widgetCategories['all-shop-page'] = 'All shoppage';
        $widgetCategories['top-20'] = 'Top-20';
        $widgetCategories['newest-code'] = 'Newest code';
        return $widgetCategories;
    }
}
