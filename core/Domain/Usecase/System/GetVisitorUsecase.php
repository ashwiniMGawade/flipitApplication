<?php
namespace Core\Domain\Usecase\System;

use \Core\Domain\Repository\VisitorRepositoryInterface;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetVisitorUsecase
{
    protected $visitorRepository;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(VisitorRepositoryInterface $visitorRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->visitorRepository = $visitorRepository;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute($conditions)
    {
        $conditions = $this->htmlPurifier->purify($conditions);
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find record.');
            return $this->errors;
        }

        $visitor = $this->visitorRepository->findOneBy('\Core\Domain\Entity\Visitor', $conditions);

        if (false === is_object($visitor)) {
            $this->errors->setError('Visitor not found');
            return $this->errors;
        }
        return $visitor;
    }
}
