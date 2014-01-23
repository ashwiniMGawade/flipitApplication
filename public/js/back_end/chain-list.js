$(document).ready(init);

/**
 * initlize all settings during page load
 * 
 */
function init() {

	var iStart = $.bbq.getState('iStart', true) || 0;
	var iSortCol = $.bbq.getState('iSortCol', true) || 1;
	var iSortDir = $.bbq.getState('iSortDir', true) || 'ASC';

		// call to keyword list function while loading
	if ($("table#chainTable").length) {
		getChainList(iStart, iSortCol, iSortDir);
	}


	$(window).bind('hashchange', function(e) {
		if (hashValue != location.hash && click == false) {
			chainListTbl.fnCustomRedraw();
		}
	});

	if( $("form#addChainForm").length > 0 )
	{
	
		addChainValidation();
	}
}

var chainListTbl = null;
var hashValue = "";
var click = false;
/**
 * get excluded keyword listing
 * 
 * @author blal
 */
function getChainList(iStart, iSortCol, iSortDir) {

	addOverLay();
	$('#chainTable').addClass('widthTB');
	chainListTbl = $("table#chainTable")
			.dataTable(
					{

						"bLengthChange" : false,
						"bInfo" : true,
						"bFilter" : true,
						"bDestroy" : true,
						"bProcessing" : false,
						"bServerSide" : true,
						"iDisplayStart" : iStart,
						"iDisplayLength" : 100,
						"oLanguage" : {
							"sInfo" : "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
						},
						"bDeferRender" : true,
						"aaSorting" : [ [ iSortCol, iSortDir ] ],
						"sPaginationType" : "bootstrap",
						"sAjaxSource" : HOST_PATH + "admin/chain/chain-list",
						"aoColumns" : [
								{

									"fnRender" : function(obj) {
										return obj.aData.id;
									},
									"bSortable" : false,
									"bVisible" : false,
									"sType" : 'numeric'
								},
								{
									"fnRender" : function(obj) {
										if (obj.aData.name != null) {

											return '<p editId="'
													+ obj.aData.id
													+ '" class = "colorAsLink word-wrap-without-margin-searchbar"><a href="javascript:void(0);">'
													+ ucfirst(obj.aData.name)
													+ '</a></p>';
										}
										return "";
									}
								},
								{
									"fnRender" : function(obj) {

										if (obj.aData.totalShops != null) {
											return '<a editId="'
													+ obj.aData.id
													+ '" class = "colorAsLink word-wrap-without-margin-searchbar"><a href="javascript:void(0);">'
													+ obj.aData.totalShops
													+ '</a></p>';
										}
										return "";
									}
								},
								{
									"fnRender" : function(obj) {
										var html = "<a href='javascript:void(0)' onclick='deleteChain("
												+ obj.aData.id
												+ ");' id='deleteChainLnk'>"
												+ __("Delete") + "</a>";
										return html;
									},
									"bSearchable" : false,
									"bSortable" : false
								} ],
						"fnPreDrawCallback" : function(oSettings) {
							$('#chainListTbl').css('opacity', 0.5);
						},
						"fnDrawCallback" : function(obj) {
							$('#chainListTbl').css('opacity', 1);

							var state = {};
							state['iStart'] = obj._iDisplayStart;
							state['iSortCol'] = obj.aaSorting[0][0];
							state['iSortDir'] = obj.aaSorting[0][1];

							$("#chainTable")
									.find('tr')
									.find('p','td')
									.click(
											function() {
												var eId = $(this).parents('tr')
														.find('p').attr(
																'editid');
												state['eId'] = eId;
												click = true;
												$.bbq.pushState(state);
												window.location.href = HOST_PATH
														+ "admin/chain/chain-item/chain/"
														+ eId
														+ "?iStart="
														+ obj._iDisplayStart
														+ "&iSortCol="
														+ obj.aaSorting[0][0]
														+ "&iSortDir="
														+ obj.aaSorting[0][1]
														+ "&eId=" + eId;
											});

							// Set the state!

							$.bbq.pushState(state);
							hashValue = location.hash;

							var aTrs = chainListTbl.fnGetNodes();

							for ( var i = 0; i < aTrs.length; i++) {
								$editId = $(aTrs[i]).find('p').attr('editid');
								if ($editId == $.bbq.getState('eId', true)) {
									$(aTrs[i]).find('td').addClass(
											'row_selected');
								}
							}

							if ($('td.row_selected').length > 0) {
								var top = $('td.row_selected').offset().top;
							}
							var windowHeight = $(window).height() / 2 - 50;
							window.scrollTo(0, top - windowHeight);

						},
						"fnInitComplete" : function(obj) {
							$('td.dataTables_empty').html(
									__('No record found !'));
							$('td.dataTables_empty').unbind('click');
							removeOverLay();

						},
						"fnServerData" : function(sSource, aoData, fnCallback) {
							$.ajax({
								"dataType" : 'json',
								"type" : "POST",
								"url" : sSource,
								"data" : aoData,
								"success" : fnCallback
							});

						}

					});
}
   

/**
 * delete keyword from database
 * 
 * @author sp singh
 */
function deleteChain(id) {
	// alert(id);
	bootbox
		.confirm(
			__("Are you sure you want to delete this chain? All chain Item will deleted as well"),
			__('No'),
			__('Yes'),
			function(r) {
				if (!r) {
					return false;
				} else {
					addOverLay();
					$
							.ajax(
									{
										type : "POST",
										url : HOST_PATH
												+ "admin/chain/delete-chain",
										data : "id=" + id
									}).done(
									function(msg) {
										window.location = HOST_PATH
												+ 'admin/chain';
									});
				}
			});
}

var validatorForNewChain = false ;
/**
 * validRules oject contain all the messages that are visible when an elment
 * value is valid
 * structure to define a message for element: key is to be element name and Value is
 * message
 */
var validRules = {
		name : __("Name looks great")
};

/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 * structure to define a message for element : key is to be element name and Value is
 * message
 */
var focusRules = {
		name : __("Enter name"),
};


/**
 * check chain beofre submit to server
 * 
 */
function addChainValidation(){
	validatorForNewChain = $("form#addChainForm").validate(
			{    
				errorClass : 'error',
				validClass : 'success',
				ignore: ":hidden",
				errorElement : 'span',
				errorPlacement : function(error, element) {
					element.parent("div").prev("div")
							.html(error);
				},
                rules : {
                	name : {
						 required : true,
						 minlength : 2
						}
				},
				messages : {
					name : {
					      required : __("Please enter name"),
					      minlength : __("Please enter at least 2 characters")
					    }
			    },
				onfocusin : function(element) {
					// display hint messages when an element got focus 
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
					// highlight borders in case of error
					$(element).parent('div')
							.removeClass(validClass)
							.addClass(errorClass).prev(
									"div").removeClass(
									validClass)
							.addClass(errorClass);
					

				},
				unhighlight : function(element,
						errorClass, validClass) {
					
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
								validRules[element.name]);
				
				},

			});
	
}


