<?php
namespace Api\Controller;

use Core\Domain\Factory\AdminFactory;

class VisitorsController extends ApiBaseController
{
    public function updateVisitor()
    {
        $response = array();
        $params = json_decode($this->app->request->getBody(), true);
        if (!is_array($params) || empty($params)) {
            echo json_encode(array('msg'=>'Invalid Parameters.'));
            exit;
        }

        foreach ($params as $mandrillData) {
            if (!isset($mandrillData['event']) || empty($mandrillData['event'])) {
                echo json_encode(array('msg'=>'Event Required'));
                exit;
            }
            if (!isset($mandrillData['msg']) || empty($mandrillData['msg'])) {
                echo json_encode(array('msg'=>'Message Required'));
                exit;
            }
            if (!$processedEventMessage = $this->processEventMessage($mandrillData['msg'])) {
                echo json_encode(array('msg'=>'Invalid Message or Message Parameters'));
                exit;
            }

            $parameter = array(
                'email' => $processedEventMessage['email'],
                'event' => $mandrillData['event']
            );
            $visitor = AdminFactory::updateVisitors()->execute($parameter);
            $response[$visitor->getEmail()] = array(
                'open' => $visitor->getMailOpenCount(),
                'click' => $visitor->getMailClickCount(),
                'soft_bounce' => $visitor->getMailSoftBounceCount(),
                'hard_bounce' => $visitor->getMailHardBounceCount()
            );
        }
        echo json_encode($response);
        exit;
    }

    private function processEventMessage($eventMessage)
    {
        $validMessageParameters = array(
            'email'
        );
        $eventMessage = $this->filterArrayElements($eventMessage, $validMessageParameters);
        foreach ($eventMessage as $key => $value) {
            if (empty($value)) {
                return false;
            }
            if ($key === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        }
        return $eventMessage;
    }

    private function filterArrayElements($parameters, $filter)
    {
        $filteredArray = array();
        if (is_array($filter) && !empty($filter) && is_array($parameters)) {
            foreach ($filter as $key) {
                $filteredArray[$key] = isset($parameters[$key]) ? $parameters[$key] : null;
            }
        }
        return $filteredArray;
    }
}
