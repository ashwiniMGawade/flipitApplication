$(document).ready(function() {
    jQuery('#delete-header-image-btn').click(function(){
        $.ajax({
            url : HOST_PATH + 'admin/accountsetting/delete-newletter-banner-images',
            type : 'post',
            dataType : 'json',
            data : {'imageType' : 'header'},
            success : function(obj) {
                window.location.reload(true);
            }
        });
    });
    jQuery('#delete-footer-image-btn').click(function() {
        $.ajax({
            url : HOST_PATH + 'admin/accountsetting/delete-newletter-banner-images',
            type : 'post',
            dataType : 'json',
            data : {'imageType' : 'footer'},
            success : function(obj){
                window.location.reload(true);
            }
        });
    });

    'use strict'
    var url = HOST_PATH +  'admin/accountsetting/update-header-image';
    $('#newsLetterHeaderImage').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data) {
            jQuery('#progress .bar',".header-image-cont").css('width',  '100%');
            jQuery("#update-header-image-btn").off("click");
            setTimeout(function(){
                jQuery('.progress-file-detail',".header-image-cont").slideUp('slow',function() {
                    jQuery('#progress .bar',".header-image-cont").css('width',  '0%');
                    jQuery("#update-header-image-btn").hide();
                    $("#delete-header-image-btn").show();                   
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
            $("#update-header-image-btn", ".header-image-cont").show();
            $("#update-header-image-btn", ".header-image-cont").off('click').on('click',function () {
                bootbox.confirm(__("are you sure you want to change newsletter header image?"),__('No'),__('Yes'),function(r){
                    if (r) {
                        $("span#selected-filename", ".header-image-cont").html(fileName);
                        $('div.progress-file-detail', ".header-image-cont").show('fast');
                        data.submit();
                        jQuery("#newsLetterHeaderImage").hide();
                    }
                });
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .bar', $(".header-image-cont")).css('width', progress + '%');
        }
    });

    var url = HOST_PATH +  'admin/accountsetting/update-footer-image';
    $('#newsLetterFooterImage').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data) {
            jQuery('#progress .bar',".footer-image-cont").css('width',  '100%');
            jQuery("#update-footer-image-btn").off("click");
            setTimeout(function() {
                jQuery('.progress-file-detail',".footer-image-cont").slideUp('slow',function() {
                    jQuery('#progress .bar',".footer-image-cont").css('width',  '0%');
                    jQuery("#update-footer-image-btn").hide();
                    $("#delete-footer-image-btn").show();                   
                    $(".footer-image-cont span.message").html('');
                });
            },500);
            var uploadedStatus = data.result;
            if (uploadedStatus.status == 200 ) {
                window.location.reload(true);
            }
        },
        add:function (e, data) {
            var acceptFileTypes = /jpg|JPG|png|PNG|jpeg|JPEG/;
            var fileName = data.originalFiles[0]['name'] ;
            if (!acceptFileTypes.test(data.originalFiles[0]['name'])) {
                $(".footer-image-cont span.message").html(  __('Please upload only *.jpg or *.png file'))
                .addClass('error').removeClass('success');;
                return false;
            }
            $(".footer-image-cont span.message").html(  __('valid file')).addClass('success').removeClass('error');
            $("#update-footer-image-btn", ".footer-image-cont").show();
            $("#update-footer-image-btn", ".footer-image-cont").off('click').on('click',function () {
                bootbox.confirm(__("are you sure you want to change newsletter footer image?"),__('No'),__('Yes'),function(r){
                    if (r) {
                        $("span#selected-filename", ".footer-image-cont").html(fileName);
                        $('div.progress-file-detail', ".footer-image-cont").show('fast');
                        data.submit();
                        jQuery("#newsLetterFooterImage").hide();
                    }
                });
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .bar', $(".footer-image-cont")).css('width', progress + '%');
        }
    });
});
function saveNewsletterBannerUrl(el)
{
    var value = $(el).val().trim();
    if(value != ''){
        $.ajax({
            url : HOST_PATH + 'admin/accountsetting/save-newsletter-banner-image-url',
            type : 'post',
            data : { name : $(el).attr('name'), val : value },
        });
    }
}