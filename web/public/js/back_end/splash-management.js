$(document).ready(function() {
    if($('#addOfferForm').length>0) {
        validateForm();
    }
    if($('#splash-offer-table').length>0) {
        $('#splash-offer-table').dataTable({"bFilter": false, "bPaginate": false}).rowReordering({
            successCallback: function () {
                $('#save-order-section').show();
            }
        });
    }
});


function validateForm() {
    jQuery('#addOfferForm').validate({
        errorClass : 'error',
        validClass : 'success',
        errorElement : 'span',
        ignore: ".ignore",
        errorPlacement : function(error, element) {
            element.parent("div").prev("div").html(error);
        },
        rules: {
            locale : "required",
            searchShopId : "required",
            searchOfferId : "required"
        },
        messages : {
            locale : {
                required : __("Please select a locale")
            },
            searchShopId : {
                required : __("Please select a shop")
            },
            searchOfferId : {
                required : __("Please select an offer")
            }
        },
        highlight : function (element, errorClass, validClass) {
            jQuery(element).parent('div')
                .removeClass(validClass)
                .addClass(errorClass)
                .prev("div")
                .removeClass(validClass)
                .addClass(errorClass);
        },
        success: function (label, element) {
            jQuery(element).parent('div')
                .removeClass(this.errorClass)
                .addClass(this.validClass)
                .prev("div")
                .removeClass(this.errorClass)
                .addClass(this.validClass);
        }
    });
}

function updateShops(localeElement) {
    var locale = parseInt(localeElement.value) ;
    if(locale > 0)
    {
        $("#searchShop").val("");

        $("#searchShop").select2({
            placeholder: __("Search Shop"),
            minimumInputLength: 1,
            ajax: {
                url: HOST_PATH + "admin/splash/shops-list/locale/" +  locale ,
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
                $("#searchShop").val(data.name);
                $("#searchShopId").val(data.id);
                updateOffers(localeElement, data.id);
                return data.name;
            }
        });
    }
}

function updateOffers(localeElement, shopId)
{
    var locale = parseInt(localeElement.value) ;
    if(locale > 0)
    {
        $("#searchOffers").val("");

        $("#searchOffers").select2({
            placeholder: __("Search Offer"),
            minimumInputLength: 1,
            ajax: {
                     url: HOST_PATH + "admin/splash/offers-list/locale/" +  locale + "/shop/" + shopId ,
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
            }
        });
    }
}