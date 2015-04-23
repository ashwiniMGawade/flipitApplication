$(document).ready(function() {
    $("#specialPagelist").select2();
    $("#specialPagelist").change(function(){
        $('#selctedPageId').val($(this).val());
        addNewOffers();
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
        $('ul#specialPages li').removeClass('selected');
            if (e.which == 13) {
              searchByTxt();
            }
    });

    $('ul#specialPages li').click(changeSelectedClass);
    $( "#specialPages" ).sortable();
    $( "#specialPages" ).disableSelection();
    $( "#specialPages" ).on( "sortstop", function( event, ui ) {
        var offerid = new Array();
        $('.ui-state-default').each(function() {
            offerid.push($(this).attr('reloffer'));
        });
        $('div.image-loading-icon').append("<img id='img-load' src='" +  HOST_PATH  + "/public/images/validating.gif'/>");
        var offerid = offerid.toString();
        $.ajax({
            type : "POST",
            url : HOST_PATH + "admin/specialpagesoffers/saveposition",
            method : "post",
            dataType : 'json',
            data: {offersIds: offerid, pageId: $('#selctedPageId').val()},
            success : function(json) { 
                $('#img-load').remove();
                $( "#specialPages" ).sortable( "refresh" );
                $( "#specialPages" ).sortable( "refreshPositions" );
                $('ul#specialPages li').remove();
                var li = '';

                if(json!=''){
                    for(var i in json) {
                        li+= "<li class='ui-state-default' relpos='" + json[i].position 
                        + "' reloffer='" + json[i].offerId + "' id='" + json[i].id + "' ><span>" 
                        + json[i].title +"</span></li>";
                    }
                    $('ul#specialPages').append(li);
                    $('ul#specialPages li').click(changeSelectedClass);
                }
                bootbox.alert(__('Offers successfully updated.'));
                setTimeout(function(){
                  bootbox.hideAll();
                }, 3000);
            }
        });
    });
});

function addNewOffers() {
    $.ajax({
        type : "POST",
        url : HOST_PATH + "admin/specialpagesoffers/addnewoffers",
        method : "post",
        dataType : 'json',
        data: '',
        success : function(json) {
            setTimeout(loadSelectedPageOffers, 1000);
        }
    });
}

function loadSelectedPageOffers() {
    var pageId =  $('#selctedPageId').val();
    window.location.href =  HOST_PATH + "admin/specialpagesoffers/index/pageId/" +  pageId;
}
function searchByTxt() {    
    $("ul.ui-autocomplete").css('display','none');
    $("ul.ui-autocomplete").html('');
    console.log('ok');
}

function changeSelectedClass() {
    $('ul#specialPages li').removeClass('selected');
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
    if($('ul#specialPages li').length > 26) {
        bootbox.alert(__('Code list can have maximum 27 records, please delete one if you want to add more code'));
        $('#addNewOffer').removeAttr('disabled');
    } else {
        if($("input#selctedOffer").val()=='' || $("input#selctedOffer").val()==undefined) {
            bootbox.alert(__('Please select an offer'));
            $('#addNewOffer').removeAttr('disabled');
        } else {
            
            var id = $("input#selctedOffer").val();
            var pageId = $("input#selctedPageId").val();
            
            $.ajax({
                url : HOST_PATH + "admin/specialpagesoffers/addoffer/id/" + id + '/pageId/' + pageId,
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

                            $('ul#specialPages').append(li);
                            $('ul#specialPages li#'+ data.id).click(changeSelectedClass);
                            $('ul#specialPages li#0').remove();
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
    var id = $('ul#specialPages li.selected').attr('id');
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
    var id = $('ul#specialPages li.selected').attr('id');
    var offerId = $('ul#specialPages li.selected').attr('reloffer');
    var pageId = $("input#selctedPageId").val();
    var title = $('ul#specialPages li.selected').children('span').html();
    var pos = $('ul#specialPages li.selected').attr('relpos');
    $.ajax({
        url : HOST_PATH + "admin/specialpagesoffers/deletecode/id/" +id+ "/pos/"+pos+"/pageId/"+pageId,
        method : "post",
        dataType : "json",
        type : "post",
        success : function(json) {
            $('ul#specialPages li').remove();
            var li = '';

            for(var i in json) {
                li+= "<li class='ui-state-default' relpos='" + json[i].position 
                + "' reloffer='" + json[i].offerId + "' id='" + json[i].id + "' ><span>" 
                + json[i].title +"</span></li>";
            }

            $('select#offerlist').append('<option value="' + offerId + '">' + title  + '</option>');
            $('ul#specialPages').append(li);
            $('ul#specialPages li#'+id).addClass('selected');
            $('ul#specialPages li').click(changeSelectedClass);
            selectedElements();
        }
    });
}

function selectedElements() {
    var selectedRelated = new Array();
    $('ul#specialPages').find('li').each(function(index) {
        selectedRelated[index] = $(this).attr('reloffer');
    });
    $('#SearchedValueIds').val(selectedRelated);
}

