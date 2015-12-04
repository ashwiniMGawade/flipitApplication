<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\User\SplashPage;
use \Core\Domain\Repository\SplashPageRepositoryInterface;
use \Core\Domain\Validator\SplashPageValidator;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Service\Errors\ErrorsInterface;

class UpdateSplashPageUsecase
{
    protected $splashPageRepository;

    protected $splashPageValidator;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(
        SplashPageRepositoryInterface $splashPageRepository,
        SplashPageValidator $splashPageValidator,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->splashPageRepository = $splashPageRepository;
        $this->splashPageValidator  = $splashPageValidator;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute(SplashPage $splashPage, $params = array())
    {
        $params = $this->htmlPurifier->purify($params);
        if (isset($params['content'])) {
            $splashPage->setContent($params['content']);
        }
        if (isset($params['image'])) {
            $splashPage->setImage($params['image']);
        }
        if (isset($params['popularShops'])) {
            $splashPage->setPopularShops($params['popularShops']);
        }
        if (isset($params['updatedBy'])) {
            $splashPage->setUpdatedBy($params['updatedBy']);
        }
        if (isset($params['infoImage'])) {
            $splashPage->setInfoImage($params['infoImage']);
        }
        if (isset($params['footer'])) {
            $splashPage->setFooter($params['footer']);
        }
        if (isset($params['visitorsPerMonthCount'])) {
            $splashPage->setVisitorsPerMonthCount((int)$params['visitorsPerMonthCount']);
        }
        if (isset($params['verifiedActionCount'])) {
            $splashPage->setVerifiedActionCount((int)$params['verifiedActionCount']);
        }
        if (isset($params['newsletterSignupCount'])) {
            $splashPage->setNewsletterSignupCount((int)$params['newsletterSignupCount']);
        }
        if (isset($params['retailerOnlineCount'])) {
            $splashPage->setRetailerOnlineCount((int)$params['retailerOnlineCount']);
        }
        $splashPage->setUpdatedAt(new \DateTime('now'));

        $validationResult = $this->splashPageValidator->validate($splashPage);
        if (true !== $validationResult && is_array($validationResult)) {
            $this->errors->setErrors($validationResult);
            return $this->errors;
        }
        return $this->splashPageRepository->save($splashPage);
    }
}
