
var validateNewShop = null; 
/**
 * execute when document is loaded 
 * @author kraj
 */
$(document).ready(init);
/**
 * initialize all the settings after document is ready
 * @author kraj
 */
function init()
{
	
	// display shop list 
	var iSearchText = $.bbq.getState( 'iSearchText' , true ) || undefined;
	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 1;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'ASC';
	
	getShops(iSearchText,iStart,iSortCol,iSortDir) ;
	
	
	$('form#searchform').submit(function() {
		return false;
	});
	$("input#searchShop").keypress(function(e)
	{
        // if the key pressed is the enter key
        if (e.which == 13)
        {
        	var searchShop = $("#searchShop").val();
        	if(searchShop =='' || searchShop == null)
        	{
        		searchShop = undefined;
        	}
        	
        	getShops(searchShop,0,0,'asc');
        	SearchArticle
        }
	});
	
	
	$("button#search-btn").click(function(){
		
		var searchShop = $("#searchShop").val();
    	if(searchShop =='' || searchShop == null)
    	{
    		searchShop = undefined;
    	}
    	
    	getShops(searchShop,0,0,'asc');
		
	});
	/**
	 * Autocomplete towards search
	 * @author kraj
	 */
	$("input#searchShop").autocomplete({
        minLength: 1,
        source: function( request, response)
        {
        	$.ajax({
        		url : HOST_PATH + "admin/shop/searchkey/keyword/" + $('#searchShop').val() + '/flag/1',
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
	
	$(window).bind( 'hashchange', function(e) {
		if(hashValue != location.hash){
			offerListTable.fnCustomRedraw();
		}
	});	
}
/**
 * get shops from database
 * 
 * @author kraj
 * @version 1.0
 */
var shopListTable = $('#shopListTable').dataTable();
var hashValue = "";
function getShops(iSearchText,iStart,iSortCol,iSortDir) {
	addOverLay();
	$("ul.ui-autocomplete").css('display','none');
	$("ul.ui-autocomplete").html('');
	$('#shopListTable').addClass('widthTB');
	//"ui-autocomplete ui-menu ui-widget ui-widget-content ui-corner-all"
	$('#createNewShop').addClass('display-none');
	var shopText =  $('#searchShop').val()=='' ? undefined : $('#searchShop').val();
	shopListTable = $("#shopListTable")
	.dataTable(
			{
				"bLengthChange" : false,
				"bInfo" : false,
				"bFilter" : true,
				"bDestroy" : true,
				"bProcessing" : false,
				"bServerSide" : true,
				"iDisplayStart" : iStart,
				"iDisplayLength" :200,
				"bDeferRender": true,
				"aaSorting": [[ iSortCol , iSortDir ]],
				"sPaginationType" : "bootstrap",
				"sAjaxSource" : HOST_PATH+"admin/shop/getshop/searchText/"+ shopText + '/flag/1',
				"aoColumns" : [
						 {
							"fnRender" : function(obj) {
							    var id = null;
								return id = obj.aData.updated_at;
						
							},
							"bVisible":    false ,
							"bSortable" : true
							//"sType": 'numeric'
							
						},
						{
							"fnRender" : function(obj) {
								
								var tag = "<p  editId='" + obj.aData.id + "' class='editId word-wrap-without-margin-shop-trash'>"+ucfirst(obj.aData.name)+"</p>"; 
								return tag;
							 },
							"bSearchable" : true,
							"bSortable" : true,
							'sWidth' : '88px'
						},
						{
							"fnRender" : function(obj) {
								var prog='';
								if(obj.aData.affliateProgram==true){
									prog='Yes';
								}
								else{
								prog = 'No';
								}
								var tag = prog ;
							    return tag;
							 
							},
							"bSearchable" : true,
							"bSortable" : true,
							'sWidth' : '103px'
						},
						{
							"fnRender" : function(obj) {
								var tag = '';
								var dat = obj.aData.created_at;
								tag = dat.split("-");
								tag2 = tag[2];
								var da = tag2.split(" ");
								 return (da[0]+'-'+tag[1]+'-'+tag[0]);
								 
							},
							"bSearchable" : true,
							"bSortable" : true,
							'sWidth' : '68px'
						},
						{
							"fnRender" : function(obj) {

								var tag = '';
								if(obj.aData.affname==null || obj.aData.affname=='' || obj.aData.affname==undefined){
									tag = '';
							}
							else{
								tag = "<p class='word-wrap-without-margin-shop-trash'>"+obj.aData.affname+"</p>";
							}
								
							return tag;
						},
							"bSearchable" : true,
							"bSortable" : true,
							'sWidth' : '83px'
						},
						{
							"fnRender" : function(obj) {
								var html = "<a href='#' onclick='restore("+obj.aData.id+");' id='restoreshop'>"+__("Restore")+ "</a>";
								return html;
							},
							"bSearchable" : false,
							"bSortable" : false,
							'sWidth' : '85px'

						},
						{
							"fnRender" : function(obj) {
								
								var html = "<a href='#' onclick='deleteParmanent("+obj.aData.id+");' id='deleteshop'>"+__("Delete")+"</a>";
								return html;
								
							},
							"bSearchable" : false,
							"bSortable" : false,
							'sWidth' : '84px'

						}
						],
				"fnDrawCallback" : function(obj) {
					window.scrollTo(0, 0);
					var state = {};
					
				    // Set the state!
				    state[ 'iStart' ] = obj._iDisplayStart ;
				    state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
				    state[ 'iSortDir' ] = obj.aaSorting[0][1] ;
				    state[ 'iSearchText' ] = shopText;
				    
				    $("#searchShop").val(shopText);
				    
				    if(shopText == undefined){
				    	$.bbq.removeState( 'iSearchText' );
				    }
				    $.bbq.pushState( state );
				    hashValue = location.hash;
				},
				
				"fnInitComplete" : function(obj) {
					$("form#createShop").each(function() { this.reset(); });
					$('td.dataTables_empty').html(__('No record found !'));
					removeOverLay();
				},
				"fnServerData" : function(sSource, aoData, fnCallback) {
					$('#shopListTable tr:gt(0)').remove();
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
 * Delete shop from database
 * @param id
 * @author kraj
 */
function deleteParmanent(id)
{
	bootbox.confirm(__("Are you sure you want to permanently delete this record?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		}
		else{
			deleteShop(id);
		}
		
	});
}
/**
 * call to ajax for delete shop
 * @param id
 * @author kraj
 */
function deleteShop(id) {
	
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/shop/deleteshop",
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
 * restore shop
 * @param id
 * @author kraj
 */
function restore(id)
{
	bootbox.confirm(__("Are you sure you want to restore this record?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		}
		else{
			
			restoreShop(id);
		}
		
	});
}
/**
 * call to ajax for restore shop
 * @param id
 * @author kraj
 */
function restoreShop(id) {
	
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/shop/restoreshop",
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
