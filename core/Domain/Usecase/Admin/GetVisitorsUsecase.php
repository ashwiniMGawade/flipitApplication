<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\VisitorRepositoryInterface;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetVisitorsUsecase
{
    protected $visitorRepository;
    private $htmlPurifier;
    private $errors;

    public function __construct(VisitorRepositoryInterface $visitorRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->visitorRepository = $visitorRepository;
        $this->htmlPurifier      = $htmlPurifier;
        $this->errors            = $errors;
    }

    public function execute($conditions = array(), $order = array(), $limit = 100, $offset = 0, $isPaginated = false)
    {

        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find record.');
            return $this->errors;
        }
        if (false === $isPaginated) {
            $visitorData = $this->visitorRepository->findBy('\Core\Domain\Entity\Visitor', $conditions, $order,
            $limit, $offset);
        } else {
            $visitorData = $this->visitorRepository->findAllPaginated('\Core\Domain\Entity\Visitor', $conditions,
                $order, $limit, $offset);
        }
        return $visitorData;
    }
}
