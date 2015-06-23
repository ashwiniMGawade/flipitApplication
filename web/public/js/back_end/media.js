/**
 * media.js 1.0
 * @author mkaur
 */
 
/**
 * execute when document is loaded 
 * @author mkaur
 */
$(document).ready(init);
/**
 * Initialize all the settings after document is ready
 * @author mkaur
 */

function init(){
	
	
	
	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 1;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'ASC';
	
	getMedia(iStart,iSortCol,iSortDir);
	
	$(window).bind( 'hashchange', function(e) {
		if(hashValue != location.hash && click == false){
			mediaListTable.fnCustomRedraw();
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
	window.location.href = "media/editmedia/id/" + id;
}

/**
 * @author mkaur
 * list all the media online/offline 
 */
var mediaListTable = null ;
var hashValue = "";
var click = false;
function getMedia(iStart,iSortCol,iSortDir) {
	addOverLay();
	$('#mediaListTable').addClass('widthTB');
	
	//"ui-autocomplete ui-menu ui-widget ui-widget-content ui-corner-all"
	//$('#createNewShop').addClass('display-none');
	//$('#mediaList').removeClass('display-none');
	mediaListTable = $("#mediaListTable")
	.dataTable(
			{
				"bLengthChange" : false,
				"bInfo" : true,
				"bFilter" : null,
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
				"sAjaxSource" : HOST_PATH+"admin/media/getmedia",
				"aoColumns" : [
						{
							
							"fnRender" : function(obj) {
								return id = obj.aData.id;
							},
								"bSortable" : true,
								"sType": 'numeric',
								"bVisible":    false, 
								//"bSearchable":false
						},
						
						{
							"fnRender" : function(obj) {
								var imgSrc = "";
								//alert(obj.aData.path);
								if (obj.aData.fileurl == null || obj.aData.fileurl=='' || obj.aData.fileurl==undefined) {
										imgSrc = HOST_PATH_PUBLIC
											+ "/images/back_end/user-avtar.jpg";
								} else {
									
									imgSrc = PUBLIC_PATH_LOCALE + "images/upload/media/thumb_L/"+(obj.aData.fileurl);
						
								}
								//var name1 = $('<div/>').text(obj.aData.name).html();

								var name = "<p class='word-wrap-without-margin-media'><a href='javascript:void(0);'>" + ucfirst(obj.aData.name)+ "</a></p>" ;
								// var html = "<div editId='" + obj.aData.id + "' class='grid-img text-center'>"+'<img src="'+imgSrc+'" width="126" height="89"/></div>' +	name;
								var html = "<div editId='" + obj.aData.id + "' class='grid-img text-center'><a href=" + HOST_PATH + "admin/media/editmedia/id/" + obj.aData.id + ">"+'<img src="'+imgSrc+'" /></a></div><a href=' + HOST_PATH + 'admin/media/editmedia/id/' + obj.aData.id + '>' +	name + "</a>";
								return html;
							},
							
							"bSortable" : true,
							//"bSearchable": false
						},
						{
							"fnRender" : function(obj) {
								var tag = '';
								if(obj.aData.authorName!=null){
								tag = "<p editId='" + obj.aData.id + "' class='editId word-wrap-without-margin'><a href='javascript:void(0);'>"+ucfirst(obj.aData.authorName)+"</a></p>"; 
								}
								return tag;
							 },
							 "bSortable" : true,
							 //"bSearchable": false
						},
						{
							"fnRender" : function(obj) {
								var tag = '';
								if(obj.aData.alternatetext != null){
								tag = "<p editId='" + obj.aData.id + "' class='editId word-wrap-without-margin'><a href='javascript:void(0);'>"+ucfirst(obj.aData.alternatetext)+"</a></p>"; 
								}
								return tag;
							 },
							 "bSortable" : true,
							 //"bSearchable": false
						},
						{
							"fnRender" : function(obj) {
								var tag = '';
								var dat = obj.aData.created_at.date;
								tag = dat.split("-");
								tag2 = tag[2];
								var da = tag2.split(" ");
								 return "<a href='javascript:void(0);'>" + (da[0]+'-'+tag[1]+'-'+tag[0]) + "</a>";
								 
							},
							"bSortable" : true,
							//"bSearchable": false
						},
						{
							"fnRender" : function(obj) {
								var html = "<a href='javascript:void(0)' onclick='callToPermanentDelete("+obj.aData.id+");' id='deleteshop'>"+__("Delete")+ "</a>";
								return html;
							},
							"bSortable" : false,
							//"bSearchable": false

						} ],
						"fnPreDrawCallback": function( oSettings ) {
							$('#mediaListTable').css('opacity',0.5);
						 },		
						"fnDrawCallback" : function(obj) {
							$('#mediaListTable').css('opacity',1);
							
							var state = {};
							$("#mediaList").find('tr').find('td:lt(3)').click(function () {
									var eId = $(this).parent('tr').find('p.editId').attr('editid');
									state[ 'eId' ] = eId ;
									$.bbq.pushState( state );
									window.location.href = HOST_PATH + "admin/media/editmedia/id/" + eId+ "?iStart="+
									obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
									obj.aaSorting[0][1]+"&eId="+eId;
							});
							
						    // Set the state!
						    state[ 'iStart' ] = obj._iDisplayStart ;
						    state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
						    state[ 'iSortDir' ] = obj.aaSorting[0][1] ;
						   
						    $.bbq.pushState( state );
						    hashValue = location.hash;
						    
						    var aTrs = mediaListTable.fnGetNodes();
			
							for ( var i=0 ; i<aTrs.length ; i++ )
							{
								$editId = $(aTrs[i]).find('p.editId').attr('editid');
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



/**
 * @author mkaur
 * @param str
 * @returns the string with first letter in upper case.
 */
function ucfirst(str) {
	if(str!=null){
	var firstLetter = str.substr(0,1);
	return firstLetter.toUpperCase() + str.substr(1);
	}
}

/**
 * bootstrap boot box for confirm messages
 * if true permanentDelete is called
 * @author mkaur 
 */
function callToPermanentDelete(id){
	bootbox.confirm(__("Are you sure you want to delete this media permanently?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		}
		else{
			permanentDelete(id);
		}
		
	});
}

/**
 * when permanentdelete action in confirmed the call to delete the record according
 * to id
 * @auther mkaur
 * @param id
 */
function permanentDelete(id) {
	window.location.href = 'media/permanentdelete/id/'+id;
}
