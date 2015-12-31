<?php
namespace Api\Controller;

use \Nocarrier\Hal;
use \Core\Service\Errors;

class EmailContentsController extends ApiBaseController
{
    protected $emailTypes = array(
        'newsletter'
    );
    public function getEmailContents($emailType, $referenceId)
    {
        if (!in_array($emailType, $this->emailTypes)) {
            $this->app->halt(400, json_encode(array('messages' => array('Invalid email type'))));
        }

        if (is_null($referenceId) || !is_numeric($referenceId)) {
            $this->app->halt(400, json_encode(array('messages' => array('Invalid newsletter campaign Id'))));
        }
        $emailContent = null;
        switch($emailType) {
            case 'newsletter':
                $emailContent = $this->buildNewsletterEmail();
                break;
            default:
                break;

        }
        $selfLink = '/emailcontents/'.$emailType.'/'.$referenceId;
        $emailContent = array('content' => $emailContent);
        $response = new Hal($selfLink, $emailContent);
        echo $response->asJson();
    }

    private function buildNewsletterEmail ()
    {

    }
}
