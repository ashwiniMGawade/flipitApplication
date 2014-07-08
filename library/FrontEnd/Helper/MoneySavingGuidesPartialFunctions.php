<?php
class FrontEnd_Helper_MoneySavingGuidesPartialFunctions
{
    public static function getArticles($articles)
    {
        $relatedArticles = '<div class="row articles-box">';
        $articleCounter = 1;
        foreach ($articles as $article) {
            if ($articleCounter == 1 || $articleCounter == 5) {
                $articleClass = 'same-height-left';
            } else if ($articleCounter == 4 || $articleCounter == 8) {
                $articleClass = 'same-height-right';
            } else {
                $articleClass = '';
            }

            $profileLink = HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link("link_redactie")."/"
                    . $article['authorDetails']['slug'];
            $articleUpdatedAtDate = new Zend_Date($article['created_at']);
            $articleUpdatedAtDate = $articleUpdatedAtDate->get(Zend_Date::DATE_LONG);
            $articleAuthorName = explode(' ', $article['authorname']);
            $authorFirstName = isset($articleAuthorName[0])? $articleAuthorName[0] : '';
            $authorLastName =  isset($articleAuthorName[1])? $articleAuthorName[1] : '';
            $authorName = FrontEnd_Helper_AuthorPartialFunctions::getAuthorName($authorFirstName, $authorLastName);
            $relatedArticles .=
                    '<article class="article col-md-3 col-sm-4 col-xs-6 '.$articleClass.'"  style="height: 361px;">
                        <div class="image">
                            <span class="category">
                                '.FrontEnd_Helper_viewHelper::__translate($article['type']).'
                            </span>
                            <a href= "'.HTTP_PATH_LOCALE.'plus/'.$article['permalink'].'">
                                <img
                                src="'.PUBLIC_PATH_CDN.$article['thumbnail']['path']
                                .$article['thumbnail']['name'].'"
                                width="270" height="192" alt="'.$article['title'].'">
                            </a>    
                        </div>
                        <div class="holder">
                            <div class="box">
                                <h2>
                                    <a href="'.HTTP_PATH_LOCALE.'plus/'.$article['permalink'].'">
                                       '.$article['title'].'
                                    </a>
                                </h2>
                                <div class="meta">
                                    <span class="author">'.FrontEnd_Helper_viewHelper::__translate('By').'
                                        <a href="'.$profileLink.'">'.$authorName.'</a>
                                    </span>
                                    <em class="date">'.$articleUpdatedAtDate.'</em>
                                </div>
                            </div>
                            <a href="'.HTTP_PATH_LOCALE.'plus/'.$article['permalink'].'" class="more">
                                '.FrontEnd_Helper_viewHelper::__translate('continue').' ›
                            </a>
                        </div>
                    </article>';
            ++$articleCounter;
        }
        echo $relatedArticles .'</div>';
    }

    public function addAuthorDetailsInArticles($categoryWiseArticles, $type)
    {

        foreach ($categoryWiseArticles[$type] as $key => $categoryWiseArticle) {
            $categoryWiseArticles[$type][$key]['authorDetails'] =
                User::getUserDetails($categoryWiseArticle['authorid']);
            $categoryWiseArticles[$type][$key]['type'] = $type;
        }

        return $categoryWiseArticles[$type];
    }

    public static function getArticlesAccordingToDescendingOrderFunction($articleCreatedDateAsc, $articleCreatedDateDesc)
    {
        return strtotime($articleCreatedDateDesc['created_at']) - strtotime($articleCreatedDateAsc['created_at']);
    }
}
