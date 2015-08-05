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
            $this->app->response->setStatus(405);
            echo json_encode(array('msg'=>'Invalid Parameters.'));
            return;
        }

        foreach ($params as $mandrillData) {
            if (!isset($mandrillData['event']) || empty($mandrillData['event'])) {
                $this->app->response->setStatus(405);
                echo json_encode(array('msg'=>'Event Required'));
                return;
            }
            if (!isset($mandrillData['msg']) || empty($mandrillData['msg'])) {
                $this->app->response->setStatus(405);
                echo json_encode(array('msg'=>'Message Required'));
                return;
            }
            if (!$processedEventMessage = $this->processEventMessage($mandrillData['msg'], $mandrillData['event'])) {
                $this->app->response->setStatus(405);
                echo json_encode(array('msg'=>'Invalid Message or Message Parameters'));
                return;
            }

            $parameter = array(
                'email' => $processedEventMessage['email'],
                'event' => $mandrillData['event']
            );

            if ($mandrillData['event'] === 'open' && isset($processedEventMessage['opens'])) {
                foreach ($processedEventMessage['opens'] as $opens) {
                    if (!isset($opens['ts']) || !is_int($opens['ts'])) {
                        $this->app->response->setStatus(405);
                        echo json_encode(array('msg'=>'Invalid Opens Timestamp'));
                        return;
                    }
                    $parameter['opensTimestamp'] = $opens['ts'];
                }
            }

            try {
                $visitor = AdminFactory::updateVisitors()->execute($parameter);
                $response[$visitor->getEmail()] = array(
                    'open' => $visitor->getMailOpenCount(),
                    'click' => $visitor->getMailClickCount(),
                    'soft_bounce' => $visitor->getMailSoftBounceCount(),
                    'hard_bounce' => $visitor->getMailHardBounceCount()
                );
            } catch (\Exception $e) {
                $this->app->response->setStatus(405);
                echo json_encode(array('msg'=>$e->getMessage()));
                return;
            }
        }
        echo json_encode($response);
        return;
    }

    private function processEventMessage($eventMessage, $eventName = null)
    {
        $validMessageParameters = array(
            'email'
        );
        if ($eventName === 'open') {
            $validMessageParameters[] = 'opens';
        }
        $eventMessage = $this->filterArrayElements($eventMessage, $validMessageParameters);
        foreach ($eventMessage as $key => $value) {
            if (empty($value)) {
                return false;
            }
            if ($key === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
            if ($key === 'opens' && !is_array($value)) {
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
