    if($("form#addOfferForm").length > 0)
    {
        $("#locale").val(2);
        updateOffers($("#locale")[0]);
        
        $("form#addOfferForm").submit(function(e){
            if($("#locale").val() == 0)
            {
                $("span[for=offerName]").html("");
                $("span[for=locale]").html(__("Please select locale"));
                return false;
                
            }else if($("#searchShops").val() == "")
            {
                $("span[for=locale]").html("");
                $("span[for=offerName]").html(__("Please select offer"));
                
                return false; 
            }else{
                return true ;
            }
        });
    }

function updateOffers(el)
{
    var locale = parseInt(el.value) ;
    if(locale > 0)
    {
        $("#searchOffers").val("");
        
        $("#searchOffers").select2({
            placeholder: __("Search Offer"),
            minimumInputLength: 1,
            ajax: {
                     url: HOST_PATH + "admin/splash/offers-list/locale/" +  locale ,
                     dataType: 'json',
                     data: function(term, page) {
                         return { keyword: term   }
                     },
                     type: 'post',
                     results: function (data, page) { 
                         return {results: data};
                     }
            },
            formatResult: function(data) {
                return data.name; 
            },
            formatSelection: function(data) { 
                $("#searchOffers").val(data.name);
                $("#searchOfferId").val(data.id);
                return data.name; 
            },
        });
    }
}