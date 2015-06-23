$(document).ready(init);	

var errorExists = {} ;

function init(){
	$('span.checkDate').click(changeDateFlag);
	$("#whichshop").select2({placeholder: __("Select a Shop")});
	$("#whichshop").change(function(){
		$("#selctedshop").val($(this).val());
	});
	
	//$('.combobox').combobox();
	
       var options = {
				'maxCharacterSize': 60,
				'displayFormat' : ''
		};
      
		$('#addofferTitle').textareaCount(options, function(data) {
			$('#metaTextLeft').val(__("Usergenerated offer title length ") + data.left + __(" characters remaining") );
		});
		
		runWordCounter();
		$('#dp3').datepicker().on('changeDate' , validateStartEndTimestamp);
		$('#dp4').datepicker().on('changeDate' , validateStartEndTimestamp);
		$('#offerstartTime').timepicker({
            minuteStep: 5,
            template: 'modal',
            showSeconds: false,
            showMeridian: false,
            'afterUpdate'  : validateStartEndTimestamp
        });
		
		$('#offerendTime').timepicker({
            minuteStep: 5,
            template: 'modal',
            showSeconds: false,
            showMeridian: false,
            'afterUpdate'  : validateStartEndTimestamp
        });
		
		$('div.multiselect ul li').click(
				selectPagesInList);
	$('.close').hide();	
	
	
	$('#deepLinkOnbtn').click(function(){
		$('#deepLinkOnbtn').addClass("btn-primary").siblings().removeClass("btn-primary");
		$('#offerRefUrl').removeAttr("disabled");
		$('#deepLinkStatus').attr("checked", "checked");
	});
	
	$('#deepLinkoofbtn').click(function(){
		$('#deepLinkoofbtn').addClass("btn-primary").siblings().removeClass("btn-primary");
		$('#offerRefUrl').attr("disabled", "disabled");
		$('#deepLinkStatus').removeAttr("checked");
	});
	//getOfferVotes();	
	getOfferDetail();	
	

	$("form").submit(function(){
		
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
		
		if( $("input[name=printableCheckbox]").is(":checked") && $("input[name=uploadoffercheck]").is(":checked") )
		{
			
			
			if ( $("input[name=uploadoffer]").val().length == 0 && $('img#uplodedOffer').length==0 )
			{
				if(!uploadPdfFlag){
				  $("div.uploadOfferMessage").removeClass("focus")
					 .html(__("<span class='error help-inline'>Please upload jpg or pdf file</span>"));
				
				
					errorExists['uploadoffer'] = false ;
				}

			} else {
				
				errorExists['uploadoffer'] =  true ;
			}
		} else {
			
			$("div.uploadOfferMessage").removeClass("focus")
			 .html("");
			 
		}
		
		
		validateStartEndTimestamp(); 
		
		for(var i in errorExists)
		{
			if( errorExists[i] != true )
			{
				return false ;
			}
		}
	});
	
	validateFormAddNewOffer();
}
jQuery.fn.multiselect = function() {
	
	$(this).each(function() {
		var checkboxes = $(this).find("input:checkbox");
		
		checkboxes.each(function() {
			var checkbox = $(this);
			
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

function getOfferDetail(){
	
	
	 $.ajax({
			url : HOST_PATH + "admin/usergeneratedoffer/offerdetail/offerId/" + $('#offerId').val(),
				method : "post",
				dataType : "json",
				type : "post",
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

var uploadPdfFlag = 0;
function setFormData(data){
	

	// populate offer type data based based on offer type
	
	if(data[0].Visability == 'MEM'){
		$("#memberOnly").click();
	}else{
		 $("#default").click();
		 if(data[0].shopId){
			 $('#selctedshop').val(data[0].shop.id);
			 $('#whichshop').select2("val", data[0].shop.id);
			 $('input.spancombo').val(data[0].shop.name);
			 if(data[0].shop.notes!=''){
				$('#aboutShopNoteDiv').show();
				$('#shopNotes').html(data[0].shop.notes);
			 }
		     if(data[0].shop.accountManagerName!=''){
				$('#aboutManagerDiv').show();
				$('#shopManager').html(data[0].shop.accountManagerName);
			 }
			 if(data[0].affname){
				$('#aboutNertworkDiv').show();
				$('#shopNetwork').html(data[0].affname);
			 }
	   }	 
	}
	
	if(parseInt(data[0].approved)){
		$('#yes').click();
	}else{
		$('#no').click();
	}
	// populate discount type data based based on discount type
	
	 $('#couponInfo').val(data[0].extendedFullDescription);
	if(data[0].discountType=='CD'){
		
		$("#couponCode").click();
		$('#couponCodeTxt').val(data[0].couponCode);
		var catCount = data[0].category.length;
		for(var i=0 ; i< catCount ; i++ ){
			console.log($("input#category-" + data[0].category[i].id).val());
			$("#categoryBtn-"+data[0].category[i].id).addClass('btn-primary');
			$("input#category-" + data[0].category[i].id).attr('rel' , true);
			$("input#category-" + data[0].category[i].id).attr('checked' , 'checked');

		}
		
	 }else if(data[0].discountType=='PA'){
		$("#printable").click();
		if(data[0].refOfferUrl){
		  $("#userefurlOption").click();
		  $('#offerrefurlPR').val(data[0].refOfferUrl);
			
		}else{
		    if(data[0].offerLogoId){
			if(data[0].logo.ext=='pdf'){
				uploadPdfFlag = 1;
				$('#offerLogoId').html('<a target="_blank" href="'+HOST_PATH_PUBLIC+data[0].logo.path+"/"+data[0].logo.name+'">'+__('View Pdf')+'</a>');
			}else{
		    var image = data[0].logo.path+"/thum_"+data[0].logo.name;
	        var imgSrc = HOST_PATH_PUBLIC + image;
			$('span#offerLogoId').append( '<img src="'+imgSrc+'" id="uplodedOffer" title="' +__('uploaded offer') +'" alt="' +__('uploaded offer') +'">');
			}
	        //$('#uplodedOffer').show().attr('src', imgSrc);
			$("#uploadOfferOption").click();
		  }
	   }
	 }else{
		  $("#sale").click();
	 }
	
	 $('#addofferTitle').val(data[0].title);
	 
	 var titleCharLeft = 60 ;
	 
	 if(data[0].title)
	{
		 titleCharLeft = 60-parseInt(data[0].title.length) ; 
	}		 
		 
	 
	 
	 
	 
	 $('#metaTextLeft').val( __("Usergenerated offer title length ") + (titleCharLeft) + __(" characters remaining"));
	 $('#offerRefUrl').val(data[0].refURL);
	 if(data[0].refURL){
		 $('#deepLinkOnbtn').click(); 
	 }else{
		 $('#deepLinkoofbtn').click(); 
	 }
	 //console.log(data[0].termandcondition.length);
	 var termsCount = data[0].termandcondition.length;
	 for(var i=0;i<termsCount;i++){
		 if(i>4){
		    $('#addmoreBtn').click();
		 }
		 $('#termsAndcondition-'+i).val(data[0].termandcondition[i].content);
		 $('#metaTextLeft-'+i).val(__('Characters Left: ')+ (80-(data[0].termandcondition[i].content.length)));
		 
	 }
	 
	 dateStart = data[0].startDate.split(' ');
	 dateEnd = data[0].endDate.split(' ');
	 $('input#offerStartDate').val(changeDateFormat(dateStart[0]));
	 $('input#offerEndDate').val(changeDateFormat(dateEnd[0]));
	
	 $('#offerstartTime').val(dateStart[1].substring(0, 5));
	 $('#offerendTime').val(dateEnd[1].substring(0, 5));
	 
	 if(data[0].exclusiveCode){
		$('#exclusivebtn').click();
	}
	if(data[0].editorPicks){
	    $('#editorpicbtn').click(); 
	 }
	
	var pageCount = data[0].page.length;
	for(var i=0 ; i< pageCount ; i++ ){
		cheboxId = "#attachedPage-"+data[0].page[i].id;
		$(cheboxId).attr('checked', 'checked');
		$(cheboxId).parent().addClass("selected");
	}
	
}

/**
 * When admin select website for user then selected class apply on selected list
 * in websiet multiselect then use this function
 * @author kraj
 */


function selectPagesInList() {

	if (($(this).children('input')).is(':checked')) {

		$(this).children('input').removeAttr('checked');
		$(this).removeClass('selected');

	} else {

		$(this).children('input').attr('checked', 'checked');
		$(this).addClass('selected');
	}
}

function getShopDetail(value){
	$('#selctedshop,#selctedPrvshop').val(value);
	$('#aboutShopDiv,#aboutShopNoteDiv,#aboutManagerDiv,#aboutNertworkDiv').hide();
    $.ajax({
		url : HOST_PATH + "admin/usergeneratedoffer/shopdetail/shopId/" + value,
			method : "post",
			dataType : "json",
			type : "post",
			success : function(data) {
				if (data != null) {
					
										
					if(data[0].notes!=''){
						$('#aboutShopNoteDiv').show();
						$('#shopNotes').html(data[0].notes);
					}
					
					if(data[0].accountManagerName!=''){
						$('#aboutManagerDiv').show();
					    $('#shopManager').html(data[0].accountManagerName);
					}
					if(data[0].affname){
						
						$('#aboutNertworkDiv').show();
						$('#shopNetwork').html(data[0].affname);
					}
					
					$('#aboutShopDiv').show();
					
					
					$('#categoryListdiv').each(function() {
						
						var checkboxes = $(this).find("input:checkbox");
						
						//console.log(bnt);
						 
						checkboxes.each(function() {
							
							var checkbox = $(this);
							//alert($(this).attr("checked"));
							//alert($(this).attr('rel'));
							if (checkbox.attr('rel')){
								
								$('#categoryBtn-'+checkbox.val()).click();
							} 
						});	
					});		
					
					/*
					 if(data[0].deepLink){
						    $('#offerRefUrl').val(data[0].deepLink);	
						    $('#deepLinkOnbtn').click();
					}else{
							 $('#offerRefUrl').attr("disabled", "disabled");
							 $('#deepLinkoofbtn').click();	
					}*/
					if(data[0].deepLink){
						  $('#offerRefUrl').val(data[0].deepLink);	
						  
						  
						  if(data[0].deepLinkStatus){
							  $('#deepLinkOnbtn').click();
							  
						  }else{
							  $('#deepLinkoofbtn').click();
						  }
						  
						  
						}else{
							 $('#offerRefUrl').attr("disabled", "disabled");
							 //$('#deepLinkoofbtn').click();	
					}
					var catCount = data[0].category.length;
					
					for(var i=0 ; i< catCount ; i++ ){
						$("#categoryBtn-"+data[0].category[i].id).click();
					}
				if(data[0].notes=='' || data[0].accountManagerName=='') {	
					$('#aboutShopDiv').hide();
				} 
				//else {
					
					//$('#aboutShopDiv').hide();
				//}
				}
			},
			error: function(message) {
	            // pass an empty array to close the menu if it was initially opened
	           // response([]);
	        }

		 });
	
}

function selectOfferType(dIv){
	$("#" + dIv).addClass("btn-primary").siblings().removeClass("btn-primary");
	
	switch(dIv){
	      
	      case 'yes':
	    	//$('#shopDetailDiv').show();
	    	$("input#yesoffercheckbox").attr('checked' , 'checked');   // check coupon code checkbox if  discount type coupon code
	        $("input#nocheckbox").removeAttr('checked');
	    	break;
	      case 'no':
	        //$('#shopDetailDiv').hide();
	        $("input#nocheckbox").attr('checked' , 'checked');   // check coupon code checkbox if  discount type coupon code
	        $("input#yesoffercheckbox").removeAttr('checked');
	        break;
	      default:
	    	  
	     
	}
}

function selectDiscountType(dIv){
	invalidForm['uploadoffer'] = false ;
	$("#" + dIv).addClass("btn-primary").siblings().removeClass("btn-primary");
	switch(dIv){
	      case 'couponCode':
	    		
		    $("input[name=couponCode]").parent("div").removeClass("error success focus")
			    .prev("div").html("");
		    	
	    	$('#couponDiv').show();
	    	$('#printDiv').hide();
	    	$('#offerrefurlPR').val('');
	    	$('#uploadoffer').val('');
	    	$("input#couponCodeCheckbox").attr('checked' , 'checked');   // check coupon code checkbox if  discount type coupon code
	        $("input#saleCheckbox").removeAttr('checked') ;          // uncheck sale checkbox if discount type coupon code
	        $("input#printableCheckbox").removeAttr('checked') ;     // uncheck print able checkbox if discount type coupon code
	        $("#extendedOfferDiv").show();
	        if ($('input#extendedoffercheckbox').is(':checked')) {
	        	
	        	$('#extendedoffer-container').show();
	        	
	        } else {
	        	
	        	$('#extendedoffer-container').hide();
	        } 
	        break;
	      case 'sale':
	        $('#couponDiv').hide();
	        $('#printDiv').hide();
	        $('#offerrefurl').val('');
	        $('#uploadoffer').val('');
	        $("input#saleCheckbox").attr('checked' , 'checked');   // check coupon code checkbox if  discount type sale 
	        $("input#couponCodeCheckbox").removeAttr('checked') ;    // uncheck coupon code checkbox if discount type sale 
	        $("input#printableCheckbox").removeAttr('checked') ; 
	        $("#extendedOfferDiv").hide();
	        $('#extendedoffer-container').hide();
	        // uncheck print able checkbox if discount type sale
	    	 break;
	      case 'printable':
	    	  
	    	$("div.uploadOfferMessage,div.offerrefurlMessage").html("");
	    		
		    $("#offerrefurlPR").parent("div").removeClass("error success focus");
			   
		    	
		    $('#couponDiv').hide();
		    $('#printDiv').show();
		    $("input#printableCheckbox").attr('checked' , 'checked');   // check print checkbox if  discount type prinable
	        $("input#saleCheckbox").removeAttr('checked') ;          // uncheck sale checkbox if discount type prinable
	        $("input#couponCodeCheckbox").removeAttr('checked') ;     // uncheck coupon code checkbox if discount type prinable
	        $("#extendedOfferDiv").hide();
	        $('#extendedoffer-container').hide();
	        break;  
	      default:
	    	  
	     
	}
}


function addCategory(e,catgory){
	
	var btn = e.target  ? e.target :  e.srcElement ;

	
	if($(btn).hasClass('btn-primary'))
	{
		$(btn).removeClass('btn-primary') ;
		$("input#category-" + catgory).removeAttr('checked') ;
	} else
	{
		$(btn).addClass('btn-primary');
		$("input#category-" + catgory).attr('checked' , 'checked');
	} 
	
}

function printOption(dIv){
	
	$("div.uploadOfferMessage,div.offerrefurlMessage").html(""); 
	
	$("#" + dIv).addClass("btn-primary").siblings().removeClass("btn-primary");
	switch(dIv){
    case 'uploadOfferOption':
  	$('#uploadofferDiv').show();        // show upload offer Div
  	$('#offerrefurlDiv').hide();        // hide offer refurl Div
  	$('#offerrefurlPR').val(''); 
  	$("input#uploadoffercheck").attr('checked' , 'checked');     // check upload offer checkbox if  user select upload offer option
    $("input#offerrefurlcheck").removeAttr('checked') ;  // uncheck ref url checkbox if user select upload offer option
    $("#offerLogoId").show();
    break;
    case 'userefurlOption':
      $('#offerrefurlDiv').show();            // show upload refurl Div
      $('#uploadofferDiv').hide();            // hide upload refurl Div
      $('#uploadoffer').val('');              // empty upload offer
      $("input#offerrefurlcheck").attr('checked' , 'checked');       // check ref url checkbox if user select ref url option
      $("input#uploadoffercheck").removeAttr('checked') ; // uncheck upload offer checkbox if  user select ref url option
      $("#offerLogoId").hide();
      $("#offerrefurlPR").parent("div").removeClass("error success  focus");
      break;
	default:
  }  
}

function runWordCounter(){
	termsCount = $('input[name=termsAndcondition\\[\\]]').length;
	var options = {
			'maxCharacterSize': 80,
			'displayFormat' : ''
	};
	
	var options1 = {
			'maxCharacterSize': 255,
			'displayFormat' : ''
	};
	
	$('#termsAndcondition-0').textareaCount(options, function(data){
		
		$('#metaTextLeft-0').val(__("Characters Left: ")+data.left);
	}); 
	
    $('#termsAndcondition-1').textareaCount(options, function(data){
		
		$('#metaTextLeft-1').val(__("Characters Left: ")+data.left);
	}); 

    $('#termsAndcondition-2').textareaCount(options, function(data){
	
	   $('#metaTextLeft-2').val(__("Characters Left: ")+data.left);
    }); 

    $('#termsAndcondition-3').textareaCount(options, function(data){
	
	   $('#metaTextLeft-3').val(__("Characters Left: ")+data.left);
    }); 

    $('#termsAndcondition-4').textareaCount(options, function(data){
	
	  $('#metaTextLeft-4').val(__("Characters Left: ")+data.left);
    }); 
    
  $('#extendedOfferTitle').textareaCount(options, function(data){
    	
  	  $('#metaTitleLeft').val(__("Characters Left: ")+data.left);
    });
    
     $('#extendedOfferMetadesc').textareaCount(options1, function(data){
    	
  	  $('#metaDescLeft').val(__("Characters Left: ")+data.left);
    });
    
    $('#couponInfo').textareaCount(options1, function(data){
    	
  	  $('#metacouponInfo').val(__("Characters Left: ")+data.left);
    }); 
	
	
  
}
var currentId = '';
function addMoreTerms(){
	termsCount = $('input[name=termsAndcondition\\[\\]]').length;
	currentId = "metaTextLeft-"+termsCount ;
	var newDiv = '<div class="mainpage-content-right">'
		+ '<div class="mainpage-content-right-inner-right-other">' 
		+ '</div><div class="mainpage-content-right-inner-left-other">' 
		+ '<input id="termsAndcondition-'+termsCount+'" name="termsAndcondition[]" type="text" maxlength="80" onKeyup="setId(this.id)" placeholder="' +__('Next Term') +'" class="span3 mbot bbot ignore">' 
		+ '<input type="text" id="metaTextLeft-'+termsCount+'" disabled="" placeholder="' +__('Characters Left:') + 80 +'"  class="input-xlarge disabled btop word-count">' +
		'</div></div>';
    $('#termsAndCondition').append(newDiv);
    
    var options = {
			'maxCharacterSize': 80,
			'displayFormat' : ''
	};
    
    $('#termsAndcondition-'+termsCount).textareaCount(options, function(data){
    	termsCount = getId().split('-');
    	$('#metaTextLeft-'+termsCount[1]).val(__("Characters Left: ")+data.left);
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
  if($(btn).hasClass('btn-primary'))
	{
		
		$(btn).removeClass('btn-primary') ;
		if(btn.value=='exclusive'){
		 $("input#exclusivecheckbox").removeAttr('checked') ;
	     }else{
	    	$("input#editorpickcheckbox").removeAttr('checked') ;
	     }
	}else{
		$(btn).addClass('btn-primary');
		if(btn.value=='exclusive'){
			$("input#exclusivecheckbox").attr('checked' , 'checked') ;
	     }else{
	    	$("input#editorpickcheckbox").attr('checked' , 'checked') ;
	     }
		
	} 
	
}


function extenOffer(e){
	
	var btn = e.target  ? e.target :  e.srcElement ;
	$(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
	///console.log(btn);
	 if($(btn).attr('value')=='yes'){
		 $("input#extendedoffercheckbox").attr('checked' , 'checked');
		 $('#extendedoffer-container').show();
	 }else{
		 $("input#extendedoffercheckbox").removeAttr('checked') ; 
		 $('#extendedoffer-container').hide();
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
	spancombo : __("Shop looks great")
		
	
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
		spancombo : __("Select Shop")
	};




/**
 * @author spsingh
 * apply validation on create new offer 
 */
var validateFormAddNewOffer1 = null ;
function validateFormAddNewOffer(){
	
	validateFormAddNewOffer1 =$("form#addOffer")
	.validate(
			{
				errorClass : 'error',
				validClass : 'success',
				errorElement : 'span',
				ignore: ":hidden",
				errorPlacement : function(error, element) {
					
						element.parent("div").prev("div")
								.html(error);
				},
				// validation rules
				rules : {
                    spancombo : { 
						required : true
					},
					couponCode : { 
						
						required : function(el)
						{
							
							if(  $("input[name=couponCodeCheckbox]")
									.is(":checked") )
								return true ;
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
							if( $("input[name=deepLinkStatus]").is(":checked") )
							
								return true ;
							else 
								return false ;
						} ,
						regex  : /^(?:(ftp|http|https):\/\/)?(?:[\w-]+\.)+[a-z]{3,6}$/
							
						//regex  : /^([_a-zA-Z\d\-]+(\.[_a-zA-Z\d\-]+)+)(([_a-zA-Z\d\-\\\.\/]+[_a-zA-Z\d\-\\\/])+)*$/
							
					},
					offerrefurlPR : {
						required : function(){
							
							if( $("input[name=printableCheckbox]").is(":checked") 
									&& $("input[name=offerrefurlcheck]").is(":checked") )
							
								return true ;
							else 
								return false ;
							
						} ,
						
						regex  : /^(?:(ftp|http|https):\/\/)?(?:[\w-]+\.)+[a-z]{3,6}$/
							
						//regex  : /^([_a-zA-Z\d\-]+(\.[_a-zA-Z\d\-]+)+)(([_a-zA-Z\d\-\\\.\/]+[_a-zA-Z\d\-\\\/])+)*$/
					},
					extendedOfferTitle : {
						
						required : function()
						{
							if(  $("input[name=extendedoffercheckbox]")
									.is(":checked") && $("input[name=couponCodeCheckbox]")
									.is(":checked") )
							
								return true ;
							 else	
								return false ;
							
							
						},
						minlength : function()
						{
							if(  $("input[name=extendedoffercheckbox]")
									.is(":checked") && $("input[name=couponCodeCheckbox]")
									.is(":checked"))
							{
								return 2 ;
							} else {
								return 0 ;
							}
						}
					},
					extendedOfferRefurl : {
						
						regex  : /^(?:(ftp|http|https):\/\/)?(?:[\w-]+\.)+[a-z]{3,6}$/
							
						//regex  : /^([_a-zA-Z\d\-]+(\.[_a-zA-Z\d\-]+)+)(([_a-zA-Z\d\-\\\.\/]+[_a-zA-Z\d\-\\\/])+)*$/
							
					}
				},
				// error messages
				messages : {
					spancombo : {
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
						regex : __("Invalid Url")
					},
					couponCode : {
						required  : __("Please enter coupon code")
					}					
				},

				onfocusin : function(element) {
					
					if(! $(element).hasClass("ignore2")) 
					// display hint messages when an element got focus 
					if (!$(element).parent('div').prev("div")
							.hasClass('success')) {
						
									var label = this.errorsFor(element);
			    		 
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
			  	 
			    	     } else {
			    	    	 
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

					// highlight borders in case of error  
						$(element).parent('div')
								.removeClass(validClass)
								.addClass(errorClass).prev(
										"div").removeClass(
										validClass)
								.addClass(errorClass);

						$('span.help-inline', $(element).parent('div')
										.prev('div')).removeClass(validClass) ;
					
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
					} else
					{
						
						// check to display errors for ignored elements or not 
						
						var showError = false ;
						
						// 
						switch( element.nodeName.toLowerCase() ) {
						case 'select' :
							
							var val = $(element).val();
							
							if($($(element).children(':selected')).attr('default') == undefined)
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
								
							} else if($.trim(element.value).length > 0) {
								
									showError =  true ;
									
								} else {
									
									showError = false ;
								}
									
							break ; 
						default:
							var val = $(element).val();
							showError =  $.trim(val).length > 0;
						}
						
						
						if(! showError )
						{
							// hide errors message and remove highlighted borders 
								$(
										'span.help-inline',
										$(element).parent('div')
										.prev('div')).hide();
								
									$(element).parent('div')
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
								
								$(element).parent('div')
								.removeClass(errorClass)
								.addClass(validClass).prev(
										"div").addClass(
										validClass)
								.removeClass(errorClass);
								
								$('span.help-inline', $(element).parent('div')
												.prev('div')).text(
									 validRules[element.name] ).show();
							} else
							{
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
						
				},
				success: function(label , element) {
				
						$(element).parent('div')
						.removeClass(this.errorClass)
						.addClass(this.validClass).prev(
								"div").addClass(
										this.validClass)
						.removeClass(this.errorClass);
					
					    $(label).append( validRules[element.name] ) ;
					    label.addClass('valid') ;
				    
				}

			});

 }
/**
 * @author spsingh
 * disable  validation event on keyup and trigger on blur
 */
$.validator.setDefaults({
		onkeyup : false,
		onfocusout : function(element) {
			$(element).valid() ;
		}

});



var invalidForm = {} ;


// used to validate upload logo type
function checkFileType(e)
{
	
	uploadPdfFlag = 0;
	if ( $("input[name= printableCheckbox]")
			.is(":checked") )  
	{
		
		 var el = e.target  ? e.target :  e.srcElement ;
		 
		 var regex = /pdf|jpg|jpeg|JPG|JPEG/ ;
		
		 if( regex.test(el.value) )
		 {
			 invalidForm[el.name] = false ;
			 		 
			 $(el).parent("div").prev()
			 .prev("div.mainpage-content-right-inner-right-other").removeClass("focus")
			 .html(__("<span class='success help-inline'>Valid file</span>"));
			 
		 } else {
			
			 $(el).parents("div").prev()
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
	var sDate = Date.parseExact( $("input#offerStartDate").val() , "dd-MM-yyyy") ;
	var eDate = Date.parseExact( $("input#offerEndDate").val(), "dd-MM-yyyy") ;
	
	var now = new Date() ;
	var currentDate = now.getDate() + "-" + ( now.getMonth() + 1 ) + "-" + now.getFullYear() ;
	
	currentDate = Date.parseExact( currentDate , "d-M-yyyy"); 
	
	// check start date should be greater than or equal to current date 
	if( sDate .compareTo (currentDate )  < 0 && flagDate==true )
	{
		$("div.dateValidationMessage1").removeClass("success").addClass("error").html(__("<span class='error help-inline'>Start date time must be equal to or greater than current date time</span>"))
		.next("div").addClass("error").removeClass("success");
		$("div.dateValidationMessage2").removeClass("success").addClass("error").html(__("<span class='error help-inline'></span>"))
		.next("div").addClass("error").removeClass("success");
		
		errorExists['currentDate'] = false ;
		return false ;
		
	} else {
		
		errorExists['currentDate'] = true ; 
	}
	
	/*
	var startTime = $("input#offerstartTime").val().split(" ");
	var endTime = $("input#offerendTime").val().split(" ");
	
	var hasError = false ;
	// check start date and end date is equaul
	if( eDate.compareTo ( sDate ) == 0)
	{
			// check time mederian are same  or not
			if(startTime[1] == endTime[1] ){
		
				// check time satrt time is greater than  end time 
				if( startTime[0] >= endTime[0]  )
				{
					hasError = true ;
				}
				
				// check if start time median is greater than end time mederian 
			} else if( startTime[1] == "PM" && endTime[1] == "AM" ) {
						
					hasError = true ; 
				
			}  else if( startTime[1] == "AM" && endTime[1] == "PM" ) {
				
					hasError = false  ; 
			}
	
	}
	
	*/
	
	var startTime = $("input#offerstartTime").val();
	var endTime = $("input#offerendTime").val();
	
	var hasError = false ;
	// check start date and end date is equaul
	if( eDate.compareTo ( sDate ) == 0)
	{
			
				// check time satrt time is greater than  end time 
				if( startTime  >= endTime  ) 
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
	
	// chekc for error i.e start date time is greater than end date time
	if(hasError)
	{
	
		$("div.dateValidationMessage1").removeClass("success").addClass("error").html(__("<span class='error help-inline'>End date time should be greater than start date time</span>"))
		.next("div").addClass("error").removeClass("success");
		$("div.dateValidationMessage2").removeClass("success").addClass("error").html(__("<span class='error help-inline'></span>"))
		.next("div").addClass("error").removeClass("success");
		
		
		errorExists['compareDate'] = false ;
	} else 	{
		$("div.dateValidationMessage1").removeClass("error").addClass("success")
			.html(__("<span class='success help-inline'>Valid</span>"))
				.next("div").removeClass("error").addClass("success");
		$("div.dateValidationMessage2").removeClass("error").addClass("success")
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
	var id = $('input#offerId').val();
	bootbox.confirm(__("Are you sure you want to move this offer to trash?"),__('No'),__('Yes'),function(r){
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
	$.ajax({
		url : HOST_PATH + "admin/usergeneratedoffer/movetotrash",
		method : "post",
		data : {
			'id' : id
		},
		dataType : "json",
		type : "post",
		success : function(data) {
			
			if (data != null) {
				
				window.location.href = HOST_PATH + "admin/usergeneratedoffer/";
				
			} else {
				
				window.location.href = HOST_PATH + "admin/usergeneratedoffer/";
			}
		}
	});	
}

/**
 * Make an offer offline
 * @param id
 * @author Raman
 */

function deleteVotes(id, offerid){
	
		bootbox.confirm(__("Are you sure you want to delete this vote?"),__('No'),__('Yes'),function(r){
	
	
		if(!r){
			return false;
		}
		else{
			deleteVote(id, offerid);
		}
		
	});
}

function deleteVote(id, offerid) {
	
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/usergeneratedoffer/deletevote",
		method : "post",
		data : {
			'id' : id
			
		},
		dataType : "json",
		type : "post",
		success : function(data) {
			
			if (data != null) {
				
				window.location.href = offerid;
				
			} else {
				
				window.location.href = offerid;
			}
		}
	});	
}
