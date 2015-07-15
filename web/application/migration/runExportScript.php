<?php

# run offer export script
require 'offerExportScript.php';

# run visitor export script
//require 'visitorExportScript.php';

#run global shop  export script
require 'globalShopExportScript.php';

#run export all shops in associated json file for each locale
require 'generateAllShopsJsonForSearch.php';

#run cretae translation json fiel for each locale (used for javascript translations)
require 'generateJsonForJsTranlastion.php';

#run create popular offers for all site
require 'GeneratePopularCodes.php';
