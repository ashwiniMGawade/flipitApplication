<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Repository\VisitorRepositoryInterface;

class GetVisitorListingUsecase
{
    protected $visitorRepository;

    public function __construct(VisitorRepositoryInterface $visitorRepository)
    {
        $this->visitorRepository = $visitorRepository;
    }

    public function execute($conditions = array(), $request = array())
    {
        $filters = $this->setFilters($conditions);
        $visitorData = $this->visitorRepository->findVisitors($filters, $request);
        $visitorData['visitors'] = $this->prepareData($visitorData['visitors']);
        return $visitorData;
    }

    private function setFilters($conditions)
    {
        $filters = array('deleted' => 0);
        if (!is_array($conditions)) {
            throw new \Exception('Invalid Parameters');
        }
        if (!empty($conditions['searchtext']) && $conditions['searchtext'] != 'undefined') {
            $filters['firstName'] = $conditions['searchtext'];
        }
        if (!empty($conditions['email']) && $conditions['email'] != 'undefined') {
            $filters['email'] = $conditions['email'];
        }
        return $filters;
    }

    private function prepareData($visitors)
    {
        $returnData = array();
        if (!empty($visitors)) {
            foreach ($visitors as $visitor) {
                $returnData[] = array(
                    'id' => $visitor->getId(),
                    'firstName' => $visitor->getFirstName(),
                    'lastName' => $visitor->getLastName(),
                    'email' => $visitor->getEmail(),
                    'weeklyNewsLetter' => $visitor->getWeeklyNewsLetter(),
                    'created_at' => $visitor->getCreatedAt(),
                    'active' => $visitor->getActive(),
                    'inactiveStatusReason' => $visitor->getInactiveStatusReason(),
                    'clicks' => $visitor->getMailClickCount(),
                    'opens' => $visitor->getMailOpenCount(),
                    'hard_bounces' => $visitor->getMailHardBounceCount(),
                    'soft_bounces' => $visitor->getMailSoftBounceCount()
                );
            }
        }
        return $returnData;
    }
}
