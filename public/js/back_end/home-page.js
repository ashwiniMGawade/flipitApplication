/**
 * home-page.js
 * @author Er.kundal
 */



var validRules = {

		'title': __("dadasdasd"),
		'emailPerLocale': __("Valid Email"),
		'url-1' : __("Vaild url"),
		'toolTip-1' : __("Tooltip looks great"),
		'url-2' : __("Vaild url"),
		'toolTip-2' : __("Tooltip looks great"),
		'url-3' : __("Vaild url"),
		'toolTip-3' : __("Tooltip looks great"),
		'url-4' : __("Vaild url"),
		'toolTip-4' : __("Tooltip looks great"),
		'url-5' : __("Vaild url"),
		'toolTip-5' : __("Tooltip looks great"),
		'url-6' : __("Vaild url"),
		'toolTip-6' : __("Tooltip looks great")
};


/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 * structure to define a message for element key is to be element name Value is
 * message
 */
var focusRules = {

		'title': __("enter title"),
		'emailPerLocale': __("enter email address"),
		'url-1' : __("Enter url"),
		'toolTip-1' : __("Enter tooltip"),
		'url-2' : __("Enter url"),
		'toolTip-2' : __("Enter tooltip"),
		'url-3' : __("Enter Url"),
		'toolTip-3' : __("Enter tooltip"),
		'url-4' : __("Enter Url"),
		'toolTip-4' : __("Enter tooltip"),
		'url-5' : __("Enter Url"),
		'toolTip-5' : __("Enter tooltip"),
		'url-6' : __("Enter Url"),
		'toolTip-6' : __("Enter tooltip"),

};




var CKcontent = false ;

$(document).ready(function(){

	init();

	jQuery('#delete-header-image-btn').click(function(){

		var headerImageName = jQuery('#delete-header-image-btn').attr('alt');

		$.ajax({
				url : HOST_PATH + 'admin/homepage/delete-header-image',
				type : 'post',
				dataType : 'json',
				data : {'imageName' : headerImageName},
				success : function(obj){
					window.location.reload(true);
				}
			});

	});

		
	jQuery('#delete-widget-image-btn').click(function(){

		var widgetImageName = jQuery('#delete-widget-image-btn').attr('alt');

		$.ajax({
				url : HOST_PATH + 'admin/homepage/delete-widget-image',
				type : 'post',
				dataType : 'json',
				data : {'widgetName' : widgetImageName},
				success : function(obj){
					window.location.reload(true);
				}
			});
		

	});

	$.ajax({
		url : HOST_PATH + 'admin/homepage/getemail',
		type : 'post',
		dataType : 'json',
		success : function(obj){
			console.log(obj);
			jQuery("#emailPerLocale").val(obj);
		}
	});

	/*
	// setup ckeditor and its configurtion
	CKEDITOR.replace( 'content1',
			{
				//fullPage : true,
				////extraPlugins : 'wordcount',
				customConfig : 'config.js' ,
				toolbar :  'BasicToolbar'  ,
				height : "300"


			});
	 */


});


/**
* Another tabs for about us tabs for admin Home page
* Author : Er.kundal
* function for add html for making tabs
*/

function getabouthtml(){

	if(parseInt($("#comnno").val()) == 8){
		bootbox.alert(__('You can not add more tab . Its limited'));
		 return false;
	}

				var tabno = parseInt($("#comnno").val())+1;
				var passvalue = "anothertab";

			$.ajax({
			   type: "get",
			   url: HOST_PATH + "admin/homepage/getanothertab",
			   //dataType: 'json',
			   cache: true,
			   data: "tabnumber="+tabno+"&tab="+passvalue+"&randval="+Math.random(),
				   success: function(dataSet)
				   {
					   $('#comnno').val(tabno);
					   $('#multidiv').append(dataSet);

					   CKEDITOR.replaceAll( 'ckeditorDynamic' );

				   }
			});

}

function removetab(id){

		$.ajax({
			   type: "POST",
			   url: HOST_PATH + "admin/homepage/deleteabout",
			   // dataType: 'json',
			   cache: true,
			   data: "removeid="+id+"&randval="+Math.random(),
				   success: function(dataSet)
				   {
					   if(dataSet == 1){
						   bootbox.alert(__('Tab has been deleted successfully'));
						   // window.location = HOST_PATH + "admin/homepage/";
						   location.reload();
					   }else{
						   bootbox.alert(__('Tab can not delete'));
					   }
				   return false;

				  }
			});

}


function removeajaxtab(){

	location.reload();

}


/**
 * change the status of email lightbox
 * @param e event from which it is called
 */
function changeStatus(el)
{


	$(el).addClass('btn-primary').siblings('button').removeClass('btn-primary active') ;

	if ($(el).attr('name') == 'on')
	{
		$("input[type=checkbox]" , $(el).parent("div")).attr('checked' , 'checked').val(1);
	} else
	{
		$("input[type=checkbox]" , $(el).parent("div")).removeAttr('checked').val(0);
	}

}



function runWordCounter(){

	var options = {
			'maxCharacterSize': '',
			'displayFormat' : ''
	};

	$('#textarea-1').textareaCount(options, function(data){

		//$('#textleft-1').val(__("Characters Left: ")+data.left);
		jQuery('#textleft-1').val((data.input) + __("  characters"));
	});
	$('#textarea-2').textareaCount(options, function(data){

		jQuery('#textleft-2').val((data.input) + __("  characters"));
	});
	$('#textarea-3').textareaCount(options, function(data){

		jQuery('#textleft-3').val((data.input) + __("  characters"));
	});
	$('#textarea-4').textareaCount(options, function(data){

		jQuery('#textleft-4').val((data.input) + __("  characters"));
	});
	$('#textarea-5').textareaCount(options, function(data){

		jQuery('#textleft-5').val((data.input) + __("  characters"));
	});
	$('#textarea-6').textareaCount(options, function(data){

		jQuery('#textleft-6').val((data.input) + __("  characters"));
	});



}



function init()
{
	/*
		$("form").submit(function(){



		if( validator.valid() )
			{
				$("button#updateseenIdButton").attr('disabled' , 'disabled') ;
			}
		});


		*/

		$("form#asSeenIn").submit(function(){



			if (! jQuery.isEmptyObject(invalidForm) )

				for(var i in invalidForm)
				{
					if(invalidForm[i])
					{

						return false ;
					}

				}


		});


		$("form#homepage_banner").submit(function(){



			if (! jQuery.isEmptyObject(invalidForm) )

				for(var i in invalidForm)
				{
					if(invalidForm[i])
					{

						return false ;
					}

				}


		});



	// apply validatios

		validateemailform();
		runWordCounter();
}


var validator = null ;



function validateemailform()
{

validator  = $("form#emailform")
.validate(
		{


			errorClass : 'error',
			validClass : 'success',
			errorElement : 'span',
			ignore: ".ignore",
			errorPlacement : function(error, element) {
				element.parent("div").prev("div")
						.html(error);
			},
			rules : {

				'emailPerLocale' : {
					required : true ,
					email  : true
				}

			},
			messages : {

				'emailPerLocale' : {
					required : __("Please enter Email Address"),
					email : __("Invaild e-mail address")
				}
			},

			onfocusin : function(element) {

				if (!$(element).parent('div').prev("div")
						.hasClass('success')) {



						this.showLabel(element, focusRules[element.name]);

						$(element).parent('div').removeClass(
										this.settings.errorClass)
								.removeClass(
										this.settings.validClass)
								.prev("div")
								.addClass('focus')
								.removeClass(
										this.settings.errorClass)
								.removeClass(
										this.settings.validClass);
				}
			},

			highlight : function(element,
					errorClass, validClass) {

				$(element).parent('div')
						.removeClass(validClass)
						.addClass(errorClass).prev(
								"div").removeClass(
								validClass)
						.addClass(errorClass);

			},
			unhighlight : function(element,
					errorClass, validClass) {
				if(! $(element).hasClass("ignore2"))
				// check for ignored elemnets and highlight borders when succeed
				if(! $(element).hasClass("ignore")) {

					$(element).parent('div')
							.removeClass(errorClass)
							.addClass(validClass).prev(
									"div").addClass(
									validClass)
							.removeClass(errorClass);
					$(
							'span.help-inline',
							$(element).parent('div')
									.prev('div')).text(
						validRules[element.name]) ;

				} else {

					if( $(element).val().length > 0 )
					{

							$(element).parent('div')
							.removeClass(errorClass)
							.addClass(validClass).prev(
									"div").addClass(
									validClass)
							.removeClass(errorClass);

						$('span.help-inline',
							$(element).parent('div')
									.prev('div')).text(
						validRules[element.name]) ;

					} else {

						// hide errors message and remove highlighted borders
						$(
								'span.help-inline',
								$(element).parent('div')
								.prev('div')).hide();

						$(element).parent('div')
						.removeClass(errorClass)
						.removeClass(validClass)
						.removeClass("focus")
						.prev("div")
						.removeClass("focus")
						.removeClass(errorClass)
						.removeClass(validClass) ;
					}
				}

			}


		});

}


$.validator.setDefaults({
	onkeyup : false,
	onfocusout : function(element) {
		$(element).valid();
	}

});




var invalidForm = {} ;


//used to validate upload logo type
function checkFileType(e,errorType)
{


		 var el = e.target  ? e.target :  e.srcElement ;

		 var regex = /jpg|jpeg|png|JPG|JPEG|PNG/ ;

		 if( regex.test(el.value) )
		 {
			 invalidForm[el.name] = false ;


			 switch(errorType)
			 {

			 	case 'homepageBanner':

			 	break;
		 		default:

		 			$(el).parents("div.as_seen-content").removeClass("focus")
		 			.removeClass("error").addClass("success")
		 			.children("div.mainpage-content-right-inner-right-other-as-seen")
		 			.addClass("success")
		 			.html(__("<span class='success help-inline'>Valid file</span>"));
			 }



		 } else {


			 switch(errorType)
			 {

			 	case 'homepageBanner':

			 		console.log(1);

			 	break;
		 		default:

		 			$(el).parents("div.as_seen-content").removeClass("focus").removeClass("success")
		 			.addClass("error")
		 			.children("div.mainpage-content-right-inner-right-other-as-seen")
		 			.removeClass("focus").removeClass("success")
		 			.addClass("error")
		 			.html(__("<span class='error '>Please upload only .jpg or .png file</span>"));

			 }



			 invalidForm[el.name] = true ;
			 errorBy = el.name ;


		 }


}


//*********************** JS fuctions for popular shop section by Er.kundal ************************************//



$(document).ready(function() {
	//call to function for selected class(button)
	addSelectedClassOnButton(1);
	$("input#searchCouponTxt").keypress(function(e) {
		addSelectedClassOnButton(1);
		$('ul#mostPopularCode li').removeClass('selected');
				// if the key pressed is the enter key
				  if (e.which == 13) {

					  searchByTxt();
				  }
		});
		//Auto complete search for top five records in a dropdown
		$("#searchCouponTxt").autocomplete({
	        minLength: 1,
	        source: function( request, response){

	        	$.ajax({
	        		url : HOST_PATH + "admin/homepage/searchtoptenshop/keyword/" + $('#searchCouponTxt').val(),
	     			method : "post",
	     			dataType : "json",
	     			type : "post",
	     			success : function(data) {

	     				if (data != null) {

	     					//pass array of the respone in respone object of the autocomplete
	     					response(data);
	     				}
	     			},
	     			error: function(message) {

	     	            // pass an empty array to close the menu if it was initially opened
	     	            response([]);
	     	        }
	   		 });
	        },
	        select: function( event, ui ) {}
	    });
		//code for selection of li
		$('ul#mostPopularCode li').click(changeSelectedClass);

		validateemailform();

});

/**
* search offer by text
* @author kundal
* @version 1.0
*/
function searchByTxt() {

	$("ul.ui-autocomplete").css('display','none');
	$("ul.ui-autocomplete").html('');
	console.log('ok');
	//addSelectedClassOnButton(4);
	//$(this).addClass().addClass('btn-primary');
}
/**
* change selected class of li
* @author kundal
* @version 1.0
*/
function changeSelectedClass() {

	$('ul#mostPopularCode li').removeClass('selected');
	$(this).addClass('selected');
	//apply selected class on current button
	addSelectedClassOnButton(2);
}
/**
* change selected of button
* @param mixed $flag
* @author kundal
* @version 1.0
*/
function addSelectedClassOnButton(flag) {

	if(flag==1){

		$('button#moveUp').removeClass('btn-primary');
		$('button#moveDown').removeClass('btn-primary');
		$('button#deleteOne').removeClass('btn-primary');
		$('button#addNewShop').addClass('btn-primary');

	} else if(flag==2){

		$('button#moveUp').addClass('btn-primary');
		$('button#moveDown').addClass('btn-primary');
		$('button#deleteOne').addClass('btn-primary');
		$('button#addNewShop').removeClass('btn-primary');

	} else {

		$('button#moveUp').removeClass('btn-primary');
		$('button#moveDown').removeClass('btn-primary');
		$('button#deleteOne').removeClass('btn-primary');
		$('button#addNewShop').removeClass('btn-primary');
		$(flag).addClass('btn-primary');
	}
}
/**
* move up element by one from list
* @author kundal
* @version 1.0
*/
function moveUp() {

	var flag =  '#moveUp';
	//apply selected class on current button
	addSelectedClassOnButton(flag);
	//get id form slected li by attr
	var id = $('ul#mostPopularCode li.selected').attr('id');
	//get postion form slected li by attr
	var pos = $('ul#mostPopularCode li.selected').attr('relpos');
	if(parseInt(id) > 0){
		$.ajax({
			url : HOST_PATH + "admin/homepage/moveup/id/" + id + "/pos/" + pos,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {
					$('ul#mostPopularCode li').remove();
					var li = '';
					for(var i in json)
						{
						 	li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].shopId + "' id='" + json[i].id + "' >" + json[i].shop.name + "</li>";

						}
					//append li in ul( list of popular code
					$('ul#mostPopularCode').append(li);
					$('ul#mostPopularCode li#'+id).addClass('selected');
					$('ul#mostPopularCode li').click(changeSelectedClass);
				}


		});

	} else {

		bootbox.alert(__('Please select a Store from list'));
	}
}
/**
* add shop in shop list
* @author Er.kundal
* @version 1.0
*/
function addNewShop() {
    var addNewShopDivId =  '#addNewShop';
    addSelectedClassOnButton(addNewShopDivId);
    if ($('ul#mostPopularCode li').length > 48) {
        bootbox.alert(__('Popular Store list can have maximum 48 records, please delete one if you want to add more popular Store'));
	} else {

		if($("input#searchCouponTxt").val()=='' || $("input#searchCouponTxt").val()==undefined)
			{
				//console.log('ok');
				bootbox.alert(__('Please select a Shop'));

			} else {

				var offerName = $("input#searchCouponTxt").val();

				$.ajax({
	        		url : HOST_PATH + "admin/homepage/addshop/name/" + offerName,
	     			method : "post",
	     			dataType : "json",
	     			type : "post",
	     			success : function(data) {
 	     				if(data=='2' || data==2)
	     					{
	     						bootbox.alert(__('This store does not exist'));

	     					} else {

	     						$('ul#mostPopularCode li#noRecord').remove();

	     						var li  = "<li class='ui-state-default' reltype='" + data.type + "' relpos='" + data.position + "' reloffer='" + data.shopId + "' id='" + data.id + "' >" + offerName + "</li>";
	     						$('ul#mostPopularCode').append(li);
	     						$('ul#mostPopularCode li#'+ data.id).click(changeSelectedClass);
	     						$("input#searchCouponTxt").val('');
	     					}

	     			}


				});
				//code add offer in list here
			}
	}
}
/**
* move down element on one position from list
* @author kraj
* @version 1.0
*/
function moveDown() {

	var flag =  '#moveDown';
	//apply selected class on current button
	addSelectedClassOnButton(flag);
	var id = $('ul#mostPopularCode li.selected').attr('id');
	var pos = $('ul#mostPopularCode li.selected').attr('relpos');

	if(parseInt(id) > 0) {
		$.ajax({
			url : HOST_PATH + "admin/homepage/movedown/id/" + id + "/pos/" + pos,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {

					$('ul#mostPopularCode li').remove();
					var li = '';
					for(var i in json) {

						 	li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].shopId + "' id='" + json[i].id + "' >" + json[i].shop.name + "</li>";

						}
					$('ul#mostPopularCode').append(li);
					$('ul#mostPopularCode li#'+id).addClass('selected');
					$('ul#mostPopularCode li').click(changeSelectedClass);
				}


		});

	} else {

		bootbox.alert(__('Please select a Store from list'));
	}
}

/**
* confirmation for deletion
* @author kraj
* @version 1.0
*/

function deleteOne() {

	var flag =  '#deleteOne';
	//apply selected class on current button
	addSelectedClassOnButton(flag);
	var id = $('ul#mostPopularCode li.selected').attr('id');
	if(parseInt(id) > 0){
	bootbox.confirm(__("Are you sure you want to delete this Store?"),__('No'),__('Yes'),function(r){

		if(!r){

			//return false if not confimed
			return false;

		} else {
			//call to delete function
			deletePopularCode();
		}

	});
} else {

		bootbox.alert(__('Please select a Store from list'));
	}
}
/**
* delete popular code from list
* @author kraj
* @version 1.0
*/
function deletePopularCode() {

	var id = $('ul#mostPopularCode li.selected').attr('id');
	var pos = $('ul#mostPopularCode li.selected').attr('relpos');

		$.ajax({
			url : HOST_PATH + "admin/homepage/deletepopularshop/id/" + id + "/pos/" + pos,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {

					$('ul#mostPopularCode li').remove();
					var li = '';

				if(json!=''){
					for(var i in json)
						{
						 	li+= "<li class='ui-state-default' reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].shopId + "' id='" + json[i].id + "' >" + json[i].name + "</li>";

						}
					$('ul#mostPopularCode').append(li);
					$('ul#mostPopularCode li#'+id).addClass('selected');
					$('ul#mostPopularCode li').click(changeSelectedClass);
				} else {

					$('ul#mostPopularCode').append('<li id="noRecord">'+__('No record found')+'</li>');
				}

				}

		});

}

//*********************** End popular shop JS *****************************//




//*********************** JS fuctions for popular VoucherCode section by Er.kundal ***********************//



$(document).ready(function() {
	//call to function for selected class(button)
	addSelectedClassOnButton1(1);
	$("input#searchCodeTxt").keypress(function(e) {
		addSelectedClassOnButton1(1);
		$('ul#mostVoucherCode li').removeClass('selected');
				// if the key pressed is the enter key
				  if (e.which == 13) {

					  searchByTxt1();
				  }
		});
		//Auto complete search for top five records in a dropdown
		$("#searchCodeTxt").autocomplete({
	        minLength: 1,
	        source: function( request, response){

	        	$.ajax({
	        		url : HOST_PATH + "admin/homepage/searchtoptenoffer/keyword/" + $('#searchCodeTxt').val(),
	     			method : "post",
	     			dataType : "json",
	     			type : "post",
	     			success : function(data) {

	     				if (data != null) {

	     					//pass array of the respone in respone object of the autocomplete
	     					response(data);
	     				}
	     			},
	     			error: function(message) {

	     	            // pass an empty array to close the menu if it was initially opened
	     	            response([]);
	     	        }
	   		 });
	        },
	        select: function( event, ui ) {}
	    });
		//code for selection of li
		$('ul#mostVoucherCode li').click(changeSelectedClass1);

});


/**
 * search offer(Voucher Code) by text
 * @author Er.kundal
 * @version 1.0
 */
function searchByTxt1() {

	$("ul.ui-autocomplete").css('display','none');
	$("ul.ui-autocomplete").html('');
	//console.log('ok');
	//addSelectedClassOnButton(4);
	//$(this).addClass().addClass('btn-primary');
}
/**
 * change selected class of li
 * @author Er.kundal
 * @version 1.0
 */
function changeSelectedClass1() {

	$('ul#mostVoucherCode li').removeClass('selected');
	$(this).addClass('selected');
	//apply selected class on current button
	addSelectedClassOnButton1(2);
}
/**
 * change selected of button
 * @param mixed $flag
 * @author Er.kundal
 * @version 1.0
 */
function addSelectedClassOnButton1(flag) {


	if(flag==1){

		$('button#moveUpCode').removeClass('btn-primary');
		$('button#moveDownCode').removeClass('btn-primary');
		$('button#deleteCode').removeClass('btn-primary');
		$('button#addNewOffer').addClass('btn-primary');

	} else if(flag==2){

		$('button#moveUpCode').addClass('btn-primary');
		$('button#moveDownCode').addClass('btn-primary');
		$('button#deleteCode').addClass('btn-primary');
		$('button#addNewOffer').removeClass('btn-primary');

	} else {

		$('button#moveUpCode').removeClass('btn-primary');
		$('button#moveDownCode').removeClass('btn-primary');
		$('button#deleteCode').removeClass('btn-primary');
		$('button#addNewOffer').removeClass('btn-primary');
		$(flag).addClass('btn-primary');
	}
}
/**
 * move up element by one from list
 * @author kundal
 * @version 1.0
 */
function moveUpCode() {

	var flag =  '#moveUpCode';
	//apply selected class on current button
	addSelectedClassOnButton1(flag);
	//get id form slected li by attr
	var id = $('ul#mostVoucherCode li.selected').attr('id');
	//get postion form slected li by attr
	var pos = $('ul#mostVoucherCode li.selected').attr('relpos');
	if(parseInt(id) > 0){
		$.ajax({
			url : HOST_PATH + "admin/homepage/moveupcode/id/" + id + "/pos/" + pos,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {
					$('ul#mostVoucherCode li').remove();

					var li = '';
					for(var i in json)
						{
						 	li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].offerId + "' id='" + json[i].id + "' >" + json[i].offer.title + "</li>";

						}
					//append li in ul( list of popular code
					$('ul#mostVoucherCode').append(li);
					$('ul#mostVoucherCode li#'+id).addClass('selected');
					$('ul#mostVoucherCode li').click(changeSelectedClass1);
				}


		});

	} else {

		bootbox.alert(__('Please select an offer from list'));
	}
}
/**
 * add offer(Voucher Code) in offer list
 * @author Er.kundal
 * @version 1.0
 */
function addNewOffer() {

	var flag =  '#addNewOffer';
	//apply selected class on current button
	addSelectedClassOnButton1(flag);

	if($('ul#mostVoucherCode li').length > 25) {

		bootbox.alert(__('Popular Voucher code list can have maximum 25 records, please delete one if you want to add more popular voucher code'));

	} else {

		if($("input#searchCodeTxt").val()=='' || $("input#searchCodeTxt").val()==undefined)
			{
				//console.log('ok');
				bootbox.alert(__('Please select an offer'));

			} else {

				var offerName = $("input#searchCodeTxt").val();

				$.ajax({
	        		url : HOST_PATH + "admin/homepage/addoffercode/name/" + offerName,
	     			method : "post",
	     			dataType : "json",
	     			type : "post",
	     			success : function(data) {

	     				if(data=='2' || data==2)
	     					{
	     						bootbox.alert(__('This offer does not exist'));

	     					} else {
	     						$('ul#mostVoucherCode li#noRecord').remove();
	     						var li  = "<li reltype='" + data.type + "' relpos='" + data.position + "' reloffer='" + data.offerId + "' id='" + data.id + "' >" + offerName + "</li>";
	     						$('ul#mostVoucherCode').append(li);

	     						$('ul#mostVoucherCode li#'+ data.id).click(changeSelectedClass1);

	     						$("input#searchCodeTxt").val('');
	     					}

	     			}


				});
				//code add offer in list here
			}
	}
}
/**
 * move down element on one position from list
 * @author Er.kundal
 * @version 1.0
 */
function moveDownCode() {

	var flag =  '#moveDownCode';
	//apply selected class on current button
	addSelectedClassOnButton1(flag);
	var id = $('ul#mostVoucherCode li.selected').attr('id');
	var pos = $('ul#mostVoucherCode li.selected').attr('relpos');

	if(parseInt(id) > 0) {
		$.ajax({
			url : HOST_PATH + "admin/homepage/movedowncode/id/" + id + "/pos/" + pos,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {

					$('ul#mostVoucherCode li').remove();
					var li = '';
					for(var i in json) {

						 	li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].offerId + "' id='" + json[i].id + "' >" + json[i].offer.title + "</li>";

						}
					$('ul#mostVoucherCode').append(li);
					$('ul#mostVoucherCode li#'+id).addClass('selected');
					$('ul#mostVoucherCode li').click(changeSelectedClass1);
				}


		});

	} else {

		bootbox.alert(__('Please select an offer from list'));
	}
}

/**
 * confirmation for deletion
 * @author kraj
 * @version 1.0
 */

function deleteCode() {

	var flag =  '#deleteCode';
	//apply selected class on current button
	addSelectedClassOnButton1(flag);
	var id = $('ul#mostVoucherCode li.selected').attr('id');
	if(parseInt(id) > 0){
	bootbox.confirm(__("Are you sure you want to delete this code?"),__('No'),__('Yes'),function(r){

		if(!r){

			//return false if not confimed
			return false;

		} else {
			//call to delete function
			deleteVochercode();
		}

	});
} else {

		bootbox.alert(__('Please select an offer from list'));
	}
}
/**
 * delete popular code from list
 * @author kraj
 * @version 1.0
 */
function deleteVochercode() {

	var id = $('ul#mostVoucherCode li.selected').attr('id');
	var pos = $('ul#mostVoucherCode li.selected').attr('relpos');

		$.ajax({
			url : HOST_PATH + "admin/homepage/deletepopularvochercode/id/" + id + "/pos/" + pos,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {
					$('ul#mostVoucherCode li').remove();
					var li = '';
				if(json!=''){
					for(var i in json)
						{
						 	li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].offerId + "' id='" + json[i].id + "' >" + json[i].offer.title + "</li>";

						}
					$('ul#mostVoucherCode').append(li);
					$('ul#mostVoucherCode li#'+id).addClass('selected');
					$('ul#mostVoucherCode li').click(changeSelectedClass);
				} else {
					$('ul#mostVoucherCode').append('<li id="noRecord">'+__('No record found')+'</li>');
				}
				}


		});


}


//*********************************** End Voucher Code JS ********************************************//


//*********************** JS fuctions for popular Category section by Er.kundal ***********************//



$(document).ready(function() {
	//call to function for selected class(button)
	addSelectedClassOnButton2(1);
	$("input#searchcatgTxt").keypress(function(e) {
		addSelectedClassOnButton2(1);
		$('ul#mostCategories li').removeClass('selected');
				// if the key pressed is the enter key
				  if (e.which == 13) {

					  searchByTxt2();
				  }
		});
		//Auto complete search for top five records in a dropdown
		$("#searchcatgTxt").autocomplete({
	        minLength: 1,
	        source: function( request, response){

	        	$.ajax({
	        		url : HOST_PATH + "admin/homepage/searchtoptencategory/keyword/" + $('#searchcatgTxt').val(),
	     			method : "post",
	     			dataType : "json",
	     			type : "post",
	     			success : function(data) {

	     				if (data != null) {

	     					//pass array of the respone in respone object of the autocomplete
	     					response(data);
	     				}
	     			},
	     			error: function(message) {

	     	            // pass an empty array to close the menu if it was initially opened
	     	            response([]);
	     	        }
	   		 });
	        },
	        select: function( event, ui ) {}
	    });
		//code for selection of li
		$('ul#mostCategories li').click(changeSelectedClass2);

});




/**
* search Category by text
* @author Er.kundal
* @version 1.0
*/
function searchByTxt2() {

	$("ul.ui-autocomplete").css('display','none');
	$("ul.ui-autocomplete").html('');

	//addSelectedClassOnButton(4);
	//$(this).addClass().addClass('btn-primary');
}
/**
* change selected class of li
* @author Er.kundal
* @version 1.0
*/
function changeSelectedClass2() {

	$('ul#mostCategories li').removeClass('selected');
	$(this).addClass('selected');
	//apply selected class on current button
	addSelectedClassOnButton2(2);
}
/**
* change selected of button
* @param mixed $flag
* @author Er.kundal
* @version 1.0
*/
function addSelectedClassOnButton2(flag) {


	if(flag==1){

		$('button#moveUpCategory').removeClass('btn-primary');
		$('button#moveDownCategory').removeClass('btn-primary');
		$('button#deleteCategory').removeClass('btn-primary');
		$('button#addNewCategory').addClass('btn-primary');

	} else if(flag==2){

		$('button#moveUpCategory').addClass('btn-primary');
		$('button#moveDownCategory').addClass('btn-primary');
		$('button#deleteCategory').addClass('btn-primary');
		$('button#addNewCategory').removeClass('btn-primary');

	} else {

		$('button#moveUpCategory').removeClass('btn-primary');
		$('button#moveDownCategory').removeClass('btn-primary');
		$('button#deleteCategory').removeClass('btn-primary');
		$('button#addNewCategory').removeClass('btn-primary');
		$(flag).addClass('btn-primary');
	}
}
/**
* move up element by one from list
* @author kundal
* @version 1.0
*/
function moveUpCategory() {

	var flag =  '#moveUpCategory';

	//apply selected class on current button
	addSelectedClassOnButton2(flag);
	//get id form slected li by attr
	var id = $('ul#mostCategories li.selected').attr('id');

	//get postion form slected li by attr
	var pos = $('ul#mostCategories li.selected').attr('relpos');
	if(parseInt(id) > 0){
		$.ajax({
			url : HOST_PATH + "admin/homepage/moveupcategory/id/" + id + "/pos/" + pos,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {

					$('ul#mostCategories li').remove();
					var li = '';
					for(var i in json)
						{
						 	li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].categoryId + "' id='" + json[i].id + "' >" + json[i].name + "</li>";

						}
					//console.log(id);
					//append li in ul( list of popular code
					$('ul#mostCategories').append(li);
					$('ul#mostCategories').find('li#'+id).addClass('selected');
					$('ul#mostCategories li').click(changeSelectedClass2);
				}


		});

	} else {

		bootbox.alert(__('Please select a Category from list'));
	}
}
/**
* add Category(addNew Category) in popular Category list
* @author Er.kundal
* @version 1.0
*/
function addNewCategory() {

	var flag =  '#addNewCategory';
	//apply selected class on current button
	addSelectedClassOnButton2(flag);

	if($('ul#mostCategories li').length > 25) {

		bootbox.alert(__('Popular Categories list can have maximum 25 records, please delete one if you want to add more popular Category'));

	} else {

		if($("input#searchcatgTxt").val()=='' || $("input#searchcatgTxt").val()==undefined)
			{
				//console.log('ok');
				bootbox.alert(__('Please select a Category'));

			} else {

				var catgName = $("input#searchcatgTxt").val();

				$.ajax({
	        		url : HOST_PATH + "admin/homepage/addnewcategory/name/" + catgName,
	     			method : "post",
	     			dataType : "json",
	     			type : "post",
	     			success : function(data) {

	     				if(data=='2' || data==2)
	     					{
	     						bootbox.alert(__('This category does not exist'));

	     					} else {

	     						$('ul#mostCategories li#noRecord').remove();

	     						var li  = "<li reltype='" + data.type + "' relpos='" + data.position + "' reloffer='" + data.categoryId + "' id='" + data.id + "' >" + catgName + "</li>";
	     						$('ul#mostCategories').append(li);
	     						$('ul#mostCategories li#'+ data.id).click(changeSelectedClass2);
	     						$("input#searchcatgTxt").val('');
	     					}

	     			}


				});
				//code add searchcatgTxt in list here
			}
	}
}
/**
* move down element on one position from category list
* @author Er.kundal
* @version 1.0
*/
function moveDownCategory() {

	var flag =  '#moveDownCategory';
	//apply selected class on current button
	addSelectedClassOnButton2(flag);
	var id = $('ul#mostCategories li.selected').attr('id');
	var pos = $('ul#mostCategories li.selected').attr('relpos');

	if(parseInt(id) > 0) {
		$.ajax({
			url : HOST_PATH + "admin/homepage/movedowncategory/id/" + id + "/pos/" + pos,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {

					$('ul#mostCategories li').remove();
					var li = '';
					for(var i in json) {

						 	li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].categoryId + "' id='" + json[i].id + "' >" + json[i].name + "</li>";

						}
					$('ul#mostCategories').append(li);
					$('ul#mostCategories').find('li#'+id).addClass('selected');
					$('ul#mostCategories li').click(changeSelectedClass2);
				}


		});
	} else {

		bootbox.alert(__('Please select a Category from list'));
	}
}

/**
* confirmation for deletion
* @author kraj
* @version 1.0
*/

function deleteCategory() {

	var flag =  '#deleteCategory';
	//apply selected class on current button
	addSelectedClassOnButton2(flag);
	var id = $('ul#mostCategories li.selected').attr('id');
	if(parseInt(id) > 0){
	bootbox.confirm(__("Are you sure you want to delete this Category?"),__('No'),__('Yes'),function(r){

		if(!r){

			//return false if not confimed
			return false;

		} else {
			//call to delete function
			deletePopularCategory();
		}

	});
} else {

		bootbox.alert(__('Please select a Category from list'));
	}
}
/**
* delete popular code from list
* @author kraj
* @version 1.0
*/
function deletePopularCategory() {

	var id = $('ul#mostCategories li.selected').attr('id');
	var pos = $('ul#mostCategories li.selected').attr('relpos');

		$.ajax({
			url : HOST_PATH + "admin/homepage/deletepopularcategory/id/" + id + "/pos/" + pos,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {

					$('ul#mostCategories li').remove();
					var li = '';
					if(json!=''){
					for(var i in json)
						{
						 	li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].categoryId + "' id='" + json[i].id + "' >" + json[i].name + "</li>";

						}
					$('ul#mostCategories').append(li);
					$('ul#mostCategories li#'+id).addClass('selected');
					$('ul#mostCategories li').click(changeSelectedClass2);
				} else {

					$('ul#mostCategories').append('<li id="noRecord">'+__('No record found')+'</li>');
				}

				}
		});
}

//*********************** End popular Category JS ************************************//

//*********************** JS fuctions for Special List section by Er.kundal ***********************//



$(document).ready(function() {
	//call to function for selected class(button)
	addSelectedClassOnButton3(1);
	$("input#searchSpecialListTxt").keypress(function(e) {
		addSelectedClassOnButton3(1);
		$('ul#mostSpecialList li').removeClass('selected');
				// if the key pressed is the enter key
				  if (e.which == 13) {

					  searchByTxt3();
				  }
		});
		//Auto complete search for top five records in a dropdown
		$("#searchSpecialListTxt").autocomplete({
	        minLength: 1,
	        source: function( request, response){

	        	$.ajax({
	        		url : HOST_PATH + "admin/homepage/searchtoptenspecial/keyword/" + $('#searchSpecialListTxt').val(),
	     			method : "post",
	     			dataType : "json",
	     			type : "post",
	     			success : function(data) {

	     				if (data != null) {

	     					//pass array of the respone in respone object of the autocomplete
	     					response(data);
	     				}
	     			},
	     			error: function(message) {

	     	            // pass an empty array to close the menu if it was initially opened
	     	            response([]);
	     	        }
	   		 });
	        },
	        select: function( event, ui ) {}
	    });
		//code for selection of li
		$('ul#mostSpecialList li').click(changeSelectedClass3);

});


/**
* move up element by one from Special offer list
* @author kundal
* @version 1.0
*/
function moveUpSpecialoffer() {

	var flag =  '#moveUpSpecialoffer';
	//apply selected class on current button
	addSelectedClassOnButton3(flag);
	//get id form slected li by attr
	var id = $('ul#mostSpecialList li.selected').attr('id');
	//get postion form slected li by attr
	var pos = $('ul#mostSpecialList li.selected').attr('relpos');
	if(parseInt(id) > 0){
		$.ajax({
			url : HOST_PATH + "admin/homepage/moveupspecial/id/" + id + "/pos/" + pos,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {
					$('ul#mostSpecialList li').remove();
					var li = '';
					for(var i in json)
						{
						 	li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].specialpageId + "' id='" + json[i].id + "' >" + json[i].title + "</li>";

						}
					//append li in ul( list of popular code
					$('ul#mostSpecialList').append(li);
					$('ul#mostSpecialList').find('li#'+id).addClass('selected');
					$('ul#mostSpecialList li').click(changeSelectedClass3);
				}


		});

	} else {

		bootbox.alert(__('Please select a Special offer from list'));
	}
}
/**
* add Special offer(addNew Special offer) in Special offer list
* @author Er.kundal
* @version 1.0
*/
function addNewSpecialoffer() {

	var flag =  '#addNewSpecialoffer';
	//apply selected class on current button
	addSelectedClassOnButton3(flag);

	if($('ul#mostSpecialList li').length > 25) {

		bootbox.alert(__('Special offer list can have maximum 25 records, please delete one if you want to add more Special list'));

	} else {

		if($("input#searchSpecialListTxt").val()=='' || $("input#searchSpecialListTxt").val()==undefined)
			{
				bootbox.alert(__('Please select a Special offer'));

			} else {

				var specialOfferName = $("input#searchSpecialListTxt").val();

				$.ajax({
	        		url : HOST_PATH + "admin/homepage/addspecial/name/" + specialOfferName,
	     			method : "post",
	     			dataType : "json",
	     			type : "post",
	     			success : function(data) {

	     				if(data=='2' || data==2)
	     					{
	     						bootbox.alert(__('This special offer does not exist'));

	     					} else {
	     						$('ul#mostSpecialList li#noRecord').remove();

	     						var li  = "<li reltype='" + data.type + "' relpos='" + data.position + "' reloffer='" + data.specialpageId + "' id='" + data.id + "' >" + specialOfferName + "</li>";
	     						$('ul#mostSpecialList').append(li);
	     						$('ul#mostSpecialList li#'+ data.id).click(changeSelectedClass3);
	     						$("input#searchSpecialListTxt").val('');
	     					}

	     			}


				});
				//code add searchcatgTxt in list here
			}
	}
}

/**
* move down element on one position from Special offer list
* @author Er.kundal
* @version 1.0
*/

function moveDownSpecialoffer() {

	var flag =  '#moveDownSpecialoffer';

	//apply selected class on current button
	addSelectedClassOnButton3(flag);
	var id = $('ul#mostSpecialList li.selected').attr('id');
	var pos = $('ul#mostSpecialList li.selected').attr('relpos');

	if(parseInt(id) > 0) {
		$.ajax({
			url : HOST_PATH + "admin/homepage/movedownspecial/id/" + id + "/pos/" + pos,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {

					$('ul#mostSpecialList li').remove();

					var li = '';
					for(var i in json) {

						 	li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].specialpageId + "' id='" + json[i].id + "' >" + json[i].title + "</li>";

						}
					$('ul#mostSpecialList').append(li);
					$('ul#mostSpecialList').find('li#'+id).addClass('selected');
					$('ul#mostSpecialList li').click(changeSelectedClass3);
				}


		});
	} else {

		bootbox.alert(__('Please select a Special offer from list'));
	}
}

/**
* confirmation for deletion
* @author kraj
* @version 1.0
*/

function deleteSpecialoffer() {

	var flag =  '#deleteSpecialoffer';
	//apply selected class on current button
	addSelectedClassOnButton3(flag);
	var id = $('ul#mostSpecialList li.selected').attr('id');
	if(parseInt(id) > 0){
	bootbox.confirm(__("Are you sure you want to delete this Special offer?"),__('No'),__('Yes'),function(r){

		if(!r){

			//return false if not confimed
			return false;

		} else {
			//call to delete function
			deleteSpecialListOffer();
		}

	});
} else {

		bootbox.alert(__('Please select a Special offer from list'));
	}
}
/**
* delete Special List Offer from Special List
* @author kraj
* @version 1.0
*/
function deleteSpecialListOffer() {

	var id = $('ul#mostSpecialList li.selected').attr('id');
	var pos = $('ul#mostSpecialList li.selected').attr('relpos');

		$.ajax({
			url : HOST_PATH + "admin/homepage/deletespecial/id/" + id + "/pos/" + pos,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {

					$('ul#mostSpecialList li').remove();

					if(json != ''){
					var li = '';
					for(var i in json)
						{
						 	li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].specialpageId + "' id='" + json[i].id + "' >" + json[i].title + "</li>";

						}
					$('ul#mostSpecialList').append(li);
					$('ul#mostSpecialList li#'+id).addClass('selected');
					$('ul#mostSpecialList li').click(changeSelectedClass3);
					//$('ul#mostPopularCode li#'+ $('ul#mostPopularCode li.selected').attr('id')).remove();
					} else {
						$('ul#mostSpecialList').append('<li id="noRecord">'+__('No record found')+'</li>');
					}


				}


		});


}

/**
* search offer by text
* @author Er.kundal
* @version 1.0
*/
function searchByTxt3() {

	$("ul.ui-autocomplete").css('display','none');
	$("ul.ui-autocomplete").html('');

	//addSelectedClassOnButton(4);
	//$(this).addClass().addClass('btn-primary');
}
/**
* change selected class of li
* @author Er.kundal
* @version 1.0
*/
function changeSelectedClass3() {

	$('ul#mostSpecialList li').removeClass('selected');
	$(this).addClass('selected');
	//apply selected class on current button
	addSelectedClassOnButton3(2);
}
/**
* change selected of button
* @param mixed $flag
* @author Er.kundal
* @version 1.0
*/
function addSelectedClassOnButton3(flag) {


	if(flag==1){

		$('button#moveUpSpecialoffer').removeClass('btn-primary');
		$('button#moveDownSpecialoffer').removeClass('btn-primary');
		$('button#deleteSpecialoffer').removeClass('btn-primary');
		$('button#addNewSpecialoffer').addClass('btn-primary');

	} else if(flag==2){

		$('button#moveUpSpecialoffer').addClass('btn-primary');
		$('button#moveDownSpecialoffer').addClass('btn-primary');
		$('button#deleteSpecialoffer').addClass('btn-primary');
		$('button#addNewSpecialoffer').removeClass('btn-primary');

	} else {

		$('button#moveUpSpecialoffer').removeClass('btn-primary');
		$('button#moveDownSpecialoffer').removeClass('btn-primary');
		$('button#deleteSpecialoffer').removeClass('btn-primary');
		$('button#addNewSpecialoffer').removeClass('btn-primary');
		$(flag).addClass('btn-primary');
	}
}


//*********************** End Special List JS ************************************//



//*********************** JS fuctions for Articles section by Er.kundal ***********************//



$(document).ready(function() {
	//call to function for selected class(button)
	addSelectedClassOnButton4(1);
	$("input#searchArticlesTxt").keypress(function(e) {
		addSelectedClassOnButton4(1);
		$('ul#mostArticles li').removeClass('selected');
				// if the key pressed is the enter key
				  if (e.which == 13) {

					  searchByTxt4();
				  }
		});
		// Auto complete search for top five records in a dropdown
		$("#searchArticlesTxt").autocomplete({
	        minLength: 1,
	        source: function( request, response){

	        	$.ajax({
	        		url : HOST_PATH + "admin/homepage/searchtoptensaving/keyword/" + $('#searchArticlesTxt').val(),
	     			method : "post",
	     			dataType : "json",
	     			type : "post",
	     			success : function(data) {

	     				if (data != null) {

	     					//pass array of the respone in respone object of the autocomplete
	     					response(data);
	     				}
	     			},
	     			error: function(message) {

	     	            // pass an empty array to close the menu if it was initially opened
	     	            response([]);
	     	        }
	   		 });
	        },
	        select: function( event, ui ) {}
	    });
		//code for selection of li
		$('ul#mostArticles li').click(changeSelectedClass4);

});


/**
* move up element by one from Articles list
* @author Er.kundal
* @version 1.0
*/
function moveUpSaving() {

	var flag =  '#moveUpSaving';
	//apply selected class on current button
	addSelectedClassOnButton4(flag);
	//get id form slected li by attr
	var id = $('ul#mostArticles li.selected').attr('id');
	//get postion form slected li by attr
	var pos = $('ul#mostArticles li.selected').attr('relpos');
	if(parseInt(id) > 0){
		$.ajax({
			url : HOST_PATH + "admin/homepage/moveupsaving/id/" + id + "/pos/" + pos,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {

					$('ul#mostArticles li').remove();
					var li = '';
					for(var i in json)
						{
						 	li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "'reloffer='" + json[i].moneysaving.id + "' id='" + json[i].id + "' >" + json[i].moneysaving.title + "</li>";
						}
					//append li in ul( list of popular code
					$('ul#mostArticles').append(li);
					$('ul#mostArticles').find('li#'+id).addClass('selected');
					$('ul#mostArticles li').click(changeSelectedClass4);
				}
		});

	} else {
		bootbox.alert(__('Please select an Article from list'));
	}
}

/**
* add Article in money saving Articles
* @author Er.kundal
* @version 1.0
*/
function addNewSaving() {

	var flag =  '#addNewSaving';
	//apply selected class on current button
	addSelectedClassOnButton4(flag);

	if($('ul#mostArticles li').length > 25) {

		bootbox.alert(__('Article list can have maximum 25 records, please delete one if you want to add more Articles'));

	} else {

		if($("input#searchArticlesTxt").val()=='' || $("input#searchArticlesTxt").val()==undefined)
			{
				bootbox.alert(__('Please select an Article'));

			} else {

				var Name = $("input#searchArticlesTxt").val();

				$.ajax({
	        		url : HOST_PATH + "admin/homepage/addsaving/name/" + Name,
	     			method : "post",
	     			dataType : "json",
	     			type : "post",
	     			success : function(data) {

	     				if(data=='2' || data==2)
	     					{
	     						bootbox.alert(__('This article does not exist'));
	     					} else {
	     						$('ul#mostArticles li#noRecord').remove();

	     						var li  = "<li reltype='" + data.type + "' relpos='" + data.position + "' reloffer='" + data.articleId + "' id='" + data.id + "' >" + Name + "</li>";
	     						$('ul#mostArticles').append(li);
	     						$('ul#mostArticles li#'+ data.id).click(changeSelectedClass4);
	     						$("input#searchArticlesTxt").val('');
	     					}
	     				}
				});
				//code add searchcatgTxt in list here
			}
	}
}

/**
* move down element on one position from Articles
* @author Er.kundal
* @version 1.0
*/

function moveDownSaving() {

	var flag = '#moveDownSaving';

	//apply selected class on current button
	addSelectedClassOnButton4(flag);
	var id = $('ul#mostArticles li.selected').attr('id');
	var pos = $('ul#mostArticles li.selected').attr('relpos');

	if(parseInt(id) > 0) {
		$.ajax({
			url : HOST_PATH + "admin/homepage/movedownsaving/id/" + id + "/pos/" + pos,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {

					$('ul#mostArticles li').remove();
					var li = '';
					for(var i in json) {

						 	li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].moneysaving.id + "' id='" + json[i].id + "' >" + json[i].moneysaving.title + "</li>";

						}
					$('ul#mostArticles').append(li);
					$('ul#mostArticles').find('li#'+id).addClass('selected');
					$('ul#mostArticles li').click(changeSelectedClass4);
				}


		});
	} else {

		bootbox.alert(__('Please select an Article from list'));
	}
}

/**
* confirmation for deletion
* @author Er.kundal romy
* @version 1.0
*/

function deleteSaving() {

	var flag =  '#deleteSaving';
	//apply selected class on current button
	addSelectedClassOnButton4(flag);
	var id = $('ul#mostArticles li.selected').attr('id');
	if(parseInt(id) > 0){
	bootbox.confirm(__("Are you sure you want to delete this Article?"),__('No'),__('Yes'),function(r){

		if(!r){

			//return false if not confimed
			return false;

		} else {
			//call to delete function
			deletesavingmoney();
		}

	});
} else {

		bootbox.alert(__('Please select an Article from list'));
	}
}
/**
* delete Article from Article List
* @author Er.kundal
* @version 1.0
*/
function deletesavingmoney() {

	var id = $('ul#mostArticles li.selected').attr('id');
	var pos = $('ul#mostArticles li.selected').attr('relpos');

		$.ajax({
			url : HOST_PATH + "admin/homepage/deletesaving/id/" + id + "/pos/" + pos,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {

					$('ul#mostArticles li').remove();
					var li = '';
					if(json!=''){
					for(var i in json)
						{
						 	li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].moneysaving.id + "' id='" + json[i].id + "' >" + json[i].moneysaving.title + "</li>";

						}
					$('ul#mostArticles').append(li);
					$('ul#mostArticles li#'+id).addClass('selected');
					$('ul#mostArticles li').click(changeSelectedClass4);
				} else {

					$('ul#mostArticles').append('<li id="noRecord">'+__('No record found')+'</li>');
				}

				}


		});


}

/**
* search offer by text
* @author Er.kundal
* @version 1.0
*/
function searchByTxt4() {

	$("ul.ui-autocomplete").css('display','none');
	$("ul.ui-autocomplete").html('');

	//addSelectedClassOnButton(4);
	//$(this).addClass().addClass('btn-primary');
}
/**
* change selected class of li
* @author Er.kundal
* @version 1.0
*/
function changeSelectedClass4() {

	$('ul#mostArticles li').removeClass('selected');
	$(this).addClass('selected');
	//apply selected class on current button
	addSelectedClassOnButton4(2);
}
/**
* change selected of button
* @param mixed $flag
* @author Er.kundal
* @version 1.0
*/
function addSelectedClassOnButton4(flag) {


	if(flag==1){

		$('button#moveUpSaving').removeClass('btn-primary');
		$('button#moveDownSaving').removeClass('btn-primary');
		$('button#deleteSaving').removeClass('btn-primary');
		$('button#addNewSaving').addClass('btn-primary');

	} else if(flag==2){

		$('button#moveUpSaving').addClass('btn-primary');
		$('button#moveDownSaving').addClass('btn-primary');
		$('button#deleteSaving').addClass('btn-primary');
		$('button#addNewSaving').removeClass('btn-primary');

	} else {

		$('button#moveUpSaving').removeClass('btn-primary');
		$('button#moveDownSaving').removeClass('btn-primary');
		$('button#deleteSaving').removeClass('btn-primary');
		$('button#addNewSaving').removeClass('btn-primary');
		$(flag).addClass('btn-primary');
	}
}


//*********************** End Articles JS ************************************//


/**
* Save email for different locales
* @author Raman
* @version 1.0
*/
function saveEmailPerLocale(){

	if($("form#emailform").valid())
	{

		email = $("#emailPerLocale").val();
		$.ajax({
			url : HOST_PATH + 'admin/homepage/saveemail',
			type : 'post',
			dataType : 'json',
			data : {'emailperlocale' : email},
			success : function(obj){
				window.location.reload(true);
			}
		});
	}

}


$(function () {
    $( "#mostPopularCode" ).sortable();
    $( "#mostPopularCode" ).disableSelection();
	$( "#mostPopularCode" ).on( "sortstop", function( event, ui ) {
		var shopid = new Array();
		$('.ui-state-default').each(function(){
	        shopid.push($(this).attr('reloffer'));
	    });
		$('div.image-loading-icon').append("<img id='img-load' src='" +  HOST_PATH  + "/public/images/validating.gif'/>");
	    var shopid = shopid.toString();
		$.ajax({
	        type : "POST",
	        url : HOST_PATH + "admin/homepage/savepopularshopsposition",
	        method : "post",
	        dataType : 'json',
	        data: { shopid: shopid },
	        success : function(json) { 
				$('#img-load').remove();
				$( "#mostPopularCode" ).sortable( "refresh" );
				$( "#mostPopularCode" ).sortable( "refreshPositions" );
				$('ul#mostPopularCode li').remove();
					var li = '';

				if(json!=''){
					for(var i in json)
						{
						 	li+= "<li class='ui-state-default' reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].shopId + "' id='" + json[i].id + "' >" + json[i].name + "</li>";

						}
					$('ul#mostPopularCode').append(li);
					$('ul#mostPopularCode li').click(changeSelectedClass);
				}
				bootbox.alert(__('Popular shops successfully updated.'));
					setTimeout(function(){
					  bootbox.hideAll();
					}, 3000);
	        }
	    });

 	});

    'use strict';

    // Define the url to send the image data to
    var url = HOST_PATH +  'admin/homepage/update-header-image';

    // Call the fileupload widget and set some parameters
    $('#homepageBanner').fileupload({
        url: url,
        dataType: 'json',
		done: function (e, data) {

			// Add each uploaded file name to the #files list

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

			var retdata = data.result;

			if(retdata.status == 200 )
			{
				var img = new Image();


				var newSrc = PUBLIC_PATH_LOCALE + retdata.path + retdata.fileName ;

				img.onload = function() {

					//$("img" , "div.homepageBanner-content").attr('src',newSrc);
					$('div.homepageBanner-content').append('<img  src="' + newSrc + '">');
					

				};

				img.src = newSrc ;

				

			}


        },
        add:function (e, data) {

        	// validate file type is excel or not
        	var acceptFileTypes = /jpg|JPG|png|PNG|jpeg|JPEG/ ;

        	var fileName = data.originalFiles[0]['name'] ;
			if(!acceptFileTypes.test(data.originalFiles[0]['name'])) {
				$(".header-image-cont span.message").html(	__('Please upload only *.jpg or *.png file'))
				.addClass('error').removeClass('success');;

				return false;
			}

			// display message if file is valid
			$(".header-image-cont span.message").html(	__('valid file'))
				.addClass('success').removeClass('error');

			$("#update-header-image-btn", ".header-image-cont").show();

			// bind button click
			$("#update-header-image-btn", ".header-image-cont").off('click').on('click',function () {


				// confirm from user to update if yes then submit the file
				bootbox.confirm(__("By updating this image, you will overwrite the current header image on homepage! Are you sure you want to import?"),__('No'),__('Yes'),function(r){
					 if(r){

						 $("span#selected-filename", ".header-image-cont").html(fileName);
						 $('div.progress-file-detail', ".header-image-cont").show('fast');
						 data.submit();
						jQuery("#homepageBanner").hide();

					 }

				});


			});




        },

        progressall: function (e, data) {
            // Update the progress bar while files are being uploaded
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .bar', $(".header-image-cont")).css(
                'width',
                progress + '%'
            );
        }

  
    }) ;



    // Define the url to send the image data to
    var url = HOST_PATH +  'admin/homepage/update-widget-background-image';

    // Call the fileupload widget and set some parameters
    $('#homepageWidgetBackground').fileupload({
        url: url,
        dataType: 'json',
		done: function (e, data) {

			// Add each uploaded file name to the #files list

        	jQuery('#progress .bar', "div.widget-image-cont").css('width',  '100%');
        	
            jQuery("#update-widget-image-btn").off("click");
			setTimeout(function(){
				jQuery('.progress-file-detail', "div.widget-image-cont").slideUp('slow',function() {
					jQuery('#progress .bar', "div.widget-image-cont").css('width',  '0%');
					jQuery("#update-widget-image-btn", "div.widget-image-cont").hide();
					
					jQuery("#delete-widget-image-btn").show();
					$(".widget-image-cont span.message").html('');

				});
			},500);

			var $retdata = data.result;

			if($retdata.status == 200 )
			{
				var img = new Image();


				var newSrc = PUBLIC_PATH_LOCALE + $retdata.path + $retdata.fileName ;

				img.onload = function() {


				   $('div.homepageWidget-content').append('<img  src="' + newSrc + '">');

					//$("img" , "div.homepageWidget-content").attr('src',newSrc);
					

				};

				img.src = newSrc ;



			}


        },
        add:function (e, data) {

        	// validate file type is excel or not
        	var acceptFileTypes = /jpg|JPG|png|PNG|jpeg|JPEG/ ;

        	var fileName = data.originalFiles[0]['name'] ;
			if(!acceptFileTypes.test(data.originalFiles[0]['name'])) {
				$(".widget-image-cont span.message").html(	__('Please upload only *.jpg or *.png file'))
				.addClass('error').removeClass('success');;

				return false;
			}

			// display message if file is valid
			$(".widget-image-cont span.message").html(	__('valid file'))
				.addClass('success').removeClass('error');

			$("#update-widget-image-btn").show();

			// bind button click
			$("#update-widget-image-btn").off('click').on('click',function () {


				// confirm from user to update if yes then submit the file
				bootbox.confirm(__("By updating this image, you will overwrite the current header image on homepage! Are you sure you want to import?"),__('No'),__('Yes'),function(r){
					 if(r){

						 $("span#selected-filename", "div.widget-image-cont").html(fileName);
						 $('div.progress-file-detail', "div.widget-image-cont").show('fast');
						 data.submit();
						 jQuery("#homepageWidgetBackground").hide();
					 }

				});


			});




        },

        progressall: function (e, data) {
            // Update the progress bar while files are being uploaded
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .bar', "div.widget-image-cont").css(
                'width',
                progress + '%'
            );
        }

    }) ;
});
