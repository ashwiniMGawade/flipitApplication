<?php
abstract class BaseNewsletterbanners extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('newsletterbanners');
        $this->hasColumn('id', 'integer', 11, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '11'
             ));
        $this->hasColumn('name', 'string', 255, array(
            'type' => 'string',
            'length' => '255'
        ));
        $this->hasColumn('path', 'string', 255, array(
            'type' => 'string',
            'length' => '255'
        ));
        $this->hasColumn('imagetype', 'string', 10, array(
            'type' => 'string',
            'length' => '10'
        ));
        $this->hasColumn('headerurl', 'string', 255, array(
            'type' => 'string',
            'length' => '255'
        ));
        $this->hasColumn('footerurl', 'string', 255, array(
            'type' => 'string',
            'length' => '255'
        ));
    }

    public function setUp()
    {
        parent::setUp();
        $softdelete0 = new Doctrine_Template_SoftDelete(array(
            'name' => 'deleted',
            'type' => 'boolean'
        ));
        $timestampable0 = new Doctrine_Template_Timestampable(
            array(
                'created' =>
                    array(
                        'name' => 'created_at'
                    ),
                'updated' =>
                    array(
                        'name' => 'updated_at'
                    ),
            )
        );
        $this->actAs($softdelete0);
        $this->actAs($timestampable0);
    }
}
