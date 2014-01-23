/**
 * moneysaving.js 1.0
 * @author Raman
*/

/**
 * validRules oject contain all the messages that are visible when an elment
 * val;ue is valid
 * 
 * structure to define a message for element key is to be element name Value is
 * message
 */

var validRules = {
	email : __("Email looks great")
};

/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 * structure to define a message for element key is to be element name Value is
 * message
 */

var focusRules = {
	email : __("Enter  email address")
};

$(document).ready(init);

/**
 * initialize all the settings after document is ready
 * @author sunny patial
 */
function init()
{
	
	$('#floatDiv').stickyfloat( {duration: 0} );
}

$(function() {  
	  $(".green-btn").click(function() {
		 
		  var email = $("input#email").val(); 
		  var signupLink = HOST_PATH_LOCALE + __link("inschrijven") + '/' + __link("stap1");
		  if($("#Fnewsletter").valid()==true){
		  $.ajax({  
		  type: "POST",  
		  url: HOST_PATH_LOCALE+"moneysavingguide/register/email/" + email,  
		  success: function() {  
		    $('#Fnewsletter').html("<div id='message'></div>");  
		    $('#message')
		    .fadeIn(1500, function() {  
		      $('#message').append("<a href="+signupLink+">"+__('Click Here to create a profile!')+"</a>");  
		    });  
		  }  
		  });
		  return false;
		  }
	  });  
	});
function scrollbyChapter(el){
	
    var aTag = $("h2#"+ $(el).attr('rel') );
   
    	$('html,body').animate({scrollTop: aTag.offset().top},'slow');
    
}

