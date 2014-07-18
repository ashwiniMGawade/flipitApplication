<?php
class FrontEnd_Helper_MoneySavingGuidesPartialFunctions
{
    public static function getArticles($categoryWiseAllArticles)
    {
        $relatedArticles = '<div class="row articles-box">';
        foreach ($categoryWiseAllArticles as $article) {
            $profileLink = HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link("link_redactie")."/"
                    . $article['authorDetails']['slug'];
            $articleUpdatedAtDate = new Zend_Date($article['created_at']);
            $articleUpdatedAtDate = $articleUpdatedAtDate->get(Zend_Date::DATE_LONG);
            $articleAuthorName = explode(' ', $article['authorname']);
            $articleAuthorFirstName = isset($articleAuthorName[0])? $articleAuthorName[0] : '';
            $articleAuthorLastName =  isset($articleAuthorName[1])? $articleAuthorName[1] : '';
            $authorName = FrontEnd_Helper_AuthorPartialFunctions::getAuthorName(
                $articleAuthorFirstName,
                $articleAuthorLastName
            );
            $articleImage = !empty($article['thumbnail']) ?
                PUBLIC_PATH_CDN.$article['thumbnail']['path'].$article['thumbnail']['name'] : '';
            $articleTitle = mb_strlen($article['title']) > 50 ?
                                        mb_substr($article['title'], 0, 50).'..' : $article['title'];
            $articleBy = !empty($authorName) ? FrontEnd_Helper_viewHelper::__translate('By') : '';
            $relatedArticles .=
                    '<article class="article col-md-3 col-sm-4 col-xs-6 ">
                        <div class="image">
                            <span class="category">
                                '.$article['artcileCategoryType'].'
                            </span>
                            <a href= "'.HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::getPagePermalink().'/'
                                .$article['permalink'].'">
                                <img
                                src="'.$articleImage.'"
                                width="270" height="192" alt="'.$article['title'].'">
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
                                <span class="author">'. $article['authorname'].$articleBy.'
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
}
