<?php

class AddSubtitleToPage extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('page', 'subtitle', 'string', 255);
    }

    public function down()
    {
        $this->removeColumn('page', 'subtitle');
    }
}
