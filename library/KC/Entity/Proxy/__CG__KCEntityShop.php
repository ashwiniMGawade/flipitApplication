<?php

namespace Proxy\__CG__\KC\Entity;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Shop extends \KC\Entity\Shop implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;

            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }

            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    
    public function __get($property)
    {
        $this->__load();
        return parent::__get($property);
    }

    public function __set($property, $value = '')
    {
        $this->__load();
        return parent::__set($property, $value);
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'name', 'permaLink', 'metaDescription', 'usergenratedcontent', 'notes', 'deepLink', 'deepLinkStatus', 'refUrl', 'actualUrl', 'affliateProgram', 'title', 'subTitle', 'overriteTitle', 'overriteSubtitle', 'overriteBrowserTitle', 'shopText', 'views', 'howToUse', 'Deliverytime', 'returnPolicy', 'freeDelivery', 'deliveryCost', 'status', 'offlineSicne', 'accoutManagerId', 'accountManagerName', 'contentManagerId', 'contentManagerName', 'screenshotId', 'keywordlink', 'deleted', 'created_at', 'updated_at', 'howtoTitle', 'howtoSubtitle', 'howtoMetaTitle', 'howtoMetaDescription', 'ideal', 'qShops', 'freeReturns', 'pickupPoints', 'mobileShop', 'service', 'serviceNumber', 'discussions', 'displayExtraProperties', 'showSignupOption', 'addtosearch', 'customHeader', 'totalviewcount', 'showSimliarShops', 'showChains', 'chainItemId', 'chainId', 'strictConfirmation', 'howToIntroductionText', 'brandingcss', 'lightboxsecondtext', 'lightboxfirsttext', 'howtoguideslug', 'moretextforshop', 'howtoSubSubTitle', 'shopsViewedIds', 'shopAndOfferClickouts', 'lastSevendayClickouts', 'customtextposition', 'showcustomtext', 'customtext', 'futurecode', 'logo', 'adminfevoriteshops', 'conversions', 'offer', 'offerNews', 'popularshop', 'articlestore', 'shopsofKeyword', 'categoryshops', 'relatedshops', 'howtochapter', 'viewcount', 'ballontext', 'shop', 'affliatenetwork', 'shopPage', 'howtousesmallimage', 'howtousebigimage', 'visitors', 'keywords', 'favoriteshops');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields as $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}