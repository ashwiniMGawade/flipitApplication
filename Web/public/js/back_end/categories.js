$(document).ready(function() {
	//cat to load category list 
	// display shop list 
	var iSearchText = $.bbq.getState( 'iSearchText' , true ) || undefined;
	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 1;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'ASC';
	getCategoryList(iSearchText,iStart,iSortCol,iSortDir);
	
	
	$('#searchButton').click(searchByCategory);
	
	
	$('form#searchform').submit(function() {
		return false;
	});
	//bind with keypress of search box
	$("input#SearchCategory").keypress(function(e)
		{
			// if the key pressed is the enter key
			  if (e.which == 13)
			  {
			      getCategoryList($(this).val(),0,1,'asc');
			  }
	});

	$(window).bind( 'hashchange', function(e) {
		if(hashValue != location.hash && click == false){
			categoryListTbl.fnCustomRedraw();
		}
	});
	
	//Auto complete for search category text box
	$("#SearchCategory").autocomplete({
        minLength: 1,
        source: function( request, response){
        	
        	$.ajax({
        		url : HOST_PATH + "admin/category/searchtopfivecategory/keyword/" + $('#SearchCategory').val(),
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
});

/**
 * function used for delete category from list
 * and from database
 * @param id
 * @author blal
 */
function deleteCategory(id) {
	
	bootbox.confirm(__("Are you sure you want to permanently delete this record?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		} else {
			$.ajax({
				type : "POST",
				url : HOST_PATH+"admin/category/deletecategory",
				data : "id="+id
			}).done(function(msg) {
				
				window.location  =    HOST_PATH + 'admin/category';
				
		    }); 
		}
		
	});
	
}
categoryListTbl = $('table#categoryListTbl').dataTable();
/**
 * function used to get category list from database
 * @author blal
 */
var hashValue = "";
var click = false;
function getCategoryList(iSearchText,iStart,iSortCol,iSortDir) {
	addOverLay();
	$("ul.ui-autocomplete").css('display','none');
	$("ul.ui-autocomplete").html('');
	$('#categoryList').addClass('widthTB');
	$('#saveCategoryForm').addClass('display-none');
	$('#categoryList').removeClass('display-none');
	
	categoryListTbl = $("table#categoryListTbl")
			.dataTable(
					{
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
						"sAjaxSource" : HOST_PATH+"admin/category/categorylist/SearchText/"+ iSearchText +"/flag/0",
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
										
										var editLink = '<p editId="' + obj.aData.id + '" class="colorAsLink word-wrap-without-margin-category"><a href="javascript:void(0);">'  + obj.aData.name +'</a></p>';
										return editLink;
									 
									},
									
								},
								{
									"fnRender" : function(obj) {
										
									var	onLine = 'btn-primary';
									var	offLine = '';
									if(!parseInt(obj.aData.status)){
										var	onLine = '';
										var	offLine = 'btn-primary'	;
									}	
										var html = "<div editId='" + obj.aData.id + "' class='btn-group'data-toggle='buttons-checkbox' style='padding-bottom:16px;margin-top:0px;'>"
												+ "<button class='btn "+ onLine +"' onClick='changeStatus("+ obj.aData.id+",this,\"online\")'>"+__('Yes')+"</button>"
												+ "<button class='btn "+ offLine +"'onClick='changeStatus("+ obj.aData.id+",this,\"offline\")'>"+__('No')+"</button>"
                                                + "</div>";
                                        
										return html;
									},
									
									"bSearchable" : false,
									"bSortable" : false,
								},{
									"fnRender" : function(obj) {
										
	                               var html ="<a href='javascript:;' onClick='deleteCategory("+obj.aData.id+")'>"+__('Delete')+"</a>";
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
						"fnPreDrawCallback": function( oSettings ) {
							$('#categoryListTbl').css('opacity',0.5);
						 },		
						"fnDrawCallback" : function(obj) {
							$('#categoryListTbl').css('opacity',1);
					
							
							var state = {};
							state[ 'iStart' ] = obj._iDisplayStart ;
							state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
							state[ 'iSortDir' ] = obj.aaSorting[0][1] ;
							state[ 'iSearchText' ] = iSearchText;
							$("#SearchCategory").val(iSearchText);
							
							$("#categoryListTbl").find('tr').find('td:lt(1)').click(function () {
									var eId = $(this).parent('tr').find('p').attr('editid');
									state[ 'eId' ] = eId ;
									click = true;
									$.bbq.pushState( state );
									window.location.href = HOST_PATH+"admin/category/editcategory/id/" + eId+ "?iStart="+
									obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
									obj.aaSorting[0][1]+"&iSearchText="+iSearchText+"&eId="+eId;
							});
							
						    // Set the state!
						    if(iSearchText == undefined){
						    	$.bbq.removeState( 'iSearchText' );
						    }
						   
						    $.bbq.pushState( state );
						    hashValue = location.hash;
						    
						    var aTrs = categoryListTbl.fnGetNodes();
			
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
 * Fetch editable record information and file in form
 * @author blal
 */
function callToEdit(){
	//get id from editable row
	var id =  $(this).children('p.colorAsLink').attr('editId');
	window.location  =    HOST_PATH + 'admin/category/editcategory/id/' +  id;


}

/**
 * change status of categories
 * @author blal
 */
function changeStatus(id,obj,status){
	 
	     addOverLay();
		 $(obj).addClass("btn-primary").siblings().removeClass("btn-primary");
		 $.ajax({
				type : "POST",
				url : HOST_PATH+"admin/category/categorystatus",
				data : "id="+id+"&status="+status
			}).done(function(msg) {
				removeOverLay();	
		}); 
	 
}
/**
 * used to remove highlighted bordes
 * @param el  current element
 * @author blal
 */
function resetBorders(el)
{
	$(el).each(function(i,o){
	 $(o).parent('div')
		.removeClass("error success")
		.prev("div").removeClass('focus error success') ;
	
	});
}


/**
 * Function call when user click on shop search button 
 * or press enter 
 * @author kraj
 */
function searchByCategory()
{
	
	var searchArt = $("#SearchCategory").val();
	if(searchArt=='' || searchArt==null)
	{
		searchArt = undefined;
	}
	getCategoryList(searchArt,0,0,'asc');
}

