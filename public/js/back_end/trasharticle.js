$(document).ready(init);
function init(){
	
	var iSearchText = $.bbq.getState( 'iSearchText' , true ) || undefined;
	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 0;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'ASC';
	
	getArticles(iSearchText,iStart,iSortCol,iSortDir);

	
	
	//bind with keypress of search box
	$("input#SearchArticle").keypress(function(e){
		
			// if the key pressed is the enter key
			  if (e.which == 13)
			  {
				  
				  var searchArticle = $("#SearchArticle").val();
		        	if(searchArticle =='' || searchArticle == null)
		        	{
		        		searchArticle = undefined;
		        	}
		        	
		        	getArticles(searchArticle,0,0,'asc');
		        	
		        	return false; 
		        	
			  }
			  
	});
	
	
	
	$("button#searchButton").click(function(){
		
		var searchArticle = $("#SearchArticle").val();
      	if(searchArticle =='' || searchArticle == null)
      	{
      		searchArticle = undefined;
      	}
      	
      	getArticles(searchArticle,0,0,'asc');
		
	});
	
	
	//Auto complete for search offer text box
	$("#SearchArticle").autocomplete({
        minLength: 1,
        source: function( request, response){
        	
        	$.ajax({
        		url : HOST_PATH + "admin/article/searchtopfivearticle/keyword/" + $('#SearchArticle').val()+"/flag/1",
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
        select: function( event, ui ) {}
    }); 
	$(window).bind( 'hashchange', function(e) {
		if(hashValue != location.hash){
			offerListTable.fnCustomRedraw();
		}
	});

}
var articleListTbl = $('table#articleListTbl').dataTable();
var hashValue = "";
function getArticles(iSearchText,iStart,iSortCol,iSortDir) {

	
	addOverLay();
	$("ul.ui-autocomplete").css('display','none');
	$("ul.ui-autocomplete").html('');
	
	//$('#shopList').addClass('widthTB');
	//"ui-autocomplete ui-menu ui-widget ui-widget-content ui-corner-all"
	
	$('#createNewShop').addClass('display-none');
	$('#shopList').removeClass('display-none');
	var searchArt = $('#SearchArticle').val()=='' ? undefined : $('#SearchArticle').val();
	articleListTbl = $("#articleListTbl")
	.dataTable(
			{
				"bLengthChange" : false,
				"bInfo" : false,
				"bFilter" : true,
				"bDestroy" : true,
				"bProcessing" : false,
				"bServerSide" : true,
				"iDisplayStart" : iStart,
				"iDisplayLength" :100,
				"bDeferRender": true,
				"aaSorting": [[ iSortCol , iSortDir ]],
                "sPaginationType" : "bootstrap",
				"sAjaxSource" : HOST_PATH+"admin/article/gettrashlist/searchText/"+ searchArt + "/flag/1",
				"aoColumns" : [
						
						{
							"fnRender" : function(obj) {
								
						        return "<p class='word-wrap-without-margin colorAsLink editId' style='width:310px;' editId='" + obj.aData.id + "'>"+obj.aData.title+"</p>";
						
							},
							"bSearchable" : true,
							"bSortable" : true,
							"sWidth": "auto" 
						},
						{
							"fnRender" : function(obj) {
								var tag = '';
								var dat = obj.aData.created_at.date;
								tag = dat.split("-");
								tag2 = tag[2];
								var da = tag2.split(" ");
								 return (da[0]+'-'+tag[1]+'-'+tag[0]);
								 
							},
							"bSearchable" : true,
							"bSortable" : true,
							"sWidth": "auto" 
						},
						{
							"fnRender" : function(obj) {
								var tag = '';
								if(obj.aData.publish==true){
								tag='Yes';
								}
								else{
								tag = 'No';
								}
								return tag;
							 },
							"bSearchable" : true,
							"bSortable" : true,
							"sWidth": "auto" 
						},
					
						{
							"fnRender" : function(obj) {
							
								return "<p class='word-wrap-without-margin' style='width:190px;'>"+obj.aData.authorname+"</p>";
							 
							},
							"bSearchable" : true,
							"bSortable" : true,
							"sWidth": "auto" 
							
						},
					{
							"fnRender" : function(obj) {
								
								var html = "<a href='javascript:void(0);' onclick='restore("+obj.aData.id+");' id='deleteArticle'>"+__("Restore")+"</a>";
								return html;
								
							},
							"bSearchable" : false,
							"bSortable" : false,
							"sWidth": "auto" 

				},
						{
							"fnRender" : function(obj) {
								
								return "<a href='javascript:void(0);' onclick='deleteParmanent("+obj.aData.id+")'id='deleteArticles'>"+__("Delete")+"</a>";
							 
							},
							"bSearchable" : false,
							"bSortable" : false,
							"sWidth": "auto" 
							
						}
				],	
						
				"fnDrawCallback" : function(obj) {
					
					$("#articleList").find('tr').each(function () {
						var $tr = $(this);
						$tr.find('td:lt(4)').each(function() {
						$(this).bind('click',callToEdit);
						$(this).css( 'cursor', 'pointer' );
												        	
					});
					});
					window.scrollTo(0, 0);
					var state = {};
					
				    // Set the state!
				    state[ 'iStart' ] = obj._iDisplayStart ;
				    state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
				    state[ 'iSortDir' ] = obj.aaSorting[0][1] ;
				    state[ 'iSearchText' ] = searchArt;
				    
				    $("#SearchArticle").val(searchArt);
				    
				    if(searchArt == undefined){
				    	$.bbq.removeState( 'iSearchText' );
				    }
				    $.bbq.pushState( state );
				    hashValue = location.hash;
					
				 },
				
				"fnInitComplete" : function(obj) {
					//$("form#createShop").each(function() { this.reset(); });
					$('td.dataTables_empty').html(__('No record found !'));
					$('td.dataTables_empty').unbind('click');
					removeOverLay();
				},
				"fnServerData" : function(sSource, aoData, fnCallback) {
					$('#articleList tr:gt(0)').remove();
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
	bootbox.confirm(__("Are you sure you want to permanently delete this article?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		}
		else{
			
			deleteArticles(id);
		}
		
	});
}
/**
 * call to ajax for delete offer
 * @param id
 */
function deleteArticles(id) {
	
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/article/deletearticles",
		method : "post",
		data : {
			'id' : id
		},
		dataType : "json",
		type : "post",
		success : function(data) {
			if (data != null) {
				window.location.href = "trasharticle";
			} else {
				window.location.href = "trasharticle";
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
	bootbox.confirm(__("Are you sure you want to restore this article?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		}
		else{
			
			restoreArticles(id);
		}
		
	});
}
/**
 * call to ajax for restore offer
 * @param id
 */
function restoreArticles(id) {
	
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/article/restorearticle",
		method : "post",
		data : {
			'id' : id
		},
		dataType : "json",
		type : "post",
		success : function(data) {
			
			if (data != null) {
				
				window.location.href = "trasharticle";
				
			} else {
				
				window.location.href = "trasharticle";
			}
		}
	});
	
}

