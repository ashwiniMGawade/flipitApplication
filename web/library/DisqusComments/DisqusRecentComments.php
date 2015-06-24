<?php
require('disqusapi/disqusapi.php');
function getDisqusRecentComments($parameters)
{
    try {
        $disqus = new DisqusAPI($parameters['DISQUS_API_SECRET'], 'json', '3.0');
        $parameters = array(
            'forum' => $parameters['DISQUS_FORUM_SHORTNAME'],
            'order' =>  $parameters['DISQUS_FETCH_ORDER'],
            'limit' => $parameters['DISQUS_FETCH_LIMIT']
        );

        $disqusCommentCreated = DisqusComments::getMaxCreatedDate();
        if (!empty($disqusCommentCreated[0]['max'])) {
            $parameters['since'] = $disqusCommentCreated[0]['max'];
        }
        do {
            $posts = $disqus->posts->list($parameters);
            $cursor = isset($posts->cursor) ? $posts->cursor : '';
            $parameters['cursor'] = !empty($cursor->next) ? $cursor->next : '';
            foreach ($posts as $post) {
                DisqusComments::saveComments($post);
            }
        } while (isset($cursor->more) ? $cursor->more : '');
        unset($parameters['since']);
        unset($parameters['cursor']);
        $unknownThreads = DisqusComments::getThreadIds();
        if (!empty($unknownThreads)) {
            $parameters['thread'] = $unknownThreads;
            do {
                $posts = $disqus->threads->list($parameters);
                $cursor = isset($posts->cursor) ? $posts->cursor : '';
                $parameters['cursor'] = !empty($cursor->next) ? $cursor->next : '';
                foreach ($posts as $post) {
                    DisqusThread::saveDisqusThread($post);
                }
            } while (isset($cursor->more) ? $cursor->more : '');
        }
    } catch (DisqusAPIError $e) {
        echo $e->getMessage();
        echo PHP_EOL;
        exit;
    }
}