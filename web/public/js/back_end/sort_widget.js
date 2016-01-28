$(document).ready(function() {
    $("#widgetCategories").select2();
    $("#widgetCategoryType").select2({"placeholder":  __("Select")});

    $("#widgetCategories").change(function() {
        $('#widgetType').val($(this).val());
        addWidgetInSortList();
    });

    $("#widgetCategoryType").change(function() {
        $('#widgetCategoryType').val($(this).val());
        addWidgetInSortList();
    });


    $("#widgetslist").select2({placeholder: __("Search a widget")});
    $("#widgetslist").change(function() {
        $("#selctedWidget").val($(this).val());
        addSelectedClassOnButton(1);
    });
    addSelectedClassOnButton(1);
    selectedElements();
    $('ul#sort-widgets-list li').click(changeSelectedClass);
    $("#sort-widgets-list").sortable();
    $("#sort-widgets-list").disableSelection();
    $("#sort-widgets-list").on("sortstop", function(event, ui) {
        var widgetId = new Array();
        $('.ui-state-default').each(function() {
            widgetId.push($(this).attr('relwidget'));
        });
        $('div.image-loading-icon').append("<img id='img-load' src='" +  HOST_PATH  + "/public/images/validating.gif'/>");
        var widgetId = widgetId.toString();
        $.ajax({
            type : "POST",
            url : HOST_PATH + "admin/widget/save-position",
            method : "post",
            dataType : 'json',
            data: {offersIds: widgetId, widgetType: $('#widgetType').val()},
            success : function(json) { 
                $('#img-load').remove();
                $("#sort-widgets-list").sortable("refresh");
                $("#sort-widgets-list").sortable("refreshPositions");
                $('ul#sort-widgets-list li').remove();
                var li = '';
                if (json!='') {
                    for (var i in json) {
                        li+= "<li class='ui-state-default' relpos='" + json[i].position 
                        + "' relwidget='" + json[i]['widget'].id + "' id='" + json[i].id + "' ><span>" 
                        + json[i]['widget'].title +"</span></li>";
                    }
                    $('ul#sort-widgets-list').append(li);
                    $('ul#sort-widgets-list li').click(changeSelectedClass);
                }
                bootbox.alert(__('Offers successfully updated.'));
                setTimeout(function() {
                  bootbox.hideAll();
                }, 3000);
            }
        });
    });
});

function addWidgetInSortList() {
    $('body').append("<div id='overlay'><img id='img-load' src='" +  HOST_PATH  + "/public/images/front_end/spinner_large.gif'/></div>");
    $.ajax({
        type : "POST",
        url : HOST_PATH + "admin/widget/add-widget-in-sort-list",
        method : "post",
        dataType : 'json',
        data: '',
        success : function(json) {
            removeOverLay();
            setTimeout(loadSelectedCategoryWidgets, 1000);
        }
    });
}

function loadSelectedCategoryWidgets() {
    var widgetType =  $('#widgetType').val();
    var widgetCategoryType =  $('#widgetCategoryType').val();
    redirectURL = HOST_PATH + "admin/widget/sort-widget/widgetType/" +  widgetType;
    if (widgetCategoryType !== '') {
        redirectURL += "/widgetCategoryType/"+widgetCategoryType;
    }
    window.location.href =  redirectURL;
}

function changeSelectedClass() {
    $('ul#sort-widgets-list li').removeClass('selected');
    $(this).addClass('selected');
    addSelectedClassOnButton(2);
}

function addSelectedClassOnButton(selectedOption) {
    if (selectedOption == 1) {
        $('button#deleteOne').removeClass('btn-primary');
        $('button#addNewWidget').addClass('btn-primary');
    } else if (selectedOption == 2) {
        $('button#deleteOne').addClass('btn-primary');
        $('button#addNewWidget').removeClass('btn-primary');
    } else {
        $('button#deleteOne').removeClass('btn-primary');
        $('button#addNewWidget').removeClass('btn-primary');
        $(selectedOption).addClass('btn-primary');
    }
}

String.prototype.escapeSingleQuotes = function () {
    if (this == null) return null;
    return this.replace(/'/g, "\\'");
};
function addNewWidget() {
    var selectedOption =  '#addNewWidget';
    $('#addNewWidget').attr('disabled' ,"disabled");
    addSelectedClassOnButton(selectedOption);
    if ($("input#selctedWidget").val()=='' || $("input#selctedWidget").val()==undefined) {
        bootbox.alert(__('Please select an widget'));
        $('#addNewWidget').removeAttr('disabled');
    } else {
        var id = $("input#selctedWidget").val();
        var widgetType = $("input#widgetType").val();
        var widgetCategoryType =  $('#widgetCategoryType').val();
        $.ajax({
            url : HOST_PATH + "admin/widget/add-widget-in-sort-list/id/" + id + '/widgetType/' + widgetType + '/widgetCategoryType/' + widgetCategoryType,
            method : "post",
            dataType : "json",
            type : "post",
            success : function(data) {
                if (data=='2' || data==2) {
                    bootbox.alert(__('This widget already exists in the list'));
                }
                else if (data=='0' && data==0) {
                    bootbox.alert(__('This widget does not exist'));
                } else {
                    var li  = "<li class='ui-state-default'  relpos='" + data.position 
                    + "' relwidget='" + data.widgetId + "' id='" + data.id + "' ><span>" 
                    + data.title.replace(/\\/g, '')  + "</span></li>";
                    $('ul#sort-widgets-list').append(li);
                    $('ul#sort-widgets-list li#'+ data.id).click(changeSelectedClass);
                    $('ul#sort-widgets-list li#0').remove();
                    $('div.coupon-sidebar-heading a.select2-choice').children('span').html(''); 
                    $("#widgetslist option[value='"+  id +"']").remove();
                    $("input#selctedWidget").val('');
                    selectedElements();
                }
                $('#addNewWidget').removeAttr('disabled');
            }
        });
    }
}

function deleteOne() {
    var selectedOption =  '#deleteOne';
    $('#deleteOne').attr('disabled' ,"disabled");
    addSelectedClassOnButton(selectedOption);
    var id = $('ul#sort-widgets-list li.selected').attr('id');
    if (parseInt(id) > 0) {
        bootbox.confirm(__("Are you sure you want to delete this code?"),__('No'),__('Yes'),function(r) {
        if (!r) {
            $('#deleteOne').removeAttr('disabled');
            return false;
        } else {
            deleteWidget();
            $('#deleteOne').removeAttr('disabled');
        }
    });
} else {
    bootbox.alert(__('Please select an widget from list'));
    $('#deleteOne').removeAttr('disabled');
    }
}

function deleteWidget() {
    var id = $('ul#sort-widgets-list li.selected').attr('id');
    var widgetId = $('ul#sort-widgets-list li.selected').attr('relwidget');
    var widgetType = $("input#widgetType").val();
    var title = $('ul#sort-widgets-list li.selected').children('span').html();
    var pos = $('ul#sort-widgets-list li.selected').attr('relpos');
    $.ajax({
        url : HOST_PATH + "admin/widget/delete-widget/id/" +id+ "/pos/"+pos+"/widgetType/"+widgetType,
        method : "post",
        dataType : "json",
        type : "post",
        success : function(json) {
            $('ul#sort-widgets-list li').remove();
            var li = '';
            for(var i in json) {
                li+= "<li class='ui-state-default' relpos='" + json[i].position 
                + "' relwidget='" + json[i]['widget'].id + "' id='" + json[i].id + "' ><span>" 
                + json[i]['widget'].title +"</span></li>";
            }
            $('select#widgetslist').append('<option value="' + widgetId + '">' + title  + '</option>');
            $('ul#sort-widgets-list').append(li);
            $('ul#sort-widgets-list li#'+id).addClass('selected');
            $('ul#sort-widgets-list li').click(changeSelectedClass);
            selectedElements();
        }
    });
}

function selectedElements() {
    var selectedRelated = new Array();
    $('ul#sort-widgets-list').find('li').each(function(index) {
        selectedRelated[index] = $(this).attr('relwidget');
    });
    $('#SearchedValueIds').val(selectedRelated);
}

