<?php
namespace admin;

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
        //$I->databaseSwitch();
       // $em = \Codeception\Module\Doctrine2::$em;
        $t =  $I->haveInRepository('KC\Entity\Settings', array('name' => 'test'));
        $I->persistEntity(
            new \KC\Entity\Settings,
            array(
            'name' => 'test',
            'created_at' => new \DateTime('now'),
            'updated_at' => new \DateTime('now'),
            'deleted' => 0,
            'value' => 123
            )
        );
        $test = $I->grabFromRepository('KC\Entity\Settings', 'value', array('name' => 'test'));
        //$em->getRepository('KC\Entity\Settings')->findOneBy(array('name' => 'test'));
        
    }
    public function test2(FunctionalTester $I, \Codeception\Scenario $scenario)
    {
        $I->databaseSwitch("_user");
        $em = \Codeception\Module\Doctrine2::$em;
        // echo "<pre>";
        // print_r($em1); die;
        $t =  $I->haveInRepository('KC\Entity\Website', array('name' => 'test'));
        $I->persistEntity(
            new \KC\Entity\Website,
            array(
            'name' => 'test',
            'created_at' => new \DateTime('now'),
            'updated_at' => new \DateTime('now'),
            'deleted' => 0,
            'url' => 123
            )
        );
        $test = $I->grabFromRepository('KC\Entity\Website', 'url', array('name' => 'test'));
        // $em->getRepository('KC\Entity\Website')->findOneBy(array('name' => 'test'));
        // if ($em->getConnection()->isTransactionActive()) {
        //     $em->getConnection()->rollback();
        // }
        // $this->clean();
    }
}
