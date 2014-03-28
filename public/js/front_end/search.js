$(document).ready(function(){
var cache = {};
$.ui.autocomplete.prototype._renderMenu = function( ul, items ) {
		   var self = this;
		   $.each( items, function( index, item ) {
		      if (index < 8) // here we define how many results to show
		         {self._renderItem( ul, item );}
		      });
}
$("input#searchFieldHeader").autocomplete({
	delay: 0,
    minLength : 1,
	source :  function( request, response ) {
				var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
				response( $.grep( shopsJSON, function( item ){
				return matcher.test( item.label );
			  }) );
	},
    select: function(event, ui ) {
        	window.location.href = HOST_PATH_LOCALE + ui.item.permalink ;
    },
	focus: function( event, ui ) {
		    $('li.wLi2').removeClass('select');
			$('a#ui-active-menuitem').parents('li').addClass('select');
		   },
    }).data( "autocomplete" )._renderItem = function( ul, item, url ) {
        url = item.permalink;
        return $("<li class='wLi2'></li>").data("item.autocomplete", item).append(
			$("<a href=' + url + '></a>").html('<div>' + (__highlight(item.label,$("input#searchFieldHeader").val())) + "</div>"))
		.appendTo(ul);
     };  
    $("a#searchbuttonHeader").click(function(){
	if ($("input#searchFieldHeader")
		.val() == $(
		"input#searchFieldHeaderHidden")
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

		var value = $(
				"input#searchFieldHeader")
				.val();
		if (value == 'Vind kortingscodes voor jouw favoriete winkels..') {
			return false;

		}
		window.location.href = HOST_PATH_LOCALE
				+ __("zoeken") + '/' + value;

	}
}
	});
	
$("input#searchFieldHeader").keyup(function(e){
	if(e.which != 37 && e.which != 38 && e.which != 39 && e.which != 40){
		$("input#searchFieldHeaderHidden").val($(this).val());
	}
});
	
$("input#searchFieldHeader").keypress(function(event){
	
if(event.which == 13 && $("input#searchFieldHeader").val()!='' && $("input#searchFieldHeaderHidden").val() == $("input#searchFieldHeader").val()){
	
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
   	 }else{
		var value = $("input#searchFieldHeader").val();
		if(value == 'Vind kortingscodes voor jouw favoriete winkels..'){
			return false;
		}
		window.location.href = HOST_PATH_LOCALE + __("zoeken") + '/' + value;
	 }
}
	 $('ul.ui-autocomplete').addClass('wd1');
	 //$('ul.ui-autocomplete li').addClass('wd1');
});
});
function __highlight(s, t) {
	var matcher = new RegExp("(" + $.ui.autocomplete.escapeRegex(t) + ")", "ig");
	return s.replace(matcher, '<strong class="abcText">$1</strong>');
}