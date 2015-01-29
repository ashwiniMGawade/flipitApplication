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
 * @package     Transl8_Controller_Action_Helper
 * @copyright   Copyright (c) 2011 - Inovia Team (http://www.inovia.fr)
 * @license     http://labs.inovia.fr/license MIT License
 * @author      Inovia-Team
 */

/**
 * This abstract controller provides 2 actions
 *  - getFormDataAction() : Returns json encoded translations for a translation key.
 *  - postAction() : Saves translations after form submission.
 *
 * @category    Transl8
 * @package     Transl8_Controller_Action_Helper
 * @copyright   Copyright (c) 2011 - Inovia Team (http://www.inovia.fr)
 * @license     http://labs.inovia.fr/license MIT License
 * @author      Inovia-Team
 */
class Transl8_Controller_Action_Helper_Transl8
    extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Returns json encoded translation form data
     */
    public function getFormDataAction()
    {
        $translationKey = urldecode($this->getRequest()->getParam('translationKey'));
        $form           = $this->_createForm();
        $locales        = $form->getLocales();
        $data           = array(
            'translationKey'   => $translationKey
        );
        foreach ($locales as $locale => $localeLabel) {
            $data[$locale]  = $this->_getTranslatedValue(
                $locale,
                $translationKey
            );
        }
        echo json_encode($data);
    }


    /**
     * Changes or creates the translation of the selected red-wrapped words
     */
    public function submitAction()
    {
        $form = $this->_createForm();
        $form->populate($this->getRequest()->getParams());

        if ($this->getRequest()->isPost()) {

            $formValues     = $form->getValues();
            $translationKey = $formValues['translationKey'];
            $writer         = new Transl8_Translate_Writer_Csv();
            $locales        = Transl8_Form::getLocales();

            foreach ($locales as $locale => $localeLabel) {
                $writer->updateTranslation(
                    $translationKey,
                    $locale,
                    $formValues[$locale]
                );
            }

            if (Zend_Translate::hasCache()) {
                Zend_Translate::clearCache();
            }
        }
    }

    /**
     * @return Zend_Translate
     */
    protected function _getTranslator()
    {
        return Zend_Registry::get('Zend_Translate');
    }

    /**
     * Returns translation for given locale and key
     *
     * @param string $locale
     * @param string $translationKey
     *
     * @return string|null Translated value or null if translation key is not found
     */
    protected function _getTranslatedValue($locale, $translationKey)
    {
        $translator = $this->_getTranslator();
        if ($translator->isTranslated($translationKey, false, $locale)) {
            $tranlstionString =  html_entity_decode($translator->translate($translationKey, $locale));
            return $tranlstionString;
        }
        return null;
    }

    /**
     * Builds form for translations
     *
     * @return Transl8_Form
     */
    public function _createForm()
    {
        return new Transl8_Form();
    }
}