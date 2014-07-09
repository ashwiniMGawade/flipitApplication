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
            $articleAuthorFirstName = isset($articleAuthorName[0])? $articleAuthorName[0] : '';
            $articleAuthorLastName =  isset($articleAuthorName[1])? $articleAuthorName[1] : '';
            $authorName = FrontEnd_Helper_AuthorPartialFunctions::getAuthorName(
                $articleAuthorFirstName,
                $articleAuthorLastName
            );
            $articleImage = !empty($article['thumbnail']) ?
                PUBLIC_PATH_CDN.$article['thumbnail']['path'].$article['thumbnail']['name'] : '';
            $articleTitle = mb_strlen($article['title']) > 20 ?
                                        mb_substr($article['title'], 0, 20).'..' : $article['title'];
            $relatedArticles .=
                    '<article class="article col-md-3 col-sm-4 col-xs-6 article-height '.$articleClass.'">
                        <div class="image">
                            <span class="category">
                                '.FrontEnd_Helper_viewHelper::__translate($article['type']).'
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
