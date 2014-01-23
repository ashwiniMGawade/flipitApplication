<?php
/**
 * RouteRedirect
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##kraj## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class RouteRedirect extends BaseRouteRedirect
{
	/**
	 * Retrieve orignalurl and exact redirectto on the basis of REQUEST_URI
	 * @author kraj
	 * @version 1.0
	 */

	public static function getRoute($orignalurl)
	{
		$orignalurl= trim($orignalurl, '/');
		$data = Doctrine_Query::create()->select()
										->from('RouteRedirect')
										->where("orignalurl = ?" ,$orignalurl)
										->fetchArray();
		return $data;
	}
	public static function getRedirects($redirectto){


		$q = Doctrine_Query::create()
		->select('rp.orignalurl')->from('RouteRedirect rp')
		->where('rp.redirectto="'.$redirectto.'"')->andWhere('deleted = 0')->fetchArray();
		return $q;


	}
	/**
	 * add new redirect
	 * @param posted form data
	 * @author kraj
	 * @version 1.0
	 */
	public static function addRedirect($params) {
		$data = new RouteRedirect();
		$data->orignalurl = BackEnd_Helper_viewHelper::stripSlashesFromString($params['orignalurl']);
		$data->redirectto = BackEnd_Helper_viewHelper::stripSlashesFromString($params['redirectto']);
		$data->save();
	}
	/**
	 * getRedirectList fetch all record from database table shop
	 * @param $params
	 * @return array
	 * @author kraj
	 * @version 1.0
	 */
	public static function getRedirect($params) {

		$redirectList = Doctrine_Query::create ()
		->select ('e.orignalurl as orignalurl,e.redirectto as redirectto,e.created_at as created_at')
		->from ( "RouteRedirect as e" )
		->orderBy("e.created_at DESC");
		$list = DataTable_Helper::generateDataTableResponse($redirectList,
				$params,array("__identifier" => 'e.id','e.id','orignalurl','redirectto','created_at'),
				array(),array());

		return $list;
	}
	/**
	 * @author kraj
	 * @param $id
	 * @return ediredirectDetail
	 */
	public static function getRedirectForEdit($id) {

		$getdata = Doctrine_Query::create()
			->select("k.*")
			->from("RouteRedirect as k")
			->where("k.id =".$id)
			->fetchArray();
	    return $getdata;
	}

	/**
	 * @author kraj
	 * @param $id
	 * @return updated keyword details
	 *
	 */
	public static function updateRedirect($params) {

		$data = Doctrine_Core::getTable('RouteRedirect')
		->find($params['id']);
		$data->orignalurl = BackEnd_Helper_viewHelper::stripSlashesFromString($params['orignalurl']);
		$data->redirectto = BackEnd_Helper_viewHelper::stripSlashesFromString($params['redirectto']);
		$data->save();
	}

	/**
	 * get list of excluded keywords for export
	 * @author kraj
	 * @return array $redirectList
	 * @version 1.0
	 */
	public static function exportRedirectList() {

		$redirectList = Doctrine_Query::create()
		->select('e.*')
		->from("RouteRedirect e")
		->orderBy("e.id DESC")
		->fetchArray();
		return $redirectList;

	}

	/**
	 * deleted record parmanetly from database.
	 * @param $id
	 * @author kraj
	 * @version 1.0
	 */
	public static function deleteRedirect($id)
	{
		$searchbarDel = Doctrine_Core::getTable('RouteRedirect')->find($id);
		$q = Doctrine_Query::create()->delete()
		->from('RouteRedirect e')
		->where("e.id=" . $id)
		->execute();
	}

	/**
	 * upload image
	 * @param $_FILES[index]  $file
	 */
	public function uploadExcel($file) {

		if (!file_exists(UPLOAD_EXCEL_PATH))
			mkdir(UPLOAD_EXCEL_PATH,776, true);


		// generate upload path for images related to shop
		$rootPath = UPLOAD_EXCEL_PATH;

		// check upload directory exists, if no then create upload directory
		if (!file_exists($rootPath))
			mkdir($rootPath, 776, true);

		$adapter = new Zend_File_Transfer_Adapter_Http();
		// set destination path and apply validations
		$adapter->setDestination($rootPath);
		$adapter->addValidator('Extension', false, array('xlsx', true));
		$adapter->addValidator('Size', false, array('min' => 20, 'max' => '2MB'));
		// get upload file info

		$files = $adapter->getFileInfo($file);
		// get file name
		$name = $adapter->getFileName($file, false);

		// rename file name to by prefixing current unix timestamp
		$newName = time() . "_" . $name;

		// generates complete path of image
		$cp = $rootPath . $newName;


		// apply filter to rename file name and set target
		$adapter
		->addFilter(
				new Zend_Filter_File_Rename(
						array('target' => $cp, 'overwrite' => true)),
				null, $file);

		// recieve file for upload
		$adapter->receive($file);

		// check is file is valid then

		if ($adapter->isValid($newName)) {

			return array("fileName" => $newName, "status" => "200",
					"msg" => "File uploaded successfully",
					"path" => $rootPath);

		} else {

			return array("status" => "-1",
					"msg" => "Please upload the valid file");

		}

	}


}
?>