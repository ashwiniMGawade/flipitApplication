<?php
namespace Api\Controller;

use Api\Controller\Helper\Authenticator;

class ApiBaseController
{
    protected $app;
    protected $request;
    protected $response;
    protected $filter;
    const RESPONSE_STATUS_UNSUPPORTED_MEDIA_TYPE = 415;

    public function init()
    {
        if (self::RESPONSE_STATUS_UNSUPPORTED_MEDIA_TYPE === $this->app->response->getStatus()) {
            $this->app->halt(self::RESPONSE_STATUS_UNSUPPORTED_MEDIA_TYPE, json_encode(array('message'=>'Unsupported media type')));
        }
        $this->authenticate();
        $this->filter = (array) $this->app->request()->get('filter');
    }

    public function setApp($app)
    {
        $this->app = $app;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function authenticate()
    {
        $apiKey = $this->request->params('api_key');
        if (strlen($apiKey)<1) {
            $this->app->halt(401, json_encode(array('message'=>'API key is required.')));
        }
        $authenticator = new Authenticator();
        if (false === $authenticator->authenticate($apiKey) ) {
            $this->app->halt(401, json_encode(array('message'=>'Invalid API key.')));
        }
    }

    public function getLink($nextLink = false)
    {
        $link = '?';
        foreach ($this->filter as $type => $fields) {
            if(is_array($fields)) {
                foreach ($fields as $field => $value) {
                    $link .= 'filter[' . $type . '][' . $field . ']=' . urlencode($value) . '&';
                }
            } else {
                if($nextLink && $type == 'skip') $fields++;
                $link .= 'filter[' . $type . ']=' . urlencode($fields) .'&';
            }
        }
        $link .= 'api_key='.urlencode($this->app->request()->get('api_key'));
        return $link;
    }
}
