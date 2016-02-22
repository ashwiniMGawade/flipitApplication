$(document).ready(function(){
    var iStart = $.bbq.getState('iStart', true) || 0;
    var iSortCol = $.bbq.getState('iSortCol', true) || 1;
    var iSortDir = $.bbq.getState('iSortDir', true) || 'ASC';
    if ($("table#UrlSettingsListTbl").length) {
        getUrlSettings(iStart, iSortCol, iSortDir);
    }
});

var UrlSettingsListTbl = null;
var click = false;
function getUrlSettings(iStart,iSortCol,iSortDir) {
    $('#UrlSettingsListTbl').addClass('widthTB');
    UrlSettingsListTbl = $("table#UrlSettingsListTbl")
    .dataTable({
        "bLengthChange" : false,
        "bFilter" : false,
        "iDisplayStart" : iStart,
        "iDisplayLength" : 100,
        "oLanguage": {
              "sInfo": "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
        },
        "sPaginationType" : "bootstrap",
        "sAjaxSource" : HOST_PATH+"admin/urlsettings/get",
        "aoColumns" : [{
            "fnRender" : function(obj){
                return id = obj.aData.id;
            },
            "bVisible": false,
            "sType": 'numeric',
            "sWidth" : '0%'
        },
        {
            "fnRender" : function(obj) {
                if(obj.aData.url != null) {
                    return '<p editId=' + obj.aData.id + ' class="editId colorAsLink word-wrap-without-margin-offer"><a href="javascript:void(0)">' + obj.aData.url +'</a></p>';
                }
            },
            "sWidth" : '50%'
        },
        {
            "fnRender" : function(obj) {
                if(obj.aData.status != null) {
                    var html = (obj.aData.status == 1) ? 'Yes' : 'No';
                    return html;
                }
            },
            "bSortable" : false,
            "sWidth" : '30%'
        },
        {
            "fnRender" : function(obj) {
                if(obj.aData.hotjarStatus != null) {
                    var html = (obj.aData.hotjarStatus == 1) ? 'Yes' : 'No';
                    return html;
                }
            },
            "bSortable" : false,
            "sWidth" : '30%'
        },
        {
            "fnRender" : function(obj) {
                return "<a href='javascript:void(0);'" + "onclick='deleteUrlSetting(" + obj.aData.id  + ")' >"
                        + __('Delete') +
                        "</a>";
            },
            "bSortable" : false,
            "sWidth" : '20%'
        }],
        "fnDrawCallback" : function(obj) {
            var state = {};
            state[ 'iStart' ] = obj._iDisplayStart ;
            state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
            state[ 'iSortDir' ] = obj.aaSorting[0][1] ;

            jQuery("#UrlSettingsListTbl").find('tr').find('td:lt(2)').click(function () {
                var eId = $(this).parent('tr').find('p').attr('editid');
                state['eId'] = eId ;
                jQuery.bbq.pushState(state);
                window.location.href = HOST_PATH+"admin/urlsettings/edit/id/" + eId + "?iStart="+
                obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
                obj.aaSorting[0][1]+"&eId="+eId;
            });
        }
    });
}

function deleteUrlSetting(id) {
   bootbox.confirm("Are you sure you want to delete this VWO Tag?",'No','Yes',function(r) {
        if (!r) {
            return false;
        } else {
            addOverLay();
            $.ajax({
                type : "POST",
                url : HOST_PATH+"admin/urlsettings/delete",
                data : "id="+id
            }).done(function(msg) {
                window.location = HOST_PATH + 'admin/urlsettings';
            });
        }
    });
}
