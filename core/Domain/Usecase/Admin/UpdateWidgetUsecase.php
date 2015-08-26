<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\Widget;
use \Core\Domain\Repository\WidgetRepositoryInterface;
use \Core\Domain\Validator\WidgetValidator;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Service\Errors\ErrorsInterface;

class UpdateWidgetUsecase
{
    private $widgetRepository;

    protected $widgetValidator;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(
        WidgetRepositoryInterface $widgetRepository,
        WidgetValidator $widgetValidator,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->widgetRepository = $widgetRepository;
        $this->widgetValidator  = $widgetValidator;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute(Widget $widget, $params = array())
    {
        $params = $this->htmlPurifier->purify($params);
        if (isset($params['title'])) {
            $widget->setTitle($params['title']);
        }
        if (isset($params['content'])) {
            $widget->setContent($params['content']);
        }
        if (isset($params['startDate'])) {
            $startDate = (!empty($params['startDate'])) ? new \DateTime(date('Y-m-d', strtotime($params['startDate']))) : null;
            $widget->setStartDate($startDate);
        }
        if (isset($params['endDate'])) {
            $endDate = (!empty($params['endDate'])) ? new \DateTime(date('Y-m-d', strtotime($params['endDate']))) : null;
            $widget->setEndDate($endDate);
        }

        $validationResult = $this->widgetValidator->validate($widget);
        if (true !== $validationResult && is_array($validationResult)) {
            $this->errors->setErrors($validationResult);
            return $this->errors;
        }
        return $this->widgetRepository->save($widget);
    }
}
