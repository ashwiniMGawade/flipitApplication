<?php
use \UnitTester;

class helloworldCest
{
    public function _before()
    {
    }

    public function _after()
    {
    }

    protected function orderNames($order)
    {
        $frontEndHelperObject = new FrontEnd_Helper_viewHelper();
        $unSortedNames = array('Amit', 'Rajbir', 'Kraj');
        $sortedNamesBySort = $frontEndHelperObject->sortNamesByOrder($unSortedNames, $order);
        return array(
            'sortedNamesBySort' => $sortedNamesBySort
        );
    }

    public function tryToAscNamesToTest(UnitTester $I)
    {
        $sortedNames = array('Amit', 'Kraj', 'Rajbir');
        $orderParameters = $this->orderNames('asc');
        $I->assertEquals($sortedNames, $orderParameters['sortedNamesBySort']);
    }

    public function tryToDescNamesToTest(UnitTester $I)
    {
        $sortedNames = array('Rajbir', 'Kraj', 'Amit');
        $orderParameters = $this->orderNames('desc');
        $I->assertEquals($sortedNames, $orderParameters['sortedNamesBySort']);
    }
}