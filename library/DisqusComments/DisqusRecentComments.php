<?php
require('disqusapi/disqusapi.php');
function getDisqusRecentComments($parameters)
{
    try {
        $disqus = new DisqusAPI($parameters['DISQUS_API_SECRET'], 'json', '3.0');
       
        $params = array(
            'forum' => $parameters['DISQUS_FORUM_SHORTNAME'],
            'order' =>  $parameters['DISQUS_FETCH_ORDER'],
            'limit' => $parameters['DISQUS_FETCH_LIMIT']
        );

        $disqusCommentCreated = DisqusComments::getMaxCreatedDate();
        if (!empty($disqusCommentCreated[0]['max'])) {
            $params['since'] = $disqusCommentCreated[0]['max'];
        }
     
        do {
            $posts = $disqus->posts->list($params);
            $cursor = isset($posts->cursor) ? $posts->cursor : '';
            $params['cursor'] = !empty($cursor->next) ? $cursor->next : '';
            foreach ($posts as $post) {
                DisqusComments::saveComments($post);
            }
        } while ($cursor->more);
        unset($params['since']);
        unset($params['cursor']);
        
        $unknownThreads = DisqusComments::getThreadIds();
     
        if (!empty($unknownThreads)) {
            $params['thread'] = $unknownThreads;
            do {
                $posts = $disqus->threads->list($params);
                // Create cursor to paginate through resultset
                $cursor = isset($posts->cursor) ? $posts->cursor : '';
                // Update our arguments with the cursor and the next position
                $params['cursor'] = !empty($cursor->next) ? $cursor->next : '';
     
                foreach ($posts as $post) {
                    DisqusThread::saveDisqusThread($post);
                }
            } while ($cursor->more);
            // End forum threads
        }
    } catch (DisqusAPIError $e) {
        echo $e->getMessage();
        echo PHP_EOL;
        exit;
    }
}