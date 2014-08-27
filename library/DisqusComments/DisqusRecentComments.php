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

    $DQCommentsAPILink =
    "http://disqus.com/api/3.0/posts/list.json?limit={$parameters['commentCount']}
    &api_key={$parameters['APIKey']}&forum={$parameters['forumName']}&include=approved";
    $DQJsonCommentResponse = DQGetJson($DQCommentsAPILink);
    $DQRawComments = @json_decode($DQJsonCommentResponse, true);

    if ($DQJsonCommentResponse != false && $DQRawComments['code'] != 2) {
        $DQRawComments = json_decode($DQJsonCommentResponse, true);
        $DQComments = $DQRawComments['response'];

        for ($index = 0; $index < count($DQComments); $index++) {

            $DQThreadAPILink =
            "http://disqus.com/api/3.0/threads/details.json?api_key={$parameters['APIKey']}
            &thread={$DQComments[$index]['thread']}";
            $DQJsonThreadResponse = DQGetJson($DQThreadAPILink);
            $DQRawThread = json_decode($DQJsonThreadResponse, true);
            $DQThread = $DQRawThread['response'];

            if ($DQThread != false) {
                $DQComments[$index]['pageTitle'] = $DQThread['title'];
                $DQComments[$index]['pageURL'] = $DQThread['link'];
            } else {
                $DQComments[$index]['pageTitle'] = 'Page Not Found';
                $DQComments[$index]['pageURL'] = '#';
            }
            $DQComments[$index]['commentId'] = $DQComments[$index]['id'];
            $DQComments[$index]['authorName'] = $DQComments[$index]['author']['name'];
            $DQComments[$index]['authorProfileURL'] = $DQComments[$index]['author']['profileUrl'];
            $DQComments[$index]['authorAvatar'] = $DQComments[$index]['author']['avatar']['cache'];
            $DQComments[$index]['message'] =
            DQLimitLength($DQComments[$index]['raw_message'], $parameters['commentLength']);
            unset($DQComments[$index]['isJuliaFlagged']);
            unset($DQComments[$index]['isFlagged']);
            unset($DQComments[$index]['forum']);
            unset($DQComments[$index]['parent']);
            unset($DQComments[$index]['author']);
            unset($DQComments[$index]['media']);
            unset($DQComments[$index]['isDeleted']);
            unset($DQComments[$index]['isApproved']);
            unset($DQComments[$index]['dislikes']);
            unset($DQComments[$index]['raw_message']);
            unset($DQComments[$index]['id']);
            unset($DQComments[$index]['thread']);
            unset($DQComments[$index]['numReports']);
            unset($DQComments[$index]['isEdited']);
            unset($DQComments[$index]['isSpam']);
            unset($DQComments[$index]['isHighlighted']);
            unset($DQComments[$index]['points']);
            unset($DQComments[$index]['likes']);
        }
        return $DQComments;
    } else {
        $DQComments = '';
        return $DQComments;
    }
}

function DQGetJson($DQAPILink)
{
    $DQcURL = curl_init();
    curl_setopt($DQcURL, CURLOPT_HEADER, false);
    curl_setopt($DQcURL, CURLOPT_URL, $DQAPILink);
    curl_setopt($DQcURL, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($DQcURL, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($DQcURL, CURLOPT_FORBID_REUSE, true);
    $DQJsonResponse = curl_exec($DQcURL);
    curl_close($DQcURL);
    return $DQJsonResponse;
}

function DQLimitLength($string, $maxLength)
{
    if (strlen($string) <= $maxLength) {
        return $string;
    } else {
        return substr($string, 0, $maxLength)."...";
    }
}
