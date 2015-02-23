<?php

class OrderAdmin extends ModelAdmin {

	public static $managed_models = array(
		'Order'
	);
	
	static $url_segment = 'orders';
	
	static $menu_title = 'Orders';

    public function getEditForm($id = null, $fields = null) {
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
