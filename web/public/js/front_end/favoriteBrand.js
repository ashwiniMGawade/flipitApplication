$(document).ready(function() {
    validateSearch();
    $.ui.autocomplete.prototype._renderMenu = function( ul, items ) {
    var currentSelectedItem = this;
    $.each( items, function( index, item ) {
        if (index < 8)
            {currentSelectedItem._renderItem( ul, item );}
    });
}

$('.brands-page article div.text a').on('touchend', function(e) {
    console.log('Mobile Touch ended : Favouriting Shop');
    var el = $(this);
    var link = el.attr('href');
    window.location = link;
});

$('body').click(function(event){
    var clickedId = event.target.id;
    if (clickedId == "searchFieldBrandHeader") {
        return false;
    }
});

$("input#searchFieldBrandHeader").autocomplete({
    minLength : 1,
    search: function(event, ui) {
        $('.ajax-autocomplete ul').empty();
    },
    source: shopsJSON,
    select: function(event, ui ) {
        $('form').submit(function() {
            return false;
        });
    },
    focus: function( event, ui ) {
            $('li.wLi2').removeClass('select');
            $('a#ui-active-menuitem').parents('li').addClass('select');
        },
    }).data( "autocomplete" )._renderItem = function( ul, item, url ) {
        url = item.value;
        return $("<li class='wLi2'></li>").data("item.autocomplete", item).append(
            $('<a href="" onClick="redirect(\'' + url + '\')"></a>').html((__highlight(item.label,$("input#searchFieldBrandHeader").val()))))
        .appendTo(ul);
     };  
    $("a#searchbuttonBrandHeader").click(function(){
    if ($("input#searchFieldBrandHeader").val()!='') {
        var autocomplete = $('input#searchFieldBrandHeader').data("autocomplete");  
        var matcher = new RegExp("("+ $.ui.autocomplete.escapeRegex($('input#searchFieldBrandHeader').val())+ ")", "ig");
        autocomplete.widget().children(".ui-menu-item").each(
        function() {
        var item = $(this).data("item.autocomplete");
        if (matcher.test(item.label|| item.value|| item)) {
             autocomplete.selectedItem = item;
        }               
       });
            
        if (autocomplete.selectedItem
                && $('input#searchFieldBrandHeader').val()
                        .toLowerCase() == autocomplete.selectedItem.value
                        .toLowerCase()) {
            item = {};
            item['permalink'] = autocomplete.selectedItem.permalink;
            autocomplete._trigger("select",
                    '', {
                        'item' : item
                    });
        } else {
            var searchedKeywordValue = $(
                    "input#searchFieldBrandHeader")
                    .val();
            if (searchedKeywordValue == 'Vind kortingscodes voor jouw favoriete winkels..') {
                return false;
            }
            var searchUrl = HOST_PATH_LOCALE + __("link_mijn-favorieten");     
        }
        redirect($("input#searchedBrandKeyword").val());
    }
    });
    
$("input#searchFieldBrandHeader").keyup(function(e){
    if(e.which != 37 && e.which != 38 && e.which != 39 && e.which != 40){
        $("input#searchedBrandKeyword").val($(this).val());
    }
});
    
$("input#searchFieldBrandHeader").keypress(function(event){
if(event.which == 13 && $("input#searchFieldBrandHeader").val()!='' && $("input#searchedBrandKeyword").val() == $("input#searchFieldBrandHeader").val()){
    var autocomplete = $( this ).data( "autocomplete" );
    var matcher = new RegExp( "("+$.ui.autocomplete.escapeRegex($(this).val())+")", "ig"  );
    autocomplete.widget().children( ".ui-menu-item" ).each(function() {
        var item = $( this ).data( "item.autocomplete" );
        if ( matcher.test( item.label || item.value || item ) ) {
            autocomplete.selectedItem = item;
        } 
    });   
    if (autocomplete.selectedItem && $(this).val().toLowerCase() == autocomplete.selectedItem.value.toLowerCase()) {
        item = {};
        item['permalink'] = autocomplete.selectedItem.permalink;
        autocomplete._trigger( "select", '', { 'item' : item } );
    } else {
        var searchedKeywordValue = $("input#searchFieldBrandHeader").val();
        if(searchedKeywordValue == 'Vind kortingscodes voor jouw favoriete winkels..'){
            return false;
        }
    }
    redirect($("input#searchedBrandKeyword").val());
}
$('ul.ui-autocomplete').addClass('wd1');
});
});

function __highlight(s, t) {
    var matcher = new RegExp("(" + $.ui.autocomplete.escapeRegex(t) + ")", "ig");
    return s.replace(matcher, '<span>$1</span>');
}

function redirect (url) {
    var Return_URL = HOST_PATH_LOCALE + __("link_mijn-favorieten");
    var form = $('<form action="' + Return_URL + '" method="post">' +
    '<input type="text" name="searchBrand" value="' + url + '" />' +
    '</form>');
    $('body').append(form);
    form.submit();
}

function validateSearch() {
    validator = $('form#search-brand-form')
    .validate({
        errorClass: 'input-error',
        validClass: 'input-success',
        rules: {
            searchBrand: {
                required: true
            }
        },
        messages : {
            searchBrand: {
                required:''
            }
        },
        onfocusin : function(element) {
            if($(element).valid() == 0){
                $(element).removeClass('input-error').removeClass('input-success');
                $(element).next('label').hide();
            } else {
                $(element).removeClass('input-error').addClass('input-success');
                $(element).next('label').hide();
            }
        },
        onfocusout :function(element) {
            if($(element).valid() == 0){
                $(element).removeClass('input-success').addClass('input-error');
                $(element).next('label').hide();
            } else {
                $(element).removeClass('input-error').addClass('input-success');
                $(element).next('label').hide();
            }
         },
        highlight : function(element, errorClass, validClass) {
            $(element).addClass(errorClass).removeClass(validClass);
            $(element).next('label').hide();
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass(errorClass);
            $(element).next('label').hide();
        },
        success: function(element, errorClass, validClass) {
            $(element).removeClass(errorClass).addClass(validClass);
            $(element).next('label').hide();
        }
    });
}