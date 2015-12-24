<?php

namespace Core\Persistence\Database\Repository;

use Core\Domain\Repository\BulkEmailInterface;
use Core\Domain\Entity\BulkEmail;

class BulkEmailRepository implements BulkEmailInterface
{
    private $awsSdk;
    private $dynamoDbClient;

    public function __construct(\AWS\Sdk $awsSdk)
    {
        $this->awsSdk = $awsSdk;
        $this->dynamoDbClient = $awsSdk->createDynamoDb();
    }

    public function save(BulkEmail $bulkEmail)
    {
        $itemArray = array(
            'EmailType' => array(
                'S' => (string) $bulkEmail->getEmailType()
            ),
            'ReferenceId' => array(
                'N' => (string) $bulkEmail->getReferenceId()
            ),
            'Local' => array(
                'S' => (string) $bulkEmail->getLocal()
            ),
        );

        if (!empty($bulkEmail->getUserId())) {
            $itemArray['UserId'] = array('N' => (string) $bulkEmail->getUserId());
        }

        return $this->dynamoDbClient->putItem(array(
            'TableName' => 'BulkEmailTest',
            'Item' => $itemArray
        ));
    }
}
