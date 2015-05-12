<?php
class PageWidgets extends BasePageWidgets
{
    public static function savePageWidgets($widgetsList)
    {
        $widgetsCategories = self::widgetCategories();
        foreach ($widgetsCategories as $widgetType => $widgetsCategory) {
            $position = 1;
            foreach ($widgetsList as $widget) {
                $pageWidgets = new PageWidgets();
                $pageWidgets->widget_type = $widgetType;
                $pageWidgets->position = $position;
                $pageWidgets->widgetId = $widget['id'];
                $pageWidgets->save();
                $position++;
            }
        }
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
        return $widgetCategories;
    }
}
