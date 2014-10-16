
var validateNewShop = null; 
/**
 * execute when document is loaded 
 * @author spsingh updated by karj
 */
$(document).ready(init);
/**
 * initialize all the settings after document is ready
 * @author spsingh updated by karj
 */
function init(){
	
	// display shop list 
	var iSearchText = $.bbq.getState( 'iSearchText' , true ) || undefined;
	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 1;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'ASC';
	getShops(iSearchText,iStart,iSortCol,iSortDir) ;
	
	$('#searchByShop').click(searchByShop);
	
	$('form#searchform').submit(function() {
		return false;
	});
	//bind with keypress of search box
	$("input#searchShop").keypress(function(e){
		
			        // if the key pressed is the enter key
			        if (e.which == 13)
			        {
			          getShops($(this).val(),0,1,'asc');
			        }
			});
	
	$('.th_shop_start').css( 'width', '95px' ) ;
	/**
	 * Autocomplete towards search
	 * @author mkaur
	 */
	$("input#searchShop").autocomplete({
        minLength: 1,
        source: function( request, response) {
        	
        	var searchShop =  $('#searchShop').val()=='' ? undefined : $('#searchShop').val();
        	$.ajax({
        		/*url : HOST_PATH + "admin/shop/searchkey/keyword/" + searchShop  + '/flag/0',
     			method : "post",
     			dataType : "json",
     			type : "post",*/
        		url : HOST_PATH + "admin/shop/searchkey",
     			method : "post",
     			dataType : "json",
     			type : "post",
     			data:{keyword:searchShop,flag:0},
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
		if(hashValue != location.hash && click == false){
			shopListTable.fnCustomRedraw();
		}
	});

}

/**
 * get shops from database and display in list
 * 
 * @author mkaur updateb by karj
 * @version 1.0
 */
var shopListTable = $('#shopListTable').dataTable();
var hashValue = "";
var click = false;


function getShops(iSearchText,iStart,iSortCol,iSortDir) {
	//$('#shopListTable tr:gt(0)').remove();
	addOverLay();
	$("ul.ui-autocomplete").css('display','none');
	$("ul.ui-autocomplete").html('');
	$('#shopList').attr('style','');
	//"ui-autocomplete ui-menu ui-widget ui-widget-content ui-corner-all"
	$('#createNewShop').addClass('display-none');
	$('#shopList').removeClass('display-none');
	
	var searchText = $('#searchShop').val()=='' ? undefined : $('#searchShop').val();
	
	shopListTable = $("#shopListTable")
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
				"oLanguage": {
				      "sInfo": "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
				},
				"bDeferRender": true,
				"aaSorting": [[ iSortCol , iSortDir ]],
                "sPaginationType" : "bootstrap",
				"sAjaxSource" : HOST_PATH+"admin/shop/getshop/searchText/"+ escape(iSearchText) + '/flag/0',
				"aoColumns" : [
						{
							"fnRender" : function(obj) {
								
						        var id = null;
								return id = obj.aData.id;
						
							},
							"bVisible":    false ,
							"sType": 'numeric'
							
						},{
							"fnRender" : function(obj) {
								
								var tag = "<p editId='" + obj.aData.id + "' class='colorAsLink word-wrap-without-margin-network'><a href='javascript:void(0);'>" + ucfirst(obj.aData.name)+"</a>"; 
								return tag;
							 },
							"bSearchable" : true,
							"bSortable" : true
						},{
							"fnRender" : function(obj) {
								var tag = "<a href='javascript:void(0);'>" + obj.aData.permaLink + "</a>";
								return tag;
							 },
							"bSearchable" : true,
							"bSortable" : true
						},{
							"fnRender" : function(obj) {
								var prog='';
								if(obj.aData.affliateProgram==true){
									prog = "<a href='javascript:void(0);'>" +"Yes" + "</a>";
								}
								else{
								prog = "<a href='javascript:void(0);'>"+"No"+"</a>";
								}
								var tag = prog ;
							    return tag;
							  
							},
							"bSearchable" : true,
							"bSortable" : true
						},{
							"fnRender" : function(obj) {
								var tag = '';
								var dat = '2010-02-08 10:50:55';
								tag = dat.split("-");
								tag2 = tag[2];
								var da = tag2.split(" ");
								var date = "<a href='javascript:void(0);'>"+da[0]+'-'+tag[1]+'-'+tag[0]+"</a>";
								return date;
								 //return (da[0]+'-'+tag[1]+'-'+tag[0]);
								 
							},
							"bSearchable" : true,
							"bSortable" : true
						},{
							"fnRender" : function(obj) {

								var tag = '';
								if(obj.aData.affname==null || obj.aData.affname=='' || obj.aData.affname==undefined){
									tag = '';
							}
							else{
								tag = "<p class='word-wrap-without-margin'><a href='javascript:void(0);'>"+obj.aData.affname+"</a></p>";
							}
								
							return tag;
							},
							"bSearchable" : true,
							"bSortable" : true
						},{
							
							"fnRender" : function(obj) {
								
								if(obj.aData.discussions){
								 return  "<a href='javascript:void(0);'>" +"Yes" + "</a>";
								}
								
								return  "<a href='javascript:void(0);'>"+"No"+"</a>";
							  
							},
							"bSearchable" : true,
							"bSortable" : true
						},{
							"fnRender" : function(obj) {
								
								if(obj.aData.showSignupOption){
									
									return "<a href='javascript:void(0);'>" +"Yes" + "</a>";
								}
								
								return  "<a href='javascript:void(0);'>"+"No"+"</a>";
							
							  
							},
							"bSearchable" : true,
							"bSortable" : true
						},{
							"fnRender" : function(obj) {
								/*var tag='';
								if(obj.aData.status==true){
									tag = "<a href='javascript:void(0);'>"+'Yes'+"</a>";
								}
								else{
									tag = "<a href='javascript:void(0);'>"+'No'+"</a>";
								}
							    return tag;*/
								var	onLine = 'btn-primary';
								var	offLine = '';
								if((obj.aData.status)==false){
									var	onLine = '';
									var	offLine = 'btn-primary';
								}	
									var html = "<div editId='" + obj.aData.id + "' class='btn-group'data-toggle='buttons-checkbox' style='padding-bottom:16px;margin-top:0px; width:78px;'>"
											+ "<button class='btn "+ onLine +"' onClick='changeStatus("+ obj.aData.id+",this,\"online\")'>"+__('Yes')+"</button>"
											+ "<button class='btn "+ offLine +"'onClick='changeStatus("+ obj.aData.id+",this,\"offline\")'>"+__('No')+"</button>"
                                            + "</div>";
                                    
									return html;
							},
							"bSearchable" : true,
							"bSortable" : true
							
						},{
							"fnRender" : function(obj) {
								var tag = '';
								if(obj.aData.offlineSicne==undefined || obj.aData.offlineSicne==null || obj.aData.offlineSicne==''){
									tag='';
								}
								else{
									tag = obj.aData.offlineSicne;	
									
									tag = tag.split("-");
									tag2 = tag[2];
									var da = tag2.split(" ");
									tag  = "<a href='javascript:void(0);'>"+da[0]+'-'+tag[1]+'-'+tag[0]+"</a>";
									//tag =  (da[0]+'-'+tag[1]+'-'+tag[0]);
								}
								return tag;
							},
							"bSearchable" : true,
							"bSortable" : true
						}, {
							"fnRender" : function(obj) {
								var html = "<a href='javascript:void(0)' onclick='moveToTrash("+obj.aData.id+");' id='deleteshop'>"+__("Delete")+ "</a>";
								return html;
							},
							"bSearchable" : false,
							"bSortable" : false
						
						} ],
				"fnPreDrawCallback": function( oSettings ) {
					$('#shopListTable').css('opacity',0.5);
				 },		
				"fnDrawCallback" : function(obj) {
					$('#shopListTable').css('opacity',1);
			
				 
					var state = {};
					state[ 'iStart' ] = obj._iDisplayStart ;
					state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
					state[ 'iSortDir' ] = obj.aaSorting[0][1] ;
					state[ 'iSearchText' ] = iSearchText;
					
					$("#shopListTable").find('tr').find('td:lt(6)').click(function (e) {
						
						var el = e.target  ? e.target :  e.srcElement ;
						
						if(el.tagName != "BUTTON")
						{
							var eId = $(this).parent('tr').find('p').attr('editid');
							state[ 'eId' ] = eId ;
							$.bbq.pushState( state );
							click = true;
							window.location.href = HOST_PATH + "admin/shop/editshop/id/" + eId+ "?iStart="+
							obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
							obj.aaSorting[0][1]+"&iSearchText="+iSearchText+"&eId="+eId;
						}
					});
					
				    // Set the state!
				    if(iSearchText == undefined){
				    	$.bbq.removeState( 'iSearchText' );
				    }
				   
				    $.bbq.pushState( state );
				    hashValue = location.hash;
				    
				    var aTrs = shopListTable.fnGetNodes();
	
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
					$("form#createShop").each(function() { this.reset(); });
					$('td.dataTables_empty').html(__('No record found !'));
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
 * Call to edit function when user click on any row of the shop list
 */
function callToEdit() {
	var id  = $(this).parents('tr').children('td').children('p.editId').attr('editId');
	//var id =  $(this).children('td:eq(0)').children('p.editId').attr('editId');
	window.location.href = HOST_PATH+"admin/shop/editshop/id/" + id;
}
/**
 * bootstrap boot box for confirm messages
 * if true move to trah is called
 * @author kraj 
 */
function moveToTrash(id){
	bootbox.confirm(__("Are you sure you want to move this shop to trash?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		}
		else{
			deleteShop(id);
		}
		
	});
}

/**
 * when moveToTrash action in confirmed the ajax call to move the record according
 * to id
 * @auther mkaur
 * @param id
 * @version 1.0
 */
function deleteShop(id) {
	
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/shop/movetotrash",
		method : "post",
		data : {
			'id' : id
		},
		dataType : "json",
		type : "post",
		success : function(data) {
			
			if (data != null) {
				
				window.location.href = "shop";
				
			} else {
				
				window.location.href = "shop";
			}
		}
	});
}
/**
 * change status of shops online/offline
 * @author blal
 */
function changeStatus(id,obj,status){
	 	 addOverLay();
		 $(obj).addClass("btn-primary").siblings().removeClass("btn-primary");
		 $.ajax({
				type : "POST",
				url : HOST_PATH+"admin/shop/shopstatus",
				data : "id="+id+"&status="+status,
				success: function(ret)
				{
					if(ret)
					{
	
						$(obj).parents('td').next('td').html( "<a href='javascript:void(0);'>"+ ret +"</a>");
					}else {
						
						$(obj).parents('td').next('td').html( "");
					}
					
					
					
				}
			}).done(removeOverLay); 
	 
}

/**
 * Function call when user click on shop search button 
 * or press enter 
 * @author kraj
 */
function searchByShop(){
	
	var searchArt = $("#searchShop").val();
	if(searchArt=='' || searchArt==null)  {
		
			searchArt = undefined;
		}
	getShops(searchArt,0,0,'asc');
}

