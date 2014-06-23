<?php
class FrontEnd_Helper_MoneySavingGuidesPartialFunctions {
    public function getMostReadArticles($mostReadArticles)
    {
        $articleNumber = 1;
        
        foreach($mostReadArticles as $mostReadArticle) {
            if($articleNumber == 1){
                $id = 'first';
                $class= 'slide active';
            } else if ($articleNumber == 2) {
                $id = 'second';
                $class = 'slide';
            } else {
                $id = 'third';
                $class = 'slide';
            }
            echo'<div class="'.$class.'" id="'.$id.'">
                <a href="'.HTTP_PATH_LOCALE.'plus/'.$mostReadArticle['articles']['permalink'].'">
                    <div class="mostread-image">
                        <img width="632" class="aligncenter" 
                        src="'.PUBLIC_PATH_CDN.$mostReadArticle['articles']['articleImage']['path']
                        .$mostReadArticle['articles']['articleImage']['name'].'" 
                        alt="'.$mostReadArticle['articles']['title'].'">
                    </div>
                    <h1>'.$mostReadArticle['articles']['title'].'</h1>
                </a>
                <p>
                   '.$mostReadArticle['articles']['content'].'
                </p>
            </div>';
            $articleNumber++;
        }
        
    }

    public function getArticles($headingType, $articles)
    {
        $relatedArticles = 
            '<header class="heading-bar">
                <h2>'.FrontEnd_Helper_viewHelper::__translate($headingType).'</h2>
            </header>
            <div class="item-block">
                <div class="holder">';
                    foreach($articles as $article) { 
        $relatedArticles .=
                '<div class="item">
                    <a href="'.HTTP_PATH_LOCALE.'plus/'.$article['permalink'].'">
                        <div class="related-image-wrapper">
                            <img src="'.PUBLIC_PATH_CDN.$article['thumbnail']['path'].$article['thumbnail']['name'].'" 
                            width="363" alt="'.$article['title'].'">
                        </div>
                    </a>
                    <div class="box">
                        <div class="caption-area">
                            <a href="'.HTTP_PATH_LOCALE.'plus/'.$article['permalink'].'">
                                <span class="caption">
                                '.$article['title'].'
                                </span>
                            </a>    
                        </div>
                        <a href="'.HTTP_PATH_LOCALE.'plus/'.$article['permalink'].'" class="link">'
                            .FrontEnd_Helper_viewHelper::__translate('more').' &#8250;
                        </a>
                    </div>
                </div>';
            }           
        $relatedArticles .=
           '</div>
        </div>';
        return $relatedArticles;
    }

}
