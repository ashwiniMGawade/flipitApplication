<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\Settings;
use \Core\Domain\Repository\SettingsRepositoryInterface;
use \Core\Domain\Adapter\PurifierInterface;

class UpdateSettingUsecase
{
    private $settingsRepository;

    protected $htmlPurifier;

    public function __construct(
        SettingsRepositoryInterface $settingsRepository,
        PurifierInterface $htmlPurifier
    ) {
        $this->settingsRepository = $settingsRepository;
        $this->htmlPurifier     = $htmlPurifier;
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
