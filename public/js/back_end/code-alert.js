$(document).ready(init);
function init() {
	var iSearchText = $.bbq.getState( 'iSearchText' , true ) || undefined;
	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 1;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'ASC';
	getcodeAlertList(iSearchText,iStart,iSortCol,iSortDir);
	$('#codeAlertListTable_filter').css('display', 'none');
	$(window).bind( 'hashchange', function(e) {
		if(hashValue != location.hash && click == false){
			codeAlertListTable.fnCustomRedraw();
		}
	});
}

function ucfirst(str) {
	if(str!=null){
		var firstLetter = str.substr(0,1);
		return firstLetter.toUpperCase() + str.substr(1);
	}
}
 
var codeAlertListTable = null ;
var hashValue = "";
var click = false;
function getcodeAlertList(iSearchText,iStart,iSortCol,iSortDir){
	$('#codeAlertListTable').addClass('widthTB');
	$("ul.ui-autocomplete").css('display','none');
	addOverLay();
	codeAlertListTable = $("#codeAlertListTable").dataTable(
		{
			"bLengthChange" : false,
			"bInfo" : true,
			"bFilter" : true,
			"bDestroy" : true,
			"bProcessing" : false,
			"bServerSide" : true,
			"iDisplayLength" : 100,
			"oLanguage": {
			      "sInfo": "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
			},
			"iDisplayStart" : iStart,
			"aaSorting": [[ iSortCol , iSortDir ]],
			"sPaginationType" : "bootstrap",
			"sAjaxSource" : HOST_PATH + "admin/widget/widgetlist/searchText/"+ iSearchText +"/flag/0",
			"aoColumns" : [
				{
					"fnRender" : function(obj) {
				     	return id = obj.aData.id;
					},
					"bVisible":    false ,
					"bSortable" : false,
					"sType": 'numeric'
				},  
               {
				"fnRender" : function(obj) {
					var tag = "";
					tag ="<p editId='" + obj.aData.id + 
					"' class='editId word-wrap-without-margin-widget store-offer'><a href='/admin/offer/editoffer/id/"+
					 obj.aData.id +"'>Zalando - offer title</a></p>";
					return tag;
				},
				"bSearchable" : true,
				"bSortable" : true
               },
               {
					"fnRender" : function(obj) {
						var tag = "";
						if(obj.aData.id){
							tag = obj.aData.id;
						} 
						return tag; 
					},
					"bSearchable" : false,
					"bSortable" : true
	               },
			],
			"fnPreDrawCallback": function( oSettings ) {
				$('#codeAlertListTable').css('opacity',0.5);
			 },		
			"fnDrawCallback" : function(obj) {
				$('#codeAlertListTable').css('opacity',1);
					var state = {};
					state[ 'iStart' ] = obj._iDisplayStart ;
					state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
					state[ 'iSortDir' ] = obj.aaSorting[0][1] ;
					state[ 'iSearchText' ] = iSearchText;
				    $("#SearchcodeAlert").val(iSearchText);
				    if(iSearchText == undefined){
				    	$.bbq.removeState( 'iSearchText' );
				    }
				  
				    $.bbq.pushState( state );
				    hashValue = location.hash;
				    var aTrs = codeAlertListTable.fnGetNodes();
	
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
				removeOverLay();
				$('td.dataTables_empty').html(__('No record found !'));
				$('td.dataTables_empty').removeAttr('style');
				$('td.dataTables_empty').unbind('click');
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
