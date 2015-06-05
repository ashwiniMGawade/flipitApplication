<?php
class UpdatePopularityCountOffer extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->changeColumn('offer', 'popularityCount', 'integer', 11);
    }

    public function down()
    {
        $this->changeColumn('offer', 'popularityCount', 'integer', 11);
    }
}