
$(document).ready(function(){
	
	$('#metaTitle').NobleCount('#metaTextLeft',{
		max_chars: 68,
		prefixString : __("Category meta title length ")
	});
	
	$('#metaDescription').NobleCount('#metaDescriptionleft',{
		max_chars: 150,
		prefixString : __("Category meta description length ")
	});
	
 
	word_count("#description", __("Catgeory description length "),"#description_count");
	$('#description').keyup(function(){
			word_count("#description", __("Catgeory description length "),"#description_count");
	});
	
	
	
	init();
	$('form#saveCategoryForm').submit(function(){
	   
		saveCategories();
	});
	
	
});


function word_count(field,msg,count) {

    var number = 0;
    var matches = $(field).val().match(/\b/g);
    if(matches) {
        number = matches.length/2;
    }
    
    $(count).val(msg +  number + __(' word') + (number > 1 ? 's' : ''));

}

function saveCategories()
{
	if($("form#saveCategoryForm").valid())
	{
		$('#saveCategory').attr('disabled' ,"disabled");
		return true;
		
	}else {
			
		return false;
	}
	
}
/**
 * validRules oject contain all the messages that are visible when an elment
 * value is valid
 * 
 * structure to define a message for element: key is to be element name and Value is
 * message
 */
var validRules = {
        categoryName : __("Category title looks great"),
		permaLink : __("Valid Url"),
		metaTitle : __("Meta title looks great"),
		metaDescription : __("Meta description looks great"),
		categoryIconNameHidden : __("Valid file"),
		description  : __("Description looks great")
};

/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 * 
 * structure to define a message for element : key is to be element name and Value is
 * message
 */
var focusRules = {
		categoryName : __("Please enter category title"),
		permaLink : __("Please enter valid Url"),
		metaTitle : __("Please enter meta title"),
		metaDescription : __("Please enter meta description"),
		categoryIconNameHidden : __("Please upload valid file"),
		description : __("Please enter description"),
};

$(document).ready(init);

function init()
{
	// code used for character count
	/*var options1 = {
			'maxCharacterSize': 68,
			'displayFormat' : ''
	};
	$('#metaTitle').textareaCount(options1, function(data){
		$('#metaTextLeft').val("Characters Left: "+data.left);
	});
	var options = {
			'maxCharacterSize': 160,
			'displayFormat' : ''
	};
	$('#metaDescription').NobleCount(options, function(data){
		$('#metaDescriptionLeft').val("Characters Left: "+data.left);
	});
	
	var options2 = {
			'maxCharacterSize': 500,
			'displayFormat' : ''
	};
	$('#description').textareaCount(options2, function(data){
		$('#descriptionLeft').val("Characters Left: "+data.left);
	});*/
	$("form").submit(function(){
		
		if (! jQuery.isEmptyObject(invalidForm) ) 
			
			for(var i in invalidForm)
			{
				if(invalidForm[i])
				{
					$('#saveCategory').removeAttr('disabled');
					return false ;
				}
				
			}
	});
	
	//function call to validate new category
	  validateFormAddNewCategory();
	  
}

$.validator.setDefaults({
	onkeyup : false,
	onfocusout : function(element) {
		$(element).valid();
	}

});

var invalidForm = {} ;
var errorBy = "" ;
/**
 * check file type to be uploaded
 * @author blal
 */
function checkFileType(e)
{
	 var el = e.target  ? e.target :  e.srcElement ;
	 
	 
	 var regex = /png|jpg|jpeg|PNG|JPG|JPEG/ ;
	 if( regex.test(el.value) )
	 {
		
		 invalidForm[el.name] = false ;
		 $(el).parents("div").addClass('success').removeClass('error');	 		 
		 $(el).parents("div.mainpage-content-right")
		 .children("div.mainpage-content-right-inner-right-other").removeClass("focus")
		 .html(__("<span class='success help-inline'>Valid file</span>"));
		 
	 } else {
		 $(el).parents("div").addClass('error').removeClass('success');	 
		 $(el).parents("div.mainpage-content-right")
		 .children("div.mainpage-content-right-inner-right-other").removeClass("focus")
		 .html(__("<span class='error help-inline'>Please upload valid file</span>"));
		 
		 invalidForm[el.name] = true ;
		 errorBy = el.name ;
		 
		 
}
}
var validatorForNewCategory = null ;
/**
 * form validation during add category 
 * @author blal
 */
function validateFormAddNewCategory(){
  validatorForNewCategory = $("form#saveCategoryForm").validate(
		  {
			    errorClass : 'error',
				validClass : 'success',
				errorElement : 'span',
				ignore: ".ignore, :hidden",
				afterReset  : resetBorders,
				errorPlacement : function(error, element) {
				element.parent("div").prev("div")
							.html(error);
				},
				rules : {
					categoryName: {
								required : true,
								minlength : 2,
					},
					permaLink : {
						required : true,
						minlength : 1,
                        remote : {
							// validating and filtering  navigational url from server 
							url: HOST_PATH + "admin/category/validatepermalink",
				        	type: "post" ,
				        	beforeSend  : function ( xhr ) {
			        			
				        	    $('span[for=permaLink]').html(__('please wait, validating...')).addClass('validating').show()
				        		.parent('div').attr('class' , 'mainpage-content-right-inner-right-other focus') ;
				        	    
				        	  },
				        	  complete : function(e) {
				        		
				        		  //alert(e.responseText);
				        		  
				        		$('span[for=permaLink]' , $("[name=permaLink]").parents('div.mainpage-content-right')).removeClass('validating') ;
				        		res = $.parseJSON(e.responseText);
				        		
				        		if(res.status == "200")
				        		{
				        			$('span[for=permaLink]' , $("[name=permaLink]").parents('div.mainpage-content-right') )
				        			.html(validRules['permaLink']).attr('remote-validated' , true);
				        			
				        			$('#permaLink').val(res.url);
				        			
				        			$("input[name=permaLink]").parent('div').prev("div").removeClass('focus')
				        			.removeClass('error').addClass('success');
				        		} 
				        		else
				        		{
				        			$('span[for=permaLink]' , $("[name=permaLink]").parents('div.mainpage-content-right') )
			        				.attr('remote-validated' , false);
				        		}
				        	}
						}
					 }
					},
				messages : {
					categoryName: {		
								required : __("Please enter category title"),
								minlength : __("Please enter atleast 2 characters")
				},
				permaLink  : {		
					required : __("Please type permalink"),
					remote : __("Permalink already exists")
					
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
						}
						else
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
							} 
							else
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

function resetBorders(el)
{
	$(el).each(function(i,o){
	 $(o).parent('div')
		.removeClass("error success")
		.prev("div").removeClass('focus error success') ;
	
	});
}
 


