
/**
 * validRules oject contain all the messages that are visible when an elment
 * value is valid
 * 
 * structure to define a message for element: key is to be element name and Value is
 * message
 */
var validRules = {
         addNetworkText : __("Network name looks great"),
         subId : __("Network subid looks great"),
         selectNetworkList : __("ok !")
};


var focusRules = {
		
		addNetworkText : __("Enter network name"),
		selectNetworkList : __("Select name"),
		subId : __("Enter network subid")
};


/**
 * executes when document is loaded 
 * @author blal
 */
$(document).ready(function(){
	
	$(":input").attr("autocomplete","off");
	$('form#updateNetworkForm').submit(function(){
		updateNetworks();
	});
	
	
	// call checkFields function
	$("input[type=text]#addNetworkText").keyup(checkFields);
		
	
	$("input[type=text]#addNetworkText").focusout(checkFields);
		

	$("input[type=text]#addNetworkText").focusin(checkFields);
	
	$(document).live('input paste',function(e){
		
		checkFields();
		
	});
	$("select#selectNetworkList").change(checkFields);
	
	
	editAffilateValidation();
	
});

/**
 * validate one of the two fields 
 * @author blal
 */
function checkFields()
{
	if( $("input[type=text]#addNetworkText").val() == "" && $("select#selectNetworkList").val() == "" )
	{
		$("button#updatenetrecord").attr("disabled", true);
		$("div.customError").show();
	}
	else if($("input[type=text]#addNetworkText").val() == "" && $("select#selectNetworkList").val() != "")
	{
		$("button#updatenetrecord").attr("disabled", false);
		$("div.customError").slideUp('slow');
		
	}else {
		
		$("button#updatenetrecord").attr("disabled", false);
		$("div.customError").slideUp('slow');
	}
	
}
function updateNetworks()
{
	if($('form#updateNetworkForm').valid())
	{
		return false;
		
	 }else {
		 
	    return false;
	}
}
var validatorForEditNetwork = null ;
/**
 * apply validation on edit network
 * @author blal
 */
function editAffilateValidation(){
	
	validatorForEditNetwork = $("form#updateNetworkForm")
	.validate(
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
					addNetworkText : {
						
						 minlength : 2
						 
						}
				},
				
				messages : {
					addNetworkText : {
						
					      minlength : __("Please enter atleast 2 characters")
					},
					subId : {
						  required : __("Please enter network subid"),	
					}
				},
				onfocusin : function(element) {
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

					$(element).parent('div')
							.removeClass(validClass)
							.addClass(errorClass).prev(
									"div").removeClass(
									validClass)
							.addClass(errorClass);

				},
				unhighlight : function(element,errorClass, validClass) 
				{
					//console.log($(element).val());
					if($(element).val()!='' && $(element).val()!=null)
					{	
						$(element).parent('div').removeClass(errorClass).addClass(validClass).prev("div").addClass(	validClass).removeClass(errorClass);
						$('span.help-inline',$(element).parent('div').prev('div')).text(validRules[element.name]).show();
						
					} else {
						
						$(element).parent('div').removeClass(errorClass).removeClass(validClass).prev("div").removeClass(validClass).removeClass(errorClass);
						$('span.help-inline',$(element).parent('div').prev('div')).hide();
						
				   }
				
				},

			});
}
$.validator.setDefaults({
	onkeyup : false,
	onfocusout : function(element) {
		$(element).valid();
	}

});

function editExtendedSubid()
{
    bootbox.confirm(__("Are you sure you want to edit Extended subid of this network?"),__('No'),__('Yes'),function(r)
    {
        if(r){
            $("input[name=extendedSubid]").data('prev-data' , $("input[name=extendedSubid]").val());
            $("input[name=extendedSubid]").removeAttr('disabled');
            $(".warningContainer").removeClass('hide');
            $(".extendedSubidEdit-lnk").hide();
            $(".extendedSubidCancel-lnk").show();
        }else {
            $("input[name=extendedSubid]").attr('disabled' ,'disabled');
            $(".warningContainer").addClass('hide');
        }
      });
    
}

function cancelEditExtendedSubid()
{
    $("input[name=extendedSubid]").parent('div').removeClass('success')
    .prev("div").addClass('focus').removeClass('success').find('span.help-inline').html('');
    $("input[name=extendedSubid]").val( $("input[name=extendedSubid]").data('prev-data'));
    $(".extendedSubidEdit-lnk").show();
    $(".extendedSubidCancel-lnk").hide();
    $("input[name=extendedSubid]").attr('disabled' ,'disabled');
    $(".warningContainer").addClass('hide');
}

/**
 * delete network while edit
 * @author blal
 */
function deleteNetworkByEdit(e)
{
	var id =  $('input#id').val();
	deleteNetwork(id);
}

function deleteNetwork(id) {
	
	bootbox.confirm(__("Are you sure you want to delete this network?"),__('No'),__('Yes'),function(r){
		if(!r){
			
			return false;
		} else {
			addOverLay();
			$.ajax({
				url : HOST_PATH+"admin/affiliate/deletenetwork",
				type : "POST",
				data : "id="+id
			}).done(function(msg) {
				window.location  = HOST_PATH + 'admin/affiliate';
				
		    }); 
		}
		
	});
	
}
/**
 * 	@author sp singh
 */

function editSubid()
{
	bootbox.confirm(__("Are you sure you want to edit subid of this network?"),__('No'),__('Yes'),function(r){
	
		if(r){
			console.log('yes');
			
			$("input[name=subId]").data('prev-data' , $("input[name=subId]").val());
			$("input[name=subId]").removeAttr('disabled');
			$(".warningContainer").removeClass('hide');
			$(".subidEdit-lnk").hide();
			$(".subidCancel-lnk").show();
		}else {
			$("input[name=subId]").attr('disabled' ,'disabled');
			$(".warningContainer").addClass('hide');
		}
		
  });
	
}

function cancelEdidSubid()
{
	
	$("input[name=subId]").parent('div').removeClass('success')
	.prev("div").addClass('focus').removeClass('success').find('span.help-inline').html('');
	
	$("input[name=subId]").val( $("input[name=subId]").data('prev-data'));
	$(".subidEdit-lnk").show();
	$(".subidCancel-lnk").hide();
	$("input[name=subId]").attr('disabled' ,'disabled');
	$(".warningContainer").addClass('hide');
}
