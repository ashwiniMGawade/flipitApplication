		
var favShop=null;

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
	gender: __("Gender looks great"),
	postalCode: __("Postal code looks great"),
	password : __("ok ! good password"),
	confPassword : __("ok ! password match"),
	confirmNewPassword : __("Password matched"),
	newPassword : __("ok ! good password"),
	oldPassword : __("Old password matched!")
	
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
	lastName : __("Enter your last name"),
	gender: __("Select gender"),
	//postalCode: __("Enter postal code"),
	password : __("Choose password!"),
	confPassword : __("Re-type password"),
	oldPassword : __("Enter old password to update password"),
	newPassword : __("Choose new password"),
	confirmNewPassword : __("Re-type new password")
   

};


$(document).ready(function(){
	$('input#newKeyword').val('');
	getFavShop();
	validateFormEditUser() ;
});


/**
 * Get favorite shops according to id of Visitor 
 * @author mkaur
 */
function getFavShop() {
	// check load event called in edit mode or not
	var hashString = window.location.hash;
	hashString = hashString.substr(1);
	var isEdit = false  ;
	var regex =  /(^.+)-([\d]+$)/ ;
	var result = regex.exec(hashString);
	isEdit  =  result && result[2] ? true : false ;
	if (favShop == null) {
		//$('div.multiselect').remove();
			var visitorid = $('input#visitorId').val();
				$.ajax({
					type : "POST",
					url : HOST_PATH + "admin/visitor/getfavoriteshop/id/" + visitorid,
					dataType : 'json',
					success : function(data) {
						
						if (data!= null) {
							favShop = data;
							if(favShop==null || favShop==''){
								$('button#delFavShop').parent('div').parent('div').remove();	
							}
							else{
								$('button#delFavShop').parent('div').parent('div').show();
							}
							var op = "<ul>";
							$('div.multiselect').empty();
							
							for (i in favShop) {
								op += "<li class= list_"+ favShop[i].id + "><input type='checkbox' style='display:none' name='shops[]"
										+ "' value='"
										+ favShop[i].id
										+ "' /> &nbsp"
										+ favShop[i].name + "</li>";
							}
							op += "</ul>";
							$('div.multiselect').append(op);
							$('div.multiselect ul li').click(
									selectShopInList);
							
							if(! isEdit )
							{
								//removeOverLay();
							}
							
							if($('#favorite')){
								highlightExistingShop($('#favorite').val());	
							}
						}
					}
				});
		// get from website access
	} else {
		var op = "<ul>";
		if (favShop != null) {
			$('div.multiselect').empty();
		for (i in favShop) {
			op += "<li class= list_"+ i + "><input  type='checkbox' style='display:none' name='shops[]"
						+ "' value='"
						+ i
						+ "' /> &nbsp"
						+ favShop[i] + "</li>";
			}
			op += "</ul>";
			$('div.multiselect').append(op);
			$('div.multiselect ul li').click(selectShopInList);
		}
	}
}
/**
 * Multiselect list favorite shops for visitors. 
 * @author mkaur
 */
function selectShopInList() {

	if (($(this).children('input')).is(':checked')) {

		$(this).children('input').removeAttr('checked');
		$(this).removeClass('selected');

	} else {

		$(this).children('input').attr('checked', 'checked');
		$(this).addClass('selected');
	}
}

/**
 * set website in website select 
 * @param websites
 * @author mkaur
 */

function highlightExistingShop(websites)
{
	if(websites){
		websites = websites.split(',');
		
		for(i in websites)
		{
			$("li.list_" + websites[i]).addClass('selected').children('input').attr('checked' , 'checked');
		}
   }	
}
/**
 * delete favorite shops by using deletefavoriteshopAction on controller 
 * @author mkaur
 */
function deleteFavShop(){
	//var vc = $('#editRegister').serialize();
	var val=$("input[name^=shops]").val();
	

	addOverLay();
	$.ajax({
		type : "POST",
		url : HOST_PATH + "admin/visitor/deletefavoriteshop",
		method : "post",
		data: $('#editRegister').serialize(),
		dataType : 'json',
		success : function(data) {
			var size = $('div.multiselect ul li').size();
			var dataLength = data.length;
			if(size==dataLength){
				$('button#delFavShop').parent('div').parent('div').remove();
			}
			if(data=='' && data==undefined && data==null) {
				  alert(__('Problem in your selection'));
				} else {
					
					for(var i in data)
					{	//console.log(data[i]);
						$('div.multiselect ul li.list_'+data[i]).remove();
						//alert('Hello');
					}
				}
			//$('div.multiselect ul li.list_'+data);
			//getFavShop();
			removeOverLay();
			
		}
	});
}


/**
 * Changes the status of Online, weekly,fashion newsletter and code-alert buttons
 * @param  pass of the event,name,status
 * @author mkaur
 */

function setOnOff(e, name ,status){
	
	var btn = e.target  ? e.target :  e.srcElement ;
	 switch(name){
			case "onlineStatus" :
				 $(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
				 if(status=='on'){
					 $('#status').val(1); 
				 }else{
					 $('#status').val(0); 
					 $(btn).parents("div.mainpage-content-right")
					 .children().removeClass("error focus succuss")
					 .children("span.help-inline").remove();
				 }
				
			break;
			case "weekly" :
				 $(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
				 if(status=='on'){
					 $('#weekly').val(1); 
				 }else{
					 $('#weekly').val(0); 
					 }
			break;
			case "fashion" :
				 $(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
                if(status=='on'){
               	 $('#fashion').val(1);
				 }else{
					 $('#fashion').val(0);
				 }
            break;
			case "travel" :
				 $(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
                if(status=='on'){
               	 $('#travel').val(1);
				 }else{
					 $('#travel').val(0);
				 }
				
			break;
			case "active" :
				 $(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
				 if(status=='on'){
					 $('#active').val(1); 
				 }else{
					 $('#active').val(0); 
					 }
			break;
			default:
				$(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
				    if(status=='on'){
			    	$('#code').val(1);
			    }else{
			    	$('#code').val(0);
		    	}
			
		}
	
}

//global validation object  
var validator; 

/**
 * form validation during update Visitor 
 * @author mkaur
 */
function validateFormEditUser(){
	validator  = $("form#editRegister")
	.validate(
			{
				errorClass : 'error',
				validClass : 'success',
				errorElement : 'span',
				afterReset  : resetBorders ,
				ignore: '.ignore3' ,
				errorPlacement : function(error, element) {
					element.parent("div").prev("div")
							.html(error);
				},
				rules : {
					firstName : {
						required : true,
						/*regex : /^[a-zA-Z\-]+$/*/
						minlength : 1,
						maxlength :20
					},
					lastName : {
						required : true,
						/*regex : /^[a-zA-Z\-]+$/*/
						minlength : 1,
						maxlength :20
					},
					gender:{
						required :true,
						number: true
						
					},
					email:{
						required :true,
						email: true
						
					},
					/*postalCode:{
						required: true, 
						//,
						minlength : 3,
						maxlength:10,
						regex: /^[0-9a-zA-Z\s-]+$/
				    },*/
					newPassword : {
						required : function(element)
						{
							 if ($("input#confirmNewPassword" , "form#editRegister").val().length > 0)
							{
								return true ;
									
							} else {
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
							 if ($("input#newPassword" , "form#editRegister").val().length > 0)
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
										/*$('span.help-inline' , $("[name=oldPassword]")
												.parents('div.mainpage-content-right') ).hide();*/
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
					
				},
				messages : {
					email : {
						required : __("Please enter your email address"),
						email : __("Please enter valid email address")
					},
					firstName : {
						required : __("Please enter your first name"),
						minlength : __("Please enter your first name between 1 to 20 chanracter"),
						maxnlength : __("Please enter your first name between 1 to 20 chanracter")
						/*regex : __("First Name should be Alphabets")*/

					},
					lastName : {
						required : __("Please enter your last name"),
						minlength : __("Please enter your last name between 1 to 20 chanracter"),
						maxnlength : __("Please enter your last name between 1 to 20 chanracter")
						/*regex : __("Last Name should be Alphabets")*/
					},
					gender: {
						required : __("Please select gender"),
						number : __("Please select gender")
					},
					/*postalCode : {
						required : __("Please enter postal code"),
						minlength : __("It should be minimum 3 characters"),
						maxlength : __("Please enter maximum 10  characters"),
						regex: __("Enter alpha numeric chracter")
							
					},*/
					newPassword : {
						required : __("Voer je nieuwe wachtwoord in"),
						minlength : __("Please enter minimum 8 characters"),
						maxlength : __("Please enter maximum 20  characters")
					},
					confirmNewPassword : {
						equalTo : __("Please enter same as new password"),
						minlength : __("Please enter minimum 8 characters"),
						required : __("Herhaal je nieuwe wachtwoord"),
						maxlength : __("Please enter maximum 20  characters")
							
					}

				},

				onfocusin : function(element) {
					
					// display hint messages when an element got focus 
					if (!$(element).parent('div').prev("div")
							.hasClass('success')) {
						
			    		 var label = this.errorsFor(element);
			    		 if( $(label).attr('hasError')  )
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
			    	    	
			    	     /*if(element.value!==''){
			    	    	 this.showLabel(element, focusRules[element.name]);
			    	    		 $(element).parent('div')
								 .removeClass("error success")
			    	    			.prev("div").removeClass('focus error success') ;
			    	    		 $('span.help-inline', $(element).parent('div')
											.prev('div')).text(
								 validRules[element.name] ).hide();
			    	    	 }
			    	    	 else{*/
			    	    	 
			    	    	 if(!$(element).hasClass('new-keywords'))
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
			    	    	// }
		    	    	 }
					}
				},
			highlight : function(element,errorClass, validClass) {
					// highlight borders in case of error  
					$(element).parent('div')
					.removeClass(validClass)
					.addClass(errorClass).prev("div")
					.removeClass(validClass)
					.addClass(errorClass);
					$('span.help-inline', $(element).parent('div')
							.prev('div')).removeClass(validClass) ;
				
			},
			unhighlight : function(element,
					errorClass, validClass) {
					// check to display errors for ignored elements or not 
					var showError = false ;
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
					if(! showError ){
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
								.removeClass(validClass);
							    
					} else
					{
						if(element.type !== "file"){
							
							if(! $(element).hasClass('ignore3'))
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
							}
						} else{
							$(element).parent('div')
							.removeClass(errorClass)
							.removeClass(validClass)
							
							.prev("div")
							.removeClass(errorClass)
							.removeClass(validClass) ;
						}
					}
				 
			},
				
				});		}

/**
 * Disable  validation event on keyup and trigger on blur
 * @author mkaur
 */
$.validator.setDefaults({
	onkeyup : false,
	onfocusout : function(element) {
		$(element).valid();
	}

});
/**
 * reset the validation border of imput field
 * @param el
 * @author mkaur
 */
function resetBorders(el){
	$(el).each(function(i,o){
		
		$(o).parent('div')
		.removeClass("error").removeClass('success')
		.prev("div").removeClass('focus').removeClass('error').removeClass('success') ;
	
	});
	

}
/**
 * delete Visitor from database by id id get from hidden field
 * @author mkaur
 */
function deleteVisitor(id) {
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
 * @author mkaur
 * @param id
 */
function moverToTrash() {
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/visitor/permanentdelete",
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
				document.location.href = HOST_PATH + "admin/visitor/" ;
				
			} else {
				bootbox.alert(__("<span class='success'>Problem in your data</span>"));
				//jAlert('Problem in your data');
			}
		}
	});
}



/**
 * addKeyword
 * 
 * add custom keywords 
 * 
 * @author Surinderpal Singh
 */
function addKeyword()
{
	if($('input#newKeyword').val()==''){
			
		bootbox.alert(__('Please enter a keyword'));
		return false;
	} else {
		
		checkDuplicateKeyword();
	}
	removeOverLay();

 }

/**
 * checkDuplicateKeyword
 * 
 * check for duplicate keywords and also add ketyword into the list 
 * 
 * @author Surinderpal Singh
 */
function checkDuplicateKeyword()
{
	var newKeryword = $('input#newKeyword').val();
	
	
	if($( 'li[type="'+ newKeryword +'"]' , 'ul#visitorKeyword-list').length > 0 )
	{
		bootbox.alert(__('You have already added this keyword'));
		return ;
	}
	
	   var path = HOST_PATH+'public/images/'+'kc-search-cross.png';
	   
	   
	   var inputEl= '<input type="hidden"  name="visitorKeywords[]" value="'+ $('input#newKeyword').val() +'">' ;
	   
	   
		var li = '<li class="vistor-keyword-item" type="'+ $('input#newKeyword').val() +'">';
		li+= $('input#newKeyword').val() + inputEl +  '<a herf="javascript:void(0);"  onClick="deleteKeyword(this)">';
		li+='<img src="' + path + '" title="Keyword"/></a>&nbsp;</li>';
		$('ul#visitorKeyword-list').append(li);
		
		$('input#newKeyword').val('');
}

/**
 * deleteKeyword
 * 
 * remove keyword
 * 
 * @author Surinderpal Singh
 */
function deleteKeyword(obj)
{
	$(obj).parent('li.vistor-keyword-item').remove();
}