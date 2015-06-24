<?php
/*namespace admin;

use \FunctionalTester;

class localeSettingsCest
{
    public function _before()
    {
       
    }

    public function _after()
    {
       
    }

    // tests
   

    public function test(FunctionalTester $I, \Codeception\Scenario $scenario)
    {
        $t =  $I->haveInRepository(
            '\Core\Domain\Entity\Settings',
            array(
                'name' => 'test',
                'created_at' => new \DateTime('now'),
                'updated_at' => new \DateTime('now'),
                'deleted' => 0,
                'value' => 123
            )
        );
        $I->persistEntity(
            new \Core\Domain\Entity\Settings,
            array(
            'name' => 'test1',
            'created_at' => new \DateTime('now'),
            'updated_at' => new \DateTime('now'),
            'deleted' => 0,
            'value' => 1232
            )
        );
        $test = $I->grabFromRepository('\Core\Domain\Entity\Settings', 'value', array('name' => 'test'));
        //$em->getRepository('\Core\Domain\Entity\Settings')->findOneBy(array('name' => 'test'));
        
    }
    public function test2(FunctionalTester $I, \Codeception\Scenario $scenario)
    {
        $I->databaseSwitch("_user");
        $t =  $I->haveInRepository(
            '\Core\Domain\Entity\User\Website',
            array(
                'name' => 'test',
                'created_at' => new \DateTime('now'),
                'updated_at' => new \DateTime('now'),
                'deleted' => 0
            )
        );
        $I->persistEntity(
            new \Core\Domain\Entity\User\Website,
            array(
            'name' => 'test2',
            'created_at' => new \DateTime('now'),
            'updated_at' => new \DateTime('now'),
            'deleted' => 0,
            'url' => 123
            )
        );
        $test = $I->grabFromRepository('\Core\Domain\Entity\User\Website', 'url', array('name' => 'test'));
    }
}*/
