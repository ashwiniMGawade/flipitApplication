$(document).ready(function() {
    $("#categorylist").select2();
    $("#categorylist").change(function(){
        $('#selctedCategoryId').val($(this).val());
        loadSelectedPageOffers();
    });

    $("#offerlist").select2({placeholder: __("Search a offer")});
    $("#offerlist").change(function(){
        $("#selctedOffer").val($(this).val());
        addSelectedClassOnButton(1);
    });

    addSelectedClassOnButton(1);
    selectedElements();
    $("input#searchCouponTxt").keypress(function(e) {
        addSelectedClassOnButton(1);
        $('ul#category li').removeClass('selected');
            if (e.which == 13) {
              searchByTxt();
            }
    });

    $('ul#category li').click(changeSelectedClass);
    $( "#category" ).sortable();
    $( "#category" ).disableSelection();
    $( "#category" ).on( "sortstop", function( event, ui ) {
        var offerid = new Array();
        $('.ui-state-default').each(function() {
            offerid.push($(this).attr('reloffer'));
        });
        $('div.image-loading-icon').append("<img id='img-load' src='" +  HOST_PATH  + "/public/images/validating.gif'/>");
        var offerid = offerid.toString();
        $.ajax({
            type : "POST",
            url : HOST_PATH + "admin/categoriesoffers/saveposition",
            method : "post",
            dataType : 'json',
            data: {offersIds: offerid, categoryId: $('#selctedCategoryId').val()},
            success : function(json) { 
                $('#img-load').remove();
                $( "#category" ).sortable( "refresh" );
                $( "#category" ).sortable( "refreshPositions" );
                $('ul#category li').remove();
                var li = '';
                if(json!=''){
                    for(var i in json) {
                        li+= "<li class='ui-state-default' relpos='" + json[i].position 
                        + "' reloffer='" + json[i]['offers'].id + "' id='" + json[i].id + "' ><span>" 
                        + json[i]['offers'].title +"</span></li>";
                    }
                    $('ul#category').append(li);
                    $('ul#category li').click(changeSelectedClass);
                }
                bootbox.alert(__('Offers successfully updated.'));
                setTimeout(function(){
                  bootbox.hideAll();
                }, 3000);
            }
        });
    });
});

function loadSelectedPageOffers() {
    var categoryId =  $('#selctedCategoryId').val();
    window.location.href =  HOST_PATH + "admin/categoriesoffers/index/categoryId/" +  categoryId;
}
function searchByTxt() {    
    $("ul.ui-autocomplete").css('display','none');
    $("ul.ui-autocomplete").html('');
    console.log('ok');
}

function changeSelectedClass() {
    $('ul#category li').removeClass('selected');
    $(this).addClass('selected');
    addSelectedClassOnButton(2);
}

function addSelectedClassOnButton(flag) {
    if(flag==1){
        $('button#deleteOne').removeClass('btn-primary');
        $('button#addNewOffer').addClass('btn-primary');
    } else if(flag==2){
        $('button#deleteOne').addClass('btn-primary');
        $('button#addNewOffer').removeClass('btn-primary');
    } else {
        $('button#deleteOne').removeClass('btn-primary');
        $('button#addNewOffer').removeClass('btn-primary');
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
    addSelectedClassOnButton(flag);
    if($('ul#category li').length > 9) {
        bootbox.alert(__('Code list can have maximum 10 records, please delete one if you want to add more code'));
        $('#addNewOffer').removeAttr('disabled');
    } else {
        if($("input#selctedOffer").val()=='' || $("input#selctedOffer").val()==undefined) {
            bootbox.alert(__('Please select an offer'));
            $('#addNewOffer').removeAttr('disabled');
        } else {
            
            var id = $("input#selctedOffer").val();
            var categoryId = $("input#selctedCategoryId").val();
            $.ajax({
                url : HOST_PATH + "admin/categoriesoffers/addoffer/id/" + id + '/categoryId/' + categoryId,
                method : "post",
                dataType : "json",
                type : "post",
                success : function(data) {
                    if(data=='2' || data==2)
                        {
                            bootbox.alert(__('This offer already exists in the list'));
                        }
                        else if(data=='0' && data==0) {
                            bootbox.alert(__('This offer does not exist'));
                        } else {
            
                            var li  = "<li class='ui-state-default'  relpos='" + data.position 
                            + "' reloffer='" + data.offerId + "' id='" + data.id + "' ><span>" 
                            + data.title.replace(/\\/g, '')  + "</span></li>";

                            $('ul#category').append(li);
                            $('ul#category li#'+ data.id).click(changeSelectedClass);
                            $('ul#category li#0').remove();
                            $('div.coupon-sidebar-heading a.select2-choice').children('span').html(''); 
                            $("#offerlist option[value='"+  id +"']").remove();
                            $("input#selctedOffer").val('');
                            selectedElements();
                        }
                    $('#addNewOffer').removeAttr('disabled');
                }
            });
        }
    }
}

function deleteOne() {
    var flag =  '#deleteOne';
    $('#deleteOne').attr('disabled' ,"disabled");
    addSelectedClassOnButton(flag);
    var id = $('ul#category li.selected').attr('id');
    if(parseInt(id) > 0){
        bootbox.confirm(__("Are you sure you want to delete this code?"),__('No'),__('Yes'),function(r){
        if(!r){
            $('#deleteOne').removeAttr('disabled');
            return false;
        } else {
            deleteCode();
            $('#deleteOne').removeAttr('disabled');
        }
    });
} else {
    bootbox.alert(__('Please select an offer from list'));
    $('#deleteOne').removeAttr('disabled');
    }
}

function deleteCode() {
    var id = $('ul#category li.selected').attr('id');
    var offerId = $('ul#category li.selected').attr('reloffer');
    var categoryId = $("input#selctedCategoryId").val();
    var title = $('ul#category li.selected').children('span').html();
    var pos = $('ul#category li.selected').attr('relpos');
    $.ajax({
        url : HOST_PATH + "admin/categoriesoffers/deletecode/id/" +id+ "/pos/"+pos+"/categoryId/"+categoryId,
        method : "post",
        dataType : "json",
        type : "post",
        success : function(json) {
            $('ul#category li').remove();
            var li = '';

            for(var i in json) {
                li+= "<li class='ui-state-default' relpos='" + json[i].position 
                + "' reloffer='" + json[i].offerId + "' id='" + json[i].id + "' ><span>" 
                + json[i].title +"</span></li>";
            }

            $('select#offerlist').append('<option value="' + offerId + '">' + title  + '</option>');
            $('ul#category').append(li);
            $('ul#category li#'+id).addClass('selected');
            $('ul#category li').click(changeSelectedClass);
            selectedElements();
        }
    });
}

function selectedElements() {
    var selectedRelated = new Array();
    $('ul#category').find('li').each(function(index) {
        selectedRelated[index] = $(this).attr('reloffer');
    });
    $('#SearchedValueIds').val(selectedRelated);
}

