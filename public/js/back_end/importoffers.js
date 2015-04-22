$(document).ready(function(){
	$("#importOffers").click(function(){
		saveRedirect();
	});
});

function saveRedirect() {
	if(validatorImport == true) {   
		$('form#addOffersForm').submit();
	}else {
		return false;
	}
}

var validatorImport = false ;

function checkFileType(e)
{
	var el = e.target  ? e.target :  e.srcElement ;
	$('#imagerrorDiv').show(); 
	var regex = /xlsx/ ;
	if (regex.test(el.value)) {
		validatorImport = true;
		console.log(el);
		$('#imagerrorDiv').html(__("<span class='success'>Valid file</span>"));
	} else {
		validatorImport = false;
		$('#imagerrorDiv').html(__("<span class='error help-inline'>Please upload only xlsx file</span>"));
	} 
}




