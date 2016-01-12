$(document).ready(function() {
    $("#PartOneOfferlist").select2({placeholder: __("Search a offer")});
    $("#PartTwoOfferlist").select2({placeholder: __("Search a offer")});

    $("#PartOneOfferlist").change(function(){
        $("#campaignOffersOne #selctedOffer").val($(this).val());
        $("#campaignOffersOne #selctedOfferText").val($(this).find('option[value='+$(this).val()+']').text());
        addSelectedClassOnButton(1, 'campaignOffersOne');
    });

    $("#PartTwoOfferlist").change(function(){
        $("#campaignOffersTwo #selctedOffer").val($(this).val());
        $("#campaignOffersTwo #selctedOfferText").val($(this).find('option[value='+$(this).val()+']').text());
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

    setTimeout(initiateDatatables(), 4000);
    $( "#partOneCode" ).disableSelection();
    $( "#partTwoCode" ).disableSelection();
});

function initiateDatatables() {
    if($('#partOneOffers').length>0) {
        $('#partOneOffers').dataTable({"bFilter": false, "bPaginate": false}).rowReordering();
    }
    if($('#partTwoOffers').length>0) {
        $('#partTwoOffers').dataTable({"bFilter": false, "bPaginate": false}).rowReordering();
    }
}

function searchByTxt() {
    $("ul.ui-autocomplete").css('display','none');
    $("ul.ui-autocomplete").html('');
}

function addNewOffer(element) {
    $(element).attr('disabled' ,"disabled");
    parentId = $(element).closest('.wrap').attr('id');
    console.log(parentId);
    var flag =  '#'+parentId+' .addNewOffer';
    addSelectedClassOnButton(flag, parentId);

    if($('#'+parentId+' table tr').length > 50) {
        bootbox.alert(__('Part one offer list can have maximum 50 records, please delete one if you want to add more offers'));
        $('#'+parentId+' .addNewOffer').removeAttr('disabled');
        return false;
    } else {
        if($("#"+parentId+" input#selctedOffer").val() == '' || $("#"+parentId+" input#selctedOffer") == undefined) {
            bootbox.alert(__('Please select an offer'));
            $('#'+parentId+' .addNewOffer').removeAttr('disabled');
            return false;
        } else {
            var id = $("#"+parentId+" input#selctedOffer").val();
            var title = $("#"+parentId+" input#selctedOfferText").val();
            var existingOffers = $("#"+parentId+" input#SearchedValueIds").val();

            if($.inArray(id, existingOffers) !== -1) {
                bootbox.alert(__('This offer already exists in the list'));
            }
            else {
                $("#"+parentId+" table tr td.dataTables_empty").parent().remove();
                var position = $('#'+parentId+' table tr').not('td.dataTables_empty').length;
                var className = ($('#'+parentId+' table tr:last').hasClass("odd")) ? "even" : "odd";
                var data = {'postion':position, 'offerId' : id, 'title': title};
                var name = (parentId === "campaignOffersOne" ) ? 'partOneOffers[]' : 'partTwoOffers[]';
                lockImage = HOST_PATH + "public/images/back_end/stock_lock.png";
                image = "<img src=" + lockImage + " height='20' style='float:right' width='20'>";

                var tr = '<tr id="row_'+position+'" data-position="'+position+'" class="'+className+'">'+
                    '<td class="sorting_1 sorting_2">'+position+'</td>'+
                    '<td><input type="hidden" name="'+name+'" value="'+data.offerId+'">'+data.title.replace(/\\/g, '')+'</td>'+
                        '<td><input type="button" class="btn ml10 mb10" onclick="deleteOne('+"row_"+position+', '+parentId+')" value="Delete"></td>'+
                        '</tr>';
                $("#"+parentId+" table tbody").append(tr);

                $("#"+parentId+" div.combobox a.select2-choice").children('span').html('');

                $("#"+parentId+" select option[value='"+  id +"']").remove();

                $("#"+parentId+" input#selctedOffer").val('');

                selectedElements(parentId);

            }
            $('#'+parentId+' .addNewOffer').removeAttr('disabled');
        }
    }
}

function changeSelectedClass(parentId) {

    $("#"+parentId+" ul li").removeClass('selected');
    $(this).addClass('selected');
    //apply selected class on current button
    addSelectedClassOnButton(2, parentId);
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

function deleteOne(id, parentId) {
    $("#"+parentId + " #" + id).remove();
}


function selectedElements(parentId) {
    var selectedRelated = new Array();
    $('#'+parentId+ ' ul').find('li').each(function(index) {
        offerid = $(this).attr('reloffer');
        if ($.inArray(offerid, selectedRelated) == -1) {
            selectedRelated[index] = offerid;
        }
    });
    $('#'+parentId+ ' #SearchedValueIds').val(selectedRelated);
}

