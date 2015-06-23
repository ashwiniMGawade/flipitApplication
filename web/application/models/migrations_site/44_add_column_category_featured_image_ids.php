<?php
class AddColumnCategoryFeaturedImageIds extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('category', 'categoryFeaturedImageId', 'bigint', null);
        $definition = array(
        'local'        => 'categoryFeaturedImageId',
        'foreign'      => 'id',
        'foreignTable' => 'image',
        'onDelete'     => 'CASCADE',
    );
    $this->createForeignKey('category', 'categoryFeaturedImageId_foreign_key', $definition );
    }

    public function down()
    {
        $this->removeColumn('category', 'categoryFeaturedImageId');
    }
}
