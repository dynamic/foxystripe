<?php

namespace Dynamic\FoxyStripe\Model;

use Foxy\FoxyClient\FoxyClient;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use SilverStripe\Core\Injector\Injector;

class FoxyStripeClient
{
    /**
     * @var string
     */
    private static $table_name = 'FS_FoxyStripeClient';

    /**
     * @var
     */
    private $client;

    /**
     * @var
     */
    private $current_store;

    /**
     * @var
     */
    private $item_categories_url;

    /**
     * @var
     */
    private $item_categories;

    /**
     * FoxyStripeClient constructor.
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct()
    {
        $config = [
            'use_sandbox' => false,
        ];

        if ($setting = FoxyStripeSetting::current_foxystripe_setting()) {
            $config['client_id'] = $setting->client_id;
            $config['client_secret'] = $setting->client_secret;
            $config['refresh_token'] = $setting->refresh_token;
            $config['access_token'] = $setting->access_token;
            $config['access_token_expires'] = 7200;
        }

        $guzzle_config = [
            'defaults' => [
                'debug' => false,
                'exceptions' => false,
            ],
        ];

        /*
         * Set up our Guzzle Client
         */
        $guzzle = new Client($guzzle_config);
        //CacheSubscriber::attach($guzzle); // todo add caching middleware guzzle-cache-middleware

        /*
         * Get our FoxyClient
         */
        $fc = new FoxyClient($guzzle, $config);

        $this->setClient($fc);
        $this->setCurrentStore();
        $this->setItemCategoriesURL();
        $this->setItemCategories();
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param $client
     *
     * @return $this
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return bool
     * @throws \SilverStripe\ORM\ValidationException
     */
    public static function is_valid()
    {
        $config = FoxyStripeSetting::current_foxystripe_setting();

        return $config->EnableAPI &&
            $config->client_id &&
            $config->client_secret &&
            $config->refresh_token &&
            $config->access_token;
    }

    /**
     * @return mixed
     */
    public function getCurrentStore()
    {
        return $this->current_store;
    }

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function setCurrentStore()
    {
        $client = $this->getClient();
        $config = FoxyStripeSetting::current_foxystripe_setting();

        $errors = [];
        $data = [
            'store_domain' => $config->StoreName,
        ];

        if ($client && $result = $client->get()) {
            $errors = array_merge($errors, $client->getErrors($result));
            if ($reporting_uri = $client->getLink('fx:reporting')) {
                $errors = array_merge($errors, $client->getErrors($reporting_uri));
                if ($result = $client->get($reporting_uri)) {
                    $errors = array_merge($errors, $client->getErrors($result));
                    if ($store_exists_uri = $client->getLink('fx:reporting_store_domain_exists')) {
                        $errors = array_merge($errors, $client->getErrors($store_exists_uri));
                        if ($result = $client->get($store_exists_uri, $data)) {
                            $errors = array_merge($errors, $client->getErrors($result));
                            if ($store = $client->getLink('fx:store')) {
                                $errors = array_merge($errors, $client->getErrors($store));
                                $this->current_store = $store;
                            }
                        }
                    }
                }
            }
            if (count($errors)) {
                Injector::inst()
                    ->get(LoggerInterface::class)->error('setCurrentStore errors - ' . json_encode($errors));
            }
        }
    }

    /**
     * @param $uri
     * @param null $data
     */
    public function post($uri, $data = null)
    {
        $this->getClient()->post($uri, $data);
    }

    /**
     * @param $uri
     * @param null $data
     */
    public function patch($uri, $data = null)
    {
        $this->getClient()->patch($uri, $data);
    }

    /**
     * @param array $data
     *
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function updateStore($data = [])
    {
        $client = $this->getClient();
        $errors = [];

        $result = $client->patch($this->getCurrentStore(), $data);

        $errors = array_merge($errors, $client->getErrors($result));
        if (count($errors)) {
            Injector::inst()->get(LoggerInterface::class)->error('updateStore errors - ' . json_encode($errors));
        }
    }

    /**
     * @return mixed
     */
    public function getItemCategoriesURL()
    {
        return $this->item_categories_url;
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setItemCategoriesURL()
    {
        $client = $this->getClient();
        $errors = [];

        if ($client) {
            $result = $client->get($this->getCurrentStore());

            if (isset($result['_links']['fx:item_categories']['href'])) {
                $this->item_categories_url = $result['_links']['fx:item_categories']['href'];
            }

            $errors = array_merge($errors, $client->getErrors($result));
            if (count($errors)) {
                Injector::inst()
                    ->get(LoggerInterface::class)->error('setItemCategoriesURL errors - ' . json_encode($errors));
            }
        }
    }

    /**
     * @return mixed
     */
    public function getItemCategories()
    {
        return $this->item_categories;
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setItemCategories()
    {
        $client = $this->getClient();
        $errors = [];

        if ($client) {
            $result = $client->get($this->getItemCategoriesURL());

            $this->item_categories = $result;

            $errors = array_merge($errors, $client->getErrors($result));
            if (count($errors)) {
                Injector::inst()
                    ->get(LoggerInterface::class)->error('setItemCategories errors - ' . json_encode($errors));
            }
        }
    }

    /**
     * @param $code
     *
     * @return bool
     *
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getCategory($code)
    {
        if ($categoriesURL = $this->getItemCategoriesURL()) {
            $client = $this->getClient();
            $errors = [];
            $data = [
                'code' => $code,
            ];
            if ($result = $client->get($categoriesURL, $data)) {
                if (count($result['_embedded']['fx:item_categories']) > 0) {
                    $category = $result['_embedded']['fx:item_categories'][0]['_links']['self']['href'];

                    return $category;
                }
                $errors = array_merge($errors, $client->getErrors($result));
                if (count($errors)) {
                    Injector::inst()
                        ->get(LoggerInterface::class)->error('getCategory errors - ' . json_encode($errors));
                }
            }
        }

        return false;
    }

    /**
     * @param array $data
     *
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function putCategory($data = [])
    {
        $client = $this->getClient();
        $errors = [];

        if ($client) {
            if ($category = $this->getCategory($data['code'])) {
                $result = $client->patch($category, $data);
            } else {
                $result = $client->post($this->getItemCategoriesURL(), $data);
            }
            $errors = array_merge($errors, $client->getErrors($result));
            if (count($errors)) {
                Injector::inst()->get(LoggerInterface::class)->error('putCategory errors - ' . json_encode($errors));
            }
        }
    }

    /**
     * @param array $data
     *
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function deleteCategory($data = [])
    {
        $client = $this->getClient();
        $errors = [];

        if ($category = $this->getCategory($data['code'])) {
            $result = $client->delete($category);

            $errors = array_merge($errors, $client->getErrors($result));
            if (count($errors)) {
                Injector::inst()->get(LoggerInterface::class)->error('deleteCategory errors - ' . json_encode($errors));
            }
        }
    }
}
