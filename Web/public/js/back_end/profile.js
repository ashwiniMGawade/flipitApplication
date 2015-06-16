/**
 * profile.js 1.0
 * @author spsingh
 */





/**
 * validRules oject contain all the messages that are visible when an elment
 * val;ue is valid
 * 
 * structure to define a message for element key is to be element name Value is
 * message
 */
var validRules = {

	firstName : __("First name looks great"),
	email : __("We will email you a confirmation"),
	lastName : __("Last name looks great"),
	confirmNewPassword : __("Password matched") ,
	newPassword : __("ok!good password"),
	oldPassword : __("Old password matched!"),
	google : __("Google+ URL looks great"),
	twitter : __("Twitter URL looks great"),
	pintrest : __("Pinterest URL looks great"),
	likes : __("Likes looks great"),
	dislike : __("Dislikes looks great"),
	maintext : __("Main text looks great"),
	popularKortingscode : __("Populaire kortingscodes looks great")
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
	oldPassword : __("Enter old password to update password"),
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


/**
 * locale holds all errors message for avtar uploading
 * @author spsingh
 */
var locale = {
	    "fileupload": {
	        "errors": {
	            "maxFileSize": __("Maximum image size is 2MB"),
	            "minFileSize": __("File is too small"),
	            "acceptFileTypes": __("Please upload only *.jpg, *.gif, *.png files"),
	            "maxNumberOfFiles": __("Max number of files exceeded"),
	            "uploadedBytes": __("Uploaded bytes exceed file size"),
	            "emptyResult": __("Empty file upload result")
	        }
	    }
};



$(document).ready(init);



// used to buffer profile image
__pImg = new Image();
__pImg.src =  HOST_PATH_PUBLIC + "/images/back_end/user-avtar.jpg";



/**
 * initialize all the settings after document is ready
 * @author spsingh
 */

function init()
{
	
	$("input[type=password" , "form#userProfile").addClass("passwordField");
	
	
	$('.cancelButton').on('click', function(evt) {
		
		
		window.location.href = HOST_PATH + "admin/user/profile" ;
		
    });


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
	
	// validate user profile data
	validateUserProfile() ;
	
 }



/**
 * update user profile
 * @author spsingh
 * @param e event
 */



// global validation object  
var validator = null; 

/**
 * form validation during update user 
 * @author spsingh
 */

function validateUserProfile()
{
	validator  = $("form#userProfile")
	.validate(
			{
				errorClass : 'error',
				validClass : 'success',
				errorElement : 'span',
				afterReset  : resetBorders,
				errorPlacement : function(error, element) {
					element.parent("div").prev("div")
							.html(error);
				},
				rules : {
					firstName : {
						required : true,
						minlength : 2,
						regex : /^[a-zA-Z\-]+$/
						//regex  :	 /^[a-zA-Z]+[\s]{1}[a-zA-Z]$|^[a-zA-Z]+[\.]{1}[\s]{1}[a-zA-Z]$/g
					},
					lastName : {
						required : true,
						minlength : 2,
						regex : /^[a-zA-Z\-]+$/
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
					newPassword : {
						required : function(element)
						{
							// if condition true and apply required validation for element
							
							if($("input#oldPassword" , "form#userProfile").val().length > 0)
							{
								return true ;
								
							} else if ($("input#confirmNewPassword" , "form#userProfile").val().length > 0)
							{
								return true ;
									
							} else {
								
								
								//	Hide error message for new password feild and confirm password feild  
								
								
								$('span.help-inline' , $("[name=newPassword]")
										.parents('div.mainpage-content-right') ).hide();
						
								$('span.help-inline' , $("[name=confirmNewPassword]")
										.parents('div.mainpage-content-right') ).hide();
								
								
								$("input#newPassword" , "form#userProfile")
								.parents("div.mainpage-content-line")
								.find(".error,.success")
								.removeClass("error success");
								
								$("input#confirmNewPassword" , "form#userProfile")
									.parents("div.mainpage-content-line")
									.find(".error,.success")
									.removeClass("error success");
								
								
								// check if element not filled , then hide validation message for old password feild
								 if ( $(element).val().length == 0 ) 
								 {
										$('span.help-inline' , $("[name=oldPassword]")
											.parents('div.mainpage-content-right') ).hide();
		
										$("input#oldPassword" , "form#userProfile")
										.parents("div.mainpage-content-line")
										.find(".error,.success")
										.removeClass("error success");
								}
										


								return false ;
							}
						},
						minlength : 8,
						maxlength : 20
					},
					popularKortingscode :{
						required : true,
						number : true
						
					},
					confirmNewPassword : {
						required: function(element)
						{
							// if condition true and apply required validation for element
							
							if($("input#oldPassword" , "form#userProfile").val().length > 0)
							{
								return true ;
								
							} else if ($("input#newPassword" , "form#userProfile").val().length > 0)
							{
								return true ;
							} else {
								//	hide error message for confirm password  feild
								
								$('span.help-inline' , $("[name=confirmNewPassword]")
										.parents('div.mainpage-content-right') ).hide();
								
								$("input#confirmNewPassword" , "form#userProfile")
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
										
										
										$("input#newPassword" , "form#userProfile")
										.parents("div.mainpage-content-line")
										.find(".error,.success")
										.removeClass("error success");
										
										
										 $("input#oldPassword" , "form#userProfile")
										 .parents("div.mainpage-content-line")
										 .find(".error,.success")
										 .removeClass("error success");
								}
								 
								
								


								return false ;
								
								
							}
						},
						equalTo :	"#newPassword",
						minlength : 8,
						maxlength : 20
					},
					oldPassword : {
						required: function(element)
						{
							// if condition true and apply required validation for element
							
							if($("input#newPassword" , "form#userProfile").val().length > 0)
							{
								return true ;
								
							} else if ($("input#confirmNewPassword" , "form#userProfile").val().length > 0)
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


								$("input#oldPassword" , "form#userProfile")
								.parents("div.mainpage-content-line")
								.find(".error,.success")
								.removeClass("error success");
								
								
								$("input#newPassword" , "form#userProfile")
									.parents("div.mainpage-content-line")
									.find(".error,.success")
									.removeClass("error success");
								
								$("input#confirmNewPassword" , "form#userProfile")
									.parents("div.mainpage-content-line")
									.find(".error,.success")
									.removeClass("error success");
								
								return false ;
							}
						},
						minlength : 8,
						maxlength : 20,
						remote : {
							url: HOST_PATH + "admin/user/validatepassword",
				        	type: "post" ,
				        	data : { id : $("input#id" , "form#userProfile").val()  } ,
				        	async: false,
				        	dataType : "JSON",
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
					firstName : {
						required : __("Please enter your first name"),
						regex : __("First Name should be Alphabets") ,
						minlength : __("Please enter minimum 2 characters"),

					},
					lastName : {
						required : __("Please enter your last name"),
						regex : __("Last Name should be Alphabets"),
						minlength : __("Please enter minimum 2 characters"),
					},
					oldPassword : {
						minlength : __("It should be minimum 8 characters"),
						remote : __("Old password don't matched"),
						maxLength : __("Please enter maximum 20 characters")
					},
					newPassword : {
						required : __("Voer je nieuwe wachtwoord in"),
						minlength : __("Please enter minimum 8 characters"),
						maxLength : __("Please enter maximum 20 characters")
					},
					confirmNewPassword : {
						equalTo : __("Please enter the same value again"),
						minlength : __("Please enter minimum 8 characters"),
						required : __("Herhaal je nieuwe wachtwoord"),
						maxLength : __("Please enter maximum 20 characters")
							
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
 * reset the validation border of the input filed
 * @param el
 * @author spsingh
 */

function resetBorders(el)
{
	$(el).each(function(i,o){
		
		$(o).parent('div')
		.removeClass("error success")
		.prev("div").removeClass('focus error success') ;
	
	});
	
 }

$.validator.setDefaults({
	onkeyup : false,
	onfocusout : function(element) {
		$(element).valid();
	}

 });
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
				bootbox.alert(__('Please select a store'));
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
 * @author kraj
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