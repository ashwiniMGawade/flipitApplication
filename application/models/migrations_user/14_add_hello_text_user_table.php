<?php
class AddEditorTextColumnUser extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('user', 'editorText', 'string', 100);
    }

    public function down()
    {
        $this->removeColumn('user', 'editorText');
    }
}
