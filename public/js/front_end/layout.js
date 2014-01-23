/**
 * footer news letter
 * @author Er.kundal
 */

/**
 * validRules oject contain all the messages that are visible when an elment
 * val;ue is valid
 *
 * structure to define a message for element key is to be element name Value is
 * message
 */
var validRules = {
	homeemail : ""
};

/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement structure to define a message for element key is to be element name
 * Value is message
 */

var focusRules = {
	homeemail : ""
};

$(document).ready(init);

/**
 * initialize all the settings after document is ready
 *
 * @author Er.kundal
 */
function init() {
	//console.log(__('freesignup'));
	$('div.nav-container').hide();
	$("a#stepOneFormSubmit").click(function(){
		signUpFromHandler();
	});

	$("#passwordHomepage,#confirmPasswordHomepage").keypress(function(event) {

	    if (event.which == 13) {

	    	if($("form#formOneHomePage").valid()){

	    		$("form#formOneHomePage").submit();
	    	}

	    }
	});
}

var hide = false;
$(document)
		.ready(
				function() {

					$('div.nav-container').hide();
					$('a.show_hide1').click(function() {

						$('div.nav-container').slideToggle();

					});

					$("div.dropdown").click(function() {

						$('div.submenu').slideToggle();

					});


					// apply selected class on main manu if data in cache
					$('div.navigation ul li a').removeClass('selected');
					var a = $('input#cName').val();
					if (a != "") {
						$('div.navigation ul li a#' + a).addClass('selected');
					}

					// end selected code for mainmenu
					$('li a.show_hide').hover(hoverInSide, hoverOutSide);
					autocomplete();

					$("button#searchbuttonHP")
							.click(
									function() {

										var value = $(
										"input#searchField")
										.val();


										var valToCheck = value.trim().toLowerCase();


										// flipt header and prevent from search and redirect
										if(valToCheck == "flipit" || valToCheck == "flip it" || valToCheck == "flipit.com")
										{
											$("div.search-outer-background").toggleClass('flipped'); //.addClass("flipped")
											return;
										}



										if ($("input#searchHidden")
														.val() == $(
														"input#searchField")
														.val()) {
												var autocomplete = $(this).parent('a').siblings('div').children('input[type=text]').data(
														"autocomplete");
												var matcher = new RegExp(
														"("
																+ $.ui.autocomplete
																		.escapeRegex($(
																				this).parent('a').siblings('div').children('input[type=text]')
																				.val())
																+ ")", "ig");

												autocomplete
														.widget()
														.children(".ui-menu-item")
														.each(
																function() {

																	var item = $(
																			this)
																			.data(
																					"item.autocomplete");
																	if (matcher
																			.test(item.label
																					|| item.value
																					|| item)) {
																		autocomplete.selectedItem = item;
																	}

																});

												if (autocomplete.selectedItem
														&& $(this).parent('a').siblings('div').children('input[type=text]').val()
																.toLowerCase() == autocomplete.selectedItem.value
																.toLowerCase()) {
													item = {};
													item['permalink'] = autocomplete.selectedItem.permalink;
													autocomplete._trigger("select",
															'', {
																'item' : item
															});
													} else {
													var value = $(
															"input#searchField")
															.val();
													if (value == 'Vind kortingscodes voor jouw favoriete winkels..') {
														return false;

													}
													window.location.href = HOST_PATH_LOCALE
															+ __("zoeken") + '/' + value;
												}
											}

									});

					$("input#searchField").keyup(
							function(e) {
								if (e.which != 37 && e.which != 38
										&& e.which != 39 && e.which != 40) {
									$("input#searchHidden").val($(this).val());
								}
							});

					$("input#searchField")
							.keypress(
									function(e) {
										if (e.which == 13
												&& $("input#searchHidden")
														.val() == $(
														"input#searchField")
														.val()) {


											var value = $("input#searchField").val();

											var valToCheck = value.trim().toLowerCase();


											// flipt header and prevent from search and redirect
											if(valToCheck == "flipit" || valToCheck == "flip it" || valToCheck == "flipit.com")
											{
												$("div.search-outer-background").toggleClass('flipped'); //.addClass("flipped")
												return;
											}



											var autocomplete = $(this).data(
													"autocomplete");
											var matcher = new RegExp(
													"("
															+ $.ui.autocomplete
																	.escapeRegex($(
																			this)
																			.val())
															+ ")", "ig");

											autocomplete
													.widget()
													.children(".ui-menu-item")
													.each(
															function() {

																var item = $(
																		this)
																		.data(
																				"item.autocomplete");
																if (matcher
																		.test(item.label
																				|| item.value
																				|| item)) {
																	autocomplete.selectedItem = item;
																}

															});

											if (autocomplete.selectedItem
													&& $(this).val()
															.toLowerCase() == autocomplete.selectedItem.value
															.toLowerCase()) {
												item = {};
												item['permalink'] = autocomplete.selectedItem.permalink;
												autocomplete._trigger("select",
														'', {
															'item' : item
														});

											} else {

												var value = $(
														"input#searchField")
														.val();
												if (value == 'Vind kortingscodes voor jouw favoriete winkels..') {
													return false;

												}
												window.location.href = HOST_PATH_LOCALE
														+ __link("zoeken")+ '/' + value;

											}
										}
									});

				});

/**
 * autocomplete function
 * @author cbhopal
 */
function __highlight(s, t) {
	var matcher = new RegExp("(" + $.ui.autocomplete.escapeRegex(t) + ")", "ig");
	return s.replace(matcher, "<strong style='color:#0B7DC1'>$1</strong>");
}
var cache = {};
function autocomplete() {

	$.ui.autocomplete.prototype._renderMenu = function( ul, items ) {
		   var self = this;
		   $.each( items, function( index, item ) {
		      if (index < 8) // here we define how many results to show
		         {self._renderItem( ul, item );}
		      });
		}


	var w = $("input#searchField").width();
	if ($("input#searchField").length > 0) {
		$("input#searchField").autocomplete({
					delay: 0,
					minLength : 1,
					source : function( request, response ) {

						var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
						response( $.grep( shopsJSON, function( item ){
						return matcher.test( item.label );
						}) );
					},
					select : function(event, ui) {
						window.location.href = HOST_PATH_LOCALE + ui.item.permalink;
					},
					focus: function( event, ui ) {
							$('li.wli').removeClass('select');
							$('a#ui-active-menuitem').parents('li').addClass('select');
					},
				}).data("autocomplete")._renderItem = function(ul, item, url) {
					url = HOST_PATH_LOCALE + item.permalink;
					// only change here was to replace .text() with .html()
					return $("<li class='wli'></li>").data("item.autocomplete", item).append(
							$("<a href=" + url + "></a>").html('<div>' + (__highlight(item.label,$("input#searchField").val())) + "</div>"))
					.appendTo(ul);
		};
		$('ul.ui-autocomplete').addClass('wd');

	} else if ($("input#offer_name").length > 0) {

		$("input#offer_name")
				.autocomplete(
						{
							minLength : 1,
							source : function(request, response) {
								if (cache.term == request.term && cache.content) {
							          response(cache.content);
							          return;
							      }
							      if (new RegExp(cache.term).test(request.term) && cache.content && cache.content.length < 13) {
							          response($.ui.autocomplete.filter(cache.content, request.term));
							          return;
							      }

								$.ajax({
											url : HOST_PATH_LOCALE
													+ "store/searchtoptenshopforusergenerated/keyword/"
													+ $('input#offer_name')
															.val(),
											method : "post",
											dataType : "json",
											type : "post",
											success : function(data) {
													if (data != null) {

														cache.term = request.term;
														cache.content = data;
														response(data);

													// pass array of the respone in respone
													// object of the autocomplete
													response($.map(data, function(item) {
														if (parseInt(item.id) != 0) {
															return {
																label : item.label,
																value : item.label,
																id : item.id,
																permalink : item.permalink
															};
														} else {

															response([]);
														}

													}));
												}
											},
											error : function(message) {
												// pass an empty array to close
												// the menu if it was initially
												// opened
												response([]);
											}

										});
							},
							select : function(event, ui) {
								$('input#shopId').val(ui.item.id);
							},
							focus: function( event, ui ) {
								$('li.wLi3').removeClass('select');
								$('a#ui-active-menuitem').parents('li').addClass('select');
							},

						}).data( "autocomplete" )._renderItem = function( ul, item, url ) {
	        url = HOST_PATH_LOCALE + item.permalink;
	        // only change here was to replace .text() with .html()
			return $("<li class='wLi3'></li>").data("item.autocomplete", item).append(
				$("<a href='" + url + "'></a>").html('<div>' + (__highlight(item.label,$("input#offer_name").val())) + "</div>"))
			.appendTo(ul);
	     };
	     $('ul.ui-autocomplete').addClass('wd2');
	}
}

/**
 * submit search form
 *
 * @returns {Boolean}
 */
function topSearchFormSubmitTrue() {
	var value = $("input#searchField").val();
	if (value == '') {
		return false;
	}
	window.location.href = HOST_PATH_LOCALE + __link("zoeken") + '/' + value;

}
/**
 * HIDE SHOW DIV ON HOVER IN OF THE SECOND LEVEL MENU
 */
function hoverInSide() {
	$(this).addClass("active");
}
/**
 * HIDE DIV ON HOVEE OUT OF THE DIV
 */
function hoverOutSide() {
	// Fades out the DIV and removes the 'active' class from the main nav menu
	// item
	$(this).removeClass("active");
}
/**
 * HIDE SHOW DIV ON HOVE OF THE DIV
 */
function hoverInSideDiv() {
	if (hide)
		clearTimeout(hide);
	$(this).prev('li').children('a.show_hide').addClass("active");
}
/**
 * HIDE HIDE DIV ON HOVE OF THE SECOND LEVEL MENU
 */
function hoverOutSideDiv() {
	// If your mouse moves out of the displayed hidden DIV, the DIv fades out
	// and removes the 'active' class
	hide = setTimeout(function() {
		$(this).fadeOut("fast");
	});
	$(this).stop().fadeIn();
	$(this).prev('li').children('a.show_hide').removeClass("active");
	$(this).fadeOut("fast");
}
function showHideDiv() {
	$(this).parent('li').next("div").slideToggle();
}
function checkTextBoxValue() {
	if ($("#searchField").val() == 'No Record Found') {
		alert(__("Store does not exist"));
		return false;

	} else {

		return true;
	}
}
/**
 * sendiscountCoupon
 *
 * Add userGenerate offer
 *
 * @author kraj
 * @version 1.0
 */
var validateFormAddNewOffer1 = null;
function sendiscountCoupon() {
	validatediscountwidget();

		if ($("form#discount_code").valid()) {
			if($('#shopId').val()!=undefined && $('#shopId').val()!=null && $('#shopId').val()!=''){
			$.ajax({
				url : HOST_PATH_LOCALE + "offer/sendiscountcoupon/shopId/"
						+ $('#shopId').val(),
				method : "post",
				dataType : "json",
				type : "post",
				data : $('form#discount_code').serialize(),
				success : function(data) {
					$('#offer_name,#offer_code,#offer_desc').val('');
					bootbox.alert(__("Your offer has been added successfully !"));
				}
			});
			}else{

				bootbox.alert(__("Please select atleast one shop !"));
			}
		} else {

		}

}
/**
 * validatediscountwidget
 *
 * chaeck validation of user generate offer
 *
 * @author kraj
 * @version 1.0
 */
function validatediscountwidget() {

	validateFormAddNewOffer1 = $("form#discount_code").validate({
		rules : {
			offer_name : {
				required : true
			},
			offer_code : {
				required : true
			},
			offer_desc : {
				required : true
			}
		},
		// error messages
		messages : {
		 offer_name : {
				required : __("Please add a shop")
		  },
		  offer_code :{
			  required : __("Please add discount code")
	  		},
	  	  offer_desc :{
			 required : __("Please add the discription of the code")
		  }
		},
		onfocusin : function(element) {
			$(element).removeClass('error').removeClass('success');
		},

		highlight : function(element, errorClass, validClass) {
			$(element).removeClass('success').addClass('error');
		},
		success : function(label, element) {

			$(element).removeClass('error').addClass('success');
		}
	});
}

function showtermsandConditions() {

	if ($('#terms_ul_li').hasClass('display-none')) {
		$('#terms_ul_li').removeClass('display-none');
		$('#terms_ul_li').addClass('display-block');
	} else {
		$('#terms_ul_li').removeClass('display-block');
		$('#terms_ul_li').addClass('display-none');
	}
}

/**
 * @author kkumar disable offer popup js start
 */

function showText(offerId) {
	$('.shoptext' + offerId).show();
	$('.shopimage' + offerId).hide();
}

function showImage(offerId) {
	$('.shoptext' + offerId).hide();
	$('.shopimage' + offerId).show();
}

function showCode1(event) {

	var offerId = $(event).attr('id');
	var vote = $(event).attr('vote');

	$('#element_to_pop_up').html('');
	$('div#blackImage' + offerId).siblings().hide();


	if(! ( /(iPod|iPhone|iPad)/i.test(navigator.userAgent) )) {

	custompopup('element_to_pop_up');
	$
			.ajax({
				url : HOST_PATH_LOCALE + "offer/offerdetail",
				method : "post",
				data : {
					'id' : offerId,
					'vote' : vote
				},
				type : "post",
				success : function(data) {
					$('#element_to_pop_up').html(data);

					$("#blackImage" + offerId +" .coupon-code").html(($("div.coupon-code" ,data).html()).trim());
					$("div.coupon-code").bigText({'maximumFontSize': 20});
					$("div.coupon-code.runtime-code","#element_to_pop_up").bigText();			
					if (vote != '0') {

						$('div.profile-bar div#vote' + offerId + ' div#pro')
								.css({
									'margin' : '0px',
									'width' : '23%',
									'float' : 'left'
						});
						$('.bar').progressbar();
						$('div.fl div.bar div.ui-progressbar-value').show();
						$('div.fl div.bar div.ui-progressbar-value').width(
								'' + $('#votepercentage').val() + '%');
						$('div.fl div.bar div.ui-progressbar-value').html(
								'<span style="color:#000;width:100%;left:240px;position:relative;top:3px;">'
										+ $('#votepercentage').val()
										+ '%</span>');
						$('div.fl div.bar div.ui-progressbar-value').css({
							'border-bottom-right-radius' : '4px;',
							'border-top-right-radius' : '4px;'
						});
						if (vote == '1') {
							$('div.fl div.bar div.ui-progressbar-value')
									.css(
											{
												'background-color' : '#86C536',
												'background-image' : '-moz-linear-gradient(center top , #86C536, #86C536)'
											});
						} else {
							$('div.fl div.bar div.ui-progressbar-value')
									.css(
											{
												'background-color' : '#FF0000',
												'background-image' : '-moz-linear-gradient(center top , #FF0000, #FF0000)'
											});
						}
						perce(offerId);
					}

					$("#savemoneybox2,#savemoneybox1").keydown(
							function(event) {
								if (event.shiftKey) {
									event.preventDefault();
								}

								if (event.keyCode == 46 || event.keyCode == 8
										|| event.keyCode == 9) {
								} else {
									if (event.keyCode < 95) {
										if (event.keyCode < 48
												|| event.keyCode > 57) {
											event.preventDefault();
										}
									} else {
										if (event.keyCode < 96
												|| event.keyCode > 105) {
											event.preventDefault();
										}
									}
								}
							});
					FB.XFBML.parse();
					twttr.widgets.load();

				}
			});
	}
}
// new function for details code add by raman


function showCode(event) {

	var offerId = $(event).attr('id');
	var vote = $(event).attr('vote');
	$('#element_to_pop_up').html('');
	$(event).hide();

	if(! ( /(iPod|iPhone|iPad)/i.test(navigator.userAgent) )) {

	custompopup('element_to_pop_up');
	$
			.ajax({
				url : HOST_PATH_LOCALE + "offer/offerdetail",
				method : "post",
				data : {
					'id' : offerId,
					'vote' : vote
				},
				type : "post",
				success : function(data) {

					$('#element_to_pop_up').html(data);
					$("#blackImage" + offerId +" .coupon-code").html(($("div.coupon-code" ,data).html()).trim());
					$("div.coupon-code").bigText({'maximumFontSize': 20});
					$("div.coupon-code.runtime-code","#element_to_pop_up").bigText();


					if (vote != '0') {

						$('div.profile-bar div#vote' + offerId + ' div#pro')
								.css({
									'margin' : '0px',
									'width' : '23%',
									'float' : 'left'
								});
						$('.bar').progressbar();
						$('div.fl div.bar div.ui-progressbar-value').show();
						$('div.fl div.bar div.ui-progressbar-value').width(
								'' + $('#votepercentage').val() + '%');
						$('div.fl div.bar div.ui-progressbar-value').html(
								'<span style="color:#000;width:100%;left:240px;position:relative;top:3px;">'
										+ $('#votepercentage').val()
										+ '%</span>');
						$('div.fl div.bar div.ui-progressbar-value').css({
							'border-bottom-right-radius' : '4px;',
							'border-top-right-radius' : '4px;'
						});
						if (vote == '1') {
							$('div.fl div.bar div.ui-progressbar-value')
									.css(
											{
												'background-color' : '#86C536',
												'background-image' : '-moz-linear-gradient(center top , #86C536, #86C536)'
											});
						} else {
							$('div.fl div.bar div.ui-progressbar-value')
									.css(
											{
												'background-color' : '#FF0000',
												'background-image' : '-moz-linear-gradient(center top , #FF0000, #FF0000)'
											});
						}
						perce(offerId);
					}

					$("#savemoneybox2,#savemoneybox1").keydown(
							function(event) {
								if (event.shiftKey) {
									event.preventDefault();
								}

								if (event.keyCode == 46 || event.keyCode == 8
										|| event.keyCode == 9) {
								} else {
									if (event.keyCode < 95) {
										if (event.keyCode < 48
												|| event.keyCode > 57) {
											event.preventDefault();
										}
									} else {
										if (event.keyCode < 96
												|| event.keyCode > 105) {
											event.preventDefault();
										}
									}
								}
							});
					FB.XFBML.parse();
					twttr.widgets.load();

				}
			});
	}
}
// new function for details code add by raman

function showCodeDetail(offerId, vote) {
	$('#element_to_pop_up').html('');
	$('#' + offerId).hide();
	custompopup('element_to_pop_up');
	$
			.ajax({
				url : HOST_PATH_LOCALE + "offer/offerdetail",
				method : "post",
				data : {
					'id' : offerId,
					'vote' : vote
				},
				type : "post",
				success : function(data) {
					$('#element_to_pop_up').html(data);
					if (vote != '0') {

						$('div.profile-bar div#vote' + offerId + ' div#pro')
								.css({
									'margin' : '0px',
									'width' : '23%',
									'float' : 'left'
								});
						$('.bar').progressbar();
						$('div.fl div.bar div.ui-progressbar-value').show();
						$('div.fl div.bar div.ui-progressbar-value').width(
								'' + $('#votepercentage').val() + '%');
						$('div.fl div.bar div.ui-progressbar-value').html(
								'<span style="color:#000;width:100%;left:240px;position:relative;top:3px;">'
										+ $('#votepercentage').val()
										+ '%</span>');
						$('div.fl div.bar div.ui-progressbar-value').css({
							'border-bottom-right-radius' : '4px;',
							'border-top-right-radius' : '4px;'
						});
						if (vote == '1') {
							$('div.fl div.bar div.ui-progressbar-value')
									.css(
											{
												'background-color' : '#86C536',
												'background-image' : '-moz-linear-gradient(center top , #86C536, #86C536)'
											});
						} else {
							$('div.fl div.bar div.ui-progressbar-value')
									.css(
											{
												'background-color' : '#FF0000',
												'background-image' : '-moz-linear-gradient(center top , #FF0000, #FF0000)'
											});
						}
						perce(offerId);
					}

					$("#savemoneybox2,#savemoneybox1").keydown(
							function(event) {
								if (event.shiftKey) {
									event.preventDefault();
								}

								if (event.keyCode == 46 || event.keyCode == 8
										|| event.keyCode == 9) {
								} else {
									if (event.keyCode < 95) {
										if (event.keyCode < 48
												|| event.keyCode > 57) {
											event.preventDefault();
										}
									} else {
										if (event.keyCode < 96
												|| event.keyCode > 105) {
											event.preventDefault();
										}
									}
								}
							});
					FB.XFBML.parse();
					twttr.widgets.load();

				}
			});

}
// end of code added by raman

function custompopup(id) {

	var popID = id; // Get Popup Name

	// Fade in the Popup and add close button
	$('#' + popID).css({
		"z-index" : 999999
	}).fadeIn();

	// Fade in Background
	$('body').append('<div onClick="custompopupClose();" id="fade"></div>');

	$('#fade').css({
		'filter' : 'alpha(opacity=80)'
	}).fadeIn();

	return false;

}
function custompopupClose() {
	$('#fade , .popup_block, .popup_block_signup').fadeOut('9000', function() {
		$('#fade').remove(); // fade them both out

	});
	return false;
}

function sendfeedback() {
	$.ajax({
		url : HOST_PATH_LOCALE + "offer/feedback",
		method : "post",
		data : {
			'id' : $('#voteId').val(),
			'product' : $('#and_i_bought').val(),
			'amount' : $('#savemoneybox1').val() + '.'
					+ $('#savemoneybox2').val()
		},
		type : "post",
		success : function(data) {
			$('#and_i_bought,#savemoneybox1,#savemoneybox2').val('');
			$('#positive-feedback').fadeOut('slow');
		}
	});
}

/**
 * @author spsingh disable validation event on keyup and trigger on blur
 */
$.validator.setDefaults({
	onkeyup : false,
	onfocusout : function(element) {
		$(element).valid();
	}

});

/**
 * Click on grey Star to Favorite shop
 *
 * @author mkaur update by kraj
 */
function activeStar(shopId, flag) {
	$.ajax({
		url : HOST_PATH_LOCALE + "offer/addtofavorite/shopId/" + shopId + '/flag/'
				+ flag,
		method : "post",
		dataType : "json",
		success : function(data) {
			if (data != null) {
				if (data == 1 || data == '1') {
					$('span.fr img.yellowstar' + shopId).show();
					$('span.fr img.blackstar' + shopId).hide();

				} else {

					$('span.fr img.yellowstar' + shopId).hide();
					$('span.fr img.blackstar' + shopId).show();
				}
			}
		}
	});

}

/**
 * get page property
 *
 * @author kkumar
 */

function getpageproperties(slug) {

	$.ajax({
		url : HOST_PATH_LOCALE + "page/getpageattributes",
		method : "post",
		data : {
			'slug' : slug
		},
		type : "post",
		dataType : 'Json',
		success : function(data) {
			if (data.id == '1') {
				$('#left_container').css("width", "100%");
				$('#right_container').hide();
			}
		}

	});

}

/**
 * View Vounter function
 *
 * @param eventType
 * @param type
 * @param id
 * @author Raman
 */
function viewCounter(eventType, type, id) {
		$.ajax({
		type : "POST",
		url : HOST_PATH_LOCALE + "viewcount/storecount/event/" + eventType + "/type/"
				+ type + "/id/" + id,
		success : function() {

		}
	});
}
/**
 * count percentage of voting of user generated offers
 * @param offerId
 * @author mkaur
 */
function perce(offerId) {

	$.ajax({
		url : HOST_PATH_LOCALE + "offer/countvotes/id/" + offerId,
		method : "post",
		dataType : "json",
		success : function(data) {
			if (data != null) {
				var barClass = '';
				var votes = data.vote;
				if (votes > 55) {
					barClass = 'bar-green';
				}
				if ((55 > votes && votes > 50) || (votes == 55)
						|| (votes == 50)) {
					barClass = 'bar-orange';
				}
				if (votes < 50) {
					barClass = 'bar-red';
				}

				$('div#profile-bar' + offerId + ' div#percentage').html(
						'<strong>' + data.vote + '%</strong>');
				$(
						'div#profile-bar' + offerId + ' div#vote' + offerId
								+ ' div#prline div#increased').removeAttr(
						'class').addClass(barClass);
				$(
						'div#profile-bar' + offerId + ' div#vote' + offerId
								+ ' div#prline').css({
					'margin' : '0px',
					'float' : 'left',
					'width' : votes + '%'
				});
				$('div#showVote' + offerId + ' .fontClass').html(
						'votes:' + data.poscount);
			}
		}
	});

}

/**
 * Function for cloaking all the outgoing links
 * @param actualUrl
 * @author Raman
 */

function cloakingLink(e, obj) {

	actualUrl = $(obj).attr('rel1');
	e.preventDefault();
	window.open(actualUrl, '_blank');

}

/**
 * Function for signup popup
 * @author cbhopal
 */

function showSignupPopup(type) {
	$.ajax({
		url : HOST_PATH_LOCALE + "freesignup/stap1",
		method : "post",
		type : "post",
		success : function(data) {
			custompopup('signUpPopup');
			$("div#signUpPopup").show().html(data);
			$("div#signInPopup").hide();
			$('input#emailAddress').val($("input#emailAddressHomepage").val());
			$('input#password').val($("input#passwordHomepage").val());
			$('input#confirmPassword').val($("input#confirmPasswordHomepage").val());
			if(type == 'homepage'){
				$("div#signUpFormOne").hide();
				$("div#signUpFormTwo").show();
				$("div#stepNumber").addClass('step-2').removeClass('step-1');
			}
		}
	});

}

/**
 * Function for Login popup
 * @author Raman
 */

function showSignInPopup(e, obj) {

	$.ajax({
		url : HOST_PATH_LOCALE + "login/loginpop",
		method : "post",
		type : "post",
		success : function(data) {
			custompopup('signInPopup');
			$("div#signInPopup").show().html(data);
			$("div#signUpPopup").hide();
		}
	});

}

/**
 * Function for Forgot Password popup
 * @author Raman
 */

function showForgotPasswordPopup(e, obj) {

	$.ajax({
		url : HOST_PATH_LOCALE + "login/forgotpassword",
		method : "post",
		type : "post",
		success : function(data) {
			custompopup('signInPopup');
			$("div#signInPopup").show().html(data);
			$("div#signUpPopup").hide();
		}
	});

}

/**
 * Function for Reset Password popup
 * @author Raman
 */

function showResetPasswordPopup(e, obj) {

	$.ajax({
		url : HOST_PATH_LOCALE + "login/resetpass",
		method : "post",
		type : "post",
		success : function(data) {
			custompopup('signInPopup');
			$("div#signInPopup").show().html(data);
			$("div#signUpPopup").hide();
		}
	});

}

/**
 * Function for registration from homepage
 * @author Raman
 */

var validator = null;
/**
 * form validation during update user
 * @author cbhopal
 */
function validateSignupHomepage()
{
	validator  = $("form#formOneHomePage")
	.validate(
			{
				errorClass : 'input-error-full-new',
				validClass : 'input-success-full-new',
				errorElement : 'label',
				errorPlacement : function(error, element) {
					if($(element).siblings('label').attr('generated')){
						$(element).siblings('label').remove();
					}
					$(element).next('a').after(error);
					$(error).css({'text-align':'right','width': '380px'});
				},
				rules : {
					emailAddress : {
						required : true,
						email : true,
						remote : {
							url : HOST_PATH_LOCALE
									+ "freesignup/checkuser",
							type : "post",
							beforeSend : function(xhr) {
								$('label[for=emailAddress]')
										.html(__('Valideren...')).addClass('validating');
							},

							complete : function(data) {
								$('label[for=emailAddressHomepage]')
										.removeClass(
												'validating');
								if (data.responseText == 'true') {

									$('label[for=emailAddressHomepage]').removeClass('input-error-full-new help-inline')
																.addClass('input-success-full-new')
																.html(__(''));
									$("input#emailAddressHomepage").removeClass('input-error-text-field')
									  .addClass('input-success-text-field input-success-full-new');

								} else {

									$('label[for=emailAddressHomepage]').removeClass('input-success-full-new')
																.addClass('input-error-full-new help-inline')
																.html(__(''));

									$("input#emailAddressHomepage").removeClass('input-success-text-field input-success-full-new')
									  		  .addClass('input-error-text-field');
								}

							}
						}
						//regex : /^[0-9]$/
					}
				},
				messages : {
				  emailAddress : {
						required : __("Voer uw e-mailadres in"),
						email : __("Voer een geldig e-mailadres"),
						remote : __("E-mail bestaat al")
				  }
				},

				onfocusin : function(element) {

						if($(element).attr('type') == 'text'){

							var label = this.errorsFor(element);

							 if( $( label).attr('hasError')  )
					    	 {

				    			 if($( label).attr('remote-validated') != "true")
				    			 	{

										$(element).removeClass('input-error-text-field input-success-text-field input-error-full-new input-success-full-new');
										$('label[for='+$(element).attr('id')+']').removeClass('input-error-full-new input-success-full-new help-inline')
												.html('');
								 	}

				    	     } else {

								$(element).removeClass('input-error-text-field input-success-text-field input-error-full-new input-success-full-new');
								$('label[for='+$(element).attr('id')+']').removeClass('input-error-full-new input-success-full-new help-inline')
										.html('');

						     }
						}
						else{
							$(element).removeClass('input-error-text-field input-success-text-field input-error-full-new input-success-full-new');
							$('label[for='+$(element).attr('id')+']').removeClass('input-error-full-new input-success-full-new help-inline')
									.html('');
						}

				},

				highlight : function(element,
						errorClass, validClass) {

						 $(element).removeClass('input-success-text-field')
                         .addClass('input-error-text-field input-error-full-new');


				},
				success: function(label , element) {

						$(element).removeClass('input-error-text-field')
								  .addClass('input-success-text-field input-success-full-new');
						$(label).removeClass('input-error-full-new help-inline')
								.html('').addClass('input-success-full-new');


				}

			});

}
function characterRedirect(char)
{
	var pathArray = window.location.pathname.split( '/' );
	//console.log(pathArray[1]);
	if(window.location.hash!=''){
		var url = HOST_PATH_LOCALE  +  __link('alle-winkels') + '#' + char;
		document.location.href = url;
		window.location.reload(true);
	} else if(pathArray[1] == "alle-winkels"){
		var url = HOST_PATH_LOCALE  + __link('alle-winkels') + '#' + char ;
		document.location.href = url;
		document.location.reload(true);
	} else{
		var url = HOST_PATH_LOCALE  + __link('alle-winkels') + '#' + char ;
		document.location.href = url;
	}

}

/**
 * Function for redirect to signup popup and apped all htmp of view
 * in addStoreToFavorite div
 *
 * @author  kraj
 * @version 1.0
 */
function addStoreToFavorite() {

	$.ajax({
		url : HOST_PATH_LOCALE + "login/redirecttosignup",
		method : "post",
		type : "post",
		success : function(data) {

			custompopup('addStoreToFavorite');
			$("div#addStoreToFavorite").show().html(data);
			$('#fade').removeAttr('onclick');
		}
	});

}

/**
 * Function show dummypage
 * in dummypage div
 *
 * @author  kraj
 * @version 1.0
 */
function showDummyPage() {

	$.ajax({
		url : HOST_PATH_LOCALE + "login/dummybox",
		method : "post",
		type : "post",
		success : function(data) {

			custompopup('dummypage');
			$("div#dummypage").show().html(data);
			$('#fade').removeAttr('onclick');
		}
	});

}


function signUpFromHandler(){
	validateSignupHomepage();
	if($("form#formOneHomePage").valid()){
		$("form#formOneHomePage").submit();
	}

	return false;
}

