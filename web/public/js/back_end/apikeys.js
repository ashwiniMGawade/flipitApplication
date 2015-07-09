$(document).ready(function(){
    var iStart = $.bbq.getState('iStart', true) || 0;
    var iSortCol = $.bbq.getState('iSortCol', true) || 1;
    var iSortDir = $.bbq.getState('iSortDir', true) || 'ASC';
    if ($("table#ApiKeysListTbl").length) {
        getApiKeyList(iStart, iSortCol, iSortDir);
    }
});

var ApiKeysListTbl = null;
var hashValue = "";
var click = false;
function getApiKeyList(iStart,iSortCol,iSortDir) {
    $('#ApiKeysListTbl').addClass('widthTB');
    ApiKeysListTbl = $("table#ApiKeysListTbl")
    .dataTable({
        "bLengthChange" : false,
        "bFilter" : false,
        "iDisplayStart" : iStart,
        "iDisplayLength" : 15,
        "oLanguage": {
              "sInfo": "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
        },
        "sPaginationType" : "bootstrap",
        "sAjaxSource" : HOST_PATH+"admin/apikeys/get",
        "aoColumns" : [{
            "fnRender" : function(obj){
                return id = obj.aData.id;
            },
            "bVisible": false,
            "sType": 'numeric'
         },
         {
            "fnRender" : function(obj) {
                if(obj.aData.apiKey != null) {  
                    return '<p>' + obj.aData.apiKey +'</p>';
                }
            }
        },
        {
            "fnRender" : function(obj) {
                if(obj.aData.createdAt != undefined && obj.aData.createdAt != '') {
                    return obj.aData.createdAt.date;
                }
            }
        },
        {
            "fnRender" : function(obj) {
                return "<a href='javascript:void(0);'" + "onclick='deleteApiKey(" + obj.aData.id  + ")' >"
                        + __('Delete') +
                        "</a>";
            },
            "bSortable" : false
        }]
    });
}

function addApiKey()
{
    $.ajax({
        type : "GET",
        url : HOST_PATH+"admin/apikeys/create"
    }).done(function() {
        window.location = HOST_PATH + 'admin/apikeys';
    });
}

function deleteApiKey(id) {
    bootbox.confirm("Are you sure you want to delete this Api Key?",'No','Yes',function(r) {
        if (!r) {
            return false;
        } else {
            addOverLay();
            $.ajax({
                type : "POST",
                url : HOST_PATH+"admin/apikeys/delete",
                data : "id="+id
            }).done(function(msg) {
                window.location = HOST_PATH + 'admin/apikeys';
            });
        }
    });
}
