<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\WidgetRepositoryInterface;
use \Core\Domain\Entity\Widget;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Validator\WidgetValidator;

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
        $params = $this->htmlPurifier->purify($params);
        return true;
    }
}
