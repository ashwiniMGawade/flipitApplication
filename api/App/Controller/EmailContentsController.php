<?php
namespace Api\Controller;

use \Core\Domain\Factory\AdminFactory;
use \Core\Domain\Factory\SystemFactory;
use \Core\Domain\Factory\TranslationsFactory;
use Core\Service\Config;
use \Nocarrier\Hal;
use \Core\Service\Errors;

class EmailContentsController extends ApiBaseController
{
    protected $data;
    protected $translator;
    protected $websiteName;
    protected $emailTypes = array(
        'newsletter'
    );

    private function initData()
    {
        $this->defineConstants();
        $this->websiteName = LOCALE != '' ? 'Flipit' : 'Kortingscode';
        $this->data = array(
            'header_text'   => '',
            'header'        => '',
            'content'       => '',
            'footer'        => ''
        );
    }

    private function defineConstants()
    {
        $config = new Config();
        $cdnPath = LOCALE === '' ? (isset($config->getConfig()->cdn->url->kortingscode) ? $config->getConfig()->cdn->url->kortingscode : 'img.kortingscode.nl/public') : (isset($config->getConfig()->cdn->url->flipit) ? $config->getConfig()->cdn->url->flipit : 'img.flipit.com/public');
        defined('HTTP_PATH')        || define('HTTP_PATH', LOCALE != '' ? 'http://www.flipit.com/' : 'http://www.kortingscode.nl/');
        defined('HTTP_PATH_LOCALE') || define('HTTP_PATH_LOCALE', LOCALE != '' ? HTTP_PATH.LOCALE.'/' : HTTP_PATH);
        defined('PUBLIC_PATH')      || define('PUBLIC_PATH', HTTP_PATH.'public/images/front_end/');
        defined('CDN_PATH')         || define('CDN_PATH', 'http://'.$cdnPath);
        defined('UPLOAD_PATH')      || define('UPLOAD_PATH', CDN_PATH.'/images/upload/');
        defined('PUBLIC_PATH_CDN')  || define('PUBLIC_PATH_CDN', LOCALE != '' ? CDN_PATH.'/'.LOCALE.'/' : CDN_PATH.'/');
        defined('PUBLIC_LOCALE_PATH') || define('PUBLIC_LOCALE_PATH', LOCALE != '' ? HTTP_PATH.'public/'.LOCALE.'/images/front_end/' : HTTP_PATH.'public/images/front_end/');
    }

    public function getEmailContents($emailType, $referenceId)
    {
        $localeLanguage = 'nl_NL';
        $localSetting = SystemFactory::getLocaleSettings()->execute(array(), array(), 1);
        if (!empty($localSetting)) {
            $localeLanguage = $localSetting[0]->locale;
        }
        $this->translator = TranslationsFactory::translator(LOCALE, $localeLanguage);

        if (!in_array($emailType, $this->emailTypes)) {
            $this->app->halt(400, json_encode(array('messages' => array('Invalid email type'))));
        }

        if (is_null($referenceId) || !is_numeric($referenceId)) {
            $this->app->halt(400, json_encode(array('messages' => array('Invalid reference Id'))));
        }
        $emailContent = null;
        $this->initData();
        switch ($emailType) {
            case 'newsletter':
                $this->buildNewsletterEmailContent($referenceId);
                break;
            default:
                break;

        }
        $emailContent   = $this->app->view()->fetch('emailContents/_layouts/layout.phtml', array('data' => $this->data));
        $selfLink       = '/emailcontents/'.$emailType.'/'.$referenceId;
        $emailContent   = array('content' => $emailContent);
        $response       = new Hal($selfLink, $emailContent);
        echo $response->asJson();
    }

    private function buildNewsletterEmailContent($referenceId)
    {
        $newsletterCampaign = AdminFactory::getNewsletterCampaign()->execute(array('id' => $referenceId));
        if ($newsletterCampaign instanceof Errors) {
            $this->app->halt(400, json_encode(array('messages' => array('Newsletter campaign record not found'))));
        }
        $this->data['header_text'] = $newsletterCampaign->getHeader();
        $this->data['header'] = $this->loadHeader(
            $newsletterCampaign->getHeaderBannerURL(),
            PUBLIC_PATH_CDN.'images/upload/newslettercampaigns/'.$newsletterCampaign->getHeaderBanner()
        );
        $this->data['footer'] = $this->loadFooter(
            $newsletterCampaign->getFooterBannerURL(),
            PUBLIC_PATH_CDN.'images/upload/newslettercampaigns/'.$newsletterCampaign->getFooterBanner(),
            $newsletterCampaign->getFooter()
        );
        $data = array(
            'newsletterCampaign'    => $newsletterCampaign,
            'top50Link'             => HTTP_PATH_LOCALE.$this->translator->translate('link_top-50'),
            'topOfferText'          => $this->translator->translate('email_Bekijk meer van onze top aanbiedingen'),
            'exclusiveText'         => $this->translator->translate('email_exclusive'),
            'codeText'              => $this->translator->translate('email_CODE'),
            'offerFromText'         => $this->translator->translate('email_valid from'),
            'offerToText'           => $this->translator->translate('email_t/m'),
            'validText'             => $this->translator->translate('email_valid t/m')
        );
        $this->data['content'] = $this->app->view()->fetch('emailContents/newsletter.phtml', $data);
    }

    private function loadHeader($bannerUrl = '', $bannerImage = '')
    {
        if (file_exists(PUBLIC_LOCALE_PATH.'emails/email-header-best.png')) {
            $headerLogo = PUBLIC_LOCALE_PATH.'emails/email-header-best.png';
        } else {
            $headerLogo = LOCALE != '' ? PUBLIC_PATH.'emails/email-header-best-flipit.png' : PUBLIC_PATH.'emails/email-header-best.png';
        }

        $headerData = array(
            'headerLogo'    => $headerLogo,
            'bannerUrl'     => $bannerUrl,
            'bannerImage'   => $bannerImage,
            'websiteName'   => $this->websiteName
        );
        return $this->app->view()->fetch('emailContents/_partials/_header.phtml', $headerData);
    }

    private function loadFooter($bannerUrl = '', $bannerImage = '', $footerText = '')
    {
        $footerLogo = LOCALE == '' ? PUBLIC_PATH.'emails/email-footer-kc.png' : PUBLIC_PATH.'emails/logo-footer.png';

        $footerData = array(
            'footerLogo'        => $footerLogo,
            'bannerUrl'         => $bannerUrl,
            'bannerImage'       => $bannerImage,
            'footerText'        => $footerText,
            'websiteName'       => $this->websiteName,
            'unSubscribeText'   => $this->translator->translate('email_Uitschrijven'),
            'editProfileText'   => $this->translator->translate('email_Wijzigen profiel'),
            'contactText'       => $this->translator->translate('email_Contact'),
            'contactLink'       => $this->translator->translate('link_info') . '/' . $this->translator->translate('link_contact'),
            'directLoginLink'   => $this->translator->translate('link_login') . '/' . $this->translator->translate('link_directlogin'),
            'unSubscribeLink'   => $this->translator->translate('link_login') . '/directloginunsubscribe'
        );
        return $this->app->view()->fetch('emailContents/_partials/_footer.phtml', $footerData);
    }
}
