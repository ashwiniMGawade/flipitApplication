/**
a * executes when document is loaded 
 * @author pkaur4 
 */
$(document).ready(function(){
	
	var iStart = $.bbq.getState( 'iStart' , true ) || 0;
	var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 1;
	var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'ASC';
	//call to keyword list function while loading
	if($("table#RedirectListTbl").length) {
		 
		 getRedirectList(iStart,iSortCol,iSortDir);
	}
	$('form#addRedirectForm').submit(function(){
		
		saveRedirect();
	});	
	//call to validation function while add redirect
	if($('form#addRedirectForm').length){
		
		addRedirectValidation();
	}
	$('form#editRedirectForm').submit(function(){
		
			saveEditRedirect();
	});
	//call to validation function while editing redirect
	if($('form#editRedirectForm').length) {
	
		editRedirectValidation();
	}
	
	$(window).bind( 'hashchange', function(e) {
		if(hashValue != location.hash && click == false){
			RedirectListTbl.fnCustomRedraw();
		}
	});

	if($.validatoo != undefined) {
	
		$.validator.setDefaults({
			onkeyup : false,
			onfocusout : function(element) {
				$(element).valid();
			}
		
		});
	}

});
function saveRedirect() {
	
	if($("form#addRedirectForm").valid()) {   
		
		$('#createRedirect').attr('disabled' ,"disabled");
		return true;
		
	}else {
		
		 return false;
	}
}
function saveEditRedirect() {
	
	if($("form#editRedirectForm").valid()) {
		
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
		orignalurl : "Valid Url",
		redirectto : "Valid Url"
};

/**
 * focusRules oject contain all the messages that are visible on focus of an
 * elelement
 * structure to define a message for element : key is to be element name and Value is
 * message
 */
var focusRules = {
		orignalurl : "Enter valid url",
		redirectto : "Enter valid url"
};
var validatorForNewRedirect = null ;
/**
 * check redirect validation while adding
 * @author kraj
 */
function addRedirectValidation(){
	validatorForNewRedirect = $("form#addRedirectForm").validate({    
				errorClass : 'error',
				validClass : 'success',
				ignore: ":hidden",
				errorElement : 'span',
				errorPlacement : function(error, element) {
					element.parent("div").prev("div")
							.html(error);
				},
                rules : {
                	orignalurl : {
				    	required : true,
				    	 regex : /((?:^(http|ftp|gopher|telnet|news):\/\/))([_a-zA-Z\d\-\W]+(\.[_a-zA-Z\d\-\W]+)+)(([_a-zA-Z\d\-\\\.\/\W]+[_a-zA-Z\d\-\\\/\W])+)*$|^([_a-zA-Z\d\-\W]+(\.[_a-zA-Z\d\-\W]+)+)(([_a-zA-Z\d\-\\\.\/\W]+[_a-zA-Z\d\-\\\/\W])+)*$/
				    	},
						redirectto : {
				    	required : true,
				    	 regex : /((?:^(http|ftp|gopher|telnet|news):\/\/))([_a-zA-Z\d\-\W]+(\.[_a-zA-Z\d\-\W]+)+)(([_a-zA-Z\d\-\\\.\/\W]+[_a-zA-Z\d\-\\\/\W])+)*$|^([_a-zA-Z\d\-\W]+(\.[_a-zA-Z\d\-\W]+)+)(([_a-zA-Z\d\-\\\.\/\W]+[_a-zA-Z\d\-\\\/\W])+)*$/
				    	}
				},
				messages : {
					orignalurl : {
			    	     required : "Please enter valid url",
			    	     regex : "Invalid url"
			            },
			            redirectto : {
			    	     required : "Please enter valid url",
			    	     regex : "Invalid url"
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
var RedirectListTbl = null;
var hashValue = "";
var click = false;
/**
 * get excluded redirect listing 
 * @author kraj
 */
function getRedirectList(iStart,iSortCol,iSortDir){
	
	addOverLay();
	$('#RedirectListTbl').addClass('widthTB');
	RedirectListTbl = $("table#RedirectListTbl")
	.dataTable({
				"bLengthChange" : false,
				"bInfo" : true,
				"bFilter" : true,
				"bDestroy" : true,
				"bProcessing" : false,
				"bServerSide" : true,
				"iDisplayStart" : iStart,
				"iDisplayLength" : 100,
				"bDeferRender": true,
				"oLanguage": {
				      "sInfo": "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
				},
				"aaSorting": [[ iSortCol , iSortDir ]],
				"sPaginationType" : "bootstrap",
				"sAjaxSource" : HOST_PATH+"admin/redirect/redirectlist",
				"aoColumns" : [
                         {
	                       "fnRender" : function(obj){
		                    var id = null;
		                    return id = obj.aData.id;
		                    },
	                       "bSortable" : false,
	                       "bVisible": false ,
	                        "sType": 'numeric'
	                     },{
	                           "fnRender" : function(obj){
	                        	 var redirectto = ''; 
	                        	 if(obj.aData.redirectto != null){  
	                        		 redirectto = '<p editId="' + obj.aData.id + '" class = "colorAsLink word-wrap-without-margin-searchbar">'
	                        		 + '<a href="javascript:void(0);">' + obj.aData.orignalurl+'</a></p>';
	                        	 }
								 return redirectto;
							},
						     
							 "bSortable" : true
							 
						}, {
	                           "fnRender" : function(obj){
	                        	 var redirectto = ''; 
	                        	 if(obj.aData.redirectto != null){  
	                        		 redirectto = '<p editId="' + obj.aData.id + '" class = "colorAsLink word-wrap-without-margin-searchbar">'
	                        		 + '<a href="javascript:void(0);">' + obj.aData.redirectto+'</a></p>';
	                        	 }
								 return redirectto;
							},
						     
							 "bSortable" : true
							 
						},{
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
						},{
							"fnRender" : function(obj) {
								
								var html = "<a href='javascript:void(0);'" + "onclick='deleteRedirect( " +
											obj.aData.id  + ")'>" + __('Delete') + "</a>";

								return html;
							},
							
							"bSortable" : false

						} ],
				"fnPreDrawCallback": function( oSettings ) {
					$('#RedirectListTbl').css('opacity',0.5);
				 },		
				"fnDrawCallback" : function(obj) {
					$('#RedirectListTbl').css('opacity',1);
					
					
					var state = {};
					$("#RedirectListTbl").find('tr').find('td:lt(3)').click(function () {
							var eId = $(this).parent('tr').find('p').attr('editid');
							state[ 'eId' ] = eId ;
							$.bbq.pushState( state );
							click = true;
							window.location.href = HOST_PATH + "admin/redirect/editredirect/id/" + eId+ "?iStart="+
							obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
							obj.aaSorting[0][1]+"&eId="+eId;
					});
					
				    // Set the state!
				    state[ 'iStart' ] = obj._iDisplayStart ;
				    state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
				    state[ 'iSortDir' ] = obj.aaSorting[0][1] ;
				    
				    
				    $.bbq.pushState( state );
				    hashValue = location.hash;
				    
				    var aTrs = RedirectListTbl.fnGetNodes();
	
					for ( var i=0 ; i<aTrs.length ; i++ ) {
						
						$editId = $(aTrs[i]).find('p').attr('editid');
						
						if ( $editId == $.bbq.getState( 'eId' , true ) ){
							
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
					$('td.dataTables_empty').html('No record found !');
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
 * check redirect validation while edit
 * @author kraj
 */
function editRedirectValidation(){
	validatorForEditKeyword = $("form#editRedirectForm").validate( {    
				errorClass : 'error',
				validClass : 'success',
				ignore: ":hidden",
				errorElement : 'span',
				errorPlacement : function(error, element) {
					element.parent("div").prev("div")
							.html(error);
				},
                rules : {
                	orignalurl : {
						 required : true,
						regex  : /((?:^(http|ftp|gopher|telnet|news):\/\/))([_a-zA-Z\d\-\W]+(\.[_a-zA-Z\d\-\W]+)+)(([_a-zA-Z\d\-\\\.\/\W]+[_a-zA-Z\d\-\\\/\W])+)*$|^([_a-zA-Z\d\-\W]+(\.[_a-zA-Z\d\-\W]+)+)(([_a-zA-Z\d\-\\\.\/\W]+[_a-zA-Z\d\-\\\/\W])+)*$/
						},
					redirectto : {
				    	required : true,
				    	regex  : /((?:^(http|ftp|gopher|telnet|news):\/\/))([_a-zA-Z\d\-\W]+(\.[_a-zA-Z\d\-\W]+)+)(([_a-zA-Z\d\-\\\.\/\W]+[_a-zA-Z\d\-\\\/\W])+)*$|^([_a-zA-Z\d\-\W]+(\.[_a-zA-Z\d\-\W]+)+)(([_a-zA-Z\d\-\\\.\/\W]+[_a-zA-Z\d\-\\\/\W])+)*$/
				       }
				},
				
				messages : {
					orignalurl : {
					      required : "Please enter url",
					      regex : "Invalid url"
					    },
				   redirectTo : {
			    	required : "Please enter valid url",
			    	regex : "Invalid url"
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
 * Fetch edited redirect and file in form
 * @author kraj
 */
function editRedirect(){
	
	var id = $(this).parents('tr').children('td').children('p.colorAsLink').attr('editId'); 
	window.location =HOST_PATH + 'admin/redirect/editredirect/id/' + id;
}
function deleteRedirectByEdit(e) {
	
	var id =  $('input#id').val();
	//call delete function to delete keyword while editing keyword
	deleteRedirect(id);
}

/**
 * delete redirect from database
 * @author kraj
 */
function deleteRedirect(id){
	
	bootbox.confirm("Are you sure you want to delete this redirect permanently?",'No','Yes',function(r){
	if (!r){
		
	return false;
	
	}else{
		
		addOverLay();
		$.ajax({
			type : "POST",
			url : HOST_PATH+"admin/redirect/deleteredirect",
			data : "id="+id
		}).done(function(msg){
			window.location = HOST_PATH + 'admin/redirect';
		}); 
	 }
	
 });
}

