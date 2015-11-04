$(function(){
    $.get("http://ipinfo.io", function(response) {
        if(response.country.toLowerCase() in availableLanguages){
            var countryCode = response.country.toLowerCase();
            var countryUrl = '/'+countryCode;
            if(countryCode == 'nl') {
                countryUrl = 'http://www.kortingscode.nl';
            }
            var countryName = availableLanguages[countryCode];
            $("#current-local").attr('href', countryUrl).html("To Flipit.com "+ countryName);
            $('select.present').val(countryCode);
            get_coupon_count(countryCode);
            jcf.refreshAll();
        }
    }, "jsonp");

    $('select.present').on('change', function(e){
        get_coupon_count($(this).val());
    });

    $('form.select-form').submit(function(e){
        e.preventDefault();
        countryCode = $('select.present').val();
        if (countryCode != '') {
            var countryUrl = '/'+countryCode;
            if(countryCode == 'nl') {
                countryUrl = 'http://www.kortingscode.nl';
            }
            window.location.href = countryUrl;
        }
    });

    $(".thanks-btn").on("click", function() {
        var expiry = new Date();
        expiry.setFullYear(expiry.getFullYear() + 10);
        document.cookie = "cookie_agreement=1; expires=" + expiry.toGMTString();
        $('#footer-cookie-bar').remove();
    });
});