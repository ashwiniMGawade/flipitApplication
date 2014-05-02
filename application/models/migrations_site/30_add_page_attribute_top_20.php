<?php
class AddNewPageAttribueTop20 extends Doctrine_Migration_Base
{

    public function up()
    {

        $pageAttribute = new PageAttribute();
        $pageAttribute->name = "Top 20";
        $pageAttribute->save();
    }

    public function down()
    {
         Doctrine_Query::create()
        ->delete('PageAttribute')
        ->where('name = "Top 20"' )->execute();
    }


}
