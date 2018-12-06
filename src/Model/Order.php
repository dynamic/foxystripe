<?php

namespace Dynamic\FoxyStripe\Model;

use Dynamic\FoxyStripe\Foxy\Transaction;
use Dynamic\FoxyStripe\Page\ProductPage;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\DateField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLVarchar;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Security\Security;

/**
 * Class Order
 * @package Dynamic\FoxyStripe\Model
 *
 * @property \SilverStripe\ORM\FieldType\DBInt Order_ID
 * @property \SilverStripe\ORM\FieldType\DBDatetime TransactionDate
 * @property \SilverStripe\ORM\FieldType\DBCurrency ProductTotal
 * @property \SilverStripe\ORM\FieldType\DBCurrency TaxTotal
 * @property \SilverStripe\ORM\FieldType\DBCurrency ShippingTotal
 * @property \SilverStripe\ORM\FieldType\DBCurrency OrderTotal
 * @property \SilverStripe\ORM\FieldType\DBVarchar ReceiptURL
 * @property \SilverStripe\ORM\FieldType\DBVarchar OrderStatus
 * @property \SilverStripe\ORM\FieldType\DBVarchar Response
 *
 * @property int MemberID
 * @method Member Member
 *
 * @method \SilverStripe\ORM\HasManyList Details
 */
class Order extends DataObject implements PermissionProvider
{
    /**
     * @var array
     */
    private static $db = [
        'Order_ID' => 'Int',
        'TransactionDate' => 'DBDatetime',
        'ProductTotal' => 'Currency',
        'TaxTotal' => 'Currency',
        'ShippingTotal' => 'Currency',
        'OrderTotal' => 'Currency',
        'ReceiptURL' => 'Varchar(255)',
        'OrderStatus' => 'Varchar(255)',
        'Response' => 'Text',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'Member' => Member::class,
    ];

    /**
     * @var array
     */
    private static $has_many = [
        'Details' => OrderDetail::class,
    ];

    /**
     * @var string
     */
    private static $singular_name = 'Order';

    /**
     * @var string
     */
    private static $plural_name = 'Orders';

    /**
     * @var string
     */
    private static $description = 'Orders from FoxyCart Datafeed';

    /**
     * @var string
     */
    private static $default_sort = 'TransactionDate DESC, ID DESC';

    /**
     * @var array
     */
    private static $summary_fields = [
        'Order_ID',
        'TransactionDate.Nice',
        'Member.Name',
        'ProductTotal.Nice',
        'ShippingTotal.Nice',
        'TaxTotal.Nice',
        'OrderTotal.Nice',
        'ReceiptLink',
    ];

    /**
     * @var array
     */
    private static $searchable_fields = [
        'Order_ID',
        'TransactionDate' => [
            'field' => DateField::class,
            'filter' => 'PartialMatchFilter',
        ],
        'Member.ID',
        'OrderTotal',
        'Details.ProductID',
    ];

    /**
     * @var array
     */
    private static $casting = [
        'ReceiptLink' => 'HTMLVarchar',
    ];

    /**
     * @var array
     */
    private static $indexes = [
        'Order_ID' => true, // make unique
    ];

    /**
     * @var string
     */
    private static $table_name = 'Order';

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @param bool $includerelations
     *
     * @return array|string
     */
    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels();

        $labels['Order_ID'] = _t('Order.Order_ID', 'Order ID#');
        $labels['TransactionDate'] = _t('Order.TransactionDate', 'Date');
        $labels['TransactionDate.NiceUS'] = _t('Order.TransactionDate', 'Date');
        $labels['Member.Name'] = _t('Order.MemberName', 'Customer');
        $labels['Member.ID'] = _t('Order.MemberName', 'Customer');
        $labels['ProductTotal.Nice'] = _t('Order.ProductTotal', 'Sub Total');
        $labels['TaxTotal.Nice'] = _t('Order.TaxTotal', 'Tax');
        $labels['ShippingTotal.Nice'] = _t('Order.ShippingTotal', 'Shipping');
        $labels['OrderTotal'] = _t('Order.OrderTotal', 'Total');
        $labels['OrderTotal.Nice'] = _t('Order.OrderTotal', 'Total');
        $labels['ReceiptLink'] = _t('Order.ReceiptLink', 'Invoice');
        $labels['Details.ProductID'] = _t('Order.Details.ProductID', 'Product');

        return $labels;
    }

    /**
     * @return mixed
     */
    public function ReceiptLink()
    {
        return $this->getReceiptLink();
    }

    /**
     * @return mixed
     */
    public function getReceiptLink()
    {
        $obj = DBHTMLVarchar::create();
        $obj->setValue(
            '<a href="' . $this->ReceiptURL . '" target="_blank" class="cms-panel-link action external-link">view</a>'
        );

        return $obj;
    }

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();

        $this->parseOrder();
    }

    /**
     * @return mixed
     */
    protected function getTransaction()
    {
        if (!$this->transaction) {
            $this->setTransaction();
        }

        return $this->transaction;
    }

    /**
     * @return $this
     */
    protected function setTransaction()
    {
        if ($this->Response) {
            $this->transaction = Transaction::create($this->Order_ID, $this->Response);
        } else {
            $this->transaction = false;
        }

        return $this;
    }

    /**
     * @return bool
     *
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function parseOrder()
    {
        if ($this->getTransaction() && $this->getTransaction()->exists()) {
            $this->parseOrderInfo();
            $this->parseOrderCustomer();
            $this->parseOrderDetails();
        }
    }

    /**
     * @return bool|string
     */
    private function getDecryptedResponse()
    {
        if (FoxyCart::getStoreKey() && $this->Response) {
            return \rc4crypt::decrypt(FoxyCart::getStoreKey(), urldecode($this->Response));
        }

        return false;
    }

    /**
     * @param $response
     */
    public function parseOrderInfo()
    {
        $transaction = $this->getTransaction()->getTransaction();

        // Record transaction data from FoxyCart Datafeed:
        $this->Store_ID = (int)$transaction->store_id;
        $this->TransactionDate = (string)$transaction->transaction_date;
        $this->ProductTotal = (float)$transaction->product_total;
        $this->TaxTotal = (float)$transaction->tax_total;
        $this->ShippingTotal = (float)$transaction->shipping_total;
        $this->OrderTotal = (float)$transaction->order_total;
        $this->ReceiptURL = (string)$transaction->receipt_url;
        $this->OrderStatus = (string)$transaction->status;

        $this->extend('handleOrderInfo', $order, $response);
    }

    /**
     * @param $response
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function parseOrderCustomer()
    {
        $transaction = $this->getTransaction()->getTransaction();

        // if not a guest transaction in FoxyCart
        if (isset($transaction->customer_email) && $transaction->is_anonymous == 0) {
            if (!$customer = Member::get()->filter('Email', $transaction->customer_email)->first()) {
                $customer = Member::create();
            }

            $customer->FromDataFeed = true;

            $customer = $customer->setDataFromTransaction($transaction);

            $customer->write();
            $customer = Member::get()->byID($customer->ID);

            // set Order MemberID
            $this->MemberID = $customer->ID;

            $this->extend('handleOrderCustomer', $order, $response, $customer);
        }
    }

    /**
     * @param $response
     *
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function parseOrderDetails()
    {
        // remove previous OrderDetails and OrderOptions so we don't end up with duplicates
        foreach ($this->Details() as $detail) {
            /** @var OrderOption $orderOption */
            foreach ($detail->OrderOptions() as $orderOption) {
                $orderOption->delete();
            }
            $detail->delete();
        }

        $transaction = $this->getTransaction()->getTransaction();

        // Associate ProductPages, Options, Quantity with Order
        foreach ($transaction->transaction_details->transaction_detail as $detail) {
            $OrderDetail = OrderDetail::create();

            $OrderDetail->Quantity = (int)$detail->product_quantity;
            $OrderDetail->ProductName = (string)$detail->product_name;
            $OrderDetail->ProductCode = (string)$detail->product_code;
            $OrderDetail->ProductImage = (string)$detail->image;
            $OrderDetail->ProductCategory = (string)$detail->category_code;
            $priceModifier = 0;

            // parse OrderOptions
            foreach ($detail->transaction_detail_options->transaction_detail_option as $option) {
                // Find product via product_id custom variable
                if ($option->product_option_name == 'product_id') {
                    // if product is found, set relation to OrderDetail
                    $OrderProduct = ProductPage::get()->byID((int)$option->product_option_value);
                    if ($OrderProduct) {
                        $OrderDetail->ProductID = $OrderProduct->ID;
                    }
                } else {
                    $OrderOption = OrderOption::create();
                    $OrderOption->Name = (string)$option->product_option_name;
                    $OrderOption->Value = (string)$option->product_option_value;
                    $OrderOption->write();
                    $OrderDetail->OrderOptions()->add($OrderOption);

                    $priceModifier += $option->price_mod;
                }
            }

            $OrderDetail->Price = (float)$detail->product_price + (float)$priceModifier;

            // extend OrderDetail parsing, allowing for recording custom fields from FoxyCart
            $this->extend('handleOrderItem', $order, $response, $OrderDetail);

            // write
            $OrderDetail->write();

            // associate with this order
            $this->Details()->add($OrderDetail);
        }
    }

    /**
     * @param bool $member
     *
     * @return bool|int
     */
    public function canView($member = null)
    {
        return Permission::check('Product_ORDERS', 'any', $member);
    }

    /**
     * @param null $member
     *
     * @return bool
     */
    public function canEdit($member = null)
    {
        return false;
        //return Permission::check('Product_ORDERS', 'any', $member);
    }

    /**
     * @param null $member
     *
     * @return bool
     */
    public function canDelete($member = null)
    {
        return false;
        //return Permission::check('Product_ORDERS', 'any', $member);
    }

    /**
     * @param null $member
     * @param array $context
     *
     * @return bool
     */
    public function canCreate($member = null, $context = [])
    {
        return false;
    }

    /**
     * @return array
     */
    public function providePermissions()
    {
        return [
            'Product_ORDERS' => 'Allow user to manage Orders and related objects',
        ];
    }
}
