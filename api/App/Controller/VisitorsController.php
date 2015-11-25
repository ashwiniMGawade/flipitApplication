<?php
namespace Api\Controller;

use \Core\Domain\Factory\AdminFactory;
use \Core\Service\Errors;

class VisitorsController extends ApiBaseController
{
    public function updateVisitor()
    {
        $response = array();
        $visitorsData = json_decode($this->app->request->getBody(), true);
        if (!is_array($visitorsData) || empty($visitorsData)) {
            $this->app->response->setStatus(405);
            echo json_encode(array('msg'=>'Invalid Parameters.'));
            return;
        }

        foreach ($visitorsData as $visitorParams) {
            if (!isset($visitorParams['event']) || empty($visitorParams['event'])) {
                $this->app->response->setStatus(405);
                echo json_encode(array('msg'=>'Event Required'));
                return;
            }
            if (!isset($visitorParams['email']) || empty($visitorParams['email'])) {
                $this->app->response->setStatus(405);
                echo json_encode(array('msg'=>'Email Required'));
                return;
            }
            $conditions = array('email' => $visitorParams['email']);
            $visitor = AdminFactory::getVisitor()->execute($conditions);
            if($visitor instanceof Errors) {
                $this->app->response->setStatus(405);
                echo json_encode(array('msg'=>'Invalid Email'));
                return;
            }
            $params = $this->createParamsData($visitor, $visitorParams);
            $result = AdminFactory::updateVisitors()->execute($visitor, $params);
            if($result instanceof Errors) {
                $this->app->response->setStatus(405);
                $response = $result->getErrorsAll();
            } else {
                $response[$result->getEmail()] = array(
                    'open' => $result->getMailOpenCount(),
                    'click' => $result->getMailClickCount(),
                    'soft_bounce' => $result->getMailSoftBounceCount(),
                    'hard_bounce' => $result->getMailHardBounceCount()
                );
            }
        }
        echo json_encode($response);
        return;
    }

    private function createParamsData($visitor, $visitorParams)
    {
        $params = array();
        switch ($visitorParams['event']) {
            case 'open':
                if (!isset($visitorParams['timeStamp']) || !is_numeric($visitorParams['timeStamp'])) {
                    $this->app->halt(405, json_encode(array('msg'=>'Invalid mail open Timestamp')));
                }
                $openCount = (int) $visitor->getMailOpenCount();
                $params['mailOpenCount'] = $openCount + 1;
                $openDate = new \DateTime();
                $openDate->setTimestamp($visitorParams['timeStamp']);
                $params['lastEmailOpenDate'] = $openDate;
                break;
            case 'click':
                $clickCount = (int) $visitor->getMailClickCount();
                $params['mailClickCount'] = $clickCount + 1;
                break;
            case 'soft_bounce':
                $softBounceCount = (int) $visitor->getMailSoftBounceCount();
                if ($softBounceCount >= 5) {
                    $params['active'] = 0;
                    $params['inactiveStatusReason'] = 'Soft Bounce';
                }
                $params['mailSoftBounceCount'] = $softBounceCount + 1;
                break;
            case 'hard_bounce':
                $hardBounceCount = (int) $visitor->getMailHardBounceCount();
                if ($hardBounceCount >= 2) {
                    $params['active'] = 0;
                    $params['inactiveStatusReason'] = 'Hard Bounce';
                }
                $params['mailHardBounceCount'] = $hardBounceCount + 1;
                break;
            default:
                $this->app->halt(405, json_encode(array('msg'=>'Invalid Event')));
                break;
        }
        return $params;
    }
}
