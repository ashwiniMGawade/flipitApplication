$(document).ready(init);
function init(){
	getOffers(undefined,undefined,undefined);
	//autocomplete for offer 
	//if press enter key the call search offer function
	$("input#searchOffer").keypress(function(e)
			{
			        // if the key pressed is the enter key
			        if (e.which == 13)
			        {
			           
			        	searchByOffer();
			        }
			});
	//if press enter key the call search shop function
	$("input#searchShop").keypress(function(e)
			{
			        // if the key pressed is the enter key
			        if (e.which == 13)
			        {
			           
			        	searchByShop();
			        }
			});
	//bind a function with coupon type drowpdown list
	$('select#couponType').change(searchByType);
	
	//auto complete for search offer text box
	$("#searchOffer").autocomplete({
        minLength: 1,
        source: function( request, response)
        {
        	$.ajax({
        		url : HOST_PATH + "admin/offer/searchtopfiveoffer/keyword/" + $('#searchOffer').val()+  "/flag/1",
     			method : "post",
     			dataType : "json",
     			type : "post",
     			success : function(data) {
     				if (data != null) {
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
        }
    });
	//auto complete for search shop text box
	$("#searchShop").autocomplete({
        minLength: 1,
        source: function( request, response)
        {
        	$.ajax({
        		url : HOST_PATH + "admin/offer/searchtopfiveshop/keyword/" + $('#searchShop').val()+  "/flag/0",
     			method : "post",
     			dataType : "json",
     			type : "post",
     			success : function(data) {
     				if (data != null) {
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
        }
    });
}
/**
 * Function call when user click on offer search button 
 * or press enter 
 * @author kraj
 */
function searchByOffer()
{
	getOffers($('input#searchOffer').val(),undefined,undefined);
}
/**
 * Function call when user click on shop search button 
 * or press enter 
 * @author kraj
 */
function searchByShop()
{
	var type = $('select#couponType').val();
	if(type=='' || type==null)
		{
			type = undefined;
		}
	getOffers(undefined,$('input#searchShop').val(),type);
}
/**
 * Function call when user change any value of coupon type drowpdown list
 * or press enter 
 * @author kraj
 */
function searchByType()
{
	var txtShop = $('input#searchShop').val();
	if(txtShop=='' || txtShop==null)
		{
			txtShop = undefined;
		}
		var type = $('select#couponType').val();
		if(type=='' || type==null)
		{
			type = undefined;
		}
	getOffers(undefined,txtShop,type);
}
var offerListTable = $('#offerListTable').dataTable();
/**
 * get offer list from database according to search 
 * @param txtOffer
 * @param txtShop
 * @param type
 * @author kraj
 */
function getOffers(txtOffer,txtShop,type) {
	addOverLay();
	$("ul.ui-autocomplete").css('display','none');
	shopListTable = $("#offerListTable")
	.dataTable(
			{
				"bLengthChange" : false,
				"bInfo" : false,
				"bFilter" : true,
				"bDestroy" : true,
				"bProcessing" : false,
				"bServerSide" : true,
				"iDisplayLength" : 100,
				"aaSorting": [[ 1, 'ASC' ]],
				"sPaginationType" : "bootstrap",
				//"sAjaxSource" : HOST_PATH+"admin/offer/getoffer/data/" + data,
				"sAjaxSource" : HOST_PATH+"admin/offer/gettrashedoffer/offerText/"+ txtOffer  + "/shopText/"+ txtShop + "/couponType/"+ type+  "/flag/1",
				"aoColumns" : [
						{
							"fnRender" : function(obj) {
								var tag = "<p editId='" + obj.aData.id + "' class=' editId colorAsLink word-wrap-without-margin-offer_trash'>"+ucfirst(obj.aData.title)+"</p>"; 
								return tag;
							 },
							"bSearchable" : true,
							"bSortable" : true
						},
						{
							"fnRender" : function(obj) {
								var tag='';
								if(obj.aData.shopOffers!=undefined && obj.aData.shopOffers!=null && obj.aData.shopOffers!='')
								{
									tag = "<p class='word-wrap-without-margin-offer'>"+ucfirst(obj.aData.shopOffers.name)+"</p>";
								}else {
									
									tag = "<p class='word-wrap-without-margin-offer'>"+ucfirst('---')+"</p>";
									
								}
								return tag;
							 },
							"bSearchable" : true,
							"bSortable" : true
						},
						{
							"fnRender" : function(obj) {
								
								var type = '';
								if (obj.aData.discountType == 'CD') {

									type = 'Coupon';

								} else if (obj.aData.discountType == 'SL') {

									type = 'Sale';

								} else {

									type = 'Printable';
								}
								var tag = "<p class='word-wrap-without-margin-offer_trash'>"+ucfirst(type)+"</p>"; 
								return tag;
							 },
							 
							"bSearchable" : true,
							"bSortable" : true
						},{
							"fnRender" : function(obj) {
								var tag = '';
								
								if(obj.aData.refURL){
									tag='Yes';
								}
								else {
									tag = 'No';
								}
								return   __(tag)  ;
							 
							},
								"bSearchable" : true,
								"bSortable" : true
						},{
							"fnRender" : function(obj) {
								var tag = '';
								//alert(obj.aData.extendedOffer);
								if(obj.aData.extendedOffer==true){
								tag='Yes';
								}
								else{
								tag = 'No';
								}
								return tag;
							 
							},
							"bSearchable" : true,
							"bSortable" : true,
							'sWidth' : '20px'
						},
						
						{
							"fnRender" : function(obj) {
								var date = "";
								if(obj.aData.startDate !=null && obj.aData.startDate !='undefined' ) {
									var splitdate = obj.aData.startDate.date.split(" ");
									if(obj.aData.startDate.date != null && splitdate[0] != '1970-01-01') {
										var date = obj.aData.startDate.date;
									}
								}
								 return "<a href='javascript:void(0)'>" + date + "</a>";
								 
							},
							"bSearchable" : true,
							"bSortable" : true,
							'sWidth' : '75px'
						},
						{
							"fnRender" : function(obj) {
								var date = "";
								if(obj.aData.endDate !=null && obj.aData.endDate !='undefined' ) {
									var splitdate = obj.aData.endDate.date.split(" ");
									if (obj.aData.endDate.date != null && splitdate[0] != '1970-01-01') {
										var date = obj.aData.endDate.date;
									}
								}
								return "<a href='javascript:void(0)'>" + date + "</a>";
							},
							"bSearchable" : true,
							"bSortable" : true,
							'sWidth' : '75px'
						},{
							"fnRender" : function(obj) {
								
								var html = "<a href='#' onclick='restore("+obj.aData.id+");' id='restoreoffer'>"+__('Restore')+"</a>";
								return html;
								
							},
							"bSearchable" : false,
							"bSortable" : false

						},
						{
							"fnRender" : function(obj) {
								
								var html = "<a href='#' onclick='deleteParmanent("+obj.aData.id+");' id='deleteoffer'>"+__('Delete')+"</a>";
								return html;
								
							},
							"bSearchable" : false,
							"bSortable" : false

						}
						],
						"fnDrawCallback" : function(obj) {
						
							window.scrollTo(0, 0);
				 },
				"fnInitComplete" : function(obj) {
					$('td.dataTables_empty').unbind('click');
					$('td.dataTables_empty').html(__('No record found !'));
					//$("form#createShop").each(function() { this.reset(); });
					removeOverLay();
				},
				"fnServerData" : function(sSource, aoData, fnCallback) {
					$('#offerListTable tr:gt(0)').remove();
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
 * Delete offer from database
 * @param id
 */
function deleteParmanent(id)
{
	bootbox.confirm(__("Are you sure you want to permanently delete this offer?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		}
		else{
			deleteOffer(id);
		}
		
	});
}
/**
 * call to ajax for delete offer
 * @param id
 */
function deleteOffer(id) {
	
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/offer/deleteoffer",
		method : "post",
		data : {
			'id' : id
		},
		dataType : "json",
		type : "post",
		success : function(data) {
			
			if (data != null) {
				
				window.location.href = "trash";
				
			} else {
				
				window.location.href = "trash";
			}
		}
	});
	

	
}
/**
 * restore offer
 * @param id
 */
function restore(id)
{
	bootbox.confirm(__("Are you sure you want to restore this offer?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		}
		else{
			
			restoreOffer(id);
		}
		
	});
}
/**
 * call to ajax for restore offer
 * @param id
 */
function restoreOffer(id) {
	
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/offer/restoreoffer",
		method : "post",
		data : {
			'id' : id
		},
		dataType : "json",
		type : "post",
		success : function(data) {
			
			if (data != null) {
				
				window.location.href = "trash";
				
			} else {
				
				window.location.href = "trash";
			}
		}
	});
	
}

