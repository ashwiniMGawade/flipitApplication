<?php
namespace KC\Repository;
class Signupmaxaccount Extends \KC\Entity\Signupmaxaccount
{
    #################################################################
    #################### REFACTORED CODE ##############################
    #################################################################
    public function __contruct($connName = false)
    {
        if (! $connName) {
            $connName = "doctrine_site" ;
        }
        echo $connName ;
        Doctrine_Manager::getInstance()->bindComponent($connName, $connName);
    }
    public static function getLocaleName()
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('signupmaxaccount.locale')
            ->from('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
            ->setParameter(1, 1)
            ->where('signupmaxaccount.id = ?1');
        $localName = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $localName;
    }
    public static function getHomepageImages()
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            'signupmaxaccount.homepagebanner_path, signupmaxaccount.homepagebanner_name,
            signupmaxaccount.homepage_widget_banner_path, signupmaxaccount.homepage_widget_banner_name'
        )
            ->from('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
            ->setParameter(1, 1)
            ->where('signupmaxaccount.id = ?1');
        $hoemPageImages = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $hoemPageImages;
    }
    public static function getTestimonials()
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            'signupmaxaccount.testemail, signupmaxaccount.showtestimonial,
            signupmaxaccount.testimonial1, signupmaxaccount.testimonial2, signupmaxaccount.testimonial3'
        )
            ->from('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
            ->setParameter(1, 1)
            ->where('signupmaxaccount.id = ?1');
        $testimonials = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $testimonials;
    }
    public static function getEmailAddress()
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale->select('signupmaxaccount.emailperlocale')
            ->from('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
            ->setParameter(1, 1)
            ->where('signupmaxaccount.id = ?1');
        $email = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $email;
    }
    public static function alterSignupMaxAccountTable()
    {
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $signupmaxaccount = new KC\Entity\Signupmaxaccount();
        $fields = $signupmaxaccount->findOneBy(array('field' => 'locale', 'field' => 'timezone'));
        $entityManagerLocale->remove($fields);
        $entityManagerLocale->flush();
    }
    #################################################################
    #################### END REFACTOR CODE ##########################
    #################################################################
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
    public static function updatestatus($value)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('signupmaxaccount')
            ->from('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
            ->setParameter(1, 1)
            ->where('signupmaxaccount.id = ?1');
        $getRecord = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (empty($getRecord)) {
            $data = new KC\Entity\Signupmaxaccount();
            $data->id = 1;
            $data->status = '';
            \Zend_Registry::get('emLocale')->persist($data);
            \Zend_Registry::get('emLocale')->flush();
        }
        $query = $entityManagerUser->update('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
            ->set('signupmaxaccount.status', $value)
            ->setParameter(1, 1)
            ->where('signupmaxaccount.id = ?1')
            ->getQuery();
        $query->execute();
    }
    public static function getAllMaxAccounts($localeScript = '')
    {
        if ($localeScript != '') {
            $localeTimezoneValues = 'p.locale,p.timezone';
        } else {
            $localeTimezoneValues = '';
        }
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
          ->select(
              'p.id,p.no_of_acc,p.max_account,p.status,p.email_confirmation,
              p.email_header,p.email_footer,
              p.emailperlocale,p.sendername,p.emailsubject,p.testemail,p.showtestimonial,p.testimonial1,
              p.testimonial2,p.testimonial3,p.homepagebanner_path,p.homepagebanner_name,p.homepage_widget_banner_path,
              p.homepage_widget_banner_name,p.newletter_is_scheduled,p.newletter_status,
              p.newsletter_sent_time, p.newletter_scheduled_time
              '.$localeTimezoneValues
          )
          ->from('KC\Entity\Signupmaxaccount', 'p')
          ->setParameter(1, 1)
          ->where('p.id = ?1');
        $allMaxAccounts = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $allMaxAccounts;
    }
    public static function getemailmaxaccounts()
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('signupmaxaccount.emailperlocale, signupmaxaccount.id')
            ->from('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
            ->setParameter(1, 1)
            ->where('signupmaxaccount.id = ?1');
        $emailMaxAccounts = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $emailMaxAccounts;
    }
    public static function updatemaxlimit($maxlimit, $userid)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->update('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
            ->set('signupmaxaccount.entered_uid', $userid)
            ->set('signupmaxaccount.no_of_acc', $maxlimit)
            ->set('signupmaxaccount.max_account', $maxlimit)
            ->setParameter(1, 1)
            ->where('signupmaxaccount.id = ?1')
            ->getQuery();
        $query->execute();
    }
    public static function getStatus()
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('signupmaxaccount.status')
            ->from('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
            ->setParameter(1, 1)
            ->where('signupmaxaccount.id = ?1');
        $status = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $status;
    }
    public static function updatecount($maxlimit, $status)
    {
        $query = $queryBuilder->update('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
            ->set('signupmaxaccount.no_of_acc', $maxlimit)
            ->setParameter(1, 1)
            ->where('signupmaxaccount.id = ?1')
            ->getQuery();
        $query->execute();
    }
    public static function changeEmailConfimationSetting($value)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('signupmaxaccount')
            ->from('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
            ->setParameter(1, 1)
            ->where('signupmaxaccount.id = ?1');
        $getRecord = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (empty($getRecord)) {
            $data = new KC\Entity\Signupmaxaccount();
            $data->id = 1;
            $data->status = '';
            \Zend_Registry::get('emLocale')->persist($data);
            \Zend_Registry::get('emLocale')->flush();
        }
        $query = $entityManagerUser->update('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
            ->set('signupmaxaccount.email_confirmation', $value)
            ->setParameter(1, 1)
            ->where('signupmaxaccount.id = ?1')
            ->getQuery();
        $query->execute();
    }
    public static function getemailConfirmationStatus()
    {
        $status = false;
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('p.id,p.email_confirmation')
        ->from('KC\Entity\Signupmaxaccount', 'p')
        ->setParameter(1, 1)
        ->where('p.id = ?1');
        $getRecord = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (count($getRecord) > 0) {
            $status = $getRecord[0]['email_confirmation'];
        }
        return $status;
    }
    public static function updateHeaderContent($value)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('p')
        ->from('KC\Entity\Signupmaxaccount', 'p')
        ->setParameter(1, 1)
        ->where('p.id = ?1');
        $getRecord = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (empty($getRecord)) {
            $data = new KC\Entity\Signupmaxaccount();
            $data->id = 1;
            $data->email_header = $value;
            \Zend_Registry::get('emLocale')->persist($data);
            \Zend_Registry::get('emLocale')->flush();
            return ;
        }
        $query = $entityManagerUser->update('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
            ->set('signupmaxaccount.email_header', "'". $value . "'")
            ->setParameter(1, 1)
            ->where('signupmaxaccount.id = ?1')
            ->getQuery();
        $query->execute();
    }
    public static function updateFooterContent($value)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('p')
        ->from('KC\Entity\Signupmaxaccount', 'p')
        ->setParameter(1, 1)
        ->where('p.id = ?1');
        $getRecord = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (empty($getRecord)) {
            $data = new KC\Entity\Signupmaxaccount();
            $data->id = 1;
            $data->email_footer = $value ;
            \Zend_Registry::get('emLocale')->persist($data);
            \Zend_Registry::get('emLocale')->flush();
            return ;
        }
        $query = $entityManagerUser->update('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
            ->set('signupmaxaccount.email_footer', "'". $value . "'")
            ->setParameter(1, 1)
            ->where('signupmaxaccount.id = ?1')
            ->getQuery();
        $query->execute();
    }
    public static function getEmailHeaderFooter()
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('p.id,p.email_header,p.email_footer')
        ->from('KC\Entity\Signupmaxaccount', 'p')
        ->setMaxResults(1);
        $emailHeaderFooter = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return  $emailHeaderFooter;
    }
    public static function saveemail($email)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('p')
        ->from('KC\Entity\Signupmaxaccount', 'p')
        ->setParameter(1, 1)
        ->where('p.id = ?1');
        $getRecord = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (empty($getRecord)) {
            $data = new KC\Entity\Signupmaxaccount();
            $data->id = 1;
            $data->emailperlocale = $email ;
            \Zend_Registry::get('emLocale')->persist($data);
            \Zend_Registry::get('emLocale')->flush();
            return ;
        }
        $query = $entityManagerUser->update('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
            ->set('signupmaxaccount.emailperlocale', "'". $email . "'")
            ->setParameter(1, 1)
            ->where('signupmaxaccount.id = ?1')
            ->getQuery();
        $query->execute();
    }
    public static function updateHeaderImage($params)
    {
        if (isset($_FILES['homepageBanner'])) {
            $result = self::uploadImage('homepageBanner');
            if ($result['status'] == '200') {
                $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $entityManagerUser->select('s.homepagebanner_name, s.homepagebanner_path')
                    ->from('KC\Entity\Signupmaxaccount', 's')
                    ->setParameter(1, 1)
                    ->where('s.id = ?1');
                $getRecord = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                if (empty($getRecord)) {
                    $data = new Kc\Entity\Signupmaxaccount();
                    $data->id = 1;
                    $data->status = '';
                    \Zend_Registry::get('emLocale')->persist($data);
                    \Zend_Registry::get('emLocale')->flush();
                } else {
                    $fileName = $getRecord['homepagebanner_name'];
                    $filePath = $getRecord['homepagebanner_path'];
                    # delete previous header image
                    @unlink(ROOT_PATH. $filePath . $fileName);
                    @unlink(ROOT_PATH. $filePath . $result['cmsFilename_prefix'] . $fileName);
                }
                $query = $entityManagerUser->update('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
                    ->set('signupmaxaccount.homepagebanner_name', '?', $result['fileName'])
                    ->set('signupmaxaccount.homepagebanner_path', '?', $result['path'])
                    ->setParameter(1, 1)
                    ->where('signupmaxaccount.id = ?1')
                    ->getQuery();
                $query->execute();
                return $result ;
            }
        }
    }
    public static function deleteHeaderImage($params)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->update('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
                    ->set('signupmaxaccount.homepagebanner_name', '')
                    ->setParameter(1, 1)
                    ->where('signupmaxaccount.id = ?1')
                    ->getQuery();
        $query->execute();
        return true ;
    }
    public static function updateWidgetBackgroundImage($params)
    {
        if (isset($_FILES['homepageWidgetBackground'])) {
            $result = self::uploadImage('homepageWidgetBackground');
            if ($result['status'] == '200') {
                $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $entityManagerUser->select('s')
                    ->from('KC\Entity\Signupmaxaccount', 's')
                    ->setParameter(1, 1)
                    ->where('s.id = ?1');
                $getRecord = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                if (empty($getRecord)) {
                    $data = new KC\Entity\Signupmaxaccount();
                    $data->id = 1;
                    $data->status = '';
                    \Zend_Registry::get('emLocale')->persist($data);
                    \Zend_Registry::get('emLocale')->flush();
                } else {
                    $fileName = $getRecord['homepage_widget_banner_name'];
                    $filePath = $getRecord['homepage_widget_banner_path'];
                    # delete previous header image
                    @unlink(ROOT_PATH. $filePath . $fileName);
                }
                $query = $entityManagerUser->update('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
                    ->set('signupmaxaccount.homepage_widget_banner_name', '?', $result['fileName'])
                    ->set('signupmaxaccount.homepage_widget_banner_path', '?', $result['path'])
                    ->setParameter(1, 1)
                    ->where('signupmaxaccount.id = ?1')
                    ->getQuery();
                $query->execute();
                return $result ;
            }
        }
    }
    public static function deleteWidgetImage($params)
    {
        $query = $entityManagerUser->update('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
                    ->set('signupmaxaccount.homepage_widget_banner_name', '?', 'null')
                    ->setParameter(1, 1)
                    ->where('signupmaxaccount.id = ?1')
                    ->getQuery();
                $query->execute();
        return true ;
    }
    public static function uploadImage($file)
    {
        if (!file_exists(UPLOAD_IMG_PATH)) {
            mkdir(UPLOAD_IMG_PATH);
        }
        $uploadPath = UPLOAD_IMG_PATH . "homepage/header_image/";
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0776, TRUE);
        }
        $adapter = new \Zend_File_Transfer_Adapter_Http();
        $rootPath = ROOT_PATH . $uploadPath;
        $files = $adapter->getFileInfo($file);
        if (!file_exists($rootPath)) {
            mkdir($rootPath);
        }
        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, array('jpg,jpeg,png', true));
        $name = $adapter->getFileName($file, false);
        $newName = time() . "_" . $name;
        $cp = $rootPath . $newName;
        $adapter->addFilter(
            new \Zend_Filter_File_Rename(
                array('target' => $cp,
                    'overwrite' => true
                )
            ),
            null,
            $file
        );
        $adapter->receive($file);
        if ($adapter->isValid($file)) {
            return array("fileName" => $newName, "status" => "200",
                    "msg" => "File uploaded successfully",
                    "path" => $uploadPath);
        } else {
            return array("status" => "-1",
                    "msg" => "Please upload the valid file");
        }
    }
    
    public static function updateNewsletterSchedulingStatus()
    {
        $date = new DateTime();
        $date->modify("+1 days");
        $date = $date->format('Y-m-d H:i:s');
        $currentDate = \FrontEnd_Helper_viewHelper::getCurrentDate();
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->update('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
            ->set('signupmaxaccount.newletter_scheduled_time', '?', $date)
            ->set('signupmaxaccount.newletter_is_scheduled', '?', 0)
            ->set('signupmaxaccount.newletter_status', '?', 1)
            ->set('newsletter_sent_time', '?', $currentDate)
            ->where('signupmaxaccount.id = 1')
            ->getQuery();
        $query->execute();
    }

    public static function saveScheduledNewsletter($request)
    {
        $scheduledDate = $request->getParam("sendDate", false);
        $scheduledTime = $request->getParam("sendTime", false);
        $timezone = $request->getParam("timezone", false);
        $scheduledDate = explode('-', $scheduledDate);
        $scheduledDate = $scheduledDate[1].'-'.$scheduledDate[0].'-'.$scheduledDate[2];
        $currentDate = \FrontEnd_Helper_viewHelper::getCurrentDate();
        $timestamp = date('Y-m-d', strtotime($scheduledDate)).' '.date('H:i:s', strtotime($scheduledTime));
        try {
            $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $entityManagerUser->select('s')
                    ->from('KC\Entity\Signupmaxaccount', 's')
                    ->setParameter(1, 1)
                    ->where('s.id = ?1');
            $getRecord = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            if (empty($getRecord)) {
                    $signupmaxaccount = new KC\Entity\Signupmaxaccount();
                    $signupmaxaccount->id = 1;
                    $signupmaxaccount->newletter_is_scheduled = $this->getRequest()->getParam("isScheduled", false);
                    $signupmaxaccount->newletter_scheduled_time = $timestamp ;
                    $signupmaxaccount->newletter_status = 0 ;
                    \Zend_Registry::get('emLocale')->persist($signupmaxaccount);
                    \Zend_Registry::get('emLocale')->flush();
                return true;
            } else {
                $query = $entityManagerUser->update('KC\Entity\Signupmaxaccount', 'signupmaxaccount')
                    ->set('signupmaxaccount.newletter_scheduled_time', '?', $timestamp)
                    ->set('signupmaxaccount.newletter_is_scheduled', '?', $request->getParam("isScheduled", false))
                    ->set('signupmaxaccount.newletter_status', '?', 0)
                    ->set('newsletter_sent_time', '?', $currentDate)
                    ->where('signupmaxaccount.id = 1')
                    ->getQuery();
                $query->execute();
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}
