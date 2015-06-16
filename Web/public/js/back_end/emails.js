$(document).ready(init);
function init(){
	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 5;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'desc';
    getEmails(iStart, iSortCol, iSortDir);
	
}

var emailsListTable = $('#emailsListTable').dataTable();
var hashValue = "";
var click = false;
/**
 * get offer list from database according to search 
 * @author asharma
 */
function getEmails(iStart,iSortCol,iSortDir) {
	addOverLay();
	
	$("ul.ui-autocomplete").css('display','none');
	$('#emailsListTable').removeClass('display-none');
	emailsListTable = $("#emailsListTable")
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
				"oLanguage": {
				      "sInfo": "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
				},
				"sAjaxSource" : encodeURI(HOST_PATH+"admin/email/get-emails/"),
				"aoColumns" : [
						{
							"fnRender" : function(obj) {
								
								return name = "<p editId='" + obj.aData.id + "' class='editId colorAsLink word-wrap-without-margin-offer'><a href='javascript:void(0)' >"+ucfirst(obj.aData.type)+"</a></p>";
						},
							"sWidth": "542px"
															
						},
						{
							"fnRender" : function(obj) {
								
								var sd = "";
								if(obj.aData.send_date != null){
								var tag = '';
								var dat = obj.aData.send_date;
								tag = dat.split("-");
								tag2 = tag[2];
								var da = tag2.split(" ");
								
								sd = (da[0]+'-'+tag[1]+'-'+tag[0]);
								}
								 return  sd;
								 
							},
							"bSearchable" : true,
							"bSortable" : true
							
						},
						{
							"fnRender" : function(obj) {
								
									var checkCounter = obj.aData.send_counter;	
									if(checkCounter != parseInt(checkCounter)){
											checkCounter = 0;

									}
								
								return number = "<p class='colorAsLink word-wrap-without-margin-offer'>"+checkCounter+"</p>";
								 
							},
							"bSearchable" : true,
							"bSortable" : true
							
						}],
				"fnPreDrawCallback": function( oSettings ) {
					$('#emailsListTable').css('opacity',0.5);
				 },		
				"fnDrawCallback" : function(obj) {
					$('#emailsListTable').css('opacity',1);
					var state = {};
					// Set the state!
					state[ 'iStart' ] = obj._iDisplayStart ;
					state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
					state[ 'iSortDir' ] = obj.aaSorting[0][1] ;

					$("#emailsListTable").find('tr').find('td:lt(9)').click(function () {
							var eId = $(this).parent('tr').find('p').attr('editid');
							state[ 'eId' ] = eId ;
							$.bbq.pushState( state );
							click = true;
							window.location.href = HOST_PATH+"admin/email/edit-emails/id/" + eId + "?iStart="+
													obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
													obj.aaSorting[0][1]+"&eId="+eId;
					});
					
				    $.bbq.pushState( state );
				    hashValue = location.hash;
				    
				    var aTrs = emailsListTable.fnGetNodes();
	
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
					//$("form#createShop").each(function() { this.reset(); });
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

function ucfirst(str) {
	var letter = '';
	if(str!=null && str!=undefined && str!=''){
		
	var firstLetter = str.substr(0, 1);
	letter =  firstLetter.toUpperCase() + str.substr(1);
	
	}else{
		
		letter = '';
	}
	return letter;
}
/* record */
