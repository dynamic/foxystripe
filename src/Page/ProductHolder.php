<?php

namespace Dynamic\FoxyStripe\Page;

use Dynamic\FoxyStripe\Model\FoxyStripeSetting;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\PaginatedList;
use Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**\
 * Class ProductHolder
 * @package Dynamic\FoxyStripe\Page
 */
class ProductHolder extends \Page
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
    private static $many_many = [
        'Products' => ProductPage::class,
    ];

    /**
     * @var array
     */
    private static $many_many_extraFields = [
        'Products' => [
            'SortOrder' => 'Int',
        ],
    ];

    /**
     * @var array
     */
    /*
    private static $allowed_children = [
        ProductHolder::class,
        ProductPage::class,
    ];
    */

    /**
     * @var string
     */
    private static $table_name = 'ProductHolder';

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            if (FoxyStripeSetting::current_foxystripe_setting()->MultiGroup) {
                $config = GridFieldConfig_RelationEditor::create();
                $config->addComponent(new GridFieldOrderableRows('SortOrder'));
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
            }
        });

        return parent::getCMSFields();
    }

    /**
     * @return DataList
     */
    public function Products()
    {
        return $this->getManyManyComponents('Products')->sort('SortOrder');
    }

    /**
     * loadDescendantProductGroupIDListInto function.
     *
     * @param mixed &$idList
     */
    public function loadDescendantProductGroupIDListInto(&$idList)
    {
        if ($children = $this->AllChildren()) {
            foreach ($children as $child) {
                if (in_array($child->ID, $idList)) {
                    continue;
                }

                if ($child instanceof self) {
                    $idList[] = $child->ID;
                    $child->loadDescendantProductGroupIDListInto($idList);
                }
            }
        }
    }

    /**
     * ProductGroupIDs function.
     *
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
     *
     * @return PaginatedList
     *
     * @throws \Exception
     */
    public function ProductList($limit = 10)
    {
        $config = FoxyStripeSetting::current_foxystripe_setting();

        if ($config->ProductLimit > 0) {
            $limit = $config->ProductLimit;
        }

        if ($config->MultiGroup) {
            $entries = $this->Products()->sort('SortOrder');
        } else {
            $filter = '"ParentID" = ' . $this->ID;

            // Build a list of all IDs for ProductGroups that are children
            $holderIDs = $this->ProductGroupIDs();

            // If no ProductHolders, no ProductPages. So return false
            if ($holderIDs) {
                // Otherwise, do the actual query
                if ($filter) {
                    $filter .= ' OR ';
                }
                $filter .= '"ParentID" IN (' . implode(',', $holderIDs) . ')';
            }

            $order = '"SiteTree"."Title" ASC';

            $entries = ProductPage::get()->where($filter);
        }

        $list = new PaginatedList($entries, Controller::curr()->getRequest());
        $list->setPageLength($limit);

        return $list;
    }
}
