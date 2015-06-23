<?php
class AddBannerurlToNewsletterbanners extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('newsletterbanners', 'footerurl', 'string', 255);
        $this->addColumn('newsletterbanners', 'headerurl', 'string', 255);
    }

    public function down()
    {
        $this->removeColumn('newsletterbanners', 'footerurl');
        $this->removeColumn('newsletterbanners', 'headerurl');
    }
}
