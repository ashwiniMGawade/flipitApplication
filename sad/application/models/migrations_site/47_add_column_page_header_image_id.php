<?php
class AddColumnPageHeaderImageId extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('page', 'pageHeaderImageId', 'bigint', null);
        $definition = array(
        'local'        => 'pageHeaderImageId',
        'foreign'      => 'id',
        'foreignTable' => 'image',
        'onDelete'     => 'CASCADE'
    );
    $this->createForeignKey('page', 'pageHeaderImageId_foreign_key', $definition );
    }

    public function down()
    {
        $this->removeColumn('page', 'pageHeaderImageId');
    }
}
