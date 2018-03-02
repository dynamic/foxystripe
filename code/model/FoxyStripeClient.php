<?php

namespace Dynamic\FoxyStripe\Model;

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Cache\CacheSubscriber;
use Foxy\FoxyClient\FoxyClient;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\Tests\MySQLDatabaseTest\Data;
use SilverStripe\SiteConfig\SiteConfig;

class FoxyStripeClient extends DataObject
{
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
     */
    public function __construct()
    {
        $config = array(
            'use_sandbox' => false
        );

        $site_config = SiteConfig::current_site_config();
        if ($site_config) {
            $config['client_id'] = $site_config->client_id;
            $config['client_secret'] = $site_config->client_secret;
            $config['refresh_token'] = $site_config->refresh_token;
            $config['access_token'] = $site_config->access_token;
        }

        $guzzle_config = array(
            'defaults' => array(
                'debug' => false,
                'exceptions' => false
            )
        );

        // todo - fix Guzzle client integration

        /**
         * Set up our Guzzle Client
         */
        //$guzzle = new Client($guzzle_config);
        //CacheSubscriber::attach($guzzle);

        /**
         * Get our FoxyClient
         */
        //$fc = new FoxyClient($guzzle, $config);

        //$this->setClient($fc);
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
     * @return $this
     */
    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrentStore()
    {
        return $this->current_store;
    }

    /**
     *
     */
    public function setCurrentStore()
    {
        $client = $this->getClient();
        $config = SiteConfig::current_site_config();

        $errors = array();
        $data = array(
            'store_domain' => $config->StoreName,
        );

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
                \SS_Log::log('setCurrentStore errors - ' . json_encode($errors), \SS_Log::WARN);
            }
        }
    }

    /**
     * @param array $data
     */
    public function updateStore($data = []) {
        $client = $this->getClient();
        $errors = [];
        $result = $client->patch($this->getCurrentStore(), $data);

        $errors = array_merge($errors, $client->getErrors($result));
        if (count($errors)) {
            \SS_Log::log('updateStore errors - ' . json_encode($errors), \SS_Log::WARN);
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
     *
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
                \SS_Log::log('setItemCategoriesURL errors - ' . json_encode($errors), \SS_Log::WARN);
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
     *
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
                \SS_Log::log('setItemCategories errors - ' . json_encode($errors), \SS_Log::WARN);
            }
        }
    }

    /**
     * @param $code
     * @return mixed
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
                    \SS_Log::log('getCategory errors - ' . json_encode($errors), \SS_Log::WARN);
                }
            }
        }
        return false;
    }

    /**
     * @param array $data
     */
    public function putCategory($data = [])
    {
        $client = $this->getClient();
        $errors = [];

        if ($category = $this->getCategory($data['code'])) {
            $result = $client->patch($category, $data);
        } else {
            $result = $client->post($this->getItemCategoriesURL(), $data);
        }
        $errors = array_merge($errors, $client->getErrors($result));
        if (count($errors)) {
            \SS_Log::log('putCategory errors - ' . json_encode($errors), \SS_Log::WARN);
        }
    }

    /**
     * @param array $data
     */
    public function deleteCategory($data = [])
    {
        $client = $this->getClient();
        $errors = [];

        if ($category = $this->getCategory($data['code'])) {
            $result = $client->delete($category);

            $errors = array_merge($errors, $client->getErrors($result));
            if (count($errors)) {
                \SS_Log::log('deleteCategory errors - ' . json_encode($errors), \SS_Log::WARN);
            }
        }
    }
}
