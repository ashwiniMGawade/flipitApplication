/**
 * widgetList.js 1.0
 * @author mkaur
 */

$(document).ready(init);
/**
 * ready function starts containing ck editor and form validation
 */

function init() {

	$("#searchWidget").select2({
        placeholder: __("Search widget"),
        minimumInputLength: 1,
        ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
            url: HOST_PATH + "admin/widget/searchkey",
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
            $("#searchWidget").val(data);
            return data; 
        },
    });
    $('.select2-search-choice-close').click(function(){
    	$('input#searchWidget').val('');
        getWidgetList(undefined,0,1,'asc');
    });
	
	var iSearchText = $.bbq.getState( 'iSearchText' , true ) || undefined;
	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 1;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'ASC';
	getWidgetList(iSearchText,iStart,iSortCol,iSortDir);
	
	
	$('#searchButton').click(searchByWidget);
	
	
	$('form#searchform').submit(function() {
		
		return false;
	});

	$("input#searchWidget").keypress(function(e)
	{
		// if the key pressed is the enter key
		  if (e.which == 13)
		  {
			  getWidgetList($(this).val(),0,1,'asc');
		      
		  }
	});

	$(window).bind( 'hashchange', function(e) {
		if(hashValue != location.hash && click == false){
			widgetListtable.fnCustomRedraw();
		}
	});
}




/**
 * addNewWidget function save the widget in database.
 * @param e
 */
function addNewWidget(e) {
	e.preventDefault();
	CKEDITOR.instances.content.updateElement();
		var data = $('#createWidget').serialize(true);
	    $('#textValue').val(data);
	}


/**
 * changeStatus action chages the online offline status of widget.
 * @param id
 * @param status
 */
function changeStatus(id,obj,status) {
	addOverLay();
	$(obj).addClass("btn-primary").siblings().removeClass("btn-primary");
	$.ajax({
		url : HOST_PATH + "admin/widget/onlinestatus",
		method : "post",
		data : {
			'id' : id,
			'state' : status
		},
		dataType : "json",
		type : "post",
		success : function(data) {
			if (data != null) {
				removeOverLay();
			} else {
				alert(__('Problem in your data'));
			}
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
 * getWidgetList shows list of widgets using bootstrap datatable grid.
 * @author mkaur
 */
var widgetListtable = null ;
var hashValue = "";
var click = false;
function getWidgetList(iSearchText,iStart,iSortCol,iSortDir){
	$('#widgetListtable').addClass('widthTB');
	
	$("ul.ui-autocomplete").css('display','none');
		addOverLay();

	widgetListtable = $("#widgetListtable").dataTable(
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
						tag ="<p editId='" + obj.aData.id + "' class='editId word-wrap-without-margin-widget'>" + ucfirst(obj.aData.title)+ "</p>";
						return tag;
						 
					},
					"bSortable" : true
	               },
	               {
						"fnRender" : function(obj) {
							var tag = "";
	 
							if(obj.aData.content){
								tag = "<a editId='" + obj.aData.id + "' href='javascript:void(0);'>" +"Yes" + "</a>";
							}
							else{
								tag = "<a editId='" + obj.aData.id + "' href='javascript:void(0);'>"+"No"+"</a>";
							}
							return tag;
							 
						},
						"bSearchable" : false,
						"bSortable" : true
		               },
				{
					"fnRender" : function(obj) {
					var onLine = "btn-primary";
					var	offLine = "";
					if(obj.aData.status == '0'){
						var	onLine = '';
						var	offLine = 'btn-primary'	;
					}	
						var html = "<div editId='" + obj.aData.id + "' class='btn-group'data-toggle='buttons-checkbox'style='padding-bottom:16px;margin-top:0px;'>"
								+ "<button class='btn "+ onLine +"' onClick='changeStatus("+ obj.aData.id+",this,\"online\")'>"+__('Yes')+"</button>"
								+ "<button class='btn "+ offLine +"'onClick='changeStatus("+ obj.aData.id+",this,\"offline\")'>"+__('No')+"</button>"
                                + "</div>";
                        
						return html;
					},
					"bSortable" : false
				}
				],
				"fnPreDrawCallback": function( oSettings ) {
					$('#widgetListtable').css('opacity',0.5);
				 },		
				"fnDrawCallback" : function(obj) {
					$('#widgetListtable').css('opacity',1);
						
						
						var state = {};
						state[ 'iStart' ] = obj._iDisplayStart ;
						state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
						state[ 'iSortDir' ] = obj.aaSorting[0][1] ;
						state[ 'iSearchText' ] = iSearchText;
						
						$("#widgetListtable").find('tr').find('td:lt(2)').click(function (e) {
							
							var el = e.target  ? e.target :  e.srcElement ;
							
							if(el.tagName != "BUTTON")
							{
								var eId = $(this).parent('tr').find('p, a').attr('editid');
								$('p', $(this) )
								state[ 'eId' ] = eId ;
								$.bbq.pushState( state );
								click = true;
								window.location.href = HOST_PATH + "admin/widget/editwidget/id/" + eId+ "?iStart="+
								obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
								obj.aaSorting[0][1]+"&iSearchText="+iSearchText+"&eId="+eId
							}
						});
						
					    // Set the state!
					    $("#SearchWidget").val(iSearchText);
					    if(iSearchText == undefined){
					    	$.bbq.removeState( 'iSearchText' );
					    }
					   
					    $.bbq.pushState( state );
					    hashValue = location.hash;
					    
					    var aTrs = widgetListtable.fnGetNodes();
		
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



/**
 * Function call when user click on shop search button 
 * or press enter 
 * @author kraj
 */
function searchByWidget()
{
	
	var searchArt = $("#searchWidget").val();
	if(searchArt=='' || searchArt==null)
	{
		searchArt = undefined;
	}
	getWidgetList(searchArt,0,0,'asc');
}



