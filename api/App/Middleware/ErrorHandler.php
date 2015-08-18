<?php
namespace Api\Middleware;

class ErrorHandler extends \Slim\Middleware
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
                if (is_numeric($error['code'])) {
                    $this->app->response->setStatus($error['code']);
                }
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
        $this->next->call();
    }
}
