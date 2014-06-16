var shopListTable = $('#articleCategoryList').dataTable();

$(document).ready(function(){
	
	var iSearchText = $.bbq.getState( 'iSearchText' , true ) || undefined;
	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 0;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'ASC';
	
	
	
	getArticleCategory(iSearchText,iStart,iSortCol,iSortDir);
	
	$('#searchByArticle').click(searchByArticleCategory);
	
	$("input#searchArtCategory").keypress(function(e)
			{
			        // if the key pressed is the enter key
			        if (e.which == 13)
			        {
			        	getArticleCategory($(this).val(),0,1,'asc');
			        }
			});
	$("input#searchArtCategory").autocomplete({
        minLength: 1,
        source: function( request, response)
        {
        	$.ajax({
        		url : HOST_PATH + "admin/articlecategory/searchkey/keyword/" + $('#searchArtCategory').val() + '/flag/0',
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
		if(hashValue != location.hash && click == false){
			shopListTable.fnCustomRedraw();
		}
	});
});

var hashValue = "";
var click = false;
function getArticleCategory(iSearchText,iStart,iSortCol,iSortDir) {
	//$('#shopListTable tr:gt(0)').remove();
	addOverLay();
	$("ul.ui-autocomplete").css('display','none');
	$("ul.ui-autocomplete").html('');
	//$('#shopList').addClass('widthTB');
	//"ui-autocomplete ui-menu ui-widget ui-widget-content ui-corner-all"
	$('#createNewShop').addClass('display-none');
	$('#shopList').removeClass('display-none');
	
	shopListTable = $("#articleCategoryList")
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
				"sAjaxSource" : HOST_PATH+"admin/articlecategory/getcategories/searchText/"+ iSearchText + "/flag/0",
				"aoColumns" : [
						{
							"fnRender" : function(obj) {
								
						        return "<p class='word-wrap-without-margin colorAsLink editId' style='width:310px;' editId='" + obj.aData.id + "'><a href='javascript:void(0)' >"+ucfirst(obj.aData.name)+"</a></p>";
						
							},
							
						},
						{
							"fnRender" : function(obj) {
								var value = obj.aData.permalink; 
								value = value.replace("plus/", "");
								return "<p class='word-wrap-without-margin' style='width:190px;'>"+value+"</p>";
							 },
							"bSearchable" : true,
							"bSortable" : true
						},
						{
							"fnRender" : function(obj) {
								return "<p class='word-wrap-without-margin' style='width:190px;'>"+obj.aData.metatitle+"</p>";
							 
							},
							"bSearchable" : true,
							"bSortable" : true
							
						},
						{
							"fnRender" : function(obj) {
								
								return "<a href='javascript:void(0);' onclick='deleteShop("+obj.aData.id+")'>"+__('Delete')+"</a>";
							 
							},
							"bSearchable" : false,
							"bSortable" : false
							
						} ],
				"fnPreDrawCallback": function( oSettings ) {
					$('#articleCategoryList').css('opacity',0.5);
				 },		
				"fnDrawCallback" : function(obj) {
					$('#articleCategoryList').css('opacity',1);
					
					$("#articleCategoryList").find('tr').find('td:lt(3)').click(function () {
						var eId = $(this).parent('tr').find('p').attr('editid');
						state[ 'eId' ] = eId ;
						$.bbq.pushState( state );
						click = true;
						window.location.href = HOST_PATH+"admin/articlecategory/editcategory/id/" + eId+ "?iStart="+
						obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
						obj.aaSorting[0][1]+"&iSearchText="+iSearchText+"&eId="+eId;
					});
					var state = {};
					
				    // Set the state!
				    state[ 'iStart' ] = obj._iDisplayStart ;
				    state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
				    state[ 'iSortDir' ] = obj.aaSorting[0][1] ;
				    state[ 'iSearchText' ] = iSearchText;
				    
				    $("#searchArtCategory").val(iSearchText);
				    
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
					//$("form#createShop").each(function() { this.reset(); });
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
function callToEdit()
{
	var id  = $(this).parents('tr').children('td').children('p.editId').attr('editId');
	//var id =  $(this).children('td:eq(0)').children('p.editId').attr('editId');
	window.location.href = HOST_PATH+"admin/articlecategory/editcategory/id/" + id;
}

function deleteShop(id) {
	
	bootbox.confirm(__("Are you sure you want to permanently delete this article category?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		}
		else{
			addOverLay();
			$.ajax({
				url : HOST_PATH + "admin/articlecategory/deletecategory",
				method : "post",
				data : {
					'id' : id
				},
				dataType : "json",
				type : "post",
				success : function(data) {
					
					if (data != null) {
						
						window.location.href = HOST_PATH + "admin/articlecategory";
						
					} else {
						
						window.location.href = HOST_PATH + "admin/articlecategory";
					}
				}
			});
		}
		
	});
	
	
}

/**
 * Function call when user click on shop search button 
 * or press enter 
 * @author kraj
 */
function searchByArticleCategory()
{
	
	var searchArt = $("#searchArtCategory").val();
	if(searchArt=='' || searchArt==null)
		{
		searchArt = undefined;
		}
	getArticleCategory(searchArt,0,0,'asc');
}

