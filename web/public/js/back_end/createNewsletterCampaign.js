$(function(){
        $('#NewsletterWizardform').bootstrapWizard({
            'tabClass': 'nav nav-tabs',
            'onNext': function(tab, navigation, index) {
                return true;
            },
            onTabClick: function(tab, navigation, index) {
                return true;
            },
            onTabShow: function(tab, navigation, index) {
                var $total = navigation.find('li').length;
                var $current = index+1;
                var $percent = ($current/$total) * 100;
                if(tab.find('a').attr('id') == 'ScheduleCampaign') {
                    $(".next:not('.last')").hide();
                    $(".accessSave").show();

                } else {
                    $(".next:not('.last')").show();
                    $(".accessSave").hide();
                }
                $('#NewsletterWizardform').find('.progress-bar').css({width:$percent+'%'});
            }
        });

    var ranNum = Math.floor((Math.random()*50)+1);
    var info = $('#gi'), num='';
    var count = 0;
    $('#gn').on('keydown', function(){info.text('.')});
    $('#guessform').bootstrapWizard({
        'tabClass': 'nav nav-tabs',
        'onNext': function(tab, navigation, index) {
            var answer = $('#gn').val();
            num = num +' '+ answer;
            count++;
            if(answer > ranNum)
            {
                info.text("Guess lower!");
                return false;
            }
            else if(answer < ranNum)
            {
                info.text("Guess higher!!");
                return false;
            }
            else if(answer==ranNum)
            {
                ranNum = Math.floor((Math.random()*50)+1);
                $('#answer').text(answer);
                $('#count').text(count);
                $('#num').text(num);
                count = 0;
                return true;
            }
        },
        onTabClick: function(tab, navigation, index) {
            return false;
        }
    });

    /**
     * validRules object contain all the messages that are visible when an elment
     * value is valid
     *
     * structure to define a message for element: key is to be element name and Value is
     * message
     */
    var validRules = {
    };

    /**
     * focusRules oject contain all the messages that are visible on focus of an
     * elelement
     *
     * structure to define a message for element : key is to be element name and Value is
     * message
     */
    var focusRules = {
    };



    var invalidForm = {} ;
        var errorBy = "" ;

        $.validator.setDefaults({
            onkeyup : false,
            onfocusout : function(element) {
                $(element).valid();
            },
            ignore:[],

        });

        function resetBorders(el)
        {
            $(el).each(function(i,o){
                $(o).parent('div')
                    .removeClass("error success")
                    .prev("div").removeClass('focus error success') ;


                $(".form-error").find('div[for='+o.name).hide();

            });

        }

        var init = function ()
        {
            $("form").submit(function(e){
                // e.preventDefault();
                if (! jQuery.isEmptyObject(invalidForm) ) {
                    for (var i in invalidForm) {
                        if (invalidForm[i]) {
                            $('#saveNewsletterCampaign').removeAttr('disabled');
                            $('#sendTestMail').removeAttr('disabled');
                            $('#cancel').removeClass('disable_a_href disabled')
                            $('.nav-tabs a').removeClass('disable_a_href');
                            $('.next a').removeClass('disable_a_href');
                            $('.previous a').removeClass('disable_a_href');
                            $("#loader").hide();
                            return false;
                        }
                    }
                }
            });

            //function call to validate
            validateNewsletterWizardform();
        }

        var validateNewsletterWizardform = function (){
            validatorForNewsletterWizardform = $("form#NewsletterWizardform").validate(
                {
                    errorClass : 'error',
                    validClass : 'success',
                    errorElement : 'div',
                    ignore: "",
                    afterReset  : resetBorders,
                    errorPlacement : function(error, element) {
                        //element.parent("div").prev("div")
                        //    .html(error);
                        if($(".form-error").find('div[for='+element.attr('name')+']').length == 0  && error.html() != ''){
                            $(".form-error").append(error).show();
                        }
                    },
                    rules : {
                        "senderEmail": {
                            required : true,
                            email    : true
                        },
                        "campaignSubject" : {
                            required : true,
                            minlength: 2
                        },
                        "campaignHeader": {
                            minlength : 10
                        },
                        "campaignFooter": {
                            minlength : 10
                        },
                        "headerBannerURL": {
                            url : true
                        },
                        "footerBannerURL": {
                            url : true
                        }
                    },
                    messages : {
                        "senderEmail" : {
                            required : __("Please enter your email address"),
                            email : __("Please enter valid email address")
                        },
                        "campaignSubject": {
                            required: "Please enter newsletter campaign subject",
                            minlength: jQuery.validator.format("Please, at least 2 characters are necessary")
                        },
                        "campaignHeader": {
                            minlength : __("Please enter atleast 10 characters")
                        },
                        "campaignFooter"  : {
                            minlength : __("Please enter atleast 10 characters")
                        },
                        "headerBannerURL": {
                            url : __("Please enter valid URL")
                        },
                        "footerBannerURL"  : {
                            url : __("Please enter valid URL")
                        }
                    },
                    onfocusin : function(element) {
                        // display hint messages when an element got focus
                        if (!$(element).parent('div').prev("div")
                                .hasClass('success')) {

                            var label = this.errorsFor(element);

                            if( $( label ).attr('hasError')  ) {
                                if($( label ).attr('remote-validated') != "true") {
                                    this.showLabel(element, focusRules[element.name]);

                                    $(element).parent('div').removeClass(
                                        this.settings.errorClass)
                                        .removeClass(
                                        this.settings.validClass)
                                        .prev("div")
                                        .addClass('focus')
                                        .removeClass(
                                        this.settings.errorClass)
                                        .removeClass(
                                        this.settings.validClass);
                                }
                            } else {
                                this.showLabel(element, focusRules[element.name]);
                                $(element).parent('div').removeClass(
                                    this.settings.errorClass)
                                    .removeClass(
                                    this.settings.validClass)
                                    .prev("div")
                                    .addClass('focus')
                                    .removeClass(
                                    this.settings.errorClass)
                                    .removeClass(
                                    this.settings.validClass);
                            }
                        }
                    },

                    highlight : function(element, errorClass, validClass) {
                        // highlight borders in case of error
                        $(element).parent('div')
                            .removeClass(validClass)
                            .addClass(errorClass).prev(
                            "div").removeClass(
                            validClass)
                            .addClass(errorClass);

                        $('span.help-inline', $(element).parent('div')
                            .prev('div')).removeClass(validClass) ;
                    },
                    unhighlight : function(element, errorClass, validClass) {
                        // check for ignored elemnets and highlight borders when succeed
                        if(! $(element).hasClass("ignore")) {
                            $(".form-error").find('div[for='+element.name+']').hide();
                            $(element).parent('div')
                                .removeClass(errorClass)
                                .addClass(validClass).prev(
                                "div").addClass(
                                validClass)
                                .removeClass(errorClass);
                            $(
                                'span.help-inline',
                                $(element).parent('div')
                                    .prev('div')).text(
                                validRules[element.name]) ;
                        } else {
                            // check to display errors for ignored elements or not
                            var showError = false ;
                            //
                            switch( element.nodeName.toLowerCase() ) {
                                case 'input':
                                    if ( this.checkable(element) ) {

                                        showError = this.getLength(element.value, element) > 0;

                                    } else if($.trim(element.value).length > 0) {

                                        showError =  true ;

                                    } else {

                                        showError = false ;
                                    }

                                    break ;
                                default:
                                    var val = $(element).val();
                                    showError =  $.trim(val).length > 0;
                            }

                            if(! showError ) {
                                // hide errors message and remove highlighted borders
                                $(
                                    'span.help-inline',
                                    $(element).parent('div')
                                        .prev('div')).hide();

                                $(".form-error").find('div[for='+element.attr('name')+']').hide();

                                $(element).parent('div')
                                    .removeClass(errorClass)
                                    .removeClass(validClass)
                                    .prev("div")
                                    .removeClass(errorClass)
                                    .removeClass(validClass) ;
                            } else {
                                // show errors message and  highlight borders
                                // display green border and message
                                $(element).parent('div')
                                    .removeClass(errorClass)
                                    .removeClass(validClass)
                                    .removeClass("focus")
                                    .prev("div")
                                    .removeClass("focus")
                                    .removeClass(errorClass)
                                    .removeClass(validClass) ;
                            }
                        }

                    },
                    success: function(label , element) {
                        //$(".form-error").find('span[for='+element.name+']').hide();
                        $(element).parent('div')
                            .removeClass(this.errorClass)
                            .addClass(this.validClass).prev(
                            "div").addClass(
                            this.validClass)
                            .removeClass(this.errorClass)
                            .removeClass("focus");
                        $(label).append( validRules[element.name] ) ;
                        label.addClass('valid') ;
                    }
                });
        }

        init();
        $('form#NewsletterWizardform').submit(function () {
            if ($("form#NewsletterWizardform").valid()) {
                $('#saveNewsletterCampaign').attr('disabled', "disabled");
                $('#sendTestMail').attr('disabled', "disabled");
                $('#cancel').addClass('disable_a_href disabled')
                $('.nav-tabs a').addClass('disable_a_href');
                $('.next a').addClass('disable_a_href');
                $('.previous a').addClass('disable_a_href');
                $("#loader").show();
                return true;
            } else {
                return false;
            }
        });


    $('#saveNewsletterCampaign, #saveCampaign').on("click", function(e) {
        $('form#NewsletterWizardform').submit();
    });

    $('#scheduleButton').on("click", function(e) {
        $("#scheduleButton").append('<input type="hidden" name="schedule">');
        $( "textarea[name='campaignHeader']" ).rules( "add", {
            required: true,
            messages: {
                required: "Please enter newsletter campaign header in email setting step."
            }
        });
        $( "textarea[name='campaignFooter']" ).rules( "add", {
            required: true,
            messages: {
                required: "Please enter newsletter campaign footer in email setting step."
            }
        });
        $( "input[name='senderName']" ).rules( "add", {
            required: true,
            messages: {
                required: "Please enter newsletter sender name in email setting step."
            }
        });
        $( "input[name='scheduleDate']" ).rules( "add", {
            required: true,
            messages: {
                required: "Please enter newsletter campaign scheduled date."
            }
        });
        if ($("form#NewsletterWizardform").valid()) {
            $('form#NewsletterWizardform').submit();
        }
    });

    $("#testEmail").select2({
        placeholder: __("Search Email"),
        minimumInputLength: 1,
        ajax: {
            url: HOST_PATH + "admin/visitor/searchemails",
            dataType: 'json',
            data: function (term, page) {
                return {
                    keyword: term,
                    flag: 0
                };
            },
            type: 'post',
            results: function (data, page) {
                $("#testEmail").trigger('change');
                return {results: data};
            }
        },
        formatResult: function (data) {
            return data;
        },
        formatSelection: function (data) {
            $("#testEmail").val(data);
            return data;
        }
    });

    $("#dp1").datepicker().on('changeDate', validateScheduleTimestamp);
    function validateScheduleTimestamp()
    {
        $("input[name=scheduleDate]").rules("add", "required");
        $("input[name=scheduleTime]").rules("add", "required");
        $("input[name=senderEmail]").rules("add", "required");
        $("input[name=campaignSubject]").rules("add", "required");
        var sDate = Date.parseExact( jQuery("input#scheduleDate").val() , "dd-MM-yyyy") ;
        var now = new Date() ;
        var currentDate = now.getDate() + "-" + ( now.getMonth() + 1 ) + "-" + now.getFullYear() ;

        currentDate = Date.parseExact( currentDate , "d-M-yyyy");

        // check start date should be greater than or equal to current date

        var sTime = jQuery("input#scheduleTime").val();


        var hasError = false ;
        if( sDate.compareTo ( currentDate ) == 0)
        {
            // check time satrt time is greater than  end time
            if(sTime  >= now.getMinutes())
            {
                hasError = true ;
            } else {
                hasError = false  ;
            }
        }
        if ( sDate.compareTo ( currentDate ) < 0 )
        {
            hasError = true   ;
        }
        if(hasError)
        {
            $('#saveNewsletterCampaign').attr('disabled', "disabled");
            $('#sendTestMail').attr('disabled', "disabled");
            $('#cancel').addClass('disable_a_href disabled')
            $('.nav-tabs a').addClass('disable_a_href');
            $('.next a').addClass('disable_a_href');
            $('.previous a').addClass('disable_a_href');
            jQuery("div.dateValidationMessage").removeClass("success").addClass("error").html(__("<span class='error help-inline'>Shedule date should be greater than current date</span>"))
                .next("div").addClass("error").removeClass("success");
        } else 	{
            $('#saveNewsletterCampaign').removeAttr('disabled');
            $('#sendTestMail').removeAttr('disabled');
            $('#cancel').removeClass('disable_a_href disabled')
            $('.nav-tabs a').removeClass('disable_a_href');
            $('.next a').removeClass('disable_a_href');
            $('.previous a').removeClass('disable_a_href');
            jQuery("div.dateValidationMessage").removeClass("error").addClass("success")
                .html(__("<span class='success help-inline'>Valid</span>"))
                .next("div").removeClass("error").addClass("success");
        }
    }

    jQuery('#scheduleTime').timepicker({
        defaultTime:'value',
        minuteStep: 5,
        template: 'modal',
        showSeconds: false,
        showMeridian: false,
        'afterUpdate'  : validateScheduleTimestamp
    });

});
