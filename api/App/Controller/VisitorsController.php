<?php
namespace Api\Controller;

use \Nocarrier\Hal;
use \Core\Domain\Factory\AdminFactory;
use \Core\Service\Errors;

class VisitorsController extends ApiBaseController
{
    public function getVisitor($id)
    {
        $conditions = array('id' => $id);
        $visitor = AdminFactory::getVisitor()->execute($conditions);
        if($visitor instanceof Errors) {
            $this->app->response->setStatus(405);
            echo json_encode(array('msg'=>'Invalid Id'));
            return;
        }
        echo $this->generateVisitorJsonData($visitor);
    }

    public function updateVisitor($id)
    {
        $params = json_decode($this->app->request->getBody(), true);
        $conditions = array('id' => $id);
        $visitor = AdminFactory::getVisitor()->execute($conditions);
        if($visitor instanceof Errors) {
            $this->app->response->setStatus(405);
            echo json_encode(array('msg'=>'Invalid Id'));
            return;
        }
        $params = $this->formatInput($params);
        $result = AdminFactory::updateVisitors()->execute($visitor, $params);
        if($result instanceof Errors) {
            $this->app->response->setStatus(405);
            $response = $result->getErrorsAll();
            $response = json_encode($response);
        } else {
            $response = $this->generateVisitorJsonData($result);
        }
        echo $response;
    }

    private  function formatInput($params)
    {
        if (isset($params['active'])) {
            $params['active'] = ( $params['active'] === 'Yes' ? 1 : ( $params['active'] === 'No' ? 0 : null ));
            if(true === is_null($params['active'])) unset($params['active']);
        }
        if (isset($params['changePasswordRequest'])) {
            $params['changePasswordRequest'] = ( $params['changePasswordRequest'] === 'Yes' ? 1 : ( $params['changePasswordRequest'] === 'No' ? 0 : null ));
            if(true === is_null($params['changePasswordRequest'])) unset($params['changePasswordRequest']);
        }
        if (isset($params['codeAlert'])) {
            $params['codeAlert'] = ( $params['codeAlert'] === 'Yes' ? 1 : ( $params['codeAlert'] === 'No' ? 0 : null ));
            if(true === is_null($params['codeAlert'])) unset($params['codeAlert']);
        }
        if (isset($params['deleted'])) {
            $params['deleted'] = ( $params['deleted'] === 'Yes' ? 1 : ( $params['deleted'] === 'No' ? 0 : null ));
            if(true === is_null($params['deleted'])) unset($params['deleted']);
        }
        if (isset($params['fashionNewsLetter'])) {
            $params['fashionNewsLetter'] = ( $params['fashionNewsLetter'] === 'Yes' ? 1 : ( $params['fashionNewsLetter'] === 'No' ? 0 : null ));
            if(true === is_null($params['fashionNewsLetter'])) unset($params['fashionNewsLetter']);
        }
        if (isset($params['gender'])) {
            $params['gender'] = ( $params['gender'] === 'Female' ? 1 : ( $params['gender'] === 'Male' ? 0 : null ));
            if(true === is_null($params['gender'])) unset($params['gender']);
        }
        if (isset($params['status'])) {
            $params['status'] = ( $params['status'] === 'Online' ? 1 : ( $params['status'] === 'Offline' ? 0 : null ));
            if(true === is_null($params['status'])) unset($params['status']);
        }
        if (isset($params['travelNewsLetter'])) {
            $params['travelNewsLetter'] = ( $params['travelNewsLetter'] === 'Yes' ? 1 : ( $params['travelNewsLetter'] === 'No' ? 0 : null ));
            if(true === is_null($params['travelNewsLetter'])) unset($params['travelNewsLetter']);
        }
        if (isset($params['weeklyNewsLetter'])) {
            $params['weeklyNewsLetter'] = ( $params['weeklyNewsLetter'] === 'Yes' ? 1 : ( $params['weeklyNewsLetter'] === 'No' ? 0 : null ));
            if(true === is_null($params['weeklyNewsLetter'])) unset($params['weeklyNewsLetter']);
        }
        return $params;
    }

    private function generateVisitorJsonData($visitor)
    {
        $lastEmailOpenDate = $visitor->getLastEmailOpenDate();
        $currentLogIn = $visitor->getCurrentLogIn();
        $dateOfBirth = $visitor->getDateOfBirth();
        $lastLogIn = $visitor->getLastLogIn();
        $codeAlertSendDate = $visitor->getCodeAlertSendDate();

        $visitorData = array(
            'email' => $visitor->getEmail(),
            'firstName' => $visitor->getFirstName(),
            'lastName' => $visitor->getLastName(),
            'mailOpenCount' => $visitor->getMailOpenCount(),
            'lastEmailOpenDate' => !empty($lastEmailOpenDate) ? $lastEmailOpenDate->format('Y-m-d H:i:s') : '',
            'mailClickCount' => $visitor->getMailClickCount(),
            'mailSoftBounceCount' => $visitor->getMailSoftBounceCount(),
            'mailHardBounceCount' => $visitor->getMailHardBounceCount(),
            'active' => (1 === $visitor->getActive()) ? 'Yes' : 'No',
            'inactiveStatusReason' => $visitor->getInactiveStatusReason(),
            'activeCodeId' => $visitor->getActiveCodeId(),
            'changePasswordRequest' => (1 === $visitor->getChangePasswordRequest()) ? 'Yes' : 'No',
            'codeAlert' => (1 === $visitor->getCodeAlert()) ? 'Yes' : 'No',
            'codeAlertSendDate' => !empty($codeAlertSendDate) ? $codeAlertSendDate->format('Y-m-d H:i:s') : '',
            'currentLogIn' => !empty($currentLogIn) ? $currentLogIn->format('Y-m-d H:i:s') : '',
            'dateOfBirth' => !empty($dateOfBirth) ? $dateOfBirth->format('Y-m-d') : '',
            'deleted' => (1 === $visitor->getDeleted()) ? 'Yes' : 'No',
            'fashionNewsLetter' => (1 === $visitor->getFashionNewsLetter()) ? 'Yes' : 'No',
            'gender' => (1 === $visitor->getGender()) ? 'Female' : 'Male',
            'interested' => $visitor->getInterested(),
            'lastLogIn' => !empty($lastLogIn) ? $lastLogIn->format('Y-m-d H:i:s') : '',
            'password' => $visitor->getPassword(),
            'postalCode' => $visitor->getPostalCode(),
            'profileImg' => $visitor->getProfileImg(),
            'pwd' => $visitor->getPwd(),
            'status' => (1 === $visitor->getStatus()) ? 'Online' : 'Offline',
            'travelNewsLetter' => (1 === $visitor->getTravelNewsLetter()) ? 'Yes' : 'No',
            'weeklyNewsLetter' => (1 === $visitor->getWeeklyNewsLetter()) ? 'Yes' : 'No',
            'username' => $visitor->getUsername()
        );
        $shop = new Hal('/visitors/'.$visitor->getId(), $visitorData);
        return $shop->asJson();
    }
}
