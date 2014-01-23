/**
 * mainmenu.js
 * @author mkaur
 */
var type = null;
var validRules = {
		label : "",
		url : "",
		position:""
	};
	var focusRules = {
			label : "",
			url : "",
			position:""
	};

$(document).ready(function(){
    addOverLay();
	$('form#menuForm').submit(function(){
    	validateMenu();
    	if($("form#menuForm").valid()){
    		submitForm();
    	}
      return false;
	});
/**
 * Get menu id to show selected
 * @author mkaur
 */ 
 $.ajax({
	    url : HOST_PATH + "admin/menu/getmainid",
		method : "post",
		dataType : "json",
			success : function(data) {
			if (data != null) {
				 getMenu();
				 getrightMenus(data[0]['root_id']);
				 removeOverLay();
				 //liselected(data[0]['root_id']);
			} else {
				alert(__("Problem in your data"));
			}
		}
	});
 
/**
 * Upload file and validate access for valid file
 * @author mkaur
 */
 $('form#menuForm').fileupload();
	$('form#menuForm').fileupload(
			'option', {
				acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
				autoUpload : true, 
				downloadTemplate: null,
				url : HOST_PATH+"admin/menu/uploadimage/",
				dropZone : null,
				dataType: "json",
				maxFileSize: 2089999, // 2 MB
				uploadTemplate: function (o) {
					$('form#menuForm span#imageName').html(__('Uploading...'))
					.addClass('validating');
					
					if(o.files[0].error)
					{
						//bootbox.alert("Please upload valid file");
						$('form#menuForm span#imageName').html(__('Uploading...'))
						.addClass('validating') 
						.html(locale.fileupload.errors[o.files[0].error] || o.files[0].error)
						.removeClass('validating success')
						.addClass('error');
						//alert(locale.fileupload.errors[o.files[0].error] || o.files[0].error) ;
					}
					
					
					if(o.files[0].error)
					{
						$('form#menuForm span#imageName')
						.html(locale.fileupload.errors[o.files[0].error] || o.files[0].error)
						.removeClass('validating')
						.removeClass('success')
						.addClass('error') ;
						
					} 
					
					
				},
				done: function (e,data) {
					str = data.result.displayFileName ;
					if(str.length > 22){
						str = str.substr(0,22) ;
						str = str + ".." ;
					    str = "<abbr class='abbrNoFomatting' title='" +  data.result.displayFileName
					    	  + "'>" + str + "</abbr>" ; 
					}
					
					$('form#menuForm span#imageName').html(str)
					.removeClass('validating')
					.removeClass('error')
					.addClass('success') ;
					$('#hidimage').val(data.result.fileName);
					$('#hidimageorg').val(data.result.displayFileName);
				}

			}); 

});

/**
 * Disable  validation event on keyup and trigger on blur
 * @author mkaur
 */
/*$.validator.setDefaults({
	onkeyup : false,
	onfocusout : function(element) {
		$(element).valid();
	}

});*/

/**
 * Form validation used in maniMenu form
 * @author mkaur
 */	
		
function validateMenu(){
	
	validateNewMenu = $("form#menuForm")
		.validate({	
			errorClass : 'error',
			validClass : 'success',
			errorElement : 'span',
			ignore: ".ignore, :hidden",
			afterReset  : resetBorders,
			errorPlacement : function(error, element) {
				element.parent("div").prev("div")
						.html(error);
			},
		rules : {
			label : {
				required : true,
				//regex: /^[0-9a-zA-Z\s-]+$/ 
			},
			url:{
				required : false,
				//regex: /((?:^(http|ftp|gopher|telnet|news):\/\/))([_a-zA-Z\d\-]+(\.[_a-zA-Z\d\-]+)+)(([_a-zA-Z\d\-\\\.\/]+[_a-zA-Z\d\-\\\/])+)*$|^([_a-zA-Z\d\-]+(\.[_a-zA-Z\d\-]+)+)(([_a-zA-Z\d\-\\\.\/]+[_a-zA-Z\d\-\\\/])+)*$/
			},
			position:{
				required : false,
				regex:/^[1-9\s]+$/
			},
			
			},
		messages : {
			label : {
				required : "",
				regex: ""
			},
			/*url:{
				regex:""
			},*/
			sort:{
				regex:""
			}
		},
		onfocusin : function(element) {
			
			// display hint messages when an element got focus 
			if (!$(element).parent('div').prev("div")
					.hasClass('success')) {
				
	    		 var label = this.errorsFor(element);
	    		 if( $(label).attr('hasError')  )
	    	     {
	    			 if($( label ).attr('remote-validated') != "true")
	    			 	{
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
	    			 
	    	     } else {
	    	    	
	    	     /*if(element.value!==''){
	    	    	 this.showLabel(element, focusRules[element.name]);
	    	    		 $(element).parent('div')
						 .removeClass("error success")
	    	    			.prev("div").removeClass('focus error success') ;
	    	    		 $('span.help-inline', $(element).parent('div')
									.prev('div')).text(
						 validRules[element.name] ).hide();
	    	    	 }
	    	    	 else{*/
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
	    	    	// }
    	    	 }
			}
		},
	highlight : function(element,errorClass, validClass) {
			// highlight borders in case of error  
			$(element).parent('div')
			.removeClass(validClass)
			.addClass(errorClass).prev("div")
			.removeClass(validClass)
			.addClass(errorClass);
			$('span.help-inline', $(element).parent('div')
					.prev('div')).removeClass(validClass) ;
		
	},
	unhighlight : function(element,
			errorClass, validClass) {
			// check to display errors for ignored elements or not 
			var showError = false ;
			switch( element.nodeName.toLowerCase() ) {
			case 'select' :
				var val = $(element).val();
				
				if($($(element).children(':selected')).attr('default') == undefined)
				{
					showError = true ;
				} else
				{
					showError  = false;
				}
				break ; 
			case 'input':
				if ( this.checkable(element) ) {
					
					showError = this.getLength(element.value, element) > 0;
					
				} else if($.trim(element.value).length > 0) {
					
						showError =  true ;
						
					} else {
						
						showError = false ;
					}
						
				break ; 
			default:
				var val = $(element).val();
				showError =  $.trim(val).length > 0;
			}
			if(! showError ){
				// hide errors message and remove highlighted borders 
					$(
							'span.help-inline',
							$(element).parent('div')
							.prev('div')).hide();
					
						$(element).parent('div')
						.removeClass(errorClass)
						.removeClass(validClass)
						
						.prev("div")
						.removeClass(errorClass)
						.removeClass(validClass);
					    
			} else
			{
				if(element.type !== "file"){
					$(element).parent('div')
					.removeClass(errorClass)
					.addClass(validClass).prev(
							"div").addClass(
							validClass)
					.removeClass(errorClass);
					
					$('span.help-inline', $(element).parent('div')
									.prev('div')).text(
						 validRules[element.name] ).show();
				} else{
					$(element).parent('div')
					.removeClass(errorClass)
					.removeClass(validClass)
					
					.prev("div")
					.removeClass(errorClass)
					.removeClass(validClass) ;
				}
			}
		 
	},
		
		});
	}

/**
 * Disable  validation event on keyup and trigger on blur
 * @author mkaur
 */
$.validator.setDefaults({
	onkeyup : false,
	onfocusout : function(element) {
		$(element).valid();
	}

});
/**
 * reset the validation border of imput field
 * @param el
 * @author mkaur
 */
function resetBorders(el){
	$(el).each(function(i,o){
		
		$(o).parent('div')
		.removeClass("error").removeClass('success')
		.prev("div").removeClass('focus').removeClass('error').removeClass('success') ;
	
	});
}
function openModel(){
	$('#myModal').modal('show');	
}

function menuSelected(id){
	$('a#'+id).addClass('selected_nav');
	
}
/**
 * Open bootstrap Model popup with filled fields as per id 
 * @param id
 * @author mkaur
 */
function showModel(id,rootid,type){
	 $('form#menuForm :input').val("");
	 $('form#menuForm span#imageName').html('');
	 $('form#menuForm div.m-item-popup-btm a.menuDelete').remove();
	 removeBorders();
	switch(type){
	 case 'get':
	if(id!=0){
		addOverLay();
		$('input#hid').val(id);
		$.ajax({
			url : HOST_PATH + "admin/menu/getmainrecord/id/"+id,
			method : "post",
			dataType : "json",
				type : "post",
				success : function(data) {
					if (data != null) {
					removeOverLay();
					$('#submitButton').attr('value','edit');
					$('#imageid').val(data[0]['iconId']);
					$('input#roothid').val(data[0]['root_id']);
					$('input#label').val(data[0]['name']);
					$('input#url').val(data[0]['url']);
					$('input#position').val(data[0]['position']);
					
					if(data[0]['menuIcon']!=null){
					var str = data[0]['menuIcon']['name'];
						if(str.length > 22){
							str = str.substr(0,22) ;
							str = str + ".." ;
							 str = "<abbr class='abbrNoFomatting' title='" +  data[0]['menuIcon']['name']
					    	  + "'>" + str + "</abbr>" ;    
						}
						$('form#menuForm span#imageName').html(str);
					}
					$('form#menuForm div.m-item-popup-btm').append('<a href="javascript:void(0);" class="red menuDelete" onclick="deleteMenu('+data[0]['id']+','+data[0]['parentId']+','+data[0]['root_id']+')"><strong>'+__('DELETE')+'</strong></a>');
					$('#myModal').modal('show');
					/* $('html, body').animate({ 
					      scrollTop: $('#new_media_div').offset().top 
					  });*/
				} else {
					bootbox.alert(__("Problem in your data"));
				}
			}
		});
	}
	break;
	 case 'addRightli':
		 $('#submitButton').attr('value','add');
		 $('input#hid').val(id);
		 $('input#roothid').val(rootid);
		 $('#myModal').modal('show');
			break;
	default:
		$('#submitButton').attr('value','add');
		 $('input#hid').val('');
		$('#myModal').modal('show');
	}
}
/**
 * Hide bootstrap model
 * @returns {Boolean}
 * @author mkaur
 */
function hideModel(){
	$('#myModal').modal('hide');
	return false;
}

/**
 * submitForm function submit form through ajax in case of edit and add menu.
 * @author mkaur
 */
function submitForm(){
	var leftLength = $('div.m-beheer-lt li').length;
	if(leftLength > 0){
		$('div.m-beheer-lt li').empty();
	}
	
	var value = $('#submitButton').val();
	switch(value){
	 case 'edit':
		addOverLay();
		//$('form#menuForm').submit();
		 $.ajax({
				url : HOST_PATH + "admin/menu/editmainmenu/id/"+$('input#hid').val(),
				method : "post",
				data: $('form#menuForm').serialize(),		
				dataType : "json",
					type : "post",
					success : function(data) {
					if (data != null) {
						removeOverLay();
						$('#myModal').modal('hide');
						getMenu();
						getrightMenus(data.roothid);
						} else {
						alert(__("Problem in your data"));
					}
				}
			});
	break;	
	default:
		addOverLay();
		 
	$.ajax({
				url : HOST_PATH + "admin/menu/addmainmenu",
				method : "post",
				data: $('form#menuForm').serialize(),		
				dataType : "json",
					type : "post",
					success : function(data) {
					if (data != null) {
						removeOverLay();
						$('#myModal').modal('hide');
						
						if(data.hid==''){
								getMenu();
						}
						else{
							getMenu();
							getrightMenus(data);	
						}
					} else {
						alert(__("Problem in your data"));
					}
				}
			});
	}
}
/**
 * Get left menu record from database and append each li.
 * @author mkaur 
 */
function getMenu(){
	$('div.m-beheer-lt ul').empty();
		//if(m-beheer-lt)
	addOverLay();
	//$('form#menuForm').submit();
	 $.ajax({
			url : HOST_PATH + "admin/menu/listmainmenu",
			method : "post",
			//data: $('form#menuForm').serialize(),		
			dataType : "json",
				//type : "post",
				success : function(data) {
				//alert(data[0]['name']);
					var length = data.length;
					if (data != null) {
						removeOverLay();
						$('#myModal').modal('hide');
						var i = null;
						for(i=0;i<length;i++){
							str = data[i]['name'];
							if(str.length > 10){
								str = str.substr(0,10) ;
								str = str + ".." ;
							  }
							
						var li = "<li id='"+data[i]['id']+"'><a id='"+data[i]['id']+"' href='javascript:void(0)' onclick='getrightMenus("+data[i]['id']+")' title='"+data[i]['name']+"'>"+str+"</a><a class='edit' href='javascript:void(0);' onclick='showModel("+data[i]['id']+","+data[i]['root_id']+","+'"get"'+")' >"+"<img src='"+HOST_PATH+"public/images/back_end/edit-icon-txt.png'></a></li>";
						$('div.m-beheer-lt  ul').append(li);
						}
						$('div.m-beheer-lt  ul').append("<li id='lftli' class='background-none'><a class='ml15'><button onclick='showModel("+'""'+","+'""'+","+'"add"'+")' class='btn btn-primary' type='button'>+</button></a></li>");
					}
				 else {
					alert(__("Problem in your data"));
				}
			}
		});
}
/**
 * Get record according to id from the database and append right menus at different(1 and 2)levels.
 * @param id
 * @author mkaur
 */
function getrightMenus(id){
	addOverLay();
	$('div.m-beheer-rt div.m-beheer-rt-left').empty();
	$('div.m-beheer-rt div.m-beheer-rt-right').empty();
	$('div.m-beheer-rt div.add_row_outer').remove();
	//liselected(id);
	$.ajax({
			url : HOST_PATH + "admin/menu/getrtmainmenu/id/"+id,
			method : "post",
			//data: $('form#menuForm').serialize(),		
			//timeout: 20000,
			dataType : "json",
				//type : "post",
				success : function(data) {
					var length = data.length;
					if (data != null) {
						$('#myModal').modal('hide');
						var div = null;
						for(var i=0;i<length;i++){
							str = data[i]['name'];
							if(str.length > 42){
								str = str.substr(0,42) ;
								str = str + ".." ;
							  }
							var classdiv = "m-beheer-rt-left";
							if(i%2==0){
								classdiv = "m-beheer-rt-right";
							}
							if(data[i]['level']==0){}
							if(data[i]['level']==1){
							div = "<div class='m-beheer-rt-txt' id='"+data[i]['id']+"'><ul><li id='"+data[i]['id']+"' title='"+data[i]['name']+"'><font>"+str+"</font><a href='javascript:;' title='"+__("edit")+"' onclick='showModel("+data[i]['id']+","+data[i]['root_id']+","+'"get"'+")'><img src='"+HOST_PATH+"public/images/back_end/edit-icon-hdr.png'></a></li></ul></div>";
							$('div.m-beheer-rt div.'+classdiv).append(div);
							
						var last="<li id='lastliSub'><button type='button' class='btn btn-primary fr' title='"+__("Add")+"' onclick='showModel("+data[i]['id']+","+data[i]['root_id']+","+'"addRightli"'+");' data-loading-text='"+("Loading...")+"'>+</button></li>";
							$('div.m-beheer-rt div.'+classdiv+' div.m-beheer-rt-txt ul:last').append(last);
							}
							
							if(data[i]['level']==2){
								//alert(data[i]['level']);
								var lirt="<li id='"+data[i]['id']+"' title='"+data[i]['name']+"'><span class='fl'>"+str+"</span><a title='"+__("edit")+"' href='javascript:;' onclick='showModel("+data[i]['id']+","+data[i]['root_id']+","+'"get"'+")'><img src='"+HOST_PATH+"public/images/back_end/edit-icon-txt.png'></a></li>";
								$('div#'+data[i]['parentId']+'.m-beheer-rt-txt ul').append(lirt);	
								//$('div#'+id+'.m-beheer-rt-txt ul li#lastliSub')
							}
						}
						
						$('div.m-beheer-rt').append("<div class='add_row_outer'><div class='add_btn'><button data-loading-text='"+__("Loading...")+"' title='"+__("Add")+"' class='btn btn-primary' type='button' onclick='showModel("+id+","+'""'+","+'"addRightli"'+")'>+</button></div></div>");
						liselected(id);
						removeOverLay();
			}
			 else {
					alert(__("Problem in your data"));
				}
			}
		});
}

/**
 * listselected function used to show selected menus by remmoving and adding classes. 
 * @param id
 * @author mkaur
 */
function liselected(id){
	//alert(id);
	var liLength = $('div.m-beheer-lt ul li.sltd').length;
	if(liLength > 0){
		$('div.m-beheer-lt ul li').removeClass('sltd');
		$('div.m-beheer-lt ul li a').removeClass('sltd');
		$('div.m-beheer-lt ul li a.edit').html('<img src="'+HOST_PATH+'public/images/back_end/edit-icon-txt.png">');
	}
	$('div.m-beheer-lt ul li#'+id).addClass('sltd');
	$('div.m-beheer-lt ul li#'+id+' a').addClass('sltd');
	$('div.m-beheer-lt ul li#'+id+' a.edit').html('<img src="'+HOST_PATH+'public/images/back_end/edit-menu.png">');
}
/**
 * Remove colored borders of input by removing classes.
 * @author mkaur 
 */
function removeBorders(){
	$("div.mainpage-content-right").removeClass("error").removeClass('success')
	.prev("div").removeClass('focus').removeClass('error').removeClass('success') ;
}

/**
 * Remove root menu,parent menu and child menu from database.
 * @param id
 * @param parentId
 * @param rootId
 * @author mkaur
 */
function deleteMenu(id,parentId,rootId){
	$.ajax({
		url : HOST_PATH + "admin/menu/deletemainmenu/id/"+id+"/parentId/"+parentId+"/rootId/"+rootId,
		method : "post",
		dataType : "json",
			success : function(data) {
				if (data != null) {
					$('#myModal').modal('hide');
					
					getMenu();
					getrightMenus(rootId);
						
				}
			}
	});	
}


