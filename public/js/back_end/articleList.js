$(document).ready(function(){

	$("#searchArticles").select2({
        placeholder: __("Search Article"),
        minimumInputLength: 1,
        ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
            url: HOST_PATH + "admin/article/searchkeyArticles",
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
            $("#searchArticles").val(data);
            return data; 
        },
    });
    $('.select2-search-choice-close').click(function(){
    	$('input#searchArticles').val('');
        getArticles(undefined,0,0,'asc');
    });

	var iSearchText = $.bbq.getState( 'iSearchText' , true ) || undefined;
	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 0;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'ASC';
	getArticles(iSearchText,iStart,iSortCol,iSortDir);
	$("ul.ui-autocomplete").css('float','right');
	
	$('#searchByArticle').click(searchByArticle);
	
	$(window).bind( 'hashchange', function(e) {
		if(hashValue != location.hash && click == false){
			shopListTable.fnCustomRedraw();
		}
	});
	
});

/**
 * Function call when user click on shop search button 
 * or press enter 
 * @author kraj
 */
function searchByArticle()
{
	
	var searchArt = $("#searchArticles").val();
	if(searchArt=='' || searchArt==null)
		{
		searchArt = undefined;
		}
	getArticles(searchArt,0,0,'asc');
}

/**
 * Call to edit function when user click on any row of the shop list
 */


function moveToTrash(id){
	bootbox.confirm(__("Are you sure you want to move this article to trash?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		}
		else{
			deleteRecord(id);
		}
		
	});
 }


/**
 * function use for deleted Article from list
 * and from database
 * @param id
 * @author jsingh5
 */
function deleteRecord(id) {
	
	
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/article/movetotrash",
		method : "post",
		data : {
			'id' : id
		},
		dataType : "json",
		type : "post",
		success : function(data) {
			
			if (data != null) {
				
				window.location.href = "article";
				
			} else {
				
				window.location.href = "article";
			}
		}
	});	
}
var articleListTbl = $('table#articleListTbl').dataTable();
var hashValue = "";
var click = false;
function getArticles(iSearchText,iStart,iSortCol,iSortDir) {
	//$('#shopListTable tr:gt(0)').remove();
	addOverLay();
	$("ul.ui-autocomplete").css('display','none');
	$("ul.ui-autocomplete").html('');
	//$('#shopList').addClass('widthTB');
	//"ui-autocomplete ui-menu ui-widget ui-widget-content ui-corner-all"
	$('#createNewShop').addClass('display-none');
	$('#shopList').removeClass('display-none');

	shopListTable = $("#articleList")
	.dataTable(
			{
				"bLengthChange" : false,
				"bInfo" : true,
				"bFilter" : true,
				"bDestroy" : true,
				"bProcessing" : false,
				"bServerSide" : true,
				//"bStateSave" : true,
				"oLanguage": {
				      "sInfo": "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
				},
				"iDisplayStart" : iStart,
				"iDisplayLength" :100,
				"bDeferRender": true,
				"aaSorting": [[ iSortCol , iSortDir ]],
                "sPaginationType" : "bootstrap",
				"sAjaxSource" : HOST_PATH+"admin/article/getarticles/searchText/"+ iSearchText + "/flag/0",
				"aoColumns" : [
					  
						{
							"fnRender" : function(obj) {
								
						        return "<p class='word-wrap-without-margin colorAsLink editId' style='' editId='" + obj.aData.id + "'><a href='javascript:void(0)'>"+obj.aData.title+"</a></p>";
						
							}
						},
						{
							"fnRender" : function(obj) {
								var tag = '';
								var dat = obj.aData.publishdate.date;
								tag = dat.split("-");
								tag2 = tag[2];
								var da = tag2.split(" ");
								 return "<a href='javascript:void(0)'>" + (da[0]+'-'+tag[1]+'-'+tag[0]) + "</a>";
								 
							},
							"bSearchable" : true,
							"bSortable" : true
						},
						{
							"fnRender" : function(obj) {
								tag = "<p class='word-wrap-without-margin-page'><a href='javascript:void(0);'>"+ucfirst(obj.aData.permalink)+"</a></p>";
								return tag;
							 },
							"bSearchable" : true,
							"bSortable" : true
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
								return "<a href='javascript:void(0)'>" + tag + "</a>";
							 },
							"bSearchable" : true,
							"bSortable" : true
						},
						{
							"fnRender" : function(obj) {
								return "<p class='word-wrap-without-margin' style=''><a href='javascript:void(0)'>"+obj.aData.authorname+"</a></p>";
							 
							},
							"bSearchable" : true,
							"bSortable" : true
							
						},
						{
							"fnRender" : function(obj) {
								
								return "<a href='javascript:void(0);' onclick='moveToTrash("+obj.aData.id+")'>"+__('Delete')+"</a>";
							 
							},
							"bSearchable" : false,
							"bSortable" : false
							
						} ],
				"fnPreDrawCallback": function( oSettings ) {
					$('#articleList').css('opacity',0.5);
				 },		
				"fnDrawCallback" : function(obj) {
					$('#articleList').css('opacity',1);
					var state = {};
					
					// Set the state!
					state[ 'iStart' ] = obj._iDisplayStart ;
					state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
					state[ 'iSortDir' ] = obj.aaSorting[0][1] ;
					state[ 'iSearchText' ] = iSearchText;
					
					$("#articleList").find('tr').find('td:lt(4)').click(function () {
						var eId = $(this).parent('tr').find('p').attr('editid');
						state[ 'eId' ] = eId ;
						click = true;
						$.bbq.pushState( state );
						window.location.href = HOST_PATH+"admin/article/editarticle/id/" + eId+ "?iStart="+
						obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
						obj.aaSorting[0][1]+"&iSearchText="+iSearchText+"&eId="+eId;
					});
					
				    $("#searchArticles").val(iSearchText);
				    
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

function callToEdit()
{
	var id  = $(this).parents('tr').children('td').children('p.editId').attr('editId');
	//var id =  $(this).children('td:eq(0)').children('p.editId').attr('editId');
	window.location.href = HOST_PATH+"admin/article/editarticle/id/" + id;
}
