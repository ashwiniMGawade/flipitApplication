<?php
class Widget extends BaseWidget
{
    //bellow function related with migration this code will delete when issue working on production
    public static function addDefaultWidgets()
    {
        self::truncateWidget();
        self::addNewDefaultWidgets('Author for shop', 'popularEditorWidget');
        self::addNewDefaultWidgets('News ticker for shop', 'shopLatestNewsWidget');
        self::addNewDefaultWidgets('International widget', 'getPageEditorWidget');
        self::addNewDefaultWidgets('Top 20 popular shops', 'popularShopWidget');
        self::addNewDefaultWidgets('Shop Also Viwes', 'shopsAlsoViewedWidget');
        self::addNewDefaultWidgets('Popular categories', 'popularCategoryWidget');
        self::addNewDefaultWidgets('Social codes', 'socialCodeWidget');
        self::addNewDefaultWidgets('Newsletter subscription', 'signUpWidget');
        self::addNewDefaultWidgets('Popular Offer For Plus', 'plusTopPopularOffers');
        self::addNewDefaultWidgets('Related articles plus guides', 'plusRecentlyAddedArticles');
        return true;
    }

    public static function truncateWidget()
    {
        $databaseConnection = Doctrine_Manager::getInstance()->getConnection('doctrine_site')->getDbh();
        $databaseConnection->query('SET FOREIGN_KEY_CHECKS = 0;');
        $databaseConnection->query('TRUNCATE TABLE widget');
        $databaseConnection->query('SET FOREIGN_KEY_CHECKS = 1;');
        unset($databaseConnection);
    }

    public static function addNewDefaultWidgets($widgetTitle, $functionName)
    {
        $widget = new Widget();
        $widget->title = $widgetTitle;
        $widget->function_name = $functionName;
        $widget->status = 1;
        $widget->showWithDefault = 0;
        $widget->save();
        return true;
    }
    //end code related with migration
    
    public function addWidget($params)
    {
        $w = new Widget();
        $w->title = BackEnd_Helper_viewHelper::stripSlashesFromString($params ['title']);
        $w->content = BackEnd_Helper_viewHelper::stripSlashesFromString($params ['content']);
        $w->save();
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget_list');
        return $w->id;
    }

    public function getDefaultwidgetList()
    {
        $widgetsList = Doctrine_Query::create()->select()
            ->from('widget w')
            ->where('w.userDefined=0')
            ->andWhere('w.deleted=0')
            ->andWhere('status = 1')
            ->fetchArray();
        return $widgetsList;
    }

    public function getUserDefinedWidgetList()
    {
        $widgetsList = Doctrine_Query::create()->select()
            ->from('widget w')
            ->where('w.deleted=0')
            ->andWhere('w.status=1')
            ->orderBy('title')
            ->fetchArray();
        return $widgetsList;
    }

    public static function getWidgetList($params)
    {
        $srh = @$params["searchText"] != 'undefined' ? @$params["searchText"] : '';
        $data = Doctrine_Query::create()
            ->select('w.*')
            ->from("Widget w")
            ->Where("deleted = 0")
            ->andWhere("w.title LIKE ?", "$srh%")
            ->orderBy("w.id DESC");
        $list = DataTable_Helper::generateDataTableResponse(
            $data,
            $params,
            array("__identifier" => 'w.id', 'w.id', 'w.title', 'w.content'),
            array(),
            array()
        );
        return $list;
    }

    public static function searchKeyword($keyword)
    {
        $data = Doctrine_Query::create()->select('w.title as title')
            ->from("Widget w")
            ->where("w.title LIKE ?", "$keyword%")
            ->andWhere("w.status=1")
            ->orderBy("w.title ASC")
            ->limit(5)->fetchArray();
        return $data;
    }
    public static function updateWidget($id)
    {
        $data = Doctrine_Query::create()->select('w.*')
            ->from("Widget w")
            ->where("w.id=".$id)
            ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget_list');
        return $data;
    }

    public function editWidgetRecord($params)
    {
        $content = addslashes($params['content']);
        $data = Doctrine_Query::create()
            ->update('widget w')
            ->set('w.title', "'". BackEnd_Helper_viewHelper::stripSlashesFromString($params['title'])."'")
            ->set('w.content', "'". BackEnd_Helper_viewHelper::stripSlashesFromString($content)."'")
            ->where('w.id='.$params['id']);
        $data->execute();
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget_list');
        return true;
    }
  
    public function permanentDeleteWidget($id)
    {
        if ($id) {
            $u1 = Doctrine_Core::getTable("refPageWidget")->find($id);
            $del1 = Doctrine_Query::create()->delete()
                ->from('refPageWidget pw')
                ->where("pw.widgetId=" . $id)
                ->execute();
            $u2 = Doctrine_Core::getTable("Widget")->find($id);
            $del2 = Doctrine_Query::create()->delete()
                ->delete('Widget w')
                ->where("w.id=" . $id)
                ->execute();
        } else {
            $id = null;
        }
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget_list');
        return $id;
    }

    public static function getAllUrls($id)
    {
        $data  = Doctrine_Query::create()->select("p.permaLink,w.id")
            ->from('Widget w')
            ->leftJoin("w.page p")
            ->where("w.id=? ", $id)
            ->andWhere("p.deleted=0")
            ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
        $urlsArray = array();
        if (isset($data['page']) && count($data['page']) > 0) {
            foreach ($data['page'] as $value) {
                if (isset($value['permaLink']) && strlen($value['permaLink']) > 0) {
                    $urlsArray[] = $value['permaLink'] ;
                }
            }
        }
        return $urlsArray ;
    }
}
