$(document).ready(function() {
	
	$('form#searchForm').submit(function(){
		getVisitorList();
		return false;
	});
	//bind with keypress of search box
	/*$("input#searchVisitor").keypress(function(e)
		{
			// if the key pressed is the enter key
			  if (e.which == 13)
			  {
				  getVisitorList();
			  }
	});*/
	/**
	 * Autocomplete towards search
	 * @author mkaur
	 */
		$("input#searchVisitor").autocomplete({
		    minLength: 1,
		    source: function( request, response)
		    {
		    	$.ajax({
		    		url : HOST_PATH + "admin/visitor/searchkey/for/0/keyword/" + $('#searchVisitor').val(),
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
		    select: function( event, ui ) {}
		});
		getVisitorList();
	});


var visitorListTable = $('table#visitorListTable').dataTable();
var id;
function getVisitorList() {
	$("ul.ui-autocomplete").css('display','none');
	$('#visitorListTable').addClass('widthTB');
	
	visitorListTable = $("table#visitorListTable").dataTable({
			"bLengthChange" : false,
			"bInfo" : false,
			"bFilter" : true,
			"bDestroy" : true,
			"bProcessing" : false,
			"bServerSide" : true,
			"iDisplayLength" : 10,
			"aaSorting": [[ 1, 'ASC' ]], 
			"sPaginationType" : "bootstrap",
			"sAjaxSource" : HOST_PATH+"admin/visitor/getvisitorlist/for/0/searchtext/"+ $('#searchVisitor').val(),
			"aoColumns" : [
					{
						"fnRender" : function(obj) {
							//alert(obj.aData.id);
							 id = null;
							return id = obj.aData.id;
						},
						"bVisible":    false ,
						"bSortable" : false,
						"sType": 'numeric'
					},
					{
					"fnRender" : function(obj) {
						var imgSrc = "";
							if (obj.aData.profile_img == null || obj.aData.profile_img=='' || obj.aData.profile_img==undefined) {
										imgSrc = HOST_PATH_PUBLIC
										+ "/images/back_end/user-avtar.jpg";
							} else {
								var image = "/images/upload/profile/"+ obj.aData.profile_img;
								imgSrc = HOST_PATH_PUBLIC + image;
							}
							var name = "<p editId='"+obj.aData.id+"' class='word-wrap-without-margin-visitor'>" + ucfirst(obj.aData.firstName) 
									+ " "
									+ ucfirst(obj.aData.lastName) + "</p>" ;
							var html = "<div editId='" + obj.aData.id + "' class='grid-img'><img src='"
									+ imgSrc + "' height='89' width='126'/></div>" +	name;
							return html;
						},
						"bSortable" : true
					},
					{
						"fnRender" : function(obj) {
						return email = "<span class='word-wrap-email' editId='"+obj.aData.id+"'>" + obj.aData.email + "</span>" ;
						},
						"bSortable" : true
					}, 
					{
					"fnRender" : function(obj) {
					
						return role =    35 ;
					
					},
					//"bSearchable" : false,
					"bSortable" : false
					} ],
					
					 "fnDrawCallback": function() {
						 $("#visitorListTable").find('tr').each(function () {
							 var $tr = $(this);
							 $tr.find('td:lt(4)').each(function() {
								 $(this).css( 'cursor', 'pointer' );	
								 $(this).bind('click',callToEdit);
									
								});
						 });
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
						$('#visitorListDiv tr:gt(0)').remove();
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
 * Fetch editable record information and file in form
 * @author mkaur
 */
function callToEdit(){
	var id =  $(this).parent('tr').children('td:eq(0)').children('div.grid-img').attr('editId');
	document.location.href =  HOST_PATH+"admin/visitor/editvisitor/id/" + id ;
}

