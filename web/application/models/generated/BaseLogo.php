<?php
//Doctrine_Manager::getInstance()->bindComponent('Logo', 'doctrine_site');

/**
 * BaseLogo
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property Doctrine_Collection $shop
 * @property Doctrine_Collection $offer
 * @property Doctrine_Collection $seenin
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseLogo extends Image
{
    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Shop as shop', array(
             'local' => 'id',
             'foreign' => 'logoId'));

        $this->hasMany('Offer as offer', array(
             'local' => 'id',
             'foreign' => 'offerLogoId'));

        $this->hasMany('SeenIn as seenin', array(
             'local' => 'id',
             'foreign' => 'logoId'));

        $this->hasMany('Page as page', array(
                'local' => 'id',
                'foreign' => 'logoId'));
    }
}
