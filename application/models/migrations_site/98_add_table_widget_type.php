<?php
class AddTableWidgetType extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('widget_location', 'widgettype', 'string', 100);
    }

    public function down()
    {
        $this->removeColumn('widget_location', 'widgettype');
    }
}
