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
 * @package     Transl8_Translate_Writer
 * @copyright   Copyright (c) 2011 - Inovia Team (http://www.inovia.fr)
 * @license     http://labs.inovia.fr/license MIT License
 * @author      Inovia-Team
 */

/**
 * Class used to write translations in a CSV file.
 *
 * Must be initialized with a destination folder where to put generated translation files
 *
 * @category    Transl8
 * @package     Transl8_Translate_Writer
 * @copyright   Copyright (c) 2011 - Inovia Team (http://www.inovia.fr)
 * @license     http://labs.inovia.fr/license MIT License
 * @author      Inovia-Team
 */
class Transl8_Translate_Writer_Csv
{
    /**
     * @var string
     */
    protected static $_destinationFolder;

    /**
     * @param string $destinationFolder
     * @return void
     */
    public static function setDestinationFolder($destinationFolder)
    {
        self::$_destinationFolder   = $destinationFolder;
    }

    /**
     * @return string
     */
    public static function getDestinationFolder()
    {
        return self::$_destinationFolder;
    }

    /**
     * Adds or modifies a translation for given key and locale
     *
     * @param string $translationKey
     * @param string $locale
     * @param string $newValue
     * @throws Exception
     */
    public function updateTranslation($translationKey, $locale, $newValue)
    {
        $locationDir        = self::getDestinationFolder() . '/' . $locale;
        $locationFile       = $locationDir . '/translations.csv';

        $openMode = 'r';
        if (!file_exists($locationFile)) {
            $openMode = 'w+';
        }
        $fileHandler        = fopen($locationFile, $openMode);

        if (!$fileHandler) {
            throw new Exception('Unable to open file ' . $locationFile . ' for reading');
        }

        $hasTranslate       = false;
        $finalArrayDataCsv  = array();

        while ($data = fgetcsv($fileHandler,null,';')) {

            list($csvTranslationKey,$csvValue) = $data;

            if ($csvTranslationKey == $translationKey) {
                $hasTranslate   = true;
                $csvValue       = $newValue;
            }

            if (!empty($csvValue)) {
                $finalArrayDataCsv[$csvTranslationKey] = $csvValue;
            }
        }
        if (!$hasTranslate && !empty($newValue)) {
            $finalArrayDataCsv[$translationKey] = $newValue;
        }

        fclose($fileHandler);

        $fileHandler2 = fopen($locationFile, 'w+');
        if (!$fileHandler2) {
            throw new Exception('Unable to open file ' . $locationFile . ' for writing');
        }
        
        while(!flock($fileHandler2, LOCK_EX)) { 
            sleep(1); 
        }
        
        $this->fputcsvCustom($fileHandler2, $finalArrayDataCsv, ';'); 

        flock($fileHandler2, LOCK_UN); 

        fclose($fileHandler2); 
    }

    /**
     * Same as fputcsv but force the insertion of the enclosure parameter.
     *
     * @param fileHandler $filePointer
     * @param array $dataArray
     * @param string $delimiter
     * @param string $enclosure
     */
    private function fputcsvCustom($filePointer,$dataArray,$delimiter=";",$enclosure="\"")
    {
        $string = "";

        // for each array element, which represents a line in the csv file...
        foreach($dataArray as $trsKey => $trsValue) {

            $elems    = array($trsKey, $trsValue);
            // No leading delimiter
            $writeDelimiter = FALSE;

            foreach($elems as $dataElement){
                // Replaces a double quote with two double quotes
                $dataElement=str_replace("\"", "\"\"", $dataElement);

                // Adds a delimiter before each field (except the first)
                if($writeDelimiter) $string .= $delimiter;

                // Encloses each field with $enclosure and adds it to the string
                $string .= $enclosure . $dataElement . $enclosure;

                // Delimiters are used every time except the first.
                $writeDelimiter = TRUE;
            }
            // Append new line
            $string .= "\n";

        } // end foreach($dataArray as $line)

        // Write the string to the file
        fwrite($filePointer,$string);
    }
}