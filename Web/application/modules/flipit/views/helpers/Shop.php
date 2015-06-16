<?php
class Zend_View_Helper_Shop extends Zend_View_Helper_Abstract
{
    public $view;

    public function shop()
    {
        return $this;
    }
    public function __invoke() {
        $this->view = $this->getView();
        $this->view->translate();
    }

    public function brandingCss($brandingCss)
    {
        $css = '';
        foreach ($brandingCss as $cssStyle){
            if(!empty($cssStyle['css-selector'])){
                $css .= $cssStyle['css-selector'].'{'.$cssStyle['css-property'].':'.$cssStyle['value']."}\r\n";
            }
        }

        if(!empty($brandingCss['header_background']['img'])){
            $css .= '#wrapper{';
                $css .= 'background: url('.PUBLIC_PATH.$brandingCss['header_background']['img'].') no-repeat;';
                $css .= 'background-size:100%;';
                $css .= 'background-position: 0% 1%;';
            $css .= '}';
        }

        if(!empty($brandingCss['overwrite']['value'])){
            $css .= $brandingCss['overwrite']['value'];
        }

        return $css;
    }

    public function brandingJs($brandingJs)
    {
        $js = "";
        if(!empty($brandingJs['newsletter_store_logo']['img'])){
            $js .= '$(function () {';
                $js .= "$('.section .block-form .icon img').attr(\"src\", '".PUBLIC_PATH.$brandingJs['newsletter_store_logo']['img']."');";
            $js .= '});';
        }
        return $js;
    }

    public function transtest(){
        echo "Dit moet ook inline trans zijn ".$this->view->translate('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa').' is het dat ook?';
    }
}
