<?php
namespace Core\Domain\Usecase\Guest;

use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Repository\DashboardRepositoryInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetDashboardUsecase
{
    protected $dashboardRepository;
    protected $htmlPurifier;
    protected $errors;

    public function __construct(
        DashboardRepositoryInterface $dashboardRepository,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->dashboardRepository = $dashboardRepository;
        $this->htmlPurifier = $htmlPurifier;
        $this->errors = $errors;
    }

    public function execute($conditions)
    {
        $conditions = $this->htmlPurifier->purify($conditions);

        $dashboard = $this->dashboardRepository->findOneBy('\Core\Domain\Entity\Dashboard', $conditions);

        if (false === is_object($dashboard)) {
            $this->errors->setError('Dashboard data not found');
            return $this->errors;
        }
        return $dashboard;
    }
}
