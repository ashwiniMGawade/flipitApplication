<?php
namespace Api\Controller;

use \Core\Domain\Factory\AdminFactory;
use \Core\Domain\Factory\SystemFactory;
use \Core\Domain\Factory\TranslationsFactory;
use \Nocarrier\Hal;
use \Core\Service\Errors;

class EmailContentsController extends ApiBaseController
{
    protected $translator;
    protected $urls;
    protected $websiteName;
    protected $data;
    protected $emailTypes = array(
        'newsletter'
    );

    private function initData()
    {
        $this->urls['httpPath'] = LOCALE != '' ? 'http://www.flipit.com/' : 'http://www.kortingscode.nl/';
        $this->urls['httpPathLocale'] = LOCALE != '' ? 'http://www.flipit.com/'.LOCALE.'/' : 'http://www.kortingscode.nl/';
        $this->urls['publicPathCdn'] = LOCALE != '' ? 'http://img.flipit.com/public/'.LOCALE.'/' : 'http://img.kortingscode.nl/public/';
        $this->urls['publicLocalePath'] = LOCALE != '' ? $this->urls['httpPath'].'public/'.LOCALE.'/images/front_end/' : $this->urls['httpPath'].'public/images/front_end/';
        $this->urls['publicPath'] = $this->urls['httpPath'].'public/images/front_end/';
        $this->websiteName = LOCALE != '' ? 'Flipit' : 'Kortingscode';
        $this->data = array(
            'header_text'   => '',
            'header'        => '',
            'content'       => '',
            'footer'        => ''
        );
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
            $newsletterCampaign->getHeaderBanner()
        );
        $this->data['footer'] = $this->loadFooter(
            $newsletterCampaign->getFooterBannerURL(),
            $newsletterCampaign->getFooterBanner(),
            $newsletterCampaign->getFooter()
        );
        $data = array(
            'newsletterCampaign'    => $newsletterCampaign,
            'urls'                  => $this->urls,
            'top50Link'             => $this->urls['httpPathLocale'].$this->translator->translate('link_top-50'),
            'topOfferText'          => $this->translator->translate('email_Bekijk meer van onze top aanbiedingen'),
            'exclusiveText'         => $this->translator->translate('email_exclusive')
        );
        $this->data['content'] = $this->app->view()->fetch('emailContents/newsletter.phtml', $data);
    }

    private function loadHeader($bannerUrl = '', $bannerImage = '')
    {
        if (fopen($this->urls['publicLocalePath'].'emails/email-header-best.png', 'r')) {
            $headerLogo = $this->urls['publicLocalePath'].'emails/email-header-best.png';
        } else {
            $headerLogo = LOCALE != '' ? $this->urls['publicPath'].'emails/email-header-best-flipit.png' : $this->urls['publicPath'].'emails/email-header-best.png';
        }

        $headerData = array(
            'headerLogo'    => $headerLogo,
            'bannerUrl'     => $bannerUrl,
            'bannerImage'   => $bannerImage,
            'urls'          => $this->urls,
            'websiteName'   => $this->websiteName
        );
        return $this->app->view()->fetch('emailContents/_partials/_header.phtml', $headerData);
    }

    private function loadFooter($bannerUrl = '', $bannerImage = '', $footerText = '')
    {
        $footerLogo = LOCALE == '' ? $this->urls['publicPath'].'emails/email-footer-kc.png' : $this->urls['publicPath'].'emails/logo-footer.png';

        $footerData = array(
            'footerLogo'        => $footerLogo,
            'bannerUrl'         => $bannerUrl,
            'bannerImage'       => $bannerImage,
            'urls'              => $this->urls,
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
