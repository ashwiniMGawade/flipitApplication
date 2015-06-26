<?php

class AddHeightWidthToImage extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('image', 'height', 'integer');
        $this->addColumn('image', 'width', 'integer');
    }

    public function down()
    {
        $this->removeColumn('image', 'height');
        $this->addColumn('image', 'width');
    }
}