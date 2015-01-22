<?php
/**
 * Transl8
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://labs.inovia.fr/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to labs@inovia.fr so we can send you a copy immediately.
 *
 * @category    Transl8
 * @package     Transl8_Controller_Plugin
 * @copyright   Copyright (c) 2011 - Inovia Team (http://www.inovia.fr)
 * @license     http://labs.inovia.fr/license MIT License
 * @author      Inovia-Team
 */

/**
 * Add all necessary javascript, css and html to the response for inline translating.
 *
 * Must be initialized with getFormDataAction() and postAction() urls before use.
 * These actions are in Transl8_Controller_Abstract and must be implemented in your project.
 *
 * @category    Transl8
 * @package     Transl8_Controller_Plugin
 * @copyright   Copyright (c) 2011 - Inovia Team (http://www.inovia.fr)
 * @license     http://labs.inovia.fr/license MIT License
 * @author      Inovia-Team
 */
class Transl8_Controller_Plugin_Transl8 extends Zend_Controller_Plugin_Abstract
{
    /**
     * @var string
     */
    protected $_actionGetFormData   = '/transl8/get-form-data/';

    /**
     * @var string
     */
    protected $_actionSubmit        = '/transl8/submit/';

    /**
     * @var string
     */
    protected $_logo                = '/images/discuss.png';


    /**
     * Set the action that will provide the translation form data
     *
     * @param string $action
     * @return void
     */
    public function setActionGetFormData($action)
    {
        if (!is_string($action)) {
            throw new Exception('$action must be a string');
        }
        $this->_actionGetFormData = $action;
    }

    /**
     * Set the action that will handle the translation form submition
     *
     * @param string $action
     * @return void
     */
    public function setActionSubmit($action)
    {
        if (!is_string($action)) {
            throw new Exception('$action must be a string');
        }
        $this->_actionSubmit = $action;
    }

    /**
     * Set the logo to use for all translations button
     *
     * @param string $logo
     */
    public function setLogo($logo)
    {
        $this->_logo = $logo;
    }

    /**
     * Adds css and javascript to the response
     */
    public function dispatchLoopShutdown()
    {
        $css            = $this->_getCss();
        $js             = $this->_getJs();
        $html           = $this->_getHtml();

        $editionForm    = $this->_getEditionForm();

        $injectionInHeadCode = $css;
        $injectionInBodyCode = '<div>' . $js . $html . $editionForm . '</div>';

        $response = $this->getResponse();
        $response->setBody(preg_replace('/(<head.*>)/i', '$1' . $injectionInHeadCode, $response->getBody()));
        $response->setBody(preg_replace('/(<\/body>)/i', $injectionInBodyCode . '$1', $response->getBody()));
    }

    /**
     * Eliminates null strings and sort the list by value
     *
     * @param array $list
     * @return array
     */
    private function _cleanList($list)
    {
        $view  = Zend_Layout::getMvcInstance()->getView();

        $translationList = array();
        foreach ($list as $value) {
            if (!empty($value)) {
                $translationList[$value] = $view->T($value, null, false);
            }
        }
        asort($translationList);

        // Clean duplicate values (caused by Zend_Form double translation)
        $countValues    = array_count_values($translationList);
        foreach ($translationList as $key => $translation) {
            if ($key == $translation &&
                $countValues[$key] > 1
            ) {
                unset($translationList[$key]);
                $countValues = array_count_values($translationList);
            }
        }
        return $translationList;
    }

    /**
     * @return string
     */
    protected function _getCss()
    {
        $css = <<<CSS
<style type="text/css" media="screen">
#transl8-list-image {
    z-index:9999;
    position:fixed;
    top:5px;
    left:5px;
}
#transl8-list {
    border:1px solid #ccc;
    z-index:999;
    background-color:white;
    position:fixed;
    top:0;
    left:80px;
    -moz-box-shadow:1px 1px 3px #888;
    -webkit-box-shadow:1px 1px 3px #888;
    box-shadow:1px 1px 3px #888;
    display:none;
    padding:10px;
    max-height:250px;
    height:auto;
    overflow:auto;
}
#transl8-list-title {
    font-weight:bold;
    font-size:1.1em;
}

</style>
CSS;
        return $css;
    }

    /**
     * @return string
     */
    protected function _getJs()
    {
        $js = <<<JS
<script type="text/javascript">
$(window).load(function() {

    /**
     * Toggle display of additional translations
     */
    $("#transl8-list-image").click(function(){
        if ($("#transl8-list:visible").size()) {
            $("#transl8-list").fadeOut();
        } else {
            $("#transl8-list").fadeIn();
        }
    });

    /**
     * Load the tooltip when the cursor goes on ".transl8-link-container" Css Classes
     */
    $(".transl8-text").each(function() {
        if (typeof $(this).qtip != "undefined") {
            $(this).qtip( {
                show : "mouseover",
                content : $(this).find("> .transl8-link-container"),
                hide : {fixed : true,when : {event : "mouseout"}},
                position : {adjust : { y : -4},corner : {target : "bottomLeft",tooltip : "topLeft"} },
            });
        } else {
            $(this).find("> .transl8-link-container").show();
        }
    });

    /**
     * Open a modal dialog form when user clicks on "Translate"
     */
    $(".transl8-link").click(function() {
        var translationKey  = $(this).attr('href').split("#")[1];
        $.ajax({
            url: '{$this->_actionGetFormData}',
            dataType: 'json',
            scope: parent,
            data: {translationKey: translationKey},
            error: function (data) {
                alert('Error');
            },
            success: function (data) {
                $.each(data, function(name,value) {
                    $("#transl8-form input[name='" + name + "']").val(value);
                });
                $("#transl8-form textarea").each(function(){
                    $(this).text('');
                });
                $.each(data, function(name, value) {
                    $("#transl8-form textarea[name='" + name + "']").text(value);
                });
                $("#transl8-form-container").dialog("option", "title", data.translationKey);
                $("#transl8-form-container").dialog("option", "draggable", true );
                $("#transl8-form-container").dialog("option", "width", 400 );
                $("#transl8-form-container").dialog("open");
            }
        });
        return false;
    });

    $("#transl8-form-container").dialog({autoOpen:false});
});
</script>
JS;
        return $js;
    }

    /**
     * @return string
     */
    protected  function _getHtml()
    {
        $translationList = $this->_cleanList(
            Transl8_Translate_Adapter_Csv::getTranslatedValues()
        );

        if (empty($translationList)) {
            return '';
        }

        $html = <<<HTML
<div id="transl8-list-image">
    <img src="{$this->_logo}" alt="All translations" />
</div>
HTML;

        $html   .='<ul id="transl8-list">';
        $html   .= '<li><span id="transl8-list-title">Other translations</span></li>';
        foreach ($translationList as $translationKey => $translationValue) {
            $html .='
<li>
  <a class="transl8-link" href="#' . urlencode($translationKey) . '" title="' . $translationKey . '">'
    . $translationValue .
  '</a>
</li>';
            }
        $html .= '</ul>';
        return $html;
    }

    /**
     * @return string
     */
    protected function _getEditionForm()
    {

        $js = <<<JS
<script type="text/javascript">
$(document).ready(function() {

    var redirect    = false;

    $('#transl8-form #translationFormSubmit').click(function(event) {
        redirect   = true;
        $('#transl8-form').submit();
        return false;
    });

    $('#transl8-form').submit(function(event) {
        $.ajax({
            type: 'POST',
            url : '{$this->_actionSubmit}',
            error: function(data) {
                $("#transl8-form-container").html(data.responseText);
            },
            success: function(data){
                if (redirect) {
                    redirect    = false;
                    window.location.reload();
                }
                $("#transl8-form-container").dialog('close');
            },
            data: $(this).serialize()
        });
        return false;
    });
});
</script>
JS;

        $form       = new Transl8_Form();
        $locales    = $form->getLocales();

        $html   = '<div id="transl8-form-container">';
        $html  .= '<form id="transl8-form" action="' . $form->getAction() . '" method="' . $form->getMethod() . '">';
        $html  .= $form->getElement('translationKey')->renderViewHelper();
        foreach ($locales as $locale => $localeLabel) {
            $html .= $form->getElement($locale)->render();
        }
        $html  .= $form->getElement('translationFormSubmit')->renderViewHelper();
        $html  .= $form->getElement('translationFormSubmitNoReload')->renderViewHelper();
        $html  .= '</form></div>';
        return $js . $html;
    }
}
