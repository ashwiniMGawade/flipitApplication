<?php
class DeleteEditorBallonTextTable extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->dropTable('editor_ballon_text');
    }

    public function down()
    {
        $this->dropTable('editor_ballon_text');
    }
}
