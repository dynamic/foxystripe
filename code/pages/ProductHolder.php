<?php

/**
 * @package FoxyStripe
 * @method Products|SS_List $Products
 */
class ProductHolder extends Page
{

    /**
     * @var string
     */
    private static $singular_name = 'Product Group';

    /**
     * @var string
     */
    private static $plural_name = 'Product Groups';

    /**
     * @var string
     */
    private static $description = 'Display a list of related products';

    /**
     * @var array
     */
    private static $allowed_children = [
        'ProductHolder',
    ];

    /**
     * @var array
     */
    private static $many_many = [
        'Products' => 'FoxyStripeProduct',
    ];

    /**
     * @var array
     */
    private static $many_many_extraFields = array(
        'Products' => array(
            'SortOrder' => 'Int'
        )
    );

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $config = GridFieldConfig_RelationEditor::create();
        if (class_exists('GridFieldSortableRows')) {
            $config->addComponent(new GridFieldSortableRows('SortOrder'));
        }
        $config->removeComponentsByType('GridFieldAddExistingAutocompleter');
        $config->addComponent(new GridFieldAddExistingSearchButton());
        $fields->addFieldToTab(
            'Root.Products',
            GridField::create(
                'Products',
                _t('ProductHolder.Products', 'Products'),
                $this->Products(),
                $config
            )
        );

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    /**
     * loadDescendantProductGroupIDListInto function.
     *
     * @access public
     * @param mixed &$idList
     */
    public function loadDescendantProductGroupIDListInto(&$idList)
    {
        if ($children = $this->AllChildren()) {
            foreach ($children as $child) {
                if (in_array($child->ID, $idList)) continue;

                if ($child instanceof ProductHolder) {
                    $idList[] = $child->ID;
                    $child->loadDescendantProductGroupIDListInto($idList);
                }
            }
        }
    }

    /**
     * ProductGroupIDs function.
     *
     * @access public
     * @return array
     */
    public function ProductGroupIDs()
    {
        $holderIDs = [];
        $this->loadDescendantProductGroupIDListInto($holderIDs);
        return $holderIDs;
    }

    /**
     * @param int $limit
     * @return PaginatedList
     */
    public function getPaginatedProducts($limit = 10)
    {
        return PaginatedList::create($this->Products()->sort('SortOrder'))->setPageLength($limit);
    }

}

class ProductHolder_Controller extends Page_Controller
{
    private static $allowed_actions = [
        'product',
    ];

    /**
     *
     */
    public function init()
    {
        parent::init();

    }

    public function product(SS_HTTPRequest $request)
    {

        if (!$slug = $request->latestParam('ID')) {
            return $this->httpError(404);
        }

        if (!$product = FoxyStripeProduct::get()->byUrlSegment($slug)) {
            return $this->httpError(404);
        }

        return $this->customise([
            'Product' => $product,
        ]);

    }
}