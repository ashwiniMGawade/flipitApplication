<?php

class Splash extends BaseSplash
{
   public function getSplashTableData()
   {
        $splashTableData = Doctrine_Query::create()
                ->select('*')
                ->from('Splash')->fetchArray();
        return $splashTableData;
   }

   public function deleteSplashoffer()
   {
        Doctrine_Query::create()->delete()->from('Splash')->execute();
        return true;
   }
}
