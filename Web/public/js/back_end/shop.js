
var validateNewShop = null; 
var validRules = {
    label : "",
    url : "",
    position:""
};
var focusRules = {
    label : "",
    url : "",
    position:""
};
/**
 * execute when document is loaded 
 * @author spsingh updated by karj
 */
$(document).ready(init);
/**
 * initialize all the settings after document is ready
 * @author spsingh updated by karj
 */
function init(){
    $("#searchShop").select2({
        placeholder: __("Search shop"),
        minimumInputLength: 1,
        ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
            url: HOST_PATH + "admin/offer/searchtopfiveshop",
            dataType: 'json',
            data: function(term, page) {
                return {
                 keyword: term,
                 flag: 0
                };
            },
            type: 'post',
            results: function (data, page) { // parse the results into the format expected by Select2.
            // since we are using custom formatting functions we do not need to alter remote JSON data
            return {results: data};
            }
        },
        formatResult: function(data) {
            return data; 
        },
        formatSelection: function(data) { 
            $("#searchShop").val(data);
            return data; 
        },
    });
    $('.select2-search-choice-close').click(function(){
        $('input#searchShop').val('');
        getShops($(this).val(),0,1,'asc');
    });
    $("input#searchShop").keypress(function(e) {
        if (e.which == 13) {
           getShops($(this).val(),0,1,'asc');
        }
    });
    // display shop list 
    var iSearchText = $.bbq.getState( 'iSearchText' , true ) || undefined;
    var iStart = $.bbq.getState( 'iStart' , true ) || 0;
    var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 1;
    var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'ASC';
    getShops(iSearchText,iStart,iSortCol,iSortDir) ;
    
    $('#searchByShop').click(searchByShop);
    
    $('form#searchform').submit(function() {
        return false;
    });

    $(window).bind( 'hashchange', function(e) {
        if(hashValue != location.hash && click == false){
            shopListTable.fnCustomRedraw();
        }
    });
    
    validateGlobalExportForm();

}

/**
 * get shops from database and display in list
 * 
 * @author mkaur updateb by karj
 * @version 1.0
 */
var shopListTable = $('#shopListTable').dataTable();
var hashValue = "";
var click = false;


function getShops(iSearchText,iStart,iSortCol,iSortDir) {
    //$('#shopListTable tr:gt(0)').remove();
    addOverLay();
    $("ul.ui-autocomplete").css('display','none');
    $("ul.ui-autocomplete").html('');
    $('#shopList').attr('style','');
    //"ui-autocomplete ui-menu ui-widget ui-widget-content ui-corner-all"
    $('#createNewShop').addClass('display-none');
    $('#shopList').removeClass('display-none');
    
    var searchText = $('#searchShop').val()=='' ? undefined : $('#searchShop').val();
    
    shopListTable = $("#shopListTable")
    .dataTable(
            {
                "bLengthChange" : false,
                "bInfo" : true,
                "bFilter" : true,
                "bDestroy" : true,
                "bProcessing" : false,
                "bServerSide" : true,
                "iDisplayStart" : iStart,
                "iDisplayLength" :100,
                "oLanguage": {
                      "sInfo": "<b>_START_-_END_</b> of <b>_TOTAL_</b>"
                },
                "bDeferRender": true,
                "aaSorting": [[ iSortCol , iSortDir ]],
                "sPaginationType" : "bootstrap",
                "sAjaxSource" : HOST_PATH+"admin/shop/getshop/searchText/"+ escape(iSearchText) + '/flag/0',
                "aoColumns" : [
                        {
                            "fnRender" : function(obj) {
                                
                                var id = null;
                                return id = obj.aData.id;
                        
                            },
                            "bVisible":    false ,
                            "sType": 'numeric'
                            
                        },{
                            "fnRender" : function(obj) {
                                
                                var tag = "<p editId='" + obj.aData.id + "' class='colorAsLink word-wrap-without-margin-network'><a href='javascript:void(0);'>" + ucfirst(obj.aData.name)+"</a>"; 
                                return tag;
                             },
                            "bSearchable" : true,
                            "bSortable" : true
                        },{
                            "fnRender" : function(obj) {
                                var tag = "<a href='javascript:void(0);'>" + obj.aData.permaLink + "</a>";
                                return tag;
                             },
                            "bSearchable" : true,
                            "bSortable" : true
                        },{
                            "fnRender" : function(obj) {
                                var prog='';
                                if(obj.aData.affliateProgram==true){
                                    prog = "<a href='javascript:void(0);'>" +"Yes" + "</a>";
                                }
                                else{
                                prog = "<a href='javascript:void(0);'>"+"No"+"</a>";
                                }
                                var tag = prog ;
                                return tag;
                              
                            },
                            "bSearchable" : true,
                            "bSortable" : true
                        },{
                            "fnRender" : function(obj) {

                            var date = "";
                            if(obj.aData.created_at !=null && obj.aData.created_at !='undefined' ) {
                                var splitdate = obj.aData.created_at.date.split(" ");
                                if (obj.aData.created_at.date != null && splitdate[0] != '1970-01-01') {
                                    
                                        var date = obj.aData.created_at.date;
                            
                                
                                }
                            }   
                            return "<a href='javascript:void(0)'>" + date + "</a>";
                            },
                            "bSearchable" : true,
                            "bSortable" : true
                        },
                        {
                            "fnRender" : function(obj) {
                                var tag = '';
                                if (obj.aData.lastSevendayClickouts==null || obj.aData.lastSevendayClickouts=='' || obj.aData.lastSevendayClickouts==undefined) {
                                    tag = '';
                                } else {
                                    tag = "<a href='javascript:void(0);'>" + obj.aData.lastSevendayClickouts + "</a>";
                                }
                                return tag;
                             },
                            "bSearchable" : true,
                            "bSortable" : true
                        },
                        {
                            "fnRender" : function(obj) {
                                var tag = "<a href='javascript:void(0);'>" + obj.aData.shopAndOfferClickouts + "</a>";
                                return tag;
                             },
                            "bSearchable" : true,
                            "bSortable" : true
                        },
                        {
                            "fnRender" : function(obj) {
                                var tag = '';
                                if(obj.aData.affliatenetwork==null || obj.aData.affliatenetwork.name=='' || obj.aData.affliatenetwork.name==undefined){
                                    tag = '';
                            }
                            else{
                                tag = "<p class='word-wrap-without-margin'><a href='javascript:void(0);'>"+obj.aData.affliatenetwork.name+"</a></p>";
                            }
                                
                            return tag;
                            },
                            "bSearchable" : true,
                            "bSortable" : true
                        },{
                            
                            "fnRender" : function(obj) {
                                
                                if(obj.aData.discussions){
                                 return  "<a href='javascript:void(0);'>" +"Yes" + "</a>";
                                }
                                
                                return  "<a href='javascript:void(0);'>"+"No"+"</a>";
                              
                            },
                            "bSearchable" : true,
                            "bSortable" : true
                        },{
                            "fnRender" : function(obj) {
                                
                                if(obj.aData.showSignupOption){
                                    
                                    return "<a href='javascript:void(0);'>" +"Yes" + "</a>";
                                }
                                
                                return  "<a href='javascript:void(0);'>"+"No"+"</a>";
                            
                              
                            },
                            "bSearchable" : true,
                            "bSortable" : true
                        },{
                            "fnRender" : function(obj) {
                                /*var tag='';
                                if(obj.aData.status==true){
                                    tag = "<a href='javascript:void(0);'>"+'Yes'+"</a>";
                                }
                                else{
                                    tag = "<a href='javascript:void(0);'>"+'No'+"</a>";
                                }
                                return tag;*/
                                var onLine = 'btn-primary';
                                var offLine = '';
                                if((obj.aData.status)==false){
                                    var onLine = '';
                                    var offLine = 'btn-primary';
                                }   
                                    var html = "<div editId='" + obj.aData.id + "' class='btn-group'data-toggle='buttons-checkbox' style='padding-bottom:16px;margin-top:0px; width:78px;'>"
                                            + "<button class='btn "+ onLine +"' onClick='changeStatus("+ obj.aData.id+",this,\"online\")'>"+__('Yes')+"</button>"
                                            + "<button class='btn "+ offLine +"'onClick='changeStatus("+ obj.aData.id+",this,\"offline\")'>"+__('No')+"</button>"
                                            + "</div>";
                                    
                                    return html;
                            },
                            "bSearchable" : true,
                            "bSortable" : true
                            
                        },{
                            "fnRender" : function(obj) {
                                var tag = '';
                                if(obj.aData.offlineSicne==undefined || obj.aData.offlineSicne==null || obj.aData.offlineSicne==''){
                                    tag='';
                                }
                                else{

                                    var tag = "";
                                    if(obj.aData.offlineSicne !=null && obj.aData.offlineSicne !='undefined' ) {
                                        var splitdate = obj.aData.offlineSicne.date.split(" ");
                                        if (obj.aData.offlineSicne.date != null && splitdate[0] != '1970-01-01') {
                                            var tag = obj.aData.offlineSicne.date;  
                                        }
                                    }
                                }
                                return tag;
                            },
                            "bSearchable" : true,
                            "bSortable" : true
                        }, {
                            "fnRender" : function(obj) {
                                var html = "<a href='javascript:void(0)' onclick='moveToTrash("+obj.aData.id+");' id='deleteshop'>"+__("Delete")+ "</a>";
                                return html;
                            },
                            "bSearchable" : false,
                            "bSortable" : false
                        
                        } ],
                "fnPreDrawCallback": function( oSettings ) {
                    $('#shopListTable').css('opacity',0.5);
                 },     
                "fnDrawCallback" : function(obj) {
                    $('#shopListTable').css('opacity',1);
            
                 
                    var state = {};
                    state[ 'iStart' ] = obj._iDisplayStart ;
                    state[ 'iSortCol' ] = obj.aaSorting[0][0] ;
                    state[ 'iSortDir' ] = obj.aaSorting[0][1] ;
                    state[ 'iSearchText' ] = iSearchText;
                    
                    $("#shopListTable").find('tr').find('td:lt(6)').click(function (e) {
                        
                        var el = e.target  ? e.target :  e.srcElement ;
                        
                        if(el.tagName != "BUTTON")
                        {
                            var eId = $(this).parent('tr').find('p').attr('editid');
                            state[ 'eId' ] = eId ;
                            $.bbq.pushState( state );
                            click = true;
                            window.location.href = HOST_PATH + "admin/shop/editshop/id/" + eId+ "?iStart="+
                            obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
                            obj.aaSorting[0][1]+"&iSearchText="+iSearchText+"&eId="+eId;
                        }
                    });
                    
                    // Set the state!
                    if(iSearchText == undefined){
                        $.bbq.removeState( 'iSearchText' );
                    }
                   
                    $.bbq.pushState( state );
                    hashValue = location.hash;
                    
                    var aTrs = shopListTable.fnGetNodes();
    
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
                    $("form#createShop").each(function() { this.reset(); });
                    $('td.dataTables_empty').html(__('No record found !'));
                    $('td.dataTables_empty').unbind('click');
                    removeOverLay();
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
/**
 * Call to edit function when user click on any row of the shop list
 */
function callToEdit() {
    var id  = $(this).parents('tr').children('td').children('p.editId').attr('editId');
    //var id =  $(this).children('td:eq(0)').children('p.editId').attr('editId');
    window.location.href = HOST_PATH+"admin/shop/editshop/id/" + id;
}
/**
 * bootstrap boot box for confirm messages
 * if true move to trah is called
 * @author kraj 
 */
function moveToTrash(id){
    bootbox.confirm(__("Are you sure you want to move this shop to trash?"),__('No'),__('Yes'),function(r){
        if(!r){
            return false;
        }
        else{
            deleteShop(id);
        }
        
    });
}

/**
 * when moveToTrash action in confirmed the ajax call to move the record according
 * to id
 * @auther mkaur
 * @param id
 * @version 1.0
 */
function deleteShop(id) {
    
    addOverLay();
    $.ajax({
        url : HOST_PATH + "admin/shop/movetotrash",
        method : "post",
        data : {
            'id' : id
        },
        dataType : "json",
        type : "post",
        success : function(data) {
            
            if (data != null) {
                
                window.location.href = "shop";
                
            } else {
                
                window.location.href = "shop";
            }
        }
    });
}
/**
 * change status of shops online/offline
 * @author blal
 */
function changeStatus(id,obj,status){
    addOverLay();
    $(obj).addClass("btn-primary").siblings().removeClass("btn-primary");
    $.ajax({
        type : "POST",
        url : HOST_PATH+"admin/shop/shopstatus",
        data : "id="+id+"&status="+status,
        success: function(ret)
        {
            if(ret && ret.date !== undefined)
            {
                $(obj).parents('td').next('td').html( "<a href='javascript:void(0);'>"+ ret.date +"</a>");
            }else {
                $(obj).parents('td').next('td').html( "");
            }
            if(ret.message == 1) {
                bootbox.alert(__('There is an how to guide for this shop. Please notify search to redirect!'));
                setTimeout(function(){
                bootbox.hideAll();
                }, 3000);
            }
        }
    }).done(removeOverLay);
}

/**
 * Function call when user click on shop search button 
 * or press enter 
 * @author kraj
 */
function searchByShop(){
    
    var searchArt = $("#searchShop").val();
    if(searchArt=='' || searchArt==null)  {
        
            searchArt = undefined;
        }
    getShops(searchArt,0,0,'asc');
}

function showGlobalExportPopUp()
{
    $('#globalExportPopUp').html('');
    customPopUp('globalExportPopUp');
        $.ajax({
            url : HOST_PATH + "admin/shop/global-export-xlx-download",
            method : "post",
            data : {},
            type : "post",
            success : function(data) {
                $('#globalExportPopUp').show();
                $('#globalExportPopUp').html(data);
            }
        });
      return false;
}

function sendExportPasswordEmail()
{
    $('body').append('<div id="export-password-modal"><div class="modal-backdrop  in"><div id="overlay"><img id="img-load" src="/public/images/ajax-loader2.gif"/></div></div></div>');
    $.ajax({
        url : HOST_PATH + "admin/shop/global-export-xlx-password",
        method : "post",
        data : {},
        type : "post",
        success : function(data) {
            $('#export-password-modal').remove();
            showModel("","","add");
        }
    });
}

function showModel(id,rootid,type){
    $('form#menuForm :input').val("");
    $('form#menuForm span#imageName').html('');
    $('form#menuForm div.m-item-popup-btm a.menuDelete').remove();
    removeBorders();
    $('#submitButton').attr('value','add');
    $('input#hid').val('');
    $('#myModal').modal('show');
}

function hideModel(){
    $('#myModal').modal('hide');
    return false;
}

function removeBorders(){
    $("div.mainpage-content-right").removeClass("error").removeClass('success')
    .prev("div").removeClass('focus').removeClass('error').removeClass('success') ;
}

function validateGlobalExportForm(){
    validateNewMenu = $("form#globalExportForm")
        .validate({ 
            errorClass : 'error',
            validClass : 'success',
            errorElement : 'span',
            ignore: ".ignore, :hidden",
            errorPlacement : function(error, element) {
                element.parent("div").prev("div")
                .html(error);
            },
            rules : {
                password : {
                required : true,
                    remote : {
                        url : HOST_PATH
                            + "admin/user/checkexportpassword",
                        type : "post",
                        beforeSend : function(xhr) {
                        },
                        complete : function(data) {
                            if (data.responseText == 'true') {
                            }
                        }
                    }
                }
            },
            messages : {
                password : {
                    required : ""
                }
            },
            onfocusin : function(element) {
                if (!$(element).parent('div').prev("div")
                .hasClass('success')) {
                    var label = this.errorsFor(element);
                    if( $(label).attr('hasError')  )
                    {
                        if($( label ).attr('remote-validated') != "true")
                        {
                            this.showLabel(element, focusRules[element.name]);
                            $(element).parent('div').removeClass(
                                this.settings.errorClass)
                            .removeClass(
                                this.settings.validClass)
                            .prev("div")
                            .addClass('focus')
                            .removeClass(
                                this.settings.errorClass)
                            .removeClass(
                                this.settings.validClass);
                        }
                    } else {
                        this.showLabel(element, focusRules[element.name]);
                        $(element).parent('div').removeClass(
                        this.settings.errorClass)
                        .removeClass(
                        this.settings.validClass)
                        .prev("div")
                        .addClass('focus')
                        .removeClass(
                        this.settings.errorClass)
                        .removeClass(
                        this.settings.validClass);
                    }
                }
            },
            highlight : function(element,errorClass, validClass) {
                $(element).parent('div')
                .removeClass(validClass)
                .addClass(errorClass).prev("div")
                .removeClass(validClass)
                .addClass(errorClass);
                $('span.help-inline', $(element).parent('div')
                .prev('div')).removeClass(validClass) ;
            },
            unhighlight : function(element,
            errorClass, validClass) {
                var showError = false ;
                switch( element.nodeName.toLowerCase() ) {
                    case 'select' :
                        var val = $(element).val();

                        if($($(element).children(':selected')).attr('default') == undefined)
                        {
                            showError = true ;
                        } else {
                            showError  = false;
                        }
                        break ; 
                    case 'input':
                        if ( this.checkable(element) ) {
                            showError = this.getLength(element.value, element) > 0;
                        } else if($.trim(element.value).length > 0) {
                            showError =  true ;
                        } else {
                            showError = false ;
                        }
                        break; 
                    default:
                        var val = $(element).val();
                        showError =  $.trim(val).length > 0;
                }
                if(! showError ){
                    $(
                    'span.help-inline',
                    $(element).parent('div')
                    .prev('div')).hide();
                    $(element).parent('div')
                    .removeClass(errorClass)
                    .removeClass(validClass)
                    .prev("div")
                    .removeClass(errorClass)
                    .removeClass(validClass);
                } else {
                    if(element.type !== "file"){
                        $(element).parent('div')
                        .removeClass(errorClass)
                        .addClass(validClass).prev(
                        "div").addClass(
                        validClass)
                        .removeClass(errorClass);
                        $('span.help-inline', $(element).parent('div')
                        .prev('div')).text(
                        validRules[element.name] ).show();
                    } else{
                        $(element).parent('div')
                        .removeClass(errorClass)
                        .removeClass(validClass)
                        .prev("div")
                        .removeClass(errorClass)
                        .removeClass(validClass);
                    }
                }
            },
            submitHandler: function(form) {
                form.submit();
                $('#globalExportForm')[0].reset();
                $('#myModal').modal('hide');
            }
        });
}