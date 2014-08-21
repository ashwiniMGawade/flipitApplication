<?php
class FrontEnd_Helper_MoneySavingGuidesPartialFunctions
{
    public static function getArticles($categoryWiseAllArticles)
    {
        $relatedArticles = '<div class="row articles-box">';
        foreach ($categoryWiseAllArticles as $article) {
            $profileLink = HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link("link_redactie")."/"
                    . $article['authorDetails']['slug'];
            $articleUpdatedAtDate = new Zend_Date($article['publishdate']);
            $articleUpdatedAtDate = $articleUpdatedAtDate->get(Zend_Date::DATE_LONG);
            $authorName = FrontEnd_Helper_AuthorPartialFunctions::getAuthorName(
                $article['authorDetails']['firstName'],
                $article['authorDetails']['lastName']
            );
            $articleImage = !empty($article['thumbnail']) ?
                PUBLIC_PATH_CDN.$article['thumbnail']['path'].$article['thumbnail']['name'] : '';
            $articleTitle = mb_strlen($article['title']) > 50 ?
                                        mb_substr($article['title'], 0, 50).'..' : $article['title'];
            $articleBy = $authorName != '' ? FrontEnd_Helper_viewHelper::__translate('By') : '';
            $categoryTitleBackgroundColor = !empty($article['articlecategory'][0]['categorytitlecolor'])
                                                ? $article['articlecategory'][0]['categorytitlecolor']
                                                : 'e69342';
            $relatedArticles .=
                    '<article class="article col-md-3 col-sm-4 col-xs-6 ">
                        <div class="image">
                            <span class="category" style = "background :#'.$categoryTitleBackgroundColor.';">
                                '.$article['artcileCategoryType'].'
                            </span>
                            <a href= "'.HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::getPagePermalink().'/'
                                .$article['permalink'].'">
                                <img class="lazy" data-original="'.$articleImage.'"
                                width="270" height="192" alt="'.$article['title'].'">
                                <noscript>
                                    <img src="'.$articleImage.'" width="270" height="192" alt="'.$article['title'].'">
                                </noscript>
                            </a>    
                        </div>
                        <div class="holder">
                            <div class="box">
                                <h2>
                                    <a href="'.HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::getPagePermalink().'/'
                                        .$article['permalink'].'">
                                       '.$articleTitle.'
                                    </a>
                                </h2>
                            </div>
                            <div class="meta">
                                <span class="author">'.$articleBy.'
                                    <a href="'.$profileLink.'">'.$authorName.'</a>
                                </span>
                                <em class="date">'.$articleUpdatedAtDate.'</em>
                            </div>
                        </div>
                    </article>';
        }
        echo $relatedArticles .'</div>';
    }

    public function addAuthorDetailsInArticles($categoryWiseArticles)
    {
        $artcileCategoryTypes = array_keys($categoryWiseArticles);
        foreach ($artcileCategoryTypes as $artcileCategoryType) {
            foreach ($categoryWiseArticles[$artcileCategoryType] as $key => $categoryWiseArticle) {
                $categoryWiseArticles[$artcileCategoryType][$key]['authorDetails'] =
                    User::getUserDetails($categoryWiseArticle['authorid']);
                $categoryWiseArticles[$artcileCategoryType][$key]['artcileCategoryType'] = $artcileCategoryType;
            }
        }
        return $categoryWiseArticles;
    }

    public static function getArticlesAccordingToDescendingOrder($articleCreatedDateAsc, $articleCreatedDateDesc)
    {
        return strtotime($articleCreatedDateDesc['created_at']) - strtotime($articleCreatedDateAsc['created_at']);
    }

    public function excludeSelectedArticle($allArticlesArray, $selectedArticleId)
    {
        $excludedArticles = array();
        foreach ($allArticlesArray as $article) {
            if ($article['id'] != $selectedArticleId) {
                $excludedArticles[] = $article;
            }
        }
        return $excludedArticles;
    }
}
