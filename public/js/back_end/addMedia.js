

/**
 * addMedia.js
 * @author mkaur
 */

/**
 * rules for validations
 */
var validRules = {
	name : __("Title looks great."),
	alternateText : __("Alternate text looks great."),
	caption: __("Caption looks great."),
	description: __("Description looks great.")
};
var focusRules = {

	name : __("Please enter title."),
	alternateText : __("Please enter alternateText."),
	caption : __("Please enter caption"),
	description: __("Please enter description")
};
/*global $, window, document */
$(document).ready(init);
function init(){
	validateMedia();    
	$('#fileupload').fileupload();
    // Enable iframe cross-domain access via redirect option:
	$('#fileupload').fileupload({
	    autoUpload: true
	});
   
   
    
	$("form#newMediaForm").submit(function(){
		if($("form#newMediaForm").valid()){
			updateMedia();

		}else {
			return false;
		}

	});
    
/**
 * click function calls to open new Media form
 * @author mkaur
 */
    $('.show').live('click',function(){
    	addOverLay();
    	 var id = $(this).attr('rel');
    	 var addMediaForm = '<div id="post-body">'+'<div id="addMediadiv"><div class="mainpage-content">'+'<div class="mainpage-content-line mb10">'+'<div class="mainpage-content-left"><label><strong></strong></label></div>'+'<div class="mainpage-content-right"><img name="ImageName_'+id+'" border="0" id="Image_'+id+'">'+'<div class="upload"> <span id="uploadingMedia"></span></div></div></div>'+'<div class="mainpage-content-line"><div class="mainpage-content-left"><label><strong>'+__('Title')+':</strong></label></div><div class="mainpage-content-right"><div class="mainpage-content-right-inner-right-other"></div><div class="mainpage-content-right-inner-left-other">'+'<input type="text" class="span3" onchange="this.value=this.value.trim()" id="name_'+id+'" name="name[]" placeholder="' +__('Name..') +'"></div></div></div>'+'<div class="mainpage-content-line"><div class="mainpage-content-left"><label><strong>'+__('Alternate text')+':</strong></label></div><div class="mainpage-content-right"><div class="mainpage-content-right-inner-right-other"></div><div class="mainpage-content-right-inner-left-other"><input type="text" class="span3" placeholder="' +__('Alternate text..') +'" id="alternateText_'+id+'" onchange="this.value=this.value.trim()" maxlength="50" name="alternateText[]"></div></div></div>'+'<div class="mainpage-content-line mt10"><div class="mainpage-content-left"><label><strong>'+__('File URL')+':</strong></label></div>'+'<div class="mainpage-content-right"><div class="mainpage-content-right-inner-right-other"></div><div class="mainpage-content-right-inner-left-other"><input type="text" class="span3" placeholder="" id="fileUrl_'+id+'" name="fileUrl[]" readonly="readonly"></div></div></div><div class="mainpage-content-line mb10"><div class="mainpage-content-left"><label><strong>&nbsp;</strong></label></div>'+'<div class="mainpage-content-right"><input type="hidden" name="hid[]" id="hid_'+id+'" value="'+id+'"></div></div></div></div></div>';
    	 cancelValidationBorder();
    	 $.ajax({
			url:HOST_PATH+'admin/media/getmediadata/id/'+id,
			type:'post',
			dataType: 'json',
			success:function(json){
				//$("div#createMedia1").show();
				$('table#mediatable tr#'+id).after('<tr id='+id+'_b><td colspan="4">'+json.name+'</td><td></td><td></td><td><a href="#" onclick="show_hide('+id+');">'+__('Hide')+'</a></td></tr><tr id='+id+'_a><td colspan="7">'+addMediaForm+'</td></tr>');

				$('tr#'+id).hide();

				var publicPath = PUBLIC_PATH_LOCALE.replace('admin.', '') ;

				if(! /(http\:\/\/www.)/.test(publicPath))
				{
					  publicPath  = publicPath.replace('http://','http://www.')	;
				}
				var src = publicPath +'images/upload/media/thumb_L/'+json.fileurl;
			 
				$('img#Image_'+id).attr("src",src);
				$('input#name_'+id).val(json.name);
				$('input#alternateText_'+id).val(json.alternateText);
				$('input#caption_'+id).val(json.caption);
				
				
				$('input#fileUrl_'+id).val(publicPath +'images/upload/media/'+json.fileurl);
				$('#description_'+id).val(json.description);
				$('#last').show();
				removeOverLay();
			}
			});
	});
}

/**
 * Form validation used in Media form
 */	
		
function validateMedia(){
	
	validateNewMedia = $("form#newMediaForm")
		.validate({	
		errorClass : 'error',
		validClass : 'success',
		errorElement : 'span',
		afterReset  : resetBorders,
		ignore : false,
		errorPlacement : function(error,element) {
				element.parent("div").prev("div").html(error);
					
		},
		rules : {
			name : {required : true,
					minlength : 2
			},
			alternateText:{
				required : false,
				minlength : 2
			},
			caption:{
				required : false,
				minlength : 2
			},
			description:{
				required : false,
				minlength : 2
			},
			},
		messages : {
			name : {
				required : __("Please enter title"),
				minlength : __("Please enter atleast 2 characters")
			},
		alternateText : {
			required : __("Please enter alternate text"),
			minlength : __("Please enter atleast 2 characters")
			},
		caption : {
			required : __("Please enter caption"),
			minlength : __("Please enter atleast 2 characters")
	    },
	    description : {
			required : __("Please enter description"),
			minlength : __("Please enter atleast 2 characters")
	    },
		},
		onfocusin : function(element) {
			if (!$(element).parent('div').hasClass('success')) {
				if(element.value==""){
				this.showLabel(element,focusRules[element.name]);
				}
				$(element).parent('div')
				.removeClass(this.settings.errorClass)
				.removeClass(this.settings.validClass)
				.prev("div").addClass('focus').removeClass(this.settings.errorClass)
							.removeClass(this.settings.validClass);
		}
			
	},

	highlight : function(element,errorClass, validClass) {
		
		$(element)
			.parent('div')
			.removeClass(validClass)
			.addClass(errorClass)
			.prev("div")
				.removeClass(validClass)
				.addClass(errorClass);
	 
	},
		unhighlight : function(element,errorClass, validClass) {
			if(element.value!=""){
				$(element).parent('div')
				.removeClass(errorClass)
				.addClass(validClass).prev("div").addClass(validClass)
					.removeClass(errorClass);
				$('span.help-inline',$(element).parent('div')
						.prev('div')).text(
				validRules[element.name]);
			}
		},
		
		});
	}	
/**
 * update media form by using controller action updatemedia
 * @author mkaur
 */
function updateMedia(){
	
	addOverLay();
	// var id = $('input#hid').val();
	 $.ajax({
		url : HOST_PATH + "admin/media/updatemedia",
		method : "post",
		data: $('form#fileupload').serialize(),		
		dataType : "json",
			type : "post",
			success : function(data) {
			if (data != null) {
				removeOverLay();
				/* $('html, body').animate({ 
				      scrollTop: $('#new_media_div').offset().top 
				  });*/

				$("div#createMedia1").hide();
				removeOverLay();
			} else {
				bootbox.alert(__("Problem in your data"));
			}
		}
	});
}
function resetBorders(el)
{
	$(el).each(function(i,o){
	 $(o).parent('div')
		.removeClass("error success")
		.prev("div").removeClass('focus error success') ;
	
	});
}

/**
 * Resets all borders and removes related css classes.
 * @author mkaur  
 */
function cancelValidationBorder(){
	$('#newMediaForm').each(function(){
		$('input').parent('div')
		.removeClass("error success")
		.prev("div").removeClass('focus error success') ;
		$('span.help-inline').remove();	
	});
	
}
/**
 * changes the attr action for multi form submissions.
 * @author mkaur
 */
function submitMedia(){
	$('form#fileupload').submit(function(){
		$(this).attr('action',HOST_PATH+'admin/media/addmedia');
	});
	window.location.href = "/media/updatemedia/";
}
/**
 * counts number of letters on description textarea of form 
 * @param field
 * @param cntfield
 * @param maxlimit
 */
function textCounter(field,cntfield,maxlimit) {
	if (field.value.length > maxlimit){ // if too long...trim it!
	field.value = field.value.substring(0, maxlimit);
	// otherwise, update 'characters left' counter
	}else{
	cntfield.value = maxlimit - field.value.length;
	}
}
/**
 * hide form and show the list. 
 * @param id
 * @author mkaur
 */
function show_hide(id){
	$('tr#'+id).show();
	$('tr#'+id+'_b').hide();
	$('tr#'+id+'_a').hide();
	//$('tr#last').show();
} 