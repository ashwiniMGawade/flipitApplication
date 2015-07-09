<?php
namespace Api\Controller;

class ApiBaseController
{
    protected $app;
    protected $request;
    protected $response;
    const ERROR_UNSUPPORTED_MEDIA_TYPE = 415;

    public function init()
    {
        if (self::ERROR_UNSUPPORTED_MEDIA_TYPE == $this->app->response->getStatus())
        {
            echo json_encode(array('error'=>'Media Type must be application/json for POST, PUT, PATCH'));
            $this->app->stop();
        }
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
}
