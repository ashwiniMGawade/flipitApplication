$(function () {
            var url = window.location.search.match(/url=([^&]+)/);
            if (url && url.length > 1) {
                url = decodeURIComponent(url[1]);
            } else {
                url = "http://developer.dev.flipit.com/swagger_resources/visitors-api-doc.json";
            }
            window.swaggerUi = new SwaggerUi({
                url: url,
                dom_id: "swagger-ui-container",
                supportedSubmitMethods: ['put'],
                onComplete: function(swaggerApi, swaggerUi){
                    if(typeof initOAuth == "function") {
                        initOAuth({
                            clientId: "your-client-id",
                            realm: "your-realms",
                            appName: "your-app-name"
                        });
                    }
                    $('pre code').each(function(i, e) {
                        hljs.highlightBlock(e)
                    });
                   // addApiKeyAuthorization();
                },
                onFailure: function(data) {
                    log("Unable to Load SwaggerUI");
                },
                docExpansion: "list",
                apisSorter: "alpha"
            });
           
            window.swaggerUi.load();
            function log() {
                if ('console' in window) {
                    console.log.apply(console, arguments);
                }
            }
        });
