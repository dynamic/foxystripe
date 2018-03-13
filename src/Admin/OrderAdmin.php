<?php

namespace Dynamic\FoxyStripe\Admin;

use Dynamic\FoxyStripe\Model\Order;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldEditButton;

class OrderAdmin extends ModelAdmin
{
    /**
     * @var array
     */
    private static $managed_models = array(
        Order::class,
    );

    /**
     * @var string
     */
    private static $url_segment = 'orders';

    /**
     * @var string
     */
    private static $menu_title = 'Orders';

    /**
     * @var int
     */
    private static $menu_priority = 4;

    /**
     * @param null $id
     * @param null $fields
     *
     * @return \SilverStripe\Forms\Form
     */
    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);

        $gridFieldName = $this->sanitiseClassName($this->modelClass);
        /** @var GridField $gridField */
        $gridField = $form->Fields()->fieldByName($gridFieldName);

        // GridField configuration
        /** @var GridFieldConfig $config */
        $config = $gridField->getConfig();

        // remove edit icon
        $config->removeComponentsByType(GridFieldEditButton::class);

        return $form;
    }
}
