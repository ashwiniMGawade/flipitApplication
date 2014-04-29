function setOnOff(e, name, status)
{
    var btn = e.target  ? e.target :  e.srcElement;

    switch(name)
    {
        case "featured-category" :
            $(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
            if (status=='on') {
                $('#featuredCategory').val(1);
            } else {
                $('#featuredCategory').val(0);
            }	
            break;
        default:

        if (status == 'toggle-btn') {
            if ($(btn).hasClass('btn-primary')) {
                $(btn).removeClass('btn-primary');
                $("input[ name="+ name +"]:hidden").val(0);
            } else {
                $(btn).addClass('btn-primary');
                $("input[ name="+ name +"]:hidden").val(1);
            } 
            return true;
        }

        $(btn).addClass("btn-primary").siblings().removeClass("btn-primary");
        var val = status == 'on' ? 1 :  0 ;
        $("input[name="+ name + "]").val(val) ;
    }
}