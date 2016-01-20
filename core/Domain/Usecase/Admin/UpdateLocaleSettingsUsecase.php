<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Entity\LocaleSettings;
use \Core\Domain\Repository\LocaleSettingRepositoryInterface;
use \Core\Domain\Validator\LocaleSettingValidator;
use \Core\Service\Errors\ErrorsInterface;

class UpdateLocaleSettingsUsecase
{
    private $localeSettingRepository;

    protected $localeSettingValidator;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(
        LocaleSettingRepositoryInterface $localeSettingRepository,
        LocaleSettingValidator $localeSettingValidator,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->localeSettingRepository = $localeSettingRepository;
        $this->localeSettingValidator  = $localeSettingValidator;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute(LocaleSettings $localeSetting, $params = array())
    {
        if (empty($params)) {
            $this->errors->setError('Invalid Parameters');
            return $this->errors;
        }
        $params = $this->htmlPurifier->purify($params);

        if (isset($params['locale'])) {
            $localeSetting->setLocale($params['locale']);
        }
        if (isset($params['timezone'])) {
            $localeSetting->setTimezone($params['timezone']);
        }
        if (isset($params['expiredCouponLogo'])) {
            $localeSetting->setExpiredCouponLogo($params['expiredCouponLogo']);
        }

        $validationResult = $this->localeSettingValidator->validate($localeSetting);

        if (true !== $validationResult && is_array($validationResult)) {
            $this->errors->setErrors($validationResult);
            return $this->errors;
        }
        return $this->localeSettingRepository->save($localeSetting);
    }
}
