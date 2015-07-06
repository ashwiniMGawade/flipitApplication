<?php
namespace Core\Domain\Usecase\Guest;

use \Core\Domain\Repository\PageRepositoryInterface;

class GetHomePageUsecase
{

    private $pageRepository;

    public function __construct(PageRepositoryInterface $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    public function excute($uri)
    {
        $conditions = array('permalink' => $uri);
        return $this->pageRepository->findOneBy('\Core\Domain\Entity\Page', $conditions);
    }
}
