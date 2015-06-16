<?php
class AddColumnHowToIntroductionText extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'shop', 'howToIntroductionText', 'blob');
    }

    public function down()
    {
        $this->removeColumn('shop', 'howToIntroductionText');
    }
}
