$(document).ready(function(){
	jQuery("#locale")
		.select2({placeholder: __("Select a locale")})
		.change(function(){
			$.ajax({
				url : HOST_PATH + 'admin/locale/savelocale',
				type : 'post',
				dataType : 'json',
				data : {'locale' : $(this).val()},
				success : function(obj){
					window.location.reload(true);
				}
			});
	});

	jQuery("select#timezone")
		.select2({placeholder: __("Select a timezone")})
		.change(function(){
			$.ajax({
				url : HOST_PATH + 'admin/locale/save-timezone',
				type : 'post',
				dataType : 'json',
				data : {'timezone' : $(this).val()},
				success : function(obj){
					window.location.reload(true);
				}
			});
		});
    
    $.ajax({
		url : HOST_PATH + 'admin/locale/getlocale',
		type : 'post',
		dataType : 'json',
		success : function(obj){
			jQuery("#locale").select2('val',obj);
		}
	});
});

function LocaleStatusToggle(el)
{
	$(el).addClass('btn-primary').siblings('button').removeClass('btn-primary active');
	var localeStatus = $(el).attr('data-status');
    
    $.ajax({
		url : HOST_PATH + 'admin/locale/savelocalestatus',
		type : 'post',
		dataType : 'json',
		data : {'localeStatus' : localeStatus},
		success : function(obj){
			window.location.reload(true);
		}
	});
}

function localeSettingToggle(element, inputFieldId)
{
    $(element).addClass('btn-primary').siblings('button').removeClass('btn-primary active');
    $('#'+inputFieldId).val($(element).attr('data-option'));
}