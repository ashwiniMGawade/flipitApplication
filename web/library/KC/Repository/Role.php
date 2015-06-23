<?php

namespace KC\Repository;

class Role extends \Core\Domain\Entity\User\Role
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
        $roles = KC\Entity\User\Role::createUserPermission(1);
        $entityManagerUser  = \Zend_Registry::get('emUser');
        foreach ($roles as $role) {
            $userRole = new KC\Entity\User\Role();
            $userRole->name = $role;
            $entityManagerUser->persist($roles);
            $entityManagerUser->flush();
        }
        return true;
    }
}
