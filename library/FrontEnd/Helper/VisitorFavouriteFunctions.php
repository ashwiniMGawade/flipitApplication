<?php
class FrontEnd_Helper_VisitorFavouriteFunctions
{
    public static function getAllShops($popularShops)
    {
        $popularShopsHtml = '';
        foreach ($popularShops as $popularShop) {
            $shopImage =
                PUBLIC_PATH_CDN.ltrim($popularShop['shop']['logo']['imgpath'], "/") 
                . $popularShop['shop']['logo']['imgname'];
            $popularShopsHtml .=
            '<article class="block col-sm-6 col-xs-6">
                <div class="text-holder">
                    <div class="text-holder">
                        <a href="'. $popularShop['shop']['permaLink'] .'" class="img-ico">
                            <img width="132" src="' . $shopImage. '" alt="Brandkids">
                        </a>
                        <div class="text">
                            <a href="'.HTTP_PATH_LOCALE. $popularShop["shop"]["permaLink"].'" class="link">'
                            . $popularShop['shop']['name']
                            . '</a>
                            <p>
                                <a href="#" class="active-offer">'
                                . $popularShop['activeCount'] . " "
                                . FrontEnd_Helper_viewHelper::__translate('active offers').
                                '</a>
                            </p>
                            <button class="pop btn btn-default" type="button">
                                <span class="glyphicon glyphicon-heart"></span>
                                <span>'. FrontEnd_Helper_viewHelper::__translate('Favorite') . '</span>
                            </button>
                        </div>
                    </div>
                </div>
            </article>';
        }
        return $popularShopsHtml;
    }
    public static function getFavouriteShops($Shops)
    {
        $allFavoriteShopsHtml =
        '<article class="box">
            <div class="text-holder">
                <a href="#" class="img-ico">
                    <img src="'. PUBLIC_PATH .'images/logo-27.png" width="132" height="75" alt="Brandkids">
                </a>
                <div class="text">
                    <a href="#" class="link">Brandkids</a>
                    <p><a href="#" class="active-offer">10 active offers</a></p>
                    <button class="pop btn btn-default" type="button"><em>I like this</em><strong>Remove</strong></button>
                </div>
            </div>
        </article>';
        return $allFavoriteShopsHtml;
    }
}
