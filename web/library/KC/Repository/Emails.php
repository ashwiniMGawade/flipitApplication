<?php
namespace KC\Repository;

class Emails extends \Core\Domain\Entity\Emails
{
    public static function getAllEmailsContent($params)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $qb = $queryBuilder
            ->from('\Core\Domain\Entity\Emails', 'e')
            ->where("e.deleted='0'");

        $request  = \DataTable_Helper::createSearchRequest(
            $params,
            array('id', 'type','send_date','send_counter')
        );

        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($qb)
            ->add('number', 'e.id')
            ->add('text', 'e.type')
            ->add('number', 'e.send_date')
            ->add('number', 'e.send_counter');
        $result = $builder->getTable()->getResponseArray();
        return $result;
    }

    public static function getTemplateContent($id)
    {

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('e.header, e.footer, e.body')
            ->from('\Core\Domain\Entity\Emails', 'e')
            ->where("e.deleted= 0")
            ->andWhere("e.id=". $id);
        $templatedata = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $templatedata;

    }

    public static function updateHeaderContent($data, $id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->update("\KC\Entity\Emails", "e")
           ->set('e.header', "'$data'")
           ->where('e.id=' . $id);
        $query->getQuery()->execute();
    }

    public static function updateFooterContent($data, $id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update("\KC\Entity\Emails", "e")
           ->set('e.footer', "'$data'")
           ->where('e.id=' . $id);
        $query->getQuery()->execute();

    }

    public static function updateBodyContent($data, $id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update("\KC\Entity\Emails", "e")
           ->set('e.body', "'$data'")
           ->where('e.id=' . $id);
        $query->getQuery()->execute();
    }


    public static function getTemplateId($type)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select("e.id")
            ->from("\KC\Entity\Emails", "e")
            ->where("e.deleted='0'")
            ->andWhere("e.type='".$type."'");
        $id = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $id;
    }

    public static function getSendCounter($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select("e.send_counter")
            ->from("\KC\Entity\Emails", "e")
            ->where("e.deleted='0'")
            ->andWhere("e.id=".$id);
        $count = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $count;
    }

    public static function updateDateCounter($count, $id)
    {
        $date = new Zend_Date();
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update("\KC\Entity\Emails", "e")
            ->set('send_counter', "'$count'")
            ->set('send_date', "'$date'")
            ->where('id=' . $id);
        $query->getQuery()->execute();
    }
}
