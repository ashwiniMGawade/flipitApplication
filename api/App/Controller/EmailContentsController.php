<?php
namespace Api\Controller;

use \Core\Service\Errors;

class EmailContentsController extends ApiBaseController
{
    public function getEmailContents($id)
    {
        if (is_null($id) || !is_numeric($id)) {
            $this->app->halt(400, json_encode(array('messages' => array('Invalid newsletter campaign Id'))));
        }
        $conditions = array('id' => $id);
        print_r($conditions); exit;
    }

}
