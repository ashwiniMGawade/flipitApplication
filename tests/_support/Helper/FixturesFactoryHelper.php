<?php
namespace Helper;

class FixturesFactoryHelper
{
    public function execute($I)
    {
        $site = new SiteFixturesHelper();
        $site->execute($I);
        $user = new UserFixturesHelper();
        $user->execute($I);
    }
}
