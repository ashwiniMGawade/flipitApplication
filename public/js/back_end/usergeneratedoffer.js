$(document).ready(init);
function init(){
	
	
	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 5;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'desc';
	var iOfferText = $.bbq.getState( 'iOfferText' , true ) || undefined;
	var iShopText = $.bbq.getState( 'iShopText' , true ) || undefined;
	var iType = $.bbq.getState( 'iType' , true ) || undefined;
	
	
	getOffers(iOfferText,iShopText,iType,iStart, iSortCol, iSortDir);
	//autocomplete for offer 
	//if press enter key the call search offer function
	$("input#searchOffer").keypress(function(e)
			{
			        // if the key pressed is the enter key
			        if (e.which == 13)
			        {
			           
			        	searchByShop();
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
	$('select#couponType').change(searchByShop);
	
	//auto complete for search offer text box
	
	$("#couponType").select2();
	$("#couponType").select2("val", "");
	
	$("#searchOffer").select2({
		placeholder: __("Search offer"),
		minimumInputLength: 1,
		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
			url : HOST_PATH + "admin/usergeneratedoffer/searchtopfiveoffer",
		 dataType: 'json',
		 data: function(term, page) {
             return {
            	 keyword: term,
            	 flag: 0
             };
         },
		 type: 'post',
		 results: function (data, page) { // parse the results into the format expected by Select2.
		 // since we are using custom formatting functions we do not need to alter remote JSON data
			 return {results: data};
			 
		 }
		},
		formatResult: function(data) { 
            return data; 
        },
        formatSelection: function(data) { 
        	$("#searchOffer").val(data);
            return data; 
        }
	});
	
	$("#searchShop").select2({
		placeholder: __("Search shop"),
		minimumInputLength: 1,
		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
			url : HOST_PATH + "admin/usergeneratedoffer/searchtopfiveshop",
		 dataType: 'json',
		 data: function(term, page) {
             return {
            	 keyword: term,
            	 flag: 0
             };
         },
		 type: 'post',
		 results: function (data, page) { // parse the results into the format expected by Select2.
		 // since we are using custom formatting functions we do not need to alter remote JSON data
			 return {results: data};
			 
		 }
		},
		formatResult: function(data) {
			return data; 
        },
        formatSelection: function(data) { 
        	$("#searchShop").val(data);
            return data; 
        },
	});
	
	
	$(window).bind( 'hashchange', function(e) {
		if(hashValue != location.hash && click == false){
			offerListTable.fnCustomRedraw();
		}
	});
	
	
}

/**
 * Function call when user click on offer search button 
 * or press enter 
 * @author kraj
 */
function searchByShop()
{
	
	var type = $("#couponType").select2('val');
	if(type=='' || type==null)
		{
			type = undefined;
		}
	var searchShop = $("#searchShop").select2('val');
	if(searchShop=='' || searchShop==null)
		{
		searchShop = undefined;
		}
	var txtOffer = $('#searchOffer').select2('val');
	if(txtOffer=='' || txtOffer==null)
	{
		txtOffer = undefined;
	}
	getOffers(txtOffer,searchShop,type,0,5,'desc');
}


var offerListTable = $('#offerListTable').dataTable();
var hashValue = "";
var click = false;
/**
 * get offer list from database according to search 
 * @param txtOffer
 * @param txtShop
 * @param type
 * @author KKumar modified by Raman
 */
function getOffers(txtOffer,txtShop,type,iStart,iSortCol,iSortDir) {
	addOverLay();
	$("ul.ui-autocomplete").css('display','none');
	$('#offerListTable').addClass('widthTB');
	$('#offerListTable').removeClass('display-none');
	offerListTable = $("#offerListTable")
	.dataTable(
			{
				"bLengthChange" : false,
				"bInfo" : true,
				"bFilter" : true,
				"bDestroy" : true,
				"bProcessing" : false,
				"bServerSide" : true,
				"oLanguage": {
				      "sInfo": "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
				},
				"iDisplayStart" : iStart,
				"iDisplayLength" :100,
				"bDeferRender": true,
				"aaSorting": [[ iSortCol , iSortDir ]],
				"sPaginationType" : "bootstrap",
				"sAjaxSource" : HOST_PATH+"admin/usergeneratedoffer/getoffer/offerText/"+ txtOffer  + "/shopText/"+ txtShop + "/couponType/"+ type +  "/flag/0",
				"aoColumns" : [
						{
							"fnRender" : function(obj) {
								
								if(obj.aData.title!=undefined && obj.aData.title!=null && obj.aData.title!=''){
								return name = "<p editId='" + obj.aData.id + "' class='editId colorAsLink word-wrap-without-margin-offer'><a href='javascript:void(0)'>"+ucfirst(obj.aData.title)+"</a></p>";
								
								}else {
									
									return name = "<p editId='" + obj.aData.id + "' class='editId colorAsLink word-wrap-without-margin-offer'><a href='javascript:void(0)'>----</a></p>";
									
								}
						},

								"bVisible":    false ,

								"bSortable" : false,
								"sType": 'numeric'
								
						},
						{
							"fnRender" : function(obj) {
								if(obj.aData.title!=undefined && obj.aData.title!=null && obj.aData.title!=''){
									
								var tag = "<p editId='" + obj.aData.id + "' style='width:130px;' class='editId colorAsLink word-wrap-without-margin-offer'><a href='javascript:void(0)'>"+ucfirst(obj.aData.title)+"</a></p>";
								
								} else{
									
								var tag = "<p editId='" + obj.aData.id + "' style='width:130px;' class='editId colorAsLink word-wrap-without-margin-offer'></p>";
									
								}
								return tag;
							 },
							"bSearchable" : true,
							"bSortable" : true,
							'sWidth' : '270px'
						},
						
						{
							"fnRender" : function(obj) {
								
								var Visability = '';
								if (parseInt(obj.aData.approved)) {

									approved = 'Yes';

								} else {

									approved = 'No';
								}
								var tag = "<p class='word-wrap-without-margin-offer'><a href='javascript:void(0)'>"+ucfirst(approved)+"</a></p>"; 
								return tag;
							 },
							"bSearchable" : true,
							"bSortable" : true,
							'sWidth' : '53px'
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
								var tag = "<p class='word-wrap-without-margin-offer'><a href='javascript:void(0)'>"+ucfirst(type)+"</a></p>"; 
								return tag;
							 },
							 
							"bSearchable" : true,
							"bSortable" : true
						},
						{
							"fnRender" : function(obj) {
								var tag='';
								if(obj.aData.shopname!=undefined && obj.aData.shopname!=null && obj.aData.shopname!='')
								{
									tag = "<p class='word-wrap-without-margin-offer'><a href='javascript:void(0)'>"+ucfirst(obj.aData.shopname)+"</a></p>";
								}else {
									
									tag = "<p class='word-wrap-without-margin-offer'><a href='javascript:void(0)'>"+ucfirst('---')+"</a></p>";
									
								}
								return tag;
							 },
							"bSearchable" : true,
							"bSortable" : true
						},
						{
							"fnRender" : function(obj) {
								
								var abc = null;
								
								if(obj.aData.startDate !=null && obj.aData.startDate !='undefined' ) {
								var splitdate = obj.aData.startDate.date.split(" ");
								if(obj.aData.startDate.date != null && splitdate[0] != '1970-01-01') {
									
										abc = obj.aData.startDate.date;
								abc ="<a href='javascript:void(0)'>" +  obj.aData.startDate.date + "</a>";
								}
							}
								 return abc;
								 
							},
							"bSearchable" : true,
							"bSortable" : true,
							'sWidth' : '100px'
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
							'sWidth' : '100px'
						},
						{
							"fnRender" : function(obj) {
								
								var tag = "<p class='word-wrap-without-margin-offer'><a href='javascript:void(0)'>"+11+"</a></p>"; 
								return tag;
							 },
							"bSearchable" : true,
							"bSortable" : true,
							'sWidth' : '64px'
						},
						{
						"fnRender" : function(obj) {
								
								var tag = " ";
								
								if(obj.aData.authorName!='' && obj.aData.authorName != null && obj.aData.authorName!=undefined){
									
									var tag = "<p class='word-wrap-without-margin-offer' style='width:55px;'><a href='javascript:void(0)'>"+obj.aData.authorName+"</a></p>";
								}
								
								return tag;
							 },
							"bSearchable" : true,
							"bSortable" : true
						},
						
						
						{
							"fnRender" : function(obj) {
								var checked;
								if(obj.aData.offline == 1){
									offlinevalue = 0;
									checked = "checked='checked'";
								} else {
								    checked = "";
								    offlinevalue = 1;
								}
								var html = "<input type='checkbox' name='offline' "+checked+" id='offline"+obj.aData.id+"' value="+offlinevalue+" onclick='makeOffline("+obj.aData.id+", this);'>";
								return html;
								
							},
							"bSearchable" : false,
							"bSortable" : false,
							'sWidth' : '56px'

						} ],
						"fnPreDrawCallback": function( oSettings ) {
							$('#offerListTable').css('opacity',0.5);
						 },		
						"fnDrawCallback" : function(obj) {
							$('#offerListTable').css('opacity',1);
							var state = {};
							state[ 'iStart' ] = obj._iDisplayStart ;
							state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
							state[ 'iSortDir' ] = obj.aaSorting[0][1] ;
							state[ 'iOfferText' ] = txtOffer;
							state[ 'iShopText' ] = txtShop;
							state[ 'iType' ] = type;
							
							$("#offerListTable").find('tr').find('td:lt(8)').click(function () {
									var eId = $(this).parent('tr').find('p').attr('editid');
									state[ 'eId' ] = eId ;
									click = true;
									$.bbq.pushState( state );
									window.location.href = HOST_PATH + "admin/usergeneratedoffer/editoffer/id/" + eId+ "?iStart="+
									obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
									obj.aaSorting[0][1]+"&iOfferText="+txtOffer+"&iShopText="+txtShop+
									"&iType="+type+"&eId="+eId;
							});
							
						    // Set the state!
						    
						    $("#couponType").select2('val',type);
						    $("#searchShop").select2('val',txtShop);
						    $("#searchOffer").select2('val',txtOffer);
						    
						    if(txtOffer == undefined){
						    	$.bbq.removeState( 'iOfferText' );
						    }
						    if(txtShop == undefined){
						    	$.bbq.removeState( 'iShopText' );
						    } 
						    if(type == undefined){
						    	$.bbq.removeState( 'iType' );
						    }
						    $.bbq.pushState( state );
						    hashValue = location.hash;
						    
						    var aTrs = offerListTable.fnGetNodes();
			
							for ( var i=0 ; i<aTrs.length ; i++ )
							{
								$editId = $(aTrs[i]).find('p').attr('editid');
								if ( $editId == $.bbq.getState( 'eId' , true ) )
								{
									$(aTrs[i]).find('td').addClass('row_selected');
								}
							}
							
							if($('td.row_selected').length > 0){
								var top = $('td.row_selected').offset().top;
							}
							var windowHeight = $(window).height() / 2 - 50;
							window.scrollTo(0, top - windowHeight);
				 },
				"fnInitComplete" : function(obj) {
					$('td.dataTables_empty').unbind('click');
					$('td.dataTables_empty').html(__('No record found !'));
					//$("form#createShop").each(function() { this.reset(); });
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
 * Call to edit function when user click on any row of the offer list
 */
function callToEdit()
{
	var id  = $(this).parents('tr').children('td').children('p.editId').attr('editId');
	//var id =  $(this).children('p.editId').attr('editId');
	window.location.href = HOST_PATH+"admin/usergeneratedoffer/editoffer/id/" + id;
}
/**
 * record move in trash
 * @param id
 * @author kraj
 */
function moveToTrash(id){
	bootbox.confirm(__("Are you sure you want to move this offer to trash?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		}
		else{
			deleteRecord(id);
		}
		
	});
}

/**
 * Make an offer offline
 * @param id
 * @author Raman
 */

function makeOffline(id, ob){
	
	if($('#offline'+id).attr('checked')) {

		bootbox.confirm(__("Are you sure you want to make this offer offline?"),__('No'),__('Yes'),function(r){
	
	
		if(!r){
			return false;
		}
		else{
			offline(id, ob);
		}
		
	});
	} else {
		bootbox.confirm(__("Are you sure you want to make this offer online?"),__('No'),__('Yes'),function(r){
			
			
			if(!r){
				return false;
			}
			else{
				offline(id, ob);
			}
			
		});
	}
	
	$('#offline'+id).removeAttr('checked','checked');
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
				
				window.location.href = "usergeneratedoffer";
				
			} else {
				
				window.location.href = "usergeneratedoffer";
			}
		}
	});	
}
function ucfirst(str) {
	var firstLetter = str.substr(0, 1);
	return firstLetter.toUpperCase() + str.substr(1);
}

/**
 * Make an offer offline
 * @param id
 * @author Raman
 */

function offline(id, ob) {
	
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/usergeneratedoffer/makeoffline",
		method : "post",
		data : {
			'id' : id,
			'ob' : ob.value
		},
		dataType : "json",
		type : "post",
		success : function(data) {
			
			if (data != null) {
				
				window.location.href = "usergeneratedoffer";
				
			} else {
				
				window.location.href = "usergeneratedoffer";
			}
		}
	});	
}