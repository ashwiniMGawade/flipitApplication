<?php
namespace Api\Controller;

use Api\Controller\Helper\Authenticate;

class ApiBaseController
{
    protected $app;
    protected $request;
    protected $response;
    const RESPONSE_STATUS_UNSUPPORTED_MEDIA_TYPE = 415;

    public function init()
    {
        if (self::RESPONSE_STATUS_UNSUPPORTED_MEDIA_TYPE === $this->app->response->getStatus()) {
            $this->app->halt(self::RESPONSE_STATUS_UNSUPPORTED_MEDIA_TYPE, json_encode(array('message'=>'Unsupported media type')));
        }
        $this->authenticate();
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
        $authenticator = new Authenticate();
        if (false === $authenticator->authenticate($apiKey) ) {
            $this->app->halt(401, json_encode(array('message'=>'Invalid API key.')));
        }


    }
}
