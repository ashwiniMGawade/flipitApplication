
var validRules = {
	password   : __("Password = OK"),
	confirmPassword : __("ConfirmPassword = OK"),
	firstName : __("Firstname = OK"),
	lastName : __("Lastname = OK"),
	gender : __("Gender = OK")
	//birthDay : __("Day = OK"),
	//birthMonth : __("Month = OK"),
	//birthYear : __("Year = OK"),
	//postCode : __("PostCode = OK")	
};

$(document).ready(function(){
	validatestep3();
});

function validatestep3()
{
	validator  = $("form#stepThreeForm")
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
						$('div.validationPlacement').html(error);
					}else if($(element).hasClass('gender')){
						$('div.validationPlacementGender').html(error);
					}
					else{
						$(element).after(error);
					}
				},
				rules : {
					firstName : {
						required : true
					},
					lastName : {
						required : true
					},
					gender : {
						required : true
					},
					/*birthDay : {
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
					},*/
					password : {
						required : true
					},
					confirmPassword : {
						required : true,
						equalTo : '#password'
					}
				},
				messages : {
				  firstName : {
						required : __("Voer uw voornaam in")
				  },
				  lastName : {
					    required : __("Voer uw achternaam in")
				  },
				  gender : {
					    required : __("Selecteer uw geslacht")
				  },
				  /* birthDay : {
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
						//regex : __("Voer uw juiste postcode in 1234AB")
				  },*/
				  password : {
					    required : __("Voer uw wachtwoord in")
				  },
				  confirmPassword : {
					    required : __("Voer hetzelfde wachtwoord in"),
						equalTo : __("Uw wachtwoorden komen niet overeen")
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
