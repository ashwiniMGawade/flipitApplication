<?php

/**
 * Offer
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class Vote extends BaseVote
{
    /**
     * getofferList(deleted and non deleted by flag) fetches all record from database
     * also search according to keyword if present.
     * @param array $params
     * @return array $offerList
     * @version 1.0
     * @author
     */
    public static function getofferVoteList($idOffer)
    {
        $offerVotesList = Doctrine_Query::create()
        ->select('v.*')
        ->from("Vote v")
        ->where("v.offerID="."'$idOffer'")
        ->andWhere("v.deleted=0")
        ->orderBy("v.id DESC")->fetchArray();
        //condition for editor

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
        /*	$u = Doctrine_Query::create()->update('Vote')
            ->set('deleted', '1')->where('id=' . $id);
            $u->execute(); */

            $v = Doctrine_Query::create()->delete('Vote v')->where('v.id ='.$id);
            $v->execute();

        } else {
            $id = null;
        }
       return $id;
    }

    public function doVote($params)
    {
        $idOffer = $params['id'];
        $offerVotesList = Doctrine_Query::create()
        ->select('v.*')
        ->from("Vote v")
        ->where("v.offerID="."'$idOffer'")
        ->andWhere("v.ipAddress='".$_SERVER['REMOTE_ADDR']."'")
        ->andWhere("v.deleted=0")
        ->fetchArray();
      if(count($offerVotesList)>0){
        self :: deleteVote($offerVotesList[0]['id']);
      }
     $this->offerId = $params['id'];
      if($params['vote']=='1'){
          $this->vote = 'positive';
      }else{
          $this->vote = 'negative';
      }

       $this->ipAddress = $_SERVER['REMOTE_ADDR'];
       $this->save();
       $id = $this->id;
       $positiveVotes = Doctrine_Query::create()
      ->select('count(*) as cnt')
      ->from("Vote v")
      ->where("v.offerID="."'$idOffer'")
      ->andWhere("v.deleted=0")
      ->andWhere("v.vote='positive'")
      ->fetchArray();

       $negativeVotes = Doctrine_Query::create()
       ->select('count(*) as cnt')
       ->from("Vote v")
       ->where("v.offerID="."'$idOffer'")
       ->andWhere("v.deleted=0")
       ->andWhere("v.vote='negative'")
       ->fetchArray();
       $arr = array();
       $arr['voteId'] = $id;
        if($params['vote']=='1'){
         $arr['vote'] = (($positiveVotes[0]['cnt'])/($negativeVotes[0]['cnt']+$positiveVotes[0]['cnt']))*100 ;
        }else{
         $arr['vote'] = (($negativeVotes[0]['cnt'])/($negativeVotes[0]['cnt']+$positiveVotes[0]['cnt']))*100 ;
        }
      return $arr;
    }


    public function addfeedback($params)
    {
        $v = Doctrine_Core::getTable("Vote")->find($params['id']);
        $v->moneySaved = $params['amount'];
        $v->product = $params['product'];
        $v->save();
        return '1';
    }


    /**
     * addVote
     * give votes to an ofer
     *
     * @param  integer $offer offer id
     * @param  string $vote 'negative', 'positive'
     */
    public static function addVote($offer,$vote)
    {
        $clientIP = FrontEnd_Helper_viewHelper::getRealIpAddress();
        $ip = ip2long($clientIP);

        # check for previous vote from same ip
        $data = Doctrine_Query::create()
        ->select('count(v.id) as exists')
        ->from('Vote v')
        ->where('v.offerId= ?' ,  $offer )
        ->andWhere("v.visitorId = ? " , Auth_VisitorAdapter::getIdentity()->id)
        ->andWhere("v.deleted=0")
        ->fetchOne(null, Doctrine::HYDRATE_ARRAY);

        if($data['exists'] == 0 ){
            # save vote for an offer
            $cnt  = new Vote();
            $cnt->offerId = $offer;
            $cnt->visitorId = Auth_VisitorAdapter::getIdentity()->id ;
            $cnt->vote = $vote;
            $cnt->save();

            return true ;
        } else {
            return false ;
        }

    }


}