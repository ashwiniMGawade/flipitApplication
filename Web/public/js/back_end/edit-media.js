
/**
 * editMedia.js
 * @author mkaur
 */

/**
 * rules for validations
 */
var validRules = {
	name : __("Title looks great"),
	alternateText : __("Alternate text looks great"),
	caption: __("Caption looks great"),
	description: __("Description looks great")
};
var focusRules = {

	name : __("Please enter title"),
	alternateText : __("Please enter alternate text"),
	caption : __("Please enter caption"),
	description: __("Please enter description")
};
/*global $, window, document */
$(document).ready(init);
function init(){
	/*var options = {
			'maxCharacterSize': 160,
			'displayFormat' : ''
	};
	$('#description').textareaCount(options, function(data){
		$('#metaTextLeft').val("Characters Left: "+data.left);
	});*/
	
	validateMedia();    
   }

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
 * Form validation used in Media form
 */	
		
function validateMedia(){
	
	validateNewMedia = $("form#editMediaForm")
		.validate({	
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
			name : {
				required : true
			},
			alternateText:{
				required : false,
			},
			caption:{
				required : false,
			},
			description:{
				required : false,
			},
			},
		messages : {
			name : {
				required : __("Please enter title")
			},
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
					$(element).parent('div')
					.removeClass(errorClass)
					.addClass(validClass).prev(
							"div").addClass(
							validClass)
					.removeClass(errorClass);
					
					$('span.help-inline', $(element).parent('div')
									.prev('div')).text(
						 validRules[element.name] ).show();
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
		
		});
	}

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

function resetBorders(el)
{
	$(el).each(function(i,o){
	 $(o).parent('div')
		.removeClass("error success")
		.prev("div").removeClass('focus error success') ;
	
	});
}


function callToPermanentDelete(){
	bootbox.confirm(__("Are you sure you want to delete this media permanently?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		}
		else{
			window.location.href = $('input#hId').val()+"/act/delete";
		}
		
	});
}
/**
 * counts number of letters on description textarea of form 
 * @param field
 * @param cntfield
 * @param maxlimit
 */
function textCounter(field,cntfield,maxlimit) {
	if (field.value.length > maxlimit){ // if too long...trim it!
	field.value = field.value.substring(0, maxlimit);
	// otherwise, update 'characters left' counter
	}else{
	cntfield.value = maxlimit - field.value.length;
	}
}
