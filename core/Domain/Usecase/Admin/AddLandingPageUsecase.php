<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Entity\LandingPages;
use \Core\Domain\Entity\Shop;
use \Core\Domain\Repository\LandingPagesRepositoryInterface;
use \Core\Domain\Validator\LandingPageValidator;
use \Core\Service\Errors\ErrorsInterface;

class AddLandingPageUsecase
{
    private $landingPageRepository;

    protected $landingPageValidator;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(
        LandingPagesRepositoryInterface $landingPageRepository,
        LandingPageValidator $landingPageValidator,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->landingPageRepository = $landingPageRepository;
        $this->landingPageValidator  = $landingPageValidator;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute(LandingPages $landingPage, $params = array())
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
            if ($params['status'] == 0) {
                $landingPage->setOfflineSince(new \DateTime('now'));
            } else {
                $landingPage->setOfflineSince(null);
            }
        }
        if (isset($params['content']) && $params['content'] != '') {
            $landingPage->setContent($params['content']);
        }
        $landingPage->setCreatedAt(new \DateTime('now'));
        $landingPage->setUpdatedAt(new \DateTime('now'));

        $validationResult = $this->landingPageValidator->validate($landingPage);

        if (true !== $validationResult && is_array($validationResult)) {
            $this->errors->setErrors($validationResult);
            return $this->errors;
        }
        return $this->landingPageRepository->save($landingPage);
    }
}
