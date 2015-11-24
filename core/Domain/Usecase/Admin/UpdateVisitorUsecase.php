<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\Visitor;
use \Core\Domain\Repository\VisitorRepositoryInterface;
use \Core\Domain\Validator\VisitorValidator;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Service\Errors\ErrorsInterface;

class UpdateVisitorUsecase
{
    protected $visitorRepository;
    protected $visitorValidator;
    protected $htmlPurifier;
    protected $errors;

    public function __construct(
        VisitorRepositoryInterface $visitorRepository,
        VisitorValidator $visitorValidator,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->visitorRepository = $visitorRepository;
        $this->visitorValidator = $visitorValidator;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute(Visitor $visitor, $params = array())
    {
        $params = $this->htmlPurifier->purify($params);

        if (isset($params['mailOpenCount'])) {
            $visitor->setMailOpenCount($params['mailOpenCount']);
        }
        if (isset($params['lastEmailOpenDate'])) {
            $visitor->setLastEmailOpenDate($params['lastEmailOpenDate']);
        }
        if (isset($params['mailClickCount'])) {
            $visitor->setMailClickCount($params['mailClickCount']);
        }
        if (isset($params['mailSoftBounceCount'])) {
            $visitor->setMailSoftBounceCount($params['mailSoftBounceCount']);
        }
        if (isset($params['mailHardBounceCount'])) {
            $visitor->setMailHardBounceCount($params['mailHardBounceCount']);
        }
        if (isset($params['active'])) {
            $visitor->setActive(0);
        }
        if (isset($params['inactiveStatusReason'])) {
            $visitor->setInactiveStatusReason($params['inactiveStatusReason']);
        }

        if (isset($params['activeCodeId'])) {
            $visitor->setActiveCodeid($params['activeCodeId']);
        }
        if (isset($params['changePasswordRequest'])) {
            $visitor->setChangepasswordrequest($params['changePasswordRequest']);
        }
        if (isset($params['codeAlert'])) {
            $visitor->setCodeAlert($params['codeAlert']);
        }
        if (isset($params['codeAlertSendDate'])) {
            $visitor->setCodeAlertSendDate($params['codeAlertSendDate']);
        }
        if (isset($params['currentLogIn'])) {
            $visitor->setCurrentLogIn($params['currentLogIn']);
        }
        if (isset($params['dateOfBirth'])) {
            $visitor->setDateOfBirth($params['dateOfBirth']);
        }
        if (isset($params['deleted'])) {
            $visitor->setDeleted($params['deleted']);
        }
        if (isset($params['email'])) {
            $visitor->setEmail($params['email']);
        }
        if (isset($params['fashionNewsLetter'])) {
            $visitor->setFashionNewsLetter($params['fashionNewsLetter']);
        }
        if (isset($params['firstName'])) {
            $visitor->setFirstName($params['firstName']);
        }
        if (isset($params['gender'])) {
            $visitor->setGender($params['gender']);
        }
        if (isset($params['interested'])) {
            $visitor->setInterested($params['interested']);
        }
        if (isset($params['lastLogIn'])) {
            $visitor->setLastLogIn($params['lastLogIn']);
        }
        if (isset($params['lastName'])) {
            $visitor->setLastName($params['lastName']);
        }
        if (isset($params['password'])) {
            $visitor->setPassword($params['password']);
        }
        if (isset($params['postalCode'])) {
            $visitor->setPostalCode($params['postalCode']);
        }
        if (isset($params['profileImg'])) {
            $visitor->setProfileImg($params['profileImg']);
        }
        if (isset($params['pwd'])) {
            $visitor->setPwd($params['pwd']);
        }
        if (isset($params['status'])) {
            $visitor->setStatus($params['status']);
        }
        if (isset($params['travelNewsLetter'])) {
            $visitor->setTravelNewsLetter($params['travelNewsLetter']);
        }
        if (isset($params['weeklyNewsLetter'])) {
            $visitor->setWeeklyNewsLetter($params['weeklyNewsLetter']);
        }
        if (isset($params['username'])) {
            $visitor->setUsername($params['username']);
        }
        if (isset($params['visitorKeyword'])) {
            $visitor->setVisitorKeyword($params['visitorKeyword']);
        }

        $visitor->setUpdatedAt(new \DateTime('now'));

        $validationResult = $this->visitorValidator->validate($visitor);

        if (true !== $validationResult && is_array($validationResult)) {
            $this->errors->setErrors($validationResult);
            return $this->errors;
        }
        return $this->visitorRepository->save($visitor);
    }
}
