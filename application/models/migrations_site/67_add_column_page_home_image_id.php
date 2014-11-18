<?php
class AddColumnPageHomeImageId extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('page', 'pageHomeImageId', 'bigint', null);
        $definition = array(
            'local'        => 'pageHomeImageId',
            'foreign'      => 'id',
            'foreignTable' => 'image',
            'onDelete'     => 'CASCADE',
        );
        $this->createForeignKey('page', 'pageHomeImageId_foreign_key', $definition);
    }

    public function down()
    {
        $this->removeColumn('page', 'pageHomeImageId');
    }
}
