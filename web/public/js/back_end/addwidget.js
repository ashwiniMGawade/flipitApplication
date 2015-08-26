$(document).ready(function(){
    $("form").bind("keypress", function(e) {
      if (e.keyCode == 13) {
          return false;
     }
    });
    $("form#createWidget").submit(function(){
      if($("form#createWidget").valid()){
            $('button#widgetSubmit').attr('disabled' ,"disabled");
            return true;
        }else {
            return false;
        }
    });

    jQuery('#widgetStartDate_div').datepicker().on('changeDate');
    jQuery('#widgetEndDate_div').datepicker().on('changeDate');
	
	// setup ckeditor and its configurtion
	CKEDITOR.replace('content',
        {
            customConfig : 'config.js' ,
            toolbar :  'BasicToolbar'  ,
            height : "400"

        });
});	

function callToPermanentDelete(){
    bootbox.confirm(__("Are you sure you want to delete this widget permanently?"),__('No'),__('Yes'),function(r){
        if (!r) {
            return false;
        } else {
            window.location.href = $('input#editedWidgetId').val()+"/delete/delete";
        }

    });
}

function selectIsDated(elemntId)
{
    $("#" + elemntId).addClass("btn-primary").siblings().removeClass("btn-primary");
    switch(elemntId) {
        case 'id_dated_yes':
            $('#date_selectors_div').show();
            break;
        case 'id_dated_no':
            $('#date_selectors_div').hide();
            $("input#widgetStartDate").val('');
            $("input#widgetEndDate").val('');
            break;
        default:
            break;
    }
}
	  		