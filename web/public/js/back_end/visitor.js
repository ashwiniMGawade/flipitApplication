$(document).ready(function() {
	
	$('form#searchForm').submit(function() {
		return false;
	});
	
	
	var iSearchText = $.bbq.getState( 'iSearchText' , true ) || undefined;
	var iEmailText = $.bbq.getState( 'iEmailText' , true ) || undefined;
	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 1;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'DESC';
	getVisitorList(iSearchText,iEmailText,iStart,iSortCol,iSortDir);
	
	
	$('#searchButton').click(searchByVisitor);
	
	//if press enter key the call search offer function
	$("input#searchVisitor").keypress(function(e)
			{
			        // if the key pressed is the enter key
			        if (e.which == 13)
			        {
			           
			        	searchByVisitor();
			        }
			});
	//if press enter key the call search shop function
	$("input#searchEmail").keypress(function(e)
			{
			        // if the key pressed is the enter key
			        if (e.which == 13)
			        {
			           
			        	searchByVisitor();
			        }
			});
	//bind a function with coupon type drowpdown list
	
	/*
	//bind with keypress of search box
	$("input#SearchCategory").keypress(function(e)
	{
		// if the key pressed is the enter key
		  if (e.which == 13)
		  {
		      getVisitorList($(this).val(),0,1,'asc');
		  }
	});
	*/
	$(window).bind( 'hashchange', function(e) {
		if(hashValue != location.hash && click == false){
			visitorListTable.fnCustomRedraw();
		}
	});
	
	
	
	/**
	 * Autocomplete towards search
	 * @author mkaur
	 */
		$("#searchVisitor").select2({
			placeholder: __("Search visitor"),
			minimumInputLength: 1,
			ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
			 url: HOST_PATH + "admin/visitor/searchkey",
			 dataType: 'json',
			 data: function(term, page) {
	             return {
	            	 keyword: term,
	            	 flag: 0
	             };
	         },
			 type: 'post',
			 results: function (data, page) { // parse the results into the format expected by Select2.
			 // since we are using custom formatting functions we do not need to alter remote JSON data
				 return {results: data};
				 
			 }
			},
			formatResult: function(data) { 
	            return data; 
	        },
	        formatSelection: function(data) { 
	        	$("#searchVisitor").val(data);
	            return data; 
	        }
		});
		$("#searchEmail").select2({
			placeholder: __("Search Email"),
			minimumInputLength: 1,
			ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
			 url: HOST_PATH + "admin/visitor/searchemails",
			 dataType: 'json',
			 data: function(term, page) {
	             return {
	            	 keyword: term,
	            	 flag: 0
	             };
	         },
			 type: 'post',
			 results: function (data, page) { // parse the results into the format expected by Select2.
			 // since we are using custom formatting functions we do not need to alter remote JSON data
				 return {results: data};
				 
			 }
			},
			formatResult: function(data) { 
	            return data; 
	        },
	        formatSelection: function(data) { 
	        	$("#searchEmail").val(data);
	            return data; 
	        }
		});
		/*
		$("input#searchVisitor").autocomplete({
		    minLength: 1,
		    source: function( request, response)
		    {
		    	$.ajax({
		    		url : HOST_PATH + "admin/visitor/searchkey/for/0/keyword/" + $('#searchVisitor').val(),
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
		*/
		
	});

function saveUser()
{
	if($("form#userRegister").valid())
	{
		
		$('#saveUser').attr('disabled' ,"disabled");
		
		return true;
		
	}else {
		return false;
	}
	
}

/**
 * show div according to slected div
 * @param divId
 */
function showDivByHashing(divId) {

	$("#" + divId).removeClass("display-none").siblings().addClass(
			"display-none");
}
/**
 * cod for website multiselect 
 * admin can select website without control
 * @author kraj
 */ 
jQuery.fn.multiselect = function() {
	
	$(this).each(function() {
		var checkboxes = $(this).find("input:checkbox");
		checkboxes.each(function() {
			var checkbox = $(this);
			// Highlight pre-selected checkboxes
			if (checkbox.attr("checked"))
				checkbox.parent().addClass("multiselect-on");

			// Highlight checkboxes that the user selects
			checkbox.click(function() {
				if (checkbox.attr("checked"))
					checkbox.parent().addClass("multiselect-on");
				else
					checkbox.parent().removeClass("multiselect-on");
			});
		});
	});
};
/**
 * delete user from database by id id get from hidden field
 * @author kraj
 */
function deleteUser(id) {
	var id = $('input#id').val();
	bootbox.confirm(__('Are you sure you want to move this visitor to trash?'), function(r){
		if(!r){
			
			return false;
			
		}else{
			moverToTrash(id);
		}
    });
}
/**
 * when delete action in confirmed the ajax call to delete the record according
 * to id
 * @author kraj
 * @param id
 */
function moverToTrash() {
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/user/deleteuser",
		method : "post",
		data : {
			'id' : $("input#id" , "form#editRegister").val()
		},
		dataType : "json",
		type : "post",
		success : function(data) {
			
			if (data != null) {
				removeOverLay();
				//bootbox.alert("Record has been deleted");
				document.location.href = HOST_PATH + "admin/user/" ;
				
			} else {
				
				bootbox.alert(__("<span class='success'>Problem in your data</span>"));
				//jAlert('Problem in your data');
			}
		}
	});
}



/**
 * Get website according to access permission of Admin user mean current logged
 * user
 * @author kraj
 */
function getWebsite() {
	
	
	// check load event called in edit mode or not
	var hashString = window.location.hash;
	hashString = hashString.substr(1);
	
		
		
	var isEdit = false  ;
	var regex =  /(^.+)-([\d]+$)/ ;
	
	var result = regex.exec(hashString);
	isEdit  =  result && result[2] ? true : false ;
	
	
	
	if (_websiteAccess == null) {

		if(! isEdit )
		{
		//addOverLay();
		}
		var roleid = $('input#roleId').val();
		var userid = $('input#userId').val();
				$.ajax({
					type : "POST",
					url : HOST_PATH + "admin/user/getwebsite/id/" + userid
							+ "/rolid/" + roleid,
					dataType : 'json',
					success : function(data) {
						var op = "<ul>";
						if (data != null) {
							_websiteAccess = data;
							$('div.multiselect').empty();
							for (i in _websiteAccess) {

								
								op += "<li class= list_"+ i + "><input  type='checkbox' style='display:none' name='websites[]"
										+ "' value='"
										+ i
										+ "' /> &nbsp"
										+ _websiteAccess[i] + "</li>";
							}
							op += "</ul>";
							$('div.multiselect').append(op);
							$('div.multiselect ul li').click(
									selectWebSiteInList);
							
							if(! isEdit )
							{
								//removeOverLay();
							}
							
							if($('#websiteaccess')){
								highlightExistingWebsites($('#websiteaccess').val());	
							}
						}
					}
				});
		// get from website access
	} else {
		var op = "<ul>";
		if (_websiteAccess != null) {
			$('div.multiselect').empty();
			for (i in _websiteAccess) {

				op += "<li class= list_"+ i + "><input  type='checkbox' style='display:none' name='websites[]"
						+ "' value='"
						+ i
						+ "' /> &nbsp"
						+ _websiteAccess[i] + "</li>";
			}
			op += "</ul>";
			$('div.multiselect').append(op);
			$('div.multiselect ul li').click(selectWebSiteInList);
		}
	}
	
	
}
/**
 * When admin select website for user then selected class apply on selected list
 * in websiet multiselect then use this function
 * @author mkaur
 */
function selectWebSiteInList() {

	if (($(this).children('input')).is(':checked')) {

		$(this).children('input').removeAttr('checked');
		$(this).removeClass('selected');

	} else {

		$(this).children('input').attr('checked', 'checked');
		$(this).addClass('selected');
	}
}

/**
 * Convert First character of the user in Capital letter
 * @author mkaur
 */
function ucfirst(str) {
	if(str!=null){
		var firstLetter = str.substr(0, 1);
		return firstLetter.toUpperCase() + str.substr(1);
	} else{
		return str;
	}
}
/**
 * Get user list from database and show in dataTable
 * @author mkaur
 */
var visitorListTable = null ;
var id;
var hashValue = "";
var click = false;
function getVisitorList(iSearchText,iEmailText,iStart,iSortCol,iSortDir) {
	
	//alert(HOST_PATH);
	
	//addOverLay();
	$("ul.ui-autocomplete").css('display','none');
	$('#visitorListTable').addClass('widthTB');
	
	visitorListTable = $("#visitorListTable").dataTable({
			"bLengthChange" : false,
			"bInfo" : true,
			"bFilter" : true,
			"bDestroy" : true,
			"bProcessing" : false,
			"bServerSide" : true,
			"iDisplayStart" : iStart,
			"iDisplayLength" : 100,
			"bDeferRender": true,
			"oLanguage": {
			      "sInfo": "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
			},
			"aaSorting": [[ iSortCol , iSortDir ]],
			"sPaginationType" : "bootstrap",
			"sAjaxSource" : HOST_PATH + "admin/visitor/getvisitorlist/for/0/searchtext/" + iSearchText  + '/email/' + iEmailText,
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
						
							return "<a rel="+ obj.aData.id +" href='javascript:void(0);'>" + ucfirst(obj.aData.firstName) + "</a>" ;
						},
						"bSortable" : true
					},
					{
						"fnRender" : function(obj) {
							return "<a href='javascript:void(0);'>" + ucfirst(obj.aData.lastName) + "</a>" ;
						},
						"bSortable" : true
					},
					{
						"fnRender" : function(obj) {
							return "<a href='javascript:void(0);'>" + obj.aData.email + "</a>" ;
						},
						"bSortable" : true
					},
                    {
						"fnRender" : function(obj) {
							return "<a href='javascript:void(0);'>" + obj.aData.opens + "</a>" ;
						},
						"bSortable" : true
					},
                    {
						"fnRender" : function(obj) {
							return "<a href='javascript:void(0);'>" + obj.aData.clicks + "</a>" ;
						},
						"bSortable" : true
					},
                    {
						"fnRender" : function(obj) {
							return "<a href='javascript:void(0);'>" + obj.aData.hard_bounces + "</a>" ;
						},
						"bSortable" : true
					},
                    {
						"fnRender" : function(obj) {
							return "<a href='javascript:void(0);'>" + obj.aData.soft_bounces + "</a>" ;
						},
						"bSortable" : true
					},
                    {
						"fnRender" : function(obj) {
							if(obj.aData.active == true)
							{
								return "<a href='javascript:void(0);'>" + __("Active") + "</a>" ;
							}else{
                                var inactiveStatusReason = '';
								if(obj.aData.inactiveStatusReason !== null) {
                                    inactiveStatusReason = "(" + obj.aData.inactiveStatusReason + ")";
                                }
								return "<a href='javascript:void(0);'>" + __("Inactive "+inactiveStatusReason+"") + "</a>" ;
							}
						},
						"bSortable" : true
					},
					{
						"fnRender" : function(obj) {
							if(obj.aData.weeklyNewsLetter == true)
							{
								return "<a href='javascript:void(0);'>" + __("On") + "</a>" ;
							}else{
								
								return "<a href='javascript:void(0);'>" + __("Off") + "</a>" ;
							}
						},
						"bSortable" : true
					},
					{
						"fnRender" : function(obj) {
						
							return "<a href='javascript:void(0);'>" + obj.aData.created_at.date + "</a>" ;
							
						},
						//"bSearchable" : false,
						"bSortable" : true
					}, 
					{
						"fnRender" : function(obj) {
						
							return "<a  onclick='deleteVisitor("+obj.aData.id+")' href='javascript:void(0)'>" + __('Delete') + "</a>" ;
							
						},
						//"bSearchable" : false,
						"bSortable" : false
					} ],
					
					"fnPreDrawCallback": function( oSettings ) {
						$('#visitorListTable').css('opacity',0.5);
					 },		
					"fnDrawCallback" : function(obj) {
						$('#visitorListTable').css('opacity',1);
						 

							var state = {};
							state[ 'iStart' ] = obj._iDisplayStart ;
							state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
							state[ 'iSortDir' ] = obj.aaSorting[0][1] ;
							state[ 'iSearchText' ] = iSearchText;
							state[ 'iEmailText' ] = iEmailText;
							
							$("#visitorListTable").find('tr').find('td:lt(6)').click(function () {
									var eId = $(this).parent('tr').find('a').attr('rel');
									state[ 'eId' ] = eId ;
									click = true;
									$.bbq.pushState( state );
									window.location.href = HOST_PATH + "admin/visitor/editvisitor/id/" + eId+ "?iStart="+
									obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
									obj.aaSorting[0][1]+"&iSearchText="+iSearchText+"&iEmailText="+iEmailText+"&eId="+eId;
							});
							
							$("#searchVisitor").select2('val',iSearchText);
						    $("#searchEmail").select2('val',iEmailText);
						    
						    // Set the state!
						    $("#searchVisitor").val(iSearchText);
						    if(iSearchText == null){
						    	$.bbq.removeState( 'iSearchText' );
						    }
						    
						    $("#searchEmail").val(iEmailText);
						    if(iEmailText == null){
						    	$.bbq.removeState( 'iEmailText' );
						    }
						    $.bbq.pushState( state );
						    hashValue = location.hash;
						    
						    var aTrs = visitorListTable.fnGetNodes();
			
							for ( var i=0 ; i<aTrs.length ; i++ )
							{
								$editId = $(aTrs[i]).find('a').attr('rel');
								if ( $editId == $.bbq.getState( 'eId' , true ) )
								{
									$(aTrs[i]).find('td').addClass('row_selected');
								}
							}
							
							if($('td.row_selected').length > 0){
								var top = $('td.row_selected').offset().top;
							}
							var windowHeight = $(window).height() / 2 - 50;
							window.scrollTo(0, top - windowHeight);
					 },
					"fnInitComplete" : function(obj) {
						removeOverLay();
						$('td.dataTables_empty').html(__('No record found !'));
						$('td.dataTables_empty').removeAttr('style');
						$('td.dataTables_empty').unbind('click');	
						
						/*$("form#userRegister").each(function() { this.reset(); });*/
							//removeOverLay();
					},
					
					"fnServerData" : function(sSource, aoData, fnCallback) {
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
 * Fetch editable record information and file in form
 * @author mkaur
 */
function callToEdit(){
	var id =  $(this).parent('tr').children('td:eq(0)').children('div.grid-img').attr('editId');
	document.location.href =  HOST_PATH+"admin/visitor/editvisitor/id/" + id ;
}
/**
 * create the dropdown for role filter
 * and show role in top of the grid in Drp
 * @author mkaur
 */
function CreateSelect() {

	$('div.fillterDiv #selectRole').empty();
	var r ='<option value="0"> '+ " " +__("All")+' </option>';
	for(var i in _roles)
		{
			r += '<option value="'+ _roles[i].id + '">'+ _roles[i].name +'</option>';
	    }
	$('div.fillterDiv #selectRole').append(r);
	
	var roleId = $('input#editUserroleId').val();
	if(roleId){
	$("select#role").val(roleId);
	}
}
/**
 *  initiallize sttings for update user
 *  @author spsingh
 */
function initializeSettingForEdit()
{
	// validations 
	validateFormEditUser() ;
	
}




var locale = {
	    "fileupload": {
	        "errors": {
	        	"maxFileSize": __("Maximum image size is 2MB"),
	            "minFileSize": __("File is too small"),
	            "acceptFileTypes": __("Please upload the valid file"),
	            "maxNumberOfFiles": __("Max number of files exceeded"),
	            "uploadedBytes": __("Uploaded bytes exceed file size"),
	            "emptyResult": __("Empty file upload result")
	        },
	        "error": __("Error"),
	        "start": __("Start"),
	        "cancel": __("Cancel"),
	        "destroy": __("Delete")
	    }
};
/**
 * set website in website select 
 * @param websites
 * @author spsingh
 */

function highlightExistingWebsites(websites)
{
	if(websites){
		websites = websites.split(',');
		
		for(i in websites)
		{
			$("li.list_" + websites[i]).addClass('selected').children('input').attr('checked' , 'checked');
		}
   }	
}

// global validation object  
var validator; 

/**
 * form validation during update user 
 * @author spsingh
 */
function validateFormEditUser()
{
	
	validator  = $("form#editRegister")
	.validate(
			{
				errorClass : 'error',
				validClass : 'success',
				errorElement : 'span',
				afterReset  : resetBorders ,
				errorPlacement : function(error, element) {
					element.parent("div").prev("div")
							.html(error);
				},
				rules : {
					firstName : {
						required : true,
						regex : /^[a-zA-Z\-]+$/
					},
					lastName : {
						required : true,
						regex : /^[a-zA-Z\-]+$/
					},
					newPassword : {
						required : function(element)
						{
							// if condition true and apply required validation for element
							
							if($("input#oldPassword" , "form#editRegister").val().length > 0)
							{
								return true ;
								
							} else if ($("input#confirmNewPassword" , "form#editRegister").val().length > 0)
							{
								return true ;
									
							} else {
								
								
								//	Hide error message for new password feild and confirm password feild  
								
								
								$('span.help-inline' , $("[name=newPassword]")
										.parents('div.mainpage-content-right') ).hide();
						
								$('span.help-inline' , $("[name=confirmNewPassword]")
										.parents('div.mainpage-content-right') ).hide();
								
								
								$("input#newPassword" , "form#editRegister")
								.parents("div.mainpage-content-line")
								.find(".error,.success")
								.removeClass("error success");
								
								$("input#confirmNewPassword" , "form#editRegister")
									.parents("div.mainpage-content-line")
									.find(".error,.success")
									.removeClass("error success");
								
								
								// check if element not filled , then hide validation message for old password feild
								 if ( $(element).val().length == 0 ) 
								 {
										$('span.help-inline' , $("[name=oldPassword]")
											.parents('div.mainpage-content-right') ).hide();
		
										$("input#oldPassword" , "form#editRegister")
										.parents("div.mainpage-content-line")
										.find(".error,.success")
										.removeClass("error success");
								}
										


								return false ;
							}
						},
						minlength : 8,
						maxlength:20
					},
					confirmNewPassword : {
						required: function(element)
						{
							// if condition true and apply required validation for element
							
							if($("input#oldPassword" , "form#editRegister").val().length > 0)
							{
								return true ;
								
							} else if ($("input#newPassword" , "form#editRegister").val().length > 0)
							{
								return true ;
							} else {
								//	hide error message for confirm password  feild
								$('span.help-inline' , $("[name=confirmNewPassword]")
										.parents('div.mainpage-content-right') ).hide();
								$("input#confirmNewPassword" , "form#editRegister")
								.parents("div.mainpage-content-line")
								.find(".error,.success")
								.removeClass("error success");
								
								
								/**
								 * check if element not filled , then hide validation message for old password feild
								 * as well as new password feild
								 */
 
								
								 if ( $(element).val().length == 0) 
								 {
										$('span.help-inline' , $("[name=newPassword]")
												.parents('div.mainpage-content-right') ).hide();

										$('span.help-inline' , $("[name=oldPassword]")
												.parents('div.mainpage-content-right') ).hide();
										
										
										$("input#newPassword" , "form#editRegister")
										.parents("div.mainpage-content-line")
										.find(".error,.success")
										.removeClass("error success");
										
										
										 $("input#oldPassword" , "form#editRegister")
										 .parents("div.mainpage-content-line")
										 .find(".error,.success")
										 .removeClass("error success");
								}
								 
								return false ;
							}
						},
						equalTo :	"#newPassword",
						minlength : 8,
						maxlength:20
					},
					oldPassword : {
						required: function(element)
						{
							// if condition true and apply required validation for element
							
							if($("input#newPassword" , "form#editRegister").val().length > 0)
							{
								return true ;
								
							} else if ($("input#confirmNewPassword" , "form#editRegister").val().length > 0)
							{
								return true ;
								
							} else {
								
								/**
								 * Hide validation message for old password feild , new password feild
								 * and  old password feild
								 */
								
								$('span.help-inline' , $("[name=oldPassword]")
										.parents('div.mainpage-content-right') ).hide();
								
								$('span.help-inline' , $("[name=newPassword]")
										.parents('div.mainpage-content-right') ).hide();
								
								$('span.help-inline' , $("[name=confirmNewPassword]")
										.parents('div.mainpage-content-right') ).hide();


								$("input#oldPassword" , "form#editRegister")
								.parents("div.mainpage-content-line")
								.find(".error,.success")
								.removeClass("error success");
								
								
								$("input#newPassword" , "form#editRegister")
									.parents("div.mainpage-content-line")
									.find(".error,.success")
									.removeClass("error success");
								
								$("input#confirmNewPassword" , "form#editRegister")
									.parents("div.mainpage-content-line")
									.find(".error,.success")
									.removeClass("error success");
								
								return false ;
							}
						},
						minlength : 8,
						maxlength:20,
						remote : {
							url: HOST_PATH + "admin/user/validatepassword",
				        	type: "post" ,
				        	data : { id : $("input#id" , "form#editRegister").val()  } ,
				        	async: false,
				        	beforeSend  : function ( xhr ) {
				        	    
				        		$('span[for=oldPassword]').html(__('please wait..')).addClass('validating').show()
				        		.parent('div').attr('class' , 'mainpage-content-right-inner-right-other focus') ;
				        	  },
				        	complete : function(e) {
				        		
			        			$('span.help-inline' , $("[name=oldPassword]").parents('div.mainpage-content-right') ).removeClass('validating') ;
				        		
				        		if(e.responseText == "true")
				        		{
				        			$("[name=oldPassword]").parent('div').prev("div").removeClass('focus')
				        			.removeClass('error').addClass('success');
				        			
				        			$('span.help-inline' , $("[name=oldPassword]").parents('div.mainpage-content-right') )
				        				.html(validRules['oldPassword'])
				        				.attr('remote-validated' , true);
				        		} else
				        		{
				        			$('span.help-inline' , $("[name=oldPassword]").parents('div.mainpage-content-right') )
			        				.attr('remote-validated' , false);
				        			
				        			
				        			
				        		}
				        	}
						},						

					},
				},
				messages : {
					email : {
						required : __("Please enter your email address"),
						email : __("Please enter valid email address")
					},
					firstName : {
						required : __("Please enter your first name"),
						regex : __("First Name should be Alphabets")

					},
					lastName : {
						required : __("Please enter your last name"),
						regex : __("Last Name should be Alphabets")
					},
					oldPassword : {
						required : __("Please enter your old password"),
						minlength : __("It should be minimum 8 characters"),
						remote : __("Old password don't matched"),
						maxlength : __("Please enter maximum 20  characters")
					},
					newPassword : {
						required : __("Please enter your new password"),
						minlength : __("Please enter minimum 8 characters"),
						maxlength : __("Please enter maximum 20  characters")
					},
					confirmNewPassword : {
						equalTo : __("Please enter same as new password"),
						minlength : __("Please enter minimum 8 characters"),
						required : __("Please re-type your new password"),
						maxlength : __("Please enter maximum 20  characters")
							
					}

				},

				onfocusin : function(element) {
					if (!$(element).parent('div').prev("div")
							.hasClass('success')) {
						
						
						
			    		 var label = this.errorsFor(element);
			    		 if( $( label).attr('hasError')  )
			    	     {
			    			 if($( label).attr('remote-validated') != "true")
			    			 	{
									this.showLabel(element, focusRules[element.name]);
									
									$(element).parent('div').removeClass(
													this.settings.errorClass)
											.removeClass(
													this.settings.validClass)
											.prev("div")
											.addClass('focus')
											.removeClass(
													this.settings.errorClass)
											.removeClass(
													this.settings.validClass);
			    			 	}
			    			 
			    	     } else {
			    	    	this.showLabel(element, focusRules[element.name]);
							
							$(element).parent('div').removeClass(
											this.settings.errorClass)
									.removeClass(
											this.settings.validClass)
									.prev("div")
									.addClass('focus')
									.removeClass(
											this.settings.errorClass)
									.removeClass(
											this.settings.validClass);
			    	     }
					}
				},

				highlight : function(element,
						errorClass, validClass) {

					$(element).parent('div')
							.removeClass(validClass)
							.addClass(errorClass).prev(
									"div").removeClass(
									validClass)
							.addClass(errorClass);

				},
				unhighlight : function(element,
						errorClass, validClass) {
					
					if(! $(element).hasClass("passwordField"))
					{

						$(element).parent('div')
								.removeClass(errorClass)
								.addClass(validClass).prev(
										"div").addClass(
										validClass)
								.removeClass(errorClass);
	
						$(
								'span.help-inline',
								$(element).parent('div')
										.prev('div')).text(
								validRules[element.name]);

					}
				},
				success: function(label , element) {
					
					
					
					$(element).parent('div')
					.removeClass(this.errorClass)
					.addClass(this.validClass).prev(
							"div").addClass(
									this.validClass)
					.removeClass(this.errorClass);
					
				    $(label).append( validRules[element.name] ) ;
				    label.addClass('valid') ;
				    }
			});

}
/**
 * set hasing of the user panle
 * @author spsingh
 */
function hashUserList()
{
	$("div.web-address-list li.selected").removeClass('selected').children('input').removeAttr('checked');
	$("form").each(function() { this.reset(); }); 
	
	$("input[type=password" , "form#editRegister").addClass("passwordField");
}

/**
 * reset the validation border of imput field
 * @param el
 * @author spsingh
 */
function resetBorders(el)
{
	$(el).each(function(i,o){
		
		$(o).parent('div')
		.removeClass("error").removeClass('success')
		.prev("div").removeClass('focus').removeClass('error').removeClass('success') ;
	
	});
	

}


/**
 * delete Visitor from database by id id get from hidden field
 * @author cbhopal
 */
function deleteVisitor(id) {
	bootbox.confirm(__('Are you sure you want to delete this visitor Permanently?'), function(r){
		if(!r){
			return false;
		}else{
			moverToTrash(id);
		}
    });
}
/**
 * when delete action in confirmed the ajax call to delete the record according
 * to id
 * @author cbhopal
 * @param id
 */
function moverToTrash(id) {
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/visitor/permanentdelete",
		method : "post",
		data : {'id' : id},
		dataType : "json",
		type : "post",
		success : function(data) {
			
			if (data != null) {
				removeOverLay();
				//bootbox.alert("Record has been deleted");
				document.location.href = HOST_PATH + "admin/visitor" ;
				
			} else {
				bootbox.alert(__("<span class='success'>Problem in your data</span>"));
				//jAlert('Problem in your data');
			}
		}
	});
}


/**
 * Function call when user click on shop search button 
 * or press enter 
 * @author kraj
 */
function searchByVisitor()
{
	
	var searchArt = $("#searchVisitor").select2('val');
	if(searchArt=='' || searchArt==null)
	{
		searchArt = undefined;
	}
	var searchEmail = $("input#searchEmail").select2('val');
	if(searchEmail=='' || searchEmail==null)
	{
		searchEmail = undefined;
	}
	getVisitorList(searchArt,searchEmail,0,0,'asc');
}

