$(document).ready(function(){
	var w = $('.huidige-stores').width();
	$('#admin-text-content').css('width',(w - 8));
	saveAdministratorText();
	
});

function saveAdministratorText()
{
	
	var content_id = $('.admin-text-content').attr('id');
   
    CKEDITOR.inline( content_id, {
        on: {
            blur: function( event ) {
                var data = event.editor.getData();
                ___addOverLay();
                var request = jQuery.ajax({
                    url: HOST_PATH+"admin/index/saveadmintext",
                    type: "POST",
                    data: {
                        content : data,
                    },
                    dataType: "html",
                    success: function(msg){
                    	//window.location.reload(true);
                    	if(msg!='1'){
                    			$('#'+content_id).html('<span class="errorMsg">' + msg + '</span>');
                    	  }
                    	 ___removeOverLay();
                    	
                    }
                });

            },
            focus: function( event ) {
                $('span.errorMsg').remove();
             }
        }
    
    } );
}