$(function(){
    $.get("http://ipinfo.io", function(response) {
        if(response.country.toLowerCase() in availableLanguages){
            var countryCode = response.country.toLowerCase();
            var countryUrl = '/'+countryCode;
            if(countryCode == 'nl') {
                countryUrl = 'http://www.kortingscode.nl';
            }
            $('#coutry').val(countryCode);
            jcf.refreshAll();
        }
    }, "jsonp");

    $('form.select-form').submit(function(e){
        e.preventDefault();
        countryCode = $('#coutry').val();
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