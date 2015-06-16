$(document).ready(init);
function init() {
    $("#searchWidget").select2({
        placeholder: __("Search widget"),
        minimumInputLength: 1,
        ajax: { 
            url: HOST_PATH + "admin/widget/searchkey",
            dataType: 'json',
            data: function(term, page) {
                return {
                    keyword: term
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
            $("#searchWidget").val(data);
            return data; 
        },
    });
    $('.select2-search-choice-close').click(function() {
        $('input#searchWidget').val('');
        getWidgetList(undefined,0,1,'asc');
    });
    var iSearchText = $.bbq.getState('iSearchText' , true) || undefined;
    var iStart = $.bbq.getState('iStart' , true) || 0;
    var iSortCol = $.bbq.getState('iSortCol' , true) || 1;
    var iSortDir = $.bbq.getState('iSortDir' , true) || 'ASC';
    getWidgetList(iSearchText,iStart,iSortCol,iSortDir);
    $('#searchButton').click(searchByWidget);
    $('form#searchform').submit(function() {
        return false;
    });
    $("input#searchWidget").keypress(function(e) {
        if (e.which == 13) {
            getWidgetList($(this).val(),0,1,'asc');
        }
    });
    $(window).bind('hashchange', function(e) {
        if (hashValue != location.hash && click == false) {
            widgetListtable.fnCustomRedraw();
        }
    });
}

function addNewWidget(e) {
    e.preventDefault();
    CKEDITOR.instances.content.updateElement();
    var data = $('#createWidget').serialize(true);
    $('#textValue').val(data);
}

function ucfirst(str) {
    if (str != null) {
    var firstLetter = str.substr(0,1);
        return firstLetter.toUpperCase() + str.substr(1);
    }
}

var widgetListtable = null;
var hashValue = "";
var click = false;
function getWidgetList(iSearchText,iStart,iSortCol,iSortDir) {
    $('#widgetListtable').addClass('widthTB');
    $("ul.ui-autocomplete").css('display','none');
    addOverLay();
    widgetListtable = $("#widgetListtable").dataTable({
        "bLengthChange" : false,
        "bInfo" : true,
        "bFilter" : true,
        "bDestroy" : true,
        "bProcessing" : false,
        "bServerSide" : true,
        "iDisplayLength" : 100,
        "oLanguage": {
              "sInfo": "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
        },
        "iDisplayStart" : iStart,
        "aaSorting": [[iSortCol , iSortDir]],
        "sPaginationType" : "bootstrap",
        "sAjaxSource" : HOST_PATH + "admin/widget/widgetlist/searchText/"+ iSearchText +"/flag/0",
        "aoColumns" : [{
            "fnRender" : function(obj) {
                    return id = obj.aData.id;
                },
                "bVisible":    false ,
                "bSortable" : false,
                "sType": 'numeric'
            }, {
                "fnRender" : function(obj) {
                    var editId = obj.aData.id;
                    if (obj.aData.showWithDefault == '0') {
                        editId = '';
                    }
                    var tag = "";
                    tag ="<a editId='" + editId + "' href='javascript:void(0);'>"
                    + "<p editId='" + editId + "' class='editId word-wrap-without-margin-widget'>" 
                    + ucfirst(obj.aData.title)+ "</p></a>";
                    return tag;
                },
                "bSortable" : true
            }, {
                "fnRender" : function(obj) {
                    var editId = obj.aData.id;
                    if (obj.aData.showWithDefault == '0') {
                        editId = '';
                    }
                    var tag = "";
                    if (obj.aData.content && obj.aData.showWithDefault == '1') {
                        tag = "<a editId='" + editId + "' href='javascript:void(0);'>" +"Yes" + "</a>";
                    } else { 
                        tag = "<a editId='" + editId + "' href='javascript:void(0);'>"+"No"+"</a>";
                    }
                    return tag;
                },
                "bSearchable" : false,
                "bSortable" : true
            }
        ],
        "fnPreDrawCallback": function(oSettings) {
            $('#widgetListtable').css('opacity',0.5);
        },      
        "fnDrawCallback" : function(obj) {
            $('#widgetListtable').css('opacity',1);
            var state = {};
            state['iStart'] = obj._iDisplayStart;
            state['iSortCol'] = obj.aaSorting[0][0];
            state['iSortDir'] = obj.aaSorting[0][1];
            state['iSearchText'] = iSearchText;
            $("#widgetListtable").find('tr').find('td:lt(2)').click(function (e) {  
                var el = e.target  ? e.target :  e.srcElement;
                if (el.tagName != "BUTTON") {
                    var eId = $(this).parent('tr').find('p, a').attr('editid');
                    $('p', $(this))
                    if (eId!= '') {
                        state['eId'] = eId;
                        $.bbq.pushState(state);
                        click = true;
                        window.location.href = HOST_PATH + "admin/widget/editwidget/id/" + eId+ "?iStart="+
                        obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
                        obj.aaSorting[0][1]+"&iSearchText="+iSearchText+"&eId="+eId
                    }
                }
            });
            $("#SearchWidget").val(iSearchText);
            if (iSearchText == undefined) {
                $.bbq.removeState('iSearchText');
            }
            $.bbq.pushState(state);
            hashValue = location.hash;
            var aTrs = widgetListtable.fnGetNodes();
            for (var i=0; i<aTrs.length; i++) {
                var editId = $(aTrs[i]).find('p').attr('editid');
                if (editId == $.bbq.getState('eId' , true)) {
                    $(aTrs[i]).find('td').addClass('row_selected');
                }
            }
            if ($('td.row_selected').length > 0) {
                var top = $('td.row_selected').offset().top;
            }
            var windowHeight = $(window).height() / 2 - 50;
            window.scrollTo(0, top - windowHeight);
        },
        "fnInitComplete" : function(obj) {
            removeOverLay();
            $('td.dataTables_empty').html(__('No record found !'));
            $('td.dataTables_empty').removeAttr('style');
            $('td.dataTables_empty').unbind('click');
        },
        "fnServerData" : function(sSource, aoData, fnCallback) {
            $.ajax({
                "dataType" : 'json',
                "type" : "POST",
                "url" : sSource,
                "data" : aoData,
                "success" : fnCallback
            });
        }
    });
}

function searchByWidget() {
    var searchString = $("#searchWidget").val();
    if (searchString == '' || searchString == null) {
        searchString = undefined;
    }
    getWidgetList(searchString, 0, 0, 'asc');
}
