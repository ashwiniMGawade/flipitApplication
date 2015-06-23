<?php
class AlterUrlOfferShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->changeColumn( 'shop', 'refurl', 'string', 512 );
        $this->changeColumn( 'shop', 'actualurl', 'string', 512 );

        $this->changeColumn( 'offer', 'refurl', 'string', 512 );
        $this->changeColumn( 'offer', 'refOfferUrl', 'string', 512 );
    }

    public function down()
    {
        $this->changeColumn( 'shop', 'refurl', 'string', 256);
        $this->changeColumn( 'shop', 'actualurl', 'string', 256);

        $this->changeColumn( 'offer', 'refURL','string', 256);
        $this->changeColumn( 'offer', 'refofferurl', 'string', 256);
    }
}
