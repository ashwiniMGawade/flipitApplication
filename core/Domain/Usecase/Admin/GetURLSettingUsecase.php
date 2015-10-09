<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Repository\URLSettingRepositoryInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetURLSettingUsecase
{
    protected $urlSettingRepository;
    protected $htmlPurifier;
    protected $errors;

    public function __construct(
        URLSettingRepositoryInterface $urlSettingRepository,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->urlSettingRepository = $urlSettingRepository;
        $this->htmlPurifier = $htmlPurifier;
        $this->errors = $errors;
    }

    public function execute($conditions)
    {
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find VWO Tag.');
            return $this->errors;
        }
        $conditions = $this->htmlPurifier->purify($conditions);

        $URLSetting = $this->urlSettingRepository->findOneBy('\Core\Domain\Entity\URLSetting', $conditions);

        if (false === is_object($URLSetting)) {
            $this->errors->setError('VWO Tag not found');
            return $this->errors;
        }
        return $URLSetting;
    }
}
