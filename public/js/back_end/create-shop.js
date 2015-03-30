
/**
 * shop.js 1.0
 * @author spsingh
 */

/**
 * validRules oject contain all the messages that are visible when an elment
 * value is valid
 * 
 * structure to define a message for element key is to be element name Value is
 * message
 * @author spsingh
 */
var validRules = {

    shopName : __("Shop name looks great"),
    shopNavUrl : __("Valid Url"),
    shopMetaDescription : __("Meta description looks great"),
    shopTitle : __("Title looks great"),
    shopSubTitle : __("Subtitle looks great"),
    shopNotes : __("Notes looks great"),
    shopDeepLinkUrl  : __("Valid Url"),
    shopRefUrl : __("Valid Url"),
    shopActualUrl : __("Valid Url"),
    shopOverwriteTitle : __("Overwrite title looks great"),
    shopOverwriteSubTitle : __("Overwrite subtitle looks great"),
    shopBrowserTitle : __("Overwrite browser title looks great"),
    shopDescription : __("Shop text looks great"),
    shopViewCount : __("Views looks great"),
    serviceNumber : __("Phone number looks great"),
    shopCustomHeader : __("Custom header looks great"),
    selecteditors :  __("Editor looks great"),
    pageSlug : __("Page slug looks great"), 
    pageTitle : __("Page title looks great"), 
    pageSubTitle : __("Page subtitle looks great"),
    pageSubSubTitle : __("Page sub subtitle looks great"),
    pagemetaTitle : __("Page meta title looks great"),
    pagemetaDesc :  __("Page description looks great"),
    smallLogoFile : __("Main image lightbox! ok"),
    bigLogoFile : __("Second image lightbox! ok "),
    'title[]' : __("Chapter title  looks great"),
    customtextposition : __("Position looks great"),
    //returnPolicy : "Return policy looks great"
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
        
    shopName : __("Enter shop name"),
    shopNavUrl : __("Enter navigational Url"),
    shopMetaDescription : __("Enter meta description"),
    shopTitle : __("Enter title"),
    shopSubTitle : __("Enter subtitle"),
    shopNotes : __("Enter notes"),
    shopDeepLinkUrl : __("Enter Url"),
    shopRefUrl : __("Enter Ref Url"),
    shopActualUrl : __("Enter Url"),
    shopOverwriteTitle : __("Enter overwrite title"),
    shopOverwriteSubTitle : __("Enter overwrite subtitle"),
    shopBrowserTitle : __("Enter overwrite browser title"),
    shopDescription : __("Enter shop text"),
    shopViewCount : __("Enter views"),
    serviceNumber : __("Enter phone number"),
    shopCustomHeader : __("Enter Custom header"),
    selecteditors :  __("Select an editor"),
    pageSlug : __("Enter page slug"), 
    pageTitle : __("Enter page title"), 
    pageSubTitle : __("Enter page subtitle"),
    pageSubSubTitle : __("Enter page sub subtitle"),
    pagemetaTitle : __("Enter page meta title"),
    pagemetaDesc :  __("Enter page description"),
    smallLogoFile : __("Upload main image lightbox"),
    bigLogoFile : __("Upload second image lightbox"),
    'title[]' : __("Enter chapter title"),
    customtextposition : __("Enter Position below 10"),
    //deliveryCost : "Enter Delivery cost",
    //Deliverytime : "Enter Delivery time",
    //returnPolicy : "Enter Return policy"

};




/**
 * @author spsingh
 * global vaiables
 */



// holds the validation object ofr new shop form   
var validateNewShop = null; 
/**
 * execute when document is loaded 
 * @author spsingh
 */
$(document).ready(init);




/**
 * initialize all the settings after document is ready
 * @author spsingh
 */

function init()
{
	jQuery('#shopOverwriteTitle').NobleCount('#shopOverwriteTitleLeft',{
		max_chars: 68,
		prefixString : __("Shop overwrite title length ")
	});
	
	jQuery('#shopMetaDescription').NobleCount('#metaTextLeft',{
		max_chars: 150,
		prefixString : __("Shop meta description length ")
	});
	jQuery('#reasonsubtitle1').NobleCount('#reasonsubtitle1count',{
		max_chars: 512,
		prefixString : __("Shop reason sub title length ")
	});
	jQuery('#reasonsubtitle2').NobleCount('#reasonsubtitle2count',{
		max_chars: 512,
		prefixString : __("Shop reason sub title length ")
	});
	jQuery('#reasonsubtitle3').NobleCount('#reasonsubtitle3count',{
		max_chars: 512,
		prefixString : __("Shop reason sub title length ")
	});
	jQuery('#reasonsubtitle4').NobleCount('#reasonsubtitle4count',{
		max_chars: 512,
		prefixString : __("Shop reason sub title length ")
	});
	jQuery('#reasonsubtitle5').NobleCount('#reasonsubtitle5count',{
		max_chars: 512,
		prefixString : __("Shop reason sub title length ")
	});
	jQuery('#reasonsubtitle6').NobleCount('#reasonsubtitle6count',{
		max_chars: 512,
		prefixString : __("Shop reason sub title length ")
	});
	jQuery('#pagemetaTitle').NobleCount('#pagemetaTitleLeft',{
		max_chars: 68,
		prefixString : __("Shop meta title length ")
	});
	
	jQuery('#pagemetaDesc').NobleCount('#pagemetaDescLeft',{
		max_chars: 150,
		prefixString : __("Shop page meta description length ")
	});

	$('#addreason2').click(function() {
		$('#addreason2').hide();
	});

	$('#addreason1').click(function(){
		$('#reasons3').show();
		$('#addreason1').hide();
		$('#addreason2').show();
		if ($('#reasons4').css('display') == 'block') {
			$('#addreason2').hide();
		}
	});
	
	$('#addreason2').click(function(){
		$('#reasons4').show();
	});

	$('#deletereason').click(function(){
		$("#off").addClass("btn-primary").siblings().removeClass("btn-primary");
		$('#buyReasons').hide();
	});

	$('#deletereason1').click(function(){
		$('#reasons3').hide();
		$('#addreason1').show();
	});

	$('#deletereason2').click(function(){
		$('#reasons4').hide();
		$('#addreason2').show();
	});

	$('button#prefillData').click(function(){
		updateTitleSubtitle();
	});
	
	var options = {
			'maxCharacterSize': '',
			'displayFormat' : ''
	};
	$('#shopMetaDescription').textareaCount(options, function(data){

        jQuery('#metaTextLeft').val(__("Shop meta description length ") + (data.input) + __(" characters"));

    });
    

    $('#shopTitle').textareaCount(options, function(data){
        jQuery('#shopTitleLeft').val(__("Shop title length ") + (data.input) + __(" characters"));

    });

    $('#shopSubTitle').textareaCount(options, function(data){
        jQuery('#shopSubTitleLeft').val(__("Shop sub-title length ") + (data.input) + __(" characters"));
    });
    
    $('#shopName').textareaCount(options, function(data){
        jQuery('#shopNameLeft').text(__("Shop name length ") + (data.input) + __(" characters"));
    });

    $('#pageSlug').textareaCount(options, function(data){
        jQuery('#pageSlugLeft').text(__("Page slug length ") + (data.input) + __(" characters"));
    });
    
    $('#pageTitle').textareaCount(options, function(data){
        jQuery('#pageTitleLeft').text(__("Page title length ") + (data.input) + __(" characters"));
    });
    
    $('#pageSubTitle').textareaCount(options, function(data){
        jQuery('#pageSubTitleLeft').text(__("Page sub title length ") + (data.input) + __(" characters"));
    });
    
    $('#pageSubSubTitle').textareaCount(options, function(data){
        jQuery('#pageSubSubTitleLeft').text(__("Page sub sub title length ") + (data.input) + __(" characters"));
    });

    $('#pagemetaTitle').textareaCount(options, function(data){
        jQuery('#pagemetaTitleLeft').val(__("Shop meta title length ") + (data.input) + __(" characters"));
    });

    
    $('#pagemetaDesc').textareaCount(options, function(data){
        jQuery('#pagemetaDescLeft').val(__("Shop page meta description length ") + (data.input) + __(" characters"));
    });

    
    var options2 = {
            'maxCharacterSize': '',
            'displayFormat' : ''
    };
    $('#shopNotes').textareaCount(options2, function(data){
        jQuery('#shopNotesLeft').val(__("Short note about the shop length ") + (data.input) + __(" characters"));
    });

    CKEDITOR.replace( 'shopDescription',
            {
                //fullPage : true,
                ////extraPlugins : 'wordcount',
                customConfig : 'config.js' ,  
                toolbar :  'BasicToolbar'  ,
                height : "300"
    });

    CKEDITOR.replace('shopCustomText',
        {
            customConfig : 'config.js',  
            toolbar :  'BasicToolbar',
            height : "300"
        }
    );
    CKEDITOR.replace('moretextforshop', {customConfig : 'config.js', toolbar : 'BasicToolbar', height : "300"});
    
 
/*  $('#shopDescription').textareaCount(options3, function(data){
        $('#shopDescriptionLeft').val(__("Characters Left: ")+data.left);
    });
    */
    $("form").submit(function(){
    
        $("button#publishShopButton").attr('disabled' , 'disabled') ;
        
        if (! jQuery.isEmptyObject(invalidForm) ) 
            
            for(var i in invalidForm)
            {
                if(invalidForm[i])
                {
                    $("button#publishShopButton").removeAttr('disabled');
                    return false ;
                }
            }
        
        
    });
        
    
    jQuery('#shopCustomHeader').textareaCount(options, function(data){
        jQuery('#shopCustomHeaderLeft').val( __("Shop custom header length ") + (data.input) + __("  characters"));

    });
    
    var shopIds = new Object;
    
    $("input#searchsimilarshop").autocomplete({
        minLength: 1,
        source: function( request, response)
        {
            var similartShopVal = $('#searchsimilarshop').val()==''? undefined : $('#searchsimilarshop').val();
            var similarStorIds = $('#similarstoreord').val() ==''? undefined  : $('#similarstoreord').val() ;
            var shopname=[];
            $.ajax({
                url : HOST_PATH + "admin/shop/searchsimilar/keyword/" + similartShopVal + '/flag/0/selctedshop/'+ similarStorIds +'/currentshopId/0',
                method : "post",
                dataType : "json",
                type : "post",
                success : function(data) {
                
                    if (data != null) {
                        for(i=0;i<data.length;i++){
                            if(i%2!=0){
                                shopIds[data[i-1]] = data[i];   
                            }else{
                                shopname.push(data[i]);
                            }
                        }
                        
                        //pass arrau of the respone in respone onject of the autocomplete
                        response(shopname);
                    } 
                },
                error: function(message) {
                    // pass an empty array to close the menu if it was initially opened
                    response([]);
                }

             });
        },
        select: function( event, ui ) {
            $('#selectedShop').val(shopIds[(ui.item.value)]);
            $('#moveupBtnbutton,#movedownBtnbutton,#deletesimistorebtn').removeClass('btn-primary');
            $('#addsimilarstorebtn').addClass('btn-primary');
            //console.log(shopIds[(ui.item.value)]);
        }
    });
    
    mangesimilarstore();
    /* $("#Deliverytime,#returnPolicy,#deliveryCost").keydown(function(event) {
           if(event.shiftKey)
           {
                event.preventDefault();
           }

           if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9)    {
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
           }); */
    word_count("#shopDescription", __("Shop description length "),"#shopDescription_count");
    $('#shopDescription').keyup(function(){
            word_count("#shopDescription", __("Shop description length "),"#shopDescription_count");
    });
    
    validateFormAddNewShop();
}


function word_count(field,msg,count) {

    var number = 0;
    var matches = $(field).val().match(/\b/g);
    if(matches) {
        number = matches.length/2;
    }
    
    $(count).val(msg +  number + __(' word') + (number > 1 ? 's' : ''));

}




/**
 * assign Categories to shop
 * @author kkumar
 * @param  form post data
 */

function addCategory(e,catgory){
    
    var btn = e.target  ? e.target :  e.srcElement ;

    if(btn.type == undefined)
    {
        return false ; 
    }
    
    if(btn.type == "abbr")
    {
        btn = $(btn).chidlren('button');
    }
    
    if($(btn).hasClass('btn-primary'))
    {
        $(btn).removeClass('btn-primary') ;
        $("input#category-" + catgory).removeAttr('checked').valid() ;
    } else
    {
        $(btn).addClass('btn-primary');
        $("input#category-" + catgory).attr('checked' , 'checked').valid();
    }
}


/**
 * assign value to accountManagerName,editorName hidden fields
 * @author kkumar
 * @param  pass of the account/content manager
 */
 function getMangerName(name,type){
    if(type=='3'){
        
        if(name == 'None' )
            $('#accountManagerName').val("");
        else
            $('#accountManagerName').val(name);
     }else{
         $('#editorName').val(name); 
     }
 }

function getBallonTexthtml(el){
		editCount = parseInt($(el).attr('rel'));
		count = editCount != undefined ? editCount + 1 : count;
		$.ajax({
			url : HOST_PATH + "admin/shop/addballontext",
			type : "post",
			data : {'partialCounter' : count},
			success : function(data) {
				$("div#multidiv").append(data);
				$(el).attr('rel',count);
				count++ ;
			}
		});
	}

function removeballontexthtml(el) {
	bootbox.confirm(__("Are you sure you want to delete this Ballon Text ?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		}
		else{
			$.ajax({
				url : HOST_PATH + "admin/shop/deleteballontext",
				type : "post",
				data : {'id' : $(el).attr('rel')},
				success : function(data) {
					var textNumber = $(el).attr('rel');
					$(el).parents('div.multidivchild').remove();
					if (textNumber != '') {
						window.location.reload(true);
					}
				}
			});
		}
	});
}
 
 /**
  * set status of the deepLink/affiliate/howTouseStatus buttons
  * @author kkumar
  * @param  pass of the event,name,status
  */
 
 function usergenrated(e, name ,status){
     var btn = e.target  ? e.target :  e.srcElement ;
     $(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
     if(status=='on'){
         $('#usergenratedchk').attr('checked','cheked');
     }else{
         $('#usergenratedchk').removeAttr('checked'); 
     }
 }
 
 /**
  * set status of allow discussions
  * @author Raman
  * @param  pass of the event,name,status
  */
 function discussionCheck(e, status){
     var btn = e.target  ? e.target :  e.srcElement ;
     $(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
     if(status=='on'){
         $('#discussions').attr('checked','cheked');
     }else{
         $('#discussions').removeAttr('checked'); 
     }
 }
 
 
 /**
  * set status of the add to google plus rich snippet
  * @author Raman
  * @param  pass of the event,name,status
  */
 function addToSearchResults(e, status){
     var btn = e.target  ? e.target :  e.srcElement ;
     $(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
     if(status=='on'){
         $('#addtosearch').attr('checked','cheked');
     }else{
         $('#addtosearch').removeAttr('checked'); 
     }
 }
 /**
  * set status of the deepLink/affiliate/howTouseStatus buttons
  * @author kkumar
  * @param  pass of the event,name,status
  */
 
 function setOnOff(e, name ,status){
	 var btn = e.target  ? e.target :  e.srcElement ;
	 switch(name)
		{
			case "deepLink" :
					
				 $(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
				 if(status=='on'){
					 $('#deepLinkStatus').val(1); 
					 $("#shopDeepLinkUrl").prop("disabled",false);

                 }else{
                     $('#deepLinkStatus').val(0); 
                     $("#shopDeepLinkUrl").val('');
                     $("#shopDeepLinkUrl").prop("disabled",true);
                     
                     $(btn).parents("div.mainpage-content-right")
                        .children().removeClass("error focus succuss")
                        .children("span.help-inline").remove();
                        
                     
                 }
                
            break;
            
            case "affiliate" :
                
                 $(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
                 if(status=='on'){
                     
                     $('#affiliateProgStatus').val(1);
                     
                 }else{
                     
                     $('#affiliateProgStatus').val(0);
                 }
                
            break;  
            
            case "Delivery" :
                
                $(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
                $(btn).addClass("active").siblings().removeClass("active");
                if(status=='always'){
               	 	 $('#freeDelivery').val(2);
               	 	 $('#div-cost').hide();
				 } else if(status=='starting') {
					 $('#freeDelivery').val(1);
					 $('#div-cost').show();
					 
				 } else if(status=='none') {
					 $('#freeDelivery').val(0);
					 $('#div-cost').show();
				 } else if(status=='nonebtn') {
					 $('#freeDelivery').val(3);
					 $('#div-cost').hide();
				 }
				
			break;	
			
			case 'howtouse' :
				 $(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
				if(status=='on'){
			    	
			    	$('#howTouseStatus').val(1);
			    	 $("select[name=shopHowToUsePageId]")
			    	 .removeAttr('disabled');
			    	 
			    }else{
			    	
			    	$('#howTouseStatus').val(0);
			    	$("select[name=shopHowToUsePageId]")
			    	 .attr('disabled', 'disabled' );
			    }
				
			break;
			
			case 'reasons' :
				$(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
				if(status=='on'){
			    	$('#buyReasons').show();
			    }else{
			    	$('#buyReasons').hide();
			    }
				
			break;
      case 'customText' :
                $(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
                if (status=='on') {
                    $('#customText').show();
                    $('#customtextposition').show();
                    $('#showcustomtext').val(1);
                } else {
                    $('#customText').hide();
                    $('#customtextposition').hide();
                    $('#showcustomtext').val(0);
                }
      break;
			default:
				
				if(status == 'toggle-btn')
                {
                    
                     if($(btn).hasClass('btn-primary'))
                     {
                         
                         $(btn).removeClass('btn-primary');
                         $("input[ name="+ name +"]:hidden").val(0);
                     }else{
                     
                         $(btn).addClass('btn-primary');
                         $("input[ name="+ name +"]:hidden").val(1);
                         
                     } 
                     
                     return true ;
                     
                }
                
                $(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
            
                var val = status == 'on' ? 1 :  0 ;
                $("input[name="+ name + "]").val(val) ;
                
            
        }
 }

 
/**
 * @author spsingh
 * apply validation on cretae new shop page
 */
function validateFormAddNewShop(){
    
    validateNewShop  = $("form#createShop")
    .validate(
            {
                errorClass : 'error',
                validClass : 'success',
                errorElement : 'span',
                ignore: ".ignore, :hidden",
                afterReset  : resetBorders,
                errorPlacement : function(error, element) {
                    
                        element.parent("div").prev("div")
                                .html(error);
                },
                rules : {
                    shopName : {
                        required : true,
                        minlength : 2
                    },  
                    shopNavUrl : {
                        required:  true ,
                        minlength : 1,

                        remote : {
                            // validating and filtering  navigational url from server 
                            url: HOST_PATH + "admin/shop/validatenavurl",
                            type: "post" ,
                            beforeSend  : function ( xhr ) {
                                
                                $('span[for=shopNavUrl]').html(__('please wait, validating...')).addClass('validating').show()
                                .parent('div').attr('class' , 'mainpage-content-right-inner-right-other focus')
                                .next("div").removeClass("success");
                                
                              },
                              complete : function(e) {
                                
                                $('span[for=shopNavUrl]' , $("[name=shopNavUrl]").parents('div.mainpage-content-right') ).removeClass('validating') ;
                                
                                res = $.parseJSON(e.responseText);
                                
                                if(res.status == "200")
                                {
                                    $('span[for=shopNavUrl]' , $("[name=shopNavUrl]").parents('div.mainpage-content-right') )
                                    .html(validRules['shopNavUrl'])
                                    .attr('remote-validated' , true);
                                    
                                    $('#shopNavUrl').val(res.url);
                                    
                                    $("input[name=shopNavUrl]").parent('div').prev("div").removeClass('focus')
                                    .removeClass('error').addClass('success');
                                    
                                } else {
                                    $('span[for=shopNavUrl]' , $("[name=shopNavUrl]").parents('div.mainpage-content-right') )
                                    .attr('remote-validated' , false);
                                }
                            }
                        }                   

                    },
                    shopTitle : {
                        required : true,
                        minlength : 2
                    },
                    shopSubTitle : {
                        required : true,
                        minlength : 2
                    },
                     'selectedCategoryies[]':{ 
                          required:true 
                    },
                      
                    shopDeepLinkUrl: {
                        required : true ,
                        regex : /((http|ftp|gopher|telnet|news|com|edu|gov):\/\/|)([_a-z\d\-]+(\.[_a-z\d\-]+)+)(([_a-z\d\-\\\.\/]+[_a-z\d\-\\\/])+)*/
                    },
                    shopRefUrl :{
                        required : true,
                        regex : /((http|https):\/\/)([_a-z\d\-]+(\.[_a-z\d\-]+)+)(([_a-z\d\-\\\.\/]+[_a-z\d\-\\\/])+)*/
                    },
                    shopActualUrl :{
                        regex : /((http|https):\/\/)([_a-z\d\-]+(\.[_a-z\d\-]+)+)(([_a-z\d\-\\\.\/]+[_a-z\d\-\\\/])+)*/
                    },
                    shopViewCount : {
                        regex : /^\d+$/  
                    },                  
                    serviceNumber : {
                        required:  true
                    },
                    selecteditors : {
                        required : true
                    },
                    customtextposition : {
                        regex : /^([1-9]|10)$/
                    },
                    pageSlug :  
                    {
                        required : function(el)
                        {
                            if($("#howTouseStatus").val() == 1) {
                                return true ;
                            }
                            return false;
                        }
                    },
                    pageTitle :  
                    {
                        required : function(el)
                        {
                            if($("#howTouseStatus").val() == 1) {
                                return true ;
                            }
                            return false;
                        }
                    },
                    pageSubTitle :{
                        required : function(el)
                        {
                            if($("#howTouseStatus").val() == 1) 
                            {
                                return true ;
                            }
                            return false;
                        }
                    },
                    pageSubSubTitle :{
                        required : function(el)
                        {
                            if($("#howTouseStatus").val() == 1) 
                            {
                                return true ;
                            }
                            return false;
                        }
                    },
                    pagemetaTitle :{
                        required : function(el)
                        {
                            if($("#howTouseStatus").val() == 1) 
                            {
                                return true ;
                            }
                            return false;
                        }
                    },
                    pagemetaDesc :{
                        required : function(el)
                        {
                            if($("#howTouseStatus").val() == 1) 
                            {
                                return true ;
                            }
                            return false;
                        }
                    },                  
                    smallLogoFile:
                    {
                        required:  function(el)
                        {
                            if($("#howTouseStatus").val() == 1) 
                            {
                                return true ;
                            }
                            return false;
                        } 
                    },
                    bigLogoFile:
                    {
                        required:  function(el)
                        {
                            if($("#howTouseStatus").val() == 1) 
                            {
                                return true ;
                            }
                            return false;
                        }
                    },
                },
                // error messages
                messages : {
                    shopName : {
                        required : __("Please enter shop name")
                    },  
                    shopNavUrl : {
                        required  : __("Please enter navigational Url"),
                        remote : __("Permalink already exists")

                    },
                    shopTitle : {
                        required  : __("Please enter title")
                    },
                    shopSubTitle : {
                        required  : __("Please enter subtitle")
                    },
                    'selectedCategoryies[]': { 
                          required: __("Please select a category") 
                    },
                    shopDeepLinkUrl: {
                        required  : __("Please enter Url"),
                        regex : __("Invalid Url")
                    },
                    shopRefUrl :{
                        required  : "Please enter Ref Url",
                        regex : __("Invalid Url")
                    },
                    shopActualUrl :{
                        required : __("Please enter Url"),
                        regex : __("Invalid Url")
                    }, 
                    shopViewCount : {
                        regex : __("Please enter numeric value")
                    },                  
                    serviceNumber : { 
                        regex:  __("Please enter number like (088) 42 64 333")
                    },
                    selecteditors : {
                        
                        required : __("Please select an editor")
                    },
                    pageSlug : {
                        required : __("Please enter page slug")
                    }, 
                    pageTitle : {
                        
                        required : __("Please enter page title")
                    }, 
                    pageSubTitle : {
                        
                        required : __("Please enter page subtitle")
                    },
                    pageSubSubTitle : {
                        required : __("Please enter page sub subtitle")
                    },
                    pagemetaTitle : {
                        
                        required : __("Please enter page meta title ")
                    },
                    pagemetaDesc : {
                        
                        required : __("Please select page description")
                    },
                    smallLogoFile : {
                        
                        required : __("Please uplaod main image lightbox")
                    },
                    bigLogoFile : {
                        
                        required : __("Please upload second image lightbox")
                    },
                    customtextposition :{
                        regex : __("Please enter numeric value less than 10")
                    }
                        
                },

                onfocusin : function(element) {
                    
                
                    // display hint messages when an element got focus 
                    if (!$(element).parent('div').prev("div")
                            .hasClass('success')) {
                        
                         var label = this.errorsFor(element);
                         
                         if( $( label ).attr('hasError')  )
                         {
                             if($( label ).attr('remote-validated') != "true")
                                {
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
                                    .prev('div'))   .attr('remote-validated' , true).removeClass(validClass) ;
                    
                    $("button#publishShopButton").removeAttr('disabled');
                    
                    if(element.name == 'selectedCategoryies[]')
                    {
                        $(window).scrollTop(1000) ;
                    }
                },
                unhighlight : function(element,
                        errorClass, validClass) {
                    
                    
                    
                    
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
                    .removeClass(this.errorClass)
                    .removeClass("focus");
                    $(label).append( validRules[element.name] ) ;
                    label.addClass('valid') ;
                    
                }

            });

 }
 

/**
 * @author spsingh
 * @param el form elements
 * remove border of elements
 */
function resetBorders(el)
{
    
    $(el).each( function(i, o){
        $(o).parent('div')
        .removeClass("error success")
        .prev("div").removeClass('focus error success') ;
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


/**
 * set title/subtitle based on the shop name
 * @author kkumar
 * updated By: Raman
 * @param  noting
 */

function updateTitleSubtitle(){
    var d=new Date();
    var month=new Array();
    month[0]= __("January");
    month[1]= __("February");
    month[2]= __("March");
    month[3]= __("April");
    month[4]= __("May");
    month[5]= __("June");
    month[6]= __("July");
    month[7]= __("August");
    month[8]= __("September");
    month[9]= __("October");
    month[10]= __("November");
    month[11]= __("December");
    var n = month[d.getMonth()]; 
    var currentYear = (new Date).getFullYear();
    var Shopname = $('#shopName').val();
    if(parseInt($('#shopName').val().length)>0){
    
      $('#shopOverwriteTitle,#shopTitle').val( Shopname + " " + __("- Kortingscode.nl"));
      $('#shopTitle').val( Shopname + " " + __("Kortingscodes") + " " + "[year]").valid();
      $('#shopMetaDescription').val(__("De meest actuele")+ " " + Shopname + " " + __("kortingscodes, aanbiedingen en kortingsbonnen. Pak gratis een")+ " " + Shopname + " " + __("kortingscode en bespaar!"));
      $('#shopSubTitle').val(__("Actuele")+ " " + Shopname + " " + __("aanbiedingen & kortingscodes -") + " " + "[month]" + " " +  "[year]").valid();
     
    }
}


var invalidForm = {} ;
var errorBy = "" ;
function checkFileType(e,max)
{
     var el = e.target  ? e.target :  e.srcElement ;
     
     var regex = /png|jpg|jpeg|PNG|JPG|JPEG/ ;
     
     if( regex.test(el.value) )
     {
        
         invalidForm[el.name] = false ;
         $(el).parents("div").addClass('success').removeClass('error');  
         $(el).parents("div.mainpage-content-right")
         .children("div.mainpage-content-right-inner-right-other").removeClass("focus")
         .html(__("<span class='success help-inline'>Valid file</span>"));
         
     } else {
         $(el).parents("div").addClass('error').removeClass('success');
         $(el).parents("div.mainpage-content-right")
         .children("div.mainpage-content-right-inner-right-other").removeClass("focus")
         .html(__("<span class='error help-inline'>Please upload only jpg or png image</span>"));
         
         invalidForm[el.name] = true ;
         errorBy = el.name ;
         
         
     }
 }
function mangesimilarstore(){
     
        selectedshop();
        $('#addsimilarstorebtn').click(
                function(){
                       if($('#selectedShop').val()!=''){
                         var size = $("#similarshopListul-li li").size();
                            className = 'grid-line2';
                            if (size % 2 == 0) {
                                className = 'grid-line1';
                            }
                         $('#similarshopListul-li')
                            .append('<li class="'
                                            + className
                                            + '" value='
                                            + parseInt($('#selectedShop').val())
                                            + '>'
                                            + $('#searchsimilarshop').val()
                                            + '</li>');
                        
                         $('#searchsimilarshop').val('');
                         $('#selectedShop').val('');
                          selectedshop();
                          reoderElements();
                     }
                 
                 
                }
        );
        
        $('#deletesimistorebtn').click(
                function() {
                    $('#similarshopListul-li li')
                            .each(
                                    function(index) {
                                        if ($(this).hasClass('selected')) {
                                            $(this).remove();
                                            reoderElements();
                                        }
                                    });

                    $('.similarshopListul-li li').each(
                            function(index) {
                                var className = 'grid-line2';
                                if (index % 2 == 0) {
                                    className = 'grid-line1';
                                }
                                $(this)
                                        .removeClass(
                                                'grid-line1 grid-line2')
                                        .addClass(className);
                                
                            });
                });
        
        $('.down').click(
                function() {
                    var size = $(".sidebar-content-box-left ul li").size();
                    $('.sidebar-content-box-left ul li').each(
                            function(index) {
                                if ($(this).hasClass('selected')) {
                                    if (index < size - 1) {
                                        classsName = 'grid-line2';
                                        removeclassName = 'grid-line1';
                                        if ($(this).hasClass('grid-line2')) {
                                            classsName = 'grid-line1';
                                            removeclassName = 'grid-line2';
                                        }
                                        $(this).clone(true).insertAfter(
                                                $(this).next())
                                                .addClass(classsName).removeClass(
                                                        removeclassName);
                                        ;
                                        $(this).next().addClass(removeclassName)
                                                .removeClass(classsName);
                                        $(this).remove();
                                        reoderElements();
                                    }
                                }
                            });
                });
        
        $('.up').click(
                function() {

                    $('.sidebar-content-box-left ul li').each(
                            function(index) {

                                if ($(this).hasClass('selected')) {
                                    if (index) {
                                        classsName = 'grid-line2';
                                        removeclassName = 'grid-line1';
                                        if ($(this).hasClass('grid-line2')) {
                                            classsName = 'grid-line1';
                                            removeclassName = 'grid-line2';
                                        }
                                        $(this).clone(true).insertBefore(
                                                $(this).prev())
                                                .addClass(classsName).removeClass(
                                                        removeclassName);
                                        $(this).prev().addClass(removeclassName)
                                                .removeClass(classsName);
                                        $(this).remove();
                                        reoderElements();
                                    }
                                }
                            });
                });

 }
 
 
 function selectedshop(){
    
     $(".sidebar-content-box-left ul li").click(function() {
        var size = $(".sidebar-content-box-left ul li").size();

        $('.sidebar-content-box-left ul li').each(function(index) {
            $(this).removeClass('selected');

        });
        
        $('#moveupBtnbutton,#movedownBtnbutton,#deletesimistorebtn').addClass('btn-primary');
        $('#addsimilarstorebtn').removeClass('btn-primary');
        $(this).addClass('selected');
            
   });
 }
 
 
 function reoderElements() {
        var similarstoreArray = new Array();
        $('.sidebar-content-box-left ul li').each(function(index) {
            similarstoreArray[index] = $(this).attr('value');
        });
        $('#similarstoreord').val(similarstoreArray);
}

 /**
  * function used to render html for multiple chapters in articles
  * @author cbhopal
  */
 function getchapterhtml(el){
    editCount = parseInt($(el).attr('rel'));
    count = editCount != undefined ? editCount + 1 : count;
    $.ajax({
        url : HOST_PATH + "admin/shop/chapters",
        type : "post",
        data : {'partialCounter' : count},
        success : function(data) {
            $("div#multidiv").append(data);
            $(el).attr('rel',count);
            count++ ;
        }
    });
 }

 function removechapterhtml(el){
    
    bootbox.confirm(__("Are you sure you want to delete this chapter ?"),__('No'),__('Yes'),function(r){
        if(!r){
            return false;
        }
        else{
            $.ajax({
                url : HOST_PATH + "admin/shop/deletechapters",
                type : "post",
                data : {'id' : $(el).attr('rel')},
                success : function(data) {
                    //$(el).parent('div.multidivchild').remove();
                    window.location.reload(true);
                }
            });
        }
        
    });
    
 }
