<?php

/**
 * Role
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class Role extends BaseRole
{

    /**
     * This function return roles,these array display in dropdown
     * then admin can add the user and select role from created dropdown
     * but dropdown create according role permission
     *@param integer $args
     *@return array $roles
     */
    public static function createUserPermission($role)
    {
        $roles  =  array();
        switch ($role) {

            case '1':
                $roles = array('1'=>'Super Administrator',
                        '2'=>'Administrator',
                        '3'=>'Account Manager',
                        '4'=>'Editor');
                break;
            case '2':
                $roles = array(
                        '2'=>'Administrator',
                        '3'=>'Account Manager',
                        '4'=>'Editor');
                break;
            case '3':
                $roles = array(
                        '3'=>'Account Manager',
                        '4'=>'Editor');
                break;
            case '4':
                $roles = array(
                        '4'=>'Editor');
                break;
            default:
                break;
        }
        return  $roles;

    }

    public function addUserRoles()
    {
        $roles = $this->createUserPermission(1);
        foreach ($roles as $role) {
            $userRole = new Role();
            $userRole->name = $role;
            $userRole->save();
        }
        return true;
    }
}
