/**
a * executes when document is loaded 
 * @author pkaur4 
 */
$(document).ready(function(){
	//call to keyword list function while loading
	$("#importVisitors").click(function(){
		
		saveRedirect();
	});
	
	
});

function saveRedirect() {
	
	if(validatorImport == true) {   
		
		$('form#addVisitorsForm').submit();
		
	}else {
		
		 return false;
	}
	
}




/**
 * validRules oject contain all the messages that are visible when an elment
 * value is valid
 * structure to define a message for element: key is to be element name and Value is
 * message
 */
var validRules = {
		
};

/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 * structure to define a message for element : key is to be element name and Value is
 * message
 */
var focusRules = {
		
};

var validatorImport = false ;
/**
 * check redirect validation while edit
 * @author kraj
 */

function checkFileType(e)
{
	 var el = e.target  ? e.target :  e.srcElement ;
	 $('#imagerrorDiv').show(); 
	 
	 
	 var regex = /xlsx/ ;
	
	 
	 
	 if( regex.test(el.value) )
	 {
		 validatorImport = true;
		// invalidForm[el.name] = false ;
		console.log(el);
		$('#imagerrorDiv')
			.html("<span class='success'>"+__("Valid file")+"</span>").removeClass('error');
		 /*$(el).parents("div.mainpage-content-right")
		 .children("div.mainpage-content-right-inner-right-other").removeClass("focus")
		 .html("<span class='success help-inline'>Valid file</span>"); */
		 
	 }else{
		 
		 validatorImport = false;
		 $('#imagerrorDiv')
			.html(__("<span class='error help-inline'>Please upload only xlsx file</span>"));
		
	 }
	 
 }




