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
 * @package     Transl8_View_Helper
 * @copyright   Copyright (c) 2011 - Inovia Team (http://www.inovia.fr)
 * @license     http://labs.inovia.fr/license MIT License
 * @author      Inovia-Team
 */

/**
 * View helper that will output html around the translation if inline translation is enabled.
 * The helper also provides a way to merge values in translation using str_replace.
 *
 * @category    Transl8
 * @package     Transl8_View_Helper
 * @copyright   Copyright (c) 2011 - Inovia Team (http://www.inovia.fr)
 * @license     http://labs.inovia.fr/license MIT License
 * @author      Inovia-Team
 */
class Transl8_View_Helper_Translate extends Zend_View_Helper_Abstract
{
	protected $_defaultPlaceholder = '%value%';

	/**
	 * @see Transl8_View_Helper_T::translate()
	 */
	// public function T($message, $param = null, $inlineTranslation = true)
	// {
	//     return $this->translate($message, $param, $inlineTranslation);
	// }

    /**
     * Return a content item into a local translated string
     *
     * @param string $message Translation key
     * @param string|mixed $param Parameters to include in translation string
     * @param bool $inlineTranslation Add or not necessary HTML code for inline translation
     * @return string
     */
	public function translate($message, $param = null, $inlineTranslation = true)
	{
        $translate      = $this->_getTranslator();

        $translation    = html_entity_decode($translate->translate($message));

        // Replace placeholder(s) with associated values
        if (is_string($param)) {
            $translation = str_replace($this->_defaultPlaceholder, $param, $translation);
        } elseif (is_array($param)) {
            foreach ($params as $token => $value) {
                $translation = str_replace($token, $value, $translation);
            }
        }

        if (Zend_Registry::get('Transl8_Activated') && $inlineTranslation) {

            // Remove key from global list of translation
            Transl8_Translate_Adapter_Csv::eliminateTranslatableValue($message);
                
            // Add necessary HTML around translated value
            $translation = '<span class="transl8-text">'
                         . $translation
                         . '<span class="transl8-link-container">'
                         . '<a class="transl8-link" href="#' . urlencode($message) . '" title="'. $message . '">Translate</a>'
                         . '</span>'
                         . '</span>';
        }
        return $translation;
	}

	/**
	 * @return Zend_Translate
	 */
	protected function _getTranslator()
	{
	    return Zend_Registry::get('Zend_Translate');
	}
}