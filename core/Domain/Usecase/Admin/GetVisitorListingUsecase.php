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
                    'id' => $visitor->id,
                    'firstName' => $visitor->firstName,
                    'lastName' => $visitor->lastName,
                    'email' => $visitor->email,
                    'weeklyNewsLetter' => $visitor->weeklyNewsLetter,
                    'created_at' => $visitor->created_at,
                    'active' => $visitor->active
                );
            }
        }
        return $returnData;
    }
}
