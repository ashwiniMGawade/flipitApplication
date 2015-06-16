$(document).ready(init);

/**
 * initlize all settings during page load
 * 
 */
function init() {

	var iSearchText = $.bbq.getState( 'iSearchText' , true ) || undefined;
	var iStart = $.bbq.getState('iStart', true) || 0;
	var iSortCol = $.bbq.getState('iSortCol', true) || 1;
	var iSortDir = $.bbq.getState('iSortDir', true) || 'ASC';

	$("input#searchChain").keypress(function(e)
	{
		// if the key pressed is the enter key
		  if (e.which == 13)
		  {
			  getChainList($(this).val(),0,0,'asc');
			  e.preventDefault(); 
		  }
			  
	});

	$('#searchByChain').click(searchByChain);

	//Auto complete search for top five records in a dropdown
	$("#searchChain").autocomplete({
        minLength: 1,
        source: function( request, response){
        	var searchText = $('#searchChain').val()=='' ? undefined : $('#searchChain').val();
        	$.ajax({
        		url : HOST_PATH + "admin/chain/search-chain/keyword/" + searchText + "/flag/0",
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

	// call to keyword list function while loading
	if ($("table#chainTable").length) {
		getChainList(iSearchText,iStart, iSortCol, iSortDir);
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
function getChainList(iSearchText,iStart, iSortCol, iSortDir) {

	addOverLay();
	$("ul.ui-autocomplete").css('display','none');
	$("ul.ui-autocomplete").html('');

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
						"sAjaxSource" : HOST_PATH + "admin/chain/chain-list/searchText/"+ iSearchText,
						"aoColumns" : [
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
  							state[ 'iSearchText' ] = iSearchText;
 							$("#searchChain").val(iSearchText);
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
											});

							// Set the state!
							    
							if(iSearchText == undefined){
								$.bbq.removeState( 'iSearchText' );
							}
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


/**
 * Function call when user click on shop search button 
 * or press enter 
 * @author kraj
 */
function searchByChain()
{
	
	var searchChain = $("#searchChain").val();
	if(searchChain =='' || searchChain == null)
	{
		searchChain = undefined;
	}
	getChainList(searchChain,0,0,'asc');
}
