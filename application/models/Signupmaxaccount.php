<?php

/**
 * Signupmaxaccount
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class Signupmaxaccount extends BaseSignupmaxaccount
{
    #################################################################
    #################### REFACTORED CODE ##############################
    #################################################################

    public static function getLocaleName()
    {
        $data = Doctrine_Query::create()
            ->select('p.locale')
            ->from('Signupmaxaccount p')
            ->where('id=1')
            ->fetchOne(null, Doctrine_Core::HYDRATE_ARRAY);
        return $data;
    }

    public static function getHomepageImages()
    {
        $data = Doctrine_Query::create()
        ->select('p.homepagebanner_path,p.homepagebanner_name,p.homepage_widget_banner_path,p.homepage_widget_banner_name')
        ->from('Signupmaxaccount p')
        ->where('id=1')
        ->fetchOne(true,Doctrine::HYDRATE_ARRAY);
        return $data;
    }

    public static function getTestimonials()
    {
        $testimonials = Doctrine_Query::create()
        ->select('p.testemail,p.showTestimonial,p.testimonial1,p.testimonial2,p.testimonial3')
        ->from('Signupmaxaccount p')
        ->where('id=1')
        ->fetchArray();
        return $testimonials;
    }
    
    
    #################################################################
    #################### END REFACTOR CODE ##########################
    #################################################################
    # list of all timezones
    public static $timezones = array(
              'Pacific/Midway'    => "(GMT-11:00) Midway Island",
              'US/Samoa'          => "(GMT-11:00) Samoa",
              'US/Hawaii'         => "(GMT-10:00) Hawaii",
              'US/Alaska'         => "(GMT-09:00) Alaska",
              'US/Pacific'        => "(GMT-08:00) Pacific Time (US &amp; Canada)",
              'America/Tijuana'   => "(GMT-08:00) Tijuana",
              'US/Arizona'        => "(GMT-07:00) Arizona",
              'US/Mountain'       => "(GMT-07:00) Mountain Time (US &amp; Canada)",
              'America/Chihuahua' => "(GMT-07:00) Chihuahua",
              'America/Mazatlan'  => "(GMT-07:00) Mazatlan",
              'America/Mexico_City' => "(GMT-06:00) Mexico City",
              'America/Monterrey' => "(GMT-06:00) Monterrey",
              'Canada/Saskatchewan' => "(GMT-06:00) Saskatchewan",
              'US/Central'        => "(GMT-06:00) Central Time (US &amp; Canada)",
              'US/Eastern'        => "(GMT-05:00) Eastern Time (US &amp; Canada)",
              'US/East-Indiana'   => "(GMT-05:00) Indiana (East)",
              'America/Bogota'    => "(GMT-05:00) Bogota",
              'America/Lima'      => "(GMT-05:00) Lima",
              'America/Caracas'   => "(GMT-04:30) Caracas",
              'Canada/Atlantic'   => "(GMT-04:00) Atlantic Time (Canada)",
              'America/La_Paz'    => "(GMT-04:00) La Paz",
              'America/Santiago'  => "(GMT-04:00) Santiago",
              'Canada/Newfoundland'  => "(GMT-03:30) Newfoundland",
              'America/Buenos_Aires' => "(GMT-03:00) Buenos Aires",
              'Greenland'         => "(GMT-03:00) Greenland",
              'Atlantic/Stanley'  => "(GMT-02:00) Stanley",
              'Atlantic/Azores'   => "(GMT-01:00) Azores",
              'Atlantic/Cape_Verde' => "(GMT-01:00) Cape Verde Is.",
              'Africa/Casablanca' => "(GMT) Casablanca",
              'Europe/Dublin'     => "(GMT) Dublin",
              'Europe/Lisbon'     => "(GMT) Lisbon",
              'Europe/London'     => "(GMT) London",
              'Africa/Monrovia'   => "(GMT) Monrovia",
              'Europe/Amsterdam'  => "(GMT+01:00) Amsterdam",
              'Europe/Belgrade'   => "(GMT+01:00) Belgrade",
              'Europe/Berlin'     => "(GMT+01:00) Berlin",
              'Europe/Bratislava' => "(GMT+01:00) Bratislava",
              'Europe/Brussels'   => "(GMT+01:00) Brussels",
              'Europe/Budapest'   => "(GMT+01:00) Budapest",
              'Europe/Copenhagen' => "(GMT+01:00) Copenhagen",
              'Europe/Ljubljana'  => "(GMT+01:00) Ljubljana",
              'Europe/Madrid'     => "(GMT+01:00) Madrid",
              'Europe/Paris'      => "(GMT+01:00) Paris",
              'Europe/Prague'     => "(GMT+01:00) Prague",
              'Europe/Rome'       => "(GMT+01:00) Rome",
              'Europe/Sarajevo'   => "(GMT+01:00) Sarajevo",
              'Europe/Skopje'     => "(GMT+01:00) Skopje",
              'Europe/Stockholm'  => "(GMT+01:00) Stockholm",
              'Europe/Vienna'     => "(GMT+01:00) Vienna",
              'Europe/Warsaw'     => "(GMT+01:00) Warsaw",
              'Europe/Zagreb'     => "(GMT+01:00) Zagreb",
              'Europe/Athens'     => "(GMT+02:00) Athens",
              'Europe/Bucharest'  => "(GMT+02:00) Bucharest",
              'Africa/Cairo'      => "(GMT+02:00) Cairo",
              'Africa/Harare'     => "(GMT+02:00) Harare",
              'Europe/Helsinki'   => "(GMT+02:00) Helsinki",
              'Europe/Istanbul'   => "(GMT+02:00) Istanbul",
              'Asia/Jerusalem'    => "(GMT+02:00) Jerusalem",
              'Europe/Kiev'       => "(GMT+02:00) Kyiv",
              'Europe/Minsk'      => "(GMT+02:00) Minsk",
              'Europe/Riga'       => "(GMT+02:00) Riga",
              'Europe/Sofia'      => "(GMT+02:00) Sofia",
              'Europe/Tallinn'    => "(GMT+02:00) Tallinn",
              'Europe/Vilnius'    => "(GMT+02:00) Vilnius",
              'Asia/Baghdad'      => "(GMT+03:00) Baghdad",
              'Asia/Kuwait'       => "(GMT+03:00) Kuwait",
              'Europe/Moscow'     => "(GMT+03:00) Moscow",
              'Africa/Nairobi'    => "(GMT+03:00) Nairobi",
              'Asia/Riyadh'       => "(GMT+03:00) Riyadh",
              'Europe/Volgograd'  => "(GMT+03:00) Volgograd",
              'Asia/Tehran'       => "(GMT+03:30) Tehran",
              'Asia/Baku'         => "(GMT+04:00) Baku",
              'Asia/Muscat'       => "(GMT+04:00) Muscat",
              'Asia/Tbilisi'      => "(GMT+04:00) Tbilisi",
              'Asia/Yerevan'      => "(GMT+04:00) Yerevan",
              'Asia/Kabul'        => "(GMT+04:30) Kabul",
              'Asia/Yekaterinburg' => "(GMT+05:00) Ekaterinburg",
              'Asia/Karachi'      => "(GMT+05:00) Karachi",
              'Asia/Tashkent'     => "(GMT+05:00) Tashkent",
              'Asia/Kolkata'      => "(GMT+05:30) Kolkata",
              'Asia/Kathmandu'    => "(GMT+05:45) Kathmandu",
              'Asia/Almaty'       => "(GMT+06:00) Almaty",
              'Asia/Dhaka'        => "(GMT+06:00) Dhaka",
              'Asia/Novosibirsk'  => "(GMT+06:00) Novosibirsk",
              'Asia/Bangkok'      => "(GMT+07:00) Bangkok",
              'Asia/Jakarta'      => "(GMT+07:00) Jakarta",
              'Asia/Krasnoyarsk'  => "(GMT+07:00) Krasnoyarsk",
              'Asia/Chongqing'    => "(GMT+08:00) Chongqing",
              'Asia/Hong_Kong'    => "(GMT+08:00) Hong Kong",
              'Asia/Irkutsk'      => "(GMT+08:00) Irkutsk",
              'Asia/Kuala_Lumpur' => "(GMT+08:00) Kuala Lumpur",
              'Australia/Perth'   => "(GMT+08:00) Perth",
              'Asia/Singapore'    => "(GMT+08:00) Singapore",
              'Asia/Taipei'       => "(GMT+08:00) Taipei",
              'Asia/Ulaanbaatar'  => "(GMT+08:00) Ulaan Bataar",
              'Asia/Urumqi'       => "(GMT+08:00) Urumqi",
              'Asia/Seoul'        => "(GMT+09:00) Seoul",
              'Asia/Tokyo'        => "(GMT+09:00) Tokyo",
              'Asia/Yakutsk'      => "(GMT+09:00) Yakutsk",
              'Australia/Adelaide' => "(GMT+09:30) Adelaide",
              'Australia/Darwin'  => "(GMT+09:30) Darwin",
              'Australia/Brisbane' => "(GMT+10:00) Brisbane",
              'Australia/Canberra' => "(GMT+10:00) Canberra",
              'Pacific/Guam'      => "(GMT+10:00) Guam",
              'Australia/Hobart'  => "(GMT+10:00) Hobart",
              'Australia/Melbourne' => "(GMT+10:00) Melbourne",
              'Pacific/Port_Moresby' => "(GMT+10:00) Port Moresby",
              'Australia/Sydney'  => "(GMT+10:00) Sydney",
              'Asia/Vladivostok'  => "(GMT+10:00) Vladivostok",
              'Asia/Magadan'      => "(GMT+11:00) Magadan",
              'Pacific/Auckland'  => "(GMT+12:00) Auckland",
              'Pacific/Fiji'      => "(GMT+12:00) Fiji",
              'Asia/Kamchatka'    => "(GMT+12:00) Kamchatka",
        );


    public function __contruct($connName = false)
    {
        if(! $connName) {
            $connName = "doctrine_site" ;
        }

        echo $connName ;
        Doctrine_Manager::getInstance()->bindComponent($connName, $connName);
    }


    /**
     * change status of maccount settings
     *
     * @param integre $value
     * @version 1.0
     */
     public static function updatestatus($value)
     {
        $getRecord = Doctrine_Query::create()->select()->from("Signupmaxaccount")->where('id = 1')->fetchArray();
        if(empty($getRecord)){
            $data = new Signupmaxaccount();
            $data->id = 1;
            $data->status = '';
            $data->save();
        }
        $q = Doctrine_Query::create()->update('Signupmaxaccount')
        ->set('status', $value)
        ->where('id=1')
        ->execute();
    }
    public static function getAllMaxAccounts()
    {
        $data = Doctrine_Query::create()
        ->select('p.id,p.no_of_acc,p.max_account,p.status,p.email_confirmation,p.email_header,p.email_footer,p.locale, p.emailperlocale,p.sendername,p.emailsubject,p.testemail,p.showTestimonial,p.testimonial1,p.testimonial2,p.testimonial3,
            p.homepagebanner_path,p.homepagebanner_name,p.homepage_widget_banner_path,p.homepage_widget_banner_name,p.timezone,
            p.newletter_is_scheduled,p.newletter_status,p.newletter_scheduled_time')
        ->from('Signupmaxaccount p')
        ->where('id=1')
        ->fetchArray();
        return $data;
    }


    public static function getemailmaxaccounts()
    {
        $data = Doctrine_Query::create()
        ->select('p.id, p.emailperlocale')
        ->from('Signupmaxaccount p')
        ->where('id=1')
        ->fetchArray();
        return $data;
    }

    public static function updatemaxlimit($maxlimit,$userid)
    {
        $q = Doctrine_Query::create()->update('Signupmaxaccount')
        ->set('entered_uid',$userid)
        ->set('no_of_acc', $maxlimit)
        ->set('max_account', $maxlimit)
        ->where('id=1')
        ->execute();
    }

    public static function getStatus()
    {
        $data = Doctrine_Query::create()
        ->select('p.status')
        ->from('Signupmaxaccount p')
        ->where('id=1')
        ->fetchArray();
        return $data;
    }

    public static function updatecount($maxlimit, $status)
    {
        $q = Doctrine_Query::create()->update('Signupmaxaccount')
        ->set('no_of_acc', $maxlimit)
        //->set('status', $status)
        ->where('id=1')
        ->execute();
    }
    /**
     * Change account email confimation settings
     *
     * @param integre $value
     * @version 1.0
     * @author kraj
     */
    public static function changeEmailConfimationSetting($value)
    {
        $getRecord = Doctrine_Query::create()->select()->from("Signupmaxaccount")->where('id = 1')->fetchArray();
        if(empty($getRecord)){
            $data = new Signupmaxaccount();
            $data->id = 1;
            $data->status = '';
            //$data->emailconfirmation = '';
            $data->save();
        }
        $q = Doctrine_Query::create()->update('Signupmaxaccount')
        ->set('email_confirmation', $value)
        ->where('id=1')
        ->execute();
    }
    /**
     * Get email_confirmation status from datbase
     *
     * @return boolean $data
     * @author kraj
     * @version 1.0
     */
    public static function getemailConfirmationStatus()
    {
            $status = false;
            $data = Doctrine_Query::create()
            ->select('p.id,p.email_confirmation')
            ->from('Signupmaxaccount p')
            ->where('id=1')
            ->fetchArray();
            if(count($data) > 0) {
                $status = $data[0]['email_confirmation'];
            }
            return $status;

    }


    /**
     * update the email header content change
     *
     * @param string $value
     * @author Surinderpal Singh
     */
    public static function updateHeaderContent($value)
    {
        $getRecord = Doctrine_Query::create()->select()->from("Signupmaxaccount")->where('id = 1')->fetchArray();
        if(empty($getRecord)){
            $data = new Signupmaxaccount();
            $data->id = 1;
            $data->email_header = $value;
            $data->save();
            return ;
        }
        $q = Doctrine_Query::create()->update('Signupmaxaccount')
        ->set('email_header', "'". $value . "'")
        ->where('id=1')
        ->execute();
    }

    /**
     * update the email footer content
     *
     * @param string  $value
     * @author Surinderpal Singh
     */
    public static function updateFooterContent($value)
    {
        $getRecord = Doctrine_Query::create()->select()->from("Signupmaxaccount")->where('id = 1')->fetchArray();
        if(empty($getRecord)){
            $data = new Signupmaxaccount();
            $data->id = 1;
            $data->email_footer = $value ;
            $data->save();
            return ;
        }
        $q = Doctrine_Query::create()->update('Signupmaxaccount')
        ->set('email_footer', "'". $value . "'" )
        ->where('id=1')
        ->execute();
    }


    /**
     * get  email header footer content
     *
     * @return array
     * @author Surinderpal Singh
     */
    public static function getEmailHeaderFooter()
    {
        return  Doctrine_Query::create()
        ->select('p.id,p.email_header,p.email_footer')
        ->from('Signupmaxaccount p')
        ->limit(1)
        ->fetchOne(NULL, Doctrine::HYDRATE_ARRAY);

    }

    public static function savelocale($loc)
    {
        $getRecord = Doctrine_Query::create()->select()->from("Signupmaxaccount")->where('id = 1')->fetchArray();
        if(empty($getRecord)){
            $data = new Signupmaxaccount();
            $data->id = 1;
            $data->locale = $loc ;
            $data->save();
            return ;
        }
        $q = Doctrine_Query::create()->update('Signupmaxaccount')
                            ->set('locale', "'". $loc . "'" )
                            ->where('id=1')
                            ->execute();
    }

    /**
     * saveTimezone
     *
     * it save /update currwen timezone
     * @param string $timezone select timezone
     */
    public static function saveTimezone($timezone)
    {
        $getRecord = Doctrine_Query::create()->select()->from("Signupmaxaccount")->where('id = 1')->fetchArray();
        if(empty($getRecord)){
            $data = new Signupmaxaccount();
            $data->id = 1;
            $data->timezone = $timezone ;
            $data->save();
            return ;
        } else {

            $q = Doctrine_Query::create()->update('Signupmaxaccount')
                        ->set('timezone', "'". $timezone . "'" )
                        ->where('id=1')
                        ->execute();
        }

    }


    public static function saveemail($email)
    {
        $getRecord = Doctrine_Query::create()->select()->from("Signupmaxaccount")->where('id = 1')->fetchArray();
        if(empty($getRecord)){
            $data = new Signupmaxaccount();
            $data->id = 1;
            $data->emailperlocale = $email ;
            $data->save();
            return ;
        }
        $q = Doctrine_Query::create()->update('Signupmaxaccount')
                        ->set('emailperlocale', "'". $email . "'" )
                        ->where('id=1')
                        ->execute();
    }

    /**
     *
     * @param array request array  saving SeeIn
     * @return integer id
     */
    public static function updateHeaderImage($params)
    {

            //  upload homaepage header image
                if (isset($_FILES['homepageBanner'])) {


                $result = self::uploadImage('homepageBanner');
                    if ($result['status'] == '200') {



                        $getRecord = Doctrine_Query::create()->select()->from("Signupmaxaccount")->where('id = 1')->fetchOne(null,Doctrine::HYDRATE_ARRAY);
                        if(empty($getRecord)){
                            $data = new Signupmaxaccount();
                            $data->id = 1;
                            $data->status = '';
                            $data->save();
                        }else {

                            $fileName = $getRecord['homepagebanner_name'];
                            $filePath = $getRecord['homepagebanner_path'];


                            # delete previous header image
                            @unlink(ROOT_PATH. $filePath . $fileName);
                            @unlink(ROOT_PATH. $filePath . $result['cmsFilename_prefix'] . $fileName);


                        }

                        $q = Doctrine_Query::create()->update('Signupmaxaccount')
                        ->set('homepagebanner_name', '?' , $result['fileName'])
                        ->set('homepagebanner_path', '?' ,$result['path'])
                        ->where('id=1')
                        ->execute();



                        return $result ;

                    }
                }




    }


    /**
     *
     * @param array request array  saving SeeIn
     * @return integer id
     */
    public static function deleteHeaderImage($params)
    {
        $q = Doctrine_Query::create()->update('Signupmaxaccount')
                    ->set('homepagebanner_name','?' , 'null')
                    ->where('id=1')
                    ->execute();

        return true ;


    }


    /**
     *
     * @param array $params request array  for save/update homepage widgets background
     * @return integer id
     */
    public static function updateWidgetBackgroundImage($params)
    {


        //  upload homaepage header image
        if (isset($_FILES['homepageWidgetBackground'])) {


            $result = self::uploadImage('homepageWidgetBackground');
            if ($result['status'] == '200') {



                $getRecord = Doctrine_Query::create()->select()->from("Signupmaxaccount")->where('id = 1')->fetchOne(null,Doctrine::HYDRATE_ARRAY);
                if(empty($getRecord)){
                    $data = new Signupmaxaccount();
                    $data->id = 1;
                    $data->status = '';
                    $data->save();
                }else {

                    $fileName = $getRecord['homepage_widget_banner_name'];
                    $filePath = $getRecord['homepage_widget_banner_path'];


                    # delete previous header image
                    @unlink(ROOT_PATH. $filePath . $fileName);



                }

                $q = Doctrine_Query::create()->update('Signupmaxaccount')
                ->set('homepage_widget_banner_name', '?' , $result['fileName'])
                ->set('homepage_widget_banner_path', '?' ,$result['path'])
                        ->where('id=1')
                        ->execute();



                        return $result ;

            }
            }




    }

    /**
     *
     * @param array request array  saving SeeIn
     * @return integer id
     */
    public static function deleteWidgetImage($params)
    {
        $q = Doctrine_Query::create()->update('Signupmaxaccount')
                ->set('homepage_widget_banner_name' ,'?', 'null')
                ->where('id=1')
                ->execute();

        return true ;


    }


    /**
     * upload image
     * @param $_FILES[index]  $file
     */
    public static function uploadImage($file)
    {
        if (!file_exists(UPLOAD_IMG_PATH))
            mkdir(UPLOAD_IMG_PATH);

        // generate upload path for images related to shop
        $uploadPath = UPLOAD_IMG_PATH . "homepage/header_image/";

        if(!file_exists($uploadPath))
            mkdir($uploadPath, 0776, TRUE);
        $adapter = new Zend_File_Transfer_Adapter_Http();

        // generate real path for upload path
        $rootPath = ROOT_PATH . $uploadPath;

        // get upload file info
        $files = $adapter->getFileInfo($file);

        // check upload directory exists, if no then create upload directory
        if (!file_exists($rootPath))
            mkdir($rootPath);

        // set destination path and apply validations
        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, array('jpg,jpeg,png', true));

        // get file name
        $name = $adapter->getFileName($file, false);

        // rename file name to by prefixing current unix timestamp
        $newName = time() . "_" . $name;

        // generates complete path of image
        $cp = $rootPath . $newName;

        // apply filter to rename file name and set target
        $adapter->addFilter(
                new Zend_Filter_File_Rename(
                        array('target' => $cp, 'overwrite' => true)),
                null, $file);

        // recieve file for upload
        $adapter->receive($file);

        // check is file is valid then
        if ($adapter->isValid($file)) {

            return array("fileName" => $newName, "status" => "200",
                    "msg" => "File uploaded successfully",
                    "path" => $uploadPath);
        } else {

            return array("status" => "-1",
                    "msg" => "Please upload the valid file");
        }

    }

    /**
    * saveTimezone
    *
    * it save /update currwen timezone
    * @param object $request  request object for newsletter scheduling value
    *
    */
    public static function saveScheduledNewsletter($request)
    {

        $scheduledDate = $request->getParam("sendDate" , false);
        $scheduledTime = $request->getParam("sendTime" , false);
        $timezone = $request->getParam("timezone" , false);;

        $timestamp =   date('Y-m-d',strtotime($scheduledDate)).' '.date('H:i:s',strtotime($scheduledTime)) ;

        try {

            $getRecord = Doctrine_Query::create()->select()->from("Signupmaxaccount")->where('id = 1')->fetchArray();
            if(empty($getRecord)) {
                    $data = new Signupmaxaccount();
                    $data->id = 1;
                    $data->newletter_is_scheduled = $this->getRequest()->getParam("isScheduled" , false) ;
                    $data->newletter_scheduled_time = $timestamp ;
                    $data->newletter_status = 0 ;
                    $data->save();
                return true ;
            } else {

                $q = Doctrine_Query::create()
                        ->update('Signupmaxaccount')
                        ->set('newletter_scheduled_time', '?' , $timestamp)
                        ->set('newletter_is_scheduled', '?' ,$request->getParam("isScheduled" , false))
                        ->set('newletter_status', '?' ,0)
                        ->where('id=1')
                        ->execute();
                return true ;
            }
        } catch (Exception $e) {

            return false;
        }


    }

    /**
     * updateNewsletterSchedulingStatus
     *
     * this will update newsletter status to sent
     *
     */

    public static function updateNewsletterSchedulingStatus()
    {

        # create past date
        $date = new DateTime();
        $date->modify("+1 days");
        $date = $date->format('Y-m-d H:i:s');

        $q = Doctrine_Query::create()
                        ->update('Signupmaxaccount')
                        ->set('newletter_scheduled_time', '?' , $date)
                        ->set('newletter_is_scheduled', '?' , 0)
                        ->set('newletter_status', '?' , 1)
                        ->where('id=1')
                        ->execute();

    }
}

