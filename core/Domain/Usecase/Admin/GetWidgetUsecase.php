<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\WidgetRepositoryInterface;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Service\Interfaces\ErrorsInterface;

class GetWidgetUsecase
{
    protected $widgetRepository;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(WidgetRepositoryInterface $widgetRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->widgetRepository = $widgetRepository;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute($conditions)
    {
        $conditions = $this->htmlPurifier->purify($conditions);
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find widget.');
            return $this->errors;
        }

        $widget = $this->widgetRepository->findOneBy('\Core\Domain\Entity\Widget', $conditions);

        if (false === is_object($widget)) {
            $this->errors->setError('Widget not found');
            return $this->errors;
        }
        return $widget;
    }
}
