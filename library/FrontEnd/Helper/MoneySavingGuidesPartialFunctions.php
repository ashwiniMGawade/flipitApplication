<?php
class FrontEnd_Helper_MoneySavingGuidesPartialFunctions
{
    public function getArticles($headingType, $articles)
    {
        if ($headingType != 'Top Article') {
            $relatedArticles = '<div class="row articles-box">';
        } else {
            $relatedArticles = '';
        }
       
        $articleCounter = 1;
        foreach ($articles as $article) {
            if ($articleCounter == 1 || $articleCounter == 5) {
                $articleClass = 'same-height-left';
            } else if ($articleCounter == 4 || $articleCounter == 8) {
                $articleClass = 'same-height-right';
            } else {
                $articleClass = '';
            }

            $headingType == 'Top Article' ? $topArticleColorClass = 'blue' : $topArticleColorClass = '';
            $profileLink = HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link("link_redactie")."/"
                    . $article['authorDetails']['slug'];
            $articleUpdatedAtDate = new Zend_Date($article['created_at']);
            $articleUpdatedAtDate = $articleUpdatedAtDate->get(Zend_Date::DATE_LONG);
            $authorName = explode(' ', $article['authorname']);
            $authorFirstName = isset($authorName[0])? $authorName[0] : '';
            $authorLastName =  isset($authorName[1])? $authorName[1] : '';
            $authorName = FrontEnd_Helper_AuthorPartialFunctions::getAuthorName($authorFirstName, $authorLastName);
            $relatedArticles .=
                    '<article class="article col-md-3 col-sm-4 col-xs-6 '.$articleClass.'"  style="height: 361px;">
                        <div class="image">
                            <span class="category '.$topArticleColorClass.'">
                                '.FrontEnd_Helper_viewHelper::__translate($headingType).'
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
        echo $relatedArticles ;
    }
}
