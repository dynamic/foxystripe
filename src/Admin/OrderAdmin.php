<?php

namespace Dynamic\FoxyStripe\Admin;

use SilverStripe\Admin\ModelAdmin;

class OrderAdmin extends ModelAdmin
{
    /**
     * @var array
     */
    private static $managed_models = array(
        'Order',
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
        $gridField = $form->Fields()->fieldByName($gridFieldName);

        // GridField configuration
        $config = $gridField->getConfig();

        // remove edit icon
        $config->removeComponentsByType('GridFieldEditButton');

        return $form;
    }
}
