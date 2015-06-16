<?php
defined('PUBLIC_PATH') || define('PUBLIC_PATH', dirname(dirname(dirname(__FILE__)))."/public");

// Parse the 3 xml's and add the url's to the varnish table
$main_urls = simplexml_load_file(PUBLIC_PATH.'/sitemaps/sitemap_main.xml');
if (!empty($main_urls)) {
    foreach ($main_urls as $url) {
        checkURL($url->loc);
    }
}
$bespaar_urls = simplexml_load_file(PUBLIC_PATH.'/sitemaps/sitemap_plus.xml');
if (!empty($bespaar_urls)) {
    foreach ($bespaar_urls as $url) {
        checkURL($url->loc);
    }
}
$shop_urls = simplexml_load_file(PUBLIC_PATH.'/sitemaps/sitemap_shops.xml');
if (!empty($shop_urls)) {
        foreach ($shop_urls as $url) {
                checkURL($url->loc);
        }
}

function checkURL($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_exec($ch);
    $info = curl_getinfo($ch);
    if ($info['http_code'] != "200" && $info['http_code'] != "301" ) {
        echo $url.' is '.$info['http_code']."\n\r";
    }
}
