<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Entity\Widget;

class CreateWidgetUsecase
{
    public function execute()
    {
        return new Widget();
    }
}
