<?php
namespace Core\Domain\Usecase\Admin;

class UpdateVisitorUsecase
{
    public function execute($parameters)
    {
        if (!is_array($parameters) || empty($parameters)) {
            throw new \Exception('Invalid Parameters');
        }
        if (!isset($parameters['email']) || !isset($parameters['event'])) {
            throw new \Exception('Invalid Parameters');
        }
        if (!$this->validateEventName($parameters['event'])) {
            throw new \Exception('Invalid Event');
        }
    }

    private function validateEventName($eventName)
    {
        $validMandrillEvents = array(
            'open',
            'click',
            'soft_bounce',
            'hard_bounce'
        );
        if (!in_array($eventName, $validMandrillEvents, true)) {
            return false;
        }
        return true;
    }
}
