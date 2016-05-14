<?php

/**
 * based on SiteTreeURLSegmentField
 */
class FoxyStripeStoreDomainField extends TextField
{
    /**
     * @var string
     */
    protected $helpText;
    /**
     * @var string
     */
    protected $urlSuffix = '.foxycart.com';

    /**
     * @var array
     */
    private static $allowed_actions = array(
        'suggest'
    );

    /**
     * @return string
     */
    public function Value()
    {
        return rawurldecode($this->value);
    }

    /**
     * @param array $properties
     * @return string
     */
    public function Field($properties = array())
    {
        Requirements::javascript(MODULE_FOXYSTRIPE_DIR . '/javascript/FoxyStripeStoreDomainField.js');
        Requirements::css(MODULE_FOXYSTRIPE_DIR . "/css/foxystripescreen.css");
        return parent::Field($properties);
    }

    /**
     * @param $request
     * @return string|void
     * @throws SS_HTTPResponse_Exception
     */
    public function suggest($request)
    {
        if (!$request->getVar('value')) {
            return $this->httpError(405,
                _t('SiteTreeURLSegmentField.EMPTY', 'Please enter a URL Segment or click cancel')
            );
        }

        $value = $request->getVar('value');

        $checkName = function () use (&$value, &$checkName) {
            $count = 0;
            $errors = array();
            $guzzleConfig = array(
                'defaults' => array(
                    'debug' => false,
                    'exceptions' => false
                )
            );

            $siteConfig = SiteConfig::current_site_config();
            $guzzle = new \GuzzleHttp\Client($guzzleConfig);
            $fc = new \Foxy\FoxyClient\FoxyClient($guzzle, array(
                'use_sandbox' => true,
                'access_token' => $siteConfig->AccessToken,
                'access_token_expires' => $siteConfig->AccessTokenExpires,
                'refresh_token' => $siteConfig->RefreshToken,
                'client_id' => $siteConfig->ClientID,
                'client_secret' => $siteConfig->ClientSecret
            ));

            $data = array(
                'store_domain' => $value
            );

            $result = $fc->get();
            $errors = array_merge($errors, $fc->getErrors($result));
            $reporting_uri = $fc->getLink('fx:reporting');

            if ($reporting_uri == '') {
                $errors[] = 'Unable to obtain fx:reporting href';
            }

            if (!count($errors)) {
                $result = $fc->get($reporting_uri);
                $errors = array_merge($errors, $fc->getErrors($result));
                $store_exists_uri = $fc->getLink('fx:reporting_store_domain_exists');
                if ($store_exists_uri == '') {
                    $errors[] = 'Unable to obtain fx:reporting_store_domain_exists href';
                }
                if (!count($errors)) {
                    $result = $fc->get($store_exists_uri, $data);
                    $errors = array_merge($errors, $fc->getErrors($result));
                    if (!count($errors)) {
                        if ($result['message'] == 'This store exists.') {
                            $count++;
                            $value = $value . "-" . $count;
                            $checkName();
                        }
                    }
                }
            }
        };

        $checkName();

        Controller::curr()->getResponse()->addHeader('Content-Type', 'application/json');
        return Convert::raw2json(array('value' => $value));
    }

    /**
     * @param string $string The secondary text to show
     */
    public function setHelpText($string)
    {
        $this->helpText = $string;
    }

    /**
     * @return string the secondary text to show in the template
     */
    public function getHelpText()
    {
        return $this->helpText;

    }

    /**
     * @return string
     */
    public function getURLSuffix()
    {
        return $this->urlSuffix;
    }

    /**
     * @param $suffix
     */
    public function setURLSuffix($suffix)
    {
        $this->urlSuffix = $suffix;
    }

    /**
     * @return string
     */
    public function Type()
    {
        return 'text foxystripestoredomain';
    }

    /**
     * @return string
     */
    public function getURL()
    {
        return $this->Value() . "{$this->getURLSuffix()}";
    }


}