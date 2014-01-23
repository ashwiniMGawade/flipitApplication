<?php

# run shop export script
require  'shopExportScript.php';


# run offer export script
require 'offerExportScript.php';


# run visitor export script
require 'visitorExportScript.php';


#run global shop  export script
require 'globalShopExportScript.php';

#run export all shops in associated json file for each locale
require 'generateAllShopsJsonForSearch.php';