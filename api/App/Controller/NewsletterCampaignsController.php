<?php
namespace Api\Controller;

use \Nocarrier\Hal;
use \Core\Domain\Factory\SystemFactory;
use \Core\Domain\Factory\AdminFactory;
use \Core\Service\Errors;

class NewsletterCampaignsController extends ApiBaseController
{
    public function getNewsletterCampaigns()
    {
        $page = (int) $this->app->request()->get('page');
        $perPage = (int) $this->app->request()->get('perPage');
        $perPage = ($perPage === 0) ? 100 : $perPage;
        $email = $this->app->request()->get('email');
        $conditions = array();
        $currentLink = '/newsletterCampaigns?page=' . ($page) . '&perPage=' . $perPage;
        $nextLink = '/newsletterCampaigns?page=' . ($page + 1) . '&perPage=' . $perPage;
        if (false === is_null($email) && false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->app->halt(405, json_encode(array('messages' => array('Invalid Email'))));
        }
        if (false === is_null($email)) {
            $conditions['email'] = $email;
            $currentLink .= '&email='.urlencode($email);
            $nextLink .= '&email='.urlencode($email);
        }
        $currentLink .= '&api_key='.urlencode($this->app->request()->get('api_key'));
        $nextLink .= '&api_key='.urlencode($this->app->request()->get('api_key'));
        $newsletterCampaigns = SystemFactory::getNewsletterCampaigns()->execute($conditions, array(), $perPage, $page);
        if ($newsletterCampaigns instanceof Errors) {
            $this->app->halt(404, json_encode(array('messages' => $newsletterCampaigns->getErrorsAll())));
        }
        $newsletterCampaignsData = new Hal($currentLink);
        if (count($newsletterCampaigns) == $perPage) {
            $newsletterCampaignsData->addLink('next', $nextLink);
        }

        foreach ($newsletterCampaigns as $newsletterCampaign) {
            $newsletterCampaign =  $this->generateNewsletterCampaignJsonData($newsletterCampaign);
            $newsletterCampaignsData->addResource('newsletterCampaigns', $newsletterCampaign);
        }
        echo $newsletterCampaignsData->asJson();
    }

    public function getNewsletterCampaign($id)
    {
        if (is_null($id) || !is_numeric($id)) {
            $this->app->halt(400, json_encode(array('messages' => array('Invalid newsletter campaign Id'))));
        }
        $conditions = array('id' => $id);
        $newsletterCampaign = AdminFactory::getNewsletterCampaign()->execute($conditions);
        if ($newsletterCampaign instanceof Errors) {
            $this->app->halt(404, json_encode(array('messages' => $newsletterCampaign->getErrorsAll())));
        }
        $newsletterCampaign = $this->generateNewsletterCampaignJsonData($newsletterCampaign);
        echo $newsletterCampaign->asJson();
    }

    public function updateNewsletterCampaign($id)
    {
        if (is_null($id) || !is_numeric($id)) {
            $this->app->halt(400, json_encode(array('messages' => array('Invalid newsletter campaign Id'))));
        }
        $params = json_decode($this->app->request->getBody(), true);
        $conditions = array('id' => $id);
        $newsletterCampaign = AdminFactory::getNewsletterCampaign()->execute($conditions);
        if ($newsletterCampaign instanceof Errors) {
            $this->app->halt(404, json_encode(array('messages' => $newsletterCampaign->getErrorsAll())));
        }
        $params = $this->formatInput($params);
        if( true === isset($params['email']) ) {
            $emailCondition = array('email' => $params['email']);
            $newsletterCampaigns = SystemFactory::getNewsletterCampaigns()->execute($emailCondition);
            if( count($newsletterCampaigns) > 1 || current($newsletterCampaigns)->getId() != $id ) {
                $this->app->halt(405, json_encode(array('messages' => array('This Email is already in use.'))));
            }
        }
        $result = AdminFactory::updateNewsletterCampaign()->execute($newsletterCampaign, $params);
        if ($result instanceof Errors) {
            $this->app->halt(405, json_encode(array('messages' => $result->getErrorsAll())));
        }
        $response = $this->generateNewsletterCampaignJsonData($result);
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

    private function generateNewsletterCampaignJsonData($newsletterCampaign)
    {
        $lastEmailOpenDate = $newsletterCampaign->getLastEmailOpenDate();
        $currentLogIn = $newsletterCampaign->getCurrentLogIn();
        $dateOfBirth = $newsletterCampaign->getDateOfBirth();
        $lastLogIn = $newsletterCampaign->getLastLogIn();
        $codeAlertSendDate = $newsletterCampaign->getCodeAlertSendDate();

        $newsletterCampaignData = array(
            'id' => $newsletterCampaign->getId(),
            'email' => $newsletterCampaign->getEmail(),
            'firstName' => $newsletterCampaign->getFirstName(),
            'lastName' => $newsletterCampaign->getLastName(),
            'mailOpenCount' => $newsletterCampaign->getMailOpenCount(),
            'lastEmailOpenDate' => !empty($lastEmailOpenDate) ? $lastEmailOpenDate->format('Y-m-d H:i:s') : '',
            'mailClickCount' => $newsletterCampaign->getMailClickCount(),
            'mailSoftBounceCount' => $newsletterCampaign->getMailSoftBounceCount(),
            'mailHardBounceCount' => $newsletterCampaign->getMailHardBounceCount(),
            'active' => (1 === $newsletterCampaign->getActive()) ? 'Yes' : 'No',
            'inactiveStatusReason' => $newsletterCampaign->getInactiveStatusReason(),
            'activeCodeId' => $newsletterCampaign->getActiveCodeId(),
            'changePasswordRequest' => (1 === $newsletterCampaign->getChangePasswordRequest()) ? 'Yes' : 'No',
            'codeAlert' => (1 === $newsletterCampaign->getCodeAlert()) ? 'Yes' : 'No',
            'codeAlertSendDate' => !empty($codeAlertSendDate) ? $codeAlertSendDate->format('Y-m-d H:i:s') : '',
            'currentLogIn' => !empty($currentLogIn) ? $currentLogIn->format('Y-m-d H:i:s') : '',
            'dateOfBirth' => !empty($dateOfBirth) ? $dateOfBirth->format('Y-m-d') : '',
            'deleted' => (1 === $newsletterCampaign->getDeleted()) ? 'Yes' : 'No',
            'fashionNewsLetter' => (1 === $newsletterCampaign->getFashionNewsLetter()) ? 'Yes' : 'No',
            'gender' => (1 === $newsletterCampaign->getGender()) ? 'Female' : 'Male',
            'interested' => $newsletterCampaign->getInterested(),
            'lastLogIn' => !empty($lastLogIn) ? $lastLogIn->format('Y-m-d H:i:s') : '',
            'password' => $newsletterCampaign->getPassword(),
            'postalCode' => $newsletterCampaign->getPostalCode(),
            'profileImg' => $newsletterCampaign->getProfileImg(),
            'pwd' => $newsletterCampaign->getPwd(),
            'status' => (1 === $newsletterCampaign->getStatus()) ? 'Online' : 'Offline',
            'travelNewsLetter' => (1 === $newsletterCampaign->getTravelNewsLetter()) ? 'Yes' : 'No',
            'weeklyNewsLetter' => (1 === $newsletterCampaign->getWeeklyNewsLetter()) ? 'Yes' : 'No',
            'username' => $newsletterCampaign->getUsername()
        );
        return new Hal('/newsletterCampaigns/'.$newsletterCampaign->getId(), $newsletterCampaignData);
    }
}
