<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\VisitorRepositoryInterface;

class UpdateVisitorUsecase
{
    protected $visitorRepository;

    public function __construct(VisitorRepositoryInterface $visitorRepository)
    {
        $this->visitorRepository = $visitorRepository;
    }

    public function execute($parameters)
    {
        if (!is_array($parameters) || empty($parameters) || !isset($parameters['email']) || !isset($parameters['event'])) {
            throw new \Exception('Invalid Parameters');
        }
        if (!$visitor = $this->visitorRepository->findOneBy('\Core\Domain\Entity\Visitor', array('email' => $parameters['email']))) {
            throw new \Exception('Invalid Email');
        }
        switch ($parameters['event']) {
            case 'open':
                $openCount = (int) $visitor->getMailOpenCount();
                $visitor->setMailOpenCount($openCount + 1);
                break;
            case 'click':
                $clickCount = (int) $visitor->getMailClickCount();
                $visitor->setMailClickCount($clickCount + 1);
                break;
            case 'soft_bounce':
                $softBounceCount = (int) $visitor->getMailSoftBounceCount();
                $newSoftBounceCount = $softBounceCount + 1;
                $visitor->setMailSoftBounceCount($newSoftBounceCount);
                if ($newSoftBounceCount >= 6) {
                    $visitor->setActive(0);
                    $visitor->setInactiveStatusReason('Soft Bounce');
                }
                break;
            case 'hard_bounce':
                $hardBounceCount = (int) $visitor->getMailHardBounceCount();
                $newHardBounceCount = $hardBounceCount + 1;
                $visitor->setMailHardBounceCount($newHardBounceCount);
                if ($newHardBounceCount >= 3) {
                    $visitor->setActive(0);
                    $visitor->setInactiveStatusReason('Hard Bounce');
                }
                break;
            default:
                throw new \Exception('Invalid Event');
                break;
        }
        return $this->visitorRepository->save($visitor);
    }
}
