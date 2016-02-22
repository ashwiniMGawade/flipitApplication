<?php
namespace Core\Domain\Repository;

interface VisitorRepositoryInterface extends BaseRepositoryInterface
{
    public function deactivateSleeper($conditions);
    public function getNewsletterReceipientCount();
}
