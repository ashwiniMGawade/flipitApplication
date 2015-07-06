$(document).ready(function() {
	
	//function call to load network list 
	var iSearchText = $.bbq.getState( 'iSearchText' , true ) || undefined;
	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 1;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'ASC';
	getNetWorkList(iSearchText,iStart,iSortCol,iSortDir);
	
	$("input#searchNetwork").keypress(function(e)
	{
			
			// if the key pressed is the enter key
			  if (e.which == 13)
			  {
				  getNetWorkList($(this).val(),0,0,'asc');
				  e.preventDefault(); 
			  }
			  
	});

	$('#searchByAff').click(searchByAffiliate);
	//Auto complete search for top five records in a dropdown
	$("#searchNetwork").autocomplete({
        minLength: 1,
        source: function( request, response){
        	var searchText = $('#searchNetwork').val()=='' ? undefined : $('#searchNetwork').val();
        	$.ajax({
        		url : HOST_PATH + "admin/affiliate/searchtopfivenetwork/keyword/" + searchText + "/flag/0",
     			method : "post",
     			dataType : "json",
     			type : "post",
     			success : function(data) {
     				
     				if (data != null) {
     					
     					//pass array of the respone in respone object of the autocomplete
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
			affiliateNetworkListTbl.fnCustomRedraw();
		}
	});
});

/**
 * function to get the list of networks from database
 * @author blal
 */
var affiliateNetworkListTbl = $('table#affiliateNetworkListTbl').dataTable();
var hashValue = "";
function getNetWorkList(iSearchText,iStart,iSortCol,iSortDir) {
	addOverLay();
	$("ul.ui-autocomplete").css('display','none');
	$("ul.ui-autocomplete").html('');
	
	affiliateNetworkListTbl = $("table#affiliateNetworkListTbl")
			.dataTable(
					
					{  
						"bDestroy" : true,
						"bProcessing" : false,
						"bServerSide" : true,
						"bFilter" : true,
						"bLengthChange" : false,
						"bInfo" : true,
						"oLanguage": {
						      "sInfo": "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
						},
						"iDisplayStart" : iStart,
						"iDisplayLength" :100,
						"bDeferRender": true,
						"aaSorting": [[ iSortCol , iSortDir ]],
						"sPaginationType" : "bootstrap",
						"sAjaxSource" : HOST_PATH+"admin/affiliate/networklist/searchText/"+ iSearchText + "/flag/0",
						"aoColumns" : [
								{
									"fnRender" : function(obj) {
										
								        var id = null;
										return id = obj.aData.id;
								
									},
									"bVisible": false ,
									"bSortable" : false,
									"sType": 'numeric'
									
								},
								{
									"fnRender" : function(obj) {
										
										var tag = '<p editId="' + obj.aData.id + '" class="colorAsLink word-wrap-without-margin-network"><a href="javascript:void(0);">'  + obj.aData.name +'</a></p>';									    return tag;
									 
									},
									"bSearchable" : true,
									"bSortable" : true,
									'sWidth' : '500px'
									
								},
								{
									"fnRender" : function(obj) {
										
										var tag = '<p editId="' + obj.aData.id + '" class="colorAsLink word-wrap-without-margin-network"><a href="javascript:void(0);">'  +  (obj.aData.subId ? obj.aData.subId : '' )    + '</a></p>';									    return tag;
									 
									},
									"bSearchable" : true,
									"bSortable" : true,
									'sWidth' : '500px'
									
								},
								{
									"fnRender" : function(obj) {
										
										var tag = '<p editId="' + obj.aData.id + '" class="colorAsLink word-wrap-without-margin-network"><a href="javascript:void(0);">'  +  (obj.aData.extendedSubid ? obj.aData.extendedSubid : '' )    + '</a></p>';									    return tag;
									 
									},
									"bSearchable" : true,
									"bSortable" : true,
									'sWidth' : '500px'
									
								}],
								
								"fnInitComplete" : function(obj) {
									$('td.dataTables_empty').html(__('No record found !'));
									$('td.dataTables_empty').unbind('click');
									removeOverLay();

								},
								
							"fnPreDrawCallback": function( oSettings ) {
								$('#affiliateNetworkListTbl').css('opacity',0.5);
							 },		
							"fnDrawCallback" : function(obj) {
								$('#affiliateNetworkListTbl').css('opacity',1);
						    	  $("#affiliateNetworkListTbl").find('tr').find('td:lt(1)').click(function () {
										var eId = $(this).parent('tr').find('p').attr('editid');
										state[ 'eId' ] = eId ;
										$.bbq.pushState( state );
										window.location.href = HOST_PATH+"admin/affiliate/editaffiliate/id/" + eId;
									});
									window.scrollTo(0, 0);
							    var state = {};
								
							    // Set the state!
							    state[ 'iStart' ] = obj._iDisplayStart ;
							    state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
							    state[ 'iSortDir' ] = obj.aaSorting[0][1] ;
							    state[ 'iSearchText' ] = iSearchText;
							    
							    $("#searchNetwork").val(iSearchText);
							    
							    if(iSearchText == undefined){
							    	$.bbq.removeState( 'iSearchText' );
							    }
							    $.bbq.pushState( state );
							    hashValue = location.hash;
							    
							    var aTrs = affiliateNetworkListTbl.fnGetNodes();
								
								for ( var i=0 ; i<aTrs.length ; i++ )
								{
									$editId = $(aTrs[i]).find('p').attr('editid');
									if ( $editId == $.bbq.getState( 'eId' , true ) )
									{
										$(aTrs[i]).find('td').addClass('row_selected');
									}
								}
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
 * @author blal
 */
function editNetwork(){
	  var id =  $(this).children('p.colorAsLink').attr('editId');
	  window.location = HOST_PATH + 'admin/affiliate/editaffiliate/id/' + id;
}

/**
 * change status of networks(online/offline)
 * @author blal
 */
function changeStatus(id,obj,status){
	
	     addOverLay(); 
		 $(obj).addClass("btn-primary").siblings().removeClass("btn-primary");
		 $.ajax({
				type : "POST",
				url : HOST_PATH+"admin/affiliate/affiliatestatus",
				data : "id="+id+"&status="+status
			}).done(function(msg) {
				removeOverLay();
				
		});
	 
 }
 
/**
 * Function call when user click on shop search button 
 * or press enter 
 * @author kraj
 */
function searchByAffiliate()
{
	
	var searchAff = $("#searchNetwork").val();
	if(searchAff=='' || searchAff==null)
		{
		searchAff = undefined;
		}
	getNetWorkList(searchAff,0,0,'asc');
}
