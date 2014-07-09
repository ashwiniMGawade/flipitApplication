$(document).ready(function() {	
	if (typeof(http) != 'undefined') { 
	    $.get(http, function (data) {
	      $('#'+divId).html(data);
	      console.log('Load of '+ link +' was performed with ajax.');
	    });
	}
});