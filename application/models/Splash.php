<?php

class Splash extends BaseSplash
{
   public static function getSplashTableData()
   {
        $splashTableData = Doctrine_Query::create()
                ->select('*')
                ->from('Splash')->fetchArray();
        return $splashTableData;
   }

   public static function deleteSplashoffer()
   {
        $deleted = Doctrine_Query::create()->delete()->from('Splash')->execute();
        return true;
   }
}
