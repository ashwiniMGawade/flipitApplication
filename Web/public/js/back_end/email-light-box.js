/**
 * validRules oject contain all the messages that are visible when an elment
 * val;ue is valid
 * structure to define a message for element key is to be element name Value is
 * message
 */
var validRules = {
		
		title : __("Title looks great"),
} ;

/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 * structure to define a message for element key is to be element name Value is
 * message
 */
var focusRules = {
		
		title : __("Enter title"),
} ;

var CKcontent = false ;

$(document).ready(function(){
	
	$(":input").attr("autocomplete","off");
	
	// function call to validate form
	 validateLightBox();
	 
	 $("form#emailLightBoxForm").submit(function(){
		  if($("form#emailLightBoxForm").valid()){
			  
			 $('#updateEmailBox').attr('disabled' ,"disabled");
			 return true;
		  }else{
			  return false;
		  }
	});
	  
	// setup ckeditor and its configurtion
	CKEDITOR.replace( 'content',
			{
				//fullPage : true,
				////extraPlugins : 'wordcount',
				customConfig : 'config.js' ,  
				toolbar :  'BasicToolbar'  ,
				width : "725" ,
				height : "351"
			});
	
});

var validator = null;
/**
 * check form validation
 * @author blal
 */
function validateLightBox()
{
	validator  = $('form#emailLightBoxForm').validate({
		
		errorClass : 'error',
		validClass : 'success',
		errorElement : 'span',
		ignore : false,
		errorPlacement : function(error,element) {
				element.parent("div").prev("div").html(error);
    },
	rules: {
		title: {
	        required: true,
	        minlength : 2
	         },
	       },
	   messages:{ 
	    title: { 
	         required: __("Please enter title"),
	         minlength : __("Please enter at least 2 characters")
	        } ,
	       },
	 onfocusin: function(element){
		if (!$(element).parent('div').hasClass('success')) {
			this.showLabel(element,focusRules[element.name]);
			$(element).parent('div')
			.removeClass(this.settings.errorClass)
			.removeClass(this.settings.validClass)
			.prev("div").addClass('focus').removeClass(this.settings.errorClass)
						.removeClass(this.settings.validClass);
	   }
	 } ,
	highlight: function(element,errorClass, validClass) {
		if (element.type != 'textarea') {
			$(element)
				.parent('div')
				.removeClass(validClass)
				.addClass(errorClass)
				.prev("div")
					.removeClass(validClass)
					.addClass(errorClass);
			} 
    },
    unhighlight: function(element,
			errorClass, validClass) {
		$(element).parent('div')
		.removeClass(errorClass)
		.addClass(validClass).prev("div").addClass(validClass)
			.removeClass(errorClass);
		$('span.help-inline',$(element).parent('div')
				.prev('div')).text(
		validRules[element.name]);
		},
    });

}

$.validator.setDefaults({
	onkeyup : false,
	onfocusout : function(element) {
		$(element).valid();
	}

});

/**
 * change the status of email lightbox
 * @param e event from which it is called
 * @author blal
 */
function changeStatus(e,name,status)
{
    var btn = e.target  ? e.target :  e.srcElement ;
	$(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
	if (status == 'on')
	{    
		$('#status').val(1); 
	} else
	{    
		$('#status').val(0); 
		$(btn).parents("div.mainpage-content-right")
			 .children().removeClass("error focus succuss")
			 .children("span.help-inline").remove();
	}
	
}

/**
 * reset edited values of email lightbox
 * @author blal
 */
function cancel()
{
	window.location.href = HOST_PATH + "admin/emaillightbox";
}



