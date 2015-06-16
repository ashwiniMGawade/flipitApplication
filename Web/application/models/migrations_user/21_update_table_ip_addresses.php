<?php
class UpdateTableIpAddresses extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->changeColumn('ip_addresses', 'ipaddress', 'string', 15);
    }

    public function down()
    {
        $this->changeColumn('ip_addresses', 'ipaddress', 'string', 255);
    }
}
