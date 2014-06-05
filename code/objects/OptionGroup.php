<?php
/**
 *
 * @package FoxyStripe
 *
 */

class OptionGroup extends DataObject{

	static $db = array('Title' => 'Text');
	
	function getCMSFields(){
		$fields = new FieldList();
		$fields->push(new TextField('Title', 'Option Group Name'));
		$this->extend('getCMSFields', $fields);
		
		return $fields;
	}
	
	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if(!DataObject::get_one('OptionGroup', "`Title` = 'None'")) {
			$do = new OptionGroup();
			$do->Title = "None";
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