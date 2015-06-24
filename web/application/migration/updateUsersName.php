<?php

/**
 * Script for change userName
 *
 * @author kraj
 *
 */
class updateUsersName
{
    protected $_localePath = '/';
    protected $_hostName = '';
    protected $_trans = null;


    public function __construct()
    {
        require_once 'ConstantForMigration.php';
        require_once('CommonMigrationFunctions.php');

        CommonMigrationFunctions::setTimeAndMemoryLimit();

        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        $manager = CommonMigrationFunctions::getGlobalDbConnectionManger();
        $imbull = $connections['imbull'];

        // cycle htoruh all site database
        $DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');


        // cycle htoruh all site database
        foreach ( $connections as $key => $connection ) {
            // check database is being must be site
            if ($key != 'imbull') {
                try {
                    if(isset($connection['dsn'])){

                        $this->changeData($connection['dsn'], $key ,$imbull );
                    }
                } catch ( Exception $e ) {

                    echo $e->getMessage ();
                    echo "\n\n";
                }
                echo "\n\n";
            }

        }


        $manager->closeConnection($DMC1);
    }

    protected function changeData($dsn, $key,$imbull)
    {
        $DMC = Doctrine_Manager::connection($dsn, 'doctrine_site');
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));

        $manager = Doctrine_Manager::getInstance();
        //Doctrine_Core::loadModels(APPLICATION_PATH . '/models/generated');

        $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');

        $cutsomLocale = LocaleSettings::getLocaleSettings();
        $cutsomLocale = !empty($cutsomLocale[0]['locale']) ? $cutsomLocale[0]['locale'] : 'nl_NL';




        //setting to no time limit,
        set_time_limit(0);

        $o = Doctrine_Query::create()->update('Offer')->set('authorName',"''")
            ->where('authorId=0 or authorId="" or authorId= null');
        $o->execute();

        $p = Doctrine_Query::create()->update('Page')->set('contentManagerName', "''")
            ->where('contentManagerId=0 or contentManagerId="" or contentManagerId=null');
        $p->execute();

        $a = Doctrine_Query::create()->update('Articles')->set('authorname', "''")
            ->where("authorid=0 or authorid='' or authorid = null");
        $a->execute();

        $s = Doctrine_Query::create()->update('Shop')->set('accountManagerName', "''")
            ->where('accoutManagerId=0 or accoutManagerId="" or accoutManagerId=null');
        $s->execute();

        $s1 = Doctrine_Query::create()->update('Shop')->set('contentManagerName', "''")
            ->where('contentManagerId=0 or contentManagerId=""  or contentManagerId=null');
        $s1->execute();

        $uObj  = new User();
        $users = $uObj->getAllUser();

        foreach ($users as $u){

            if($u['deleted']==1){

            //echo  $u['deleted'];

                        $id  = $u['id'];
                        //update offer
                        $Of = Doctrine_Query::create()->select('id')->from('Offer')->where('authorId=' . $id)->fetchArray();
                        $Ids = array();
                        if(!empty($Of)):
                            foreach($Of as $arr):
                                $Ids[] = $arr['id'];
                            endforeach;
                        endif;

                        if(!empty($Ids)){
                            $o = Doctrine_Query::create()->update('Offer')
                                ->set('authorName',"''")
                                ->set('authorId',0)
                                ->whereIn('id', $Ids);
                            $o->execute();
                        }


                        //update page
                        $page = Doctrine_Query::create()->select('id')->from('Page')->where('contentManagerId=' . $id)->fetchArray();

                        $Ids = array();
                        if(!empty($page)):
                            foreach($page as $arr):
                                $Ids[] = $arr['id'];
                            endforeach;
                        endif;

                        if(!empty($Ids)){

                            $p = Doctrine_Query::create()->update('Page')
                                ->set('contentManagerName', "''")
                                ->set('contentManagerId', 0)
                                ->whereIn('id', $Ids);
                            $p->execute();
                        }


                        //update articles
                        $art = Doctrine_Query::create()->select('id')->from('Articles')->where('authorid=' . $id)->fetchArray();

                        $Ids = array();
                        if(!empty($art)):
                            foreach($art as $arr):
                                $Ids[] = $arr['id'];
                            endforeach;
                        endif;

                        if(!empty($Ids)){

                            $a = Doctrine_Query::create()->update('Articles')->set('authorname', "''")
                                ->set('authorid', 0)
                                ->whereIn('id', $Ids);
                            $a->execute();
                        }

                        //update shops
                        $shops = Doctrine_Query::create()->select('id')->from('Shop')->where('accoutManagerId=' . $id)->fetchArray();

                        $Ids = array();
                        if(!empty($shops)):
                            foreach($shops as $arr):
                                $Ids[] = $arr['id'];
                            endforeach;
                        endif;

                        if(!empty($Ids)){
                            $s = Doctrine_Query::create()
                                ->update('Shop')
                                ->set('accountManagerName', "''")
                                ->set('accoutManagerId', 0)
                                ->whereIn('id', $Ids);
                            $s->execute();
                        }

                        $shops1 = Doctrine_Query::create()->select('id')->from('Shop')->where('contentManagerId=' . $id)->fetchArray();

                        $Ids = array();
                        if(!empty($shops1)):
                            foreach($shops1 as $arr):
                                $Ids[] = $arr['id'];
                            endforeach;
                        endif;


                        if(!empty($Ids)){
                            $s1 = Doctrine_Query::create()
                                ->update('Shop')
                                ->set('contentManagerName', "''")
                                ->set('contentManagerId', 0)
                                ->whereIn('id', $Ids);
                            $s1->execute();
                        }

            }else{

                $fullName = $u['firstName']. " " . $u['lastName'];

                $o = Doctrine_Query::create()->update('Offer')->set('authorName',"'$fullName'")
                    ->where('authorId=' . $u['id']);
                $o->execute();



                $p = Doctrine_Query::create()->update('Page')->set('contentManagerName', "'$fullName'")
                    ->where('contentManagerId=' . $u['id']);
                $p->execute();


                $a = Doctrine_Query::create()->update('Articles')->set('authorname', "'$fullName'")
                    ->where('authorid=' . $u['id']);
                $a->execute();

                $s = Doctrine_Query::create()->update('Shop')->set('accountManagerName', "'$fullName'")
                    ->where('accoutManagerId=' . $u['id']);
                $s->execute();


                $s1 = Doctrine_Query::create()->update('Shop')->set('contentManagerName', "'$fullName'")
                    ->where('contentManagerId=' . $u['id']);
                $s1->execute();
            }
        }

        $manager->closeConnection($DMC);

        echo "\n";
        print "$key - User has been updated successfully!!!";
    }
}

new updateUsersName();
