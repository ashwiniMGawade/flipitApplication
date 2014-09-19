# to edit varnisch config:
# scp /home/kortingscode.nl/kortingscode.nl/varnish/default.vcl root@31.3.97.202:/etc/varnish/default.vcl
# reload varnisch service varnish reload
# set cookie in Chrome: javascript:document.cookie="kc_session_active=1" http://blog.nategood.com/quickly-add-and-edit-cookies-in-chrome

# PURGE
# curl -X PURGE http://kortingscode.nl

backend default {
  .host = "178.18.91.234";
  .port = "80";
}

acl ClearCache {
    "127.0.0.1";
    "141.138.196.192";
    "62.195.10.151";
    "81.206.55.78";
    "178.18.91.234";
}

acl block {
   "89.248.174.101";
}

sub vcl_recv {

    if (client.ip ~ block) {
        error 403 "Not allowed";
    }

    remove req.http.X-Forwarded-For;
    set req.http.X-Forwarded-For = client.ip;

    ## Default request checks
    if (req.request != "GET" &&
        req.request != "HEAD" &&
        req.request != "PUT" &&
        req.request != "POST" &&
        req.request != "TRACE" &&
        req.request != "OPTIONS" &&
        req.request != "REFRESH" &&
        req.request != "PURGE" &&
        req.request != "DELETE") {
            # Non-RFC2616 or CONNECT which is weird.
            return (pipe);
    }

    # Pass anything other than GET and HEAD directly.
    if (req.request != "GET" && req.request != "HEAD" && req.request != "REFRESH" && req.request != "PURGE" && req.request != "BAN") {
        return( pass );
    } /* We only deal with GET and HEAD by default */

    # Never cache these pages
    if ((req.request == "GET" && (req.url ~ "(/out/|.xml|^/admin|^/nieuws|^/public/blog|/js/back_end/gtData.js)")) || req.http.X-Requested-With == "XMLHttpRequest" || req.url ~ "nocache" ) {
        set req.http.HTTP_X_PIPE = "1";
        return(pass);
    }

    # Nodig voor varnish-agent
    set req.http.X-Full-Uri = req.http.host + req.url;

    if (req.request == "PURGE") {
        if (!client.ip ~ ClearCache) {
            error 405 "Method not allowed";
        }else{
            ban("req.http.host == " +req.http.host+" && obj.http.content-type ~ "+req.http.content-type);
            error 200 "Ban added";
        }
    }

    if (req.request == "REFRESH") {
        set req.request = "GET";
        # set req.http.HTTP_X_REFRESH = "1";
        set req.hash_always_miss = true;
    }

    if (req.http.Cookie == "") {
       remove req.http.Cookie;
    }

    if(req.http.X-Requested-With == "XMLHttpRequest" || req.url ~ "nocache") {
        return (pipe);
    }

    if (req.url ~ "^(/dk|/fi|/id|/jp|/my|/no|/tr|/uk|/za|/kr|/ar|/ru|/hk|/sk|/cl|/ie|/mx|/cn)(/|$)" ){
        error 404 "This locale doesn't yet exist. Stay tuned!";
    }

    # Set client IP
    if (req.http.x-forwarded-for) {
        set req.http.X-Forwarded-For =
        req.http.X-Forwarded-For + ", " + client.ip;
    } else {
        set req.http.X-Forwarded-For = client.ip;
    }

    set req.http.HTTP_X_VARNISH = "1";

    return (lookup);

}

sub vcl_hash {

    hash_data (req.url);
    hash_data (req.http.host);

    if( req.http.Cookie ~ "kc_unique_user_id" ) {
        set req.http.X-Varnish-Hashed-On =
            regsub( req.http.Cookie, "^.*?kc_unique_user_id=([^;]*);*.*$", "\1" );
    }

    if( req.url ~ "/login/userwidget" && req.http.X-Varnish-Hashed-On ) {
        hash_data (req.http.X-Varnish-Hashed-On);
    }

    if( req.url ~ "/login/userfooter" && req.http.X-Varnish-Hashed-On ) {
        hash_data (req.http.X-Varnish-Hashed-On);
    }

    if( req.url ~ "/store/followbutton" && req.http.X-Varnish-Hashed-On ) {
        hash_data (req.http.X-Varnish-Hashed-On);
    }

    if( req.url ~ "/login/usersignup" && req.http.X-Varnish-Hashed-On ) {
        hash_data("logged in");
    }

    if( req.url ~ "/login/usermenu" && req.http.X-Varnish-Hashed-On ) {
        hash_data("logged in");
    }

    if( req.url ~ "/store/signup" && req.http.X-Varnish-Hashed-On ) {
        hash_data("logged in");
    }

    if( req.url ~ "/store/discountcodewidget" && req.http.X-Varnish-Hashed-On ) {
        hash_data("logged in");
    }

    return(hash);
}


sub vcl_hit {
    if (req.request == "PURGE") {
        purge;
        error 200 "Purged";
    }
}

sub vcl_miss {
    if (req.request == "PURGE") {
        purge;
        error 404 "Not in cache";
    }

    #if (req.http.HTTP_X_REFRESH == "1") {
    #    error 200 "Refreshed";
    #}
}

sub vcl_pass {
    if (req.request == "PURGE") {
        error 502 "PURGE on a passed object";
    }
}

sub vcl_fetch{

    if (req.http.HTTP_X_PIPE == "1") {
        return(deliver);
    }

    #/* Use to see what cookies go through our filtering code to the server */
    #set beresp.http.X-Varnish-Cookie-Debug = "Cleaned request cookie: " + req.http.Cookie;

    unset beresp.http.Etag;
    unset beresp.http.Vary;
    unset beresp.http.Server;
    unset beresp.http.X-Powered-By;

    if (beresp.status >= 500 || beresp.status == 301 || beresp.http.X-Nocache  == "no-cache" || (req.url ~ "store/followbutton" && req.http.X-Varnish-Hashed-On )) {
        set beresp.ttl = 0s;
        set beresp.http.Cache-Control = "max-age = 0";
    } elseif (req.request != "POST" && beresp.http.Set-Cookie !~ "delete") {
        set beresp.ttl = 24 h;
        set beresp.http.Cache-Control = "max-age = 3600";
        unset beresp.http.Pragma;
        unset beresp.http.Expires;
        unset beresp.http.Set-Cookie;
    }

    if (beresp.http.esi-enabled == "1") {
        set beresp.do_esi = true; /* Do ESI processing */
        unset beresp.http.esi-enabled;
    }

    if (req.url ~ "\.(jpg|jpeg|gif|png|ico|zip|tgz|gz|rar|bz2|pdf|txt|tar|wav|bmp|rtf|flv|swf)$") {
        set beresp.ttl = 365d;
        set beresp.http.Cache-Control = "max-age = 31536000";
    }

}


sub vcl_deliver{
    # remove some headers added by varnish
    unset resp.http.Via;
    unset resp.http.X-Varnish;

    if (obj.hits > 0) {
        set resp.http.X-Cache = "Cached";
    } else {
        set resp.http.X-Cache = "Not-Cached";
    }
}

sub vcl_error {

    if (obj.status == 404) {
        set obj.http.Location = "http://www.flipit.com";
        set obj.status = 302;
        return(deliver);
    }
}
