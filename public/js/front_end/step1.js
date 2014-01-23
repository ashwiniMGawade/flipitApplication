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
	emailAddress : __("E-mail = OK")
	
};
/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 * structure to define a message for element key is to be element name Value is
 * message
 */

$(document).ready(init);
/**
 * initialize all the settings after document is ready
 * @author cbhopal
 */
function init()
{
	// validate account setting form data
	
	validatestep1();
	
	
	
		$("form#stepOneForm").submit(function(){
			
			$("form#stepOneForm").valid();
			});
		
	
}

var submitBttn = false ;
var validator = null; 
/**
 * form validation during update user 
 * @author sunny patial
 */
function validatestep1()
{
	validator  = $("form#stepOneForm")
	.validate(
			{
				errorClass : 'input-error-full-new',
				validClass : 'input-success-full-new',
				errorElement : 'label',
			    onfocusout: false,
				errorPlacement : function(error, element) {
					if($(element).next('div.errorAppend').children('label').attr('generated')){
						$(element).next('div.errorAppend').children('label').remove();
					}
					$(element).next('div.errorAppend').html(error);
				},
				rules : {
					emailAddress : {
						required : true,
						minlength : 6,
						email : true,
						remote : {
							url : HOST_PATH_LOCALE + "freesignup/checkuser",
							type : "post",
							beforeSend : function(xhr) {
								
								var overlay = $("<div id='againSet'><img src='/public/images/ajax-loader2.gif' id='img-load'/></div>");
								
								$(overlay).css('height' , '81px');
								
								$("img#img-load" , overlay).css('top' , '33%'); 
								submitBttn = $('div#changeWhenStep1').clone();
								$('div#changeWhenStep1').replaceWith(overlay);
							},

							complete : function(data) {
								
								if (data.responseText == 'true') {
									
									$('label[for=emailAddress]').removeClass('input-error-full-new help-inline')
																.addClass('input-success-full-new');
																
									$("input#emailAddress").removeClass('input-error-text-field')
									  .addClass('input-success-text-field input-success-full-new');
											
								} else {
                                  
									$("div#againSet").replaceWith(submitBttn);
									$('label[for=emailAddress]').removeClass('input-success-full-new')
																.addClass('input-error-full-new help-inline');
									
									$("input#emailAddress").removeClass('input-success-text-field input-success-full-new')
									  		  .addClass('input-error-text-field');
								}

							}
						}
					}
				},
				messages : {
				  emailAddress : {
						required : __("Voer uw e-mailadres in"),
						email : __("Voer een geldig e-mailadres"),
						minlength: __("Vul minimaal 6 karakters"),
						remote : __("Dit e-mailadres is al in gebruik")
				  }
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
												.html('');
								 	}
				    			 
				    	     } else {
				    	    	
								$(element).removeClass('input-error-text-field input-success-text-field input-error-full-new input-success-full-new');
								$('label[for='+$(element).attr('name')+']').removeClass('input-error-full-new input-success-full-new help-inline')
										.html('');
								
						     }
						}
						else{
							$(element).removeClass('input-error-text-field input-success-text-field input-error-full-new input-success-full-new');
							$('label[for='+$(element).attr('name')+']').removeClass('input-error-full-new input-success-full-new help-inline')
									.html('');
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
						
				}

			});

}