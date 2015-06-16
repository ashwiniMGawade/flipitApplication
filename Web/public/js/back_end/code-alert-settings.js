$(document).ready(init);
function init() {
    var iSearchText = $.bbq.getState( 'iSearchText' , true ) || undefined;
    var iStart = $.bbq.getState( 'iStart' , true ) || 0;
    var iSortCol = $.bbq.getState( 'iSortCol' , true ) || 1;
    var iSortDir = $.bbq.getState( 'iSortDir' , true ) || 'ASC';
    getcodeAlertList(iSearchText,iStart,iSortCol,iSortDir);
    $('#codeAlertListTable_filter').css('display', 'none');
    $(window).bind( 'hashchange', function(e) {
        if(hashValue != location.hash && click == false){
            codeAlertListTable.fnCustomRedraw();
        }
    });
}

function ucfirst(str) {
    if(str!=null){
        var firstLetter = str.substr(0,1);
        return firstLetter.toUpperCase() + str.substr(1);
    }
}
 
var codeAlertListTable = null ;
var hashValue = "";
var click = false;
function getcodeAlertList(iSearchText,iStart,iSortCol,iSortDir){
    $('#codeAlertListTable').addClass('widthTB');
    $("ul.ui-autocomplete").css('display','none');
    addOverLay();
    codeAlertListTable = $("#codeAlertListTable").dataTable(
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
            "sAjaxSource" : HOST_PATH + "admin/widget/widgetlist/searchText/"+ iSearchText +"/flag/0",
            "aoColumns" : [
                {
                    "fnRender" : function(obj) {
                        return id = obj.aData.id;
                    },
                    "bVisible":    false ,
                    "bSortable" : false,
                    "sType": 'numeric'
                },  
               {
                "fnRender" : function(obj) {
                    var tag = "";
                    tag ="<p editId='" + obj.aData.id + 
                    "' class='editId word-wrap-without-margin-widget store-offer'><a href='/admin/offer/editoffer/id/"+
                    obj.aData.id +"'>Zalando - offer title</a></p>";
                    return tag;
                },
                "bSearchable" : true,
                "bSortable" : true
               },
               {
                    "fnRender" : function(obj) {
                        var tag = "";
                        if(obj.aData.id){
                            tag = obj.aData.id;
                        } 
                        return tag; 
                    },
                    "bSearchable" : false,
                    "bSortable" : true
                   },
            ],
            "fnPreDrawCallback": function( oSettings ) {
                $('#codeAlertListTable').css('opacity',0.5);
             },     
            "fnDrawCallback" : function(obj) {
                $('#codeAlertListTable').css('opacity',1);
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
                    var aTrs = codeAlertListTable.fnGetNodes();
    
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

var validRules = {
    acclimit : __("Max free accounts looks great")
};

var focusRules = {
    acclimit : __("Enter numbers only")
};

$(document).ready(init);
function init()
{
    validatespeacialForm();
    
}

var validator = null; 

function validatespeacialForm()
{
    validator  = $("form#speacialForm")
    .validate(
            {
                errorClass : 'error',
                validClass : 'success',
                errorElement : 'span',
                afterReset  : resetBorders,
                errorPlacement : function(error, element) {
                    element.parent("div").prev("div")
                        .html(error);
                },
                rules : {
                    acclimit : {
                        required : true,
                        maxlength : 10
                    }
                },
                messages : {
                    acclimit : {
                        required : __("Please enter maximum account limit"),
                        maxlength : __("Please not enter more than 10 digits"),

                    }
                },

                onfocusin : function(element) {
                    if (!$(element).parent('div').prev("div")
                            .hasClass('success')) {                     
                        var label = this.errorsFor(element);
                        if( $( label).attr('hasError')  )
                        {
                            if($( label).attr('remote-validated') != "true")
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

                highlight : function(element,
                        errorClass, validClass) {

                    $(element).parent('div')
                            .removeClass(validClass)
                            .addClass(errorClass).prev(
                                    "div").removeClass(
                                    validClass)
                            .addClass(errorClass);

                },
                unhighlight : function(element,
                        errorClass, validClass) {
                    
                    if(! $(element).hasClass("passwordField"))
                    {
                        $(element).parent('div')
                                .removeClass(errorClass)
                                .addClass(validClass).prev(
                                        "div").addClass(
                                        validClass)
                                .removeClass(errorClass);
                        $(
                                'span.help-inline',
                                $(element).parent('div')
                                        .prev('div')).text(
                                validRules[element.name]);
                    }
                },
                success: function(label , element) {
                    $(element).parent('div')
                    .removeClass(this.errorClass)
                    .addClass(this.validClass).prev(
                            "div").addClass(
                                    this.validClass)
                    .removeClass(this.errorClass);
                    
                    $(label).append( validRules[element.name] ) ;
                    label.addClass('valid') ;
                }
            });
}

function resetBorders(el)
{
    $(el).each(function(i,o){
        $(o).parent('div')
        .removeClass("error success")
        .prev("div").removeClass('focus error success') ;
    
    });
    
}

$(document).ready(function() {
    $("#testEmail").select2({
        placeholder: __("Search Email"),
        minimumInputLength: 1,
        ajax: { 
             url: HOST_PATH + "admin/visitor/searchemails",
             dataType: 'json',
             data: function(term, page) {
                return {
                    keyword: term,
                    flag: 0
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
            $("#testEmail").val(data);
            return data; 
        }
    });
    
    $("#emailHeader").blur(function(){
        saveEmailHeaderFooter('email-header' , $(this).val() );
    });

    jQuery('#dp3').datepicker();
    jQuery('#offerstartTime').timepicker({
            minuteStep: 5,
            template: 'modal',
            showSeconds: false,
            showMeridian: false,
            defaultTime : $("input#currentSendTime").val()
    });
});

function saveEmailHeaderFooter(name , data)
{
    $.ajax({
        url : HOST_PATH + "admin/email/savecodealertemailheader",
        type : 'post',
        data : { 'template' : name , 'data' : data}
    });
}

function saveSenderEmail(el)
{
    var value = $(el).val().trim();
    if (value != '') {
        $.ajax({
            url : HOST_PATH + 'admin/email/savecodealertemailsubject',
            type : 'post',
            data : { name : $(el).attr('name'), val : value },
        });
    }
}

function getTotalRecepients()
{
    var count = 0 ;
    $.ajax({
        url : HOST_PATH + "admin/email/total-recepients",
        method : "post",
        dataType : "json",
        type : "post",
        async : false,
        success : function(data) {
            count = data['recepients'];
        }
    });

    return count;
}
