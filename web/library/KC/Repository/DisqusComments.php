<?php
namespace KC\Repository;
class DisqusComments extends \Core\Domain\Entity\DisqusComments
{
    public static function getPageUrlBasedDisqusComments($pageUrl)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
            ->select('d, dt')
            ->from('\Core\Domain\Entity\DisqusThread', 'd')
            ->leftJoin('d.disqusComments', 'dt')
            ->where('d.link like '."'%".$pageUrl."%'");
        $commentInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $disqusCommentMessages = !empty($commentInformation) ?  $commentInformation : '';
        return $disqusCommentMessages;
    }

    public static function saveComments($comments)
    {
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $comments  = new \Core\Domain\Entity\DisqusComments();
        $comments->id = $post->id;
        $comments->author_name = $post->author->name;
        $comments->comment = $post->raw_message;
        $comments->thread_id = $post->thread;
        $comments->created = strtotime($post->createdAt."+0000");
        $entityManagerLocale->persist($comments);
        $entityManagerLocale->flush();
    }
    public static function getMaxCreatedDate()
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $commentMaxCreatedDate = $entityManagerLocale
            ->select('max(created) as max')
            ->from('\Core\Domain\Entity\DisqusComments', 'd');
        $commentMaxCreatedDate = $commentMaxCreatedDate->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $commentMaxCreatedDate;
    }

    public static function getThreadIds()
    {
        $unknownThreads = array();
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $commentThreadIds = $entityManagerLocale
            ->select('c.thread_id')
            ->from('\Core\Domain\Entity\DisqusComments', 'c')
            ->where("(SELECT count(dt.id) from \Core\Domain\Entity\DisqusThread dt where dt.id = c.thread_id) = 0");
        $commentThreadIds = $commentThreadIds->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        foreach ($commentThreadIds as $commentThreadId) {
            $unknownThreads[] = $commentThreadId['thread_id'];
        }
        return $unknownThreads;
    }
}