$(document).ready(function() {
	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 1;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'ASC';
	getNewstickerList(iStart,iSortCol,iSortDir);
	$(window).bind( 'hashchange', function(e) {
		if(hashValue != location.hash){
			newstickerListTbl.fnCustomRedraw();
		}
	});
});

var newstickerListTbl = $('table#newstickerListTbl').dataTable();
/**
 * function used to get newsticker list from database
 * @author blal
 */
var hashValue = "";
function getNewstickerList(iStart,iSortCol,iSortDir) {
	//addOverLay();
	$("ul.ui-autocomplete").css('display','none');
	$("ul.ui-autocomplete").html('');
	$('#newstickerList').addClass('widthTB');
	$('#createNewstickerForm').addClass('display-none');
	$('#newstickerList').removeClass('display-none');
	newstickerListTbl = $("table#newstickerListTbl")
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
						"oLanguage": {
						      "sInfo": "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
						},
						"aaSorting": [[ iSortCol , iSortDir ]],
						"sPaginationType" : "bootstrap",
						"sAjaxSource" : HOST_PATH+"admin/newsticker/newstickerlist",
						"aoColumns" : [
								{
									"fnRender" : function(obj) {
										
								        var id = null;
										return id = obj.aData.id;
								
									},
									"bVisible":    false ,
									"bSortable" : false,
									"sType": 'numeric'
									
								},     
								{
									"fnRender" : function(obj) {
										
										var editLink = '<p editId="' + obj.aData.id + '" class="editId colorAsLink word-wrap-without-margin-offer"><a href="javascript:void(0);">' + obj.aData.title +'</a></p>';
										return editLink;
									 
									}
									
								},
								
								{
									"fnRender" : function(obj) {
										var shpName='';
										if(obj.aData.shop!=undefined && obj.aData.shop!=null && obj.aData.shop!=''){
											if(obj.aData.shop.name!=undefined && obj.aData.shop.name!=null && obj.aData.shop.name!='')
											{
												shpName = "<p class='word-wrap-without-margin-offer'><a href='javascript:void(0);'>"+obj.aData.shop.name+"</a></p>";
												
											}else {
												shpName = "<p class='word-wrap-without-margin-offer'><a href='javascript:void(0);'>"+obj.aData.shop.name+"</a></p>";
												
											}
										}
										
										return shpName;
									
									},
									
									"bSearchable" : true,
									"bSortable" : true,
								},
								{
									"fnRender" : function(obj) {
										var editLink = "";
										if(obj.aData.startdate != null) {
											if(obj.aData.startdate.date != null){
											var tag = '';	
											var dat = obj.aData.startdate.date;
											tag = dat.split("-");
											tag2 = tag[2];
											var da = tag2.split(" ");
											editLink = (da[0]+'-'+tag[1]+'-'+tag[0]);
											}
										}
										//editLink = '<p editId="' + obj.aData.date + '" class="editId colorAsLink word-wrap-without-margin-offer">'  + obj.aData.date +'</p>';
										return "<a href='javascript:void(0);'>" + editLink + "</a>";
									},
									
									"bSearchable" : true,
									"bSortable" : true,
								},{
									
									"fnRender" : function(obj) {
										
										if(obj.aData.linkstatus)
										{
											return "<a href='javascript:void(0);'>" + __('On') + "</a>";
										}
										
										return "<a href='javascript:void(0);'>" + __('Off') + "</a>";
										
									},
									"sClass" : "news-sticker-refUrl"
									
								},{
									"fnRender" : function(obj) {
										
	                               var html ="<a href='javascript:void(0);' onClick='deleteNewsticker("+obj.aData.id+")'>"+__('Delete')+"</a>";
											return html;
										},
										"bSearchable" : false,
										"bSortable" : false,
								} ],
								
								
								"fnInitComplete" : function(obj) {
									$('td.dataTables_empty').html(__('No record found!'));
									$('td.dataTables_empty').unbind('click');
									 removeOverLay();
								},
						"fnDrawCallback" : function(obj) {
							
							$("#newstickerListTbl").find('tr').find('td:lt(3)').click(function () {
								var eId = $(this).parent('tr').find('p').attr('editid');
								state[ 'eId' ] = eId ;
								$.bbq.pushState( state );
								window.location.href = HOST_PATH + "admin/newsticker/editnewsticker/id/" + eId;
						});
							
							
							window.scrollTo(0, 0);
							var state = {};
							
						    // Set the state!
						    state[ 'iStart' ] = obj._iDisplayStart ;
						    state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
						    state[ 'iSortDir' ] = obj.aaSorting[0][1] ;
						    
						    
						    var aTrs = newstickerListTbl.fnGetNodes();
							
							for ( var i=0 ; i<aTrs.length ; i++ )
							{
								$editId = $(aTrs[i]).find('p').attr('editid');
								if ( $editId == $.bbq.getState( 'eId' , true ) )
								{
									$(aTrs[i]).find('td').addClass('row_selected');
								}
							}
							
							$.bbq.pushState( state );
						    hashValue = location.hash;
						    
						},
						
						"fnServerData" : function(sSource, aoData, fnCallback) {
							$('#newstickerListTbl tr:gt(0)').remove();
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
	window.location.href = HOST_PATH+"admin/newsticker/editnewsticker/id/" + id;
}

function deleteNewsticker(id) {
	
	bootbox.confirm(__("Are you sure you want to permanently delete this record?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		} else {
			$.ajax({
				type : "POST",
				url : HOST_PATH+"admin/newsticker/deletenewsticker",
				data : "id="+id
			}).done(function(msg) {
				window.location  =  HOST_PATH + 'admin/newsticker';
			}); 
		}
		
	});
	
}
