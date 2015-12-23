$(document).ready(function() {
    $("#PartOneOfferlist").select2({placeholder: __("Search a offer")});
    $("#PartTwoOfferlist").select2({placeholder: __("Search a offer")});

    $("#PartOneOfferlist").change(function(){
        $("#PartOneOfferlist #selctedOffer").val($(this).val());
        addSelectedClassOnButton(1, 'campaignOffersOne');
    });

    $("#PartTwoOfferlist").change(function(){
        $("#PartTwoOfferlist #selctedOffer").val($(this).val());
        addSelectedClassOnButton(1, 'campaignOffersTwo');
    });
    //call to function for selected class(button)
    addSelectedClassOnButton(1, 'campaignOffersOne');
    addSelectedClassOnButton(1, 'campaignOffersTwo');
    selectedElements();
    $("input#searchCouponTxt").keypress(function(e) {
        addSelectedClassOnButton(1);
        $('ul#mostPopularCode li').removeClass('selected');
        if (e.which == 13) {
            searchByTxt();
        }
    });
    //code for selection of li
    $('ul#partOneCode li').on('click', function() {
        $('ul#partOneCode li').removeClass('selected');
        $(this).addClass('selected');
        //apply selected class on current button
        addSelectedClassOnButton(2, "campaignOffersOne");
    });

    $('ul#partTwoCode li').on('click', function() {
        $('ul#partTwoCode li').removeClass('selected');
        $(this).addClass('selected');
        //apply selected class on current button
        addSelectedClassOnButton(2, "campaignOffersTwo");
    });


    $( "#partOneCode").sortable();
    $( "#partTwoCode").sortable();
    $( "#partOneCode" ).disableSelection();
    $( "#partTwoCode" ).disableSelection();
    $( "#partOneCode" ).on( "sortstop", function( event, ui ) {
        var offerid = new Array();
        $('#partOneCode .ui-state-default').each(function(){
            offerid.push($(this).attr('reloffer'));
        });
        $('div.image-loading-icon').append("<img id='img-load' src='" +  HOST_PATH  + "/public/images/validating.gif'/>");
        var offerid = offerid.toString();
        //$.ajax({
        //    type : "POST",
        //    url : HOST_PATH + "admin/popularcode/savepopularoffersposition",
        //    method : "post",
        //    dataType : 'json',
        //    data: { offerid: offerid },
        //    success : function(json) {
        //        $('#img-load').remove();
        //        $( "#mostPopularCode" ).sortable( "refresh" );
        //        $( "#mostPopularCode" ).sortable( "refreshPositions" );
        //        $('ul#mostPopularCode li').remove();
        //        var li = '';
        //
        //        if(json!=''){
        //            for(var i in json)
        //            {
        //                lockImage = HOST_PATH + "public/images/back_end/stock_lock.png";
        //                image = "<img src=" + lockImage + " height='20' style='float:right' width='20'>";
        //                li+= "<li class='ui-state-default' reltype='" + json[i].type + "' relpos='" + json[i].position + "' reloffer='" + json[i].offerId + "' id='" + json[i].id + "' >" + json[i].title + "</span>" + image + "</li>";
        //
        //            }
        //            $('ul#mostPopularCode').append(li);
        //            $('ul#mostPopularCode li').click(changeSelectedClass);
        //        }
        //        $('#popular_success_message').css("visibility", "visible");
        //        setTimeout(function(){
        //            $('#popular_success_message').css("visibility", "hidden");
        //        }, 3000);
        //    }
        //});

    });

});

function searchByTxt() {

    $("ul.ui-autocomplete").css('display','none');
    $("ul.ui-autocomplete").html('');
    console.log('ok');
    //addSelectedClassOnButton(4);
    //$(this).addClass().addClass('btn-primary');
}

function addSelectedClassOnButton(flag, id) {

    if(flag==1){
        $('#'+id+' button#deleteOne').removeClass('btn-primary');
        $('#'+id+' button#addNewOffer').addClass('btn-primary');

    } else if(flag==2){
        $('#'+id+' button#deleteOne').addClass('btn-primary');
        $('#'+id+' button#addNewOffer').removeClass('btn-primary');
    } else {
        $('#'+id+' button#deleteOne').removeClass('btn-primary');
        $('#'+id+' button#addNewOffer').removeClass('btn-primary');
        $(flag).addClass('btn-primary');
    }
}

String.prototype.escapeSingleQuotes = function () {
    if (this == null) return null;
    return this.replace(/'/g, "\\'");
};

function addNewOffer() {
    var flag =  '#addNewOffer';
    $('#addNewOffer').attr('disabled' ,"disabled");
    //apply selected class on current button
    addSelectedClassOnButton(flag);

    if($('ul#mostPopularCode li').length > 26) {

        bootbox.alert(__('Popular code list can have maximum 27 records, please delete one if you want to add more popular code'));

        $('#addNewOffer').removeAttr('disabled');

    } else {

        if($("input#selctedOffer").val()=='' || $("input#selctedOffer").val()==undefined)
        {
            //console.log('ok');
            bootbox.alert(__('Please select an offer'));

            $('#addNewOffer').removeAttr('disabled');

        } else {

            var id = $("input#selctedOffer").val();

            $.ajax({
                url : HOST_PATH + "admin/popularcode/addoffer/id/" + id,
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
                        $('ul#mostPopularCode').append(li);

                        $('ul#mostPopularCode li#'+ data.id).click(changeSelectedClass);

                        $('ul#mostPopularCode li#0').remove();

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


function deleteOne() {

    var flag =  '#deleteOne';
    $('#deleteOne').attr('disabled' ,"disabled");
    //apply selected class on current button
    addSelectedClassOnButton(flag);
    var id = $('ul#mostPopularCode li.selected').attr('id');
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

function deletePopularCode() {

    var id = $('ul#mostPopularCode li.selected').attr('id');
    var offerId = $('ul#mostPopularCode li.selected').attr('reloffer');
    var title = $('ul#mostPopularCode li.selected').children('span').html();
    var pos = $('ul#mostPopularCode li.selected').attr('relpos');

    $.ajax({
        url : HOST_PATH + "admin/popularcode/deletepopularcode/id/" + id + "/pos/" + pos,
        method : "post",
        dataType : "json",
        type : "post",
        success : function(json) {

            $('ul#mostPopularCode li').remove();
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

            $('ul#mostPopularCode').append(li);
            $('ul#mostPopularCode li#'+id).addClass('selected');
            $('ul#mostPopularCode li').click(changeSelectedClass);
            //$('ul#mostPopularCode li#'+ $('ul#mostPopularCode li.selected').attr('id')).remove();
            selectedElements();
        }


    });


}

function selectedElements() {
    var selectedRelated = new Array();
    $('ul#mostPopularCode').find('li').each(function(index) {
        selectedRelated[index] = $(this).attr('reloffer');
    });
    $('#SearchedValueIds').val(selectedRelated);
}

