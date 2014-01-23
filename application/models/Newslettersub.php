<?php

/**
 * PageAttribute
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class Newslettersub extends BaseNewslettersub
{

	public static function checkDuplicateUser($email)
	{
	
		$cnt  = Doctrine_Core::getTable("Newslettersub")->findBy('email', $email)->count();
		return $cnt;
	}
	
	public static function registerUser($email)
	{
	
		//echo $email;
		//die('Raman');
		$cnt  = new Newslettersub();
		$cnt->email = $email;
		$cnt->save();
		
	}
	
	
}