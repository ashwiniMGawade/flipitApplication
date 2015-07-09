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
        $app = $this->app;
        $app->notFound(
    function () use ($app) {
        echo json_encode(array('code' => 404,'message' => 'Not found'));
    }
);


$app->error(
    function (\Exception $e) use ($app) {
        // Standard exception data
        $error = array(
            'code'      => $e->getCode(),
            'message'   => $e->getMessage(),
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
        );
 
        // Graceful error data for production mode
        if ('production' === getenv('APPLICATION_ENV')) {
            $error['message'] = 'There was an internal error';
            unset($error['file'], $error['line']);
        }
 
        if (!empty($errors)) {
            $error['errors'] = $errors;
        }

        $log = $app->getLog();
        $log->error('#File:'.$e->getFile().';#Line:'.$e->getLine().';#msg:'.$e->getMessage());

        echo json_encode($error);
    }
);
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
