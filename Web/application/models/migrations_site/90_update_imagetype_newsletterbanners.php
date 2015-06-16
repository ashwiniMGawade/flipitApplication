<?php
class UpdateImagetypeNewsletterbanners extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->changeColumn('newsletterbanners', 'imagetype', 'string', 10);
    }

    public function down()
    {
        $this->changeColumn('newsletterbanners', 'imagetype', 'string', 1);
    }
}