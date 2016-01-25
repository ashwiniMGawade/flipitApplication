
jQuery.noConflict();
jQuery(document).ready(init);


var errorExists = {} ;
var flagT = 1;
var tile_id ='';

function init(){
    jQuery('#code-alert-queue').click(function(){
        ___addOverLay()
        var offerId = jQuery('#offerId').val();
        var shopId = jQuery('#selctedshop').val();
        jQuery.ajax({
            type : "POST",
            url: HOST_PATH + "admin/email/codealertqueue",
            dataType: 'json',
            method : "post",
            data: { shopId: shopId, offerId: offerId},
            success: function(dataSet)
            { 
                if(dataSet == 1) {
                    bootbox.alert(__("Code alert successfully added to the queue."));
                } else if(dataSet == 2){
                	bootbox.alert(__("Code alert addition to queue failed. Shop has not been favorited by any visitor yet."));
                } else {
                    bootbox.alert(__("Code alert Queue for this offer already exist."));
                }
                ___removeOverLay();
                return false;
            } 
        });
    });

	
	
	jQuery('#saleDiv').hide();
	//jQuery('ul#imgLiForTiles li a').mouseover(showHidedDiv);
	
	jQuery('form#menuForm').submit(function(){
		validateMenu();
		if(jQuery("form#menuForm").valid()){
    		submitForm();
    	}
		   return false;
		});
	
	jQuery('#tileUpload').fileupload({
	       url: HOST_PATH + 'admin/offer/onfileselect',
	       dataType : 'json',
	       /*done: function (e, data) {
	           console.log(data);
	      }*/
	       add: function (e, data) {
	   			jQuery('#submitButton').attr('disabled' ,"disabled");           
	   			data.submit();
	              
	           
	       },
	       done: function (e, data) {
	    	 jQuery("input[type=hidden]#hidimage").val(data.result.name);
	  		 jQuery("input[type=hidden]#hidimagepath").val(data.result.type);
	  		 jQuery("input[type=hidden]#hidimageext").val(data.result.path);
	  		 jQuery('#submitButton').removeAttr('disabled');
	       }  
	    /*   
		success: function(result)
		{    
			 jQuery("input[type=hidden]#hidimage").val(result.name);
			 jQuery("input[type=hidden]#hidimagepath").val(result.type);
			 jQuery("input[type=hidden]#hidimageext").val(result.path);
			 jQuery('#submitButton').removeAttr('disabled');
			 
		}*/
    });
	
	

	
	
	jQuery('#existingShop,#memberOnly').click(showExistDiv);
	jQuery('#notExistingShop').click(showNotExistDiv);
	
	var _data = '';
	CKEDITOR.replace( 'couponInfo',
				{
					//fullPage : true,
					////extraPlugins : 'wordcount',
					customConfig : 'config.js' ,  
					toolbar :  'BasicToolbar'  ,
					height : "300"
		});
	
	CKEDITOR.replace( 'termsAndcondition',
			{
				//fullPage : true,
				////extraPlugins : 'wordcount',
				customConfig : 'config.js' ,  
				toolbar :  'BasicToolbar'  ,
				height : "80"
	});
	
	
	/*jQuery('#extendedOfferTitle').NobleCount('#metaTitleLeft',{

		max_chars: 68
	});
	
	jQuery('#extendedOfferMetadesc').NobleCount('#metaDescLeft',{
		max_chars: 150
	});*/

    if (jQuery('#socialCodeSelection').val() == 1) {
        jQuery('#socialCode').addClass('new-blue');
    }
	
	jQuery('.word_count').each(function() {
        var input = '#' + this.id;
        var count = input + '_count';
        jQuery(count).show();
        word_count(input, count);
        jQuery(this).keyup(function() { word_count(input, count)});
    });
	
	jQuery('span.checkDate').click(changeDateFlag);
	jQuery("#whichshop").select2({placeholder: __("Select a Shop")});
	jQuery("#whichshop").change(function(){
		jQuery("#selctedshop").val(jQuery(this).val());
		jQuery('#deepLinkOnbtn').removeClass('btn-primary').siblings('button').addClass('btn-primary');
		jQuery('#offerRefUrl').val('').attr('disabled','disabled');
	});
      var options = {
				'maxCharacterSize': '',
				'displayFormat' : ''
		};
      	
      	jQuery('#extendedOfferTitle').textareaCount(options, function(data){
			jQuery('#metaTitleLeft').val(__("Extended offer meta title length ") + (data.input) + __(" Characters"));
		});
		jQuery('#extendedTitle').textareaCount(options, function(data){
			jQuery('#extendedTitleLeft').val(__("Extended title length ") + (data.input) + __(" Characters"));
		});
    	jQuery('#extendedOfferMetadesc').textareaCount(options, function(data){
			jQuery('#metaDescLeft').val(__("Extended offer meta description length ") + (data.input) + __(" Characters"));
		});
		jQuery('#addofferTitle').textareaCount(options, function(data){
			jQuery('#metaTextLeft').val(__("Offer title length ")  +(data.input) + __(" Characters"));
		});
 
		jQuery('#dp3').datepicker().on('changeDate' , validateStartEndTimestamp);
		jQuery('#dp4').datepicker().on('changeDate' , validateStartEndTimestamp);
		jQuery('#offerstartTime').timepicker({
            minuteStep: 5,
            template: 'modal',
            showSeconds: false,
            showMeridian: false,
            'afterUpdate'  : validateStartEndTimestamp
        });
		jQuery('#offerendTime').timepicker({
            minuteStep: 5,
            template: 'modal',
            showSeconds: false,
            showMeridian: false,
            'afterUpdate'  : validateStartEndTimestamp
        });
		
		jQuery('div.multiselect ul li').click(
				selectPagesInList);
	jQuery('.close').hide();	
	
	
	jQuery('#deepLinkOnbtn').click(function(){
		jQuery('#deepLinkOnbtn').addClass("btn-primary").siblings().removeClass("btn-primary");
		jQuery('#offerRefUrl').removeAttr("disabled");
		jQuery('#deepLinkStatus').attr("checked", "checked");
	//	getDeeplinkForShop(jQuery("#whichshop option:selected").val());
	});
	
	jQuery('#deepLinkoofbtn').click(function(){
		jQuery('#deepLinkoofbtn').addClass("btn-primary").siblings().removeClass("btn-primary");
		jQuery('#offerRefUrl').attr("disabled", "disabled");
		jQuery('#deepLinkStatus').removeAttr("checked");
		jQuery('#offerRefUrl').val('');
	});
		
	getOfferDetail();	
	jQuery("form").submit(function(){
		
		if (! jQuery.isEmptyObject(invalidForm) ) 
			
			for(var i in invalidForm)
			{
				if(invalidForm[i])
				{
					errorExists['fileType'] = false ;
					break;
				} else {
					
					errorExists['fileType'] = true ;
				}
				
			}
		
		if(jQuery("input[name=printableCheckbox]").is(":checked") && jQuery("input[name=uploadoffercheck]").is(":checked") && !jQuery('#printableOfferFileName').val()) {
			if (!jQuery('input[name=uploadoffer]').val()) {
					jQuery("div.uploadOfferMessage").removeClass("focus")
					 .html(__("<span class='error help-inline'>Please upload jpg or pdf file</span>"));
					errorExists['uploadoffer'] = false ;
			} else {
				errorExists['uploadoffer'] =  true ;
			}
		} else {
			jQuery("div.uploadOfferMessage").removeClass("focus").html("");
		}
		if(jQuery('#news').hasClass('btn-primary') == false){
			validateStartEndTimestamp(); 
		}
		for(var i in errorExists)
		{
			if( errorExists[i] != true )
			{
				return false ;
			}
			
		}
 		
	});
	
	jQuery("#discountamount,#maxoffertxt").keydown(function(event) {
		   if(event.shiftKey)
		   {
		        event.preventDefault();
		   }

		   if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 188 || event.keyCode == 110)    {
		   }
		   else {
		        if (event.keyCode < 95) {
		          if (event.keyCode < 48 || event.keyCode > 57) {
		                event.preventDefault();
		          }
		        }
		        else {
		              if (event.keyCode < 96 || event.keyCode > 105) {
		                  event.preventDefault();
		              }
		        }
		      }
		   });
	
	validateFormAddNewOffer();
	jQuery('button#prefillData').click(function(){
		setDefaultTitle(this);
	});
	jQuery('#couponInfo').val(_data);
	jQuery('button#optionsOnbtn').click(function(){
      jQuery("#optionsOnbtn").addClass("btn-primary").siblings().removeClass("btn-primary");
      jQuery('#offerOption').show();
      jQuery('#offerButtons').hide();
  	});

  	jQuery('button#optionsoffbtn').click(function(){
      jQuery("#optionsoffbtn").addClass("btn-primary").siblings().removeClass("btn-primary");
      jQuery('#offerOption').hide();
      jQuery('#offerButtons').show();
  	});
}
function showNotExistDiv()
{
	jQuery('#notExistingShop').addClass("btn-primary").siblings().removeClass("btn-primary");
	jQuery('#fromWhichShop').removeAttr("checked");
	jQuery('#fromWhichShop').val(0);
	jQuery('#shopDetailDiv').hide();
	jQuery('#notExistShop').show();
	jQuery("input#notExistingShopCheckbox").attr('checked' , 'checked');   // check coupon code checkbox if  discount type coupon code
    jQuery("input#existingShopCheckbox").removeAttr('checked');
}
function showExistDiv()
{
	jQuery('#existingShop').addClass("btn-primary").siblings().removeClass("btn-primary");
	jQuery('#fromWhichShop').attr("checked", "checked");
	jQuery('#fromWhichShop').val(1);
	jQuery('#shopDetailDiv').show();
	jQuery('#notExistShop').hide();
	jQuery("input#existingShopCheckbox").attr('checked' , 'checked');   // check coupon code checkbox if  discount type coupon code
    jQuery("input#notExistingShopCheckbox").removeAttr('checked');
}
function setDefaultTitle(el)
{   
	var discountValue = 0;
	if(jQuery("#discountamount").val() != ''){
		discountValue = jQuery("#discountamount").val();
	}
	var shopName = jQuery("#whichshop option:selected").text();
	//alert(shopName); 
	var textLen1 = ('At the value of \u20AC ') +discountValue+ __(' at ')  +shopName;
	textLength1 = textLen1.length;
	var textLen2 = ('At the value of ') +discountValue+ __('% at ')  +shopName;
	textLength2 = textLen2.length;
	
	 
		if(jQuery('#euro').hasClass('btn-primary')){
			jQuery("#addofferTitle, #extendedOfferTitle, #extendedOfferRefurl").val(__('At the value of ') + ' \u20AC'+discountValue+ __(' at ') +' '+shopName);
			jQuery("#metaTextLeft").val( __("Offer title length") + " " + textLength1 + __(' characters'));
			jQuery("#metaTitleLeft").val( __("Extended offer meta title length") + " " + textLength1 + __(' characters'));
			
			var string = jQuery('#extendedOfferTitle').val().replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			jQuery("#extendedOfferRefurl").val(string.toLowerCase());
			jQuery("#extendedOfferRefurl").valid();
			
		}
		if(jQuery('#percentage').hasClass('btn-primary')){
			jQuery("#addofferTitle, #extendedOfferTitle, #extendedOfferRefurl ").val(__('At the value of ') +' '+discountValue+ __('% at ') +' '+shopName);
			jQuery("#metaTextLeft").val( __("Offer title length ") + textLength2 + __(' characters'));
			jQuery("#metaTitleLeft").val( __("Extended offer meta title lengt h") + textLength2 + __(' characters'));
			
			var string = jQuery('#extendedOfferTitle').val().replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
			jQuery("#extendedOfferRefurl").val(string.toLowerCase());
			jQuery("#extendedOfferRefurl").valid();
			
		}
	
}
jQuery.fn.multiselect = function() {
	
	jQuery(this).each(function() {
		var checkboxes = jQuery(this).find("input:checkbox");
		
		checkboxes.each(function() {
			var checkbox = jQuery(this);
			
			// Highlight pre-selected checkboxes
			if (checkbox.attr("checked"))
				checkbox.parent().addClass("selected");

			// Highlight checkboxes that the user selects
		/*	checkbox.click(function() {
				if (checkbox.attr("checked"))
					checkbox.parent().addClass("multiselect-on");
				else
					checkbox.parent().removeClass("multiselect-on");
			}); */
		});
	});
};

function gethtmlAddmorenews(param){
	
	var passvalue = "passwordmd5";
		 
			jQuery.ajax({
			   url: HOST_PATH + "admin/offer/addmorenews/",
			   //dataType: 'json',
			   cache: true,
			   data: "secret="+param+"&jcode="+passvalue+"&randval="+Math.random(),
				   success: function(dataSet)
				   { 
					   
					   jQuery('#newsDiv').append(dataSet);
					   return false;
				  } 
			});			   
				   
}

function setApprovedStatus(){
    jQuery('input#approveSocialCode').val(1);
}

function getDeeplinkForShop(param){
	
		 
			jQuery.ajax({
			   url: HOST_PATH + "admin/offer/shopdetail",
			   dataType: 'json',
			   data: {'shopId' : param},
			   success: function(dataSet)
			   {
				   if(dataSet[0].deepLinkStatus){
					   jQuery('#offerRefUrl').val(dataSet[0].deepLink).removeAttr('disabled');
					   jQuery('#deepLinkOnbtn').addClass('btn-primary').siblings('button').removeClass('btn-primary');
				   }
				   /*else{
					   jQuery('#deepLinkOnbtn').removeClass('btn-primary').siblings('button').addClass('btn-primary');
					   jQuery('#offerRefUrl').val('').attr('disabled','disabled');
				   }*/
					
			   } 
			});			   
				   
}



function getOfferDetail(){
	
	 jQuery.ajax({
			url : HOST_PATH + "admin/offer/offerdetail/offerId/" + jQuery('#offerId').val(),
				dataType : "json",
				success : function(data) {
					if (data != null) {
						setFormData(data);
						
					} 
				},
				error: function(message) {
		            // pass an empty array to close the menu if it was initially opened
		           // response([]);
		        }

			 });
		
}


function setFormData(data){
    var shopClassifications = { 1:'A', 2:'A+', 3:'AA', 4:'AA+', 5:'AAA'};
    jQuery('span#shopRating span').html(shopClassifications[data[0].shopClassification]);
	jQuery('span#newsDiv').append('<div class="mainpage-content-right-inner-left"><label><strong>' +__("Title") +'</strong></label></div><div class="mainpage-content-right-inner-right-other"></div><div class="mainpage-content-right-inner-right-full"><input type="text" name="newsTitle[]" value="" placeholder="' +__('News title') +'" class="span3"></div><div class="mainpage-content-right-inner-left"><label><strong>'+__("Ref URL")+'</strong></label></div><div class="mainpage-content-right-inner-right-other"></div><div class="mainpage-content-right-inner-right-full"><input type="text" name="newsrefUrl[]" value="" id="newsrefUrl" disabled="disabled" placeholder="' +__('Ref.Url') +'" class="span3 ignore" style="width:330px !important;">&nbsp;&nbsp;&nbsp;'+__("Deeplinking is") +' <button class="btn" id="newsdeepLinkOnbtn" name="newsdeepLinkOnbtn" type="button" style="border-radius: 4px 0 0 4px;" onclick="newschangelinkStatus(this)">'+__("On") +'</button><button id="newsdeepLinkoofbtn" name="newsdeepLinkoofbtn" style="border-radius: 0 4px 4px 0;" onclick="newschangelinkStatus(this)" class="btn mr10 btn-primary"  type="button">'+__("Off") +'</button><input type="checkbox" style="display:none;" id="newsdeepLinkStatus" value="1" name="newsdeepLinkStatus[]"></div><div class="mainpage-content-right-inner-left"><label><strong>'+__("Description") +'</strong></label></div><div class="mainpage-content-right-inner-right-other"></div><div class="mainpage-content-right-inner-right-full"><textarea rows="4" cols="3" name="newsDescription[]" id="newsDescription"></textarea></div>');
	// populate offer type data based based on offer type
	if(data[0].Visability == 'MEM'){
		jQuery("#memberOnly").click();
		
		if(data[0].shopExist ==true || data[0].shopExist=='1'){
			
			 //code 
			 //0.showexitdiv
				showExistDiv();
				jQuery('#selctedshop').val(data[0].shopId);
				jQuery('#whichshop').select2("val", data[0].shopId);
				jQuery('input.spancombo').val(data[0].shopName);
			 //1.show dropdown for shop
			 //2. set value drp
		     
		 }else{
			 
			 showNotExistDiv();
			 jQuery('input#newShop').val(data[0].shopName);
			 //fill values in field
		 }
		
	}else{
		 jQuery("#default").click();
		 if(data[0].shopId){
			 jQuery('#selctedshop').val(data[0].shopId);
			 jQuery('#whichshop').select2("val", data[0].shopId);
			 jQuery('input.spancombo').val(data[0].shopName);
			 if(data[0].notes!=''){
				jQuery('#aboutShopNoteDiv').show();
				jQuery('#shopNotes').html(data[0].notes).addClass('alert-error');
			 }
			 jQuery('#aboutShopDiv').show();
		     if(data[0].accountManagerName!=''){
				jQuery('#aboutManagerDiv').show();
				jQuery('#shopManager').html(data[0].accountManagerName);
			 }
			 if(data[0].affname){
				jQuery('#aboutNertworkDiv').show();
				jQuery('#shopNetwork').html(data[0].affname);
			 }

			 // check if selected shop has restrcited content or not
			// if yes then disable submit button and ask check to accept term and conditions
			if(data[0].strictConfirmation) {

				jQuery("#updateOfferBtn").addClass("disabled").attr('disabled','disabled');
				jQuery(".strict-confirmation-alert").show();
				jQuery('#enableSaveButtons').removeAttr('checked');

				// bind enable and disbale buttons event on checbox only when strcit confrimation is on 	
				jQuery('#enableSaveButtons').click(function(){

					if(data[0].strictConfirmation){

						if(jQuery(this).is(':checked')){
							jQuery("#updateOfferBtn").removeClass("disabled").removeAttr('disabled','disabled');
						} else{
							jQuery("#updateOfferBtn").addClass("disabled").attr('disabled','disabled');
						}
					}
				});


			} else{

				jQuery("#addOfferBtn,#saveAndAddnew").removeClass("disabled").removeAttr('disabled','disabled');
				jQuery(".strict-confirmation-alert").hide();
			}
	   }	 
	}
	
		jQuery("#couponCodeType").val(data[0].couponCodeType);
		
		if(data[0].couponCodeType == 'UN')
		{
			couponType('unique');
		} else {
			couponType('general');
		}
		
		
		
		jQuery("p.avail strong").html(data[0].available);
		jQuery("p.used strong").html(data[0].used);
		
		$totalCodes = parseInt(data[0].used) + parseInt(data[0].available) ;
		jQuery("p.total strong").html($totalCodes);
		
		
	
	// populate discount type data based based on discount type
   // console.log(data[0].tiles);
   // console.log('aaa' +  data[0].tiles.id);
	if(data[0].tiles!= null){
		
		tile_id = data[0].tilesId;
	}
	
	if(data[0].discountType=='CD'){
		jQuery("#couponCode").click();
		jQuery('#couponCodeTxt').val(data[0].couponCode);
		//$('#couponInfo').val(data[0].extendedFullDescription);
		var catCount = data.length;
		for(var i=0 ; i< catCount ; i++ ){
			// Add class by Er.kundal for select catg of shop
			jQuery("#categoryBtn-"+data[i].categoryId).addClass('btn-primary');
			jQuery("input#category-" + data[i].categoryId).attr('checked' , 'checked');
		}

	if(data[0].extendedOffer==true){

		
		 jQuery('#extendedYes').click();
		 if(data[0].extendedTitle != null && data[0].extendedTitle != ''){
			// alert(parseInt(data[0].extendedTitle.length));
			 jQuery('#extendedOfferTitle').val(data[0].extendedTitle);
			 jQuery('#metaTitleLeft').val(__("Extended offer meta title length ") + parseInt(data[0].extendedTitle.length) + __(" characters"));
			jQuery('#extendedOfferTitle').NobleCount('#metaTitleLeft',{
				max_chars: 68,
				prefixString : __("Extended offer meta title length ")
			});
	//jQuery('#metaTitleLeft').val((data.input) + __(" characters"));
			//jQuery('span#metaTitleLeft').text(68-parseInt(data[0].extendedTitle.length) + __(' characters remaining'));
		 }

		if(data[0].extendedTitle != null && data[0].extendedTitle != ''){
			// alert(parseInt(data[0].extendedTitle.length));
			 jQuery('#extendedTitle').val(data[0].extendedoffertitle);
			 jQuery('#extendedTitleLeft').val(__("Extended title length ") + parseInt(data[0].extendedTitle.length) + __(" characters"));
			 //jQuery('#metaTitleLeft').val((data.input) + __(" characters"));
			//jQuery('span#metaTitleLeft').text(68-parseInt(data[0].extendedTitle.length) + __(' characters remaining'));
		 }
		 if(data[0].extendedMetaDescription != null && data[0].extendedMetaDescription != ''){
			 jQuery('#extendedOfferMetadesc').val(data[0].extendedMetaDescription);
			 jQuery('#metaDescLeft').val(__("Extended offer meta description length ") + parseInt(data[0].extendedMetaDescription.length) + __(" characters"));
			jQuery('#extendedOfferMetadesc').NobleCount('#metaDescLeft',{
				max_chars: 150,
				prefixString : __("Extended offer meta description length ")
			});
	// jQuery('span#metaDescLeft').text(150-parseInt(data[0].extendedMetaDescription.length) + __(' characters remaining'));

		 }
		
		 jQuery('[name=extendedOfferRefurl]').val(data[0].extendedUrl);
		 
		 if(data[0].extendedFullDescription != null && data[0].extendedFullDescription != ''){
			 
			 //console.log(data[0].extendedFullDescription);
			 _data = data[0].extendedFullDescription;
			 CKEDITOR.instances['couponInfo'].setData(_data);
			 //jQuery('textarea#couponInfo').val(data[0].extendedFullDescription);
//			 jQuery('#metacouponInfo').val(__('Characters Left: ')+ (255-parseInt(data[0].extendedFullDescription.length)));
		 }
	}
	}
	if(data[0].discountType=='SL' || data[0].discountType=='PR' || data[0].discountType=='PA'){
		var catCount = data.length;
		for(var i=0 ; i< catCount ; i++ ){
			// Add class by Er.kundal for select catg of shop
			jQuery("#categoryBtn-"+data[i].categoryId).addClass('btn-primary');
			jQuery("input#category-" + data[i].categoryId).attr('checked' , 'checked');
		}
	}	
	if(data[0].discountType=='CD'){
		jQuery("#couponCode").click();
		jQuery('#couponCodeTxt').val(data[0].couponCode);
		//$('#couponInfo').val(data[0].extendedFullDescription);

		// Add class by Er.kundal for select catg of shop
		
		
		if(data[0].discountvalueType=='1'){
			 jQuery('#euro').addClass("btn-primary").siblings().removeClass("btn-primary");
			 jQuery('#discountchk').val('1');
			 jQuery('#discountamount').val(data[0].discount);
		}else if(data[0].discountvalueType=='2'){
			 jQuery('#percentage').addClass("btn-primary").siblings().removeClass("btn-primary");
			jQuery('#discountchk').val('2');
			jQuery('#discountamount').val(data[0].discount);
		}else{
			jQuery('#offdiscount').addClass("btn-primary").siblings().removeClass("btn-primary");
			jQuery('#discountchk').val('0');
			jQuery('#discountamount').attr('disabled', 'disabled');
		}
		
	 }
	
	else if(data[0].discountType=='PA') {
		jQuery("#printable").click();
		if(data[0].refOfferUrl){
              jQuery("#userefurlOption").click();
              jQuery('#offerrefurlPR').val(data[0].refOfferUrl);
		} else {
		    if(data[0].imageName){
                jQuery('#printableOfferFileName').val(data[0].imageName);
                if (data[0].imageType !== 'pdf') {
                    var image = data[0].path+"/thum_"+data[0].imageName;
                    var imgSrc = PUBLIC_PATH_LOCALE + image;
                    jQuery('span#offerLogoId').append('<img src="'+imgSrc+'" id="uplodedOffer" alt="uploaded offer" title="uploaded offer">');
                }
                else {
                    var image = data[0].path+data[0].imageName;
                    var imgSrc = PUBLIC_PATH_LOCALE + image;
                    jQuery('span#offerLogoId').append('<p><a href="'+imgSrc+'" target="_blank">'+data[0].imageName+'</a></p>');
                }
                jQuery("#uploadOfferOption").click();
		    }
	    }
    }
	else if(data[0].discountType=='NW'){
		jQuery("#visibiliyDiv").hide();
	    jQuery("#offertitledetail").hide();
	    jQuery("#datesdiv").hide();
	    jQuery("#attachpagesDiv").hide();
	    jQuery("#extendedOfferDiv").hide();
		jQuery("#news").click(); 
	
		var newsCount = data[0].newsContent.length;
		if(newsCount >0)
			{
				jQuery('span#newsDiv').empty();
			}
		for(var n=0;n<newsCount;n++){
			 if(newsCount>0){
				 if(n!=0){
				 	jQuery('span#newsDiv').append('<div class="clear line"></div>');
			 	 }
			 jQuery('span#newsDiv').append('<div class="mainpage-content-right-inner-left"><label><strong>'+__("Title") +'</strong></label></div><div class="mainpage-content-right-inner-right-other"></div><div class="mainpage-content-right-inner-right-full"><input type="text" name="newsTitle[]" value="'+data[0].offernews[n].title+'" placeholder="' +__('News title') +'" class="span3"></div><div class="mainpage-content-right-inner-left"><label><strong>' +__("Ref URL")+'</strong></label></div><div class="mainpage-content-right-inner-right-other"></div><div class="mainpage-content-right-inner-right-full"><input type="text" name="newsrefUrl[]" value="'+data[0].offernews[n].url+'" id="newsrefUrl" disabled="disabled" placeholder="' +__('Ref.Url') +'" class="span3 ignore" style="width:330px !important;">&nbsp;&nbsp;&nbsp;'+__("Deeplinking is")+' <button class="btn" id="newsdeepLinkOnbtn" name="newsdeepLinkOnbtn" type="button" style="border-radius: 4px 0 0 4px;" onclick="newschangelinkStatus(this)">'+__("On")+'</button><button id="newsdeepLinkoofbtn" name="newsdeepLinkoofbtn" style="border-radius: 0 4px 4px 0;" onclick="newschangelinkStatus(this)" class="btn mr10 btn-primary"  type="button">'+__('Off')+'</button><input type="checkbox" style="display:none;" id="newsdeepLinkStatus" value="1" name="newsdeepLinkStatus[]"></div><div class="mainpage-content-right-inner-left"><label><strong>'+__("Description")+'</strong></label></div><div class="mainpage-content-right-inner-right-other"></div><div class="mainpage-content-right-inner-right-full"><textarea rows="4" cols="3" name="newsDescription[]" id="newsDescription">'+data[0].offernews[n].content+'</textarea></div>');
			 
			 
			 if (data[0].offernews[n].linkstatus != '1')
				{
				 jQuery('button#newsdeepLinkoofbtn').click();
				}else{
				 jQuery('button#newsdeepLinkOnbtn').click();
				}
			
			 }
		 }
		 
			
	}
	else{
		  jQuery("#sale").click();
		  
	 }

	if (data[0].title != undefined || data[0].title != null) {
	 	jQuery('#addofferTitle').val(data[0].title);
	 	jQuery('#metaTextLeft').val(__("Offer title length ") + parseInt(data[0].title.length) + __(" characters"));
	}
	if (data[0].offer_position != undefined || data[0].offer_position != null) {
	 	jQuery('#offerPosition').val(data[0].offer_position);
	}
	 jQuery('#offerRefUrl').val(data[0].refURL);
	 if(data[0].refURL){
		 jQuery('#deepLinkOnbtn').click(); 
	 }else{
		 jQuery('#deepLinkoofbtn').click(); 
	 }
	 //console.log(data[0].termandcondition.length);
	 CKEDITOR.instances['termsAndconditions'].setData(data[0].termsAndconditionContent);
	 var termsCount = data.length;
	
		for(var i=0;i<termsCount;i++){
			 if(i>4){
			    jQuery('#addmoreBtn').click();
			 }
		 }


	 if(data[0].startDate != null && data[0].endDate != null){
			 dateStart = data[0].startDate.date.split(' ');
			 dateEnd = data[0].endDate.date.split(' ');
		
			 jQuery('input#offerStartDate').val(changeDateFormat(dateStart[0]));
			 jQuery('input#offerEndDate').val(changeDateFormat(dateEnd[0]));
			
			 jQuery('#offerstartTime').val(dateStart[1].substring(0, 5));
			 jQuery('#offerendTime').val(dateEnd[1].substring(0, 5));
	 }
	 
	if(data[0].exclusiveCode){
		jQuery('#exclusivebtn').addClass('btn-primary');
		jQuery('input#exclusivecheckbox').attr('checked', 'checked') ;
	}else if(data[0].editorPicks){
	    jQuery('#editorpicbtn').addClass('btn-primary'); 
	    jQuery('input#editorpickcheckbox').attr('checked', 'checked') ;
	} else if (data[0].userGenerated == 1) {
        jQuery('#socialcodebtn').addClass('btn-primary'); 
        jQuery('input#socialcodecheckbox').attr('checked', 'checked');
	 } else {
		 jQuery('#nonebtn').addClass('btn-primary'); 
		 jQuery('input#nonecheckbox').attr('checked', 'checked') ;
	 }
	
	if(parseInt(data[0].maxlimit)){
		jQuery('#maxlimityes').click();
		jQuery('#maxoffertxt').removeAttr('disabled');
		jQuery('#maxoffertxt').val(data[0].maxcode);
		jQuery("input#maxoffercheckbox").attr('checked' , 'checked');
	}

	if(data[0]!=undefined && data[0]!=null && data[0].tilesId){
		jQuery('a#selectImg_' + data[0].tilesId).show();
		selectOfferImage(data[0].tilesId);
		//jQuery("input#selectedTiles").attr('checked' , 'checked').valid();
	}
	//getAllTiles();
	
	//selectOfferImage(tile_id);
	var fvshopId = jQuery('#selctedshop').val();
	jQuery("#code-alert-visitors-count").text('Updating...');
	jQuery.ajax({
		url : HOST_PATH + "admin/offer/favouriteshopdetail/shopId/" + fvshopId,
			dataType : "json",
			success : function(data) {
			jQuery("#code-alert-visitors-count").text(data);
			},
			error: function(message) {
	        }
	});
	jQuery('#ccode').html('<label><strong>Coupon code</strong></label>');
    getShopDetail(jQuery("#selctedshop").val());
}

function newschangelinkStatus(el)
{
	jQuery(el).addClass('btn-primary').siblings('button').removeClass('btn-primary active') ;	
	if (jQuery(el).attr('name') == 'newsdeepLinkOnbtn')
	{
		jQuery("input[type=checkbox]" , jQuery(el).parent("div")).attr('checked' , 'checked').val(1);
		//jQuery('#newsdeepLinkOnbtn').addClass("btn-primary").siblings().removeClass("btn-primary");
		jQuery("#newsrefUrl" , jQuery(el).parent("div")).removeAttr("disabled");
		//jQuery('#newsdeepLinkStatus').attr("checked", "checked");
	} else
	{
		 jQuery("input[type=checkbox]" , jQuery(el).parent("div")).removeAttr('checked').val(0);
		//jQuery('#newsdeepLinkoofbtn').addClass("btn-primary").siblings().removeClass("btn-primary");
		 jQuery("#newsrefUrl" , jQuery(el).parent("div")).attr("disabled", "disabled");
		//jQuery('#newsdeepLinkStatus').removeAttr("checked");
		//jQuery("#newsrefUrl").parent("div").removeClass("error success focus").prev("div").html("");
	}
}



/**
 * When admin select website for user then selected class apply on selected list
 * in websiet multiselect then use this function
 * @author kraj
 */


function selectPagesInList() {

	if ((jQuery(this).children('input')).is(':checked')) {

		jQuery(this).children('input').removeAttr('checked');
		jQuery(this).removeClass('selected');

	} else {

		jQuery(this).children('input').attr('checked', 'checked');
		jQuery(this).addClass('selected');
	}
}

function getShopDetail(value){

	if(value != "") {

		jQuery('#selctedshop,#selctedPrvshop').val(value);
		//jQuery('#aboutShopDiv,#aboutShopNoteDiv,#aboutManagerDiv,#aboutNertworkDiv').hide();
	    jQuery.ajax({
			url : HOST_PATH + "admin/offer/shopdetail/shopId/" + value,
				dataType : "json",
				success : function(data) {
					if (data != null) {

						if(data[0].notes != '' && data[0].notes != null){

							jQuery('#aboutShopNoteDiv').show();
							jQuery('#shopNotes').html(data[0].notes).addClass('alert-error');;
						} else {
							jQuery('#shopNotes').html('&nbsp;')
												  .removeClass('alert-error');
						}


						if(data[0].affname != '' && data[0].affname != null){
							jQuery('#aboutNertworkDiv').show();
							jQuery('#shopNetwork').html(data[0].affname).addClass('alert-error');;
						} else {
							jQuery('#shopNetwork').html('&nbsp;')
												  .removeClass('alert-error');
						}

						
						jQuery('#aboutShopDiv').show();
						
						jQuery('#categoryListdiv').each(function() {
							var checkboxes = jQuery(this).find("input:checkbox");
							
							//console.log(bnt);
							 
							checkboxes.each(function() {
								var checkbox = jQuery(this);
								if (checkbox.attr("checked")){
									jQuery('#categoryBtn-'+checkbox.val()).click();
								} 
							});	
						});		
	 
							
							// check if selected shop has restrcited content or not
							// if yes then disable submit button and ask check to accept term and conditions
							if(data.strictConfirmation) {

								jQuery("#updateOfferBtn").addClass("disabled").attr('disabled','disabled');
								jQuery(".strict-confirmation-alert").show();
								jQuery('#enableSaveButtons').removeAttr('checked');

								// bind enable and disbale buttons event on checbox only when strcit confrimation is on 	
								jQuery('#enableSaveButtons').click(function(){

									if(data.strictConfirmation){

										if(jQuery(this).is(':checked')){
											jQuery("#updateOfferBtn").removeClass("disabled").removeAttr('disabled','disabled');
										} else{
											jQuery("#updateOfferBtn").addClass("disabled").attr('disabled','disabled');
										}
									}
								});
							} else{
								jQuery("#updateOfferBtn").removeClass("disabled").removeAttr('disabled','disabled');
								jQuery(".strict-confirmation-alert").hide();
							}

						var catCount = data.length;
						
						for(var i=0 ; i< catCount ; i++ ){
							// Add class by Er.kundal for select catg of shop
							jQuery("#categoryBtn-"+data[i].categoryId).addClass('btn-primary');
							jQuery("input#category-" + data[i].categoryId).attr('checked' , 'checked');
						}
						
						if(data.notes=='' && data.affname=='' && data.accountManagerName=='') {
							jQuery('#aboutShopDiv').hide();
						}
						
						/*if(data.notes=='' || data.accountManagerName=='' || data.affname=='') {
							 jQuery('#aboutShopNoteDiv').hide();
							 jQuery('#aboutManagerDiv').hide();
							 jQuery('#aboutNertworkDiv').hide();
							 jQuery('#aboutShopDiv').hide();
						}*/

					//} else {
						
					//	jQuery('#aboutShopDiv').hide();
						//}
					}
				},
				error: function(message) {
		            // pass an empty array to close the menu if it was initially opened
		           // response([]);
		        }

		 });
		jQuery("#code-alert-visitors-count").text('Updating...');
		jQuery.ajax({
			url : HOST_PATH + "admin/offer/favouriteshopdetail/shopId/" + value,
				dataType : "json",
				success : function(data) {
				jQuery("#code-alert-visitors-count").text(data);
				},
				error: function(message) {
		        }
		});
	} else{
		jQuery("#updateOfferBtn").removeClass("disabled").removeAttr('disabled','disabled');
		jQuery(".strict-confirmation-alert").hide();
	}
}

function selectOfferType(dIv){
	jQuery("#" + dIv).addClass("btn-primary").siblings().removeClass("btn-primary");
	
	switch(dIv){
	      
	      case 'default':
	    	jQuery('#shopDetailDiv').show();
	    	jQuery('#membersOnlyDiv').hide();
	    	jQuery('div#extendedOfferDiv').show();
	    	jQuery("input#defaultoffercheckbox").attr('checked' , 'checked');   // check coupon code checkbox if  discount type coupon code
	        jQuery("input#memberonlycheckbox").removeAttr('checked');
	    	break;
	      case 'memberOnly':
	    	jQuery('#membersOnlyDiv').show(); 
	        jQuery('#shopDetailDiv').hide();
	        jQuery('div#extendedOfferDiv').hide();
	        jQuery('div#extendedoffer-container').hide();
	        jQuery("input#memberonlycheckbox").attr('checked' , 'checked');   // check coupon code checkbox if  discount type coupon code
	        jQuery("input#defaultoffercheckbox").removeAttr('checked');
	        break;
	      default:
	    	  
	     
	}
}

function selectDiscountType(dIv){
	//console.log('ONCLICK');
	invalidForm['uploadoffer'] = false ;
	jQuery("#" + dIv).addClass("btn-primary").siblings().removeClass("btn-primary");
	switch(dIv){
	      case 'couponCode':
	    		
		    jQuery("input[name=couponCode]").parent("div").removeClass("error success focus")
			    .prev("div").html("");
		    jQuery('.general-code-cont').show();
		 	jQuery('#ccode').html(' <label><strong>Coupon code</strong></label>');
	    	jQuery('#couponDiv').show();
	    	jQuery('#printDiv').hide();
	    	jQuery('span#saleDiv').hide();//code add by blal
	    	jQuery('#newsDiv').hide();
	    	jQuery('#mainnewsDiv').hide();
	    	jQuery('#morenewsbtn').hide();
	    	jQuery("#visibiliyDiv").show();
		    jQuery("#offertitledetail").show();
		    jQuery("#datesdiv").show();
		    
		    jQuery('#extra-options').show();
	    	jQuery('#offerrefurlPR').val('');
	    	jQuery('#uploadoffer').val('');
	    	jQuery("input#couponCodeCheckbox").attr('checked' , 'checked');   // check coupon code checkbox if  discount type coupon code
	    	jQuery("input#newsCheckbox").removeAttr('checked') ;          // uncheck news div checkbox if discount type coupon code
	        jQuery("input#saleCheckbox").removeAttr('checked') ;          // uncheck sale checkbox if discount type coupon code
	        jQuery("input#printableCheckbox").removeAttr('checked') ;     // uncheck print able checkbox if discount type coupon code
	       
	        if (jQuery('input#extendedoffercheckbox').is(':checked')) {
	        	
	        	jQuery('#extendedoffer-container').show();
	        	
	        } else {
	        	
	        	jQuery('#extendedoffer-container').hide();
	        } 
	        
	        errorExists.uploadoffer = true ;
	        
	        
	        break;
	      case 'news':
		        jQuery('#couponDiv').hide();
		        jQuery('#printDiv').hide();
		        jQuery('span#saleDiv').hide();//code add by blal
		        jQuery('#offerrefurlPR').val('');
		        jQuery('#uploadoffer').val('');
		        jQuery("input#newsCheckbox").attr('checked' , 'checked');   // check coupon code checkbox if  discount type sale 
		        jQuery("input#saleCheckbox").removeAttr('checked') ;    // uncheck coupon code checkbox if discount type sale
		        jQuery("input#couponCodeCheckbox").removeAttr('checked') ;    // uncheck coupon code checkbox if discount type sale 
		        jQuery("input#printableCheckbox").removeAttr('checked') ;     // uncheck print able checkbox if discount type sale
		        jQuery('#mainnewsDiv').show();
		        jQuery('#newsDiv').show();
		        
		        jQuery('#morenewsbtn').show();
		        jQuery("#visibiliyDiv").hide();
		        jQuery("#offertitledetail").hide();
		        jQuery("#datesdiv").hide();
		        jQuery("#attachpagesDiv").hide();
		    
		        jQuery('#extendedoffer-container').hide();
		        //jQuery("input#extendedoffercheckbox").addClass("btn-primary").siblings().removeClass("btn-primary");
				// jQuery("input#extendedoffercheckbox").removeAttr('checked') ; 
				//jQuery('#extendedoffer-container').hide();
		        
		        break;  
	        
	        
	      case 'sale':
	      	jQuery('.general-code-cont').hide();
	        jQuery('#couponDiv').hide();
	        jQuery('#printDiv').hide();
	        jQuery('span#saleDiv').show();//code add by blal
	        jQuery('#mainnewsDiv').hide();
	        jQuery('#newsDiv').hide();
	        jQuery('#morenewsbtn').hide();
	        jQuery("#visibiliyDiv").show();
		     jQuery("#offertitledetail").show();
		     jQuery("#datesdiv").show();
		     
		    jQuery("#attachpagesDiv").show();
		    jQuery('#extra-options').hide();
	        jQuery('#offerrefurl').val('');
	        jQuery('#uploadoffer').val('');
	        jQuery("input#saleCheckbox").attr('checked' , 'checked');   // check coupon code checkbox if  discount type sale 
	        jQuery("input#newsCheckbox").removeAttr('checked') ;          // uncheck news div checkbox if discount type coupon code
	        jQuery("input#couponCodeCheckbox").removeAttr('checked') ;    // uncheck coupon code checkbox if discount type sale 
	        jQuery("input#printableCheckbox").removeAttr('checked') ; 
	        jQuery("#extendedNo").click();
	       
	        jQuery('#extendedoffer-container').hide();
	        
	        
	        errorExists.uploadoffer = true ;
	        
	        
	        // uncheck print able checkbox if discount type sale
	    	 break;
	      case 'printable':
	    	  
	    	jQuery("div.uploadOfferMessage,div.offerrefurlMessage").html("");
	    		
		    jQuery("#offerrefurlPR").parent("div").removeClass("error success focus");
			   
		    jQuery('.general-code-cont').hide();	
		    jQuery('#couponDiv').hide();
		    jQuery('span#saleDiv').hide();//code add by blal
		    jQuery('#printDiv').show();
		    jQuery('#mainnewsDiv').hide();
		    jQuery('#newsDiv').hide();
		    jQuery('#morenewsbtn').hide();
		    jQuery("#visibiliyDiv").show();
		    jQuery("#offertitledetail").show();
		    jQuery("#datesdiv").show();
		    jQuery("#attachpagesDiv").show();
		    jQuery('#extra-options').hide();
		    jQuery("input#printableCheckbox").attr('checked' , 'checked');   // check print checkbox if  discount type prinable
		    jQuery("input#newsCheckbox").removeAttr('checked') ;          // uncheck news div checkbox if discount type coupon code
	        jQuery("input#saleCheckbox").removeAttr('checked') ;          // uncheck sale checkbox if discount type prinable
	        jQuery("input#couponCodeCheckbox").removeAttr('checked') ;     // uncheck coupon code checkbox if discount type prinable
	        jQuery("#extendedNo").click();
	        jQuery('#extendedoffer-container').hide();
	        
	        break;  
	      default:
	    	  break;
	    	  
	     
	}
}


function addCategory(catgory){
	if(jQuery("button#categoryBtn-" + catgory).hasClass('btn-primary')==true) {
		jQuery("button#categoryBtn-" + catgory).removeClass('btn-primary') ;
		jQuery("input#category-" + catgory).removeAttr('checked');
	} else {
		jQuery("button#categoryBtn-"+ catgory).addClass('btn-primary');
		jQuery("input#category-" + catgory).attr('checked' , 'checked');
	} 
}

function printOption(dIv){
	
	jQuery("div.uploadOfferMessage,div.offerrefurlMessage").html(""); 
	
	jQuery("#" + dIv).addClass("btn-primary").siblings().removeClass("btn-primary");
	switch(dIv){
    case 'uploadOfferOption':
  	jQuery('#uploadofferDiv').show();        // show upload offer Div
  	jQuery('#offerrefurlDiv').hide();        // hide offer refurl Div
  	jQuery('#offerrefurlPR').val(''); 
  	jQuery("input#uploadoffercheck").attr('checked' , 'checked');     // check upload offer checkbox if  user select upload offer option
    jQuery("input#offerrefurlcheck").removeAttr('checked') ;  // uncheck ref url checkbox if user select upload offer option
    jQuery("#offerLogoId").show();
    break;
    case 'userefurlOption':
      jQuery('#offerrefurlDiv').show();            // show upload refurl Div
      jQuery('#uploadofferDiv').hide();            // hide upload refurl Div
      jQuery('#uploadoffer').val('');              // empty upload offer
      jQuery("input#offerrefurlcheck").attr('checked' , 'checked');       // check ref url checkbox if user select ref url option
      jQuery("input#uploadoffercheck").removeAttr('checked') ; // uncheck upload offer checkbox if  user select ref url option
      jQuery("#offerLogoId").hide();
      jQuery("#offerrefurlPR").parent("div").removeClass("error success  focus");
      
      errorExists.uploadoffer = true ;
      
      break;
	default:
  }  
}

 
var currentId = '';
function addMoreTerms(){
	termsCount = jQuery('input[name=termsAndcondition\\[\\]]').length;
	currentId = "metaTextLeft-"+termsCount ;
	var newDiv = '<div class="mainpage-content-right">'
		+ '<div class="mainpage-content-right-inner-right-other">' 
		+ '</div><div class="mainpage-content-right-inner-left-other">' 
		+ '<input id="termsAndcondition-'+termsCount+'" name="termsAndcondition[]" type="text" maxlength="80" onKeyup="setId(this.id)" placeholder="' +__('Next Term') +'" class="span3 mbot bbot ignore">' 
		+ '<input type="text" id="metaTextLeft-'+termsCount+'" disabled="" placeholder="' +__('Characters Left:') + 80 +'"  class="input-xlarge disabled btop word-count">' +
		'</div></div>';
    jQuery('#termsAndCondition').append(newDiv);
    
    var options = {
			'maxCharacterSize': 80,
			'displayFormat' : ''
	};
    
    jQuery('#termsAndcondition-'+termsCount).textareaCount(options, function(data){
    	termsCount = getId().split('-');
    	jQuery('#metaTextLeft-'+termsCount[1]).val(__("Characters Left: ")+data.left);
   }); 
}

function setId(Id){
	currentId = Id;
}

 function getId(){
	 return currentId;
 }



function exclusiveeditorpick(e){
	
   var btn = e.target  ? e.target :  e.srcElement ;
   
   if(btn != null){
	   jQuery(btn).addClass('btn-primary');
	   jQuery(btn).siblings('button').removeClass('btn-primary');
	   
		if(btn.value=='exclusive'){
			jQuery("input#exclusivecheckbox").attr('checked' , 'checked') ;
			jQuery("input#editorpickcheckbox").removeAttr('checked') ;
			jQuery("input#nonecheckbox").removeAttr('checked') ;
			jQuery("input#socialcodecheckbox").removeAttr('checked');
	     }else if(btn.value=='editorpic'){
	    	jQuery("input#editorpickcheckbox").attr('checked' , 'checked') ;
	    	jQuery("input#exclusivecheckbox").removeAttr('checked') ;
	    	jQuery("input#nonecheckbox").removeAttr('checked') ;
	    	jQuery("input#socialcodecheckbox").removeAttr('checked');
		} else if (btn.value=='socialcode') {
            jQuery("input#socialcodecheckbox").attr('checked' , 'checked');
            jQuery("input#exclusivecheckbox").removeAttr('checked');
            jQuery("input#nonecheckbox").removeAttr('checked');
            jQuery("input#editorpickcheckbox").removeAttr('checked');
	     }else {
		    jQuery("input#nonecheckbox").attr('checked' , 'checked') ;
		   	jQuery("input#exclusivecheckbox").removeAttr('checked') ;
		   	jQuery("input#editorpickcheckbox").removeAttr('checked') ;
		   	jQuery("input#socialcodecheckbox").removeAttr('checked');
			     
		 }
  } 
   
}


function extenOffer(e){
	var btn = e.target  ? e.target :  e.srcElement ;
	jQuery(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
	///console.log(btn);
	 if(jQuery(btn).attr('value')=='yes'){
		 jQuery("input#extendedoffercheckbox").attr('checked' , 'checked');
		 jQuery('#extendedoffer-container').show();
	 }else{
		 jQuery("input#extendedoffercheckbox").removeAttr('checked') ; 
		 jQuery('#extendedoffer-container').hide();
	 }
}

/**
* validRules oject contain all the messages that are visible when an elment
* val;ue is valid
* 
* structure to define a message for element key is to be element name Value is
* message
* @author spsingh
*/
var validRules = {

	addofferTitle : __("Title looks great"),
	offerrefurl : __("Valid Url"),
	extendedOfferMetadesc : __("Meta description looks great"),
	extendedOfferTitle : __("Title looks great"),
	couponInfo : __("Full info looks great"),
	couponCode : __("Coupon code looks great"),
	offerRefUrl : __("Valid url"),
	"termsAndcondition[]" : __("Term and condition looks great"),
	offerrefurlPR  : __("Valid url"),
	extendedOfferRefurl : __("Valid url"),
	discountamount : __("Valid Discount Amount"),
	uploadoffer : __("Valid file"),
	maxoffertxt: __("Max code looks great"),
	label : ""
	
};


/**
* focusRules oject contain all the messages that are visible on focus of an
* elelement
* 
* structure to define a message for element key is to be element name Value is
* message
* @author spsingh
*/
var focusRules = {
		
		addofferTitle : __("Enter title"),
		extendedOfferMetadesc : __("Enter meta description"),
		extendedOfferTitle : __("Enter title"),
		couponInfo : __("Enter full info"),
		couponCode : __("Enter coupon code"),
		offerRefUrl : __("Enter ref url"),
		"termsAndcondition[]" : __("Enter term and condition"),
		offerrefurlPR  : __("Enter ref url"),
		extendedOfferRefurl : __("Enter ref url"),
		uploadoffer : __("Please upload a file"),
		discountamount : __("Enter Discount Amount"),
		maxoffertxt: __("Enter max code"),
		label : "",
	};




/**
 * @author spsingh
 * apply validation on create new offer 
 */
var validateFormAddNewOffer1 = null ;
function validateFormAddNewOffer(){
	
	validateFormAddNewOffer1 =jQuery("form#addOffer")
	.validate(
			{
				errorClass : 'error',
				validClass : 'success',
				errorElement : 'span',
				ignore: [],
				errorPlacement : function(error, element) {
					
						/*element.parent("div").next("div")
								.html(error);*/
				},
				// validation rules
				rules : {
					whichshop : { 
						
						required : function(el)
						{	
		 					
							if(jQuery("#fromWhichShop").val() == 1 && jQuery(el).val().length < 1) 
							{
								return true ;
							}
							return false;
						}
					},
					'selectedcategories[]':{ 
				    	  required: function(el)
				    	  {
				    		  	if(jQuery("#couponCodeCheckbox").is(":checked"))
			    		  		{
			    		  			return true;
			    		  		
			    		  		}
				    		  return false; 
				    		  
				    	  }
				    },
				    'selectedTiles[]':{ 
				    	  required: function(el)
				    	  {
				    		  	if(jQuery("#couponCodeCheckbox").is(":checked"))
			    		  		{
			    		  			return true;
			    		  		
			    		  		}
				    		  return false; 
				    	  }
				    },
				    offerImageSelect:{
				    	required:true
				    },
				    'saleTiles[]':{ 
				    	  required: function(el)
				    	  {
				    		  	if(jQuery("#saleCheckbox").is(":checked"))
			    		  		{
			    		  			return true;
			    		  		
			    		  		}
				    		  return false; 
				    	  }
				    },
					selctedshop : { 
						
						required : function(el)
						{
							
							if(jQuery("#fromWhichShop").val() == 1 && jQuery(el).val().length < 1) 
							{
								return true ;
							}
							return false;
						}
					},
					newShop : { 
						
						required : function(el)
						{
							if( jQuery("#fromWhichShop").val() == 0 && jQuery(el).val().length < 1){
								return true;
							}
							return false ;
						}
					},
                      spancombo : { 
						required : function(el)
						{
							
							if(  jQuery("input[name=defaultoffercheckbox]")
									.is(":checked") )
								return true ;
							 else 
								return false ;
						}
					},
					couponCode : { 
						
						required : function(el)
						{
							
							if(  jQuery("input[name=couponCodeCheckbox]")
									.is(":checked") ){
								
								if(jQuery("input#couponCodeType").val() == 'GN'){
									
									return true ;
									
								}else{
									return false ;
								}
									
								
							}
							 else 
								return false ;
						}
					},
					addofferTitle : {
						required : true,
						minlength : 2
					},	
					offerRefUrl : {
						
						required: function(el)
						{
							if( jQuery("input[name=deepLinkStatus]").is(":checked") )
							
								return true ;
							else 
								return false ;
						} ,
						regex  :/((http|https):\/\/)([_a-z\d\-]+(\.[_a-z\d\-]+)+)(([_a-z\d\-\\\.\/]+[_a-z\d\-\\\/])+)*/
							
					},
					offerrefurlPR : {
						required : function(){
							
							if( jQuery("input[name=printableCheckbox]").is(":checked") 
									&& jQuery("input[name=offerrefurlcheck]").is(":checked") )
							
								return true ;
							else 
								return false ;
							
						} ,
						
						regex  : /\b([\d\w\.\/\+\-\?\:]*)((ht|f)tp(s|)\:\/\/|[\d\d\d|\d\d]\.[\d\d\d|\d\d]\.|www\.|\.tv|\.ac|\.com|\.edu|\.gov|\.int|\.mil|\.net|\.org|\.biz|\.info|\.name|\.pro|\.museum|\.co)([\d\w\.\/\%\+\-\=\&amp;\?\:\\\&quot;\'\,\|\~\;]*)\b/
					
					},
					extendedOfferTitle : {
						
						required : function()
						{
							if(  jQuery("input[name=extendedoffercheckbox]")
									.is(":checked") && jQuery("input[name=couponCodeCheckbox]")
									.is(":checked") )
							
								return true ;
							 else	
								return false ;
							
							
						},
						minlength : function()
						{
							if(  jQuery("input[name=extendedoffercheckbox]")
									.is(":checked") && jQuery("input[name=couponCodeCheckbox]")
									.is(":checked"))
							{
								return 2 ;
							} else {
								return 0 ;
							}
						}
					},
					extendedOfferRefurl : {
						
						required : function()
						{
							if(  jQuery("input[name=extendedoffercheckbox]")
									.is(":checked") && jQuery("input[name=couponCodeCheckbox]")
									.is(":checked") )
							
								return true ;
							 else	
								return false ;
							
							
						},
						 remote : {
								// validating and filtering  navigational url from server 
								url: HOST_PATH + "admin/offer/validatepermalink/id/"+jQuery("#offerId").val(),
					        	complete : function(e) {
					        		
					        		  //alert(e.responseText);
					        		res = jQuery.parseJSON(e.responseText);
					        		if(res.status == "200")
					        		{
					        			jQuery('span[for=extendedOfferRefurl]' , jQuery("[name=extendedOfferRefurl]").parents('div.mainpage-content-right') )
					        			.attr('remote-validated' , true);
					        			
					        			jQuery('#extendedOfferRefurl').val(res.url);
					        			
					        			jQuery("input[name=extendedOfferRefurl]").parent('div').prev("div").removeClass('focus')
					        		.removeClass('error').addClass('success');
					        		} 
					        		else
					        		{
					        		jQuery('span[for=extendedOfferRefurl]' , jQuery("[name=extendedOfferRefurl]").parents('div.mainpage-content-right') )
				        				.removeClass('valid').attr('remote-validated' , false);
					        		}
					        	}
							}
					}
				},
				// error messages
				messages : {
					newShop : {
						required : __("Please Enter the Name of the Shop")
					},
					'selectedcategories[]': { 
				    	  required: __("Please select a category") 
				    },
				    'selectedTiles[]': { 
				    	  required: __("Please select a offer image") 
				    },
				    offerImageSelect:{
				    	required : __("Please Select Offer Tile")
				    },
					selctedshop : {
						required : __("Please Select Shop")
					},
					addofferTitle : {
						required : __("Please enter title")
					},	
					offerrefurlPR : {
						required  : __("Please enter ref Url"),
						regex : __("Invalid Url")

					},
					offerRefUrl : {
						required  : __("Please enter ref Url"),
						regex : __("Invalid Url")
					},
					extendedOfferTitle :{
						required  : __("Please enter title")
					},
					extendedOfferRefurl : {
						required  : __("Please enter permalink"),
						remote : __("Permalink already exists")
					},
					couponCode : {
						required  : __("Please enter coupon code")
					}					
				},

				onfocusin : function(element) {
					
					if(! jQuery(element).hasClass("ignore2")) 
					// display hint messages when an element got focus 
					if (!jQuery(element).parent('div').prev("div")
							.hasClass('success')) {
						
									var label = this.errorsFor(element);
			    		 
			    		 	 		this.showLabel(element, focusRules[element.name]);
									
									jQuery(element).parent('div').removeClass(
													this.settings.errorClass)
											.removeClass(
													this.settings.validClass)
											.prev("div")
											.addClass('focus')
											.removeClass(
													this.settings.errorClass)
											.removeClass(
													this.settings.validClass);
			  	 
			    	     } else {
			    	    	 
							this.showLabel(element, focusRules[element.name]);
							
							jQuery(element).parent('div').removeClass(
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

					// highlight borders in case of error  
						jQuery(element).parent('div')
								.removeClass(validClass)
								.addClass(errorClass).prev(
										"div").removeClass(
										validClass)
								.addClass(errorClass);

						jQuery('span.help-inline', jQuery(element).parent('div')
						.prev('div')).removeClass(validClass);
						
						if(element.name == 'selectedTiles[]' || element.name == 'saleTiles[]')
						{
							jQuery(window).scrollTop(400) ;
						}
						
						if(element.name == 'selectedcategories[]')
						{
							jQuery(window).scrollTop(800) ;
						}
					
				},
				unhighlight : function(element,
						errorClass, validClass) {

					if(! jQuery(element).hasClass("ignore2")) 

					// check for ignored elemnets and highlight borders when succeed
					if(! jQuery(element).hasClass("ignore")) {
						
						jQuery(element).parent('div')
								.removeClass(errorClass)
								.addClass(validClass).prev(
										"div").addClass(
										validClass)
								.removeClass(errorClass);
						jQuery(
								'span.help-inline',
								jQuery(element).parent('div')
										.prev('div')).text(
							validRules[element.name]) ;
					} else
					{
						
						// check to display errors for ignored elements or not 
						
						var showError = false ;
						
						// 
						switch( element.nodeName.toLowerCase() ) {
						case 'select' :
							
							var val = jQuery(element).val();
							
							if(jQuery(jQuery(element).children(':selected')).attr('default') == undefined)
							{
								showError = true ;
							} else
							{
								showError  = false;
							}
							break ; 
						case 'input':
							if ( this.checkable(element) ) {
								
								showError = this.getLength(element.value, element) > 0;
								
							} else if(jQuery.trim(element.value).length > 0) {
								
									showError =  true ;
									
								} else {
									
									showError = false ;
								}
									
							break ; 
						default:
							var val = jQuery(element).val();
							showError =  jQuery.trim(val).length > 0;
						}
						
						
						if(! showError )
						{
							// hide errors message and remove highlighted borders 
								jQuery(
										'span.help-inline',
										jQuery(element).parent('div')
										.prev('div')).hide();
								
									jQuery(element).parent('div')
									.removeClass(errorClass)
									.removeClass(validClass)
									.prev("div")
									.removeClass(errorClass)
									.removeClass(validClass) ;
						} else
						{
							// show errors message and  highlight borders 
							
							// display green border and message 
							//if ignore element type is not file
						  
							if(element.type !== "file")
							{
								
								jQuery(element).parent('div')
								.removeClass(errorClass)
								.addClass(validClass).prev(
										"div").addClass(
										validClass)
								.removeClass(errorClass);
								
								jQuery('span.help-inline', jQuery(element).parent('div')
												.prev('div')).text(
									 validRules[element.name] ).show();
							} else
							{
								jQuery(element).parent('div')
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
                },
				success: function(label , element) {
				
						jQuery(element).parent('div')
						.removeClass(this.errorClass)
						.addClass(this.validClass).prev(
								"div").addClass(
										this.validClass)
						.removeClass(this.errorClass);
					
					    jQuery(label).append( validRules[element.name] ) ;
					    label.addClass('valid') ;
					    jQuery('div#error-message').html('').removeClass('error-message');
				}
             });

 }
 
/**
 * @author spsingh
 * disable  validation event on keyup and trigger on blur
 */
jQuery.validator.setDefaults({
		onkeyup : false,
		onfocusout : function(element) {
			jQuery(element).valid() ;
		}

});

var invalidForm = {} ;


// used to validate upload logo type
function checkFileType(e)
{
	
	if ( jQuery("input[name= printableCheckbox]")
			.is(":checked") )  
	{
		
		 var el = e.target  ? e.target :  e.srcElement ;
		 
		 var regex = /pdf|jpg|jpeg|JPG|JPEG|PNG/ ;
		
		 if( regex.test(el.value) )
		 {
			
			 invalidForm[el.name] = false ;
			 jQuery(el).parents("div").addClass('success').removeClass('error');
			 jQuery(el).parent("div").prev()
			 .prev("div.mainpage-content-right-inner-right-other").removeClass("focus")
			 .html(__("<span class='success help-inline'>Valid file</span>"));
			 
		 } else {
			 jQuery(el).parents("div").addClass('error').removeClass('success');
			 jQuery(el).parents("div").prev()
			 .prev("div.mainpage-content-right-inner-right-other").removeClass("focus")
			 .html(__("<span class='error help-inline'>Please upload only jpg or pdf file</span>"));
			 
			 invalidForm[el.name] = true ;
			 errorBy = el.name ;
		 }
      } else {
		invalidForm = {} ;
	}
}

var flagDate = false;
function changeDateFlag()
{
	flagDate=true;	

}
//used to validate start and end  date time
function validateStartEndTimestamp()
{
	var sDate = Date.parseExact( jQuery("input#offerStartDate").val() , "dd-MM-yyyy") ;
	var eDate = Date.parseExact( jQuery("input#offerEndDate").val(), "dd-MM-yyyy") ;
	var now = new Date() ;
	var currentDate = now.getDate() + "-" + ( now.getMonth() + 1 ) + "-" + now.getFullYear() ;
	
	currentDate = Date.parseExact( currentDate , "d-M-yyyy"); 
	
	// check start date should be greater than or equal to current date 
	
	var startTime = jQuery("input#offerstartTime").val();
	var endTime = jQuery("input#offerendTime").val();
	
	var hasError = false ;
	// check start date and end date is equaul
	if( eDate.compareTo ( sDate ) == 0)
	{
		// check time satrt time is greater than  end time 
		if(startTime  >= endTime) 
		{
			hasError = true ;
		} else {
			hasError = false  ; 
		}
	}
	
	// end date is greaqtetr than start date 
	if ( eDate.compareTo ( sDate ) < 0 ) 
	{
		hasError = true   ; 
	}
	
	// check for error i.e start date time is greater than end date time
	if(hasError)
	{
		// Change msg by Er.kundal
		jQuery("div.dateValidationMessage1").removeClass("success").addClass("error").html(__("<span class='error help-inline'>End date should be greater than start date</span>"))
		.next("div").addClass("error").removeClass("success");
		jQuery("div.dateValidationMessage2").removeClass("success").addClass("error").html(__("<span class='error help-inline'></span>"))
		.next("div").addClass("error").removeClass("success");
		
		
		errorExists['compareDate'] = false ;
	} else 	{
		jQuery("div.dateValidationMessage1").removeClass("error").addClass("success")
			.html(__("<span class='success help-inline'>Valid</span>"))
				.next("div").removeClass("error").addClass("success");
		jQuery("div.dateValidationMessage2").removeClass("error").addClass("success")
			.html(__("<span class='success help-inline'>Valid</span>"))
				.next("div").removeClass("error").addClass("success");
		errorExists['compareDate'] = true ;
	}
}

/**
 * delete user from database by id id get from hidden field
 * @author kraj
 */
function deleteOffer() {
	var id = jQuery('input#offerId').val();
	bootbox.confirm(__("Are you sure you want to move this offer to trash?"),__("No"),__("Yes"),function(r){
		if(!r){
			return false;
		}
		else{
			deleteRecord(id);
		}
		
	});
	   
}
function deleteRecord(id) {
	
	addOverLay();
	jQuery.ajax({
		url : HOST_PATH + "admin/offer/movetotrash",
		data : {
			'id' : id
		},
		dataType : "json",
		type : "post",
		success : function(data) {
			
			if (data != null) {
				
				window.location.href = HOST_PATH + "admin/offer/";
				
			} else {
				
				window.location.href = HOST_PATH + "admin/offer/";
			}
		}
	});	
}



function setDiscount(e,type){
	
	var btn = e.target ? e.target : e.srcElement;
	jQuery('#discountamount').removeAttr('disabled');
	jQuery(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
	switch(type){
	    case 'euro':
		     jQuery('#discountchk').val('1');
		     break;
		case 'percentage':
			jQuery('#discountchk').val('2');
			break;
		case 'off':	
			jQuery('#discountchk').val('0');
			//jQuery('#discountamount').val('');
			jQuery('#discountamount').attr('disabled', 'disabled');
			break;
		default:
		    break;
		}
}

function maxOffer(e){
	
	var btn = e.target  ? e.target :  e.srcElement ;
	jQuery(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
	 if(btn.value=='yes'){
		jQuery('#maxoffertxt').removeAttr('disabled');
		  jQuery("input#maxoffercheckbox").attr('checked' , 'checked');
		
	 }else{
		 jQuery('#maxoffertxt').attr('disabled' , 'disabled');
		 jQuery("input#maxoffercheckbox").removeAttr('checked') ; 
		
	 }
}
function word_count(field, count) {
	var number = 0;
    var matches = jQuery(field).val().match(/\b/g);
    if(matches) {
    	number = matches.length/2;
    }
    jQuery(count).val( number + __(' word') + (number != 1 ? 's' : ''));

}


/*************************** code added by blal********************/
 

function validateMenu(){
      validateNewMenu = jQuery("form#menuForm")
		.validate({	
			errorClass : 'error',
			validClass : 'success',
			errorElement : 'span',
			ignore: ".ignore, :hidden",
			//afterReset  : resetBorders,
			errorPlacement : function(error, element) {
				element.parent("div").prev("div")
						.html(error);
			},
		rules : {
			label : {
				required : true,
				
			} 
		},
		messages : {
			label : {
				required : "",
				
			} 
		},
		onfocusin : function(element) {
			
			// display hint messages when an element got focus 
			if (!jQuery(element).parent('div').prev("div")
					.hasClass('success')) {
				
	    		 var label = this.errorsFor(element);
	    		 if( jQuery(label).attr('hasError')  )
	    	     {
	    			 if(jQuery( label ).attr('remote-validated') != "true")
	    			 	{
						 this.showLabel(element, focusRules[element.name]);
							
							jQuery(element).parent('div').removeClass(
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
	    			 
	    	     } else {
	    	    	
 
					this.showLabel(element, focusRules[element.name]);
						jQuery(element).parent('div').removeClass(
									this.settings.errorClass)
							.removeClass(
									this.settings.validClass)
							.prev("div")
							.addClass('focus')
							.removeClass(
									this.settings.errorClass)
							.removeClass(
									this.settings.validClass);
	    	    	// }
    	    	 }
			}
		},
	highlight : function(element,errorClass, validClass) {
			// highlight borders in case of error  
			jQuery(element).parent('div')
			.removeClass(validClass)
			.addClass(errorClass).prev("div")
			.removeClass(validClass)
			.addClass(errorClass);
			jQuery('span.help-inline', jQuery(element).parent('div')
					.prev('div')).removeClass(validClass) ;
		
	},
	unhighlight : function(element,
			errorClass, validClass) {
			// check to display errors for ignored elements or not 
			var showError = false ;
			switch( element.nodeName.toLowerCase() ) {
			case 'select' :
				var val = jQuery(element).val();
				
				if(jQuery(jQuery(element).children(':selected')).attr('default') == undefined)
				{
					showError = true ;
				} else
				{
					showError  = false;
				}
				break ; 
			case 'input':
				if ( this.checkable(element) ) {
					
					showError = this.getLength(element.value, element) > 0;
					
				} else if(jQuery.trim(element.value).length > 0) {
					
						showError =  true ;
						
					} else {
						
						showError = false ;
					}
						
				break ; 
			default:
				var val = jQuery(element).val();
				showError =  jQuery.trim(val).length > 0;
			}
			if(! showError ){
				// hide errors message and remove highlighted borders 
					jQuery(
							'span.help-inline',
							jQuery(element).parent('div')
							.prev('div')).hide();
					
						jQuery(element).parent('div')
						.removeClass(errorClass)
						.removeClass(validClass)
						
						.prev("div")
						.removeClass(errorClass)
						.removeClass(validClass);
					    
			} else
			{
				if(element.type !== "file"){
					jQuery(element).parent('div')
					.removeClass(errorClass)
					.addClass(validClass).prev(
							"div").addClass(
							validClass)
					.removeClass(errorClass);
					
					jQuery('span.help-inline', jQuery(element).parent('div')
									.prev('div')).text(
						 validRules[element.name] ).show();
				} else{
					jQuery(element).parent('div')
					.removeClass(errorClass)
					.removeClass(validClass)
					
					.prev("div")
					.removeClass(errorClass)
					.removeClass(validClass) ;
				}
			}
		 
	},
		
 });
	
}
 

function submitForm(){
		var divVal = jQuery('div#offertypeDiv div.btn-group').children('button.btn-primary').attr('id');
		jQuery('input#hidtype').val(divVal);
	      jQuery.ajax({
				url : HOST_PATH + "admin/offer/addoffertile",
				data: jQuery('form#menuForm').serialize(),		
				dataType : "json",
				type : "post",
				success : function(data) {
					if (data != null) {
						//alert(data.imagepath);
						jQuery('#myModal').modal('hide');
						jQuery('input#label').val('');
						jQuery('input#position').val('');
						var li = jQuery('<li id="' + data.imgId + '" ><a id="' + data.imgId + '" onClick="selectOfferImage(' +  data.imgId + ')"; href="javascript:void(0)"><img class="liimg" src="' + data.imagepath + '" title="' + data.label + '" id="offertileimg"/></a><div  id="divShow_' + data.imgId + '" style="display:block" class="hideShow"><a onclick="setValueInHidden(' +  data.imgId + ')" href="_#myModal" data-toggle="modal"><img src="'+ HOST_PATH + 'public/images/back_end/edit-icon-txt.png"></a><a class="unselect" id="selectImg_' + data.imgId + '" style="display:none" href="javascript:void(0);"><img src="'+HOST_PATH+'public/images/back_end/tick.png"></a></div></li>');
						//FOR  EDIT IMAGE
						var divVal = jQuery('div#offertypeDiv div.btn-group').children('button.btn-primary').attr('id');
						if(parseInt(jQuery('input#forDelete').val()) > 0) {
								
									var Id = parseInt(jQuery('input#forDelete').val());
									switch(divVal){
									case 'sale':
										jQuery('span#saleDiv div#offertilesimg').children('ul#imgLiForTiles').children('li#'+ Id).replaceWith(li);
										break;
									case 'printable':
										jQuery('span#printDiv div#offertilesimg').children('ul#imgLiForTiles').children('li#'+ Id).replaceWith(li);
										break;
									case 'couponCode':
										jQuery('span#couponDiv div#offertilesimg').children('ul#imgLiForTiles').children('li#'+ Id).replaceWith(li);
										break;
									default:
										break;
									}
									
							} else {
							//FOR NEW IMAGE
							switch(divVal){
							case 'sale':
								jQuery('span#saleDiv div#offertilesimg').children('ul#imgLiForTiles').children('li#before').before(li);
								break;
							case 'printable':
								jQuery('span#printDiv div#offertilesimg').children('ul#imgLiForTiles').children('li#before').before(li);
								break;
							case 'couponCode':
								jQuery('span#couponDiv div#offertilesimg').children('ul#imgLiForTiles').children('li#before').before(li);
								break;
							default:
								break;
							}
						}
					} else {
						
						alert(__("Problem in your data"));
						
					}
				}
			});
}

function selectOfferImage(id)
{
	jQuery('a.unselect').hide();
	jQuery('a#selectImg_'+id).show();
	jQuery('input#offerImageSelect').val(id);
	//jQuery("input.tiles").removeAttr('checked').valid();
 
	
	if(jQuery("#couponCodeCheckbox").is(":checked"))
	{
		jQuery("span[for='selectedTiles[]']").remove();

		jQuery("input#selectedTiles").attr('checked' , 'checked'); //.valid();
		
	}
	
	if(jQuery("#saleCheckbox").is(":checked"))
	{
		jQuery("span[for='saleTiles[]']").remove();
		jQuery("input#saleTiles").attr('checked' , 'checked'); //.valid();
		
	}
	
	
	
}
function showHidedDiv()
{
	id = jQuery(this).attr('id');
	jQuery(this).children('div#divShow_'+id).show();
}
function hideDiv()
{
	id = jQuery(this).attr('id');
	jQuery(this).children('div#divShow_'+id).hide();
}
function getAllTiles() {
	
	jQuery.ajax({
		url : HOST_PATH + "admin/offer/getalltiles",
		dataType : "json",
			success : function(data) {
				if (data != null) {
					renderDataInLiAllTiles(data);
					selectOfferImage(tile_id);
				}
			}
	});	
}
function renderDataInLiAllTiles(tiles) {
	           for(i in tiles){
		 		//jQuery('input#offerImageSelect').val(tiles[i].id);
		 		//console.log(tiles);
		 		switch(i){
				case 'sale':
					for (var j in tiles[i]){
						
							var li = jQuery('<li id="' + tiles[i][j].id + '" ><a id="' + tiles[i][j].id + '" onClick="selectOfferImage(' +  tiles[i][j].id + ')"; href="javascript:void(0)"><img class="liimg" src="' + PUBLIC_PATH_LOCALE + tiles[i][j].path + tiles[i][j].name +'" title="'+ tiles[i][j].label +'" id="offertileimg"/></a><div  id="divShow_' + tiles[i][j].id + '" style="display:block" class="hideShow"><a onclick="setValueInHidden(' +  tiles[i][j].id + ')" href="_#myModal" data-toggle="modal"><img src="'+ HOST_PATH + 'public/images/back_end/edit-icon-txt.png"></a><a class="unselect" id="selectImg_' + tiles[i][j].id + '" style="display:none" href="javascript:void(0);"><img src="'+HOST_PATH+'public/images/back_end/tick.png"></a></div></li>');
							jQuery('div#offertypeDiv span#saleDiv div#offertilesimg').children('ul#imgLiForTiles').children('li#before').before(li);
						}
					break;
				case 'printable':
					for (var j in tiles[i]){
						
						var li = jQuery('<li id="' + tiles[i][j].id + '" ><a id="' + tiles[i][j].id + '" onClick="selectOfferImage(' +  tiles[i][j].id + ')"; href="javascript:void(0)"><img class="liimg" src="' + PUBLIC_PATH_LOCALE + tiles[i][j].path + tiles[i][j].name +'" title="'+ tiles[i][j].label +'" id="offertileimg"/></a><div  id="divShow_' + tiles[i][j].id + '" style="display:block" class="hideShow"><a onclick="setValueInHidden(' +  tiles[i][j].id + ')" href="_#myModal" data-toggle="modal"><img src="'+ HOST_PATH + 'public/images/back_end/edit-icon-txt.png"></a><a class="unselect" id="selectImg_' + tiles[i][j].id + '" style="display:none" href="javascript:void(0);"><img src="'+HOST_PATH+'public/images/back_end/tick.png"></a></div></li>');
						jQuery('div#offertypeDiv span#printDiv div#offertilesimg').children('ul#imgLiForTiles').children('li#before').before(li);
					}
					break;
				case 'couponCode':
					for (var j in tiles[i]) {
						
						var li = jQuery('<li id="' + tiles[i][j].id + '" ><a id="' + tiles[i][j].id + '" onClick="selectOfferImage(' +  tiles[i][j].id + ')"; href="javascript:void(0)"><img class="liimg" src="' + PUBLIC_PATH_LOCALE + tiles[i][j].path + tiles[i][j].name +'" title="'+ tiles[i][j].label +'" id="offertileimg"/></a><div  id="divShow_' + tiles[i][j].id + '" style="display:block" class="hideShow"><a onclick="setValueInHidden(' +  tiles[i][j].id + ')" href="_#myModal" data-toggle="modal"><img src="'+ HOST_PATH + 'public/images/back_end/edit-icon-txt.png"></a><a class="unselect" id="selectImg_' + tiles[i][j].id + '" style="display:none" href="javascript:void(0);"><img src="'+HOST_PATH+'public/images/back_end/tick.png"></a></div></li>');
						jQuery('div#offertypeDiv span#couponDiv div#offertilesimg').children('ul#imgLiForTiles').children('li#before').before(li);
						
					}
					break;
				default:
					break;
				}
		 		
		 }
	
	//jQuery('div.hideShow').hide();
	//jQuery('ul#imgLiForTiles li').hover(showHidedDiv,hideDiv);
	
}
function deleteImage()
{
	var id = jQuery('input#forDelete').val();
	jQuery('ul#imgLiForTiles li#' + id).remove();
	jQuery('#deleteImage').hide();
	//call ajax for permanent delete from database 
	deleteMenu(id);
	jQuery('input#forDelete').val('');
	jQuery('#myModal').modal('hide');
}
function setValueInHidden(id)
{
	//id = jQuery(this).attr('id');
	jQuery('input#forDelete').val(id);
	getTileById(id);
	jQuery('#deleteImage').show();
	jQuery('#deleteImage').click(deleteImage);
}
function hideDeleteButton()
{
	jQuery('#deleteImage').hide();
	jQuery('input#forDelete').val('');
}
function getTileById(id)
{
	//alert("yessss");
	jQuery.ajax({
		url : HOST_PATH + "admin/offer/getilebyid/id/"+id,
		dataType : "json",
			success : function(data) {
				if (data != null) {
					jQuery('input#label').val(data.label);
					jQuery('input#position').val(data.position);
					jQuery('input#hidimage').val(data.name);
					jQuery('input#hidimagepath').val(data.path);
					jQuery('input#hidimageext').val(data.ext);
					jQuery('input#hidtype').val(data.type);
				}
			}
	});	
}
function deleteMenu(id){
	//alert("yessss");
	jQuery.ajax({
		url : HOST_PATH + "admin/offer/deletemenu/id/"+id,
		type : "post",
		dataType : "json",
			success : function(data) {
				if (data != null) {
					jQuery('#myModal').modal('hide');
				}
			}
	});	
}



jQuery.extend(jQuery.validator.prototype , {
	focusInvalid : function(value, element , regex )
	{
		if( this.settings.focusInvalid ) {
			try {
				
				var el = '' ;
 				switch(this.errorList[0].element.name)
				{
				
					case 'whichshop' :
						el = jQuery("a.select2-choice.select2-default" , "div#shopDiv") ;
					break;
					
					case 'selectedcategories[]' :
					
						el = jQuery("input[name='selectedcategories[]']:first").focus().click().removeAttr('checked');
						if(jQuery("input[name='selectedcategories[]']:first").hasClass('success') == false) {
							jQuery('div#error-message').html(__('please select a category')).addClass('error-message');
						}
					break;
					
					default :
						el = jQuery(this.findLastActive() || this.errorList.length && this.errorList[0].element || []) ;
				}
 				
				el.filter(":visible")
				.focus()
				// manually trigger focusin event; without it, focusin handler isn't called, findLastActive won't have anything to find
				.trigger("focusin");
			} catch(e) {
				// ignore IE throwing errors when focusing hidden elements
			}
		}
	}
});

jQuery(function () {
    'use strict';
    
    // Define the url to send the image data to
    var url = HOST_PATH +  'admin/offer/importcodes';
    
    // Call the fileupload widget and set some parameters
    jQuery('#codeUpload').fileupload({
        url: url,
        dataType: 'json',
		formData : {'offer': jQuery("#offerId").val() },
        done: function (e, data) {
         
        	// Add each uploaded file name to the #files list
            
        	jQuery('#progress .bar').css('width',  '100%');
        	
            jQuery("#import-codes-btn").off("click");
			
			setTimeout(function(){
					jQuery('.progress-file-detail').slideUp('slow',function() {
						jQuery('#progress .bar').css('width',  '0%');
						jQuery("#import-codes-btn").hide();
					});
			},2000);

	   	   	
			var $retdata, $errClass , $msg,$message;
			$retdata = data.result;   
			$msg = $retdata.message ;
			
			if($retdata.status == 200 )
			{
				$errClass= 'success';
				
				jQuery("p.avail strong").html($retdata.available);
				jQuery("p.used strong").html($retdata.used);
				jQuery("p.total strong").html($retdata.total);
			} else {
				$errClass= 'error';
			}
			
			jQuery(".mainpage-content-colorbox.msg").prev('br').remove();
			jQuery(".mainpage-content-colorbox.msg").remove();
			
			
			$message = '<br><div class="msg mainpage-content-colorbox '+  $errClass + '">' ;
			$message += '<span class="'+  $errClass + '">'+ $msg +'</span></div>'
			
			jQuery("div.edit-offer-main-cont").prepend($message);
		 
        },
        add:function (e, data) {
		 
        	
        	
        	// validate file type is excel or not
        	var acceptFileTypes = /xlsx|xls/ ;

        	var fileName = data.originalFiles[0]['name'] ;
			if(!acceptFileTypes.test(data.originalFiles[0]['name'])) {
				jQuery(".unique-code-cont span.help-inline").html(	__('Please upload only xlsx file'))
				.addClass('error').removeClass('success');;
				
				return false;
			}
			
/*			if(data.originalFiles[0]['size'] > 5000000) {
				uploadErrors.push('Filesize is too big');
			}
			 */

				// display message if file is valid
				jQuery(".unique-code-cont span.help-inline").html(	__('valid file'))
					.addClass('success').removeClass('error');
				
				jQuery("#import-codes-btn").show();
				
				// bind button click 
				jQuery("#import-codes-btn").off('click').on('click',function () {
					
					
					// confirm from user to update if yes then submit the file
					bootbox.confirm(__("By importing this list you will overwrite the current codes of this offer! Are you sure you want to import?"),__('No'),__('Yes'),function(r){
						 if(r){
							 
							 jQuery("span#imported-filename").html(fileName);
							 jQuery('div.progress-file-detail').show('fast');
							 data.submit();
						 }
						 
					});
						 
						 
				});			
			
			
        		 					
          
        },

        progressall: function (e, data) {
            // Update the progress bar while files are being uploaded
            var progress = parseInt(data.loaded / data.total * 100, 10);
            jQuery('#progress .bar').css(
                'width',
                progress + '%'
            );
        }
    }) ;
}); 




function couponType(type)
{
	jQuery("#" + type).addClass("btn-primary").siblings().removeClass("btn-primary");
	if(type == 'unique')
	{
		jQuery("div.general-code-cont").hide();
		jQuery("div.unique-code-cont").show();
		jQuery("input#couponCodeType").val('UN');
	} else {
		jQuery("input#couponCodeType").val('GN');
		jQuery("div.unique-code-cont").hide();
		jQuery("div.general-code-cont").show();
	}
}