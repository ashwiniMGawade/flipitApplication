<?php
namespace Core\Domain\Usecase\Guest;

use Core\Domain\Adapter\PurifierInterface;
use Core\Domain\Repository\ViewCountRepositoryInterface;
use Core\Service\Errors\ErrorsInterface;

class GetViewCountsUsecase
{
    protected $viewCountRepository;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(ViewCountRepositoryInterface $viewCountRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->viewCountRepository = $viewCountRepository;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors = $errors;
    }

    public function execute($conditions)
    {
        $conditions = $this->htmlPurifier->purify($conditions);
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find ViewCount.');
            return $this->errors;
        }
        $viewCount = $this->viewCountRepository->findBy('\Core\Domain\Entity\ViewCount', $conditions);
        return $viewCount;
    }
}
