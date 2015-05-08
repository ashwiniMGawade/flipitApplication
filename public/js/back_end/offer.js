$(document).ready(init);
function init(){
	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 5;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'desc';
	var iOfferText = $.bbq.getState( 'iOfferText' , true ) || undefined;
	var iShopText = $.bbq.getState( 'iShopText' , true ) || undefined;
	var iShopCoupon = $.bbq.getState( 'iShopCoupon' , true ) || undefined;
	var iType = $.bbq.getState( 'iType' , true ) || undefined;
	getOffers(iOfferText,iShopText,iShopCoupon,iType,iStart, iSortCol, iSortDir);
	
	$("#couponType").select2();
	$("#couponType").select2("val", "");
	
	$("#searchOffer").select2({
		placeholder: __("Search offer"),
		minimumInputLength: 1,
		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
		 url: HOST_PATH + "admin/offer/searchtopfiveoffer",
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
        
        	console.log('aa');
        	$("#searchOffer").val(data);
            return data; 
        }
	}).on('change', function(e) {
		
		console.log(e)
	    try {
	        var slug = e.val.slug;
	        window.location.href = "http://localhost/mysite/posts/"+slug;
	    } catch(error) {
	        console.log('Selected search result is invalid: ');
	    }
	});;
	
	$("#searchShop").select2({
		placeholder: __("Search shop"),
		minimumInputLength: 1,
		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
		 url: HOST_PATH + "admin/offer/searchtopfiveshop",
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

	$("#searchCoupon").select2({
		placeholder: __("Search Coupon Code"),
		minimumInputLength: 1,
		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
		 url: HOST_PATH + "admin/offer/searchtopfivecoupon",
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
        	$("#searchCoupon").val(data);
            return data; 
        },
	});

	$('.select2-search-choice-close').click(function(e){
    	$(this).parents('div.resetSearch').children('input').val('');
        searchByShop();
    });
	
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
	//if press enter key the call search Coupon function
	$("input#searchCoupon").keypress(function(e)
			{
			        // if the key pressed is the enter key
			        if (e.which == 13)
			        {
			           
			        	searchByShop();
			        }
			});
	//bind a function with coupon type drowpdown list
	$('select#couponType').change(searchByShop);
	
	
	$(window).bind( 'hashchange', function(e) {
		//console.log(window.location.hash);
		if(hashValue != location.hash && click == false){
			offerListTable.fnCustomRedraw();
		}
	});
	
//    $(window).trigger( 'hashchange' );
	
}



/**
 * Function call when user click on shop search button 
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

	var searchCoupon = $("#searchCoupon").select2('val');

	if(searchCoupon =='' || searchCoupon == null)
	{
		searchCoupon = undefined;
	} 

	getOffers(txtOffer,searchShop,searchCoupon,type,0,5,'desc');
}


var offerListTable = $('#offerListTable').dataTable();
var hashValue = "";
var click = false;
/**
 * get offer list from database according to search 
 * @param txtOffer
 * @param txtShop
 * @param type
 * @author kraj
 */
function getOffers(txtOffer,txtShop,txtCoupon,type,iStart,iSortCol,iSortDir) {
	addOverLay();
	
	$("ul.ui-autocomplete").css('display','none');
	//$('#offerListTable').addClass('widthTB');
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
				"iDisplayStart" : iStart,
				"iDisplayLength" :100,
				"bDeferRender": true,
				"aaSorting": [[ iSortCol , iSortDir ]],
				"sPaginationType" : "bootstrap",
				"oLanguage": {
				      "sInfo": "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
				},
				"sAjaxSource" : encodeURI(HOST_PATH+"admin/offer/getoffer/offerText/"+ txtOffer  + "/shopText/"+ txtShop + "/shopCoupon/"+ txtCoupon + "/couponType/"+ type +  "/flag/0"),
				"aoColumns" : [
						{
							"fnRender" : function(obj) {
								return name = "<p editId='" + obj.aData.id + "' class='editId colorAsLink word-wrap-without-margin-offer'><a href='javascript:void(0)' >"+ucfirst(obj.aData.title)+"</a></p>";
						},
							"sWidth": "542px"
						},
						{
							"fnRender" : function(obj) {
								var tag='';
								if(obj.aData.shopOffers!=undefined && obj.aData.shopOffers!=null && obj.aData.shopOffers!='') {
									if(obj.aData.shopOffers.name!=undefined && obj.aData.shopOffers.name!=null && obj.aData.shopOffers.name!='')
									{
										tag = "<p class='word-wrap-without-margin-offer'><a href='javascript:void(0)'>"+ucfirst(obj.aData.shopOffers.name)+"</a></p>";
									}else {
										
										
										tag = "<p class='word-wrap-without-margin-offer'><a href='javascript:void(0)'>"+ucfirst(obj.aData.shopOffers.name)+"</p></a>";
										
									}
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

								} else if (obj.aData.discountType == 'NW') {

									type = 'News';

								}else if (obj.aData.discountType == 'SL') {

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
							
							var tag = '';
							
							if(obj.aData.refURL){
								tag='Yes';
							}
							else {
								tag = 'No';
							}
												
							
						return  "<p editId='" + obj.aData.id + "' class='editId colorAsLink word-wrap-without-margin-offer'><a href='javascript:void(0)'>" +
						 __( tag) + "</a></p>"; 
							
							
						 },
						"bSearchable" : true,
						"bSortable" : true
						},{
							"fnRender" : function(obj) {
								var tag = '';
								if(obj.aData.couponCode){
									tag = obj.aData.couponCode;
								}
								else {
									tag = '<p style= "color: red;">Not Available</p>';
								}
								return "<a href='javascript:void(0)'>" + __(tag) + "</a>";
							 
							},
							"bSearchable" : true,
							"bSortable" : true
							
						},{
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
							"bSortable" : true
							
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
							"bSortable" : true
							
						},
						{
							"fnRender" : function(obj) {
								var click ;
								if(obj.aData.totalViewcount > 0){
									click = obj.aData.totalViewcount;
								} else {
									click = 0;
								}
								return "<a href='javascript:void(0)'>" + click + "</a>";
								//return tag;
							 },
							"bSearchable" : true,
							"bSortable" : true
						},
						{
						"fnRender" : function(obj) {
							 
								var tag = '';
								if(obj.aData.authorName!='' && obj.aData.authorName != null && obj.aData.authorName!=undefined){
									
									tag = "<p class='word-wrap-without-margin-offer'>"+obj.aData.authorName+"</p>";
								}
								
								return "<a href='javascript:void(0)'>" + tag + "</a>";
							 },
							"bSearchable" : true,
							"bSortable" : true
						},
						
						
						{
							"fnRender" : function(obj) {
								
								var html = "<a href='javascript:void(0);' onclick='moveToTrash("+obj.aData.id+");' id='deleteoffer'>"+__("Delete")+ "</a>";
								return html;
								
							},
							"bSearchable" : false,
							"bSortable" : false

						} ],
				"fnPreDrawCallback": function( oSettings ) {
					$('#offerListTable').css('opacity',0.5);
				 },		
				"fnDrawCallback" : function(obj) {
					$('#offerListTable').css('opacity',1);
					var state = {};
					// Set the state!
					state[ 'iStart' ] = obj._iDisplayStart ;
					state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
					state[ 'iSortDir' ] = obj.aaSorting[0][1] ;
					state[ 'iOfferText' ] = txtOffer;
					state[ 'iShopText' ] = txtShop;
					state[ 'iType' ] = type;
					
					$("#offerListTable").find('tr').find('td:lt(9)').click(function () {
							var eId = $(this).parent('tr').find('p').attr('editid');
							state[ 'eId' ] = eId ;
							$.bbq.pushState( state );
							click = true;
							window.location.href = HOST_PATH+"admin/offer/editoffer/id/" + eId + "?iStart="+
													obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
													obj.aaSorting[0][1]+"&iOfferText="+txtOffer+"&iShopText="+txtShop+"&iShopCoupon="+txtCoupon+
													"&iType="+type+"&eId="+eId;
					});
					
					$("#couponType").select2('val',type);
				    $("#searchShop").select2('val',txtShop);
				    $("#searchOffer").select2('val',txtOffer);
				    $("#searchCoupon").select2('val',txtCoupon);
				    
				    if(txtOffer == undefined){
				    	$.bbq.removeState( 'iOfferText' );
				    }
				    if(txtShop == undefined){
				    	$.bbq.removeState( 'iShopText' );
				    } 
				    if(txtCoupon == undefined){
				    	$.bbq.removeState( 'iShopCoupon' );
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
	//console.log(HOST_PATH+"admin/offer/editoffer/id/" + id + "#iStart="+iStart+"&iSortCol="+iSortCol+"&iSortDir="+iSortDir+"&eId="+id); 
	window.location.href = HOST_PATH+"admin/offer/editoffer/id/" + id + "#iStart="+iStart+"&iSortCol="+iSortCol+"&iSortDir="+iSortDir+"&eId="+id;
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
function deleteRecord(id) {
	
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/offer/movetotrash",
		method : "post",
		data : {
			'id' : id
		},
		dataType : "json",
		type : "post",
		success : function(data) {
			
			if (data != null) {
				
				window.location = HOST_PATH + "admin/offer";
				
			} else {
				
				window.location = HOST_PATH + 'admin/offer';
				
			} 
		}
	});	
}
function ucfirst(str) {
	var letter = '';
	if(str!=null && str!=undefined && str!=''){
		
	var firstLetter = str.substr(0, 1);
	letter =  firstLetter.toUpperCase() + str.substr(1);
	
	}else{
		
		letter = '';
	}
	return letter;
}
/* record */
