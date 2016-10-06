<?php
/**
 * Dropdown field, created from a <select> tag. This field handles cart encryption based on store settings.
 **
 * <b>Populate with Array</b>
 *
 * Example instantiation:
 * <code>
 * FoxyStripeDropdownField::create('Country')
 * 		->setSource(array(
 *     'NZ' => 'New Zealand',
 *     'US' => 'United States',
 *     'GEM'=> 'Germany'
 *   ));
 * </code>
 *
 * <b>Populate with Enum-Values</b>
 *
 * You can automatically create a map of possible values from an {@link Enum} database column.
 *
 * Example model definition:
 * <code>
 * class MyObject extends DataObject {
 *   static $db = array(
 *     'Country' => "Enum('New Zealand,United States,Germany','New Zealand')"
 *   );
 * }
 * </code>
 *
 * Field construction:
 * <code>
 * FoxyStripeDropdownField::create('Country')
 *   ->setSource(singleton('MyObject')->dbObject('Country')->enumValues());
 * </code>
 *
 * <b>Disabling individual items</b>
 *
 * Individual items can be disabled by feeding their array keys to setDisabledItems.
 *
 * <code>
 * $DrDownField->setDisabledItems( array( 'US', 'GEM' ) );
 * </code>
 *
 * @see CheckboxSetField for multiple selections through checkboxes instead.
 * @see ListboxField for a single <select> box (with single or multiple selections).
 * @see TreeDropdownField for a rich and customizeable UI that can visualize a tree of selectable elements
 *
 * @package forms
 * @subpackage fields-basic
 */
class FoxyStripeDropdownField extends DropdownField{

	/**
	 * Mark certain elements as disabled,
	 * regardless of the {@link setDisabled()} settings.
	 *
	 * @param array $items Collection of array keys, as defined in the $source array
     * @return $this
	 */
	public function setDisabledItems($items){
		$controller = Controller::curr();
		$code = $controller->data()->Code;
		$updated = [];
		if(is_array($items) && !empty($items)){
			foreach($items as $item){
				array_push($updated, FoxyStripeProduct::getGeneratedValue($code, $this->getName(), $item, 'value'));
			}
		}
		$this->disabledItems = $updated;
		return $this;
	}

	/**
	 * @param array $source
     * @return $this
	 */
	public function setSource($source) {
		$controller = Controller::curr();
		$code = $controller->data()->Code;
		$updated = [];
		if(is_array($source) && !empty($source)){
			foreach($source as $key => $val){
				$updated[FoxyStripeProduct::getGeneratedValue($code, $this->getName(), $key, 'value')] = $val;
			}
		}
		$this->source = $updated;
		return $this;
	}

}
