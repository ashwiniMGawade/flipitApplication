<?php
namespace Api\Controller;
use \Core\Domain\Service\Purifier;

class ApiBaseController
{
    protected $app;
    protected $request;
    protected $response;
    const RESPONSE_STATUS_UNSUPPORTED_MEDIA_TYPE = 415;

    public function init()
    {
        if (self::RESPONSE_STATUS_UNSUPPORTED_MEDIA_TYPE === $this->app->response->getStatus()) {
            $this->app->halt(self::RESPONSE_STATUS_UNSUPPORTED_MEDIA_TYPE);
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

    protected function sanitize($params)
    {
        $purifier = new Purifier();
        return $purifier->purify($params);
    }
}
