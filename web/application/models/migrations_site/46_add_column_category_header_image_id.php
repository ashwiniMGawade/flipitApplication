<?php
class AddColumnCategoryHeaderImageId extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('category', 'categoryHeaderImageId', 'bigint', null);
        $definition = array(
        'local'        => 'categoryHeaderImageId',
        'foreign'      => 'id',
        'foreignTable' => 'image',
        'onDelete'     => 'CASCADE'
    );
    $this->createForeignKey('category', 'categoryHeaderImageId_foreign_key', $definition );
    }

    public function down()
    {
        $this->removeColumn('category', 'categoryHeaderImageId');
    }
}
