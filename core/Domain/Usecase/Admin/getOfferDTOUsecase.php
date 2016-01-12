<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Adapter\PurifierInterface;
use Core\Domain\Repository\OfferRepositoryInterface;
use Core\Service\Errors\ErrorsInterface;

class GetOfferDTOUsecase
{
    private $offerRepository;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(OfferRepositoryInterface $offerRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->offerRepository = $offerRepository;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors = $errors;
    }

    public function execute($conditions)
    {
        $conditions = $this->htmlPurifier->purify($conditions);
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find offer.');
            return $this->errors;
        }

        $offer = $this->offerRepository->getOfferDTO('\Core\Domain\Entity\Offer', $conditions);

        if (is_object($offer) === false) {
            $this->errors->setError('Offer not found');
            return $this->errors;
        }
        return $offer;
    }
}
