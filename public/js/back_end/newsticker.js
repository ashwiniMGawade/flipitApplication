$(document).ready(function() {
	
	selectShop();
	$('#dp3').datepicker().on('changeDate');
	
});

function selectShop(){
$("#whichshop").select2({placeholder: __("Select a Shop")});
   $("#whichshop").change(function(){
		$("#selctedshop").val($(this).val());
	});
}

function newschangelinkStatus(el)
{
	jQuery(el).addClass('btn-primary').siblings('button').removeClass('btn-primary active') ;	
	if (jQuery(el).attr('name') == 'newsdeepLinkOnbtn')
	{
		jQuery("input[type=checkbox]" , jQuery(el).parent("div")).attr('checked' , 'checked').val(1);
		jQuery("#newsrefUrl" , jQuery(el).parent("div")).removeAttr("disabled");
		
	} else
	{
		 jQuery("input[type=checkbox]" , jQuery(el).parent("div")).removeAttr('checked').val(0);
		 jQuery("#newsrefUrl" , jQuery(el).parent("div")).attr("disabled", "disabled");
		
	}
}


