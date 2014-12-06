<?php
/**
 *
 * @package FoxyStripe
 *
 */

class OptionGroup extends DataObject{

	static $db = array('Title' => 'Varchar(100)');

    static $singular_name = 'Product Option Group';
    static $plural_name = 'Product Option Groups';
    static $description = 'Groups of product options, e.g. size, color, etc';
	
	function getCMSFields(){
		
		$fields = parent::getCMSFields();
		
		$this->extend('getCMSFields', $fields);
		
		return $fields;
	}
	
	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if(!DataObject::get_one('OptionGroup', "`Title` = 'Size'")) {
            $do = new OptionGroup();
            $do->Title = "Size";
            $do->write();
        }
        if(!DataObject::get_one('OptionGroup', "`Title` = 'Color'")) {
            $do = new OptionGroup();
            $do->Title = "Color";
            $do->write();
        }
        if(!DataObject::get_one('OptionGroup', "`Title` = 'Type'")) {
            $do = new OptionGroup();
            $do->Title = "Type";
            $do->write();
        }
	}
	
	public function onBeforeDelete(){
		parent::onBeforeDelete();
		
		//make sure that if we delete this option group, we reassign the group's option items to the 'None' group.
		$items = DataObject::get('OptionItem', "ProductOptionGroupID = {$this->ID}");
		
		if(isset($items)){
			$noneGroup = DataObject::get_one("OptionGroup", "`Title` = 'None'");
			foreach($items as $item){
				$item->ProductOptionGroupID = $noneGroup->ID;
				$item->write();
			}
		}
	}

	public function canView($member = false) {
		return true;
	}

	public function canEdit($member = null) {
		return Permission::check('Product_CANCRUD');
	}

	public function canDelete($member = null) {
		return Permission::check('Product_CANCRUD');
	}

	public function canCreate($member = null) {
		return Permission::check('Product_CANCRUD');
	}

}