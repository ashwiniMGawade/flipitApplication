<?php
class AddTestmonialsColumnAccountSettings extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'signupmaxaccount', 'testimonial1', 'text', null ,
                        array('notnull' => false ));
        $this->addColumn( 'signupmaxaccount', 'testimonial2', 'text', null ,
                        array('notnull' => false ));
        $this->addColumn( 'signupmaxaccount', 'testimonial3', 'text', null ,
                        array('notnull' => false ));

        $this->addColumn( 'signupmaxaccount', 'showtestimonial', 'boolean', null ,
                array('default' => 0 ,
                        'notnull' => true	));


    }

    public function down()
    {
        $this->removeColumn('signupmaxaccount', 'testimonial1');
        $this->removeColumn('signupmaxaccount', 'testimonial2');
        $this->removeColumn('signupmaxaccount', 'testimonial3');
        $this->removeColumn('signupmaxaccount', 'showtestimonial');
    }
}
