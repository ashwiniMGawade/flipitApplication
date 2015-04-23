var filter = "";
var trashUserList = $('#trashUserList').dataTable();
var _roles =null;
/**
 * @author mkaur
 * ready function redirect to getUserListFromTrash()
 */
$(document).ready(function() {
		getRoles();

		//auto complete
		$("#tags").autocomplete({
        minLength: 1,
        source: function( request, response)
        {
        	$.ajax({
     			url : HOST_PATH + "admin/user/gettopfive/for/1/text/" + $('#tags').val(),
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
    //function call when 
	$('#selectRole').change(searchFilterForDrp);
	getUserListFromTrash(undefined,undefined);
	});
/**
 * Get roles from database and display in role filter
 * @author kraj
 * 
 */
function getRoles()
{
	$.ajax({
		type : "POST",
		url : HOST_PATH + "admin/user/getroles",
		dataType : 'json',
		success : function(data) {
			
			_roles = data;
			CreateSelect();
		}
			
		});

}
/**
 * Call function when user click on 
 * Search button for search
 */
function searchFilterForDrp()
{
	getUserListFromTrash(undefined,$(this).val());

}
/**
 * function for filtering
 * @author kraj
 */
function searchFilter()
{
	var filterString = $('input[name=search]').val();
	getUserListFromTrash(filterString,undefined);
	/*	
	 var filterString = $('.dataTables_filter :input[name=search]').val();
	 console.log(filterString);
	 userList.fnFilter(filterString);
	 */
}
/**
 * Create dropdown for role filter.
 * @author kraj
 */
function CreateSelect() {

	$('div.fillterDiv #selectRole').empty();
	var r ='<option value="0"> '+__("All")+' </option>';
	for(var i in _roles)
		{
			r += '<option value="'+ _roles[i].id + '">'+ _roles[i].name +'</option>';
	    }
	//return r ;//+ '</select>';
	$('div.fillterDiv #selectRole').append(r);
}
/**
 * function for first letter upercase.
 * @param str
 * @returns
 * @author kraj
 */
function ucfirst(str) {
    var firstLetter = str.substr(0, 1);
    return firstLetter.toUpperCase() + str.substr(1);
}
/**
 * functionality for trashed user list
 * @author mkaur
 */
function getUserListFromTrash(searchtext,role) {
	//$("form").each(function() { this.reset(); }); 
	//addOverLay();
	$("ul.ui-autocomplete").css('display','none');
	$('#trashUserList').addClass('widthTB');
	if(searchtext==undefined)
	{
		$('input[name=search]').val('');
	}
	trashUserList = $("#trashUserList")
		.dataTable(
				{

					"bLengthChange" : false,
					"bInfo" : false,
					"bFilter" : true,
					"bDestroy" : true,
					"bProcessing" : false,
					"bServerSide" : true,
					"iDisplayLength" : 100,
					"aaSorting": [[ 1, 'ASC' ]], 
					"sPaginationType" : "bootstrap",
					"sAjaxSource" : HOST_PATH + "admin/user/trashuserlist/searchtext/" + searchtext + "/role/" + role,
					"aoColumns" : [
							{
								"fnRender" : function(obj) {
							
									return email = obj.aData.id;
							
								},
								
								"bSortable" : true,
								"sType": 'numeric',
								"bVisible":    false 
							},
							{

								"fnRender" : function(obj) {
									var imgSrc = "";
									if (obj.aData.profileimage == null || obj.aData.profileimage=='' || obj.aData.profileimage==undefined) {
									    imgSrc = HTTP_PATH_CDN
											+ "/images/user-avtar.jpg";
									} else {
										var image = obj.aData.profileimage.path
											+ obj.aData.profileimage.name;
										imgSrc = HTTP_PATH_CDN + image;
									}
									var name = "<span class='word-wrap-username'>" + ucfirst(obj.aData.firstName)
											+ " "
											+ ucfirst(obj.aData.lastName) + "</span>" ;
									var html = "<div editId='" + obj.aData.id + "' class='grid-img'>"
	                                        + "<a href='javascript:void(0);'><img src='" + imgSrc + "'/></a></div>" 
	                                        + "<a href='javascript:void(0);'>" + name + "</a>";
									return  html;
								},
								
								"bSortable" : true

								},
								{
									"fnRender" : function(obj) {

										return email = "<span class='word-wrap-nocolor-email'>" + obj.aData.email + "</span>" ;


									},
									
									"bSortable" : true
								}, {
									
									"fnRender" : function(obj) {

										return role =    obj.aData.users.name;

									},
									
									"bSortable" : true,
									"bSearchable" : true,
									'sWidth':'200px'
								} ,
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

									  var del = "<a href='javascript:void(0);' id='delete' onClick='permanentDeleteUser(" + obj.aData. id +");' >"+__('Delete')+"</a>";
                                      return  del;

								},
								"bSearchable" : false,
								"bSortable" : false

							} 
					],
						"fnInitComplete" : function(obj) {
							
							//removeOverLay();
						},
						"fnDrawCallback": function() {
							
							window.scrollTo(0, 0);
							
						 },
						"fnServerData" : function(sSource, aoData, fnCallback) {
							$('#trashUserList tr:gt(0)').remove();
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
	bootbox.confirm(__('Do you want to restore this record?'), function(r) {
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
		url : HOST_PATH + "admin/user/restoreuser",
		method : "post",
		data : {
			'id' : id
		},
		dataType : "json",
		type : "post",
		success : function(data) {
			if (data != null) {
			window.location.href = 	HOST_PATH + "admin/user/trash/";
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
function permanentDeleteUser(id) {
	
	bootbox.confirm(__('Are you sure you want to permanent delete this record?'), function(r){
		if(!r){
			return false;
		}
		else{
			permanentDeleteUserByTrashed(id);
		}
	});
}
/**
 * Permanent delete user through usercontroller.
 * @param id
 * @author mkaur
 */
function permanentDeleteUserByTrashed(id) {
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/user/permanentdelete",
		method : "post",
		data : {
			'id' : id
		},
		dataType : "json",
		type : "post",
		success : function(data) {
			if (data != null) {
				window.location.href = 	HOST_PATH + "admin/user/trash/";
				//alert('Record has been deleted');

			} else {

				bootbox.alert(__('Problem in your data'));
			}
			//getUserListFromTrash();
		}
	});
	
}


