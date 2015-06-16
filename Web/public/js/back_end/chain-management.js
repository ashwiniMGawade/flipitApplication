$(document).ready(init);

/**
 * initlize all settings during page load
 * 
 */
function init() {

	 
	var iStart = $.bbq.getState('iStart', true) || 0;
	var iSortCol = $.bbq.getState('iSortCol', true) || 1;
	var iSortDir = $.bbq.getState('iSortDir', true) || 'ASC';

	 
	var CKVal = $('input#checkBoxVal').val();
	if (CKVal == 0 || CKVal == '0') {
		$("#addStore").hide();
		$("#listShop").hide();
		$('div#addButton').hide();

	} else {

		$("#addStore").show();
		$("#listShop").show();
		$('div#addButton').show();
	}
	
	
	// call to keyword list function while loading
	if ($("table#searchChainItemTable").length) {
		getChainList(iStart, iSortCol, iSortDir);
	}

	$(window).bind('hashchange', function(e) {
		if (hashValue != location.hash && click == false) {
			keywordListTbl.fnCustomRedraw();
		}
	});
 
	
	if($("form#addShopForm").length > 0)
	{
		$("#locale").val(2);
		updateShops($("#locale")[0]);
		
		$("form#addShopForm").submit(function(e){
			

			if($("#locale").val() == 0)
			{
				$("span[for=shopName]").html("");
				$("span[for=locale]").html(__("Please select locale"));
				return false;
				
			}else if($("#searchShops").val() == "")
			{
				$("span[for=locale]").html("");
				$("span[for=shopName]").html(__("Please select shop name"));
				
				return false; 
			}else{
				return true ;
			}

			
		});
	}
	
	
}
 

var keywordListTbl = null;
var hashValue = "";
var click = false;
/**
 * get excluded keyword listing
 * 
 * @author blal
 */
function getChainList(iStart, iSortCol, iSortDir) {

	addOverLay();
	$('#searchChainItemTable').addClass('widthTB');
	keywordListTbl = $("table#searchChainItemTable")
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
						"sAjaxSource" : HOST_PATH + "admin/chain/chain-item-list/id/" + $("input#chainId").val()  ,
						"aoColumns" : [
		 						{
									"fnRender" : function(obj) {
										if (obj.aData.shopName != null) {

											return '<p editId="'
													+ obj.aData.id
													+ '" class = "colorAsLink word-wrap-without-margin-searchbar"><a href="javascript:void(0);">'
													+ obj.aData.shopName
													+ '</a></p>';
										}
										return "";
									}
								},
								{
									"fnRender" : function(obj) {

										if (obj.aData.website != null) {
											
											var val = obj.aData.website.name;
											 
											return '<p editId="'
													+ obj.aData.id
													+ '" class = "colorAsLink word-wrap-without-margin-searchbar"><a href="javascript:void(0);">'
													+ val;
													+ '</a></p>';
										}
										return "";
									},
									"bSearchable" : true,
									"bSortable" : true,
								},
								{
									"fnRender" : function(obj) {
										if (obj.aData.locale != null) {

											return '<p editId="'
													+ obj.aData.id
													+ '" class = "colorAsLink word-wrap-without-margin-searchbar"><a href="javascript:void(0);">'
													+ obj.aData.locale
													+ '</a></p>';
										}
										return "";
									}
								},
								{
									"fnRender" : function(obj) {
										if (obj.aData.status != null) {

											return '<p editId="'
													+ obj.aData.id
													+ '" class = "colorAsLink word-wrap-without-margin-searchbar"><a href="javascript:void(0);">'
													+ ((obj.aData.status == '1') ? __('On') : __('Off'))
													+ '</a></p>';
										}
										return "";
									}
								},
								{
									"fnRender" : function(obj) {
										var html = "<a href='javascript:void(0)' onclick='deleteChain("
												+ obj.aData.id
												+ ");' id='deleteshop'>"
												+ __("Delete") + "</a>";
										return html;
									},
									"bSearchable" : false,
									"bSortable" : false
								} ],
						"fnPreDrawCallback" : function(oSettings) {
							$('#keywordListTbl').css('opacity', 0.5);
						},
						"fnDrawCallback" : function(obj) {
							$('#keywordListTbl').css('opacity', 1);

				    	   window.scrollTo(0, 0);

							var state = {};
							state['iStart'] = obj._iDisplayStart;
							state['iSortCol'] = obj.aaSorting[0][0];
							state['iSortDir'] = obj.aaSorting[0][1];

							// Set the state!
							$.bbq.pushState(state);
							hashValue = location.hash;

							var aTrs = keywordListTbl.fnGetNodes();

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
 

function updateShops(el)
{
	var locale = parseInt(el.value);
	if(locale > 0)
	{
		$("#searchShops").val("");
		$("#searchShops").select2({
			placeholder: __("Search shop"),
			minimumInputLength: 1,
			ajax: {
					 url: HOST_PATH + "admin/chain/shops-list/locale/" +  locale ,
					 dataType: 'json',
					 data: function(term, page) {
			             return { keyword: term   }
			         },
					 type: 'post',
					 results: function (data, page) { 
						 return {results: data};
					 }
			},
			formatResult: function(data) {
				return data.name; 
	        },
	        formatSelection: function(data) { 
	        	$("#searchShops").val(data.name);
	        	$("#searchShopId").val(data.id);
	            return data.name; 
	        },
		});
	}
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
			__("Are you sure you want to delete this shop from the chain?"),
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
												+ "admin/chain/delete-chain-item",
										data : "id=" + id
									}).done(
									function(msg) {
										window.location.reload();
									});
				}
			});
}
 