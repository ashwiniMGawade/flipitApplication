<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Repository\VisitorRepositoryInterface;

class GetVisitorsUsecase
{
    protected $visitorRepository;

    public function __construct(VisitorRepositoryInterface $visitorRepository)
    {
        $this->visitorRepository = $visitorRepository;
    }

    public function execute($conditions = array(), $request = array())
    {
        $filters = array('deleted' => 0);
        if (!is_array($conditions)) {
            throw new \Exception('Invalid Parameters');
        }
        if (!empty($conditions['firstName']) && $conditions['firstName'] != 'undefined') {
            $filters['firstName'] = $conditions['firstName'];
        }
        if (!empty($conditions['email']) && $conditions['email'] != 'undefined') {
            $filters['email'] = $conditions['email'];
        }
        $visitorData = $this->visitorRepository->findVisitors($filters, $request);

        return $visitorData;
    }
}
