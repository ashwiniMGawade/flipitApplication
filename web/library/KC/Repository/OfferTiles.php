<?php
namespace KC\Repository;

class OfferTiles extends \Core\Domain\Entity\OfferTiles
{
    public static function addOfferTile($params, $ext = "")
    {
        if ($params['forDelete']) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
            ->select('t')
            ->from('KC\Entity\OfferTiles', 't')
            ->where('t.id = '.$params['forDelete']);
            $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        } else {
            $data = new KC\Entity\OfferTiles();
        }

        $data->type = $params['hidtype'];
        $data->label = $params['label'];
        $data->name = $params['hidimage'];
        $data->ext = $ext;
        $data->path = "images/upload/offertiles/";
        $data->position = $params['position'];
        \Zend_Registry::get('emLocale')->persist($data);
        \Zend_Registry::get('emLocale')->flush();
        $id = $data->id;
        return $id;
    }

    public static function getOfferTilesList($TileId)
    {
        $oneTile = '';
        if ($TileId) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
            ->select('t')
            ->from('KC\Entity\OfferTiles', 't')
            ->where('t.id ='.$TileId);
            $oneTile = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $oneTile;
    }

    public static function deleteMenuRecord($params)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->delete('KC\Entity\OfferTiles', 't')
        ->where('t.id = '.@$params['id'])
        ->getQuery();
        $query->execute();
        return true;
    }

    public static function getAllTiles()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('t.id, t.label, t.type, t.ext, t.path, t.name, t.position')
        ->from('KC\Entity\OfferTiles', 't');
        $allTile = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $Ar = array();
        foreach ($allTile as $t) {

            $Ar[$t['type']][] = $t;
        }
        return $Ar;
    }
}
