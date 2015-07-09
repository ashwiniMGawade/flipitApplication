<?php
namespace Api\Middleware;

class JSON extends \Slim\Middleware
{
    public function __construct($root = '')
    {
        $this->root = $root;
    }
    
    public function call()
    {
        if (preg_match('|^' . $this->root . '.*|', $this->app->request->getResourceUri())) {

            // Force response headers to JSON
            $this->app->response->headers->set('Content-Type', 'application/json');
            
            $method = strtolower($this->app->request->getMethod());

            if (in_array($method, array('post', 'put', 'patch'))) {
                
                $mediaType = $this->app->request->getMediaType();
                if (empty($mediaType) || $mediaType !== 'application/json') {
                    $this->app->response->setStatus(415);
                }
            }
        }
        $this->next->call();
    }
}
