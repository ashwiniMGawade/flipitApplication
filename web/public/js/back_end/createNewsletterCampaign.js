$(function(){
        $('#NewsletterWizardform').bootstrapWizard({
            'tabClass': 'nav nav-tabs',
            'onNext': function(tab, navigation, index) {
                return true;
                // wrire validation logic here
            },
            onTabClick: function(tab, navigation, index) {
                return true;
            },
            onTabShow: function(tab, navigation, index) {
                var $total = navigation.find('li').length;
                var $current = index+1;
                var $percent = ($current/$total) * 100;
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
            "campaignHeader" : __("Campaign header looks great"),
            "campaignFooter" : __("Campaign foorer looks great"),
            "senderEmail" : __("Email address looks great"),
            "headerBannerURL" : __("Campaign header banner URL looks great"),
            "footerBannerURL" : __("Campaign footer banner URL looks great")
        };

        /**
         * focusRules oject contain all the messages that are visible on focus of an
         * elelement
         *
         * structure to define a message for element : key is to be element name and Value is
         * message
         */
        var focusRules = {
            "campaignHeader" : __("Please enter valid Campaign header"),
            "campaignFooter" : __("Please enter valid Campaign foorert"),
            "senderEmail" : __("Please enter valid Email address"),
            "headerBannerURL" : __("Please enter valid Campaign header banner URL"),
            "footerBannerURL" : __("Please enter valid Campaign footer banner URL")
        };



    var invalidForm = {} ;
        var errorBy = "" ;

        $.validator.setDefaults({
            onkeyup : false,
            onfocusout : function(element) {
                $(element).valid();
            }

        });

        function resetBorders(el)
        {
            $(el).each(function(i,o){
                $(o).parent('div')
                    .removeClass("error success")
                    .prev("div").removeClass('focus error success') ;

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
                    errorElement : 'span',
                    ignore: ".ignore, :hidden",
                    afterReset  : resetBorders,
                    errorPlacement : function(error, element) {
                        element.parent("div").prev("div")
                            .html(error);
                    },
                    rules : {
                        "senderEmail": {
                            required : true,
                            email    : true
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
                return true;
            } else {
                return false;
            }
        });
    });
