var validRules = {

		topFooter : __("Top footer looks great"),
		column1 : __("Column1 looks great"),
		column2 : __("Column2 looks great"),
		column3 : __("Column3 looks great"),
		column4 : __("Column4 looks great"),
		bottomFooter : __("Bottom footer looks great")
};

var focusRules = {

	topFooter : __("Enter top footer"),
	column1 : __("Enter column1"),
	column2 : __("Enter column2"),
	column3 : __("Enter column3"),
	column4 : __("Enter column4"),
	bottomFooter : __("Enter bottom footer")

};


var CKcontent = false ;

$(document).ready(function(){
	

	// setup ckeditor and its configurtion
	CKEDITOR.replace( 'topFooter',
			{
				//fullPage : true,
				////extraPlugins : 'wordcount',
				customConfig : 'config.js' ,  
				toolbar :  'BasicToolbar'  ,
				width : "605" ,
				height : "250"
				
			});
	// setup ckeditor and its configurtion
	CKEDITOR.replace( 'column1',
			{
				//fullPage : true,
				////extraPlugins : 'wordcount',
				customConfig : 'config.js' ,  
				toolbar :  'BasicToolbar'  ,
				width : "605" ,
				height : "250"
				
			});
	// setup ckeditor and its configurtion
	CKEDITOR.replace( 'column2',
			{
				//fullPage : true,
				////extraPlugins : 'wordcount',
				customConfig : 'config.js' ,  
				toolbar :  'BasicToolbar'  ,
				width : "605" ,
				height : "250"
				
			});
	// setup ckeditor and its configurtion
	CKEDITOR.replace( 'column3',
			{
				//fullPage : true,
				////extraPlugins : 'wordcount',
				customConfig : 'config.js' ,  
				toolbar :  'BasicToolbar'  ,
				width : "605" ,
				height : "250"
				
			});
	// setup ckeditor and its configurtion
	CKEDITOR.replace( 'column4',
			{
				//fullPage : true,
				////extraPlugins : 'wordcount',
				customConfig : 'config.js' ,  
				toolbar :  'BasicToolbar'  ,
				width : "605" ,
				height : "250"
				
			});
	// setup ckeditor and its configurtion
	CKEDITOR.replace( 'bottomFooter',
			{
				//fullPage : true,
				////extraPlugins : 'wordcount',
				customConfig : 'config.js' ,  
				toolbar :  'BasicToolbar'  ,
				width : "605" ,
				height : "250"
				
			});
	
	
});
$(document).ready(init);
/**
 * initialize all the settings after document is ready
 * @author spsingh
 */

function init()
{
		$(":input").attr("autocomplete","off");
		$("form").submit(function(){
		
			if( validator.valid() )
			{
					$("button#updateFooterButton").attr('disabled' , 'disabled') ;
			}
		});
	
	/*
		  $("form").bind("keypress", function(e) {
	          if (e.keyCode == 13) 
	        
	        	  return false; 
	        	  this.submit();
	         
	    });
	*/
	
	// apply validatios
	validateFooter() ;
}


var validator = null ;
/**
 * form validation for footer feilds 
 * @author spsingh
 */

function validateFooter()
{
	validator  = $("form#editFooter")
	.validate(
			{
				errorClass : 'error',
				validClass : 'success',
				errorElement : 'span',
				errorPlacement : function(error, element) {
					element.parent("div").prev("div")
							.html(error);
				},
				rules : {
				
					topFooter : {
						required : true 
					},
					column1 : {
						required : true 
					},
					column2 : {
						required : true 
					},
					column3 : {
						required : true 
					},
					column4 : {
						required : true 
					}, 
					bottomFooter : {
						required : true 
					}
					
					
					
				},
				messages : {
					topFooter : {
						required : __("Please enter top footer")
					},
					column1 : {
						required : __("Please enter column1")
					},
					column2 : {
						required : __("Please enter column2")
					},
					column3 : {
						required : __("Please enter column3")
					},
					column4 : {
						required : __("Please enter column4")
					}, 
					bottomFooter : {
						required : __("Please enter bottom footer") 
					}

				},

				onfocusin : function(element) {
					if (!$(element).parent('div').prev("div")
							.hasClass('success')) {
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

$.validator.setDefaults({
	onkeyup : false,
	onfocusout : function(element) {
		$(element).valid();
	}

});

function cancelFooter()
{
	
	window.location.href = HOST_PATH + "admin/footer";
}