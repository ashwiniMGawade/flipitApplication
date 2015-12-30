$(function () {
    var url = window.location.search.match(/url=([^&]+)/);
    if (url && url.length > 1) {
        url = decodeURIComponent(url[1]);
    } else {
        url = 'http://'+window.location.host+'/swagger_resources/emailcontents-api-doc.json';
    }
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        contentType: 'application/json',
        ifModified: true,
        cache: false,
        complete: function (xhr, status) {
            var spec = JSON.parse(xhr.responseText);
            var host = window.location.host;
            spec.host = host.replace('developer', 'api');
            window.swaggerUi = new SwaggerUi({
                url : url, // This will make Swagger happy
                dom_id: "swagger-ui-container",
                validatorUrl : null,
                supportedSubmitMethods: ['get', 'post', 'put', 'delete', 'patch'],
                spec: spec,
                docExpansion: "list",
                apisSorter: "alpha"
             });
            window.swaggerUi.load();
        }
    });

    function addApiKeyAuthorization(){
        var key = encodeURIComponent($('#input_apiKey')[0].value);
        if(key && key.trim() != "") {
            var apiKeyAuth = new SwaggerClient.ApiKeyAuthorization("api_key", key, "query");
            window.swaggerUi.api.clientAuthorizations.add("api_key", apiKeyAuth);
            log("added key " + key);
        }
    }

    $('#input_apiKey').change(addApiKeyAuthorization);

    function log() {
        if ('console' in window) {
            console.log.apply(console, arguments);
        }
    }
});
