<?php

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Cache;


/**
 * Class FoxyStripeClient
 */
class FoxyStripeClient
{

    /**
     * @var
     */
    private $client;

    /**
     * @var array
     */
    private $guzzle_config = array(
        'defaults' => array(
            'debug' => false,
            'exceptions' => false
        )
    );

    /**
     * FoxyStripeClient constructor.
     * @param $config
     */
    public function __construct($config)
    {

        $this->setClient($config);

    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param $config
     * @return $this
     */
    public function setClient($config)
    {
        $this->client = new FoxyClient(new Client($this->getGuzzleConfig()), $config);
        return $this;
    }

    /**
     * @param array $guzzleConfig
     * @return $this
     */
    public function setGuzzleConfig($guzzleConfig = array())
    {
        $this->guzzle_config = $guzzleConfig;
        return $this;
    }

    /**
     * @return array
     */
    public function getGuzzleConfig()
    {
        return $this->guzzle_config;
    }

}