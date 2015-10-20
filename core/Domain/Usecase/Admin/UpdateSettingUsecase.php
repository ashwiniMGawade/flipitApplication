<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\Settings;
use \Core\Domain\Repository\SettingsRepositoryInterface;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Validator\SettingsValidator;
use \Core\Service\Errors\ErrorsInterface;

class UpdateSettingUsecase
{
    private $settingsRepository;

    protected $htmlPurifier;

    protected $settingsValidator;

    protected $errors;

    public function __construct(
        SettingsRepositoryInterface $settingsRepository,
        SettingsValidator $settingsValidator,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->settingsRepository   = $settingsRepository;
        $this->htmlPurifier         = $htmlPurifier;
        $this->settingsValidator    = $settingsValidator;
        $this->errors               = $errors;
    }

    public function execute(Settings $setting, $params = array())
    {
        $params = $this->htmlPurifier->purify($params);
        if (isset($params['value'])) {
            $setting->setValue($params['value']);
        }
        $setting->setUpdatedAt(new \DateTime('now'));

        return $this->settingsRepository->save($setting);
    }
}
