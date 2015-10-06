<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Entity\URLSetting;

class CreateURLSettingUsecase
{
    public function execute()
    {
        return new URLSetting();
    }
}
