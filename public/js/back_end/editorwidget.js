$(document).ready(function() {
	CKEDITOR.replace(
		'description',
	    {
            customConfig : 'config.js' ,  
            toolbar :  'BasicToolbar'  ,
            height : "300"
		}
	);
});

function changeAction(e, type)
{
	var id =   $(e).attr("id");
	if (id=='btnNo') {
		$('#btnNo').addClass('btn-primary');
		$('#btnYes').removeClass('btn-primary');
	} else {
		$('#btnYes').addClass('btn-primary');
		$('#btnNo').removeClass('btn-primary');
		$('#redirectTo').parent('div').removeClass('error')
		.removeClass('success')
		.prev("div")
		.addClass('focus')
		.removeClass('error')
		.removeClass('error');
		$('span[for=redirectTo]').remove();
	}
	$("input#actionType").val(type);
}

function getPageTypeData() {
	var pageType = $("#pageType").val();
	$.ajax({
	  	url: HOST_PATH + "admin/popularcode/pagetypedetail",
	   	dataType: 'json',
	   	data: {'pageType' : pageType},
	   	success: function(dataSet) {
		    if (dataSet != '') {
		   		CKEDITOR.instances['description'].setData(dataSet[0].description);
			 	$("#subtitle").val(dataSet[0].subtitle);
			 	$("#selecteditors").val(dataSet[0].editorId);
			 	if (dataSet[0].status) {
			 		$('#btnYes').addClass('btn-primary');
					$('#btnNo').removeClass('btn-primary');
					$("#actionType").val(1);
			 	} else {
			 		$('#btnNo').addClass('btn-primary');
					$('#btnYes').removeClass('btn-primary');
					$("#actionType").val(0);
			 	}
			 	
		    } else {
		    	$("#description").val();
			 	$("#subtitle").val();
			 	$("#selecteditors").val();
			 	$("#btnYes").val(1);
		    }
	    } 
	});	
}
