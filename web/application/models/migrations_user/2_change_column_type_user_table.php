<?php

class ChangeColumnDatatypeUser extends Doctrine_Migration_Base
{
    public function up()
    {
        $options = array( 'length' => 255 );
        $options1 = array( 'notnull' => false );

        $this->changeColumn( 'user', 'firstname', 'string', $options);
        $this->changeColumn( 'user', 'lastname', 'string', $options);

        $this->changeColumn( 'user', 'likes', 'string', $options1);
        $this->changeColumn( 'user', 'dislike', 'string', $options1);
        $this->changeColumn( 'user', 'google', 'string', $options1);
        $this->changeColumn( 'user', 'twitter', 'string', $options1);
        $this->changeColumn( 'user', 'pinterest', 'string', $options1);

    }

    public function down()
    {
        $options = array( 'length' => 100 );
        $options1 = array( 'length' => 255 );

        $this->changeColumn( 'user', 'firstname', 'string', $options);
        $this->changeColumn( 'user', 'lastname', 'string', $options);

        $this->changeColumn( 'user', 'likes', 'string', $options1);
        $this->changeColumn( 'user', 'dislike', 'string', $options1);
        $this->changeColumn( 'user', 'google', 'string', $options1);
        $this->changeColumn( 'user', 'twitter', 'string', $options1);
        $this->changeColumn( 'user', 'pinterest', 'string', $options1);
    }
}
