<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Repository\SettingsRepositoryInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetSettingsUsecase
{
    private $settingRepository;
    private $htmlPurifier;
    private $errors;

    public function __construct(SettingsRepositoryInterface $settingRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->settingRepository        = $settingRepository;
        $this->htmlPurifier             = $htmlPurifier;
        $this->errors                   = $errors;
    }

    public function execute($conditions = array())
    {
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find setting.');
            return $this->errors;
        }
        $conditions = $this->htmlPurifier->purify($conditions);

        return $this->settingRepository->findBy('\Core\Domain\Entity\Settings', $conditions);
    }
}
