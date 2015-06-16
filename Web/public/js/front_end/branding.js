$(function() {
    $('form#branding :input').change(function(){
        var cssElement = $(this).data("css-selector");
        if(cssElement) $(cssElement).css($(this).data("css-property"), this.value);
    });

    $('#preview_submit').click(function(){
        $('#preview').val(1);
        $('#branding').submit();
    });

    $('#reset_submit').click(function(){
        $('#reset').val(1);
        $('#branding').submit();
    });
    
    $('#close_branding a').click(function(){
        $('#branding_panel').hide();
        $('#show_branding').show();
    });
    
    $('#show_branding a').click(function(){
        $('#branding_panel').show();
        $('#show_branding').hide();
    });
});