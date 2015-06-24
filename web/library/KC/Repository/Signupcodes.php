<?php
namespace KC\Repository;

class Signupcodes extends \Core\Domain\Entity\Signupcodes
{
    public static function getfreeCodelogin()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('p.id, p.entered_uid, p.code,p.created_at')
            ->from('\Core\Domain\Entity\Signupcodes', 'p')
            ->orderBy('p.code', 'ASC');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function deletecodebyid($id)
    {
        if ($id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('\Core\Domain\Entity\Signupcodes', 'sc')
                ->where("sc.id=" .$id)
                ->getQuery()->execute();

            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_signupcode_list');
        }
    }

    public static function addcode($codetext, $userid)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $code = new \Core\Domain\Entity\Signupcodes();
        $code->entered_uid = $userid;
        $code->code = "$codetext";
        $code->created_at = new \DateTime('now');
        $code->updated_at = new \DateTime('now');
        $entityManagerLocale->persist($code);
        $entityManagerLocale->flush();

        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_signupcode_list');
    }
    public static function getcodebytxt($txt)
    {
        if ($txt) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->select('p.id')
                ->from('\Core\Domain\Entity\Signupcodes', 'p')
                ->where('code=' . "'$txt'");
            $pc = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            if (count($pc)>0) {
                return $pc[0]['id'];
            } else {
                return 0;
            }
        }
    }
}
