<?php

class EmailCampain extends BaseEmailCampain
{
    public function fetchAll()
    {
        return Doctrine_Query::create()
                                ->select()
                                ->from("EmailCampain")
                                ->where("EmailCampain.deleted='0'")
                                ->fetchArray();
    }

    public function saveForm($data){

        if (!empty($data['id'])) {
            $campain =  Doctrine_Core::getTable("EmailCampain")->find($data['id']);
            //echo '<pre>'.print_r($campain, true).'</pre>';
        }else{
            $campain            = $this;
            $campain->sender    = $data['sender'];
            $campain->subject   = $data['subject'];
        }
        $campain->save();
        return $campain->get('id');
    }
}
