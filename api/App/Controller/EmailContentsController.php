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
    protected $emailTypes = array(
        'newsletter'
    );

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
            $this->app->halt(400, json_encode(array('messages' => array('Invalid newsletter campaign Id'))));
        }
        $emailContent = null;
        switch($emailType) {
            case 'newsletter':
                $emailContent = $this->buildNewsletterEmailContent($referenceId);
                break;
            default:
                break;

        }
        $selfLink = '/emailcontents/'.$emailType.'/'.$referenceId;
        $emailContent = array('content' => $emailContent);
        $response = new Hal($selfLink, $emailContent);
        echo $response->asJson();
    }

    private function buildNewsletterEmailContent ($referenceId)
    {
        $newsletterCampaign = AdminFactory::getNewsletterCampaign()->execute(array('id' => $referenceId));
        if ($newsletterCampaign instanceof Errors) {
            $this->app->halt(400, json_encode(array('messages' => array('Newsletter campaign record not found'))));
        }
        $html = $this->app->view()->fetch('newsletter.php', array('newsletterCampaign' => $newsletterCampaign));
        return $html;
    }
}
