/**
a * executes when document is loaded 
 * @author pkaur4 
 */
$(document).ready(function(){
	

	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 1;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'ASC';
	
	var CKVal =  $('input#checkBoxVal').val();
	if(CKVal==0 || CKVal=='0')
		{
		 $("#addStore").hide();
		 $("#listShop").hide();
		 $('div#addButton').hide();
		 
		} else {
			
			 $("#addStore").show();
			 $("#listShop").show();
			 $('div#addButton').show();
		}
	//Auto complete for search shop
	if($("#connectTo").length){
    
	$("#connectTo").autocomplete({
		minLength: 1,
        source: function( request, response){
        	
        	$.ajax({
        		url : HOST_PATH + "admin/searchbar/searchshops/keyword/" + $('#connectTo').val()+ "/selectedShop/" + $('input#selectedShopForSearchbar').val(),

     			dataType : "json",
     			type : "post",
     			success : function(data) {
     				if(data!=null){
     					
     				 response(data);
     				}
     			},
     			error: function(message) {
     				
     	            // pass an empty array to close the menu if it was initially opened
     	            response([]);
     	        }
   		 });
        },
        select: function( event, ui ) {
        	$('input#selectedShopId').val(ui.item.id);
        	console.log(ui.item.id);

        }
    });
	}
	
	//call to keyword list function while loading
    if($("table#keywordListTbl").length)
	{
		 getKeywordList(iStart,iSortCol,iSortDir);
	}
	
	 $('form#addKeywordForm').submit(function(){
		 
	   saveKeyword();
	 });	
	
	//call to validation function while add keyword
	if($('form#addKeywordForm').length){
		
		addKeywordValidation();
	}
	
	$('form#editKeywordForm').submit(function(){
		
			saveEditKeyword();
		   
		});
	
	
	

	$(window).bind( 'hashchange', function(e) {
		if(hashValue != location.hash && click == false){
			keywordListTbl.fnCustomRedraw();
		}
	});
	
	
	//call to validation function while editing keyword
	if($('form#editKeywordForm').length)
		{
			editKeywordValidation();
		   
		
		}
});
/*$.validator.setDefaults({
	onkeyup : false,
	onfocusout : function(element) {
		$(element).valid();
	}
});*/
function saveKeyword()
{
	if($("form#addKeywordForm").valid())
	{   
		$('#createKeyword').attr('disabled' ,"disabled");
		return true;
		
	}else {
		
		 return false;
	}
	
}

function saveEditKeyword()
{
	if($("form#editKeywordForm").valid())
	{
		return true;
		
	}else {
		
		return false;
	}
	
}


/**
 * validRules oject contain all the messages that are visible when an elment
 * value is valid
 * structure to define a message for element: key is to be element name and Value is
 * message
 */
var validRules = {
		keyword : __("Keyword looks great"),
		redirectTo : __("Valid Url")
};

/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 * structure to define a message for element : key is to be element name and Value is
 * message
 */
var focusRules = {
		keyword : __("Enter keyword"),
		redirectTo : __("Enter valid url")
};
var validatorForNewKeyword = null ;
/**
 * check keyword validation while adding
 * @author blal
 */
function addKeywordValidation(){
	validatorForNewKeyword = $("form#addKeywordForm").validate(
			{    
				errorClass : 'error',
				validClass : 'success',
				ignore: ":hidden",
				errorElement : 'span',
				errorPlacement : function(error, element) {
					element.parent("div").prev("div")
							.html(error);
				},
                rules : {
					keyword : {
						 required : true,
						 minlength : 2
						},
						redirectTo : {
				    	 required : true,
				    	 regex : /^(?:http(|s)\:\/\/)?(?:[a-z0-9-]+\.)*(?:flipit\.com|kortingscode\.nl)(?:\/.*)?$/
				    	}
				},
				messages : {
					keyword : {
					      required : __("Please enter keyword"),
					      minlength : __("Please enter at least 2 characters")
					    },
					    redirectTo : {
			    	     required : __("Please enter valid url"),
			    	     regex : __("Invalid url")
			            }
			    },
				onfocusin : function(element) {
					// display hint messages when an element got focus 
					if (!$(element).parent('div').prev("div")
							.hasClass('success')) {
						this.showLabel(element, focusRules[element.name]);
							
							$(element).parent('div').removeClass(
											this.settings.errorClass)
									.removeClass(
											this.settings.validClass)
									.prev("div")
									.addClass('focus')
									.removeClass(
											this.settings.errorClass)
									.removeClass(
											this.settings.validClass);
			    	 
					}
				},
				highlight : function(element,
						errorClass, validClass) {
					// highlight borders in case of error
					$(element).parent('div')
							.removeClass(validClass)
							.addClass(errorClass).prev(
									"div").removeClass(
									validClass)
							.addClass(errorClass);
					

				},
				unhighlight : function(element,
						errorClass, validClass) {
					
						$(element).parent('div')
								.removeClass(errorClass)
								.addClass(validClass).prev(
										"div").addClass(
										validClass)
								.removeClass(errorClass);
						$(
								'span.help-inline',
								$(element).parent('div')
										.prev('div')).text(
								validRules[element.name]);
				
				},

			});
	
}
var keywordListTbl = null;
var hashValue = "";
var click = false;
/**
 * get excluded keyword listing 
 * @author blal
 */
function getKeywordList(iStart,iSortCol,iSortDir){
	
	addOverLay();
	$('#keywordListTbl').addClass('widthTB');
	keywordListTbl = $("table#keywordListTbl")
	.dataTable(
			{
			
				"bLengthChange" : false,
				"bInfo" : true,
				"bFilter" : true,
				"bDestroy" : true,
				"bProcessing" : false,
				"bServerSide" : true,
				"iDisplayStart" : iStart,
				"iDisplayLength" : 100,
				"oLanguage": {
				      "sInfo": "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
				},
				"bDeferRender": true,
				"aaSorting": [[ iSortCol , iSortDir ]],
				"sPaginationType" : "bootstrap",
				"sAjaxSource" : HOST_PATH+"admin/searchbar/keywordlist",
				"aoColumns" : [
                         {
	                       "fnRender" : function(obj){
		                    var id = null;
		                    return id = obj.aData.id;
		                    },
	                       "bSortable" : false,
	                       "bVisible": false ,
	                        "sType": 'numeric'
	                     },    
				         {
                           "fnRender" : function(obj){
                        	 var keyword = ''; 
                        	 if(obj.aData.keyword != null){  
							 keyword = '<p editId="' + obj.aData.id + '" class = "colorAsLink word-wrap-without-margin-searchbar"><a href="javascript:void(0);">' + obj.aData.keyword+'</a></p>';
                        	 }
							 return keyword;
						},
						     
							 "bSortable" : true
						},
						{
						   "fnRender" : function(obj){
							   var action = '';
							   if(obj.aData.action == 0){
							   action = 'Redirect';
								}else{
							  action = 'Connect';	
								}
							   return "<a href='javascript:void(0);'>" + action + "</a>";
						    },
							
                            
							"bSortable" : true
						},
						{
							"fnRender" : function(obj) {
								var tag = '';
								if(obj.aData.created_at!=undefined && obj.aData.created_at!=''){
								var dat = obj.aData.created_at.date;
								tag = dat.split("-");
								tag2 = tag[2];
								var da = tag2.split(" ");
								return "<a href='javascript:void(0)'>" +  (da[0]+'-'+tag[1]+'-'+tag[0]) + "</a>";
								}
								return "<a href='javascript:void(0)'></a>";
								 
							},
							
							  
                              "bSortable" : true
						},
						{
							"fnRender" : function(obj) {
								
								var html = "<a href='javascript:void(0);'" +
											"onclick='deleteKeywords( " +
											obj.aData.id  + ")'>" + __('Delete') + "</a>";

								return html;
							},
							
							"bSortable" : false

						} ],
				"fnPreDrawCallback": function( oSettings ) {
					$('#keywordListTbl').css('opacity',0.5);
				 },		
				"fnDrawCallback" : function(obj) {
					$('#keywordListTbl').css('opacity',1);
					
					
					var state = {};
					state[ 'iStart' ] = obj._iDisplayStart ;
					state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
					state[ 'iSortDir' ] = obj.aaSorting[0][1] ;

					$("#keywordListTbl").find('tr').find('td:lt(3)').click(function () {
							var eId = $(this).parent('tr').find('p').attr('editid');
							state[ 'eId' ] = eId ;
							click = true;
							$.bbq.pushState( state );
							window.location.href = HOST_PATH + "admin/searchbar/editkeyword/id/" + eId+ "?iStart="+
							obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
							obj.aaSorting[0][1]+"&eId="+eId;
					});
					
				    // Set the state!
				    
				    
				    $.bbq.pushState( state );
				    hashValue = location.hash;
				    
				    var aTrs = keywordListTbl.fnGetNodes();
	
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
					$('td.dataTables_empty').html(__('No record found !'));
					$('td.dataTables_empty').unbind('click');
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
var validatorForEditKeyword = null ;
/**
 * check keyword validation while edit
 * @author blal
 */
function editKeywordValidation(){
	validatorForEditKeyword = $("form#editKeywordForm").validate(
			{    
				errorClass : 'error',
				validClass : 'success',
				ignore: ":hidden",
				errorElement : 'span',
				errorPlacement : function(error, element) {
					element.parent("div").prev("div")
							.html(error);
				},
                rules : {
					keyword : {
						 required : true,
						 minlength : 2
						},
					redirectTo : {
				    	required : true,
				    	regex  : /^(?:http(|s)\:\/\/)?(?:[a-z0-9-]+\.)*(?:flipit\.com|kortingscode\.nl)(?:\/.*)?$/
				       }
				},
				
				messages : {
					keyword : {
					      required : __("Please enter keyword"),
					      minlength : __("Please enter at least 2 characters")
					    },
				   redirectTo : {
			    	required : __("Please enter valid url"),
			    	regex : __("Invalid url")
			            }
				},
				onfocusin : function(element) {
					
					// display hint messages when an element got focus 
					if (!$(element).parent('div').prev("div")
							.hasClass('success')) {
						this.showLabel(element, focusRules[element.name]);
							
							$(element).parent('div').removeClass(
											this.settings.errorClass)
									.removeClass(
											this.settings.validClass)
									.prev("div")
									.addClass('focus')
									.removeClass(
											this.settings.errorClass)
									.removeClass(
											this.settings.validClass);
			    	 
					} else {
						
						
					}
				},
				highlight : function(element,
						errorClass, validClass) {
					// highlight borders in case of error
					$(element).parent('div')
							.removeClass(validClass)
							.addClass(errorClass).prev(
									"div").removeClass(
									validClass)
							.addClass(errorClass);
					

				},
				unhighlight : function(element,
						errorClass, validClass) {
					
						$(element).parent('div')
								.removeClass(errorClass)
								.addClass(validClass).prev(
										"div").addClass(
										validClass)
								.removeClass(errorClass);
						$(
								'span.help-inline',
								$(element).parent('div')
										.prev('div')).text(
								validRules[element.name]);
				
				},

			});
	
}



/**
 * Fetch edited keyword and file in form
 * @author blal
 */
function editKeyword(){
	
	  var id = $(this).parents('tr').children('td').children('p.colorAsLink').attr('editId'); 
	  //var id = $(this).children('p.colorAsLink').attr('editId');
	  window.location =HOST_PATH + 'admin/searchbar/editkeyword/id/' + id;
}

function deleteKeywordByEdit(e)
{
	var id =  $('input#id').val();
	//alert(id);
	//call delete function to delete keyword while editing keyword
	deleteKeywords(id);
}

/**
 * delete keyword from database
 * @author blal
 */
function deleteKeywords(id){
	//alert(id);
	bootbox.confirm(__("Are you sure you want to delete this excluded keyword permanently?"),__('No'),__('Yes'),function(r){
	if (!r){
	return false;
	}else{
		addOverLay();
		$.ajax({
			type : "POST",
			url : HOST_PATH+"admin/searchbar/deletekeywords",
			data : "id="+id
		}).done(function(msg){
			window.location = HOST_PATH + 'admin/searchbar';
		}); 
	 }
 });
}


/**
 * change the action of searchbar
 * @param e event from which it is called
 * @author blal
 */



function changeAction(e,type)
{
	//console.log($(e.target).attr('name'));
	var id =   $(e).attr("id") ;
	if(id=='btnredirect')
		{
			$('#btnredirect').addClass('btn-primary');
			$('#btnconnect').removeClass('btn-primary');
			
		} else {
			
			$('#btnconnect').addClass('btn-primary');
			$('#btnredirect').removeClass('btn-primary');
			$('#redirectTo').parent('div').removeClass('error')
			.removeClass('success')
			.prev("div")
			.addClass('focus')
			.removeClass('error')
			.removeClass('error');
			$('span[for=redirectTo]').remove();
		}
	//$('#'+id).addClass('btn-primary').siblings('button').removeClass('btn-primary') ;
	$("input#actionType").val(type);
	if (type == '1' ||  type == 1)
	{    
		 $("input#redirectTo").attr('disabled','disabled');
		 $("input#connectTo").removeAttr('disabled');
		 $("#addStore").show();
		 $("#listShop").show();
		 $('div#addButton').show();
		
	}
	else
	{   
		$("input#connectTo").attr('disabled','disabled');
		$("input#redirectTo").removeAttr('disabled');
		$("#addStore").hide();
		$("#listShop").hide();
		$('div#addButton').hide();
		 
	}
	 $('input#checkBoxVal').val(type);
}


function addShop(){
	
	
	if($('input#connectTo').val()==''){
			
		bootbox.alert(__('Please select a shop'));
		return false;
	} else {
		
		checkDuplicate();
	}
	 //slectEvent();
	removeOverLay();

 }
function checkDuplicate()
{
	$.ajax({
		url : HOST_PATH + "admin/searchbar/checkstoreexist/id/" + $('input#selectedShopId').val(),
			method : "post",
			dataType : "json",
			type : "post",
			success : function(json) {
				
				 switch(json) {
				 
				 	case 0:
				 		//store not exist in database
				 		bootbox.alert('This store does not exist');
				 		break;
				 	default:
				 		//find store in  database
				 		//appen li
				 		reoderElements(); 
				 			    var path = HOST_PATH+'public/images/'+'kc-search-cross.png';
				 				//var ltVal =  $('input#selectedShopId').val();
				 				var li = '<li id="' + json + '" class="search-add" type="'+ $('input#connectTo').val() +'">';
				 				li+='<a herf="javascript:void(0);" style="cursor:pointer" onClick="deleteShop(' + json + ')">';
				 				li+='<img src="' + path + '"/></a>&nbsp;'+ $('input#connectTo').val()	+'</li>';
				 				$('#shopListul-li').append(li);
				 				$('input#connectTo').val('');
				 	}
			}
	});

}

function reoderElements() {
	var pr   =  $('#selectedShopForSearchbar').val();
	var selectedShops = '';
	if(pr=='')
		{
		pr = $('input#selectedShopId').val();
		
		}else {
			
			pr+="," +  $('input#selectedShopId').val();
		}
	$('input#selectedShopId').val('');
	$('#selectedShopForSearchbar').val(pr);
}

function deleteShop(id) {
	
	//var id = $('ul#shopListul-li li.selected').attr('id');
	if(id !=null && id!='' && id!=undefined){
		regenerateObj(id);
		$('ul#shopListul-li  li#' + id).remove();
	} 
}
function regenerateObj(id)
{
	
	var pr = $("#selectedShopForSearchbar").val().split(',');
	var j = pr.length-1;
	var newValOfPr = '';
	for (var i in pr) {
		 if (pr[i] == id) { 
		 } else {
			if (i == j) { 
				newValOfPr += pr[i];
			} else {
				newValOfPr += pr[i] + ",";
			} 
		 }	
	}
	$("#selectedShopForSearchbar").val(newValOfPr);
}

