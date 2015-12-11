<?php
namespace KC\Repository;
class ShopExcelInformation extends \Core\Domain\Entity\ShopExcelInformation
{
    public static function getExcelInfo($shopExcelParameters, $type = '')
    {
        $searchText = isset($shopExcelParameters["SearchText"]) && $shopExcelParameters["SearchText"] != 'undefined'
            ? $shopExcelParameters["SearchText"] : '';
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $excelInformation = $queryBuilder
            ->from("\Core\Domain\Entity\ShopExcelInformation", "sei");

        if (empty($type)) {
            $excelInformation = $excelInformation->where("sei.deleted = 0");
        } else {
            $excelInformation = $excelInformation->andWhere('sei.deleted = 1');
        }
        $request  = \DataTable_Helper::createSearchRequest(
            $shopExcelParameters,
            array()
        );
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($excelInformation)
            ->add('number', 'sei.id')
            ->add('text', 'sei.created_at')
            ->add('number', 'sei.totalShopsCount')
            ->add('number', 'sei.passCount')
            ->add('number', 'sei.failCount')
            ->add('text', 'sei.updated_at')
            ->add('text', 'sei.userName');
        $excelImportInformation = $builder->getTable()->getResponseArray();
        return $excelImportInformation;
    }

    public static function moveCodeAlertToTrash($shopExcelId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->update('\Core\Domain\Entity\ShopExcelInformation', 'c')
            ->set('c.deleted', 1)
            ->where('c.id ='.$shopExcelId)
            ->getQuery();
        $query->execute();
        return true;
    }

    public static function saveShopExcelData($totalShopsCount, $userName, $fileName, $passcount = '', $failCount = '')
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $excelData = new \Core\Domain\Entity\ShopExcelInformation();
        $excelData->totalShopsCount = $totalShopsCount;
        $excelData->userName = $userName;
        $excelData->passCount = $passcount;
        $excelData->failCount = $failCount;
        $excelData->filename = $fileName;
        $excelData->deleted = 0;
        $excelData->created_at = new \DateTime('now');
        $excelData->updated_at = new \DateTime('now');
        $entityManagerLocale->persist($excelData);
        $entityManagerLocale->flush();
        return true;
    }

    public static function checkExistingShopFile()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('sei')
            ->from("\Core\Domain\Entity\ShopExcelInformation", "sei")
            ->where("sei.deleted = 0")
            ->andWhere("sei.passCount = 0")
            ->andWhere("sei.failCount = 0")
            ->andWhere("sei.filename <> 0")
            ->andWhere("sei.totalShopsCount <> 0");
        $excelInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return empty($excelInformation) ? true : false;
    }
}