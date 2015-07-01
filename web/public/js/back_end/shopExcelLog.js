$(document).ready(init);
function init() {
    var iSearchText = $.bbq.getState( 'iSearchText' , true ) || undefined;
    var iStart = $.bbq.getState( 'iStart' , true ) || 0;
    var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 1;
    var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'ASC';
    getImportedExcelDetail(iSearchText,iStart,iSortCol,iSortDir);
    $('#shopExcelListTable_filter').css('display', 'none');
    $(window).bind( 'hashchange', function(e) {
        if(hashValue != location.hash && click == false){
            shopExcelListTable.fnCustomRedraw();
        }
    });
}

function ucfirst(str) {
    if(str!=null){
        var firstLetter = str.substr(0,1);
        return firstLetter.toUpperCase() + str.substr(1);
    }
}
 
var shopExcelListTable = null ;
var hashValue = "";
var click = false;
function getImportedExcelDetail(iSearchText,iStart,iSortCol,iSortDir){
    $('#shopExcelListTable').addClass('widthTB');
    $("ul.ui-autocomplete").css('display','none');
    addOverLay();
    shopExcelListTable = $("#shopExcelListTable").dataTable(
        {
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
            "aaSorting": [[ iSortCol , iSortDir ]],
            "sPaginationType" : "bootstrap",
            "sAjaxSource" : HOST_PATH + "admin/shop/get-shop-excel-log/searchText/"+ iSearchText +"/flag/0",
            "aoColumns" : [
                {
                    "fnRender" : function(obj) { 
                        return id = obj.aData;
                    },
                    "bVisible":    false ,
                    "bSortable" : false,
                    "sType": 'numeric'
                },  
               {
                "fnRender" : function(obj) {
                    var tag = "";
                    tag ="<p editId='' class='editId word-wrap-without-margin-widget store-offer'>"+obj.aData.updated_at.date+"</a></p>";
                    return tag;
                },
                "bSearchable" : true,
                "bSortable" : true
               },
               {
                    "fnRender" : function(obj) {
                        var tag = "";
                        tag = obj.aData.passCount;
                        return tag; 
                    },
                    "bSearchable" : false,
                    "bSortable" : true
                },
                {
                    "fnRender" : function(obj) {
                        var tag = "";
                        if(obj.aData){
                            tag = obj.aData.failCount;
                        } 
                        return tag; 
                    },
                    "bSearchable" : false,
                    "bSortable" : true
                }
            ],
            "fnPreDrawCallback": function( oSettings ) {
                $('#shopExcelListTable').css('opacity',0.5);
             },     
            "fnDrawCallback" : function(obj) {
                $('#shopExcelListTable').css('opacity',1);
                    var state = {};
                    state[ 'iStart' ] = obj._iDisplayStart ;
                    state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
                    state[ 'iSortDir' ] = obj.aaSorting[0][1] ;
                    state[ 'iSearchText' ] = iSearchText;
                    $("#SearchcodeAlert").val(iSearchText);
                    
                    if(iSearchText == undefined){
                        $.bbq.removeState( 'iSearchText' );
                    }
                  
                    $.bbq.pushState( state );
                    hashValue = location.hash;
                    var aTrs = shopExcelListTable.fnGetNodes();
    
                    for ( var i=0 ; i<aTrs.length ; i++ )
                    {
                        $editId = $(aTrs[i]).find('p').attr('editid');
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
