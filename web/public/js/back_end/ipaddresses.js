$(document).ready(function(){
    var iStart = $.bbq.getState('iStart', true) || 0;
    var iSortCol = $.bbq.getState('iSortCol', true) || 1;
    var iSortDir = $.bbq.getState('iSortDir', true) || 'ASC';
    if ($("table#IpaddressesListTbl").length) {
        getIpaddressesList(iStart, iSortCol, iSortDir);
    }
    $('form#addIpaddressForm').submit(function(){
        saveIpaddress();
    }); 
    
    if ($('form#addIpaddressForm').length){
        addIpaddressValidation();
    }
    $ ('form#editIpaddressesForm').submit(function(){
        saveEditIpaddress();
    });
    
    if ($('form#editIpaddressesForm').length) {
        editIpaddressValidation();
    }

    $(window).bind( 'hashchange', function(e) {
        if (hashValue != location.hash && click == false) {
            IpaddressesListTbl.fnCustomRedraw();
        }
    });

    if ($.validatoo != undefined) {
        $.validator.setDefaults({
            onkeyup : false,
            onfocusout : function(element) {
                $(element).valid();
            }
        });
    }
});

function saveIpaddress() {
    if ($("form#addIpaddressForm").valid()) {   
        $('#createIpaddress').attr('disabled' ,"disabled");
        return true;
    } else {
         return false;
    }
}

function saveEditIpaddress() {
    if ($("form#editIpaddressesForm").valid()) {
        return true;
    } else {
        return false;
    }
}

var validRules = {
    name : __("Valid Name"),
    ipaddress : __("Valid IP address")
};

var focusRules = {
    name : __("Enter valid name"),
    ipaddress : __("Enter IP address")
};
var validatorForNewRedirect = null ;
function addIpaddressValidation() {
    validatorForNewRedirect = $("form#addIpaddressForm").validate({    
        errorClass : 'error',
        validClass : 'success',
        ignore: ":hidden",
        errorElement : 'span',
        errorPlacement : function(error, element) {
            element.parent("div").prev("div").html(error);
        },
        rules : {
            name : {
                required : true,
                regex : /^[a-zA-Z ]*$/
            },
            ipaddress : {
                required : true,
                regex : /^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/
            }
        },
        messages : {
            name : {
                required : __("Please enter valid name"),
                regex : __("Invalid name")
            },
            ipaddress : {
                required : __("Please enter valid IP address"),
                regex : __("Invalid IP address")
            }
        },
        onfocusin : function(element) {
            if (!$(element).parent('div').prev("div").hasClass('success')) {
                this.showLabel(element, focusRules[element.name]);
                $(element).parent('div').removeClass(this.settings.errorClass).removeClass(this.settings.validClass)
                    .prev("div").addClass('focus').removeClass(this.settings.errorClass).removeClass(
                    this.settings.validClass
                );
            }
        },
        highlight : function(element, errorClass, validClass) {
            $(element).parent('div').removeClass(validClass).addClass(errorClass).prev("div").removeClass(validClass)
                .addClass(errorClass);
        },
        unhighlight : function(element, errorClass, validClass) {
            $(element).parent('div').removeClass(errorClass).addClass(validClass).prev("div").addClass(validClass)
                .removeClass(errorClass);
                $('span.help-inline', $(element).parent('div').prev('div')).text(validRules[element.name]);
        },
    });
}
var IpaddressesListTbl = null;
var hashValue = "";
var click = false;
function getIpaddressesList(iStart,iSortCol,iSortDir) {
    addOverLay();
    $('#IpaddressesListTbl').addClass('widthTB');
    IpaddressesListTbl = $("table#IpaddressesListTbl")
    .dataTable({
        "bLengthChange" : false,
        "bInfo" : true,
        "bFilter" : true,
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
        "sAjaxSource" : HOST_PATH+"admin/ipaddresses/getipaddreses",
        "aoColumns" : [{
           "fnRender" : function(obj){
            var id = null;
                return id = obj.aData.id;
            },
           "bSortable" : false,
           "bVisible": false ,
            "sType": 'numeric'
         },{
           "fnRender" : function(obj){
                var ipaddress = ''; 
                if(obj.aData.ipaddress != null){  
                     ipaddress = '<p editId="' + obj.aData.id 
                     + '" class = "colorAsLink word-wrap-without-margin-searchbar">'
                     + '<a href="javascript:void(0);">' + obj.aData.name+'</a></p>';
                }
                 return ipaddress;
                },
                "bSortable" : true
        }, {
               "fnRender" : function(obj){
                 var ipaddress = ''; 
                 if(obj.aData.ipaddress != null){  
                     ipaddress = '<p editId="' + obj.aData.id 
                     + '" class = "colorAsLink word-wrap-without-margin-searchbar">'
                     + '<a href="javascript:void(0);">' + obj.aData.ipaddress+'</a></p>';
                 }
                 return ipaddress;
            },
             "bSortable" : true
        },{
            "fnRender" : function(obj) {
                var tag = '';
                if(obj.aData.created_at!=undefined && obj.aData.created_at!=''){
                    var dat = obj.aData.created_at.date;
                    return "<a href='javascript:void(0)'>" +  dat + "</a>";
                }
                return "<a href='javascript:void(0)'></a>";
            },
            "bSortable" : true
        },{
            "fnRender" : function(obj) {
                var html = "<a href='javascript:void(0);'" + "onclick='deleteIpaddress( " +
                    obj.aData.id  + ")'>" + __('Delete') + "</a>";
                return html;
            },
            "bSortable" : false
        }],
        "fnPreDrawCallback": function( oSettings ) {
            $('#IpaddressesListTbl').css('opacity',0.5);
        },     
        "fnDrawCallback" : function(obj) {
            $('#IpaddressesListTbl').css('opacity',1);
            var state = {};
            $("#IpaddressesListTbl").find('tr').find('td:lt(3)').click(function () {
                var eId = $(this).parent('tr').find('p').attr('editid');
                state[ 'eId' ] = eId ;
                $.bbq.pushState( state );
                click = true;
                window.location.href = HOST_PATH + "admin/ipaddresses/editipaddress/id/" + eId+ "?iStart="+
                obj._iDisplayStart+"&iSortCol="+obj.aaSorting[0][0]+"&iSortDir="+
                obj.aaSorting[0][1]+"&eId="+eId;
            });
            state['iStart'] = obj._iDisplayStart ;
            state['iSortCol'] = obj.aaSorting[0][0] ;
            state['iSortDir'] = obj.aaSorting[0][1] ;
            $.bbq.pushState(state);
            hashValue = location.hash;
            var aTrs = IpaddressesListTbl.fnGetNodes();
            for (var i=0 ; i<aTrs.length ; i++) {
                $editId = $(aTrs[i]).find('p').attr('editid');
                if ($editId == $.bbq.getState('eId', true)){
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
            $('td.dataTables_empty').html('No record found !');
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
var validatorForEditIpaddress = null ;
function editIpaddressValidation() {
    validatorForEditIpaddress = $("form#editIpaddressesForm").validate( {    
        errorClass : 'error',
        validClass : 'success',
        ignore: ":hidden",
        errorElement : 'span',
        errorPlacement : function(error, element) {
            element.parent("div").prev("div").html(error);
        },
        rules : {
            name : {
                required : true,
                regex : /^[a-zA-Z ]*$/

            },
            ipaddress : {
                required : true,
                regex : /^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/
            }
        },
        messages : {
            name : {
                required : __("Please enter valid name"),
                regex : __("Invalid name")
            },
            ipaddress : {
                required : __("Please enter valid IP address"),
                regex : __("Invalid IP address")
            }
        },
        onfocusin : function(element) {
            if (!$(element).parent('div').prev("div").hasClass('success')) {
                this.showLabel(element, focusRules[element.name]);
                    $(element).parent('div').removeClass(
                        this.settings.errorClass).removeClass(this.settings.validClass).prev("div")
                        .addClass('focus').removeClass(this.settings.errorClass).removeClass(this.settings.validClass);
            } else {
            }
        },
        highlight : function(element, errorClass, validClass) {
            $(element).parent('div').removeClass(validClass)
               .addClass(errorClass).prev("div").removeClass(validClass).addClass(errorClass);
        },
        unhighlight : function(element,
            errorClass, validClass) {
            $(element).parent('div').removeClass(errorClass)
                .addClass(validClass).prev("div").addClass(validClass).removeClass(errorClass);
            $('span.help-inline', $(element).parent('div').prev('div')).text(validRules[element.name]);
        },
    });
}

function editIpaddress() {
    var id = $(this).parents('tr').children('td').children('p.colorAsLink').attr('editId'); 
    window.location =HOST_PATH + 'admin/ipaddresses/editipaddress/id/' + id;
}

function deleteIpaddressByEdit(e) {
    var id =  $('input#id').val();
    deleteIpaddress(id);
}

function deleteIpaddress(id) {
    bootbox.confirm("Are you sure you want to delete this ip addresses permanently?",'No','Yes',function(r) {
    if (!r) {
        return false;
    } else {
        addOverLay();
        $.ajax({
            type : "POST",
            url : HOST_PATH+"admin/ipaddresses/deleteipaddress",
            data : "id="+id
        }).done(function(msg) {
            window.location = HOST_PATH + 'admin/ipaddresses';
        }); 
     }
 });
}

