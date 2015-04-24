var filter = "";

var gt = new Gettext();
function __ (msgid) { return gt.gettext(msgid); }
function __link (msgid) { return gt.gettext(msgid); }

var HOST_PATH = 'http://' + window.location.host + '/';
var _websiteAccess = null;

function ___addOverLay() {
	if( jQuery("div#overlay").length == 0) {
		var overlay = jQuery("<div id='overlay'><img id='img-load' src='" +  HOST_PATH  + "/public/images/back_end/ajax-loader.gif'/></div>");
		overlay.appendTo(document.body);
	}
}

function ___removeOverLay() {
	jQuery('div#overlay').remove();
	return true ;
}

function addOverLay() {

}

function removeOverLay() {
	jQuery('div#overlay').remove();
	return true ;
}

var l10n = {'Enter your Username':'l10'};
function jsMsgTranslate(msg) {
	return l10n['Enter your Username'];
}
function ucfirst(str) {
	str = str == null ?  "" : str ;
	var firstLetter = str.substr(0, 1);
	return firstLetter.toUpperCase() + str.substr(1);
}

function changeDateFormat(date){
	date = date.split('-');
	return date['2']+'-'+date['1']+'-'+date['0'];
}

function addWidgetPopup() {
	if( jQuery("div#overlay").length == 0) {
		var overlay = jQuery('<div id="overlay"><div class="fancybox-skin" style="padding: 15px;">'+jQuery('#widgetListUserdefined').html()+'</div>');
		overlay.appendTo(document.body);
		jQueryt = jQuery(document.body); // CHANGE it to the table's id you have
		jQuery(".fancybox-skin").css({
		  top  : ((jQueryt.height() / 2)-(jQuery('.fancybox-skin').height()/2)),
		  left : ((jQueryt.width() / 2)-(jQuery('.fancybox-skin').width()/2))
		});
	}
	jQuery('div#overlay').css({ opacity: 0.8 });
}


function localeSettingCMS(obj) {
	var val = obj.value.split('/');
	val = val[1] == undefined ? 'en' : val[1] ;
	jQuery.cookie('locale', val , { expires: 1 , path: '/' });
	jQuery.cookie('site_name',obj.value, { expires: 1 , path: '/' });
	obj.form.submit();
}

jQuery(document).ready(function() {
    if(jQuery(".shops .similar-shops-images").length > 0) {
        $(".shops li").each(function(index, value) { 
            var backgroundImageAttribute = jQuery(this).find(".similar-shops-images").attr('data-src');
            jQuery(this).find(".similar-shops-images").css("background-image", "url('" + backgroundImageAttribute + "')")
        });
    }

	if(jQuery("div.coupon-code").length > 0) {
		jQuery("div.coupon-code").bigText({'maximumFontSize': 20});
	}
	var offset = 200;
	var duration = 500;
	jQuery(window).scroll(function() {
	if (jQuery(this).scrollTop() > offset) {
	jQuery('.scroll-to-top').fadeIn(duration);
	} else {
	jQuery('.scroll-to-top').fadeOut(duration);
	}
	});

	jQuery('.scroll-to-top').click(function(event) {
	event.preventDefault();
	jQuery('html, body').animate({scrollTop: 0}, duration);
	return false;
	})
});

function htmlEncode(value){
    return $('<div/>').text(value).html();
}


function htmlDecode(value){
	return $('<div/>').html(value).text();
}
function showLightBox(link){
	jQuery('#myModalLightBox').modal('show');
}
function refreshVarnish(){
	window.location.href = HOST_PATH +  "admin/refreshvarnish";
}

function ucfirst (str) {
	str += '';
	var f = str.charAt(0).toUpperCase();
	return f + str.substr(1);
}