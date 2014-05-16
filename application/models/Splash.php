<?php

class Splash extends BaseSplash
{
   public function getSplashInformation()
   {
        $splashInformation = Doctrine_Query::create()
                ->select('*')
                ->from('Splash')->fetchArray();
        return $splashInformation;
   }

   public function deleteSplashoffer()
   {
        Doctrine_Query::create()->delete()->from('Splash')->execute();
        return true;
   }
}
