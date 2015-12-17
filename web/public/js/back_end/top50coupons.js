
$(document).ready(function() {
	$("#offerlist").select2({placeholder: __("Search a offer")});
	
	$("#offerlist").change(function(){
		$("#selctedOffer").val($(this).val());
		addSelectedClassOnButton(1);
	});
	//call to function for selected class(button)
	addSelectedClassOnButton(1);
	selectedElements();
	$("input#searchCouponTxt").keypress(function(e) {
		addSelectedClassOnButton(1);
		$('ul#top50coupons li').removeClass('selected');
            // if the key pressed is the enter key
              if (e.which == 13) {
                  searchByTxt();
              }
		});

		//code for selection of li
		$('ul#top50coupons li').click(changeSelectedClass);
		
		$( "#top50coupons" ).sortable();
	    $( "#top50coupons" ).disableSelection();
		$( "#top50coupons" ).on( "sortstop", function( event, ui ) {
			var offerid = new Array();
			$('.ui-state-default').each(function(){
		        offerid.push($(this).attr('reloffer'));
		    });
			$('div.image-loading-icon').append("<img id='img-load' src='" +  HOST_PATH  + "/public/images/validating.gif'/>");
		    var offerid = offerid.toString();
			$.ajax({
		        type : "POST",
		        url : HOST_PATH + "admin/top50coupons/savetop50couponspositionAction",
		        method : "post",
		        dataType : 'json',
		        data: { offerid: offerid },
		        success : function(json) { 
					$('#img-load').remove();
					$( "#top50coupons" ).sortable( "refresh" );
					$( "#top50coupons" ).sortable( "refreshPositions" );
					$('ul#top50coupons li').remove();
						var li = '';

					if(json!=''){
						for(var i in json)
							{
								lockImage = HOST_PATH + "public/images/back_end/stock_lock.png";
							    image = "<img src=" + lockImage + " height='20' style='float:right' width='20'>";
							 	li+= "<li class='ui-state-default' reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].offerId + "' id='" + json[i].id + "' >" + json[i].title + "</span>" + image + "</li>";

							}
						$('ul#top50coupons').append(li);
						$('ul#top50coupons li').click(changeSelectedClass);
					}
                    $('#popular_success_message').css("visibility", "visible");
					setTimeout(function(){
                        $('#popular_success_message').css("visibility", "hidden");
					}, 3000);
		        }
		    });

	 	});
		
});
/**
 * search offer by text
 */
function searchByTxt() {
	$("ul.ui-autocomplete").css('display','none');
	$("ul.ui-autocomplete").html('');
}
/**
 * change selected class of li
 */
function changeSelectedClass() {
	$('ul#top50coupons li').removeClass('selected');
	$(this).addClass('selected');
	//apply selected class on current button
	addSelectedClassOnButton(2);
}
/**
 * change selected of button
 */
function addSelectedClassOnButton(flag) {
	if (flag == 1) {
		$('button#moveUp').removeClass('btn-primary');
		$('button#moveDown').removeClass('btn-primary');
		$('button#deleteOne').removeClass('btn-primary');
		$('button#addNewOffer').addClass('btn-primary');
		$('button#lock').removeClass('btn-primary');
	} else if (flag == 2) {
		$('button#moveUp').addClass('btn-primary');
		$('button#moveDown').addClass('btn-primary');
		$('button#deleteOne').addClass('btn-primary');
		$('button#addNewOffer').removeClass('btn-primary');
		$('button#lock').addClass('btn-primary');
	} else {
		$('button#moveUp').removeClass('btn-primary');
		$('button#moveDown').removeClass('btn-primary');
		$('button#deleteOne').removeClass('btn-primary');
		$('button#addNewOffer').removeClass('btn-primary');
		$('button#lock').removeClass('btn-primary');
		$(flag).addClass('btn-primary');
	}
}
/**
 * move up element by one from list
 */
function moveUp() {
	var flag =  '#moveUp';
	$('#moveUp').attr('disabled' ,"disabled");

	//apply selected class on current button
	addSelectedClassOnButton(flag);

	//get id form slected li by attr
	var id = $('ul#top50coupons li.selected').attr('id');
	var previousCodeId = $('ul#top50coupons li.selected').prev('li').attr('id');

	//get postion form slected li by attr
	var pos = $('ul#top50coupons li.selected').attr('relpos');
	var previousCodePosition = $('ul#top50coupons li.selected').prev('li').attr('relpos');
	if(parseInt(id) > 0){
		$.ajax({
			url : HOST_PATH + "admin/popularcode/moveup/id/" + id + "/pos/" + pos + "/previousCodeId/" + previousCodeId + "/previousCodePosition/"+ previousCodePosition,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {
					$('ul#top50coupons li').remove();
					var li = '';
					for(var i in json)
						{
						if(json[i].type == "MN"){
							lockImage = HOST_PATH + "public/images/back_end/stock_lock.png";
							image = "<img src=" + lockImage + " height='20' style='float:right' width='20'>";
						}else{
							image = "";
						}
						 	li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].offerId + "' id='" + json[i].id + "' ><span>" + json[i].offer.title + "</span>" + image + "</li>";
 						
						}
					//append li in ul( list of popular code
					$('ul#top50coupons').append(li);
					$('ul#top50coupons li#'+id).addClass('selected');
					$('ul#top50coupons li').click(changeSelectedClass);
					$('#moveUp').removeAttr('disabled');
				}
		});
		
	} else {
		
		bootbox.alert(__('Please select an offer from list'));
		$('#moveUp').removeAttr('disabled');
	}
}
/**
 * add offer in offer list
 * @author kraj updated by cbhopal
 * @version 1.0
 */
String.prototype.escapeSingleQuotes = function () {
    if (this == null) return null;
    return this.replace(/'/g, "\\'");
};


function addNewOffer() {
	var flag =  '#addNewOffer';
	$('#addNewOffer').attr('disabled' ,"disabled");
	//apply selected class on current button
	addSelectedClassOnButton(flag);
	
	if ($('ul#top50coupons li').length > 50) {
		bootbox.alert(__('Top 50 coupons list can have maximum 50 records, please delete one if you want to add more coupons to list'));
		$('#addNewOffer').removeAttr('disabled');
	} else {
		if($("input#selctedOffer").val()=='' || $("input#selctedOffer").val()==undefined) {
				bootbox.alert(__('Please select an offer'));
				$('#addNewOffer').removeAttr('disabled');
        } else {
            var id = $("input#selctedOffer").val();
            $.ajax({
                url : HOST_PATH + "admin/top50coupons/addoffer/id/" + id,
                method : "post",
                dataType : "json",
                type : "post",
                success : function(data) {

                    //console.log(data);
                    if(data=='2' || data==2)
                        {
                            bootbox.alert(__('This offer already exists in the list'));
                        }
                        else if(data=='0' && data==0) {

                            bootbox.alert(__('This offer does not exist'));


                        } else {

                            if(data.type == "MN"){
                                lockImage = HOST_PATH + "public/images/back_end/stock_lock.png";
                                image = "<img src=" + lockImage + " height='20' style='float:right' width='20'>";
                            }else{
                                image = "";
                            }
                            var li  = "<li class='ui-state-default' reltype='" + data.type + "' relpos='" + data.position + "' reloffer='" + data.offerId + "' id='" + data.id + "' ><span>" + data.title.replace(/\\/g, '')  + "</span>"+ image + "</li>";
                            $('ul#top50coupons').append(li);

                            $('ul#top50coupons li#'+ data.id).click(changeSelectedClass);

                            $('ul#top50coupons li#0').remove();

                            $('div.combobox a.select2-choice').children('span').html('');

                            $("#offerlist option[value='"+  id +"']").remove();

                            $("input#selctedOffer").val('');

                            selectedElements();

                        }


                    $('#addNewOffer').removeAttr('disabled');
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
	$('#moveDown').attr('disabled' ,"disabled");
	//apply selected class on current button
	addSelectedClassOnButton(flag);

	var id = $('ul#top50coupons li.selected').attr('id');
	var nextCodeId = $('ul#top50coupons li.selected').next('li').attr('id');
	//get postion form slected li by attr
	var pos = $('ul#top50coupons li.selected').attr('relpos');
	
	if(parseInt(id) > 0) {
		$.ajax({
			url : HOST_PATH + "admin/popularcode/movedown/id/" + id + "/pos/" + pos+ "/nextCodeId/" + nextCodeId,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {
					
					$('ul#top50coupons li').remove();
					var li = '';
					for(var i in json) {
						if(json[i].type == "MN"){
							lockImage = HOST_PATH + "public/images/back_end/stock_lock.png";
							image = "<img src=" + lockImage + " height='20' style='float:right' width='20'>";
						}else{
							image = "";
						}
						li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].offerId + "' id='" + json[i].id + "' ><span>" + json[i].offer.title  + "</span>" + image +"</li>";
 						
						}
					$('ul#top50coupons').append(li);
					$('ul#top50coupons li#'+id).addClass('selected');
					$('ul#top50coupons li').click(changeSelectedClass);
					
					$('#moveDown').removeAttr('disabled');
				}
		
			
		});
		
	} else {
		
		bootbox.alert(__('Please select an offer from list'));
		$('#moveDown').removeAttr('disabled');
	}
	
}

/**
 * confirmation for deletion
 * @author kraj
 * @version 1.0
 */

function deleteOne() {
	
	var flag =  '#deleteOne';
	$('#deleteOne').attr('disabled' ,"disabled");
	//apply selected class on current button
	addSelectedClassOnButton(flag);
	var id = $('ul#top50coupons li.selected').attr('id');
	if(parseInt(id) > 0){
	bootbox.confirm(__("Are you sure you want to delete this code?"),__('No'),__('Yes'),function(r){
		
		if(!r){
			$('#deleteOne').removeAttr('disabled');
			//return false if not confimed
			return false;
			
		} else {
			//call to delete function
			deletePopularCode();
			$('#deleteOne').removeAttr('disabled');
		}
		
	});
} else {
		
		bootbox.alert(__('Please select an offer from list'));
		$('#deleteOne').removeAttr('disabled');
	}
}
/**
 * delete popular code from list
 * @author kraj
 * @version 1.0
 */
function deletePopularCode() {
	
	var id = $('ul#top50coupons li.selected').attr('id');
	var offerId = $('ul#top50coupons li.selected').attr('reloffer');
	var title = $('ul#top50coupons li.selected').children('span').html();
	var pos = $('ul#top50coupons li.selected').attr('relpos');
	
		$.ajax({
			url : HOST_PATH + "admin/popularcode/deletepopularcode/id/" + id + "/pos/" + pos,
				method : "post",
				dataType : "json",
				type : "post",
				success : function(json) {
					
					$('ul#top50coupons li').remove();
					var li = '';
					for(var i in json) {
						if(json[i].type == "MN"){
							lockImage = HOST_PATH + "public/images/back_end/stock_lock.png";
							image = "<img src=" + lockImage + " height='20' style='float:right' width='20'>";
						}else{
							image = "";
						}
						li+= "<li class='ui-state-default' reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].offerId + "' id='" + json[i].id + "' ><span>" + json[i].title +"</span>" + image + "</li>";
 						
						
					}
					$('select#offerlist').append('<option value="' + offerId + '">' + title  + '</option>');
					
					$('ul#top50coupons').append(li);
					$('ul#top50coupons li#'+id).addClass('selected');
					$('ul#top50coupons li').click(changeSelectedClass);
					//$('ul#top50coupons li#'+ $('ul#top50coupons li.selected').attr('id')).remove();
					selectedElements();
				}
		
			
		});
		
	
}

/**
 * add id's of already selected offers in an array
 * @author kraj
 * @version 1.0
 */
function selectedElements() {
	var selectedRelated = new Array();
	$('ul#top50coupons').find('li').each(function(index) {
		selectedRelated[index] = $(this).attr('reloffer');
	});
	$('#SearchedValueIds').val(selectedRelated);
}

/**
 * lock element for one position from list
 * @author Raman 
 * @version 1.0
 */
function lock() {
	
	var flag =  '#lock';
	//$('#moveDown').attr('disabled' ,"disabled");
	//apply selected class on current button
	addSelectedClassOnButton(flag);
	var id = $('ul#top50coupons li.selected').attr('reloffer');
	//var pos = $('ul#top50coupons li.selected').attr('relpos');
	
	if(parseInt(id) > 0) {
		$.ajax({
			url : HOST_PATH + "admin/popularcode/lock",
			type : "post",
			dataType : "json",
			data : {'id' : id},
			success : function(json) {
				$('ul#top50coupons li').remove();
				var li = '';
				for(var i in json) {
					if(json[i].type == "MN"){
						lockImage = HOST_PATH + "public/images/back_end/stock_lock.png";
						image = "<img src=" + lockImage + " height='20' style='float:right' width='20'>";
					}else{
						image = "";
					}
					li+= "<li reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].offerId + "' id='" + json[i].id + "' ><span>" + json[i].offer.title + "</span>" + image +"</li>";
						
					}
				$('ul#top50coupons').append(li);
				$('ul#top50coupons li#'+id).addClass('selected');
				$('ul#top50coupons li').click(changeSelectedClass);
				$('#lock').removeAttr('disabled');
			}
		});
		
	} else {
		
		bootbox.alert(__('Please select an offer from list'));
		//$('#moveDown').removeAttr('disabled');
	}
	
}

