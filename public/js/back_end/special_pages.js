$(document).ready(function() {
    $('ul#specialPages li').click(changeSelectedClass);
    $( "#specialPages" ).sortable();
    $( "#specialPages" ).disableSelection();
    $( "#specialPages" ).on( "sortstop", function( event, ui ) {
        var ids = new Array();
        $('.ui-state-default').each(function(){
            ids.push($(this).attr('id'));
        });
        $('div.image-loading-icon').append("<img id='img-load' src='" +  HOST_PATH  + "/public/images/validating.gif'/>");
        var ids = ids.toString();
        $.ajax({
            type : "POST",
            url : HOST_PATH + "admin/specialpages/savepopulararticlesposition",
            method : "post",
            dataType : 'json',
            data: { articleIds: ids },
            success : function(json) { 
                $('#img-load').remove();
                $( "#specialPages" ).sortable( "refresh" );
                $( "#specialPages" ).sortable( "refreshPositions" );
                $('ul#specialPages li').remove();
                var li = '';
                if(json!=''){
                    for(var i in json) {
                        li+= "<li class='ui-state-default' id='" + json[i].articles.id + "'>" 
                        + json[i].articles.title + "</span></li>";
                    }
                    $('ul#specialPages').append(li);
                    $('ul#specialPages li').click(changeSelectedClass);
                }
                bootbox.alert(__('Special page offers successfully updated.'));
                setTimeout(function(){
                  bootbox.hideAll();
                }, 3000);
            }
        });
    });
});
function changeSelectedClass() {
    $('ul#specialPages li').removeClass('selected');
    $(this).addClass('selected');
}