<?php
namespace Core\Domain\Usecase\System;

use \Core\Domain\Repository\SettingsRepositoryInterface;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetSettingUsecase
{
    protected $settingsRepository;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(SettingsRepositoryInterface $settingsRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->settingsRepository    = $settingsRepository;
        $this->htmlPurifier             = $htmlPurifier;
        $this->errors                   = $errors;
    }

    public function execute($conditions)
    {
        $conditions = $this->htmlPurifier->purify($conditions);
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find setting.');
            return $this->errors;
        }

        $setting = $this->settingsRepository->findOneBy('\Core\Domain\Entity\Settings', $conditions);

        if (false === is_object($setting)) {
            $this->errors->setError('Setting not found');
            return $this->errors;
        }
        return $setting;
    }
}
