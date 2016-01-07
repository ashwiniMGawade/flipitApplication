<?php
namespace Core\Domain\Usecase\System;

use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Repository\LocaleSettingRepositoryInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetLocaleSettingsUsecase
{
    private $localeSettingRepository;
    private $htmlPurifier;
    private $errors;

    public function __construct(LocaleSettingRepositoryInterface $localeSettingRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->localeSettingRepository    = $localeSettingRepository;
        $this->htmlPurifier                    = $htmlPurifier;
        $this->errors                          = $errors;
    }

    public function execute($conditions = array(), $order = array(), $limit = 100, $offset = 0)
    {
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find record.');
            return $this->errors;
        }
        $conditions = $this->htmlPurifier->purify($conditions);

        $localeSettings = $this->localeSettingRepository->findBy('\Core\Domain\Entity\LocaleSettings', $conditions, $order, $limit, $offset);
        return $localeSettings;
    }
}
