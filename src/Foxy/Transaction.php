<?php

namespace Dynamic\FoxyStripe\Foxy;

use Dynamic\FoxyStripe\Model\FoxyCart;
use SilverStripe\Core\Injector\Injectable;

/**
 * Class Transaction
 * @package Dynamic\FoxyStripe\Foxy
 */
class Transaction
{
    use Injectable;

    /**
     * @var
     */
    private $transaction;

    /**
     * Transaction constructor.
     * @param $data
     */
    public function __construct($transactionID, $data)
    {
        $this->setTransaction($transactionID, $data);
    }

    /**
     * @param $data
     * @return $this
     */
    public function setTransaction($transactionID, $data)
    {
        $decryptedData = $this->getDecryptedData($data);

        foreach ($decryptedData->transactions->transaction as $transaction) {
            if ($transactionID == (int)$transaction->id) {
                $this->transaction = $transaction;
                break;
            }
        }

        if (!$this->transaction) {
            $this->transaction = false;
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return $this->getTransaction() != false && !empty($this->getTransaction());
    }

    /**
     * @param $data
     * @return \SimpleXMLElement
     * @throws \SilverStripe\ORM\ValidationException
     */
    private function getDecryptedData($data)
    {
        return new \SimpleXMLElement(\rc4crypt::decrypt(FoxyCart::getStoreKey(), urldecode($data)));
    }
}
