<?php
namespace Api\Controller;

use \Nocarrier\Hal;
use \Core\Domain\Factory\AdminFactory;
use \Core\Service\Errors;

class VisitorsController extends ApiBaseController
{
    public function getVisitors()
    {
        $page = isset($this->filter['skip']) ? $this->filter['skip'] : 0;
        $perPage = isset($this->filter['limit']) ? $this->filter['limit'] : 100;
        $page = $page > 0 ? ($page*$perPage) : 0;
        $where = isset($this->filter['where']) ? (array) $this->filter['where'] : array();
        $conditions = $this->formatInput($where);
        if (true === isset($conditions['email']) && false === is_null($conditions['email']) && false === filter_var($conditions['email'], FILTER_VALIDATE_EMAIL)) {
            $this->app->halt(405, json_encode(array('messages' => array('Invalid Email'))));
        }
        $visitors = AdminFactory::getVisitors()->execute($conditions, array(), $perPage, $page);
        if ($visitors instanceof Errors) {
            $this->app->halt(404, json_encode(array('messages' => $visitors->getErrorsAll())));
        }
        $currentLink = '/visitors'.$this->getLink();
        $visitorsData = new Hal($currentLink);
        if (count($visitors) == $perPage) {
            $nextLink = '/visitors'.$this->getLink(true);
            $visitorsData->addLink('next', $nextLink);
        }

        foreach ($visitors as $visitor) {
            $visitor =  $this->generateVisitorJsonData($visitor);
            $visitorsData->addResource('visitors', $visitor);
        }
        echo $visitorsData->asJson();
    }

    public function getVisitor($id)
    {
        if (is_null($id) || !is_numeric($id)) {
            $this->app->halt(400, json_encode(array('messages' => array('Invalid visitor Id'))));
        }
        $conditions = array('id' => $id);
        $visitor = AdminFactory::getVisitor()->execute($conditions);
        if ($visitor instanceof Errors) {
            $this->app->halt(404, json_encode(array('messages' => $visitor->getErrorsAll())));
        }
        $visitor = $this->generateVisitorJsonData($visitor);
        echo $visitor->asJson();
    }

    public function updateVisitor($id)
    {
        if (is_null($id) || !is_numeric($id)) {
            $this->app->halt(400, json_encode(array('messages' => array('Invalid visitor Id'))));
        }
        $params = json_decode($this->app->request->getBody(), true);
        $conditions = array('id' => $id);
        $visitor = AdminFactory::getVisitor()->execute($conditions);
        if ($visitor instanceof Errors) {
            $this->app->halt(404, json_encode(array('messages' => $visitor->getErrorsAll())));
        }
        $params = $this->formatInput($params);
        if( true === isset($params['email']) ) {
            $emailCondition = array('email' => $params['email']);
            $visitors = AdminFactory::getVisitors()->execute($emailCondition);
            if( count($visitors) > 1 || current($visitors)->getId() != $id ) {
                $this->app->halt(405, json_encode(array('messages' => array('This Email is already in use.'))));
            }
        }
        $result = AdminFactory::updateVisitor()->execute($visitor, $params);
        if ($result instanceof Errors) {
            $this->app->halt(405, json_encode(array('messages' => $result->getErrorsAll())));
        }
        $response = $this->generateVisitorJsonData($result);
        echo $response->asJson();
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
        $fields = isset($this->filter['fields']) ? (array) $this->filter['fields'] : array();
        $limitFields = empty($fields) ? false : true;
        $visitorData['id'] = $visitor->getId();
        if ($limitFields && isset($fields['email']) && true == $fields['email']) {
            $visitorData['email'] = $visitor->getEmail();
        }
        if ($limitFields && isset($fields['firstName']) && true == $fields['firstName']) {
            $visitorData['firstName'] = $visitor->getFirstName();
        }
        if ($limitFields && isset($fields['lastName']) && true == $fields['lastName']) {
            $visitorData['lastName'] = $visitor->getLastName();
        }
        if ($limitFields && isset($fields['mailOpenCount']) && true == $fields['mailOpenCount']) {
            $visitorData['mailOpenCount'] = $visitor->getMailOpenCount();
        }
        if ($limitFields && isset($fields['lastEmailOpenDate']) && true == $fields['lastEmailOpenDate']) {
            $lastEmailOpenDate = $visitor->getLastEmailOpenDate();
            $visitorData['lastEmailOpenDate'] = !empty($lastEmailOpenDate) ? $lastEmailOpenDate->format('Y-m-d H:i:s') : '';
        }
        if ($limitFields && isset($fields['mailClickCount']) && true == $fields['mailClickCount']) {
            $visitorData['mailClickCount'] = $visitor->getMailClickCount();
        }
        if ($limitFields && isset($fields['mailSoftBounceCount']) && true == $fields['mailSoftBounceCount']) {
            $visitorData['mailSoftBounceCount'] = $visitor->getMailSoftBounceCount();
        }
        if ($limitFields && isset($fields['mailHardBounceCount']) && true == $fields['mailHardBounceCount']) {
            $visitorData['mailHardBounceCount'] = $visitor->getMailHardBounceCount();
        }
        if ($limitFields && isset($fields['active']) && true == $fields['active']) {
            $visitorData['active'] = (1 === $visitor->getActive()) ? 'Yes' : 'No';
        }
        if ($limitFields && isset($fields['inactiveStatusReason']) && true == $fields['inactiveStatusReason']) {
            $visitorData['inactiveStatusReason'] = $visitor->getInactiveStatusReason();
        }
        if ($limitFields && isset($fields['activeCodeId']) && true == $fields['activeCodeId']) {
            $visitorData['activeCodeId'] = $visitor->getActiveCodeId();
        }
        if ($limitFields && isset($fields['changePasswordRequest']) && true == $fields['changePasswordRequest']) {
            $visitorData['changePasswordRequest'] = (1 === $visitor->getChangePasswordRequest()) ? 'Yes' : 'No';
        }
        if ($limitFields && isset($fields['codeAlert']) && true == $fields['codeAlert']) {
            $visitorData['codeAlert'] = (1 === $visitor->getCodeAlert()) ? 'Yes' : 'No';
        }
        if ($limitFields && isset($fields['codeAlertSendDate']) && true == $fields['codeAlertSendDate']) {
            $codeAlertSendDate = $visitor->getCodeAlertSendDate();
            $visitorData['codeAlertSendDate'] = !empty($codeAlertSendDate) ? $codeAlertSendDate->format('Y-m-d H:i:s') : '';
        }
        if ($limitFields && isset($fields['currentLogIn']) && true == $fields['currentLogIn']) {
            $currentLogIn = $visitor->getCurrentLogIn();
            $visitorData['currentLogIn'] = !empty($currentLogIn) ? $currentLogIn->format('Y-m-d H:i:s') : '';
        }
        if ($limitFields && isset($fields['dateOfBirth']) && true == $fields['dateOfBirth']) {
            $dateOfBirth = $visitor->getDateOfBirth();
            $visitorData['dateOfBirth'] = !empty($dateOfBirth) ? $dateOfBirth->format('Y-m-d') : '';
        }
        if ($limitFields && isset($fields['deleted']) && true == $fields['deleted']) {
            $visitorData['deleted'] = (1 === $visitor->getDeleted()) ? 'Yes' : 'No';
        }
        if ($limitFields && isset($fields['fashionNewsLetter']) && true == $fields['fashionNewsLetter']) {
            $visitorData['fashionNewsLetter'] = (1 === $visitor->getFashionNewsLetter()) ? 'Yes' : 'No';
        }
        if ($limitFields && isset($fields['gender']) && true == $fields['gender']) {
            $visitorData['gender'] = (1 === $visitor->getGender()) ? 'Female' : 'Male';
        }
        if ($limitFields && isset($fields['interested']) && true == $fields['interested']) {
            $visitorData['interested'] = $visitor->getInterested();
        }
        if ($limitFields && isset($fields['lastLogIn']) && true == $fields['lastLogIn']) {
            $lastLogIn = $visitor->getLastLogIn();
            $visitorData['lastLogIn'] = !empty($lastLogIn) ? $lastLogIn->format('Y-m-d H:i:s') : '';
        }
        if ($limitFields && isset($fields['password']) && true == $fields['password']) {
            $visitorData['password'] = $visitor->getPassword();
        }
        if ($limitFields && isset($fields['postalCode']) && true == $fields['postalCode']) {
            $visitorData['postalCode'] = $visitor->getPostalCode();
        }
        if ($limitFields && isset($fields['profileImg']) && true == $fields['profileImg']) {
            $visitorData['profileImg'] = $visitor->getProfileImg();
        }
        if ($limitFields && isset($fields['pwd']) && true == $fields['pwd']) {
            $visitorData['pwd'] = $visitor->getPwd();
        }
        if ($limitFields && isset($fields['status']) && true == $fields['status']) {
            $visitorData['status'] = (1 === $visitor->getStatus()) ? 'Online' : 'Offline';
        }
        if ($limitFields && isset($fields['travelNewsLetter']) && true == $fields['travelNewsLetter']) {
            $visitorData['travelNewsLetter'] = (1 === $visitor->getTravelNewsLetter()) ? 'Yes' : 'No';
        }
        if ($limitFields && isset($fields['weeklyNewsLetter']) && true == $fields['weeklyNewsLetter']) {
            $visitorData['weeklyNewsLetter'] = (1 === $visitor->getWeeklyNewsLetter()) ? 'Yes' : 'No';
        }
        if ($limitFields && isset($fields['username']) && true == $fields['username']) {
            $visitorData['username'] = $visitor->getUsername();
        }

        return new Hal('/visitors/'.$visitor->getId(), $visitorData);
    }
}
