$(document).ready(function() {
    $('ul#popularArticles li').click(changeSelectedClass);
    $( "#popularArticles" ).sortable();
    $( "#popularArticles" ).disableSelection();
    $( "#popularArticles" ).on( "sortstop", function( event, ui ) {
        var ids = new Array();
        $('.ui-state-default').each(function(){
            ids.push($(this).attr('id'));
        });
        $('div.image-loading-icon').append("<img id='img-load' src='" +  HOST_PATH  + "/public/images/validating.gif'/>");
        var ids = ids.toString();
        $.ajax({
            type : "POST",
            url : HOST_PATH + "admin/populararticles/savepopulararticlesposition",
            method : "post",
            dataType : 'json',
            data: { articleIds: ids },
            success : function(json) { 
                $('#img-load').remove();
                $( "#popularArticles" ).sortable( "refresh" );
                $( "#popularArticles" ).sortable( "refreshPositions" );
                $('ul#popularArticles li').remove();
                var li = '';
                if(json!=''){
                    for(var i in json) {
                        li+= "<li class='ui-state-default' id='" + json[i].articleId + "'>" 
                        + json[i].title + "</span></li>";
                    }
                    $('ul#popularArticles').append(li);
                    $('ul#popularArticles li').click(changeSelectedClass);
                }
                bootbox.alert(__('Popular Articles successfully updated.'));
                setTimeout(function(){
                  bootbox.hideAll();
                }, 3000);
            }
        });
    });
});
function changeSelectedClass() {
    $('ul#popularArticles li').removeClass('selected');
    $(this).addClass('selected');
}