<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Entity\LandingPage;
use \Core\Domain\Entity\Shop;
use \Core\Domain\Repository\LandingPageRepositoryInterface;
use \Core\Domain\Validator\LandingPageValidator;
use \Core\Service\Errors\ErrorsInterface;

class UpdateLandingPageUsecase
{
    private $landingPageRepository;

    protected $landingPageValidator;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(
        LandingPageRepositoryInterface $landingPageRepository,
        LandingPageValidator $landingPageValidator,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->landingPageRepository = $landingPageRepository;
        $this->landingPageValidator  = $landingPageValidator;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute(LandingPage $landingPage, $params = array())
    {
        if (empty($params)) {
            $this->errors->setError('Invalid Parameters');
            return $this->errors;
        }
        $params = $this->htmlPurifier->purify($params);

        if (isset($params['title'])) {
            $landingPage->setTitle($params['title']);
        }
        if (isset($params['shop']) && $params['shop'] instanceof Shop) {
            $landingPage->setShop($params['shop']);
        }
        if (isset($params['permalink'])) {
            $pattern = array("/&amp;/", "/[\,+@#$%'&*!;&\"<>\^()]+/", '/\s/', "/-{2,}/");
            $replaceWith = array("", "", "-", "-");
            $urlString = preg_replace($pattern, $replaceWith, trim($params['permalink']));
            $params['permalink'] = strtolower($urlString);
            $landingPage->setPermalink($params['permalink']);
        }
        if (isset($params['refUrl'])) {
            $landingPage->setRefUrl($params['refUrl']);
        }
        if (isset($params['subTitle'])) {
            $landingPage->setSubTitle($params['subTitle']);
        }
        if (isset($params['metaTitle'])) {
            $landingPage->setMetaTitle($params['metaTitle']);
        }
        if (isset($params['metaDescription'])) {
            $landingPage->setMetaDescription($params['metaDescription']);
        }
        if (isset($params['status'])) {
            $landingPage->setStatus((int) $params['status']);
            $offlineSince = $landingPage->getOfflineSince();
            if ($params['status'] == 0) {
                if (empty($offlineSince)) {
                    $landingPage->setOfflineSince(new \DateTime('now'));
                }
            } else {
                $landingPage->setOfflineSince(null);
            }
        }
        if (isset($params['content'])) {
            $landingPage->setContent($params['content']);
        }
        if (isset($params['brandingCss'])) {
            $landingPage->setBrandingCss($params['brandingCss']);
        }
        $landingPage->setUpdatedAt(new \DateTime('now'));

        $validationResult = $this->landingPageValidator->validate($landingPage);

        if (true !== $validationResult && is_array($validationResult)) {
            $this->errors->setErrors($validationResult);
            return $this->errors;
        }
        return $this->landingPageRepository->save($landingPage);
    }
}
