<?php
class DataTable_Helper
{
    public static function generateDataTableResponse ($Query, $Params, $aColumns, $actions = null,$explicitsearchcolumns = null)
    {
    	//echo $Query->getSqlQuery();
        $retArr = array();
        $gCols = $aColumns;
        if (gettype($Query) == "string") {
            $Query = Doctrine_Query::create()->from($Query);
        }
        else
        {
        	//$Query;
        }
        if (isset($Params["columnnames"])) {
            $aColumns = $Params["columnnames"];
        }

        if($explicitsearchcolumns != null)
        {
//        	print_r($explicitsearchcolumns);
//

//			print_r($explicitsearchcolumns);
//        	die();
        	//$aColumns = $explicitsearchcolumns ;

//        	die(print_r($Params));
        }

        if (isset($Params["mode"])) {
            $params = array();
            foreach ($Params["data"] as $value) {
                $params[$value["name"]] = $value["value"];
            }
        } else {
            $params = isset($Params["mode"]) ? $Params["data"] : $Params;
        }


        $params = $Params;

        $filterColNames = array();

        if (isset($Params["filterColumnNames"])) {
        	$filterColNames = explode(",", $Params["filterColumnNames"]);
        }

        if (isset($Params["mode"])) {
            switch ($Params["mode"]) {
                case "getfilters":
                    $data = array();
                    foreach ($filterColNames as $colName) {

                        $Q = $Query;
                        $alias = "";
                        if (isset($explicitsearchcolumns[$colName])) {
                        	$alias = $explicitsearchcolumns[$colName]["alias"];
                        	$colName = $explicitsearchcolumns[$colName]["fieldName"];
                        }
                        if (trim($colName) != "" ) {
	                        if(gettype($colName) == "string")
	                        {
								$Q->select($colName.' as '.$alias);
	                        	if ((isset($aColumns["__identifier"]))) {
	                        		$Q->addSelect( $aColumns["__identifier"] );
	                        	}
	                            $Q->distinct(true);
	                            $data[$alias] = $Q->fetchArray();

	                        }
	                        else
	                        {
	                        	$data[$colName["name"]] = $Q->select($colName["name"])
	                            ->distinct(true)
	                            ->fetchArray();
	                        }
						}


                    }
                    $retArr["columns"] = $data;
                    break;
            }
        } else {
            $params = $Params;
            $Q = $Query;
           // echo count($Q->fetchArray()); die;
            // ############ get Total count ##########
            $iTotal = $Q->count();
            //############## Searching ##############
            $and = "";
            $or = "";

            for ($i = 0; $i < @intval($params["iColumns"]); $i ++) {




            	if ($params["mDataProp_" . $i] == "__action") {
            		continue;
            	}

                if ($params["bSearchable_" . $i] == true &&
                 $params["sSearch_" . $i] != "") {

                 	if($explicitsearchcolumns != null )
        			{
                 		$and .= $and != "" ? " AND " : "";
                    	$and .= $explicitsearchcolumns[$params["mDataProp_" . $i]]['fieldName'] . " LIKE '%" . $params["sSearch_" . $i] . "%'";
        			}
        			else
        			{
        				$and .= $and != "" ? " AND " : "";
        				$and .= $params["mDataProp_" . $i] . " LIKE '%" . $params["sSearch_" . $i] . "%'";
        			}

                }
                if ($params["sSearch"] != "") {
                	if($explicitsearchcolumns != null )
        			{
                			if($params["bSearchable_" . $i] == 'true')
                			{
	                			$or .= $or == "" ? "" : " OR ";
		                   		$or .= $explicitsearchcolumns[$params["mDataProp_" . $i]]['fieldName'] . " LIKE '%" . $params["sSearch"] . "%'";
                			}
        			}
        			else
        			{
	                	$or .= $or == "" ? "" : " OR ";
	                    $or .= $params["mDataProp_" . $i] . " LIKE '%" . $params["sSearch"] . "%'";
        			}
                }
            }
            if ($or != "") {
            	$Q->andWhere($or);
            }
        	if ($and != "") {
            	$Q->andWhere($and);
            }

            $iDTotal = $Q->count();



            // ######################## Sorting ############
            //echo "OUT";
            if (isset($params['iSortCol_0'])) {

            	//echo "IN" .  $Q->hasSqlAggregateAlias("orderBy");
            	//die();
            	//if(!$Q->hasSqlAggregateAlias("orderBy"))
            	//{
		                for ($i = 0; $i < intval($params['iSortingCols']); $i ++) {
		                    if ($params['bSortable_' . intval($params['iSortCol_' . $i])] == "true")
		                     {

		                    	if($explicitsearchcolumns != null )
		        				{
		        					//print_r($explicitsearchcolumns);
		        					//die('Helll0');
			                   	  	$Q->orderBy($explicitsearchcolumns[$params["mDataProp_" . $i]]['fieldName'] . " " .
			                         $params['sSortDir_' . $i]);
			                    }
			                    else
			                    {
			                    	//print_r($aColumns);
			                    	//die('Helll  111 1  1');
			                    	$Q->orderBy($aColumns[intval($params['iSortCol_' . $i])] . " " . $params['sSortDir_' . $i]);
			                    }

		                    }
		            	}

                //}
            }

            // #################### Paging #########################

            if (isset($params["iDisplayStart"]) &&
             isset($params["iDisplayLength"])) {
                if (intval($params["iDisplayStart"] > - 1) &&
                 intval($params["iDisplayLength"]) > 0) {
                    $Q->limit(intval($params["iDisplayLength"]))->offset(
                    intval($params["iDisplayStart"]));
                }

            }

         	//die($Q->getSqlQuery());
            // ######################## Execute Query ###################
     	    $sqlQuery = $Q->getSqlQuery();
            $data = $Q->fetchArray();
            $retData = array();
            if ($actions != null) {

	            foreach ($data as $item) {
	            	$actionsHTML = " 1 ";

	            	if (is_array($actions)) {
            			$editHTML = "";
            			$deleteHTML = "";
	            		foreach ($actions as $aKey => $action) {
	            			$actionName = $aKey;

	            			$edit["template"] = "<a rel='%s' %s href=\"%s\">%s</a>";
	            			$edit["dataColumn"] = "id";
	            			$edit["label"] = "Edit";
	            			$edit["cssClass"] = "";

	            			$editTemplate = "<a rel='" . @$item["id"] . "' href=\"javascript: alert('Deleting : ' + $(this).attr('rel'));\">Delete</a>";
	            			$delete["template"] = "<a rel='%s' %s href=\"%s\">%s</a>";
	            			$delete["dataColumn"] = "id";
	            			$delete["label"] = "Delete";
	            			$delete["cssClass"] = "";


	            			$deleteTemplate = "<a rel='" . @$item["id"] . "' href=\"javascript: alert('Deleting : ' + $(this).attr('rel'));\">Delete</a>";


	            			$element = null;

	            			if(is_array($action))
	            			{
            					$element = $$aKey;
	            				foreach ($action as $key => $value)
	            				{
	            					$element[$key] = $value;
	            				}
	            			}

	            			switch ($actionName)
	            			{
	            				case "edit" :
	            					$editHTML = sprintf($element["template"], $item[$element["dataColumn"]], $element["cssClass"] == "" ? "" : "class='" . $element["cssClass"] . "'",$item[$element["dataColumn"]], $element["label"]);
	            					break;
	            				case "delete" :
	            					$deleteHTML = sprintf($element["template"], $item[$element["dataColumn"]], $element["cssClass"] == "" ? "" : "class='" . $element["cssClass"] . "'",$item[$element["dataColumn"]], $element["label"]);
	            					break;
	            			}
	            		}
	            		$actionsHTML = $editHTML . $deleteHTML;
	            	}
	            	$item["__action"] = $actionsHTML;
	            	$retData[] = $item;

	            }

            }
            else
            {
            	$retData = $data ;
            }
            @$retArr = array("sEcho" => intval($params['sEcho']),
            "iTotalRecords" => intval($iTotal),
            "iTotalDisplayRecords" => intval($iDTotal),
            "aaData" => $retData
            ,"query" => $sqlQuery
            );
        }
        // build response

        return $retArr;
    }
}
?>