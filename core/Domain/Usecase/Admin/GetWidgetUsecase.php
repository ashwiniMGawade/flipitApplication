<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\WidgetRepositoryInterface;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Service\Errors;

class GetWidgetUsecase
{
    protected $widgetRepository;

    protected $htmlPurifier;

    public function __construct(WidgetRepositoryInterface $widgetRepository, PurifierInterface $htmlPurifier)
    {
        $this->widgetRepository   = $widgetRepository;
        $this->htmlPurifier     = $htmlPurifier;
    }

    public function execute($conditions)
    {
        $conditions = $this->htmlPurifier->purify($conditions);
        if (!is_array($conditions)) {
            $errors = new Errors();
            $errors->setError('Invalid input, unable to find widget.');
            return $errors;
        }

        $widget = $this->widgetRepository->findOneBy('\Core\Domain\Entity\Widget', $conditions);

        if (false === is_object($widget)) {
            $errors = new Errors();
            $errors->setError('Widget not found');
            return $errors;
        }
        return $widget;
    }
}
