<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Entity\URLSetting;
use \Core\Domain\Repository\URLSettingRepositoryInterface;
use \Core\Domain\Validator\UrlSettingValidator;
use \Core\Service\Errors\ErrorsInterface;

class UpdateURLSettingUsecase
{
    private $urlSettingRepository;

    protected $urlSettingValidator;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(
        URLSettingRepositoryInterface $urlSettingRepository,
        UrlSettingValidator $urlSettingValidator,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->urlSettingRepository = $urlSettingRepository;
        $this->urlSettingValidator  = $urlSettingValidator;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute(URLSetting $urlSetting, $params = array())
    {
        if (empty($params)) {
            $this->errors->setError('Invalid Parameters');
            return $this->errors;
        }
        $params = $this->htmlPurifier->purify($params);

        if (isset($params['url'])) {
            $urlSetting->setUrl($params['url']);
        }
        if (isset($params['status'])) {
            $urlSetting->setStatus((int) $params['status']);
        }
        $urlSetting->setUpdatedAt(new \DateTime('now'));

        $validationResult = $this->urlSettingValidator->validate($urlSetting);

        if (true !== $validationResult && is_array($validationResult)) {
            $this->errors->setErrors($validationResult);
            return $this->errors;
        }
        return $this->urlSettingRepository->save($urlSetting);
    }
}
