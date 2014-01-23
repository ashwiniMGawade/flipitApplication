var _img = new Image();
$(document).ready(function() {
	$('#fileupload').fileupload({
	    autoUpload: true,
	    maxNumberOfFiles: 1,
	    dataType: 'json' ,
        done: function (e, data) {
        	console.log(data);
        	_img.onload = function(){
        		
        		if($("img" , "div#showHide").hasClass('mt24'))
    			{
        			$("img" , "div#showHide").removeClass('mt24');
        			
    			}
        		$("img" , "div#showHide").attr('src' , _img.src) ;
        		
        	} ;
        	
        	if(data.result!=undefined && data.result!='' && data.result!=null){
        	_img.src =  HOST_PATH_PUBLIC + data.result.imgPath + data.result.imgName ;
        	$("div#messageDiv").hide();
        	}else{
        		
        		flashMessage(__('Dit bestand wordt niet ondersteund! Upload een .jpg of .png'));
        		
        	}
        	___removeOverLay();
        },
        progress: function (e, data) {
        	
        	___addOverLay();
        }
	});
});
function hidediv()
{
	$('div.uploder').hide();
}
function showdiv()
{
	$('div.uploder').show();
}
function flashMessage(msg)
{
	$("div#messageDiv").fadeIn()
					   .find('strong')
					   .html(msg);
}
