<?php

namespace Core\Persistence\Database\Service;

class DynamoDbManager
{
    private $region;
    private $apiVersion;
    private $dynamoDbRegion;
    private $accessKey;
    private $secretKey;

    public function __construct(AppConfig $appConfig)
    {
        $config = $appConfig->getConfigs();
        $this->setRegion('eu-west-1');
        $this->setApiVersion('latest');
        $this->setDynamoDbRegion($config['connections']['dynamoDb']['dynamoDbRegion']);
        $this->setAccessKey($config['connections']['dynamoDb']['accessKey']);
        $this->setSecretKey($config['connections']['dynamoDb']['securityKey']);
    }

    /**
     * @return mixed
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param mixed $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return mixed
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * @param mixed $apiVersion
     */
    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;
    }

    /**
     * @return mixed
     */
    public function getDynamoDbRegion()
    {
        return $this->dynamoDbRegion;
    }

    /**
     * @param mixed $dynamoDbRegion
     */
    public function setDynamoDbRegion($dynamoDbRegion)
    {
        $this->dynamoDbRegion = $dynamoDbRegion;
    }

    /**
     * @return mixed
     */
    public function getAccessKey()
    {
        return $this->accessKey;
    }

    /**
     * @param mixed $accessKey
     */
    public function setAccessKey($accessKey)
    {
        $this->accessKey = $accessKey;
    }

    /**
     * @return mixed
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * @param mixed $secretKey
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function getAwsSdk()
    {
        $credentials = new \Aws\Credentials\Credentials($this->getAccessKey(), $this->getSecretKey());

        return new \Aws\Sdk([
            'region' => $this->getRegion(),
            'version' => $this->getApiVersion(),
            'credentials' => $credentials,
            'DynamoDb' => [
                'region' => $this->getDynamoDbRegion()
            ]
        ]);
    }
}
