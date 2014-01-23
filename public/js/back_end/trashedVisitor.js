var filter = "";
var trashVisitorListTable = $('#trashVisitorListTable').dataTable();
/**
 * trashedUserList.js
 */
$(document).ready(function() {
	$('form#searchForm').submit(function() {
		trashVisitorList();
		return false;
	});
		/**
		 * Autocomplete towards search
		 * @author mkaur
		 */
			$("input#searchVisitor").autocomplete({
			    minLength: 1,
			    source: function( request, response)
			    {
			    	$.ajax({
			    		url : HOST_PATH + "admin/visitor/searchkey/for/1/keyword/" + $('#searchVisitor').val(),
			 			method : "post",
			 			dataType : "json",
			 			type : "post",
			 			success : function(data) {
			 				if (data != null) {
			 					//pass arrau of the respone in respone onject of the autocomplete
			 					response(data);
			 				} 
			 			},
			 			error: function(message) {
			 	            // pass an empty array to close the menu if it was initially opened
			 	            response([]);
			 	        }
			 		 });
			    },
			    select: function( event, ui ) {
			    }
			});
			trashVisitorList();
				   
	});


/**
 * functionality for trashed user list
 * @author mkaur
 */
function trashVisitorList() {
	addOverLay();
	$("ul.ui-autocomplete").css('display','none');
	$('#trashVisitorListTable').addClass('widthTB');
	trashVisitorListTable = $("#trashVisitorListTable").dataTable({
			"bLengthChange" : false,
			"bInfo" : false,
			"bFilter" : true,
			"bDestroy" : true,
			"bProcessing" : false,
			"bServerSide" : true,
			"iDisplayLength" : 10,
			"aaSorting": [[ 1, 'ASC' ]], 
			"sPaginationType" : "bootstrap",
			"sAjaxSource" : HOST_PATH + "admin/visitor/getvisitorlist/for/1/searchtext/" + ($('#searchVisitor').val().length > 0 ? $('#searchVisitor').val() : null),
			"aoColumns" : [
							{
								"fnRender" : function(obj) {
									 id = null;
									return id = obj.aData.id;
								},
								"bVisible":    false ,
								"bSortable" : false,
								"sType": 'numeric'
							},
							{
								"fnRender" : function(obj) {
								
									return "<a href='"+HOST_PATH+"admin/visitor/editvisitor/id/"+obj.aData.id+"'>" + ucfirst(obj.aData.firstName) + "</a>" ;
								},
								"bSortable" : true
							},
							{
								"fnRender" : function(obj) {
									return "<a href='"+HOST_PATH+"admin/visitor/editvisitor/id/"+obj.aData.id+"'>" + ucfirst(obj.aData.lastName) + "</a>" ;
								},
								"bSortable" : true
							},
							{
								"fnRender" : function(obj) {
									return "<a href='"+HOST_PATH+"admin/visitor/editvisitor/id/"+obj.aData.id+"'>" + obj.aData.email + "</a>" ;
								},
								"bSortable" : true
							}, 
							{
								"fnRender" : function(obj) {
								
									return "<a href='"+HOST_PATH+"admin/visitor/editvisitor/id/"+obj.aData.id+"'>" + obj.aData.created_at + "</a>" ;
									
								},
								//"bSearchable" : false,
								"bSortable" : true
							},
						{
							"fnRender" : function(obj) {

								  var del = "<a href='javascript:void(0);' id='restore' onClick='restoreUser(" + obj.aData. id +");' >"+__('Restore')+"</a>";
                                  return  del;

							},
							"bSearchable" : false,
							"bSortable" : false

						},
						{
							"fnRender" : function(obj) {

								  var del = "<a href='javascript:void(0);' id='delete' onClick='permanentDeleteVisitor(" + obj.aData. id +");' >"+__('Delete')+"</a>";
                                  return  del;

							},
							"bSearchable" : false,
							"bSortable" : false

						} 
					],
						"fnInitComplete" : function(obj) {
							removeOverLay();
						},
						"fnDrawCallback": function() {
							
							window.scrollTo(0, 0);
							
						 },
						"fnServerData" : function(sSource, aoData, fnCallback) {
							$('#trashVisitorListTable tr:gt(0)').remove();
							$.ajax({
								"dataType" : 'json',
								"type" : "POST",
								"url" : sSource,
								"data" : aoData,
								"success" : fnCallback
							});
						}
						
						
					});
}
/**
 * confir box for restore record or not.
 * @param id
 * @author mkaur
 */
function restoreUser(id) {
	bootbox.confirm(__('Do you want to restore this visitor?'), function(r) {
		if(!r){
		    return false;
		}else{
			restoreDeletedUser(id);
		}
	});
    }


/**
 * Through ajax restoretolist works to restore the record to userlist  
 * @param id
 * @author mkaur
 */
function restoreDeletedUser(id) {
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/visitor/restorevisitor",
		method : "post",
		data : {
			'id' : id
		},
		dataType : "json",
		type : "post",
		success : function(data) {
			if (data != null) {
			window.location.href = 	HOST_PATH + "admin/visitor/trash";
		} else {
		bootbox.alert(__('Problem in your data'));
			}
		}
	});
	//getUserListFromTrash();
}
/**
 * Checks With confirm box,whether permanently delete or not 
 * @param id
 * @author mkaur
 */
function permanentDeleteVisitor(id) {
	
	bootbox.confirm(__('Are you sure you want to permanently delete this visitor?'), function(r){
		if(!r){
			return false;
		}
		else{
			permanentDeleteVisitorByTrashed(id);
		}
	});
}
/**
 * Permanent delete visitor through VisitorController.
 * @param id
 * @author mkaur
 */
function permanentDeleteVisitorByTrashed(id) {
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/visitor/permanentdelete",
		method : "post",
		data : {
			'id' : id
		},
		dataType : "json",
		type : "post",
		success : function(data) {
			if (data != null) {
				window.location.href = 	HOST_PATH + "admin/visitor/trash/";
			} else {
				bootbox.alert(__('Problem in your data'));
			}
			//getUserListFromTrash();
		}
	});
	
}


