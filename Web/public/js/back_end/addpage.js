
$(document).ready(function(){
	
	$('#pagemetaTitle').NobleCount('#metaTextLeft',{
		max_chars: 68,
		prefixString : __("Page meta title length ")
	});
	
	$('#pagemetaDesc').NobleCount('#metaDescLeft',{
		max_chars: 150,
		prefixString : __("Page meta description length ")
	});
	
	var options = {
			'maxCharacterSize': '' ,
			'displayFormat' : ''
	};
  	jQuery('#pagemetaTitle').textareaCount(options, function(data){
		jQuery('#metaTextLeft').val(__("Page meta title length ") + (data.input) + __("  characters"));

	});
  	
  	jQuery('#pagemetaDesc').textareaCount(options, function(data){
		jQuery('#metaDescLeft').val(__("Page meta description length ") + (data.input) + __("  characters"));

	});
  	
  	jQuery('#pageCustomHeader').textareaCount(options, function(data){
		jQuery('#pageCustomHeaderLeft').val(__("Page custom header length ") + (data.input) + __("  characters"));

	});
  	
  	
	//alert($('#pageTemplate').val());
	moneysavingtemp($('#pageTemplate').val());
	
	$(".multiselect").multiselect();
	
   $('form#addPageform').submit(function(){
	
		savePage();
		
	});
   
   $('div.multiselect ul li').click(selectWebSiteInList);

   $('button#prefillData').click(function(){
		$("#pagemetaTitle").val($("#pageTitle").val());
	});
});


/**
 * Code for category multiselect 
 * admin can select category without control
 * @author Er.kundal
 */ 
jQuery.fn.multiselect = function() {
	$(this).each(function() {
		
		var checkboxes = $(this).find("input:checkbox");
		checkboxes.each(function() {
			
			var checkbox = $(this);
			// Highlight pre-selected checkboxes
			if (checkbox.attr("checked"))
				checkbox.parent().addClass("multiselect-on selected");

			// Highlight checkboxes that the user selects
			checkbox.click(function() {
				if (checkbox.attr("checked"))
					checkbox.parent().addClass("multiselect-on selected");
				else
					checkbox.parent().removeClass("multiselect-on selected");
			});
		});
	});
};

function selectWebSiteInList() {

	if (($(this).children('input')).is(':checked')) {

		$(this).children('input').removeAttr('checked');
		$(this).removeClass('selected');

	} else {

		$(this).children('input').attr('checked', 'checked');
		$(this).addClass('selected');
	}
}

function moneysavingtemp(moneysavingid){
	
	
	if(moneysavingid == 13){
		$("#artcatg-div").show();
	}else{
		$("#artcatg-div").hide();
	}
	
}


function savePage()
{
	if($("form#addPageform").valid())
	{
		$('#publishBtn').attr('disabled' ,"disabled");
		return true;
		
	}else {
			
		return false;
	}
	
}

var validRules = {

	pageTitle : __("Page title looks great"),
	pagepermalink : __("Permalink looks great"),
};


/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 * structure to define a message for element key is to be element name Value is
 * message
 */
var focusRules = {

	pageTitle : __("Enter page title"),
	pagepermalink : __("Enter permalink"),

};



$(document).ready(init);
var errorExists = {} ;
function init() {
	
	$('form#addPageform').submit(function(){
		
		//validatePublishdate(); 
		
		for(var i in errorExists)
		{
			if( errorExists[i] != true )
			{
				//$('input#addOfferBtn').removeAttr('disabled');
				return false ;
			}else {
				//$('input#addOfferBtn').attr('disabled' ,"disabled");
				return true ;
			}
			
		}
		
		savePage();
		
	});
	
	/*var options = {
			'maxCharacterSize': 160,
			'displayFormat' : ''
	};
	$('#pagemetaDesc').textareaCount(options, function(data){
		$('#metaTextLeft').val("Characters Left: "+data.left);
	});*/
	
	manageWidgets();
	
	$('#publishDate').datepicker({'startDate': 'today','autoclose':true,'format': 'dd-mm-yyyy'});
	$('#publishTimehh').timepicker({
	 	minuteStep: 1,
        template: 'modal',
        showSeconds: false,
        showMeridian: false,
        'afterUpdate'  : validatePublishdate
    });
   
	 CKEDITOR.replace( 'pageDesc',
				{
					//fullPage : false,
					//////extraPlugins : 'wordcount',
					customConfig : 'config.js' ,  
					toolbar :  'BasicToolbar'  ,
					height : "400"
	});

	//CKEDITOR.replace( 'pageDesc' );
	 
	 $('#publishDate').datepicker().on('changeDate' , validatePublishdate);
	 
	 $("#maxOffer").keydown(function(event) {
		   if(event.shiftKey)
		   {
		        event.preventDefault();
		   }

		   if (event.keyCode == 46 || event.keyCode == 8)    {
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
	 
	 $("#numberofDays").keydown(function(event){
		   if(event.shiftKey)
		   {
		        event.preventDefault();
		   }

		   if (event.keyCode == 46 || event.keyCode == 8)    {
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
	 
	 $("#clickConstraintTxt").keydown(function(event) {
		   if(event.shiftKey)
		   {
		        event.preventDefault();
		   }

		   if (event.keyCode == 46 || event.keyCode == 8)    {
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
	validateFormAddNewPage();
		
}

/* Widget section start */
function manageWidgets() {
	reoderElements();
	
	
	$(".sidebar-content-box-left ul li").click(function() {
		// alert($(this).attr('value'));
		var size = $(".sidebar-content-box-left ul li").size();

		var flag = 1;
		if ($(this).hasClass('selected')) {
			flag = 0;
		}
		$('.sidebar-content-box-left ul li').each(function(index) {
			$(this).removeClass('selected');

		});

		if (flag) {
			$(this).addClass('selected');

		}

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
				
						$('div#storesDiv').animate({
						    scrollTop: "-=20"
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
						$('div#storesDiv').animate({
							 scrollTop: "+=20"
						});
			});

	$('.deletewidget')
			.click(
					function() {
						$('.sidebar-content-box-left ul li')
								.each(
										function(index) {
											if ($(this).hasClass('selected')) {
												// if($(this).attr('type')>4){
												$(
														'div#widgetListUserdefined select#widgetListUserdefinedSelect')
														.append(
																'<option value="'
																		+ $(
																				this)
																				.attr(
																						'value')
																		+ '">'
																		+ $(
																				this)
																				.html()
																		+ '</option>');
												// }
												$(this).remove();
												reoderElements();
												// slectEvent();
											}
										});

						$('.sidebar-content-box-left ul li').each(
								function(index) {
									var className = 'grid-line2';
									if (index % 2 == 0) {
										className = 'grid-line1';
									}
									$(this)
											.removeClass(
													'grid-line1 grid-line2')
											.addClass(className);
									// alert(index);
								});
					});

	$('.addwidget').click(function() {

		addWidgetPopupPage();

	});

}


function addWidgetPopupPage()
{
	
	if( jQuery("div#overlay").length == 0)
	{
		

		var overlay = jQuery('<div id="overlay"></div><div class="fnyBoxCont"><div class="fancybox-skin" style="padding: 15px;">' + jQuery('#widgetListUserdefined').html()+ '</div></div>') ;
		
		//var overlay = jQuery('<div id="overlay"><div class="fancybox-skin" style="padding: 15px;">'+jQuery('#widgetListUserdefined').html()+'</div>');
		
		overlay.appendTo(document.body);
		
		
		jQueryt = jQuery(document.body); // CHANGE it to the table's id you have
		jQuery(".fancybox-skin").css({
		  top  : ((jQueryt.height() / 2)-(jQuery('.fancybox-skin').height()/2)),
		  left : ((jQueryt.width() / 2)-(jQuery('.fancybox-skin').width()/2))
		});
	} 
	jQuery('div#overlay').css({ opacity: 0.8 });
}


/**
 * remove overlay
 * @author kraj
 */
function hideWidget()
{
	jQuery('div#overlay').remove();
	jQuery('div.fnyBoxCont').remove();
	return true ;
}




function addWidget() {
	
	$('div.fnyBoxCont div.fancybox-skin span#selectWidgetError.error').hide();
	if($('.fnyBoxCont select#widgetListUserdefinedSelect option:selected').val()==''){
	   $('div.fnyBoxCont div.fancybox-skin span#selectWidgetError.error').show();
	  return false;
	}
	var size = $(".sidebar-content-box-left ul li").size();
	className = 'grid-line2';
	if (size % 2 == 0) {
		className = 'grid-line1';
	}
	liCount = parseInt(size);
	var index = $('.fnyBoxCont select#widgetListUserdefinedSelect').get(0).selectedIndex;
	$(
			'div#widgetListUserdefined select#widgetListUserdefinedSelect option:eq('
					+ index + ')').remove();
	$('#widgetListul-li')
			.append(
					'<li class="'
							+ className
							+ '" value='
							+ $(
									'.fnyBoxCont select#widgetListUserdefinedSelect option:selected')
									.val()
							+ ' type='
							+ liCount
							+ '>'
							+ $(
									'.fnyBoxCont select#widgetListUserdefinedSelect option:selected')
									.text() + '</li>');
	hideWidget();
	slectEvent();
	reoderElements();

 }

function slectEvent() {
	$('.sidebar-content-box-left ul li').off('click');

	$(".sidebar-content-box-left ul li").click(function() {
		var size = $(".sidebar-content-box-left ul li").size();
		var flag = 1;
		if ($(this).hasClass('selected')) {
			flag = 0;
		}
		$('.sidebar-content-box-left ul li').each(function(index) {
			$(this).removeClass('selected');

		});

		if (flag) {
			$(this).addClass('selected');

		}

	});
}

function reoderElements() {
	var selectedWidgets = new Array();
	$('.sidebar-content-box-left ul li').each(function(index) {
		selectedWidgets[index] = $(this).attr('value');
	});
	$('#selectedWigetForPage').val(selectedWidgets);
}
/* widget section end */

function selectPagetype(dIv) {
    
	$("select#pageTemplate option").each(function(index, val){
		$(this).show();
	});
	
	$("#" + dIv).addClass("btn-primary").siblings().removeClass("btn-primary");
	switch (dIv) {
	case 'defaultPagebtn':
		$('#offerconstraint').hide();
		$("input#selectedpageType").removeAttr('checked');
		break;
	case 'offerListpageBtn':
		$('#offerconstraint').show();
		$("input#selectedpageType").attr('checked', 'checked');
		$("select#pageTemplate option").each(function(index, val){
			if($(this).val().replace(/(^[\s]+|[\s]+$)/g, '')!=''){
				var value = parseInt($(this).val());
				if(value>3){
					$(this).hide();
				}
			}
		});
		break;

	}
	
	$("select#pageTemplate").val(''); 

	
	

	//show elements
	

}

function lockPageStatus(dIv) {
	$("#" + dIv).addClass("btn-primary").siblings().removeClass("btn-primary");
	switch (dIv) {
	case 'lockbtnYes':
		$("input#lockPageStatuschk").attr('checked', 'checked');
		break;
	case 'lockbtnNo':
		$("input#lockPageStatuschk").removeAttr('checked');
		break;

	}
}

function showSitemapStatus(dIv) {
    $("#" + dIv).addClass("btn-primary").siblings().removeClass("btn-primary");
    switch (dIv) {
    case 'showSitemapYes':
	    $("input#showSitemapStatuscheck").val(1);
	    break;
    case 'showSitemapNo':
	    $("input#showSitemapStatuscheck").val(0);
	    break;
    }
}

function showMobileMenuStatus(dIv) {
    $("#" + dIv).addClass("btn-primary").siblings().removeClass("btn-primary");
    switch (dIv) {
    case 'showMobileMenuYes':
	    $("input#showMobileMenuStatuscheck").val(1);
	    break;
    case 'showMobileMenuNo':
	    $("input#showMobileMenuStatuscheck").val(0);
	    break;
    }
}

function setOffersOrder(dIv) {

	$("#" + dIv).addClass("btn-primary").siblings().removeClass("btn-primary");
	switch (dIv) {
	case 'ascendingOffer':
		$("input#offersOrderchk").attr('checked', 'checked');
		break;
	case 'decendingOffer':
		$("input#offersOrderchk").removeAttr('checked');
		break;

	}

}

function constraint(e, type) {
	var check = e.target ? e.target : e.srcElement;
   
	switch (type) {

	case 'time':
		if ($(check).is(":checked")) {
			$('#showTimeConstraint').show();
		} else {
			$('#showTimeConstraint').hide();
		}
        break;
        
	case 'word':
		if ($(check).is(":checked")) {
			$('#wordConstraint').show();
		} else {
			$('#wordConstraint').hide();
		}
		break;
		
	case 'award':
		if ($(check).is(":checked")) {
			$('#awardConstraint').show();
		} else {
			$('#awardConstraint').hide();
		}
		break;
		
	case 'clicks':
		if ($(check).is(":checked")) {
			$('#clickConstraint').show();
		} else {
			$('#clickConstraint').hide();
		}
		break;
	}
	
}

function offerType(dIv, type, value) {
	// $("#" +
	// dIv).addClass("btn-primary").siblings().removeClass("btn-primary");
	switch (type) {

	case 'coupon':
		if ($("#" + dIv).hasClass("btn-primary")) {
			$("#" + dIv).removeClass("btn-primary");
			if (value == 'regular') {
				$("input#coupconCoderegularchk").removeAttr('checked');
			} else if (value == 'editor') {
				$("input#coupconCodeeditorchk").removeAttr('checked');
			} else {
				$("input#coupconCodeeclusivechk").removeAttr('checked');
			}
		} else {
			$("#" + dIv).addClass("btn-primary");
			if (value == 'regular') {
				$("input#coupconCoderegularchk").attr('checked', 'checked');
			} else if (value == 'editor') {
				$("input#coupconCodeeditorchk").attr('checked', 'checked');
			} else {
				$("input#coupconCodeeclusivechk").attr('checked', 'checked');
			}

		}
		break;
	case 'sale':
		if ($("#" + dIv).hasClass("btn-primary")) {
			$("#" + dIv).removeClass("btn-primary");
			if (value == 'regular') {
				$("input#saleregularchk").removeAttr('checked');
			} else if (value == 'editor') {
				$("input#saleeditorchk").removeAttr('checked');
			} else {
				$("input#saleeclusivechk").removeAttr('checked');
			}
		} else {
			$("#" + dIv).addClass("btn-primary");
			if (value == 'regular') {
				$("input#saleregularchk").attr('checked', 'checked');
			} else if (value == 'editor') {
				$("input#saleeditorchk").attr('checked', 'checked');
			} else {
				$("input#saleeclusivechk").attr('checked', 'checked');
			}

		}
		break;
	case 'printable':
		if ($("#" + dIv).hasClass("btn-primary")) {
			$("#" + dIv).removeClass("btn-primary");
			if (value == 'regular') {
				$("input#printableregularchk").removeAttr('checked');
			} else if (value == 'editor') {
				$("input#printableeditorchk").removeAttr('checked');
			} else {
				$("input#printableexclusivechk").removeAttr('checked');
			}
		} else {
			$("#" + dIv).addClass("btn-primary");
			if (value == 'regular') {
				$("input#printableregularchk").attr('checked', 'checked');
			} else if (value == 'editor') {
				$("input#printableeditorchk").attr('checked', 'checked');
			} else {
				$("input#printableexclusivechk").attr('checked', 'checked');
			}

		}
		break;

	}
}


function setAuthorName(){
 
	$('#selectedpageAuthorName').val($('#pageAuthor option:selected').text());	
}

var request = true;

function validateFormAddNewPage(){

	
	$("form#addPageform")
	.validate(
			{
				errorClass : 'error',
				validClass : 'success',
				errorElement : 'span',
				ignore: ":hidden",
				errorPlacement : function(error, element) {
					
					element.parent("div").next("div")
					.html(error);
				},
				// validation rules
				rules : {
					pageTitle : {
						required : true,
						minlength : 2
					},	
					pagepermalink : {
						required : true,
						remote : function() {
                        	
                        	if(request == true){
	                        	$.ajax({
	                        		url: HOST_PATH + "admin/page/validatepermalink",
						        	type: "post" ,
						        	data: { 'pagepermalink' : $("#pagepermalink").val() ,'id' :   $('#pageId').val() },
						        	success : function(e) {
						        		
						        		
						        		res = $.parseJSON(e);
						        		if(res.status == "200")
						        		{
						        			$('span[for=pagepermalink]' , $("[name=pagepermalink]").parents('div.mainpage-content-right') )
						        			.html(validRules['permaLink']).attr('remote-validated' , true);
						        			
						        			$('#pagepermalink').val(res.url);
						        			
						        			$("input[name=pagepermalink]").parent('div').prev("div").removeClass('focus')
						        		.removeClass('error').addClass('success');
						        			
						        		} 
						        		else
						        		{
						        			$("input[name=pagepermalink]").parent('div').next("div").removeClass('focus')
											.addClass('error').removeClass('success');
											$("input[name=pagepermalink]").parent('div').removeClass('focus').removeClass('success').addClass('error');
											$('span[for=pagepermalink]').html(__('Permalink already exists')).removeClass('valid');
						        		}
						        	}
	                        	});
                        	}
                        }
					 }
				},
				// error messages
				messages : {
					pageTitle : {
						required : __("Please enter page title"),
						minlength : __("Please enter minimum 2 characters")
					},	
					
					pagepermalink : {
						required : __("Please enter permalink"),
						remote : __("Permalink already exists")
					},	
									
				},

                onfocusin : function(element) {
                    if(element.type.toLowerCase()!='file') {
                    var flag = 1; 
                        if(element.name=='pageTemplate' 
                           || element.name=='pageAuthor' 
                           || element.name=='pagemetaTitle' 
                           || element.name=='postwithin') {
                               flag = 0;
                        }
                        if(element.name == 'pagemetaDesc') {
                            $(element).parent('div')
                            .removeClass('success');
                            return true;
                        }
                        if(element.name == 'pageCustomHeader') {
                            $(element).parent('div')
                            .removeClass('success');
                            return true;
                        }
                        if (!$(element).parent('div').next("div")
                            .hasClass('success') && flag) {
                            this.showLabel(element, focusRules[element.name]);
                            $(element).parent('div').next('div').children('span.help-inline')
                            .removeClass('error').removeClass('success').removeClass('valid');
                        }
                    }
                },

				highlight : function(element,
						errorClass, validClass) {

					var flag = 1; 
					if(element.name=='pageTemplate'|| element.name=='pageAuthor' || element.name=='pagemetaTitle' || element.name=='postwithin'){
						flag = 0;
					}
					
					if(flag){
					$(element).parent('div')
							.removeClass(validClass)
							.addClass(errorClass).next(
									"div").removeClass(
									validClass)
							.addClass(errorClass);
					}

				},
				unhighlight : function(element,errorClass, validClass) {
					var flag = 1; 
					if(element.name=='pageTemplate'|| element.name=='pageAuthor' || element.name=='pagemetaTitle' || element.name=='postwithin'){
						flag = 0;
					}
					if(element.value == ''){
						$(element).parent('div')
							.removeClass('success');
							return true;
					}
					if(flag){
					$(element).parent('div')
							.removeClass(errorClass)
							.addClass(validClass).prev(
									"div").addClass(
									validClass)
							.removeClass(errorClass);
					$('span.help-inline',$(element).parent('div')
									.next('div')).text(
							validRules[element.name]);
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

$.validator.setDefaults({
	onkeyup : false,
	onfocusout : function(element) {
		$(element).valid();
	}
  });

function showPublishDate(){
	if($('#publishlater').hasClass('display-none')){
		$('#publishlater').removeClass('display-none');
	}else{
		$('#publishlater').addClass('display-none');
	}
}

function deleteImage(imgId,pageId){
	$.ajax({
		url : HOST_PATH + "admin/page/deleteimage/pageId/"+pageId+"/imgId/"+imgId,
			method : "post",
			type : "post",
			success : function(data) {
				window.location.href = HOST_PATH+"admin/page/editpage/id/" + pageId;	
			}
	 });

}



function moveToTrash(id){
	bootbox.confirm(__("Are you sure you want to move this page to trash?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		}
		else{
			deleteRecord(id);
		}
		
	});
 }
/**
 * function use for deleted page from editpage
 * and from database
 * @param id
 * @author jsingh5
 */
function deleteRecord(id) {
	
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/page/movetotrash",
		method : "post",
		data : {
			'id' : id
		},
		dataType : "json",
		type : "post",
		success : function(data) {
			
			if (data != null) {
				
				window.location.href = "/admin/page/";
				
			} else {
				
				window.location.href = "page";
			}
		}
	});	
}

function checkFileType(e)
{
	 var el = e.target  ? e.target :  e.srcElement ;
	 $('#imagerrorDiv').show(); 
	 
	 
	 var regex = /png|jpg|jpeg|PNG|JPG|JPEG/ ;
	
	 
	 
	 if( regex.test(el.value) )
	 {
		// invalidForm[el.name] = false ;
		
		 $(el).parent("div").removeClass("error").addClass("success").next("div")
			.html(__("<span class='success help-inline'>Valid file</span>"));
		 /*$(el).parents("div.mainpage-content-right")
		 .children("div.mainpage-content-right-inner-right-other").removeClass("focus")
		 .html("<span class='success help-inline'>Valid file</span>"); */
		 
	 }else{
		 
		 $(el).parents("div").addClass('error').removeClass('success');	 

		 $(el).parent("div").removeClass("success").addClass("error").next("div")
			.html(__("<span class='error help-inline'>Please upload only jpg or png image</span>"));
		
	 }
	 
 }



function validatePublishdate()
{
	var sDate = Date.parseExact( $("input#publishDate").val() , "dd-MM-yyyy") ;
 
	
	var now = new Date() ;
	var currentDate = now.toString('d-M-yyyy');
	
	//now.getDate() + "-" + ( now.getMonth() + 1 ) + "-" + now.getFullYear() +" "+  ;
	
	currentDate = Date.parseExact( currentDate , "d-M-yyyy"); 
	
	// check start date should be greater than or equal to current date 
	if( sDate .compareTo (currentDate )  < 0 )
	{
		
		// Change msg by Er.kundal
		$("div#publisherror").removeClass("success").addClass("error").html(__("<span class='error help-inline'>Start date must be equal to or greater than current date</span>"))
		.next("div").addClass("error").removeClass("success");
	    errorExists['publishDate'] = false ;
		return false ;
		
	} else {
		
		
		/*if( sDate.compareTo (currentDate )  == 0 )
		{
			var currentTime = now.toString('HH:mm:00');
		    selectedTime = $('#publishTimehh').val()+':00';
		    if(selectedTime<currentTime){
		    	$("div#publisherror").removeClass("success").addClass("error").html("<span class='error help-inline'>Start date must be equal to or greater than current date</span>")
				.next("div").addClass("error").removeClass("success");
			    errorExists['publishDate'] = false ;
				return false ;	
		    }
		} */
		
		
		$("div#publisherror").removeClass("error").addClass("success").html(__("<span class='success help-inline'>Valid date</span>"))
		.next("div").addClass("success").removeClass("error");
		errorExists['publishDate'] = true ; 
		return true;
	}
	
	
	
	
	/*
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
		// Change msg by Er.kundal
		$("div.dateValidationMessage1").removeClass("success").addClass("error").html("<span class='error help-inline'>End date should be greater than start date</span>")
		.next("div").addClass("error").removeClass("success");
		$("div.dateValidationMessage2").removeClass("success").addClass("error").html("<span class='error help-inline'></span>")
		.next("div").addClass("error").removeClass("success");
		
		
		errorExists['compareDate'] = false ;
	} else 	{
		$("div.dateValidationMessage1").removeClass("error").addClass("success")
			.html("<span class='success help-inline'>Valid</span>")
				.next("div").removeClass("error").addClass("success");
		$("div.dateValidationMessage2").removeClass("error").addClass("success")
			.html("<span class='success help-inline'>Valid</span>")
				.next("div").removeClass("error").addClass("success");
		errorExists['compareDate'] = true ;
	} */
}