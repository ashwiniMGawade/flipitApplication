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
 * @package     Transl8_Form
 * @copyright   Copyright (c) 2011 - Inovia Team (http://www.inovia.fr)
 * @license     http://labs.inovia.fr/license MIT License
 * @author      Inovia-Team
 */

/**
 *
 * Form class for translations edition
 *
 * Instanciates
 *  - 1 text element for each enabled language
 *  - 1 hidden element that will store the translation key
 *  - 2 submit elements (1 to submit and refresh the page and 1 to submit without refresh)
 *
 * This class needs to be initialized with an array of enabled languages before use.
 *
 *
 * @category    Transl8
 * @package     Transl8_Form
 * @copyright   Copyright (c) 2011 - Inovia Team (http://www.inovia.fr)
 * @license     http://labs.inovia.fr/license MIT License
 * @author      Inovia-Team
 */
class Transl8_Form extends Zend_Form
{
    /**
     * @var array of Zend_Locale
     */
    protected static $_locales = array();

    /**
     * (non-PHPdoc)
     * @see Zend_Form::init()
     */
    public function init()
    {
        $locales    = $this->getLocales();

        // Add text fields for each locale
        foreach ($locales as $locale => $localeLabel) {
            $element = new Zend_Form_Element_Textarea($locale);
            //$element->setAttribs(array('cols' => 25, 'rows' => 3));
            $element->setAttribs(array('style' => 'width:100%;', 'rows' => 3));
            $element->setLabel($localeLabel);
            $this->addElement($element);
        }

        // Add keyName hidden field
        $elementKey = new Zend_Form_Element_Hidden('translationKey');
        $this->addElement($elementKey);

        // Add submit buttons
        $submitButtonReload   = new Zend_Form_Element_Submit(
            'translationFormSubmit'
        );
        $submitButtonReload->setAttrib( 'class', 'btn btn-large btn-primary');
        $submitButtonReload->setLabel('Save and Reload');
        $this->addElement($submitButtonReload);

        $submitButtonNoReload   = new Zend_Form_Element_Submit(
            'translationFormSubmitNoReload'
        );
        $submitButtonNoReload->setAttrib( 'class', 'btn btn-large btn-primary');
        $submitButtonNoReload->setLabel('Save');
        $this->addElement($submitButtonNoReload);
    }

    /**
     * @return Zend_Translate
     */
    protected function _getTranslator()
    {
        return Zend_Registry::get('Zend_Translate');
    }

    /**
     * Returns all languages from Zend_Translate instance
     *
     * @return array of Zend_Locale
     */
    public static function getLocales()
    {
        return self::$_locales;
    }

    /**
     * Sets available locales for edition
     *
     * @param array $locales An array of Zend_Locale
     * @return void
     */
    public static function setLocales($locales)
    {
        if (!is_array($locales)) {
            $locales    = array($locales);
        }
        self::$_locales = $locales;
    }
}
