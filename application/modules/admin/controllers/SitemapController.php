<?php

/**
 * Sitemap Generation
 *
 * @author Raman
 *
 */
class Admin_SitemapController extends Zend_Controller_Action
{
    public function init()
    {
        ini_set ("display_errors", "1");
        error_reporting(E_ALL);
        /*
         * Initialize action controller here
         */
    }

    public function indexAction()
    {
        //setting to no time limit,
        set_time_limit(0);
        //declaring class instance
        $sitemap = new PHPSitemap_sitemap();

        $locale = '';
        if(isset($_COOKIE['locale']) && ($_COOKIE['locale']) != 'en') {

            $locale = $_COOKIE['locale'];

        }
        $pathToXMLFile = ROOT_PATH;
        $domain = 'http://www.'.$pathToXMLFile;
        //submitting site map to Google, Yahoo, Bing, Ask and Moreover services
        //$sitemap->ping("http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);

        //Create robot.txt file

        $robotTextContent = "User-agent: *
                            Disallow:/info/
                            Disallow:/rssfeed/
                            Disallow:/out/
                            Disallow:/zoeken/
                            Disallow:/admin/
                            Sitemap: http://www.kortingscode.nl/public/sitemaps/sitemap_main.xml
                            Sitemap: http://www.kortingscode.nl/public/sitemaps/sitemap_shops.xml
                            Sitemap: http://www.kortingscode.nl/public/sitemaps/sitemap_plus.xml

        ";
        $robotTextFile = $pathToXMLFile."robots.txt";
        $robotTxtHandle = fopen($robotTextFile, 'w');
        fwrite($robotTxtHandle, $robotTextContent);

        //generating sitemap shops
        $shopmap = $sitemap->generate_shops_sitemap($locale);
        $mainDir = $pathToXMLFile."sitemaps/";
        $shopFile = $pathToXMLFile."sitemaps/sitemap_shops.xml";

        if(!file_exists($mainDir))
            mkdir($mainDir, 776, TRUE);


        $shopHandle = fopen($shopFile, 'w');
        fwrite($shopHandle, $shopmap);

        echo "<br>";
        print "Sitemap for Online shops has been created successfully!!!";
        fclose($shopHandle);

        //generating sitemap pluss
        $guidemap = $sitemap->generateGuidesSitemap($locale);
        $guideFile = $pathToXMLFile."sitemaps/sitemap_plus.xml";

        $guideHandle = fopen($guideFile, 'w');
        fwrite($guideHandle, $guidemap);

        echo "<br>";
        print "Sitemap for Guides has been created successfully!!!";
        fclose($guideHandle);

        //generating Main sitemap
        $mainmap = $sitemap->generate_main_sitemap($locale);
        $mainFile = $pathToXMLFile."sitemaps/sitemap_main.xml";

        $mainHandle = fopen($mainFile, 'w');
        fwrite($mainHandle, $mainmap);

        echo "<br>";
        print "Sitemap for Guides has been created successfully!!";
        fclose($mainHandle);
        die();

        }










}
