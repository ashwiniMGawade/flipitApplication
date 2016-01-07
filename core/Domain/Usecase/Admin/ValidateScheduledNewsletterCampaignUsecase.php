<?php
namespace Core\Domain\Usecase\Admin;

/**
 * Class ValidateScheduledNewsletterCampaignUsecase
 *
 * @package Core\Domain\Usecase\Admin
 */
class ValidateScheduledNewsletterCampaignUsecase
{
    public function __construct() {
    }

    /**
     * @param $properties
     *
     * @return mixed
     * @throws \Exception
     */
    public function execute($params)
    {
        $response = [];
        if (isset($params['scheduleDate']) && empty($params['scheduleDate'])) {
            $response['error'][] = "Please enter campaign scheduled Date";
        }
        if (isset($params['campaignSubject']) && empty($params['campaignSubject'])) {
            $response['error'][] = "Please enter campaign subject";
        }
        if (isset($params['campaignHeader']) && empty($params['campaignHeader'])) {
            $response['error'][] = "Please enter campaign Header";
        }
        if (isset($params['campaignFooter']) && empty($params['campaignFooter'])) {
            $response['error'][] = "Please enter campaign Footer";
        }
        if (isset($params['senderName']) && empty($params['senderName'])) {
            $response['error'][] = "Please enter sender Name";
        }
        return $response;
    }
}
