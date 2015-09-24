<?php
namespace Core\Domain\Usecase\Guest;

use Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Repository\OfferRepositoryInterface;
use Core\Service\Errors\ErrorsInterface;

class GetOffersUsecase
{
    private $offerRepository;
    private $htmlPurifier;
    private $errors;

    public function __construct(OfferRepositoryInterface $offerRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->offerRepository   = $offerRepository;
        $this->htmlPurifier      = $htmlPurifier;
        $this->errors            = $errors;
    }

    public function execute($conditions = array())
    {
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find offers.');
            return $this->errors;
        }
        $conditions = $this->htmlPurifier->purify($conditions);

        return $this->offerRepository->findBy('\Core\Domain\Entity\Offer', $conditions);
    }
}
