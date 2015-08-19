<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\WidgetRepositoryInterface;
use \Core\Domain\Entity\Widget;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Validator\WidgetValidator;
use \Core\Service\Errors;

class AddWidgetUsecase
{
    private $widgetRepository;

    protected $widgetValidator;

    protected $htmlPurifier;

    public function __construct(
        WidgetRepositoryInterface $widgetRepository,
        WidgetValidator $widgetValidator,
        PurifierInterface $htmlPurifier
    ) {
        $this->widgetRepository = $widgetRepository;
        $this->widgetValidator  = $widgetValidator;
        $this->htmlPurifier     = $htmlPurifier;
    }

    public function execute(Widget $widget, $params = array())
    {
        $result = true;
        $params = $this->htmlPurifier->purify($params);
        if (isset($params['title'])) {
            $widget->setTitle($params['title']);
        }
        if (isset($params['content'])) {
            $widget->setContent($params['content']);
        }
        if (isset($params['startDate']) && strlen($params['startDate']) > 0) {
            $startDate = new \DateTime(date('Y-m-d', strtotime($params['startDate'])));
            $widget->setStartDate($startDate);
        }
        if (isset($params['endDate']) && strlen($params['endDate']) > 0) {
            $endDate = new \DateTime(date('Y-m-d', strtotime($params['endDate'])));
            $widget->setEndDate($endDate);
        }

        $widget->setShowWithDefault(1);
        $widget->setCreatedAt(new \DateTime('now'));
        $widget->setUpdatedAt(new \DateTime('now'));

        $validationResult = $this->widgetValidator->validate($widget);

        if (true !== $validationResult && is_array($validationResult)) {
            $result = new Errors();
            $result->setErrors($validationResult);
            return $result;
        }
        return $this->widgetRepository->save($widget);
    }
}
