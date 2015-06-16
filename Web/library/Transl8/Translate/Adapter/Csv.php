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
 * @package     Transl8_Translate_Adapter
 * @copyright   Copyright (c) 2011 - Inovia Team (http://www.inovia.fr)
 * @license     http://labs.inovia.fr/license MIT License
 * @author      Inovia-Team
 */

/**
 * This class keeps track of every used translation key.
 *
 * @category    Transl8
 * @package     Transl8_Translate_Adapter
 * @copyright   Copyright (c) 2011 - Inovia Team (http://www.inovia.fr)
 * @license     http://labs.inovia.fr/license MIT License
 * @author      Inovia-Team
 */
class Transl8_Translate_Adapter_Csv extends Zend_Translate_Adapter_Csv
{
    /**
     * @var array
     */
    private static $_translatedValues = array();


    /**
     * (non-PHPdoc)
     * @see Zend_Translate_Adapter::translate()
     */
    public function translate($messageId, $locale = null)
    {
        self::$_translatedValues[$messageId] = $messageId;
        return parent::translate($messageId, $locale);
    }

    /**
     * @return array
     */
    public static function getTranslatedValues()
    {
        return self::$_translatedValues;
    }

    /**
     * @param string $messageId
     * @return void
     */
    public static function eliminateTranslatableValue($messageId)
    {
        if (array_key_exists($messageId, self::$_translatedValues)) {
            unset(self::$_translatedValues[$messageId]);
        }
    }
}