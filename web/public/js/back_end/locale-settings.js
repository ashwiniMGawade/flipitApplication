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

    jQuery('#delete-expired-coupon-logo-btn').click(function(){
        $.ajax({
            url : HOST_PATH + 'admin/locale/delete-expired-coupon-logo',
            type : 'post',
            dataType : 'json',
            data : {'imageType' : 'header'},
            success : function(obj) {
                window.location.reload(true);
            }
        });
    });

    $('#expiredCouponLogo').fileupload({
        url: HOST_PATH +  'admin/locale/update-expired-coupon-logo',
        dataType: 'json',
        done: function (e, data) {
            jQuery('#progress .bar',".header-image-cont").css('width',  '100%');
            jQuery("#update-expired-coupon-logo-btn").off("click");
            setTimeout(function(){
                jQuery('.progress-file-detail',".header-image-cont").slideUp('slow',function() {
                    jQuery('#progress .bar',".header-image-cont").css('width',  '0%');
                    jQuery("#update-expired-coupon-logo-btn").hide();
                    $("#delete-expired-coupon-logo-btn").show();
                    $(".header-image-cont span.message").html('');
                });
            },500);
            var uploadedStatus = data.result;
            if (uploadedStatus.status == 200 ) {
                window.location.reload(true);
            }
        },
        add:function (e, data) {
            var acceptFileTypes = /jpg|JPG|png|PNG|jpeg|JPEG/ ;
            var fileName = data.originalFiles[0]['name'] ;
            if (!acceptFileTypes.test(data.originalFiles[0]['name'])) {
                $(".header-image-cont span.message").html(  __('Please upload only *.jpg or *.png file'))
                    .addClass('error').removeClass('success');;
                return false;
            }
            $(".header-image-cont span.message").html(  __('valid file')).addClass('success').removeClass('error');
            $("#update-expired-coupon-logo-btn", ".header-image-cont").show();
            $("#update-expired-coupon-logo-btn", ".header-image-cont").off('click').on('click',function () {
                bootbox.confirm(__("are you sure you want to change expired coupon logo?"),__('No'),__('Yes'),function(r){
                    if (r) {
                        $("span#selected-filename", ".header-image-cont").html(fileName);
                        $('div.progress-file-detail', ".header-image-cont").show('fast');
                        data.submit();
                        jQuery("#expiredCouponLogo").hide();
                    }
                });
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .bar', $(".header-image-cont")).css('width', progress + '%');
        }
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