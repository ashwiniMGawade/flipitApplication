var filter = "";

var gt = new Gettext();
function __ (msgid) { return gt.gettext(msgid); }
function __link (msgid) { return gt.gettext(msgid); }

//var HOST_PATH_PUBLIC = 'http://' + window.location.host + '/public';

var HOST_PATH = 'http://' + window.location.host + '/';
//var userList = jQuery('#userList').dataTable();
//var trashUserList = jQuery('#trashUserList').dataTable();
var _websiteAccess = null;
/**
 * Add overlay for front-end
 * @author kraj
 */
function ___addOverLay()
{


	if( jQuery("div#overlay").length == 0)

	{

		var overlay = jQuery("<div id='overlay'><img id='img-load' src='" +  HOST_PATH  + "/public/images/back_end/ajax-loader.gif'/></div>");
		overlay.appendTo(document.body);

	}

}
/**
 * remove overlay
 * @author kraj
 */
function ___removeOverLay()
{
	jQuery('div#overlay').remove();
	return true ;
}
/**
 * Add overlay for back-end
 * @author kraj
 */
function addOverLay()
{


	/*if( jQuery("div#overlay").length == 0)

	{

		var overlay = jQuery("<div id='overlay'><img id='img-load' src='" +  HOST_PATH_PUBLIC + "/images/back_end/ajax-loader.gif'/></div>");
		overlay.appendTo(document.body);
		jQueryt = jQuery(document.body); // CHANGE it to the table's id you have
		jQuery("#img-load").css({
		  top  : (jQueryt.height() / 2),
		  left : (jQueryt.width() / 2)
		});
	} */

}
/**
 * remove overlay
 * @author kraj
 */
function removeOverLay()
{
	jQuery('div#overlay').remove();
	return true ;
}
/**
 * function only use for unique message
 * for javascript files
 * @author kraj
 */
var l10n = {'Enter your Username':'l10'};
function jsMsgTranslate(msg)
{
	return l10n['Enter your Username'];
	//return typeof l10n[s] != 'undefined' ? l10n[s] : s;
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

function addWidgetPopup()
{

	if( jQuery("div#overlay").length == 0)
	{

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


function localeSettingCMS(obj)
{
	var val = obj.value.split('/');
	val = val[1] == undefined ? 'en' : val[1] ;

	jQuery.cookie('locale', val , { expires: 1 , path: '/' });
	jQuery.cookie('site_name',obj.value, { expires: 1 , path: '/' });
	obj.form.submit();
}

jQuery(document).ready(function() {
	//Refactored block
    if (jQuery(".shop-name")[0]) {
        jQuery(".shop-name:last-child").append("<a class='btn-top' href='#'>"+ __('Back to top') +"</a>");
    }

	if(jQuery("div.coupon-code").length > 0)
	{
		jQuery("div.coupon-code").bigText({'maximumFontSize': 20});
	}
	//end refactored block
	// set user menu
	// jQuery.post('/login/usermenu/rnd/'+Math.random()*9999999999, function(data) {
	// 	jQuery('.top-nav').html(data);
	// });
	// // set user footer
	// jQuery.post('/login/userfooter/rnd/'+Math.random()*9999999999, function(data) {
	// 	jQuery('#user_footer').html(data);
	// });
	// // set user widget
	// jQuery.post('/login/userwidget/rnd/'+Math.random()*9999999999, function(data) {
	// 	jQuery('#user_widget').html(data);
	// });

});


function htmlEncode(value){
	  //create a in-memory div, set it's inner text(which jQuery automatically encodes)
	  //then grab the encoded contents back out.  The div never exists on the page.
	  return $('<div/>').text(value).html();
}


function htmlDecode(value){
	  return $('<div/>').html(value).text();
}
function showLightBox(link){
	jQuery('#myModalLightBox').modal('show');
	//alert('aa');
}
function refreshVarnish(){
	 window.location.href = HOST_PATH +  "admin/refreshvarnish";
}

function ucfirst (str) {
	  str += '';
	  var f = str.charAt(0).toUpperCase();
	  return f + str.substr(1);
	}
