

//var CKEDITOR_BASEPATH = HOST_PATH+"public/js/back_end/ckeditor/";

/**
 * widgetList.js 1.0
 * @author mkaur
 */
var validRules = {
	title : __("Title looks great."),
	content : __("ok !")
};
var focusRules = {

	title : __("Enter title"),
	content : __("Enter Widget content")

};
var CKcontent = false ;
$(document).ready(function(){
	validateFormAddNewWidget();
	  $("form").bind("keypress", function(e) {
          if (e.keyCode == 13) {
              return false;
         }
    });
	  $("form#createWidget").submit(function(){
		  if($("form#createWidget").valid()){
				$('button#widgetSubmit').attr('disabled' ,"disabled");
				return true;
			}else {
				return false;
			}
	  });
	
	// setup ckeditor and its configurtion
	CKEDITOR.replace('content',
			{
				//fullPage : true,
				////extraPlugins : 'wordcount',
				customConfig : 'config.js' ,  
			    toolbar :  'BasicToolbar'  ,
				height : "400"
				
			});
});	
	
/**
 * form validation used for both edit and create widget.
 */	
	
function validateFormAddNewWidget(){
	validateNewWidget  = $("form#createWidget")
	.validate({	
	errorClass : 'error',
	validClass : 'success',
	errorElement : 'span',
	ignore : false,
	errorPlacement : function(error,element) {
			element.parent("div").prev("div").html(error);
				
	},
	rules : {
		title : {required : true,
				minlength : 2
		},
		},
	messages : {
		title : {
			required : __("Please enter title"),
			minlength : __("Please enter atleast 2 characters")
	},
	
	},
	onfocusin : function(element) {
		if (!$(element).parent('div').hasClass('success')) {
			this.showLabel(element,focusRules[element.name]);
			$(element).parent('div')
			.removeClass(this.settings.errorClass)
			.removeClass(this.settings.validClass)
			.prev("div").addClass('focus').removeClass(this.settings.errorClass)
						.removeClass(this.settings.validClass);
	}
},

highlight : function(element,errorClass, validClass) {
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
	unhighlight : function(element,
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
/**
 * Permanent delete confirmation using bootstrap bootbox
 * @author mkaur 
 */
function callToPermanentDelete(){
		bootbox.confirm(__("Are you sure you want to delete this widget permanently?"),__('No'),__('Yes'),function(r){
			if(!r){
				return false;
			}
			else{
				window.location.href = $('input#editedWidgetId').val()+"/delete/delete";
			}
			
		});
}
	  		