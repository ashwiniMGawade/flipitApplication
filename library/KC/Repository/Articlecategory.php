<?php
namespace KC\Repository;

class Articlecategory extends \KC\Entity\Articlecategory
{
    public static function getartCategories()
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
        ->from('KC\Entity\Articlecategory', 'cat')
        ->where('cat.deleted = 0');
        $request  = \DataTable_Helper::createSearchRequest(
            '',
            array('cat.id','cat.name','cat.permalink','cat.metatitle')
        );
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($query)
            ->add('text', 'cat.name')
            ->add('text', 'cat.permalink')
            ->add('text', 'cat.metatitle');
        $result = $builder->getTable()->getResultQueryBuilder()->getQuery()->getArrayResult();
        $result = \DataTable_Helper::getResponse($result, $request);
        return $result;
    }
}