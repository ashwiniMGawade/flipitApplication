<?php
function getDisqusRecentComments($parameters)
{

    if (!isset($parameters['APIKey']) || !isset($parameters['forumName'])) {
        return false;
    }
    if (!isset($parameters['commentCount'])) {
        $parameters['commentCount'] = 25;
    }
    if (!isset($parameters['commentLength'])) {
        $parameters['commentLength'] = 100;
    }

    $DisqusCommentsAPILink =
    "http://disqus.com/api/3.0/posts/list.json?limit={$parameters['commentCount']}
    &api_key={$parameters['APIKey']}&forum={$parameters['forumName']}&include=approved";
    $DisqusJsonCommentResponse = DisqusGetJson($DisqusCommentsAPILink);
    $DisqusRawComments = json_decode($DisqusJsonCommentResponse, true);

    if ($DisqusJsonCommentResponse != false && $DisqusRawComments['code'] != 2) {
        $DisqusRawComments = json_decode($DisqusJsonCommentResponse, true);
        $DisqusComments = $DisqusRawComments['response'];

        for ($index = 0; $index < count($DisqusComments); $index++) {

            $DisqusThreadAPILink =
            "http://disqus.com/api/3.0/threads/details.json?api_key={$parameters['APIKey']}
            &thread={$DisqusComments[$index]['thread']}";
            $DisqusJsonThreadResponse = DisqusGetJson($DisqusThreadAPILink);
            $DisqusRawThread = json_decode($DisqusJsonThreadResponse, true);
            $DisqusThread = $DisqusRawThread['response'];

            if ($DisqusThread != false) {
                $DisqusComments[$index]['pageTitle'] = $DisqusThread['title'];
                $DisqusComments[$index]['pageURL'] = $DisqusThread['link'];
            } else {
                $DisqusComments[$index]['pageTitle'] = 'Page Not Found';
                $DisqusComments[$index]['pageURL'] = '#';
            }
            $DisqusComments[$index]['commentId'] = $DisqusComments[$index]['id'];
            $DisqusComments[$index]['authorName'] = $DisqusComments[$index]['author']['name'];
            $DisqusComments[$index]['authorProfileURL'] = $DisqusComments[$index]['author']['profileUrl'];
            $DisqusComments[$index]['authorAvatar'] = $DisqusComments[$index]['author']['avatar']['cache'];
            $DisqusComments[$index]['message'] =
            DisqusLimitLength($DisqusComments[$index]['raw_message'], $parameters['commentLength']);
            unset($DisqusComments[$index]['isJuliaFlagged']);
            unset($DisqusComments[$index]['isFlagged']);
            unset($DisqusComments[$index]['forum']);
            unset($DisqusComments[$index]['parent']);
            unset($DisqusComments[$index]['author']);
            unset($DisqusComments[$index]['media']);
            unset($DisqusComments[$index]['isDeleted']);
            unset($DisqusComments[$index]['isApproved']);
            unset($DisqusComments[$index]['dislikes']);
            unset($DisqusComments[$index]['raw_message']);
            unset($DisqusComments[$index]['id']);
            unset($DisqusComments[$index]['thread']);
            unset($DisqusComments[$index]['numReports']);
            unset($DisqusComments[$index]['isEdited']);
            unset($DisqusComments[$index]['isSpam']);
            unset($DisqusComments[$index]['isHighlighted']);
            unset($DisqusComments[$index]['points']);
            unset($DisqusComments[$index]['likes']);
        }
        return $DisqusComments;
    } else {
        $DisqusComments = '';
        return $DisqusComments;
    }
}

function DisqusGetJson($DisqusCommentsAPILink)
{
    $DisqusCURL = curl_init();
    curl_setopt($DisqusCURL, CURLOPT_HEADER, false);
    curl_setopt($DisqusCURL, CURLOPT_URL, $DisqusCommentsAPILink);
    curl_setopt($DisqusCURL, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($DisqusCURL, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($DisqusCURL, CURLOPT_FORBID_REUSE, true);
    $DisqusJsonResponse = curl_exec($DisqusCURL);
    curl_close($DisqusCURL);
    return $DisqusJsonResponse;
}

function DisqusLimitLength($message, $maxLength)
{
    if (strlen($message) <= $maxLength) {
        return $message;
    } else {
        return substr($message, 0, $maxLength)."...";
    }
}
