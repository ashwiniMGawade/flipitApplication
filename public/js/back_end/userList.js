var filter = "";
var userList = $('#userList').dataTable();
var _websiteAccess = null;
var _roles =null;
var _availableTags = null;
/**
 * validRules oject contain all the messages that are visible when an elment
 * val;ue is valid
 * structure to define a message for element key is to be element name Value is
 * message
 */
var validRules = {

	firstName : __("First name looks great"),
	email : __("Email looks great"),
	lastName : __("Last name looks great"),
	password : __("ok ! good password"),
	confPassword : __("ok ! password match"),
	confirmNewPassword : __("Password matched") ,
	newPassword : __("ok ! good password"),
	//oldPassword : __("Old password matched!"),
	google : __("Google+ URL looks great"),
	twitter : __("Twitter URL looks great"),
	pintrest : __("Pinterest URL looks great"),
	likes : __("Likes looks great"),
	dislike : __("Dislikes looks great"),
	maintext : __("Main text looks great"),
	popularKortingscode : __("Populaire kortingscodes looks great")
	//role : "ok ! "
	// files:'222222'
};
/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 * structure to define a message for element key is to be element name Value is
 * message
 */
var focusRules = {

	firstName : __("Enter your first name"),
	email : __("What &#39;s your email address?"),
	lastName : __("Enter your last name."),
	password : __("Choose password!"),
	confPassword : __("Re-type password"),
	//oldPassword : __("Enter old password to update password"),
	newPassword : __("Choose new password"),
	confirmNewPassword : __("Re-type new password"),
	google : __("Enter your google+ URL"),
	twitter : __("Enter your twitter URL "),
	pintrest : __("Enter your pinterest URL"),
	likes : __("Enter your favorite things"),
	dislike : __("Enter your unfavorite things"),
	maintext : __("Enter your main text"),
	imageName: __("Upload your profile picture"),
	popularKortingscode : __("Enter populaire kortingscodes")

};
var validatorAddUser = null;
$(document).ready(function() {


	var iSearchText = $.bbq.getState( 'iSearchText' , true ) || undefined;
	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 1;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'ASC';
	var role = $.bbq.getState( 'iRole' , true ) || undefined;

	getUserList(iSearchText,iStart,iSortCol,iSortDir,role);

	$('#searchButton').click(searchByUser);

	//bind with keypress of search box
	$("input#tags").keypress(function(e)
		{
			// if the key pressed is the enter key
			  if (e.which == 13)
			  {
			  		var role = $("#selectRole").val();

			  		if(role == '' || role==null)
					{
						role = undefined;
					}

					getUserList($(this).val(),0,1,'ASC' , role );
			  }
	});

	$(window).bind( 'hashchange', function(e) {
		if(hashValue != location.hash && click == false){
			userList.fnCustomRedraw();
		}
	});

	 				$(":input").attr("autocomplete","off");
					//Get Role for Filter
					getRoles();
					//auto complete
					//if press enter key the call search offer function

					$("#tags").autocomplete({
                    minLength: 1,
                    source: function( request, response)
                    {
                    	$.ajax({
                 			url : HOST_PATH + "admin/user/gettopfive/for/0/text/" + $('#tags').val(),
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
				$(".multiselect").multiselect();
				initializeSettingForEdit();
				// validate form
					validateFormAddNewUser();

						//getUserList(undefined,undefined);
						getWebsite();//getwebsite of logged user

						//add code by kuldeep according to new changes in user module
						$('#deleteOne').click(deleteOne);
						$('#addNewStore').click(addNewStore);
						//auto complete for shop search from database and get to ten best search store
						//var shopIds=[];

						$("#searchShopText").autocomplete({
					        minLength: 1,
					        source: function( request, response)
					        {
					        	$.ajax({
					        		url : HOST_PATH + "admin/user/searchtoptenshop/keyword/" + $('#searchShopText').val() + "/selectedShop/" + $('input#fevoriteStore').val(),
					     			method : "post",
					     			dataType : "json",
					     			type : "post",
					     			success : function(data) {
					     			btnSelectionAddNew();
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
					        select: function(event, ui ) {

					        	$('input#selectedShopId').val(ui.item.id);
					        	//$("#searchShopText").val(ui.item.label);
					        	//console.log(ui.item.id);

					        }

					    });
						//code for selection of li
						$('ul#favoriteStore li').click(changeSelectedClass);

						//end code

						//VALIDATION MESSAE DISPLAY OR NOT

						$("form").submit(function(){
							if (! jQuery.isEmptyObject(invalidForm) )

								for(var i in invalidForm)
								{
									if(invalidForm[i])
									{
										$("button#saveUser").removeAttr('disabled');
										return false;
									}else {


									}

								}
						});
						$('form#userRegister').submit(function(){

							saveUser();

						});

				});

$.validator.setDefaults({
	onkeyup : false,
	onfocusout : function(element) {
		$(element).valid();
	}

});
function saveUser()
{
	if($("form#userRegister").valid())
	{
		$("button#saveUser").attr('disabled' , 'disabled') ;
		return true;

	}else {

		return false;
	}
}
var invalidForm = {} ;
var errorBy = "" ;
//used to validate upload logo type
function checkFileType(e)
{

	var el = e.target  ? e.target :  e.srcElement ;
	 var cls = '';
	if(parseInt($('input#editUserroleId').val()) > 0 )
	{
		cls = 'marginForError';

	}
	 var regex = /png|jpg|jpeg|gif|GIF|PNG|JPG|JPEG/ ;
	 if( regex.test(el.value) )
	 {

		 invalidForm[el.name] = false ;

		 $(el).parents("div.mainpage-content-right").addClass('success')
		 .children("div.mainpage-content-right-inner-right-other").removeClass("focus")
		 .html(__("<span class='" + cls +" success help-inline'>Valid file</span>"));

	 } else {

		 $(el).parents("div.mainpage-content-right").addClass('error').removeClass('success')
		 .children("div.mainpage-content-right-inner-right-other").removeClass("focus")
		 .html(__("<span class='" + cls + " error help-inline'>Please upload valid file</span>"));

		 invalidForm[el.name] = true ;
		 errorBy = el.name ;
	 }
}
/**
 * check form i valid or not
 * @author kraj
 */
function validateFormAddNewUser()
{
	validatorAddUser  = $("form#userRegister")
	.validate(
			{
				errorClass : 'error',
				validClass : 'success',
				errorElement : 'span',
				afterReset  : resetBorders,
				errorPlacement : function(error,
						element) {

					element.parent("div").prev("div")
							.html(error);
				},
				rules : {
					email : {
						required : true,
						minlength : 6,
						email : true,
						remote : {
							url : HOST_PATH
									+ "admin/user/checkuser",
							type : "post",
							beforeSend : function(xhr) {

								$('span[for=email]')
										.html(__('Validating...'))
										.addClass('validating')
										.parent('div')
										.addClass('focus')
										.next('div')
										.addClass('focus');
								;

							},

							complete : function(data) {
								$('span[for=email]')
										.removeClass(
												'validating');
								if (data.responseText == 'true') {
									$('span[for=email]')
											.html(__('Valid Email'))
											.parent(
													'div')
											.removeClass(
													'error')
											.addClass(
													'success')
											.prev("div")
											.removeClass(
													'error')
											.addClass(
													'success');
								} else {

									$('span[for=email]')
											.parent(
													'div')
											.removeClass(
													'success')
											.addClass(
													'error')
											.prev("div")
											.removeClass(
													'success')
											.addClass(
													'error');
								}

							}
						}
					},

					firstName : {
						required : true,
						regex : /^[a-zA-Z \-]+$/
					},
					lastName : {
						required : true,
						regex : /^[a-zA-Z \-]+$/
					},
					password : {
						required : true,
						minlength : 8,
						maxlength :20
					},
					confPassword : {
						required : true,
						minlength : 8,
						maxlength :20,
						equalTo : "#password"
					},
					popularKortingscode :{
						required : true,
						number : true

					},
					google:{

						regex: /^(?:(ftp|http|https):\/\/)?(?:[\w-]+\.)?plus.google.com/
						//regex  : /^(?:(ftp|http|https):\/\/)?(?:[\w-]+\.)+[a-z]{3,6}$/

					},
					twitter:{
						regex: /^(?:(ftp|http|https):\/\/)?(?:[\w-]+\.)?twitter.com/
						//regex  : /^(?:(ftp|http|https):\/\/)?(?:[\w-]+\.)+[a-z]{3,6}$/

					},
					pintrest:{
						regex: /^(?:(ftp|http|https):\/\/)?(?:[\w-]+\.)?pinterest.com/
						//regex  : /^(?:(ftp|http|https):\/\/)?(?:[\w-]+\.)+[a-z]{3,6}$/

					}
				},
				messages : {
					email : {
						required : __("Please enter your email address"),
						email : __("Please enter valid email address"),
						minlength: __("Please enter valid email address"),
						remote : __("Email already exists")
					},
					firstName : {
						required : __("Please enter your first name"),
						regex : __("First Name should be Alphabets")

					},
					lastName : {
						required : __("Please enter your last name"),
						regex : __("Last Name should be Alphabets")
					},
					password : {
						required : __("Please enter your password"),
						minlength : __("Please enter minimum 8  characters"),
						maxlength : __("Please enter maximum 20  characters")
					},
					confPassword : {
						required : __("Please re-type your password"),
						minlength : __("Please enter minimum 8  characters"),
						maxlength : __("Please enter maximum 20  characters")
					},
					twitter:{

						regex: __("Please enter the valid twitter URL")
					},
					google:{

						regex: __("Please enter the valid google+ URL")
					},
					pintrest:{

						regex: __("Please enter the valid pinterest URL")
					},
					popularKortingscode :
					{
						required : __("Please enter populaire kortingscodes"),
						number : __("Please enter numeric value"),

					}
				},

				onfocusin : function(element) {

					// display hint messages when an element got focus
					if (!$(element).parent('div').prev("div")
							.hasClass('success')) {

			    		 var label = this.errorsFor(element);

			    		 if( $( label ).attr('hasError')  )
			    	     {
			    			 if($( label ).attr('remote-validated') != "true")
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

					// highlight borders in case of error
					$(element).parent('div')
							.removeClass(validClass)
							.addClass(errorClass).prev(
									"div").removeClass(
									validClass)
							.addClass(errorClass);

					$('span.help-inline', $(element).parent('div')
									.prev('div')).removeClass(validClass) ;

				},
				unhighlight : function(element,
						errorClass, validClass) {




					// check for ignored elemnets and highlight borders when succeed
					if(! $(element).hasClass("ignore")) {

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
							validRules[element.name]) ;
					} else
					{

						// check to display errors for ignored elements or not

						var showError = false ;

						//
						switch( element.nodeName.toLowerCase() ) {
						case 'select' :

							var val = $(element).val();

							if($($(element).children(':selected')).attr('default') == undefined)
							{
								showError = true ;
							} else
							{
								showError  = false;
							}
							break ;
						case 'input':
							if ( this.checkable(element) ) {

								showError = this.getLength(element.value, element) > 0;

							} else if($.trim(element.value).length > 0) {

									showError =  true ;

								} else {

									showError = false ;
								}

							break ;
						default:
							var val = $(element).val();
							showError =  $.trim(val).length > 0;
						}


						if(! showError )
						{
							// hide errors message and remove highlighted borders
								$(
										'span.help-inline',
										$(element).parent('div')
										.prev('div')).hide();

									$(element).parent('div')
									.removeClass(errorClass)
									.removeClass(validClass)
									.prev("div")
									.removeClass(errorClass)
									.removeClass(validClass) ;
						} else
						{
							// show errors message and  highlight borders

							// display green border and message
							//if ignore element type is not file

							if(element.type !== "file")
							{

								$(element).parent('div')
								.removeClass(errorClass)
								.addClass(validClass).prev(
										"div").addClass(
										validClass)
								.removeClass(errorClass);

								$('span.help-inline', $(element).parent('div')
												.prev('div')).text(
									 validRules[element.name] ).show();
							} else
							{
								$(element).parent('div')
								.removeClass(errorClass)
								.removeClass(validClass)
								.removeClass("focus")
								.prev("div")
								.removeClass("focus")
								.removeClass(errorClass)
								.removeClass(validClass) ;
							}
						}
					}

				},
				success: function(label , element) {

					$(element).parent('div')
					.removeClass(this.errorClass)
					.addClass(this.validClass).prev(
							"div").addClass(
									this.validClass)
					.removeClass(this.errorClass)
					.removeClass("focus");
				    $(label).append( validRules[element.name] ) ;
				    label.addClass('valid') ;

				}

			});

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
	bootbox.confirm(__('Are you sure you want to move this record to trash?'), function(r){
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
 * Get roles from database and display in role filter
 * @author kraj
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


								op += "<li class= list_"+ _websiteAccess[i]['id'] + "><input type='checkbox' style='display:none' name='websites[]"
										+ "' value='"
										+ _websiteAccess[i]['id']
										+ "' /> &nbsp"
										+ _websiteAccess[i]['name'] + "</li>";
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
 * @author kraj
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
 * @author kraj
 */
function ucfirst(str) {
	var firstLetter = str.substr(0, 1);
	return firstLetter.toUpperCase() + str.substr(1);
}
/**
 * Get user list from database and show in dataTable
 * @author kraj
 */
var click = false;
var hashValue = "";
function getUserList(iSearchText,iStart,iSortCol,iSortDir,iRole) {
	//addOverLay();
    $("ul.ui-autocomplete").css('display','none');


	$('#userList').addClass('widthTB');

	userList = $("#userList")
			.dataTable(
					{

						"bLengthChange" : false,
						"bInfo" : true,
						"bFilter" : true,
						"bDestroy" : true,
						"bProcessing" : false,
						"bServerSide" : true,
						"iDisplayLength" : 100,
						"oLanguage": {
						      "sInfo": "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
						},
						"iDisplayStart" : iStart,
						"aaSorting": [[ iSortCol , iSortDir ]],
						"sPaginationType" : "bootstrap",
						"sAjaxSource" : HOST_PATH + "admin/user/getuserlist/searchtext/" + iSearchText + "/role/" + iRole,
						"aoColumns" : [
						{
							"fnRender" : function(obj) {
						         var id = null;
								return id = obj.aData.id;

							},
							"bVisible":    false ,
							"bSortable" : false,
							"sType": 'numeric'

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

								return email = "<span class='word-wrap-email'><a href='javascript:void(0);'>" + obj.aData.email + "</a></span>" ;

							},

							"bSortable" : true
						}, {

							"fnRender" : function(obj) {

								return role =    "<a href='javascript:void(0);'>" + obj.aData.users.name + "</a>";

							},

							"bSortable" : true,
							'sWidth':'200px'
						}  ],
						"fnInitComplete" : function(obj) {
							$('td.dataTables_empty').html(__('No record found !'));
							$('td.dataTables_empty').unbind('click');
							$("form#userRegister").each(function() { this.reset(); });
							//removeOverLay();
						},
						"fnPreDrawCallback": function( oSettings ) {
							$('#userList').css('opacity',0.5);
						 },
						"fnDrawCallback" : function(obj) {
							$('#userList').css('opacity',1);

							var state = {};
								$("#userList").find('tr').find('td').click(function () {
										var eId = $(this).parent('tr').find('div').attr('editid');
										state[ 'eId' ] = eId ;
										click = true;
										$.bbq.pushState( state );
										window.location.href = HOST_PATH + "admin/user/edituser/id/" + eId+ "?iStart="+
										obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
										obj.aaSorting[0][1]+"&iSearchText="+iSearchText+"&iRole="+iRole+"&eId="+eId;
								});




								//console.log(obj);
							    // Set the state!
							    state[ 'iStart' ] = obj._iDisplayStart ;
							    state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
							    state[ 'iSortDir' ] = obj.aaSorting[0][1] ;
							    state[ 'iSearchText' ] = iSearchText;
							    state[ 'iRole' ] = iRole;
							    $("#SearchCategory").val(iSearchText);

							    if(iSearchText == undefined){
							    	$.bbq.removeState( 'iSearchText' );
							    }

							    if(iRole == undefined){
							    	$.bbq.removeState( 'iRole' );
							    }


							    $.bbq.pushState( state );
							    hashValue = location.hash;

							    var aTrs = userList.fnGetNodes();

								for ( var i=0 ; i<aTrs.length ; i++ )
								{
									$editId = $(aTrs[i]).find('div').attr('editid');
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
 * Search record from database according to role filter
 * @author kraj
 */
function searchFilterForDrp()
{
	//$('#selectRole')
	var role  =  $(this).val();
	getUserList(undefined,0,1,'asc',role);
}

/**
 * Fetch editable record information and file in form
 * @author spsingh
 */
function callToEdit()
{
	var id =  $(this).children('td:eq(0)').children('div.grid-img').attr('editId');
	document.location.href =  HOST_PATH+"admin/user/edituser/id/" + id ;

}
/**
 * create the dropdown for role filter
 * and show role in top of the grid in Drp
 * @author kraj
 */
function CreateSelect() {

	$('div.fillterDiv #selectRole').empty();
	var r ='<option value="0"> '+__("All")+' </option>';
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
						regex : /^[a-zA-Z \-]+$/
					},
					lastName : {
						required : true,
						regex : /^[a-zA-Z \-]+$/
					},
					newPassword : {
						minlength : 8,
						maxlength:20
					},
					confirmNewPassword : {
						equalTo :	"#newPassword",
						minlength : 8,
						maxlength:20
					},
					google:{

						regex: /^(?:(ftp|http|https):\/\/)?(?:[\w-]+\.)?plus.google.com/
						//regex  : /^(?:(ftp|http|https):\/\/)?(?:[\w-]+\.)+[a-z]{3,6}$/

					},
					twitter:{
						regex: /^(?:(ftp|http|https):\/\/)?(?:[\w-]+\.)?twitter.com/
						//regex  : /^(?:(ftp|http|https):\/\/)?(?:[\w-]+\.)+[a-z]{3,6}$/

					},
					pintrest:{
						regex: /^(?:(ftp|http|https):\/\/)?(?:[\w-]+\.)?pinterest.com/
						//regex  : /^(?:(ftp|http|https):\/\/)?(?:[\w-]+\.)+[a-z]{3,6}$/

					},
					popularKortingscode :{
						required : true,
						number : true

					}
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
					newPassword : {

						minlength : __("Please enter minimum 8 characters"),
						maxlength : __("Please enter maximum 20  characters")
					},
					confirmNewPassword : {
						equalTo : __("Please enter same as new password"),
						minlength : __("Please enter minimum 8 characters"),
						maxlength : __("Please enter maximum 20  characters")

					},
					twitter:{

						regex: __("Please enter the valid twitter URL")
					},
					google:{

						regex: __("Please enter the valid google+ URL")
					},
					popularKortingscode :{
						required : true,
						number : true

					},
					pintrest:{

						regex: __("Please enter the valid pinterest URL")
					},
					popularKortingscode :
					{
						required : __("Please enter populaire kortingscodes"),
						number : __("Please enter numeric value"),

					}

				},

				onfocusin : function(element) {

					// display hint messages when an element got focus
					if (!$(element).parent('div').prev("div")
							.hasClass('success')) {

			    		 var label = this.errorsFor(element);

			    		 if( $( label ).attr('hasError')  )
			    	     {
			    			 if($( label ).attr('remote-validated') != "true")
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
									if( element.type=='file')
									{
										$(element).parent('div')
										.siblings('div.mainpage-content-right-inner-right-other')
										.children('span.help-inline').addClass('marginForError');
									}
			    	     }
					}
				},

				highlight : function(element,
						errorClass, validClass) {

					// highlight borders in case of error
					$(element).parent('div')
							.removeClass(validClass)
							.addClass(errorClass).prev(
									"div").removeClass(
									validClass)
							.addClass(errorClass);

					$('span.help-inline', $(element).parent('div')
									.prev('div')).removeClass(validClass) ;

				},
				unhighlight : function(element,
						errorClass, validClass) {
					// check for ignored elemnets and highlight borders when succeed
					if(! $(element).hasClass("ignore")) {
						if($(element).val().length > 0)
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
							validRules[element.name]) ;


						} else {

						$(element).parent('div')
								.removeClass(errorClass).removeClass(validClass)
								.addClass('focus').prev("div").addClass('focus').removeClass(validClass)
								.removeClass(errorClass);
								$('span.help-inline',$(element).parent('div').prev('div')).text(
							validRules[element.name]) ;
						}
					} else
					{

						// check to display errors for ignored elements or not

						var showError = false ;

						//
						switch( element.nodeName.toLowerCase() ) {
						case 'select' :

							var val = $(element).val();

							if($($(element).children(':selected')).attr('default') == undefined)
							{
								showError = true ;
							} else
							{
								showError  = false;
							}
							break ;
						case 'input':
							if ( this.checkable(element) ) {

								showError = this.getLength(element.value, element) > 0;

							} else if($.trim(element.value).length > 0) {

									showError =  true ;

								} else {

									showError = false ;
								}

							break ;
						default:
							var val = $(element).val();
							showError =  $.trim(val).length > 0;
						}


						if(! showError )
						{
							// hide errors message and remove highlighted borders
								$(
										'span.help-inline',
										$(element).parent('div')
										.prev('div')).hide();

									$(element).parent('div')
									.removeClass(errorClass)
									.removeClass(validClass)
									.prev("div")
									.removeClass(errorClass)
									.removeClass(validClass) ;
						} else
						{
							// show errors message and  highlight borders

							// display green border and message
							//if ignore element type is not file

							if(element.type !== "file")
							{

								$(element).parent('div')
								.removeClass(errorClass)
								.addClass(validClass).prev(
										"div").addClass(
										validClass)
								.removeClass(errorClass);

								$('span.help-inline', $(element).parent('div')
												.prev('div')).text(
									 validRules[element.name] ).show();
							} else
							{


								$(element).parent('div')
								.removeClass(errorClass)
								.removeClass(validClass)
								.removeClass("focus")
								.prev("div")
								.removeClass("focus")
								.removeClass(errorClass)
								.removeClass(validClass) ;

							}
						}
					}

				},
				success: function(label , element) {

					$(element).parent('div')
					.removeClass(this.errorClass)
					.addClass(this.validClass).prev(
							"div").addClass(
									this.validClass)
					.removeClass(this.errorClass)
					.removeClass("focus");
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
 * export uselist in excell format
 * pass array of user list
 */
function exportUserList(e)
{
	$.ajax({
		type : "POST",
		url : HOST_PATH + "admin/user/exportuserlist/",
		dataType : 'json',
		success : function(data) {
			alert('Hello');
		}
	});

}
//add code by kudeep according to new changes
/**
 * select category
 * @author kraj
 * @param  form post data
 */

function addCategory(e,catgory){

	var btn = e.target  ? e.target :  e.srcElement ;


	if(btn.type == "abbr")
	{
		btn = $(btn).chidlren('button');
	}

	if($(btn).hasClass('btn-primary'))
	{
		$(btn).removeClass('btn-primary') ;
		$("input#category-" + catgory).removeAttr('checked').valid() ;
	} else
	{
		$(btn).addClass('btn-primary');
		$("input#category-" + catgory).attr('checked' , 'checked').valid();
	}
}
/**
 * change selected class of li
 * @author kraj
 * @version 1.0
 */
function changeSelectedClass() {

	$('ul#favoriteStore li').removeClass('selected');
	$(this).addClass('selected');
	//apply selected class on current button
	btnSelectionDel();
}
/**
 * apply selected class on button
 * on deletion time
 * @author kraj
 * @version 1.0
 */
function btnSelectionDel()
{
	$("#deleteOne").addClass('btn-primary');
	$("#addNewStore").removeClass('btn-primary');
}
/**
 * apply selected class on button
 * on addding time
 * @author kraj
 * @version 1.0
 */
function btnSelectionAddNew()
{
	$("#addNewStore").addClass('btn-primary');
	$("#deleteOne").removeClass('btn-primary');
}
/**
 * add store in favorites list
 * @author kraj
 * @version 1.0
 */
function addNewStore() {

	btnSelectionAddNew();
	$('#addNewStore').attr('disabled' ,"disabled");
	//apply selected class on current button
	if($('ul#favoriteStore li').length > 24) {

		bootbox.alert(__('store list can have maximum 25 records, please delete one if you want to add more store'));

		$('#addNewStore').removeAttr('disabled');

	} else {

		if($("input#searchShopText").val()=='' ||
			$("input#searchShopText").val()==undefined) {

				//console.log('ok');
				bootbox.alert('Please select a store');
				$('#addNewStore').removeAttr('disabled');

				} else {

					var shopName = $("input#selectedShopId").val();
					checkDuplicate(shopName);

	     	  }
			$('#addNewStore').removeAttr('disabled');
	     }

		//code add offer in list here

	}
/**
 * check store in database exist or not
 * @param shopName
 * @author kraj
 * @version 1.0
 */
function checkDuplicate(shopName)
{
	$.ajax({
		url : HOST_PATH + "admin/user/checkstoreexist/name/" + shopName,
			method : "post",
			dataType : "json",
			type : "post",
			success : function(json) {

				 switch(json) {

				 	case 0:
				 		//store not exist in database
				 		bootbox.alert(__('This store does not exists'));
				 		break;

				 	case 1:
				 		//find store in  database
				 		var a =  checkStoreInList(shopName);
				 		console.log(a + 'retrunVal');
				 		if(a==1) {

				 			  	//appen li
				 			   generateObj(shopName);
				 			   $('ul#favoriteStore li#0').remove();
				 			   $('ul#favoriteStore li').removeClass('selected');
				 			   var li  = "<li class='selected'  relstore='" + shopName + "' id='" + shopName + "' >" + $("input#searchShopText").val() + "</li>";
				 			   $('ul#favoriteStore').append(li);
				 			   $('ul#favoriteStore li#'+ shopName).click(changeSelectedClass);
				 			   $("input#searchShopText").val('');

				 			} else {

				 				bootbox.alert(__('This store already exists in the list'));
				 			}
				 		break;
				 	default:
				 		break;
				 }
			}
	});

}
/**
 * check store in list if exist then
 * retunrn 2
 * @return int netVal
 * @author kraj
 * @version 1.0
 */
function checkStoreInList(shopName) {

	var pr = $("#fevoriteStore").val().split(',');
	var netVal = 1;
	for(var i in pr) {
		if(pr[i]==shopName) {
		 netVal =  2;
		  }
		}
    return netVal;
}
/**
 * generate string of li values and
 * add in hidden field when user add store in list
 * @param id
 * @author kraj
 */
function generateObj(shopName) {

	var pr = $("#fevoriteStore").val();
	var newValOfPr = '';
	if(pr=='') {

		newValOfPr = shopName;

		} else {

		 newValOfPr  = pr + "," + shopName;
		}
	$("#fevoriteStore").val(newValOfPr);
}
/**
 * delete store from list
 * @author kraj
 * @version 1.0
 */
function deleteOne() {

	btnSelectionDel();
	$('#deleteOne').attr('disabled' ,"disabled");
	//apply selected class on current button
	var id = $('ul#favoriteStore li.selected').attr('id');
	if(id !=null && id!='' && id!=undefined){

		$('ul#favoriteStore li#' + id).remove();
			regenerateObj(id);
		$('#deleteOne').removeAttr('disabled');

	} else {

		bootbox.alert(__('Please select a store from list'));
		$('#deleteOne').removeAttr('disabled');
	}

}
/**
 * regennerate string of li values and
 * add in hidden field when user delete a store
 * from list
 * @param id
 * @author kraj
 */
function regenerateObj(id)
{
	/*var pr = $("#fevoriteStore").val().split(',');
	var j = pr.length-1;
	var newValOfPr = '';
	for(var i in pr) {

		 if(pr[i]==id){

		 } else {
			if(i==j){

				 newValOfPr += pr[i];

			 } else {

				 newValOfPr += pr[i] + ",";
			 }

		 }

	 }*/
	var newValOfPr = '';

	var j =  $('ul#favoriteStore li').length - 1;
	$('ul#favoriteStore li').each(function(index) {


		if(index==j){

			 newValOfPr +=  $(this).attr('id');

		} else {

			 newValOfPr += $(this).attr('id') + ",";
		}


	});
	$("#fevoriteStore").val(newValOfPr);
}

/**
 * change status of name in about listing
 * @author blal
 */
function aboutListNameStatus(e,name,status){

	var btn = e.target  ? e.target :  e.srcElement ;
	$(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
	if (status == 'yes')
	{
		$('#nameStatus').val(1);
	} else
	{
		$('#nameStatus').val(0);
		$(btn).parents("div.mainpage-content-right")
			 .children().removeClass("error focus succuss")
			 .children("span.help-inline").remove();
	}
}



/**
 * Function call when user click on shop search button
 * or press enter
 * @author kraj
 */
function searchByUser()
{

	var searchArt = $("#tags").val();
	if(searchArt=='' || searchArt==null)
	{
		searchArt = undefined;
	}


	var role = $("#selectRole").val();
	if(role == '' || role==null)
	{
		role = undefined;
	}

	getUserList(searchArt,0,1,'ASC',role);
}

/**
 * set status of the add to google plus rich snippet
 * @author Raman
 * @param  pass of the event,name,status
 */
function addToSearchResults(e, status){
	 var btn = e.target  ? e.target :  e.srcElement ;
	 $(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
	 if(status=='on'){
		 $('#addtosearch').attr('checked','cheked');
	 }else{
		 $('#addtosearch').removeAttr('checked');
	 }
}
