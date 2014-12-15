<?php
namespace KC\Repository;
class DisqusComments extends \KC\Entity\DisqusComments
{
    public static function getPageUrlBasedDisqusComments($pageUrl)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
            ->select('d')
            ->from('KC\Entity\DisqusComments', 'd')
            ->where("d.page_url LIKE '%$pageUrl%'");
        $commentInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $disqusCommentMessages = !empty($commentInformation) ?  $commentInformation : '';
        return $disqusCommentMessages;
    }

    public static function saveComments($comments)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->delete('KC\Entity\DisqusComments', 'dc')
            ->where('dc.id  > 0')
            ->getQuery();
        $query->execute();
        foreach ($comments as $key => $comment) {
            $entityManagerLocale  = \Zend_Registry::get('emLocale');
            $disqusComment = new \KC\Entity\DisqusComments();
            $disqusComment[$key]->comment_id = $comment['commentId'];
            $disqusComment[$key]->message =  $comment['message'];
            $disqusComment[$key]->page_title =  $comment['pageTitle'];
            $disqusComment[$key]->page_url =  $comment['pageURL'];
            $disqusComment[$key]->created_at =  $comment['createdAt'];
            $disqusComment[$key]->author_name =  $comment['authorName'];
            $disqusComment[$key]->author_profile_url =  $comment['authorProfileURL'];
            $disqusComment[$key]->author_avtar =  $comment['authorAvatar'];
            $entityManagerLocale->persist($disqusComment);
            $entityManagerLocale->flush();
        }
        $tempFiles = glob(PUBLIC_PATH.'tmp/*');
        foreach ($tempFiles as $tempFile) {
            if (is_file($tempFile)) {
                unlink($tempFile);
            }
        }
    }
}