$(document).ready(function() {
    var iStart = $.bbq.getState('iStart', true) || 0;
    var iSortCol = $.bbq.getState('iSortCol', true) || 1;
    var iSortDir = $.bbq.getState('iSortDir', true) || 'DESC';
    getNewsletterCampaignList(iStart, iSortCol, iSortDir);
});

var newsletterCampaignListTable = null ;
var id;
var hashValue = "";
var click = false;
function getNewsletterCampaignList(iStart,iSortCol,iSortDir) {
    $("ul.ui-autocomplete").css('display','none');
    $('#newsletterCampaignListTable').addClass('widthTB');

    newsletterCampaignListTable = $("#newsletterCampaignListTable").dataTable({
        "bLengthChange" : false,
        "bInfo" : true,
        "bFilter" : false,
        "bDestroy" : true,
        "bProcessing" : false,
        "bServerSide" : true,
        "iDisplayStart" : iStart,
        "iDisplayLength" : 100,
        "bDeferRender": true,
        "oLanguage": {
            "sInfo": "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
        },
        "aaSorting": [[ iSortCol , iSortDir ]],
        "sPaginationType" : "bootstrap",
        "sAjaxSource" : HOST_PATH + "admin/newslettercampaigns/getNewsletterCampaignList",
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
            $(nRow).attr( "data-id", aData.id);
            return nRow;
        },
        "aoColumns" : [
            {
                "fnRender" : function(obj) {

                    return "<a rel="+ obj.aData.id +" href='javascript:void(0);'>" + ucfirst(obj.aData.campaignName) + "</a>" ;
                },
                "bSortable" : true
            },
            {
                "fnRender" : function(obj) {
                    return "<a href='javascript:void(0);'>" + ucfirst(obj.aData.campaignSubject) + "</a>" ;
                },
                "bSortable" : true
            },
            {
                "fnRender" : function(obj) {
                    if(obj.aData.scheduledStatus == true)
                    {
                        return "<span class='success'>" + __("Scheduled") + "</span>" ;
                    }else{
                       return "<span href='javascript:void(0);' class='error'>" + __("Not Scheduled") + "</span>" ;
                    }
                },
                "bSortable" : true
            },
            {
                "fnRender" : function(obj) {
                    if(obj.aData.scheduledStatus == true) {
                        return "<a href='javascript:void(0);'>" + obj.aData.scheduledTime.date + "</a>";
                    }else{
                        return "<span href='javascript:void(0);' class='error'>-</span>" ;
                    }

                },
                //"bSearchable" : false,
                "bSortable" : true
            },
            {
                "fnRender" : function(obj) {
                    if(obj.aData.warnings == 'OK') {
                        return "<span class='success'>" + obj.aData.warnings + "</span>";
                    } else {
                        return "<span class='error'>" + obj.aData.warnings + "</span>";
                    }

                },
                //"bSearchable" : false,
                "bSortable" : true
            },
            {
                "fnRender" : function(obj) {

                    return "<a  onclick='deleteVisitor("+obj.aData.id+")' href='javascript:void(0)'>" + __('Delete') + "</a>" ;

                },
                //"bSearchable" : false,
                "bSortable" : false
            } ],

        "fnPreDrawCallback": function( oSettings ) {
            $('#newsletterCampaignListTable').css('opacity',0.5);
        },
        "fnDrawCallback" : function(obj) {
            $('#newsletterCampaignListTable').css('opacity',1);

            var state = {};
            state[ 'iStart' ] = obj._iDisplayStart ;
            state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
            state[ 'iSortDir' ] = obj.aaSorting[0][1] ;

            $("#newsletterCampaignListTable").find('tr').find('td:lt(6)').click(function () {
                var eId = $(this).parent('tr').find('a').attr('rel');
                state[ 'eId' ] = eId ;
                click = true;
                $.bbq.pushState( state );
                window.location.href = HOST_PATH + "admin/newslettercampaigns/edit/id/" + eId+ "?iStart="+
                obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
                obj.aaSorting[0][1];
            });

            $.bbq.pushState( state );
            hashValue = location.hash;

            var aTrs = newsletterCampaignListTable.fnGetNodes();

            for ( var i=0 ; i<aTrs.length ; i++ )
            {
                $editId = $(aTrs[i]).find('a').attr('rel');
                if ( $editId == $.bbq.getState( 'eId' , true ) )
                {
                    $(aTrs[i]).find('td').addClass('row_selected');
                }
            }

            if($('td.row_selected').length > 0){
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

            /*$("form#userRegister").each(function() { this.reset(); });*/
            //removeOverLay();
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