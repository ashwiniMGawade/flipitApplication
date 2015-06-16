$(document).ready(function() {
	getVisitorList();
	
	$('#fileupload').fileupload({
	    autoUpload: true,
	    dataType: 'json',
        done: function (e, data) {
            window.location.reload(true);
        }
	});
});


var visitorListTable = null;
var id;

function getVisitorList() {
	$("ul.ui-autocomplete").css('display','none');
	$('#languageTable').addClass('widthTB');
	
	visitorListTable = $("table#languageTable").dataTable({
			"bLengthChange" : false,
			"bInfo" : false,
			"bFilter" : true,
			"bDestroy" : true,
			"bProcessing" : false,
			"bAutoWidth": false,
			"bServerSide" : true,
			"iDisplayLength" : 10,
			"aaSorting": [[ 1, 'ASC' ]], 
			"sPaginationType" : "bootstrap",
			"sAjaxSource" : HOST_PATH+"admin/language/getlanguagelist",
			"aoColumns" : [
					
					{
					"fnRender" : function(obj) {
							
							return obj.aData.fileName;
						},
						"bSortable" : true
					},
					{
					"fnRender" : function(obj) {
						return "<div style='padding-bottom : 10px;'>" +
									"<a class='btn btn-primary' style='margin-right:10px;'href='"+HOST_PATH+"admin/language/downloadfile/fname/"+obj.aData.fileName+"'>Download</a>" +
									"<a class='btn' href='"+HOST_PATH+"admin/language/scanfile/fname/"+obj.aData.fileName+"'>Scan File</a>"+
									"<a class='red delete-lng-lnk' onclick='deleteFile(\"" + obj.aData.fileName + "\");' href='javascript:void(0);' >" +__('Delete') + "</a>"+
								"</div>" ;
					}
					}],
					
					 "fnDrawCallback": function() {
						 
						 window.scrollTo(0, 0);
					 },
					"fnInitComplete" : function(obj) {
						removeOverLay();
						$('td.dataTables_empty').html(__('No record found !'));
						$('td.dataTables_empty').removeAttr('style');
						$('td.dataTables_empty').unbind('click');	
						
						/*$("form#userRegister").each(function() { this.reset(); });*/
							//removeOverLay();
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


function deleteFile(file)
{
	bootbox.confirm(__("Are you sure you want to delete this file?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		}
		else{
			 addOverLay();
			 $.ajax({
				 url : HOST_PATH + "admin/language/delete",
				 method : "post",
				 data : { 'file' : file },
				 dataType : "json",
				 type : "post",
				 success : function(data) {
						window.location = HOST_PATH + 'admin/language';
				 }
			 }); 
		}
	}); 
}
