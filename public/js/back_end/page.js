$(document).ready(function() {
	 
	 $("#SearchPage").select2({
        placeholder: __("Search Page"),
        minimumInputLength: 1,
        ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
            url: HOST_PATH + "admin/page/searchtopfivepage",
            dataType: 'json',
            data: function(term, page) {
                return {
                 	keyword: term
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
            $("#SearchPage").val(data);
            return data; 
        },
    });
    $('.select2-search-choice-close').click(function(){
    	$('input#SearchPage').val('');
    	var type =  $('#pagetype').val()=='' ? undefined : $('#pagetype').val();
        pageList(undefined,0,0,'asc' ,type);
    });

	//cat to load page list 
	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 0;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'asc';
	var iSearchText = $.bbq.getState( 'iSearchText' , true ) || undefined;
	var iType = $.bbq.getState( 'iType' , true ) || undefined;
	
	pageList(iSearchText,iStart,iSortCol,iSortDir,iType);
	
	
	$('#searchButton').click(searchByPage);
	
	
	$('form#searchform').submit(function() {
		return false;
	});
	//bind with keypress of search box
	$("input#SearchPage").keypress(function(e)
		{
			// if the key pressed is the enter key
			  if (e.which == 13)
			  {
				 var type =  $('#pagetype').val()=='' ? undefined : $('#pagetype').val();
				  pageList($(this).val(),0,0,'asc' ,type);
			  }
	});
	
	$(window).bind( 'hashchange', function(e) {
		if(hashValue != location.hash && click == false){
			pageListTbl.fnCustomRedraw();
		}
	});

});

function moveToTrash(id){
	bootbox.confirm(__("Are you sure you want to move this page to trash?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		}
		else{
			deleteRecord(id);
		}
		
	});
 }
/**
 * function use for deleted category from list
 * and from database
 * @param id
 * @author pkaur4
 */
function deleteRecord(id) {
	
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/page/movetotrash",
		method : "post",
		data : {
			'id' : id
		},
		dataType : "json",
		type : "post",
		success : function(data) {
			
			if (data != null) {
				
				window.location.href = "page";
				
			} else {
				
				window.location.href = "page";
			}
		}
	});	
}
/**
 * function to get the list of networks from database
 * @author Jsingh5 updated by kraj
 */
var pageListTbl = $('#pageListTbl').dataTable();
var hashValue = "";
var click = false;
function pageList(iSearchText,iStart,iSortCol,iSortDir,iType) {
	addOverLay();
	
	//var type = $('#pagetype').val()=='' ? undefined : $('#pagetype').val();
	$("ul.ui-autocomplete").css('display','none');
	$("ul.ui-autocomplete").html('');
	pageListTbl = $("#pageListTbl")
	.dataTable({ 
		
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
		"iDisplayLength" : 100,
		"bDeferRender": true,
		"aaSorting": [[ iSortCol , iSortDir ]],
		"sPaginationType" : "bootstrap",
		"sAjaxSource" : HOST_PATH+"admin/page/pagelist/searchText/"+ iSearchText +"/searchType/"+ iType  +"/flag/0",
		"aoColumns" : [
				
				{
					"fnRender" : function(obj) {
						tag = "<p class='editId colorAsLink word-wrap-without-margin-page' editid="+obj.aData.id+"><a href='javascript:void(0);'>"+obj.aData.pageTitle+"</a></p>";
						return tag;
					 },
					"bSearchable" : true,
					"bSortable" : true
				},
				
				{
					"fnRender" : function(obj) {
						tag = "<p class='word-wrap-without-margin-page'><a href='javascript:void(0);'>"+ucfirst(obj.aData.pageType)+"</a></p>";
						return tag;
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
						var dat = obj.aData.created_at.date;
						tag = dat.split("-");
						tag2 = tag[2];
						var da = tag2.split(" ");
						 return "<a href='javascript:void(0);'>"+ (da[0]+'-'+tag[1]+'-'+tag[0]) + "</a>";
						 
					},
					"bSearchable" : true,
					"bSortable" : true
				},
				
				{
				"fnRender" : function(obj) {
						
						var tag = "<p class='word-wrap-without-margin-offer'><a href='javascript:void(0);'>"+obj.aData.contentManagerName+"</a></p>"; 
						return tag;
					 },
					"bSearchable" : true,
					"bSortable" : true
				},
				
				
				{
					"fnRender" : function(obj) {
						
						var html = "<a href='javascript:void(0)' onclick='moveToTrash("+obj.aData.id+");' id='deleteoffer'>"+__("Delete")+ "</a>";
						return html;
						
					},
					"bSearchable" : false,
					"bSortable" : false

				} ],
				"fnPreDrawCallback": function( oSettings ) {
					$('#pageListTbl').css('opacity',0.5);
				 },		
				"fnDrawCallback" : function(obj) {
					$('#pageListTbl').css('opacity',1);
					
					var state = {};
					
					// Set the state!
					state[ 'iStart' ] = obj._iDisplayStart ;
					state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
					state[ 'iSortDir' ] = obj.aaSorting[0][1] ;
					state[ 'iSearchText' ] = iSearchText;
					state[ 'iType' ] = iType;
				
					$("#pageListTbl").find('tr').find('td:lt(4)').click(function () {
							var eId = $(this).parent('tr').find('p').attr('editid');
							state[ 'eId' ] = eId ;
							$.bbq.pushState( state );
							click = true;
							window.location.href = HOST_PATH + "admin/page/editpage/id/" + eId+ "?iStart="+
							obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
							obj.aaSorting[0][1]+"&iSearchText="+iSearchText+"&iType="+iType+"&eId="+eId;
					});
					
					
				    $("#SearchPage").val(iSearchText);
				    $.bbq.pushState( state );
				    hashValue = location.hash;
				    
				    var aTrs = pageListTbl.fnGetNodes();
					
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
 * Fetch edited record and file in form
 * @author pkaur4
 */


/**
 * Call to edit function when user click on any row of the offer list
 */
function callToEdit()
{
	var id  = $(this).parents('tr').children('td').children('p.editId').attr('editId');
	//var id =  $(this).children('p.editId').attr('editId');
	window.location.href = HOST_PATH + "admin/page/editpage/id/" + id;
}
 
/**
 * Function call when user click on shop search button 
 * or press enter 
 * @author kraj
 */
function searchByPage()
{
	var type = $("#pagetype").val() ;
	if(type=='' || type==null)
	{
		type = undefined;
	}
	

	
	var searchArt = $("#SearchPage").val();
	if(searchArt=='' || searchArt==null)
	{
		searchArt = undefined;
	}
	pageList(searchArt,0,0,'asc',type);
}