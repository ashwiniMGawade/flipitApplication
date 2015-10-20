$(document).ready(init);
function init(){
	//cat to load page list 
	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 0;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'desc';
	pageList(iStart,iSortCol,iSortDir);
}

/**
 * function to get the list of networks from database
 * @author Jsingh5 updated by kraj
 */
var pageListTbl = $('table#pageListTbl').dataTable();
var hashValue = "";
function pageList(iStart,iSortCol,iSortDir) {
	addOverLay();
	pageListTbl = $("#pageListTbl")
	.dataTable({
        "bLengthChange" : false,
        "bInfo" : true,
        "bFilter" : true,
        "bDestroy" : true,
        "bProcessing" : false,
        "bServerSide" : false,
        "oLanguage": {
            "sInfo": "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
        },
        "iDisplayStart" : iStart,
        "iDisplayLength" : 100,
        "bDeferRender": true,
        "aaSorting": [[ iSortCol , iSortDir ]],
        "sPaginationType" : "bootstrap",
		"sAjaxSource" : HOST_PATH+"admin/page/trashlist",
		"aoColumns" : [
				
				{
					"fnRender" : function(obj) {
						tag = "<p class='word-wrap-without-margin-page'>"+obj.aData.pageTitle+"</p>";
						return tag;
					 },
					"bSearchable" : true,
					"bSortable" : true
				},
				
				{
					"fnRender" : function(obj) {
						var tag = '';
                        return obj.aData.created_at.date;
					},
					"bSearchable" : true,
					"bSortable" : true
				},
				{
					"fnRender" : function(obj) {
						
						var tag = '';
                        return obj.aData.updated_at.date;
					},
					"bSearchable" : true,
					"bSortable" : true
				},
				
				{
				"fnRender" : function(obj) {
						
						var tag = "<p class='word-wrap-without-margin-offer'>"+obj.aData.contentManagerName+"</p>"; 
						return tag;
					 },
					"bSearchable" : true,
					"bSortable" : true
				},
				
				{
					"fnRender" : function(obj) {
						
						var html = "<a href='#' onclick='restore("+obj.aData.id+");' id='deleteoffer'>"+__("Restore")+ "</a>";
						return html;
						
				},
					"bSearchable" : false,
					"bSortable" : false

				},
				
				{
					"fnRender" : function(obj) {
						
						var html = "<a href='#' onclick='deleteParmanent("+obj.aData.id+");' id='deleteoffer'>"+__("Delete")+ "</a>";
						return html;
						
				},
					"bSearchable" : false,
					"bSortable" : false

				}],
				"fnDrawCallback" : function(obj) {
					$("#offerListTable").find('tr').each(function () {
						var $tr = $(this);
						$tr.find('td:lt(9)').each(function() {
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
				    $.bbq.pushState( state );
				    hashValue = location.hash;
		 },
		"fnInitComplete" : function(obj) {
			$('td.dataTables_empty').unbind('click');
			$('td.dataTables_empty').html(__('No record found !'));
			removeOverLay();
		},
		"fnServerData" : function(sSource, aoData, fnCallback) {
			$('#pageListTbl tr:gt(0)').remove();
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
	bootbox.confirm(__("Are you sure you want to permanently delete this page?"),__('No'),__('Yes'),function(r){
		if(!r){
			
			return false;
		}
		else{
			
			deletePage(id);
		}
		
	});
}
/**
 * call to ajax for delete offer
 * @param id
 */
function deletePage(id) {
	
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/page/deletepage",
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
	bootbox.confirm(__("Are you sure you want to restore this page?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		}
		else{
			
			restorePage(id);
		}
		
	});
}
/**
 * call to ajax for restore offer
 * @param id
 */
function restorePage(id) {
	
	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/page/restorepage",
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

