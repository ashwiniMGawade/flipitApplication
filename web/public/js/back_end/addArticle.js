
var validRules = {

		articleTitle : __("Article title looks great"),
		articlepermalink : __("Permalink looks great"),
	};
var searchedValue  = "";

	/**
	 * focusRules oject contain all the messages that are visible on focus of an
	 * elelement
	 * structure to define a message for element key is to be element name Value is
	 * message
	 */
	var focusRules = {

			articleTitle : __("Enter Article title"),
			articlepermalink : __("Enter permalink"),

	};


	$(document).ready(init);
	flag = false;
	function init() {
		
		var options = {
				'maxCharacterSize': '' ,
				'displayFormat' : ''
		};
      	jQuery('#articlemetaTitle').textareaCount(options, function(data){
			jQuery('#metaTextLeft').val(__("Artical meta title length ") + (data.input) + __(" characters"));

		});
      	jQuery('#articlemetaDesc').textareaCount(options, function(data){
			jQuery('#metaDescriptionleft').val(__("Artical meta description length ") + (data.input) + __(" characters"));
			
		});
		
		jQuery('#articlemetaTitle').NobleCount('#metaTextLeft',{
			max_chars: 68,
			prefixString : __("Artical meta title length ")
		});
		
		jQuery('#articlemetaDesc').NobleCount('#metaDescriptionleft',{
			max_chars: 150,
			prefixString : __("Artical meta description length ")
		});
	      	
		manageWidgets();
		$('#publishDate').datepicker({'autoclose':true,'format': 'dd-mm-yyyy'});
		$('#publishTimehh').timepicker({
		 	minuteStep: 5,
	        template: 'modal',
	        showSeconds: false,
	        showMeridian: false
	    });
	   
		validateFormAddNewPage();
		
		$('form#addArticleform').submit(function(){
			//validateAuthor();
			flag = true;
			if($(this).valid() ){
				$("#publishBtn").attr('disabled','disabled');
				$("#saveDraftbtn").attr('disabled','disabled');
				return true;
				
			}else{
				return false;
			}
			
		});	
		$("input#selectStoreForArticle").autocomplete({
	        minLength: 1,
	        source: function( request, response)
	        {
	        	var selectedStoreForArticles = $('#selectStoreForArticle').val() =='' ? undefined : $('#selectStoreForArticle').val() ;
	        	var selectedRelatedStore = $('#selectedRelatedStores').val() =='' ? undefined : $('#selectedRelatedStores').val() ;
	        	$.ajax({
	        		url : HOST_PATH + "admin/article/searchkey/keyword/" + selectedStoreForArticles + '/selectedshops/'+  selectedRelatedStore +'/flag/0',
	     			method : "post",
	     			dataType : "json",
	     			type : "post",
	     			success : function(data) {
	     				if (data != null) {
	     					searchedValue = request.term;
	     					//pass arrau of the respone in respone onject of the autocomplete
	     					response(data);
	     				} 
	     			},
	     			error: function(message) {
	     	            // pass an empty array to close the menu if it was initially opened
	     	            response([]);
	     	        }

	     		 });
	        },
	        select: function( event, ui ) {
	        	
	        	$("#selectStoreForArticle").val(ui.item.label).attr('rel',ui.item.id);
	        }
	    });
		
		$(".sidebar-content-box-left").find('ul#RelatedCategoryListul-li').find('li').click(function() {
			
			
			if($(this).hasClass('selected')){
				$(this).removeClass('selected');
				var newValues = [];
				var values = $('#selectedRelatedCategory').val();
				    values =  values.split(',');
				    
				    	for(d in values){
				    		if($(this).attr('value') != values[d]){
				    			newValues.push(parseInt(values[d]));
				    		}
				    	}
						
							console.log(newValues);
							$('#selectedRelatedCategory').val(newValues);
					
					
			}else{
				$(this).addClass('selected');
				var selectedRelated = new Array();
				$('.sidebar-content-box-left').find('ul#RelatedCategoryListul-li').find('li.selected').each(function(index) {
					selectedRelated[index] = $(this).attr('value');
				});
				
				$('#selectedRelatedCategory').val(selectedRelated);
			}
			
		

		});
		
		$("select#authorList").change(function(){
			$("input#authorNameHidden").val($(this).children('option:selected').text());
		});
	}

	/*validate author field*/
	
//	function validateAuthor()
//	{
//		if($("select[name=authorList]").val() != ""){
//			
//			// invalidForm[el.name] = false ;
//			
//			 $("select[name=authorList]").parent("div").removeClass("error").addClass("success").next("div")
//				.html("<span class='success help-inline'>Author looks great!</span>");
//			 /*$(el).parents("div.mainpage-content-right")
//			 .children("div.mainpage-content-right-inner-right-other").removeClass("focus")
//			 .html("<span class='success help-inline'>Valid file</span>"); */
//			 return true;
//		
//		} else{
//			 
//			 $("select[name=authorList]").parents("div.sidebar-content-box").addClass('error').removeClass('success');	 
//	
//			 $("select[name=authorList]").parent("div").removeClass("success").addClass("error").next("div")
//				.html("<span class='error help-inline'>Please select an author</span>");
//			 return false;
//		 }
//	}
	/* Widget section start */
	function manageWidgets() {
		reoderElements();
		$(".sidebar-content-box-left").find('ul#storeListul-li').find('li').click(function() {
			// alert($(this).attr('value'));
			var size = $(".sidebar-content-box-left ul li").size();

			var flag = 1;
			if ($(this).hasClass('selected')) {
				flag = 0;
			}
			$('.sidebar-content-box-left').find('ul#storeListul-li').find('li').each(function(index) {
				$(this).removeClass('selected');

			});

			if (flag) {
				$(this).addClass('selected');

			}

		});

		$('.up').click(
				function() {

					$('.sidebar-content-box-left').find('ul#storeListul-li').find('li').each(
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
					var size = $(".sidebar-content-box-left").find('ul#storeListul-li').find('li').size();
					$('.sidebar-content-box-left').find('ul#storeListul-li').find('li').each(
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
							$('.sidebar-content-box-left').find('ul#storeListul-li').find('li')
									.each(
											function(index) {
												if ($(this).hasClass('selected')) {
													// if($(this).attr('type')>4){
													
													// }
													$(this).remove();
													reoderElements();
													// slectEvent();
												}
											});

							$('.sidebar-content-box-left').find('ul#storeListul-li').find('li').each(
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

			//addWidgetPopup();
			if($("input#selectStoreForArticle").val() == "" || $("input#selectStoreForArticle").val() == "No Record Found" || searchedValue == $("input#selectStoreForArticle").val()){
				return false;
			}
			var li = $('<li\>',{'value' : $("input#selectStoreForArticle").attr('rel')}).html($("input#selectStoreForArticle").val());
			$('.sidebar-content-box-left').find('ul#storeListul-li').append(li);
			$(li).click(function() {
				
				
					if($(this).hasClass('selected')){
						$(this).removeClass('selected');
						$(this).siblings().removeClass('selected');
					}else{
						$(this).addClass('selected');
						$(this).siblings().removeClass('selected');
					}
				
				

			});
			
			$('.sidebar-content-box-left').find('ul#storeListul-li').find('li').each(
					function(index) {
						var className = 'grid-line2';
						if (index % 2 == 0) {
							className = 'grid-line1';
						}
						$(this)
								.removeClass(
										'grid-line1 grid-line2')
								.addClass(className).attr('type',index);
						// alert(index);
					});
			
			reoderElements();
			$("#selectStoreForArticle").val('').removeAttr('rel');
		});

	}

	function addStores() {
		
		$('div#overlay div.fancybox-skin span#selectWidgetError.error').hide();
		if($('#overlay select#widgetListUserdefinedSelect option:selected').val()==''){
		   $('div#overlay div.fancybox-skin span#selectWidgetError.error').show();
		  return false;
		}
		var size = $(".sidebar-content-box-left ul li").size();
		className = 'grid-line2';
		if (size % 2 == 0) {
			className = 'grid-line1';
		}
		liCount = parseInt(size) + 5;
		var index = $('#overlay select#widgetListUserdefinedSelect').get(0).selectedIndex;
		$(
				'div#widgetListUserdefined select#widgetListUserdefinedSelect option:eq('
						+ index + ')').remove();
		$('#storeListul-li')
				.append(
						'<li class="'
								+ className
								+ '" value='
								+ $(
										'#overlay select#widgetListUserdefinedSelect option:selected')
										.val()
								+ ' type='
								+ liCount
								+ '>'
								+ $(
										'#overlay select#widgetListUserdefinedSelect option:selected')
										.text() + '</li>');
		removeOverLay();
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
		var selectedRelated = new Array();
		$('.sidebar-content-box-left').find('ul#storeListul-li').find('li').each(function(index) {
			selectedRelated[index] = $(this).attr('value');
		});
		$('#selectedRelatedStores').val(selectedRelated);
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


	var request = true;
	function validateFormAddNewPage(){
		
		$("form#addArticleform")
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
						
						
						articleTitle : {
							required : true,
							minlength : 2
						},
						
						articlepermalink : {
							required : true,
							minlength : 1,
	                        remote : {
	                        	
	                        	
	                        		url: HOST_PATH + "admin/article/validatepermalink",
						        	type: "post" ,
						        	data  : { 'isEdit' : $('#isEdit').val() , 'id' : $("#pageId").val() },
						        	complete : function(data) {
						        		
						        		
						        		res = $.parseJSON(data.responseText);
						        		
						        		console.log(data);
						        		if(res.status == "200"){	
						        			
						        			$('span[for=articlepermalink]' , $("[name=articlepermalink]").parents('div.mainpage-content-right') )
						        			.attr('remote-validated' , true);
						        			
						        			$('#articlepermalink').val(res.url);
						        			
						        			$("input[name=articlepermalink]").parent('div').prev("div").removeClass('focus')
						        			.removeClass('error').addClass('success');
						        			if(flag == true){
						        				$('form#addArticleform').submit();
						        			}
						        			
						        		} else {
						        			
						        			$("input[name=articlepermalink]").parent('div').next("div").removeClass('focus')
											.addClass('error').removeClass('success');
											$("input[name=articlepermalink]").parent('div').removeClass('focus').removeClass('success').addClass('error');
											$('span[for=articlepermalink]').html(__('Permalink already exists'))
											.attr('remote-validated' , false);
											return false;
						        		}
						        	}
		                        	
	                        	
	                        } }
						 
					},
					// error messages
					messages : {
						articleTitle : {
							minlength : __("Please enter minimum 2 characters"),
							required :  __("Please enter article title")
						},	
						articlepermalink : {
							required : __("Please enter Permalink"),
							remote : __("Invalid Url")
						},	
						articleImage : {
							 regex : __("Please upload only jpg or png image")
						}
										
					},

					onfocusin : function(element) {
                        if(element.type.toLowerCase()!='file') {
                            if (!$(element).parent('div').prev("div")
                               .hasClass('success')) {
                                var label = this.errorsFor(element);
                                if( $( label ).attr('hasError')  ) {
                                    if($( label ).attr('remote-validated') != "true") {
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
                                }
                            },

					highlight : function(element,
							errorClass, validClass) {

						$(element).parent('div')
								.removeClass(validClass)
								.addClass(errorClass).next(
										"div").removeClass(
										validClass)
								.addClass(errorClass);

					},
					unhighlight : function(element,
							errorClass, validClass) {
						if($(element).val() != ""){
						$(element).parent('div')
								.removeClass(errorClass)
								.addClass(validClass).prev(
										"div").addClass(
										validClass)
								.removeClass(errorClass);
						}
						/*$(
								'span.help-inline',
								$(element).parent('div')
										.next('div')).text(
								validRules[element.name]);*/
				
					},
					success: function(label , element) {
					
						if($(element).val() != ""){
							$(element).parent('div')
							.removeClass(this.errorClass)
							.addClass(this.validClass).prev(
									"div").addClass(
											this.validClass)
							.removeClass(this.errorClass);
							$(label).remove();
						  /*  $(label).append( validRules[element.name] ) ; */
						    label.addClass('valid') ;
						}
					}

				});

	 
	}

	$.validator.setDefaults({
		onkeyup : false,
		onfocusout : function(element) {
			if($(element).attr('type') != 'file' && $(element).attr('name') != 'authorList'){
				$(element).valid();
			}
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


	function moveToTrash(id){
		bootbox.confirm(__("Are you sure you want to move this article to trash?"),__('No'),__('Yes'),function(r){
			if(!r){
				return false;
			}
			else{
				deleteRecord(id);
			}
			
		});
	 }

	
	
	/**
	 * function use for deleted Article from list
	 * and from database
	 * @param id
	 * @author jsingh5
	 */
	function deleteRecord(id) {
		
		
		addOverLay();
		$.ajax({
			url : HOST_PATH + "admin/article/movetotrash",
			method : "post",
			data : {
				'id' : id
			},
			dataType : "json",
			type : "post",
			success : function(data) {
				
				if (data != null) {
					
					window.location.href = HOST_PATH + "admin/article";
					
				} else {
					
					window.location.href = HOST_PATH + "admin/article";
				}
			}
		});	
	}
	
	/**
	 * function used to render html for multiple chapters in articles
	 * @author cbhopal
	 */
	function getchapterhtml(el){
		editCount = parseInt($(el).attr('rel'));
		count = editCount != undefined ? editCount + 1 : count;
		$.ajax({
			url : HOST_PATH + "admin/article/chapters",
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
					url : HOST_PATH + "admin/article/deletechapters",
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

function featuredImageToggle(e){
    var btn = e.target  ? e.target :  e.srcElement ;
    jQuery(btn).addClass("btn-primary").siblings().removeClass("btn-primary");

    if(btn.value=='yes'){
        jQuery("input#featuredimagecheckbox").val(1);
    }else{
        jQuery("input#featuredimagecheckbox").val(0); 
    }
}
