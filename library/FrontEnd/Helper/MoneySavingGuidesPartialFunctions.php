<?php
class FrontEnd_Helper_MoneySavingGuidesPartialFunctions
{
    public static function getArticles($categoryWiseAllArticles)
    {
        $relatedArticles = '<div class="row articles-box">';
        foreach ($categoryWiseAllArticles as $article) {
            if (!empty($article['authorDetails'])) {
                $profileLink = HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link("link_redactie")."/"
                        . $article['authorDetails']['slug'];
                $articleUpdatedAtDate = $article['articles']['publishdate'];
                $articleUpdatedAtDate = new Zend_Date($articleUpdatedAtDate->date);
                $articleUpdatedAtDate = $articleUpdatedAtDate->get(Zend_Date::DATE_LONG);
                $authorName = FrontEnd_Helper_AuthorPartialFunctions::getAuthorName(
                    $article['authorDetails']['firstName'],
                    $article['authorDetails']['lastName']
                );
                $articleImage = !empty($article['articles']['thumbnail']) ?
                    PUBLIC_PATH_CDN.$article['articles']['thumbnail']['path'].$article['articles']['thumbnail']['name'] : '';

                if (isset($article['articles']['plusTitle']) && $article['articles']['plusTitle'] != '') {
                    $articleTitle = mb_strlen($article['articles']['plusTitle']) > 50
                        ? mb_substr($article['articles']['plusTitle'], 0, 50).'..'
                        : $article['articles']['plusTitle'];
                    $altTitle = $article['articles']['plusTitle'];
                } else {
                    $articleTitle = mb_strlen($article['articles']['title']) > 50
                        ? mb_substr($article['articles']['title'], 0, 50).'..'
                        : $article['articles']['title'];
                    $altTitle = $article['articles']['title'];
                }
                
                $articleBy = $authorName != '' ? FrontEnd_Helper_viewHelper::__translate('By') : '';
                $categoryTitleBackgroundColor = !empty($article['articles']['category'][0]['categorytitlecolor'])
                                                    ? $article['articles']['category'][0]['categorytitlecolor']
                                                    : 'e69342';
                $relatedArticles .=
                        '<article class="article col-md-3 col-sm-4 col-xs-6 ">
                            <div class="image">
                                <span class="category" style = "background :#'.$categoryTitleBackgroundColor.';">
                                    '.$article['artcileCategoryType'].'
                                </span>
                                <a href= "'.HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::getPagePermalink().'/'
                                    .$article['articles']['permalink'].'">
                                    <img class="lazy" data-original="'.$articleImage.'"
                                    width="270" height="192" alt="'.$altTitle.'" title="'.$altTitle.'">
                                    <noscript>
                                        <img src="'.$articleImage.'" width="270" height="192" alt="'.$altTitle.'" title="'.$altTitle.'">
                                    </noscript>
                                </a>    
                            </div>
                            <div class="holder">
                                <div class="box">
                                    <h2>
                                        <a href="'.HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::getPagePermalink().'/'
                                            .$article['articles']['permalink'].'">
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
        }
        echo $relatedArticles .'</div>';
    }

    public function addAuthorDetailsInArticles($categoryWiseArticles)
    {
        foreach ($categoryWiseArticles as $key => $categoryWiseArticle) {
            $categoryWiseArticles[$key]['authorDetails'] =
                KC\Repository\User::getUserDetails($categoryWiseArticle['articles']['authorid']);
            $articleCategoryType = !empty($categoryWiseArticle['articles']['category'])
                ? $categoryWiseArticle['articles']['category'][0]['name'] : '';
            $categoryWiseArticles[$key]['artcileCategoryType'] = $articleCategoryType;
        }
        
        return $categoryWiseArticles;
    }

    public static function getArticlesAccordingToDescendingOrder($articleCreatedDateAsc, $articleCreatedDateDesc)
    {
        return strtotime($articleCreatedDateDesc['publishdate']) - strtotime($articleCreatedDateAsc['publishdate']);
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
