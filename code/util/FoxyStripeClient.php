<?php

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Cache\CacheSubscriber;
use Foxy\FoxyClient\FoxyClient;


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
     * @param array $config
     */
    public function __construct($config = array())
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
    public function setClient($config = array())
    {
        $config = empty($config) ? $this->getGuzzleConfig() : $config;
        $guzzle = new GuzzleHttp\Client($this->getGuzzleConfig());
        CacheSubscriber::attach($guzzle);
        $this->client = new FoxyClient($guzzle, $config);
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