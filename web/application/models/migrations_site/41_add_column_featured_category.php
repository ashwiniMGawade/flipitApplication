<?php
class AddColumnFeaturedCategory extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'category', 'featured_category', 'boolean');
    }

    public function down()
    {
        $this->removeColumn('category', 'featured_category');
    }
}
