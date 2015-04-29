<?php
class BackEnd_Helper_viewHelper
{
    #####################################################
    ############# REFACORED CODE ########################
    #####################################################   
    public $zendTranslate = '';
    public function __construct() {
        $this->zendTranslate =Zend_Registry::get('Zend_Translate');
    }

    public function getOnOffButtonsForFeaturedCategory($featuredCategory)
    {
        if($featuredCategory == 1) {
            $featuredOnClass = 'btn-primary default';
            $featuredOffClass = '';
        } else {
            $featuredOnClass = '';
            $featuredOffClass = 'btn-primary default';
        }

        $featuredCategoryButton = '<button onclick="setOnOff(event,\'featured-category\',\'on\');" class="btn '.$featuredOnClass.'" type="button">'.$this->zendTranslate->translate('Yes').'</button>                     
            <button onclick="setOnOff(event,\'featured-category\',\'off\');" class="btn '.$featuredOffClass.'" type="button">'.$this->zendTranslate->translate('No').'</button>';
        return $featuredCategoryButton;
    }

    public static function getLocaleByWebsite($localeId)
    {
        $websiteDetails = Website::getWebsiteDetails($localeId);
        $localeName = explode('/', $websiteDetails['name']);
        $locale = isset($localeName[1]) ?  $localeName[1] : "en";
        return $locale;
    }
    public function getLocaleStatusButtons($localeStatus)
    {
        if ($localeStatus == 'online') {
            $localeOnClass = 'btn-primary default';
            $localeOffClass = '';
        } else {
            $localeOnClass = '';
            $localeOffClass = 'btn-primary default';
        }

        $localeStatusButton = '<button onclick="LocaleStatusToggle(this);"
            class="btn '.$localeOnClass.'"
            data-status="online"
            type="button">'.$this->zendTranslate->translate('Online').'</button>                     
            <button onclick="LocaleStatusToggle(this);" class="btn '.$localeOffClass.'"
            data-status="offline"
            type="button">'.$this->zendTranslate->translate('Offline').'</button>';
        return $localeStatusButton;
    }

    public static function getVarnishUrlsCount()
    {
        $varnishUrlsCount = array();
        $application = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
        $connections = $application->getOption('doctrine');

        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $connectionObject = BackEnd_Helper_DatabaseManager::addConnection($key);
                    $varnish = new Varnish($connectionObject['connName']);
                    $varnishUrlsCount[] = $varnish->getVarnishUrlsCount();
                    BackEnd_Helper_DatabaseManager::closeConnection($connectionObject['adapter']);
                } catch (Exception $e) {
                }
            }
        }
 
        return !empty($varnishUrlsCount) ? array_sum($varnishUrlsCount) : 0;
    }

    public function getOnOffButtonsForArticleFeaturedImage($featuredImage)
    {
        if($featuredImage == 1) {
            $featuredOnClass = 'btn-primary default';
            $featuredOffClass = '';
        } else {
            $featuredOnClass = '';
            $featuredOffClass = 'btn-primary default';
        }

        $featuredImageButton = '<button onclick="featuredImageToggle(event);" value="yes" class="btn '.$featuredOnClass.'" type="button">'.$this->zendTranslate->translate('Yes').'</button>                     
            <button onclick="featuredImageToggle(event);" value="no"  class="btn '.$featuredOffClass.'" type="button">'.$this->zendTranslate->translate('No').'</button>';
        return $featuredImageButton;
    }
    #####################################################
    ############# END REFACORED CODE ####################
    #####################################################
    /**
     * Mail sent ot the user registration and forgot password etc
     * @param array $recipents
     * @param string $subject
     * @param string $body
     * @param string $fromEmail
     * @param string $config
     */
   public static function SendMail($recipents,$subject,$body,$fromEmail = null,$config = null)
    {

        $to = array() ; $cc =array() ; $bcc = array() ;

        foreach($recipents as $key => $value)
        {

            switch($key)
            {
                case "to" :
                    if(gettype($value) == "string")
                    {

                        $to[] = $value;

                    }
                    else
                    {
                        foreach($value as $val)
                        {
                            if(gettype($val) == "string")
                            {
                                $to[] = $val;
                            }
                        }
                    }

                    break;

                case "cc" :
                    if(gettype($value) == "string")
                    {
                        $cc[] = $value;
                    }
                    else
                    {
                        foreach($value as $val)
                        {
                            if(gettype($val) == "string")
                            {
                                $cc[] = $val;
                            }
                        }
                    }

                    break;

                case "bcc" :
                    if(gettype($value) == "string")
                        {
                            $bcc[] = $value;
                        }
                        else
                        {
                            foreach($value as $val)
                            {
                                if(gettype($val) == "string")
                                {
                                    $bcc[] = $val;
                                }
                            }
                        }

                    break;
                default:
                    if(gettype($value) == 'string')
                    {
                        $to[] = $value;
                    }

            }

        }
        if($config == null)
        {
            $config = array('auth' => 'login',
            //'server' => '192.178.1.2',
            'username' => 'rhyme2chetan@gmail.com',
            'password' => 'amritsar@123',
            'ssl' => 'ssl',
            'port' => '465'
            );

        }


        //$transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com',$config);

        $mail = new Zend_Mail();
        $mail->setBodyHtml($body);
        $mail->setBodyText(strip_tags($body));

//      $mail = new Zend_Mail();
//      $mail->setBodyText("Dear ".$params['first_name'].",\n\nYour new account has been created on ".$this->view->SiteName." your login credentials are given below.\nUsername: ".$params['email']."\nPassword: ".$params['password']."\nTo activate your account click on the link given below\n".HTTP_PATH."index/confirm/refId/".base64_encode($registerUser)."\n\nThank You \n".$this->view->SiteName." ");
//      $mail->setFrom($this->view->SiteName, $this->view->EmailFrom);
//      $mail->addTo($params['email'], $params['first_name']);
//      $mail->setSubject('Account detail on '.$this->view->SiteName.' ');
//      $mail->send();

        $email_data = Signupmaxaccount::getemailmaxaccounts();
        $emailFrom  = $email_data[0]['emailperlocale'];
        if(count($to) > 0)
        {
            foreach($to as $email)
            {
                $mail->addTo($email);
            }
            foreach($cc as $email)
            {
                $mail->addCc($email);
            }
            foreach($bcc as $email)
            {
                $mail->addBcc($email);
            }


            $mail->setFrom($emailFrom);
            $mail->setSubject($subject);
            $mail->send();
        }

    }
    /**
     * Split with . and get extension from file
     * @param string $filename
     * @return string
     */
    public static function getImageExtension($filename)
    {

            $pos = strrpos($filename,".");
            $ext = substr($filename,$pos+1);
            return $ext;

    }

    /**
     * Create connection with other databse
     * mean Imbull
     */
    public static function addConnection()
    {

        //      $manager = Doctrine_Manager::getInstance();
        //      $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        //      $options = $bootstrap->getOptions();
        //      $conn2 = $manager->connection($options['doctrine']['imbull'],
        //              "connection1");
        //      $conn2->execute('SHOW TABLES');
        //      if ($conn2 === $manager->getCurrentConnection()) {
        //          //echo 'jee';
        //         return $conn2;
        //      }

    }

    public static function addConnectionSite()
    {

        //  $manager = Doctrine_Manager::getInstance();
        //  $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        //  $options = $bootstrap->getOptions();
        //  $conn2 = $manager->connection($options['doctrine']['dsn'],
        //          "connection2");

        //  if ($conn2 === $manager->getCurrentConnection()) {
        //      //echo 'jee'; die;
        //      return $conn2;
        //  }

    }


    /**
     * Close databse connection
     * mean Imbull
     */


    public static function closeConnection($conn)
    {
        //      $manager = Doctrine_Manager::getInstance();
        //      $manager->closeConnection($conn);

    }
    /**
     * Generate thumnail
     * @param Array $file
     * @param string $orFileName
     * @param integer $toWidth
     * @param integer $toHeight
     * @param string $savePath
     * @author kraj
     */
    public static function resizeImage($file,$orFileName,$toWidth,$toHeight,$savePath)
    {

        $image =$file["name"];
        $img  = $file['tmp_name'];
        if ($image)  {

            $filename = $file['name'];
            $imgInfo = getimagesize($img);
            switch ($imgInfo[2]) {
                case 1: $im = imagecreatefromgif($img); break;
                case 2: $im = imagecreatefromjpeg($img);  break;
                case 3: $im = imagecreatefrompng($img); break;
                default:  @trigger_error('Unsupported filetype!', E_USER_WARNING);

                break;
            }
            if($toWidth==0 && $toHeight!=0) {
                    //get width
                    $width = self::resizeToHeightImage($toHeight,$img);
                    $toWidth = $width;
                    $height = $toHeight;
                }
                elseif($toWidth!=0 && $toHeight==0) {
                    //get height
                    $height     = self::resizeToWidthImage($toWidth,$img);
                    $toHeight = $height;
                    $width      = $toWidth;

                }
                elseif($toWidth!=0 && $toHeight!=0) {
                    $width      = $toWidth;
                    $height = $toHeight;
                }
                $newImg = imagecreatetruecolor($width, $height);
                /* Check if this image is PNG or GIF, then set if Transparent*/
                if(($imgInfo[2] == 1) OR ($imgInfo[2]==3)){
                    imagealphablending($newImg, false);
                    imagesavealpha($newImg,true);
                    $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
                    imagefilledrectangle($newImg, 0, 0, $width, $height, $transparent);
                }

                imagecopyresampled($newImg, $im, 0, 0, 0, 0, $width, $height, $imgInfo[0], $imgInfo[1]);

                //Generate the file, and rename it to $newfilename
                switch ($imgInfo[2]) {
                    case 1: imagegif($newImg,$savePath,100); break;
                    case 2: imagejpeg($newImg,$savePath,100);  break;
                    case 3: imagepng($newImg,$savePath); break;
                    default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
                }
                imagedestroy($im);
                //imagedestroy($tmp);
                imagedestroy($newImg);
            }

        }

        public static function resizeImageForAjax($file,$orFileName,$toWidth,$toHeight,$savePath)
        {

            $image =$file["name"][0];
            $img  = $file['tmp_name'][0];
            if ($image)  {

                $filename = $file['name'][0];
                $imgInfo = getimagesize($img);
                switch ($imgInfo[2]) {
                    case 1: $im = imagecreatefromgif($img); break;
                    case 2: $im = imagecreatefromjpeg($img);  break;
                    case 3: $im = imagecreatefrompng($img); break;
                    default:  trigger_error('Unsupported filetype!', E_USER_WARNING);  break;
                }
                if($toWidth==0 && $toHeight!=0) {
                    //get width
                    $width = self::resizeToHeightImage($toHeight,$img);
                    $toWidth = $width;
                    $height = $toHeight;
                }
                elseif($toWidth!=0 && $toHeight==0) {
                    //get height
                    $height     = self::resizeToWidthImage($toWidth,$img);
                    $toHeight = $height;
                    $width      = $toWidth;

                }
                elseif($toWidth!=0 && $toHeight!=0) {
                    $width      = $toWidth;
                    $height = $toHeight;
                }
                $newImg = imagecreatetruecolor($width, $height);
                /* Check if this image is PNG or GIF, then set if Transparent*/
                if(($imgInfo[2] == 1) OR ($imgInfo[2]==3)){
                    imagealphablending($newImg, false);
                    imagesavealpha($newImg,true);
                    $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
                    imagefilledrectangle($newImg, 0, 0, $width, $height, $transparent);
                }

                imagecopyresampled($newImg, $im, 0, 0, 0, 0, $width, $height, $imgInfo[0], $imgInfo[1]);

                //Generate the file, and rename it to $newfilename
                switch ($imgInfo[2]) {
                    case 1: imagegif($newImg,$savePath,100); break;
                    case 2: imagejpeg($newImg,$savePath,100);  break;
                    case 3: imagepng($newImg,$savePath); break;
                    default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
                }
                imagedestroy($im);
                //imagedestroy($tmp);
                imagedestroy($newImg);
            }

        }


        /**
         * Generate thumnail
         * @param Array $file
         * @param string $orFileName
         * @param integer $toWidth
         * @param integer $toHeight
         * @param string $savePath
         * @author kraj
         */
        public static function resizeImageForFrontEnd($file,$orFileName,$toWidth,$toHeight,$savePath)
        {
            $image =$file["name"];
            $img  = $file['tmp_name'];
            if ($image)  {

                $filename = $file['name'];
                $imgInfo = getimagesize($img);
                switch ($imgInfo[2]) {
                    case 1: $im = imagecreatefromgif($img); break;
                    case 2: $im = imagecreatefromjpeg($img);  break;
                    case 3: $im = imagecreatefrompng($img); break;
                    default:  @trigger_error('Unsupported filetype!', E_USER_WARNING);
                    return  false;
                    break;
                }
                if($toWidth==0 && $toHeight!=0) {
                    //get width
                    $width = self::resizeToHeightImage($toHeight,$img);
                    $toWidth = $width;
                    $height = $toHeight;
                }
                elseif($toWidth!=0 && $toHeight==0) {
                    //get height
                    $height     = self::resizeToWidthImage($toWidth,$img);
                    $toHeight = $height;
                    $width      = $toWidth;

                }
                elseif($toWidth!=0 && $toHeight!=0) {
                    $width      = $toWidth;
                    $height = $toHeight;
                }
                $newImg = imagecreatetruecolor($width, $height);
                /* Check if this image is PNG or GIF, then set if Transparent*/
                if(($imgInfo[2] == 1) OR ($imgInfo[2]==3)){
                    imagealphablending($newImg, false);
                    imagesavealpha($newImg,true);
                    $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
                    imagefilledrectangle($newImg, 0, 0, $width, $height, $transparent);
                }

                imagecopyresampled($newImg, $im, 0, 0, 0, 0, $width, $height, $imgInfo[0], $imgInfo[1]);

                //Generate the file, and rename it to $newfilename
                switch ($imgInfo[2]) {
                    case 1: imagegif($newImg,$savePath,100); break;
                    case 2: imagejpeg($newImg,$savePath,100);  break;
                    case 3: imagepng($newImg,$savePath); break;
                    default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
                }
                imagedestroy($im);
                //imagedestroy($tmp);
                imagedestroy($newImg);
                return  true;
            }

        }

    /**
     * Generate thumnail for images which are picked from a folder
     * @param string $originalImage
     * @param integer $toWidth
     * @param integer $toHeight
     * @param string $path
     * @param string $type
     * @author Raman modified by kraj
     */
    public static function resizeImageFromFolder($originalImage, $toWidth, $toHeight, $savePath, $type)
    {
        ini_set("memory_limit", "256M");
        $imgType = $type;
        $image =$originalImage;
        $img  = $originalImage;
        if ($image)  {

            $filename = $originalImage;
            $imgInfo = getimagesize($originalImage);
            switch ($imgInfo[2]) {
                case 1: $im = imagecreatefromgif($img); break;
                case 2: $im = imagecreatefromjpeg($img);  break;
                case 3: $im = imagecreatefrompng($img); break;
                default:  @trigger_error('Unsupported filetype!', E_USER_WARNING);
                break;
            }
            if($toWidth==0 && $toHeight!=0) {
                //get width
                $width = self::resizeToHeightImage($toHeight,$img);
                $toWidth = $width;
                $height = $toHeight;
            }
            elseif($toWidth!=0 && $toHeight==0) {
                //get height
                $height     = self::resizeToWidthImage($toWidth,$img);
                $toHeight = $height;
                $width      = $toWidth;

            }
            elseif($toWidth!=0 && $toHeight!=0) {
                $width      = $toWidth;
                $height = $toHeight;
            }
            $newImg = imagecreatetruecolor($width, $height);
            # Check if this image is PNG or GIF, then set if Transparent
            if(($imgInfo[2] == 1) OR ($imgInfo[2]==3)){
                imagealphablending($newImg, false);
                imagesavealpha($newImg,true);
                $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
                imagefilledrectangle($newImg, 0, 0, $width, $height, $transparent);
            }

            imagecopyresampled($newImg, $im, 0, 0, 0, 0, $width, $height, $imgInfo[0], $imgInfo[1]);

            # Generate the file, and rename it to $newImg
            switch ($imgInfo[2]) {
                case 1: imagegif($newImg,$savePath,100); break;
                case 2: imagejpeg($newImg,$savePath,100);  break;
                case 3: imagepng($newImg,$savePath); break;
                default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
            }
            imagedestroy($im);
            //imagedestroy($tmp);
            imagedestroy($newImg);
        }
    }
    /**
     * get the width of image
     * @author kraj
     * @return  $size
     */
    public static function getWidthImage($file) {

        $size = getimagesize($file);
        return $size[0];
    }
    /**
     * get the height of image
     * @author kraj
     * @return  $size
     */
    public static function getHeightImage($file) {

        $size = getimagesize($file);
        return $size[1];
    }
    /**
     * get size of uploaded image
     * @author kraj
     * @return $width
     */
    public static function resizeToHeightImage($height,$file) {

        $ratio = $height / self::getHeightImage($file);
        $width = self::getWidthImage($file) * $ratio;
        return  $width;
        //self::resize($width,$height);
    }
    /**
     * get size of uploaded image
     * @author kraj
     * @return $height
     */
    public static function resizeToWidthImage($width,$file) {
        $ratio = $width / self::getWidthImage($file);
        $height = self::getheightImage($file) * $ratio;
        return  $height;
        //self::resize($width,$height);
    }
    /**
     * function for image upload
     * @author mkaur
     *
     */
    var $image;
    var $image_type;

    function load($filename) {

        $image_info = getimagesize($filename);
        $this->image_type = $image_info[2];
        if($this->image_type == IMAGETYPE_JPEG ) {

            $this->image = imagecreatefromjpeg($filename);
        } elseif( $this->image_type == IMAGETYPE_GIF ) {

            $this->image = imagecreatefromgif($filename);
        } elseif( $this->image_type == IMAGETYPE_PNG ) {

            $this->image = imagecreatefrompng($filename);
        }
    }
    function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {

        if( $image_type == IMAGETYPE_JPEG ) {
            imagejpeg($this->image,$filename,$compression);
        } elseif( $image_type == IMAGETYPE_GIF ) {

            imagegif($this->image,$filename);
        } elseif( $image_type == IMAGETYPE_PNG ) {

            imagepng($this->image,$filename);
        }
        if( $permissions != null) {

            chmod($filename,$permissions);
        }
    }
    function output($image_type=IMAGETYPE_JPEG) {

        if( $image_type == IMAGETYPE_JPEG ) {
            imagejpeg($this->image);
        } elseif( $image_type == IMAGETYPE_GIF ) {

            imagegif($this->image);
        } elseif( $image_type == IMAGETYPE_PNG ) {

            imagepng($this->image);
        }
    }
    public function getWidth() {

        return imagesx($this->image);
    }
    function getHeight() {

        return imagesy($this->image);
    }
    function resizeToHeight($height) {

        $ratio = $height / self::getHeight();
        $width = self::getWidth() * $ratio;
        self::resize($width,$height);
    }

    function resizeToWidth($width) {
        $ratio = $width / self::getWidth();
        $height = self::getheight() * $ratio;
        self::resize($width,$height);
    }

    function scale($scale) {
        $width = self::getWidth() * $scale/100;
        $height = self::getheight() * $scale/100;
        self::resize($width,$height);
    }

    function resize($width,$height) {
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, self::getWidth(), self::getHeight());
        $this->image = $new_image;
    }

    public static function stripSlashesFromString($string){

        $search = array(
                '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
                '@[\\\]@'   // Strip out slashes
        );

        $output = preg_replace($search, array('',''), $string);

        return $output;
    }

    /**
     * currentRelease
     *
     * fucntion returns the current version and release date
     * @return array
     * @author Kim Pellikaan
     */

    public static function currentRelease()
    {

        // Getting the currently deployed version and displaying in the sidebar
        if ($handle = opendir('/var/www/flipit.com/releases')) {

            # This is the correct way to loop over the directory.
            while (false !== ($entry = readdir($handle))){
                if ($entry != "." && $entry != "..") {
                        $dir[] = $entry;
                }
            }
            closedir($handle);
        }

        rsort($dir);
        $split = explode("_", $dir[0]);
        $releaseDate = $split[1];
        $version = $split[2];

        return array('version' => $version , 'releaseDate' => $releaseDate );

    }



    /**
     * random md5 poassword generator
     *
     *
     * @return password string
     * @author sp singh
     */

    public static function randomPassword()
    {

        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";

        $pass = array(); //remember to declare $pass as an array

        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache

        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return md5(implode($pass)); //turn the array into a string
    }



    /**
     * removeScriptTag
     *
     * This function is specially designd for backend text editor input to strip out javasctrip tags
     *
     * @param string $input
     * @param boolean $stripTags set true for strip out all tags
     */

    public static function  removeScriptTag($input,$stripTags = false) {

        if (is_array($input)) {
            foreach($input as $var=>$val) {
                $output[$var] = FrontEnd_Helper_viewHelper::sanitize($val);
            }
        }
        else {

            if (get_magic_quotes_gpc()) {
                $input = stripslashes($input);
            }

            if($stripTags)
            {
                $intput = strip_tags($input);
            }

            $search = array(
                    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
                    '@<![\s\S]*?--[ \t\n\r]*>@'
            );

            $output = preg_replace($search, '', $input);

            $output = trim(rtrim(rtrim($output)));

        }
        return $output;
    }


        /**
    * Sort a 2 dimensional array based on 1 or more indexes.
    *
    * msort() can be used to sort a rowset like array on one or more
    * 'headers' (keys in the 2th array).
    *
    * @param array        $array      The array to sort.
    * @param string|array $key        The index(es) to sort the array on.
    * param string|array  $preserveValue set values which you don't want to sort or keep current position. these values would always be on first after that sorted values
    * @param int          $sort_flags The optional parameter to modify the sorting
    *                                 behavior. This parameter does not work when
    *                                 supplying an array in the $key parameter.
    *
    * @return array The sorted array.
    */
    public static function msort($array, $key,$preserveValue = false, $sort_flags = SORT_REGULAR) {

        if (is_array($array) && count($array) > 0) {
            if (!empty($key)) {
                $mapping = array();
                foreach ($array as $k => $v) {
                    $sort_key = '';
                    if (!is_array($key)) {

                        # keep same index for preserved values
                        if($preserveValue &&  ( $preserveValue == $v[$key] || is_array($preserveValue) && in_array($v[$key], $preserveValue)) )
                        {
                            $sort_key = "" ;
                        } else{

                            $sort_key = $v[$key];
                        }

                    } else {

                        foreach ($key as $key_key) {

                            # keep same index for preserved values
                            if($preserveValue &&  ( $preserveValue == $v[$key_key] || is_array($preserveValue) && in_array($v[$key_key], $preserveValue)) )
                            {
                                continue;
                            }
                            $sort_key .= $v[$key_key];

                        }

                        $sort_flags = SORT_STRING;
                    }
                    $mapping[$k] = $sort_key;
                }

                asort($mapping, $sort_flags);
                $sorted = array();
                foreach ($mapping as $k => $v) {

                    $sorted[] = $array[$k];
                }
                return $sorted;
            }
        }
        return $array;
    }

    /**
     * getTopVouchercodesDatamandrill
     *
     * This function loops the offer data and set the needed data in gloabal arrays for mandrill newsletter
     *
     * @param array $topVouchercodes
     * @author cbhopal
     * @version 1.0
     */
    public static function getTopVouchercodesDataMandrill($topVouchercodes)
    {

           
            $path =  defined('HTTP_PATH_FRONTEND') ? HTTP_PATH_FRONTEND :  HTTP_PATH_LOCALE ;
            $publicPath  =  defined('PUBLIC_PATH_CDN') ? PUBLIC_PATH_CDN :  PUBLIC_PATH ;
            
            $dataShopName = $dataShopImage =  $shopPermalink = $expDate = $dataOfferName = array();

        foreach ($topVouchercodes as $key => $value) {

            $permalinkEmail = $path . $value['offer']['shop']['permaLink'].'?utm_source=transactional&utm_medium=email&utm_campaign='.date('d-m-Y');
            //sets the $dataShopName array with shop names
            $dataShopName[$key]['name'] = "shopTitle_".($key+1);
            $dataShopName[$key]['content'] = "<a style='color:#333333; text-decoration:none;'href='$permalinkEmail'>".$value['offer']['shop']['name']."</a>";

            //sets the $dataOfferName array with offer names
            $dataOfferName[$key]['name'] = "offerTitle_".($key+1);
            $dataOfferName[$key]['content'] = $value['offer']['title'];

            //set the logo for shop if it exists or not in $dataShopImage array
            if(count($value['offer']['shop']['logo']) > 0):
                $img = $publicPath.$value['offer']['shop']['logo']['path'].'thum_medium_store_'. $value['offer']['shop']['logo']['name'];
            else:
                $img = $publicPath."images/NoImage/NoImage_200x100.jpg";
            endif;

            $dataShopImage[$key]['name'] = 'shopLogo_'.($key+1);
            $dataShopImage[$key]['content'] = "<a href='$permalinkEmail'><img src='$img'></a>";

            //set $expDate array with the expiry date of offer
            $expiryDate = new Zend_Date($value['offer']['endDate']);
            $expDate[$key]['name'] = 'expDate_'.($key+1);
            $expDate[$key]['content'] = FrontEnd_Helper_viewHelper::__link('link_Verloopt op:') ." " . $expiryDate->get(Zend_Date::DATE_LONG);

            //set $shopPermalink array with the permalink of shop
            $shopPermalink[$key]['name'] = 'shopPermalink_'.($key+1);
            $shopPermalink[$key]['content'] = $permalinkEmail;
        }

        return array('dataShopName' => $dataShopName,
                     'dataShopImage' => $dataShopImage,
                     'shopPermalink' => $shopPermalink,
                     'expDate' => $expDate,
                     'dataOfferName' =>  $dataOfferName );
    }


    /**
     * getTemplateId
     * get template id
     * @param type of template
     * @author asharma
     */

    public static function getTemplateId($type)
    {
        $id = Emails::getTemplateId($type);
        return $id;
        
    }


    /**
     * insertTemplateData
     * insert Template Data for inserting send date, counter
     * @param id of template
     * @author asharma
     */

    public static function insertTemplateData($id)
    {
        $sendCounter =  Emails::getSendCounter($id);
        $newCounterValue = $sendCounter + 1;
        Emails::updateDateCounter($newCounterValue , $id);
        
    }

    public static function uploadExcel($file, $import = false, $type = '')
    {
        if (!file_exists(UPLOAD_EXCEL_PATH)) {
            mkdir(UPLOAD_EXCEL_PATH, 0776, true);
        }
        
        $rootPath = UPLOAD_EXCEL_PATH;
        if ($import) {
            $rootPath .= 'import/';
        }

        if (!file_exists($rootPath)) {
            mkdir($rootPath, 0775, true);
        }
        $maximumUploadLimit = $type == 'offer' ? '2MB' : '2MB';
        $minimumUploadLimit = $type == 'offer' ? '5' : '20';
        $adapter = new Zend_File_Transfer_Adapter_Http();
        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, array('xlsx', true));
        $adapter->addValidator('Size', false, array('min' => $minimumUploadLimit, 'max' => $maximumUploadLimit));
        $files = $adapter->getFileInfo($file);
        $fileName = $adapter->getFileName($file, false);
        $newFileName = time() . "_" . $fileName;
        $changedFilePath = $rootPath . $newFileName;
        $adapter
        ->addFilter(
            new Zend_Filter_File_Rename(
                array(
                    'target' => $changedFilePath,
                    'overwrite' => true
                )
            ),
            null,
            $file
        );
        $adapter->receive($file);
        $messages = $adapter->getMessages();
        echo '<pre>'.print_r($messages, true).'</pre>';
        if ($adapter->isValid($newFileName)) {
            return array(
                "fileName" => $newFileName,
                "status" => "200",
                "msg" => "File uploaded successfully",
                "path" => $rootPath
            );
        } else {
            return array(
                "status" => "-1",
                "msg" => "Please upload the valid file"
            );
        }
    }
    
    public static function convertOfferTimeToServerTime($offerTime)
    {
        $refreshTime = new DateTime($offerTime, new DateTimeZone(LOCALE_TIMEZONE));
        $refreshTime->setTimezone(new DateTimeZone('Europe/Amsterdam'));
        return $refreshTime->format('Y-m-d H:i:s');
    }

    public static function convertCurrentTimeToServerTime()
    {
        $refreshTime = new DateTime('now', new DateTimeZone(LOCALE_TIMEZONE));
        $refreshTime->setTimezone(new DateTimeZone('Europe/Amsterdam'));
        return $refreshTime->format('Y-m-d H:i:s');
    }
}