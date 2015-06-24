<?php
namespace KC\Repository;

class Vote extends \Core\Domain\Entity\Votes
{
    public static function getofferVoteList($idOffer)
    {
        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('v')
            ->from('\Core\Domain\Entity\Votes', 'v')
            ->setParameter(1, $entityManagerLocale->find('\Core\Domain\Entity\Offer', $idOffer))
            ->where('v.offer = ?1')
            ->setParameter(2, '0')
            ->andWhere('v.deleted = ?2')
            ->orderBy('v.id', 'DESC');
        $offerVotesList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $offerVotesList;
    }

    /**
     * Permanent delete record from database.
     * @param integer $id
     * @version 1.0
     * @author Raman
     * @return integer $id
     */
    public static function deleteVote($id)
    {
        if ($id) {
            //find record by id and change status (deleted=1)
            /*  $u = Doctrine_Query::create()->update('Vote')
            ->set('deleted', '1')->where('id=' . $id);
            $u->execute(); */

            $entityManagerLocale  =\Zend_Registry::get('emLocale');
            $v =  $entityManagerLocale->find('\Core\Domain\Entity\Votes', $id);
            $entityManagerLocale->remove($v);
            $entityManagerLocale->flush();

        } else {
            $id = null;
        }
        return $id;
    }

    public function doVote($params)
    {
        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        $idOffer = $params['id'];
        $queryBuilder = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->select('v')
            ->from('\Core\Domain\Entity\Votes', 'v')
            ->setParameter(1, $entityManagerLocale->find('\Core\Domain\Entity\Offer', $idOffer))
            ->where('v.offer = ?1')
            ->setParameter(2, '0')
            ->andWhere('v.deleted = ?2')
            ->setParameter(3, $_SERVER['REMOTE_ADDR'])
            ->andWhere('v.ipaddress = ?3');
        $offerVotesList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
     
        if (count($offerVotesList) > 0) {
            self::deleteVote($offerVotesList[0]['id']);
        }
        $vote  = new \Core\Domain\Entity\Votes();
        $vote->offer = $entityManagerLocale->find('\Core\Domain\Entity\Offer', $params['id']);
        if ($params['vote']=='1') {
            $vote->vote = 'positive';
        } else {
            $vote->vote = 'negative';
        }

        $vote->ipaddress = $_SERVER['REMOTE_ADDR'];
        $vote->created_at = new \DateTime('now');
        $vote->updated_at = new \DateTime('now');
        $vote->moneySaved = '0';
        $vote->status = '1';
        $vote->deleted = '0';
        $entityManagerLocale->persist($vote);
        $entityManagerLocale->flush();
        $id = $vote->getId();

        $queryBuilder = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->select('count(v) as cnt')
            ->from('\Core\Domain\Entity\Votes', 'v')
            ->setParameter(1, $entityManagerLocale->find('\Core\Domain\Entity\Offer', $idOffer))
            ->where('v.offer = ?1')
            ->setParameter(2, '0')
            ->andWhere('v.deleted = ?2')
            ->setParameter(3, 'positive')
            ->andWhere('v.vote = ?3');
        $positiveVotes = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $queryBuilder = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->select('count(v) as cnt')
            ->from('\Core\Domain\Entity\Votes', 'v')
            ->setParameter(1, $entityManagerLocale->find('\Core\Domain\Entity\Offer', $idOffer))
            ->where('v.offer = ?1')
            ->setParameter(2, '0')
            ->andWhere('v.deleted = ?2')
            ->setParameter(3, 'negative')
            ->andWhere('v.vote = ?3');
        $negativeVotes = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $arr = array();
        $arr['voteId'] = $id;
        if ($params['vote']=='1') {
            $arr['vote'] = (($positiveVotes[0]['cnt'])/($negativeVotes[0]['cnt']+$positiveVotes[0]['cnt']))*100 ;
        } else {
            $arr['vote'] = (($negativeVotes[0]['cnt'])/($negativeVotes[0]['cnt']+$positiveVotes[0]['cnt']))*100 ;
        }
        return $arr;
    }


    public function addfeedback($params)
    {
        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        $v = $entityManagerLocale->find('\Core\Domain\Entity\Votes', $params['id']);
        $v->moneySaved = $params['amount'];
        $v->product = $params['product'];
        $entityManagerLocale->persist($v);
        $entityManagerLocale->flush();
        return '1';
    }


    /**
     * addVote
     * give votes to an ofer
     *
     * @param  integer $offer offer id
     * @param  string $vote 'negative', 'positive'
     */
    public static function addVote($offer, $vote)
    {
        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        $clientIP = \FrontEnd_Helper_viewHelper::getRealIpAddress();
        $ip = ip2long($clientIP);

        # check for previous vote from same ip
        $queryBuilder = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->select('vt.id')
            ->from('\Core\Domain\Entity\Votes', 'vt')
            ->setParameter(1, $entityManagerLocale->find('\Core\Domain\Entity\Offer', $offer))
            ->where('vt.offer = ?1')
            ->setParameter(2, '0')
            ->andWhere('vt.deleted = ?2')
            ->setParameter(3, Auth_VisitorAdapter::getIdentity()->id)
            ->andWhere('vt.visitorid = ?3');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (empty($data)) {
            # save vote for an offer
            $cnt  = new \Core\Domain\Entity\Votes();
            $cnt->offer =  $entityManagerLocale->find('\Core\Domain\Entity\Offer', $offer);
            $cnt->visitorId = Auth_VisitorAdapter::getIdentity()->id;
            $cnt->vote = $vote;
            $cnt->created_at = new \DateTime('now');
            $cnt->updated_at = new \DateTime('now');
            $cnt->moneySaved = '0';
            $cnt->status = '1';
            $cnt->deleted = '0';
            $entityManagerLocale->persist($cnt);
            $entityManagerLocale->flush();
            return true ;
        } else {
            return false ;
        }

    }
}
