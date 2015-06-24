<?php
namespace KC\Repository;
class MoneysavingArticle extends \Core\Domain\Entity\MoneysavingArticle
{
    public static function searchTopTenSaving($keyword, $flag)
    {
        $lastdata = self::getSaving();
        if (sizeof($lastdata)>0) {
            for ($i=0; $i<sizeof($lastdata); $i++) {
                $codevalues[$i]=$lastdata[$i]['moneysaving']['id'];
            }

            $codevalues = implode(",", $codevalues);
        } else {
            $codevalues = '0';
        }

        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
        ->select('o.title as title')
        ->from('\Core\Domain\Entity\Articles', 'o')
        ->where('o.deleted = '.$flag)
        ->andWhere("o.title LIKE '$keyword%'")
        ->setParameter(1, $codevalues)
        ->andWhere($entityManagerLocale->expr()->notIn('o.id', '?1'))
        ->orderBy('o.title', 'ASC')
        ->setMaxResults(10);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getSaving()
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
        ->select('p,o')
        ->from('\Core\Domain\Entity\MoneysavingArticle', 'p')
        ->leftJoin('p.moneysaving', 'o')
        ->orderBy('p.position', 'ASC');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function addSaving($title)
    {
        $title = addslashes($title);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('a')
        ->from('\Core\Domain\Entity\Articles', 'a')
        ->where('a.title =' ."'".$title."'")
        ->setMaxResults(1);
        $article = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $flag = '2';

        if (sizeof($article) > 0) {
            $queryBuilderArticle = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderArticle
            ->select('m')
            ->from('\Core\Domain\Entity\MoneysavingArticle', 'm')
            ->where('m.moneysaving =' .$article[0]['id']);
            $pc = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            if (sizeof($pc) > 0) {
            } else {
                $flag = '1';
                $queryBuilderPosition = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $queryBuilderPosition
                ->select('p.position')
                ->from('\Core\Domain\Entity\MoneysavingArticle', 'p')
                ->orderBy('p.position', 'DESC')
                ->setMaxResults(1);
                $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

                if (sizeof($data) > 0) {
                    $NewPos = $data[0]['position'] + 1;
                } else {
                    $NewPos = 1;
                }

                $entityManagerLocale  = \Zend_Registry::get('emLocale');
                $moneySavingArticle = new \KC\Entity\MoneysavingArticle();
                $moneySavingArticle->type = 'MN';
                $moneySavingArticle->moneysaving = $entityManagerLocale->find(
                    'KC\Entity\Articles',
                    $article[0]['id']
                );
                $moneySavingArticle->position = (intval($NewPos));
                $moneySavingArticle->created_at = new \DateTime('now');
                $moneySavingArticle->updated_at = new \DateTime('now');
                $moneySavingArticle->deleted = 0;
                $moneySavingArticle->status = 0;
                $entityManagerLocale->persist($moneySavingArticle);
                $entityManagerLocale->flush();
                $flag = 3;
            }
        }
        $art_id = $article[0]['id'];
        $authorId = self::getAuthorId($art_id);
        $uid = $authorId[0]['authorid'];
        $mostreadkey ="all_". "mostread".$uid ."_list";
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($mostreadkey);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_moneySaving_list');
        return $flag;
    }

    public static function deleteSaving($id, $position)
    {
        if ($id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\MoneysavingArticle', 'm')
                ->where('m.id ='.$id)
                ->getQuery();
            $query->execute();
            $queryBuilder->update('KC\Entity\MoneysavingArticle', 'p')
                ->set('p.position', 'p.position -1')
                ->where('p.position >' . $position)
                ->getQuery()->execute();
        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_moneySaving_list');
        $art_id = $id;
        $authorId = self::getAuthorId($art_id);
        $uid = $authorId[0]['authorid'];
        $mostreadkey ="all_". "mostread".$uid ."_list";
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($mostreadkey);
        return true ;
    }

    public static function moveUpSaving($id, $position)
    {
        $pos = (intval($position) - 1);

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('p')
            ->from('\Core\Domain\Entity\MoneysavingArticle', 'p')
            ->where('p.position = '.$pos);
        $PrevPc = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        if (count($PrevPc) > 0) {
            $queryBuilderPreviousPosition = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $queryBuilderPreviousPosition->update('KC\Entity\MoneysavingArticle', 'm')
                ->set('m.position', $position)
                ->where('m.id = ' . $PrevPc[0]['id'])
                ->getQuery()->execute();

            //change position of current element with postition + 1
            $queryBuilderNewPosition = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $queryBuilderNewPosition->update('KC\Entity\MoneysavingArticle', 'msa')
                ->set('msa.position', $pos)
                ->where('msa.id = ' . $id)
                ->getQuery()->execute();
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_moneySaving_list');
            return true ;
        }
        return false ;
    }

    public static function moveDownSaving($id, $position)
    {
        $pos = (intval($position) + 1);
        //find next element from database based of current
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('p')
            ->from('\Core\Domain\Entity\MoneysavingArticle', 'p')
            ->where('p.position = '.$pos);
        $PrevPc = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        //change position of next element with current
        if (count($PrevPc) > 0) {
            $queryBuilderPreviousPosition = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $queryBuilderPreviousPosition->update('KC\Entity\MoneysavingArticle', 'm')
                ->set('m.position', $position)
                ->where('m.id = '.$PrevPc[0]['id'])
                ->getQuery()->execute();

            //change position of current element with postition - 1
            $queryBuilderNewPosition = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $queryBuilderNewPosition->update('KC\Entity\MoneysavingArticle', 'msa')
                ->set('msa.position', $pos)
                ->where('msa.id = ' . $id)
                ->getQuery()->execute();
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_moneySaving_list');
            return true ;
        }
            return false;
    }

    public static function getmoneySavingArticle($flag)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
        ->select('p,o,a')
        ->from('\Core\Domain\Entity\MoneysavingArticle', 'p')
        ->leftJoin('p.moneysaving', 'o')
        ->leftJoin('o.articleImage', 'a')
        ->setMaxResults($flag)
        ->orderBy('p.position', 'ASC');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getAuthorId($artId)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
        ->select('a.authorid')
        ->from('\Core\Domain\Entity\Articles', 'a')
        ->where('a.id = '.$artId);
        $userId = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $userId;
    }
}