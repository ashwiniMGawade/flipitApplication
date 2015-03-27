<?php
class DisqusComments extends BaseDisqusComments
{
    public static function saveComments($post)
    {
        $comments  = new DisqusComments();
        $comments->id = $post->id;
        $comments->author_name = $post->author->name;
        $comments->comment = $post->raw_message;
        $comments->thread_id = $post->thread;
        $comments->created = strtotime($post->createdAt."+0000");
        $comments->save();
        $tempFiles = glob(PUBLIC_PATH.'tmp/*');
        foreach ($tempFiles as $tempFile) {
            if (is_file($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    public static function getMaxCreatedDate()
    {
        $commentMaxCreatedDate = Doctrine_Query::create()
            ->select('max(created) as max')
            ->from('DisqusComments')
            ->fetchArray();
        return $commentMaxCreatedDate;
    }

    public static function getThreadIds()
    {
        $unknownThreads = array();
        $commentThreadIds = Doctrine_Query::create()
            ->select('c.thread_id')
            ->from('DisqusComments c')
            ->where("(SELECT count(*) from DisqusThread where id = c.thread_id) = 0")
            ->fetchArray();
        foreach ($commentThreadIds as $commentThreadId) {
            $unknownThreads[] = $commentThreadId['thread_id'];
        }

        return $unknownThreads;
    }

    public static function getPageUrlBasedDisqusComments($pageUrl)
    {
        $commentInformation = Doctrine_Query::create()
            ->select('*')
            ->from('DisqusComments dc')
            ->leftJoin('dc.thread dt')
            ->where('dt.link like '."'%".$pageUrl."%'")
            ->fetchArray();
        $disqusCommentMessages = !empty($commentInformation) ?  $commentInformation : '';
        return $disqusCommentMessages;

    }
}
