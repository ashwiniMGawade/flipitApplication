<?php
namespace Api\Controller;

class ApiBaseController
{
    protected $app;
    protected $request;
    protected $response;

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
