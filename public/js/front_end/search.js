$(document).ready(function(){
$.ui.autocomplete.prototype._renderMenu = function( ul, items ) {
   var currentSelectedItem = this;
   $.each( items, function( index, item ) {
        if (index < 8)
            {currentSelectedItem._renderItem( ul, item );}
        });
}
$('body').click(function(event){
    var clickedId = event.target.id;
    if (clickedId == "searchFieldHeader") {
        return false;
    }
});


$("input#searchFieldHeader").autocomplete({
    delay: 0,
    minLength : 1,
    search: function(event, ui) {
        $('.ajax-autocomplete ul').empty();
    },
    source :  function( request, response ) {
                var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
                response( $.grep( shopsJSON, function( item ){
                return matcher.test( item.label );
              }) );
    },
    select: function(event, ui ) {
        $('form').submit(function() {
            return false;
        });
        window.location.href = HOST_PATH_LOCALE + ui.item.permalink;
    },
    focus: function( event, ui ) {
            $('li.wLi2').removeClass('select');
            $('a#ui-active-menuitem').parents('li').addClass('select');
           },
    }).data( "autocomplete" )._renderItem = function( ul, item, url ) {
        url = item.permalink;
        return $("<li class='wLi2'></li>").data("item.autocomplete", item).append(
            $("<a href=" + HOST_PATH_LOCALE + url + "></a>").html((__highlight(item.label,$("input#searchFieldHeader").val()))))
        .appendTo(ul);
     };  
    $("a#searchbuttonHeader").click(function(){
    if ($("input#searchFieldHeader")
        .val() == $(
        "input#searchedKeyword")
        .val() && $("input#searchFieldHeader").val()!='') {
        var autocomplete = $('input#searchFieldHeader').data("autocomplete");  
        var matcher = new RegExp("("+ $.ui.autocomplete.escapeRegex($('input#searchFieldHeader').val())+ ")", "ig");
        autocomplete.widget().children(".ui-menu-item").each(
        function() {
        var item = $(this).data("item.autocomplete");
        if (matcher.test(item.label|| item.value|| item)) {
             autocomplete.selectedItem = item;
        }               
       });
            
        if (autocomplete.selectedItem
                && $('input#searchFieldHeader').val()
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
                    "input#searchFieldHeader")
                    .val();
            if (searchedKeywordValue == 'Vind kortingscodes voor jouw favoriete winkels..') {
                return false;
            }
            
            $('form').submit(function() {
                return false;
            });
            var searchUrl = HOST_PATH_LOCALE + __("zoeken") + '/' + encodeURIComponent(searchedKeywordValue);
            window.location.href = searchUrl;
        }
    }
    });
    
$("input#searchFieldHeader").keyup(function(e){
    if(e.which != 37 && e.which != 38 && e.which != 39 && e.which != 40){
        $("input#searchedKeyword").val($(this).val());
    }
});
    
$("input#searchFieldHeader").keypress(function(event){
if(event.which == 13 && $("input#searchFieldHeader").val()!='' && $("input#searchedKeyword").val() == $("input#searchFieldHeader").val()){
    var autocomplete = $( this ).data( "autocomplete" );
    var matcher = new RegExp( "("+$.ui.autocomplete.escapeRegex($(this).val())+")", "ig"  );
    autocomplete.widget().children( ".ui-menu-item" ).each(function() {
        var item = $( this ).data( "item.autocomplete" );
        if ( matcher.test( item.label || item.value || item ) ) {
            autocomplete.selectedItem = item;
        } 
    });   
    if ( autocomplete.selectedItem && $(this).val().toLowerCase() == autocomplete.selectedItem.value.toLowerCase()) {
        item = {};
        item['permalink'] = autocomplete.selectedItem.permalink;
        autocomplete._trigger( "select", '', { 'item' : item } );
    } else {
        var searchedKeywordValue = $("input#searchFieldHeader").val();
        if(searchedKeywordValue == 'Vind kortingscodes voor jouw favoriete winkels..'){
            return false;
        }
        $('form').submit(function() {
          return false;
        });

        var searchUrl = HOST_PATH_LOCALE + __("zoeken") + '/' + encodeURIComponent(searchedKeywordValue);
        window.location.href = searchUrl;
    }
}
$('ul.ui-autocomplete').addClass('wd1');
});
});

function __highlight(s, t) {
    var matcher = new RegExp("(" + $.ui.autocomplete.escapeRegex(t) + ")", "ig");
    return s.replace(matcher, '<span>$1</span>');
}