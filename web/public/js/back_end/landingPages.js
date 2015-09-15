$(document).ready(function(){
    var iStart = $.bbq.getState('iStart', true) || 0;
    var iSortCol = $.bbq.getState('iSortCol', true) || 1;
    var iSortDir = $.bbq.getState('iSortDir', true) || 'ASC';
    if ($("table#LandingPagesListTbl").length) {
        getLandingPages(iStart, iSortCol, iSortDir);
    }
});

var LandingPagesListTbl = null;
var click = false;
function getLandingPages(iStart,iSortCol,iSortDir) {
    $('#LandingPagesListTbl').addClass('widthTB');
    LandingPagesListTbl = $("table#LandingPagesListTbl")
    .dataTable({
        "bLengthChange" : false,
        "bFilter" : false,
        "iDisplayStart" : iStart,
        "iDisplayLength" : 100,
        "oLanguage": {
              "sInfo": "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
        },
        "sPaginationType" : "bootstrap",
        "sAjaxSource" : HOST_PATH+"admin/landingpages/get",
        "aoColumns" : [{
            "fnRender" : function(obj){
                return id = obj.aData.id;
            },
            "bVisible": false,
            "sType": 'numeric'
        },
        {
            "fnRender" : function(obj) {
                if(obj.aData.title != null) {
                    return '<p editId=' + obj.aData.id + ' class="editId colorAsLink word-wrap-without-margin-offer"><a href="javascript:void(0)">' + obj.aData.title +'</a></p>';
                }
            }
        },
        {
            "fnRender" : function(obj) {
                if(obj.aData.shopName != null) {
                    return '<p editId=' + obj.aData.id + ' class="editId colorAsLink word-wrap-without-margin-offer"><a href="javascript:void(0)">' + obj.aData.shopName +'</a></p>';
                }
            }
        },
        {
            "fnRender" : function(obj) {
                if(obj.aData.permalink != null) {
                    return '<p editId=' + obj.aData.id + ' class="editId colorAsLink word-wrap-without-margin-offer"><a href="javascript:void(0)">' + obj.aData.permalink +'</a></p>';
                }
            }
        },
        {
            "fnRender" : function(obj) {
                if(obj.aData.shopOffersCount != null) {
                    return '<p editId=' + obj.aData.id + ' class="editId colorAsLink word-wrap-without-margin-offer"><a href="javascript:void(0)">' + obj.aData.shopOffersCount +'</a></p>';
                }
            }
        },
        {
            "fnRender" : function(obj) {
                if(obj.aData.shopClickoutCount != null) {
                    return '<p editId=' + obj.aData.id + ' class="editId colorAsLink word-wrap-without-margin-offer"><a href="javascript:void(0)">' + obj.aData.shopClickoutCount +'</a></p>';
                }
            }
        },
        {
            "fnRender" : function(obj) {
                if(obj.aData.status != null) {
                    var online = (obj.aData.status == 1) ? 'btn-primary' : '';
                    var offline = (obj.aData.status == 0) ? 'btn-primary' : '';
                    var html = "<div editId='" + obj.aData.id + "' class='btn-group'data-toggle='buttons-checkbox' style='margin-bottom:10px;'>"
                        + "<button class='btn "+ online +"' onClick='changeStatus("+ obj.aData.id+",this,\"online\")'>"+__('Yes')+"</button>"
                        + "<button class='btn "+ offline +"'onClick='changeStatus("+ obj.aData.id+",this,\"offline\")'>"+__('No')+"</button>"
                        + "</div>";
                    return html;
                }
            }
        },
        {
            "fnRender" : function(obj) {
                if(obj.aData.offlineSince != undefined && obj.aData.offlineSince != '') {
                    return '<p editId=' + obj.aData.id + ' class="editId colorAsLink word-wrap-without-margin-offer"><a href="javascript:void(0)">' + obj.aData.offlineSince.date + '</a></p>';
                } else {
                    return '';
                }
            }
        },
        {
            "fnRender" : function(obj) {
                return "<a href='javascript:void(0);'" + "onclick='deleteLandingPage(" + obj.aData.id  + ")' >"
                        + __('Delete') +
                        "</a>";
            },
            "bSortable" : false
        }],
        "fnDrawCallback" : function(obj) {
            var state = {};
            state[ 'iStart' ] = obj._iDisplayStart ;
            state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
            state[ 'iSortDir' ] = obj.aaSorting[0][1] ;

            jQuery("#LandingPagesListTbl").find('tr').find('td:lt(7)').click(function () {
                var eId = $(this).parent('tr').find('p').attr('editid');
                state['eId'] = eId ;
                jQuery.bbq.pushState(state);
                window.location.href = HOST_PATH+"admin/landingpages/edit/id/" + eId + "?iStart="+
                obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
                obj.aaSorting[0][1]+"&eId="+eId;
            });
        }
    });
}

function deleteLandingPage(id) {
   bootbox.confirm("Are you sure you want to delete this Landing Page?",'No','Yes',function(r) {
        if (!r) {
            return false;
        } else {
            addOverLay();
            $.ajax({
                type : "POST",
                url : HOST_PATH+"admin/landingpages/delete",
                data : "id="+id
            }).done(function(msg) {
                window.location = HOST_PATH + 'admin/landingpages';
            });
        }
    });
}