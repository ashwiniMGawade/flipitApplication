<?php
/**
 * About
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##Er.kundal## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class About extends BaseAbout
{
	
	
	/**
	 *  @return mixed About content array or false if doesn't exists!
	 */
	public static function getAboutContent($status="")
	{
	
		$retVal = self::checkAboutContent1() ;
		
		if($retVal)
		{
		# create object of previous data
		
		if($status == 1){
			$status = array("1");
		}else{ 
			$status = array("1","0");
		}
		
		$result = array();
		for($s=0;$s<count($retVal);$s++){
			$result[$s] = $retVal[$s]['value'];
		}
		
		$about = Doctrine_Query::create()
			->select()
			->from("About")
			->whereIn('status',$status)
			->whereIn('id',$result)
			->fetchArray();
		
		/* echo "<pre>";
		print_r($about);
		die; */
		
		
		return $about ;
		
		
	}
	
	return false ;
	
	}
	
	/**
	 * delete about tab
	 * @param integer $id
	 * @param integer $position
	 * @author Er.kundal
	 * @version 1.0
	 */
	public static function removeeabouttab($id) {
	
		if ($id) {
			//delete about tab from list
			$a = Doctrine_Query::create()->delete('about')
			->where('id=' . $id)->execute();
			//change position by 1 of each below element
			return true;
		}else{
			return false;
		}
	}	
	
	
	
	/**
	*
	* @param array request array  saving About
	* @return integer id
	*/
	public static function  update($params)
	{
	
		//echo "<pre>"; print_r($params); die;
		
		for( $a=0 ;$a < count($params['title']) ; $a++ )
		{
			$i = $a+1;
			$retVal = self::checkAboutContent("about_". $i) ;
		
			# check if it has integer id of footer
			
			if($retVal)
			{
			
				# create object of previous data
				$about = Doctrine_Core::getTable("About")->find($retVal) ;
			
			} else
			{
				
					# new object
					$about = new About() ;
			}
		
			$about->title = @$params['title'][$a] ? 
								BackEnd_Helper_viewHelper::stripSlashesFromString($params['title'][$a]) : null;
			$about->content = @$params['content'][$a] ? 
								BackEnd_Helper_viewHelper::stripSlashesFromString($params['content'][$a]) : null;
			$about->status = @$params['status'][$a] ? 1 : 0 ;
		
			$about->save();
			
			if(! $retVal)
			{
				self::newAboutSetting($about->id, "about_".$i);
			}
			
			$about->free(true) ;
		
	   }	
	 
	  //call cache function
	  FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_about_page');
	}

	/**
	* check About is exist or not
	* by geetings its setting value
	* @return mixed setting value or false
	*/
	public static function checkAboutContent($name)
	{
		return Settings::getSettings($name) ;
	}
	
	public static function checkAboutContent1()
	{
		//$about = array();
		$about = Settings::getaboutSettings("about_") ;
		//$about[] = Settings::getaboutSettings("about_") ;
		//$about[] = Settings::getaboutSettings("about_") ;
		
		return $about ;
	}


	/**
	*  save new special settings
	*  @param $id integer special id
	*/
	public static function newAboutSetting($id,$name)
	{

		 $settings =  new Settings();
		 //$settings->name =  constant(  "Settings::" . $name ) ;
		 $settings->name = $name ;
		 $settings->value = $id ;
		 $settings->save();
		 //call cache function
		 FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_about_page');
	}
	

}