<?php

class DisqusComments extends BaseDisqusComments
{
    
    public static function saveComments($comments)
    { 
        $databaseConnection = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
        $databaseConnection->query('TRUNCATE TABLE disqus_comments');
        $disqusComment = new Doctrine_Collection('DisqusComments');
        foreach ($comments as $key => $comment) {
            $disqusComment[$key]->comment_id = $comment['commentId'];
            $disqusComment[$key]->message =  $comment['message'];
            $disqusComment[$key]->page_title =  $comment['pageTitle'];
            $disqusComment[$key]->page_url =  $comment['pageURL'];
            $disqusComment[$key]->created_at =  $comment['createdAt'];
            $disqusComment[$key]->author_name =  $comment['authorName'];
            $disqusComment[$key]->author_profile_url =  $comment['authorProfileURL'];
            $disqusComment[$key]->author_avtar =  $comment['authorAvatar'];
        }
        $disqusComment->save();
    }

    public static function getPageUrlBasedDisqusComments($pageUrl)
    {
        $commentInformation = Doctrine_Query::create()
            ->select('message')
            ->from('DisqusComments')
            ->where('page_url = '."'".$pageUrl."'")
            ->fetchArray();
        $disqusCommentMessages = !empty($commentInformation) ?  $commentInformation : '';
        return $disqusCommentMessages;

    }
}
