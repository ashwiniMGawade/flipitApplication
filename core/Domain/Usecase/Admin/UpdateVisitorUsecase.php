<?php
namespace Core\Domain\Usecase\Admin;

class UpdateVisitorUsecase
{
    public function execute($parameters)
    {
        if (!is_array($parameters) || empty($parameters)) {
            throw new \Exception('Invalid Parameters');
        }
        foreach ($parameters as $email => $event) {
        }
    }
}
