/*var validRules = {
	emailAddress : __("E-mail ziet er geweldig uit"),
	password : __("ok! goed wachtwoord"),
	confirmPassword : __("ok! wachtwoord wedstrijd"),
	firstName : __("voornaam ziet er geweldig uit"),
	lastName : __("achternaam ziet er geweldig uit"),
	gender : __("geslacht ziet er geweldig uit"),
	birthDay : __("dag ziet er geweldig uit"),
	birthMonth : __("maand ziet er geweldig uit"),
	birthYear : __("jaar ziet er geweldig uit"),
	postCode : __("postcode er geweldig uit")
	
};*/
/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 * structure to define a message for element key is to be element name Value is
 * message
 */
var focusRules = {
	emailAddress : __(""),
	password   : __(""),
	confirmPassword : __(""),
	firstName : __(""),
	lastName : __(""),
	gender : __("")
	/*birthDay : __(""),
	birthMonth : __(""),
	birthYear : __(""),
	postCode : __("")*/
};

var flag = false;
$(document).ready(init);
/**
 * initialize all the settings after document is ready
 * @author sunny patial
 */
function init()
{
	validateprofile();
	$("a#profileUpdateBtn").click(function(){
		if($('form#profileForm').valid()){
			if(flag == false){
				updateprofile();
				flag = true;
			}
		}
	});
	$("input").keypress(function(event) {
	    if (event.which == 13) {
	    	if($("form#profileForm").valid()){
	    		
	    		if(flag == false){
					updateprofile();
					flag = true;
				}
	    		
	    	}else{
	    		
	    		 return false;
	    	}
	        
	    }
	});
	
	
}
/**
 * update user profile
 * @author sunny patial
 * @param e event
 */
// global validation object  
var validator = null; 
/**
 * form validation during update user 
 * @author sunny patial
 */
function validateprofile()
{
	
	validator  = $("form#profileForm")
	.validate(
			{
				errorClass : 'input-error-full-new',
				validClass : 'input-success-full-new',
				errorElement : 'label',
				errorPlacement : function(error, element) {
					if($(element).next('label').attr('generated')){
						$(element).next('label').remove();
					}
					if($(element).hasClass('comboBox')){
						$('span.validationPlacement').html(error);
					}else{
						$(element).after(error);
					}
				},
				rules : {
					firstName : {
						required : true,
						minlength : 1,
						maxlength :20
					},
					lastName : {
						required : true,
						minlength : 1,
						maxlength :200
					},
					emailAddress : {
						required : true,
						email : true,
						remote : {
							url : HOST_PATH_LOCALE
									+ "freesignup/checkuser",
							type : "post",
							data : {'id' : $("input#userId").val()},
							beforeSend : function(xhr) {
								$('label[for=emailAddress]')
										.html(__('Valideren...')).addClass('validating');
							},

							complete : function(data) {
								$('label[for=emailAddress]')
										.removeClass(
												'validating');
								if (data.responseText == 'true') {
									
									$('label[for=emailAddress]').removeClass('input-error-full-new help-inline')
																.addClass('input-success-full-new');
									$("input#emailAddress").removeClass('input-error-text-field')
									  .addClass('input-success-text-field input-success-full-new');
											
								} else {
                                  
									$('label[for=emailAddress]').removeClass('input-success-full-new')
																.addClass('input-error-full-new help-inline')
																.html(__('Geldig E-mail'));
									
									$("input#emailAddress").removeClass('input-success-text-field input-success-full-new')
									  		  .addClass('input-error-text-field');
								}

							}
						}
						
					},
					gender : {
						required : true
					}/*,
					birthDay : {
						required : true
					},
					birthMonth : {
						required : true
					},
					birthYear : {
						required : true
					},
					postCode : {
						required : true,
						//regex : /^[1-9][0-9]{3}\s?[a-zA-Z]{2}$/
					}*/
				},
				messages : {
				  firstName : {
						required : __("Voer uw voornaam in"),
						minlength: __("Vul minimaal 3 karakters"),
						maxlength : __("Vul maximaal 20 karakters")
				  },
				  lastName : {
					    required : __("Voer uw achternaam in"),
						minlength : __("Vul minimaal 2 tekens"),
						maxlength : __("Vul maximaal 200 tekens")
				  },
				  gender : {
					    required : __("Selecteer uw geslacht")
				  }/*,
				  birthDay : {
						required : __("Selecteer uw geboorte dag")
				  },
				  birthMonth : {
						required : __("Selecteer uw geboorte maand")
				  },
				  birthYear : {
						required : __("Selecteer uw geboorte jaar")
				  },
				  postCode : {
						required : __("Voer uw postcode in"),
						//regex : __("Voer uw juiste postcode in")
				  }*/
				},

				onfocusin : function(element) {
					
					if($(element).attr('type') == 'text'){
					
						var label = this.errorsFor(element);
						
						 if( $( label).attr('hasError')  )
				    	 {
							 
			    			 if($( label).attr('remote-validated') != "true")
			    			 	{
			    				 
									$(element).removeClass('input-error-text-field input-success-text-field input-error-full-new input-success-full-new');
									$('label[for='+$(element).attr('name')+']').removeClass('input-error-full-new input-success-full-new help-inline')
											.html(focusRules[$(element).attr('name')]);
							 	}
			    			 
			    	     } else {
			    	    	
							$(element).removeClass('input-error-text-field input-success-text-field input-error-full-new input-success-full-new');
							$('label[for='+$(element).attr('name')+']').removeClass('input-error-full-new input-success-full-new help-inline')
									.html(focusRules[$(element).attr('name')]);
							
					     }
					}
					else{
						$(element).removeClass('input-error-text-field input-success-text-field input-error-full-new input-success-full-new');
						$('label[for='+$(element).attr('name')+']').removeClass('input-error-full-new input-success-full-new help-inline')
								.html(focusRules[$(element).attr('name')]);
					}
			},

			highlight : function(element,
					errorClass, validClass) {
					 $(element).removeClass('input-success-text-field')
                     .addClass('input-error-text-field input-error-full-new');
			},
			success: function(label , element) {
					$(element).removeClass('input-error-text-field')
							  .addClass('input-success-text-field input-success-full-new');
					$(label).removeClass('input-error-full-new help-inline')
							.html(validRules[element.name]).addClass('input-success-full-new');
			}

			});

}

function updateprofile(){
	var data = $('form#profileForm').serialize();
	___addOverLay();
	$.ajax({
		url : HOST_PATH_LOCALE + "login/updateuserdata",
		type : 'post',
		data : data,
		success : function(obj){
			flag = false;
			$(window).scrollTop(0);
			obj = $.parseJSON(obj);

			// update current newsletter susbcription status
			$("input#currentSubscriptionStatus").val(obj.newStatus);

			// display appropridate update message	
			switch(obj.updateType) {

				case 'subsribed' :
					$("div.newsletter-unsubscribed-cont").hide();
				break;

				case 'unsubscibed':
					$("div.newsletter-unsubscribed-cont").show();
				break;

				default: 
					// do nothing
				break;
			}

			flashMessage(obj.message);
			___removeOverLay();
		}
		
	});
}
function flashMessage(msg)
{
	$("div#messageDiv").fadeIn()
					   .find('strong')
					   .html(msg);
}
