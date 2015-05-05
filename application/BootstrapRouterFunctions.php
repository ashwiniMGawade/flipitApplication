<?php
class BootstrapRouterFunctions
{
    public static function getPermalink()
    {
        $permalinkWithoutLeftSlash = ltrim(REQUEST_URI, '/');
        $permalink = rtrim($permalinkWithoutLeftSlash, '/');
        $routeRedirectName = KC\Repository\RouteRedirect::getRoute(HTTP_PATH.$permalink);
        $splitPermalinkFromQueryString = strstr($permalink, '?');

        self::redirectUrl($routeRedirectName, $splitPermalinkFromQueryString);

        if (!empty($splitPermalinkFromQueryString)) {
            $permalink = strstr($permalink, '?', true);
        }

        return $permalink;
    }

    public static function redirectUrl($routeRedirectName, $splitPermalinkFromQueryString)
    {
        if (count($routeRedirectName) > 0) {
            $redirectUrl = $routeRedirectName[0]['redirectto'];
            header('Location: '.$redirectUrl.$splitPermalinkFromQueryString, true, 301);
            exit();
        }
    }


    public static function splitRouteProperties($permalink, $splitRouteProperties)
    {
        if (count($splitRouteProperties) == 1) {
            $permalink = $splitRouteProperties[0];
        } else if (count($splitRouteProperties) == 2) {
            if (is_int($splitRouteProperties[0])) {
                $permalink = $splitRouteProperties[0];
            } else {
                preg_match('/^[1-3]{1}$/', $splitRouteProperties[1], $maximumIntegerNumber);
                if ($maximumIntegerNumber) {
                    $permalink = $splitRouteProperties[0];
                } else {
                    if ($splitRouteProperties[0] == LOCALE) {
                        $permalink = $splitRouteProperties[1];
                    } else {
                        $permalink = $splitRouteProperties[0]. '/'. $splitRouteProperties[1];
                    }
                }
            }
        } else if (count($splitRouteProperties) == 3) {
            preg_match('/^[1-3]{1}$/', $splitRouteProperties[2], $maximumIntegerNumber);
            if ($maximumIntegerNumber) {
                $permalink = $splitRouteProperties[2];
            } else if ($splitRouteProperties[0] == LOCALE) {
                $permalink = $splitRouteProperties[1]. '/'. $splitRouteProperties[2];
            }
        }

        return $permalink;
    }

    public static function replacePermalinkString($permalink)
    {
        $searchString = '~([a-zA-z]+.)([\?].+)~';
        $replaceString = '$1';
        preg_match($searchString, $permalink, $resultString);

        if ($resultString) {
            $permalink = preg_replace($searchString, $replaceString, $permalink);
        }

        return $permalink;
    }

    public static function setRouteForPermalink(
        $getPermalinkFromDb,
        $actualPermalink,
        $routeProperties,
        $routeObject,
        $moduleName,
        $httpScheme = ''
    ) {
        $urlArray = self::getPageUrls($getPermalinkFromDb, $actualPermalink);
        if (in_array(strtolower($routeProperties[0]), $moduleName)) {

            $urlArray['module'] = 'default';
            $urlArray['lang'] = $routeProperties[0];

            if (self::routeForDefaultModule($routeObject, $httpScheme) == true) {
                return;
            }
            $route = new Zend_Controller_Router_Route(
                $routeProperties[0] .'/'. $actualPermalink,
                $urlArray
            );
            $routeObject->addRoute('user', $route);
            return;
        } else {
            $route = new Zend_Controller_Router_Route($actualPermalink, $urlArray);
            $routeObject->addRoute('user', $route);
        }
        return;
    }

    public static function routeForDefaultModule($routeObject, $httpScheme = '')
    {
        if (HTTP_HOST == $httpScheme.'.kortingscode.nl') {
            $routeObject->addRoute(
                'kortingscode',
                new Zend_Controller_Router_Route(
                    '/:lang/*',
                    array(
                        'controller' => ':lang',
                        'module' => 'default'
                    )
                )
            );
            return true;
        }
    }

    public static function getPageUrls($getPermalinkFromDb)
    {
        // get the page detail from page table on the basis of permalink
        $pageDetail = KC\Repository\RoutePermalink::getPageProperties(
            strtolower($getPermalinkFromDb[0]['permalink'])
        );
        //check if there exist page belongs to the permalink then append the
        //id of that page with actual URL
        if (!empty($pageDetail)) {
            $getPermalinkFromDb[0]['exactlink'] =
            $getPermalinkFromDb[0]['exactlink'].'/attachedpage/'.$pageDetail[0]['id'];
        }
        $permalinkUrl = explode('/', $getPermalinkFromDb[0]['exactlink']);
        $urlArray = array(
                'controller' => isset($permalinkUrl[0]) ? $permalinkUrl[0] : '',
                'action'     => isset($permalinkUrl[1]) ? $permalinkUrl[1] : ''
        );
        if (!empty($permalinkUrl)) {
            for ($index = 2; $index < count($permalinkUrl); $index++) {
                if ($index % 2 == 0) {
                    if (isset($permalinkUrl[$index]) && isset($permalinkUrl[$index+1])) {
                        $urlArray[$permalinkUrl[$index]] = $permalinkUrl[$index+1];
                    }
                }
            }
        }
        if (!empty($pageDetail)) {
            $urlArray['attachedpage'] = $pageDetail[0]['id'];
        }
        return $urlArray;
    }

    public static function redirectionForOldWebsiteUrls($permalink)
    {
        // for 301 redirections of old indexed pages
        if (is_array($permalink)) {
            $permalinkType = explode('/', $permalink[0]);
        } else {
            $permalinkType = explode('/', $permalink);
        }

        switch ($permalinkType[0]) {
            case 'kortingen':
                $newPermalink = HTTP_PATH.'nieuw';
                header('Location: '.$newPermalink, true, 301);
                die();
                break;
            case 'shops':
                $newPermalink = HTTP_PATH.'store';
                header('Location: '.$newPermalink, true, 301);
                die();
                break;
            case 'producten':
                $newPermalink = HTTP_PATH.'categorieen';
                header('Location: '.$newPermalink, true, 301);
                die();
                break;
            case 'rssfeeds':
            case 'get-action-ratings':
            case 'get-shop-rating':
            case 'get-shop-reviews':
            case 'get-shop-ratings':
            case 'dynamics':
            case '2010':
            case '2011':
            case '2012':
                $newPermalink = HTTP_PATH;
                header('Location: '.$newPermalink, true, 301);
                die();
                break;
            default:
                break;
        }

    }

    public static function setRouteIfModuelNotExist($routeObject, $httpScheme)
    {
        if (HTTP_HOST == $httpScheme.'.kortingscode.nl') {
            $routeObject->addRoute(
                'kortingscode_error',
                new Zend_Controller_Router_Route(
                    'kortingscode_error',
                    array(
                        'action' => "error",
                        'controller' => "error"
                    )
                )
            );
            return;
        }
    }

    public static function setRouteForLocale($request, $routeObject, $routeProperties)
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/routes.ini', 'production');

        self::routeForDefaultModule($routeObject);

        if ($request->isXmlHttpRequest()) {
            $routeObject->addRoute(
                'xmlHttp',
                new Zend_Controller_Router_Route(
                    '/:lang/:@controller/:@action/*',
                    array(
                        'action' => ':action',
                        'controller' => ':controller',
                        'module' => 'default'
                    )
                )
            );
            foreach ($config->routes as $key => $r) {
                switch ($key) {
                    case 'usermenu':
                    case 'userwidget':
                    case 'userfooter':
                    case 'usersignup':
                        $module  = isset($r->defaults->module) ? $r->defaults->module : 'default';
                        $routeObject->addRoute(
                            'langmod_'. $key,
                            new Zend_Controller_Router_Route(
                                '/:lang/'.$r->route,
                                array(
                                    'lang' => ':lang',
                                    'action' => $r->defaults->action,
                                    'controller' => $r->defaults->controller,
                                    'module' => $module,
                                )
                            )
                        );
                        break;
                }
            }

        }

        foreach ($config->routes as $key => $r) {
            if ($r->type != 'Zend_Controller_Router_Route_Regex') {
                $module  = isset($r->defaults->module) ? $r->defaults->module : 'default';
                $page = isset($r->defaults->page) ? 1 : null;
                switch ($key) {
                    case 'o2feed':
                        if ($routeProperties[0] == 'pl' || $routeProperties[0] == 'in') {
                            $routeObject->addRoute(
                                'langmod_'. $key,
                                new Zend_Controller_Router_Route(
                                    '/:lang/'.$r->route,
                                    array(
                                        'lang' => ':lang',
                                        'action' => 'top10.xml',
                                        'controller' => 'o2feed'
                                    )
                                )
                            );
                        }
                        break;
                    default:
                        $routeObject->addRoute(
                            'langmod_'. $key,
                            new Zend_Controller_Router_Route(
                                '/:lang/'.$r->route,
                                array(
                                    'lang' => ':lang',
                                    'action' => $r->defaults->action,
                                    'controller' => $r->defaults->controller,
                                    'module' => $module,
                                    'page' => $page
                                )
                            )
                        );
                        break;
                }
            } else {
                $routeLanguage = new Zend_Controller_Router_Route(':lang', array('lang' => ':lang'));
                $baseChain = new Zend_Controller_Router_Route(
                    '@link_redactie',
                    array(
                        'controller' => 'about',
                        'module' => 'default'
                    )
                );
                switch ($key) {
                    case 'profilepage':
                        $page = new Zend_Controller_Router_Route_Regex(
                            '^(\d?+)$',
                            array('page' => '1', 'action' => 'index'),
                            array(1 => 'page'),
                            '%d'
                        );

                        self::addRouteForChain($routeLanguage, $baseChain, $page, $routeObject, 'redactier_page');

                        break;
                    case 'aboutdefault':
                        // validate slug parameter with regex i.e name of redactie
                        $slug = new Zend_Controller_Router_Route_Regex(
                            '^([a-zA-Z]+(?:-[a-zA-Z]+)?+)+$',
                            array( 'slug' => '','action' => 'profile'),
                            array( 1 => 'slug' ),
                            '%d'
                        );

                        self::addRouteForChain($routeLanguage, $baseChain, $slug, $routeObject, "redactier_slug");
                        
                        break;
                }

            }
        }
        return;
    }

    public static function addRouteForChain($routeLanguage, $baseChain, $slug, $routeObject, $routeName)
    {
        $chainedRouteSlug = new Zend_Controller_Router_Route_Chain();
        $slugChained = $chainedRouteSlug->chain($routeLanguage)->chain($baseChain)->chain($slug);
        $routeObject->addRoute($routeName, $slugChained);
        return true;
    }

    public static function errorRouteForFlipit($routeObject)
    {
        $routeObject->addRoute(
            'marktplaatsfeed',
            new Zend_Controller_Router_Route(
                'marktplaatsfeed',
                array(
                    'action' => "error",
                    'controller' => "error"
                )
            )
        );
        $routeObject->addRoute(
            'metronieuws',
            new Zend_Controller_Router_Route(
                'metronieuws/top10.xml',
                array(
                    'action' => "error",
                    'controller' => "error"
                )
            )
        );
        $routeObject->addRoute(
            'sargassofeed',
            new Zend_Controller_Router_Route(
                'sargassofeed',
                array(
                    'action' => "error",
                    'controller' => "error"
                )
            )
        );
        $routeObject->addRoute(
            'whitelabel',
            new Zend_Controller_Router_Route(
                'whitelabel/top10.xml',
                array(
                       'action' => "error",
                       'controller' => "error"
                )
            )
        );
    }

    public static function getRouteFromRuleFile($routeObject)
    {
        $routeObject->addConfig(
            new Zend_Config_Ini(
                APPLICATION_PATH.'/configs/routes.ini',
                'production'
            ),
            'routes'
        );
        return;
    }
}
