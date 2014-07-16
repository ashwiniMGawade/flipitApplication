<?php
class AddCascadeConstraints extends Doctrine_Migration_Base
{

    public function up()
    {
        $definition = array(
            'local'        => 'visitorId',
            'foreign'      => 'id',
            'foreignTable' => 'visitor',
            'onDelete'     => 'CASCADE',
        );

        $this->createForeignKey( 'favorite_shop', 'fav_cascade', $definition );
        $this->createForeignKey( 'visitor_keyword', 'vis_cascade', $definition );
    }

    public function down()
    {
        $this->dropForeignKey( 'favorite_shop', 'fav_cascade' );
        $this->dropForeignKey( 'visitor_keyword', 'vis_cascade' );
    }

}
