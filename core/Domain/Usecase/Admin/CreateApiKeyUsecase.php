<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Entity\User\ApiKey;

/**
 * Class CreateApiKeyUsecase
 *
 * @package Core\Domain\Usecase\Admin
 */
class CreateApiKeyUsecase
{
    public function execute()
    {
        return new ApiKey();
    }
}
