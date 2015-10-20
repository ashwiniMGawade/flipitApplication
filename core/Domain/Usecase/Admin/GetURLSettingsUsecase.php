<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Repository\URLSettingRepositoryInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetURLSettingsUsecase
{
    private $urlSettingRepository;
    private $htmlPurifier;
    private $errors;

    public function __construct(
        URLSettingRepositoryInterface $urlSettingRepository,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->urlSettingRepository = $urlSettingRepository;
        $this->htmlPurifier = $htmlPurifier;
        $this->errors = $errors;
    }

    public function execute($conditions = array())
    {
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find VWO Tag.');
            return $this->errors;
        }
        $conditions = $this->htmlPurifier->purify($conditions);

        return $this->urlSettingRepository->findBy('\Core\Domain\Entity\URLSetting', $conditions);
    }
}
