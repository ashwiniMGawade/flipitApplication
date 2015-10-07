<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\URLSetting;
use \Core\Domain\Repository\URLSettingRepositoryInterface;

class DeleteURLSettingUsecase
{

    private $urlSettingRepository;

    public function __construct(URLSettingRepositoryInterface $urlSettingRepository)
    {
        $this->urlSettingRepository = $urlSettingRepository;
    }

    public function execute(URLSetting $urlSetting)
    {
        return $this->urlSettingRepository->remove($urlSetting);
    }
}
