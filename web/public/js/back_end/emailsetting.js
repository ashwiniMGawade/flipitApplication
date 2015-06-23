 /**
  * Change button status when use click on yes no of confimation email
  *
  * @param integer value
  * @author asharma
  * @version 1.0
  */
 function confirmationChanged(value) {

	addOverLay();
	$.ajax({
		url : HOST_PATH + "admin/accountsetting/changemailconfirmation/status/"
				+ value,
		method : "post",
		dataType : "json",
		type : "post",
		success : function(json) {
			if (value == 1) {
				$('button#btnEmailConfimationOn').addClass('btn-primary');
				$('button#btnEmailConfimationOff').removeClass('btn-primary');
			} else {
				$('button#btnEmailConfimationOn').removeClass('btn-primary');
				$('button#btnEmailConfimationOff').addClass('btn-primary');
			}

			removeOverLay();
		}
	});
}


$(document).ready(function() {

	
	$("#testEmail").select2({
		placeholder: __("Search Email"),
		minimumInputLength: 1,
		ajax: { 
			 url: HOST_PATH + "admin/visitor/searchemails",
			 dataType: 'json',
			 data: function(term, page) {
	             return {
	            	 keyword: term,
	            	 flag: 0
             };
         },
		 type: 'post',
		 results: function (data, page) { 
			 return {results: data};
	 		 }
		},
		formatResult: function(data) { 
            return data; 
        },
        formatSelection: function(data) { 
        	$("#testEmail").val(data);
            return data; 
        }
	});
	
	$("#emailHeader").blur(function(){

		saveEmailHeaderFooter('email-header' , $(this).val(), $(this).attr("data-id") );
	});

	$("#emailFooter").blur(function(){

		saveEmailHeaderFooter('email-footer' , $(this).val(), $(this).attr("data-id") );
	});


	jQuery('#dp3').datepicker(); //.on('changeDate' , validateStartEndTimestamp);




	jQuery('#offerstartTime').timepicker({
		 	minuteStep: 5,
            template: 'modal',
            showSeconds: false,
            showMeridian: false,
            defaultTime : $("input#currentSendTime").val()
         //   'afterUpdate'  : validateStartEndTimestamp
    });



	CKEDITOR.replace( 'testimonial1',
			{
				//fullPage : true,
				////extraPlugins : 'wordcount',
				customConfig : 'config.js' ,
				toolbar :  'BasicToolbar'  ,
				height : "150"


			});
	CKEDITOR.replace( 'testimonial2',
			{
				//fullPage : true,
				////extraPlugins : 'wordcount',
				customConfig : 'config.js' ,
				toolbar :  'BasicToolbar'  ,
				height : "150"


			});
	CKEDITOR.replace( 'testimonial3',
			{
				//fullPage : true,
				////extraPlugins : 'wordcount',
				customConfig : 'config.js' ,
				toolbar :  'BasicToolbar'  ,
				height : "150"


			});

//	http://www.flipit.com/admin/accountsetting/maxlimit/limit/1


	CKEDITOR.instances.testimonial1.on('blur', function(e) {

			    var data = e.editor.getData();

			    saveTestimonail(data,'testimonial1');



	});

	CKEDITOR.instances.testimonial2.on('blur', function(e) {
	    var data = e.editor.getData();
	    saveTestimonail(data,'testimonial2');
	});

	CKEDITOR.instances.testimonial3.on('blur', function(e) {
		var data = e.editor.getData();
		saveTestimonail(data,'testimonial3');
	});


});

function saveTestimonail(content,type)
{
    ___addOverLay();
    jQuery.ajax({
        url: HOST_PATH+"admin/accountsetting/save-testimonials",
        type: "POST",
        data: {
            content : content,
            type : type
        },
        dataType: "html",
        success: function(msg){
        	 ___removeOverLay();
        }
} );

}

/**
 * change selected class of li
 * @author kraj
 * @version 1.0
 */
function changeSelectedClass() {

	$('ul#mostPopularCode li').removeClass('selected');
	$(this).addClass('selected');
	//apply selected class on current button
	addSelectedClassOnButton(2);
}
/**
 * change selected of button
 * @param mixed $flag
 * @author kraj
 * @version 1.0
 */
function addSelectedClassOnButton(flag) {

	if(flag==1){

		$('button#moveUp').removeClass('btn-primary');
		$('button#moveDown').removeClass('btn-primary');
		$('button#deleteOne').removeClass('btn-primary');
		$('button#addNewOffer').addClass('btn-primary');

	} else if(flag==2){

		$('button#moveUp').addClass('btn-primary');
		$('button#moveDown').addClass('btn-primary');
		$('button#deleteOne').addClass('btn-primary');
		$('button#addNewOffer').removeClass('btn-primary');

	} else {

		$('button#moveUp').removeClass('btn-primary');
		$('button#moveDown').removeClass('btn-primary');
		$('button#deleteOne').removeClass('btn-primary');
		$('button#addNewOffer').removeClass('btn-primary');
		$(flag).addClass('btn-primary');
	}
}
/**
 * move up element by one from list
 * @author kraj
 * @version 1.0
 */
function moveUp() {

	var flag =  '#moveUp';
	//apply selected class on current button
	addSelectedClassOnButton(flag);
	//get id form slected li by attr
	var id = $('ul#mostPopularCode li.selected').attr('id');
	//get postion form slected li by attr
	var pos = $('ul#mostPopularCode li.selected').attr('relpos');
	if(parseInt(id) > 0){
		$.ajax({
			url : HOST_PATH + "admin/popularcode/moveup/id/" + id + "/pos/" + pos,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {
					$('ul#mostPopularCode li').remove();
					var li = '';
					for(var i in json)
						{
						 	li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].offerId + "' id='" + json[i].id + "' >" + json[i].offer.title + "</li>";

						}
					//append li in ul( list of popular code
					$('ul#mostPopularCode').append(li);
					$('ul#mostPopularCode li#'+id).addClass('selected');
					$('ul#mostPopularCode li').click(changeSelectedClass);
				}


		});

	} else {

		bootbox.alert(__('Please select an offer from list'));
	}
}
/**
 * add offer in offer list
 * @author kraj
 * @version 1.0
 */
function addNewOffer() {

	var flag =  '#addNewOffer';
	//apply selected class on current button
	addSelectedClassOnButton(flag);

	if($('ul#mostPopularCode li').length > 25) {

		bootbox.alert(__('Popular code list only show 25 records please delete one If you want to add this popular code'));

	} else {

		if($("input#searchCouponTxt").val()=='' || $("input#searchCouponTxt").val()==undefined)
			{
				//console.log('ok');
				bootbox.alert(__('Please select an offer'));

			} else {

				var offerName = $("input#searchCouponTxt").val();

				$.ajax({
	        		url : HOST_PATH + "admin/popularcode/addoffer/name/" + offerName,
	     			method : "post",
	     			dataType : "json",
	     			type : "post",
	     			success : function(data) {

	     				if(data=='2' || data==2)
	     					{
	     						bootbox.alert(__('Problem in your selection'));

	     					} else {

	     						var li  = "<li reltype='" + data.type + "' relpos='" + data.position + "' reloffer='" + data.offerId + "' id='" + data.id + "' >" + offerName + "</li>";
	     						$('ul#mostPopularCode').append(li);

	     						$('ul#mostPopularCode li#'+ data.id).click(changeSelectedClass);

	     						$("input#searchCouponTxt").val('');
	     					}

	     			}


				});
				//code add offer in list here
			}
	}
}
/**
 * move down element on one position from list
 * @author kraj
 * @version 1.0
 */
function moveDown() {

	var flag =  '#moveDown';
	//apply selected class on current button
	addSelectedClassOnButton(flag);
	var id = $('ul#mostPopularCode li.selected').attr('id');
	var pos = $('ul#mostPopularCode li.selected').attr('relpos');

	if(parseInt(id) > 0) {
		$.ajax({
			url : HOST_PATH + "admin/popularcode/movedown/id/" + id + "/pos/" + pos,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {

					$('ul#mostPopularCode li').remove();
					var li = '';
					for(var i in json) {

						 	li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].offerId + "' id='" + json[i].id + "' >" + json[i].offer.title + "</li>";

						}
					$('ul#mostPopularCode').append(li);
					$('ul#mostPopularCode li#'+id).addClass('selected');
					$('ul#mostPopularCode li').click(changeSelectedClass);
				}


		});

	} else {

		bootbox.alert(__('Please select an offer from list'));
	}
}
/**
 * confirmation for deletion
 * @author kraj
 * @version 1.0
 */
function deleteOne() {

	var flag =  '#deleteOne';
	//apply selected class on current button
	addSelectedClassOnButton(flag);
	var id = $('ul#mostPopularCode li.selected').attr('id');
	if(parseInt(id) > 0){
	bootbox.confirm(__("Are you sure you want to delete this code?"),__('No'),__('Yes'),function(r){

		if(!r){

			//return false if not confimed
			return false;

		} else {
			//call to delete function
			deletePopularCode();
		}

	});
} else {

		bootbox.alert(__('Please select an offer from list'));
	}
}
/**
 * delete popular code from list
 * @author kraj
 * @version 1.0
 */
function deletePopularCode() {

	var id = $('ul#mostPopularCode li.selected').attr('id');
	var pos = $('ul#mostPopularCode li.selected').attr('relpos');

		$.ajax({
			url : HOST_PATH + "admin/popularcode/deletepopularcode/id/" + id + "/pos/" + pos,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {

					$('ul#mostPopularCode li').remove();
					var li = '';
					for(var i in json)
						{
						 	li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].offerId + "' id='" + json[i].id + "' >" + json[i].offer.title + "</li>";

						}
					$('ul#mostPopularCode').append(li);
					$('ul#mostPopularCode li#'+id).addClass('selected');
					$('ul#mostPopularCode li').click(changeSelectedClass);
					//$('ul#mostPopularCode li#'+ $('ul#mostPopularCode li.selected').attr('id')).remove();

				}


		});


}

function sendNewsLetter(e){

	$receipt = getTotalRecepients() ;
	bootbox.confirm(__("Are you sure you want to send the newsletter to "+ $receipt +" recepients?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		} else {
			e.preventDefault();
			var newForm = jQuery('<form>', {
		        'action': HOST_PATH + "admin/accountsetting/mandrill",
		        'target': '_top',
		        'method' : 'post'
		    });
			$(newForm).append($("#speacialForm").html());
			$("input#offerstartTime" , newForm).val( $("input#offerstartTime", "#speacialForm").val());
			$("input#offerStartDate" , newForm ).val($("input#offerStartDate", "#speacialForm").val());
			newForm.appendTo('body').submit().remove();
		}

	});
 }

function sendTestNewsLetter(e){
	bootbox.confirm(__("Are you sure you want to send the newsletter to one person?"),__('No'),__('Yes'),function(r){
		if(!r){
			return false;
		}
		else{

			e.preventDefault();

			var newForm = jQuery('<form>', {
		        'action': HOST_PATH + "admin/accountsetting/mandrill/send/test",
		        'target': '_top',
		        'method' : 'post',
		    }).append(jQuery('<input>', {
		        'name': 'send',
		        'value': 'test',
		        'type': 'hidden'
		    })).append(jQuery('<input>', {
		        'name': 'testEmail',
		        'value': $("input#testEmail").val(),
		        'type': 'hidden'
		    }));

		    newForm.appendTo('body').submit().remove();
		}

	});
 }

function saveEmailHeaderFooter(name , data, id)
{
	$.ajax({
		url : HOST_PATH + "admin/email/email-header-footer",
		type : 'post',
		data : { 'template' : name , 'data' : data, 'templateId': id }
	});
}

function saveSenderEmail(el)
{
	var value = $(el).val().trim();
	var templateId = $(el).attr("data-id");
	if(value != ''){
		$.ajax({
			url : HOST_PATH + 'admin/email/saveemailcontent',
			type : 'post',
			data : { name : $(el).attr('name'), val : value, 'templateId' : templateId},
		});
	}
}


/**
 * Change button status when use click on yes no of show testimonials
 *
 * @param integer value
 * @version 1.0
 */
function displayTestimonials(value) {

	$.ajax({
		url : HOST_PATH + "admin/accountsetting/save-testimonials",
        data : {
        	content: value ,
        	type : "showTestimonial"
		},
		method : "post",
		dataType : "json",
		type : "post",
		success : function(json) {
			if (value == 1) {
				$('button#btnShowTestimonialsOn').addClass('btn-primary');
				$('button#btnShowTestimonialsOff').removeClass('btn-primary');
			} else {
				$('button#btnShowTestimonialsOn').removeClass('btn-primary');
				$('button#btnShowTestimonialsOff').addClass('btn-primary');
			}


		}
	});
}

function getTotalRecepients()
{

	var count = 0 ;
	$.ajax({
		url : HOST_PATH + "admin/accountsetting/total-recepients",
			method : "post",
			dataType : "json",
			type : "post",
			async : false,
			success : function(data) {
				count = 	data['recepients'];
			}

	});

	return count ;
}


/**
* schedule newsletter
* @param dom object el from which it is being called
*/
function scheduleNewsletter(el)
{
	$(el).addClass('btn-primary').siblings('button').removeClass('btn-primary active') ;

	$("input[type=hidden]#isScheduled").val(1);

	jQuery('#dp3').datepicker(); 


	jQuery('#offerstartTime').timepicker({
		 	minuteStep: 5,
            template: 'modal',
            showSeconds: false,
            showMeridian: false,
    });

	$("div#timestamp-feild-container").show();
	$("a#sendNewsletter-btn").text(__('Save Scheduling'));
}

/**
 * hide scheduling options
 * @param dom object el from which it is being called
 */
function unScheduleNewsletter(el)
{
	$(el).addClass('btn-primary').siblings('button').removeClass('btn-primary active') ;

	$("input[type=hidden]#isScheduled").val(0);
	$("div#timestamp-feild-container").hide();
	$("a#sendNewsletter-btn").text(__('Send Newsletter'));
}





