/**
 * profile.js 1.0
 * @author sunny patial
 */
/**
 * validRules oject contain all the messages that are visible when an elment
 * val;ue is valid
 * 
 * structure to define a message for element key is to be element name Value is
 * message
 */
/*var validRules = {
		
		uname : __("E-mail ziet er geweldig uit"),
		pwd : __("ok! goed wachtwoord")
};*/
/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 * structure to define a message for element key is to be element name Value is
 * message
 */
var focusRules = {
	
		uname : " ",
		pwd   : " "
};
$(document).ready(init);
$(document).ready(function() {
	
	$(".blue-btn").click(function(event) {
		if($("#login").valid()==false){
			 return false;
		}
	});
	$('input#uname').bind('keypress', function(event) {
		  if(event.keyCode === 9) {
			    $('#pwd').focus();
	            return false;
		  }
		});
	$("input").keypress(function(event) {
	    if (event.which == 13) {
	    	if($("#login").valid()==true){
	    		
	    			checkLogin();
	    		
	    	}else{
	    		
	    		 return false;
	    	}
	        
	    }
	});
});

/**
 * initialize all the settings after document is ready
 * @author sunny patial
 */
function init()
{
	validatelogin() ;
	
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
function validatelogin()
{
	validator  = $("form#login")
	.validate(
			{ 
				errorClass : 'input-error-full-new',
				validClass : 'input-success-full-new',
				txtvalidClass:'suctxtcls',
				errorElement : 'span',
				afterReset  : resetBorders,
				errorPlacement : function(error, element) {
					element.next("label")
							.html(error);
				},
				rules : {
					uname : {
						required : true,
						minlength : 6,
						email : true
					},
					pwd : {
						required : true,
					}
				},
				messages : {
					uname : {
						required : " ",
						email : __("Voer een geldig email adres in"),
						minlength: __("Voer een geldig email adres in")
					},
				  pwd : {
					    required : " ",
				  }
				},

				onfocusin : function(element) {
					if (!$(element).hasClass('input-success-full-new')) {
						 var label = this.errorsFor(element);
						 if( $( label).attr('hasError')  )
			    	     {
			    			 if($( label).attr('remote-validated') != "true")
			    			 	{
									this.showLabel(element, focusRules[element.name]);
									$(element).next("label").removeClass(this.settings.errorClass).removeClass(this.settings.validClass);
									$(element).next("label").children("span").removeClass(this.settings.errorClass).removeClass(this.settings.validClass);
									$(element).removeClass('errtxtcls').removeClass('succtxtcls');	
							 	}
			    			 
			    	     } else {
			    	    	this.showLabel(element, focusRules[element.name]);
							$(element).next("label").removeClass(this.settings.errorClass).removeClass(this.settings.validClass);
							$(element).next("label").children("span").removeClass(this.settings.errorClass).removeClass(this.settings.validClass);
							$(element).removeClass('errtxtcls').removeClass('succtxtcls');		
							
					     }
					}
					else if ($(element).hasClass('input-success-full-new')){
						this.showLabel(element, focusRules[element.name]);
						$(element).parent('div').removeClass(this.settings.errorClass).removeClass(this.settings.validClass).next("label").removeClass(this.settings.validClass).removeClass(this.settings.errorClass).children("span").removeClass(this.settings.validClass).removeClass("label-right-error");
						$(element).removeClass('errtxtcls').removeClass('succtxtcls');	
					}
				},

				highlight : function(element,
						errorClass, validClass) {
					$(element).next("label").removeClass(validClass).addClass(errorClass).children("span").removeClass(validClass).addClass("label-right-error");
					$(element).removeClass(validClass).addClass('errtxtcls').removeClass('input-success-text-field');	
				},
				unhighlight : function(element,
						errorClass, validClass) {
					if(! $(element).hasClass("passwordField"))
					{
						$(element).next("label").removeClass(errorClass).addClass(validClass).children("span").removeClass(errorClass).addClass("label-right-success");
						$(element).removeClass('errtxtcls').addClass('succtxtcls');	
						
					}
				},
				success: function(label , element) {

					$(element).removeClass('input-error-text-field')
							  .addClass('input-success-text-field input-success-full-new');
					$(label).removeClass('input-error-full-new help-inline')
							.html(validRules[element.name]).addClass('input-success-full-new');
				}

			});
}

/**
 * reset the validation border of the input filed
 * @param el
 * @author sunny patial
 */

function resetBorders(el)
{
	$(el).each(function(i,o){
		
		$(o).parent('div')
		.removeClass("error success")
		.prev("div").removeClass('focus error success') ;
	
	});
	
}

/**
 * Function for Login Check
 * @author Raman
 */

function checkLogin(e, obj) {
	
	if($("#login").valid()){
		
		var overlay = $("<div id='againSet'><a><img src='/public/images/ajax-loader2.gif' id='img-load'/></a></div>");
		$('div#changeWhenLogin').replaceWith(overlay);
		
		var username = $("#uname").val();
		var password = $("#pwd").val();
		$.ajax({
			url : "login/checklogin/usr/"+username+"/rnd/"+Math.random()*9999999,
			type : "post",
			data : {'uname' : username,'pwd': password},
			success : function(data){
				
				if(data == 'false'){
					
					$("#errorPop").show();
					var againButton = '<div id="changeWhenLogin" class="blue_btn"><a href="javascript:void(0);"  tabindex="3" onclick="checkLogin();">'+ __("aanmelden") +'</a></div>';
					$('div#againSet').replaceWith(againButton);
					
				} else {
					
					window.location.href = data;
				}
			}
		});
	}
}


