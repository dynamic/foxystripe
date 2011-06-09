<?php
/**
 *
 * @package FoxyStripe
 *
 */

class ProductPage extends Page {
	public static $allowed_children = 'none';
	public static $db = array(
		'Price' => 'Float',
		'Weight' => 'Float',
		'Code' => 'Text',
		'ReceiptTitle' => 'Text'
	);
	
	public static $has_one = array(
		'PreviewImage' => 'Image',
		'Category' => 'ProductCategory'
	);
	
	public static $has_many = array(
		'ProductImages' => 'ProductImage',
		'ProductOptions' => 'OptionItem'
	);

	public function getCMSFields(){
		$fields = parent::getCMSFields();
		
		$ctf = 'ComplexTableField';
		$hoctf = 'HasOneComplexTableField';
		$hmctf = 'HasManyComplexTableField';
		
		if(ClassInfo::exists('DataObjectManager')){
			$ctf = 'DataObjectManager';
			$hoctf = 'HasOneDataObjectManager';
			$hmctf = 'HasManyDataOBjectManager';
		}
		
		$fields->addFieldToTab('Root.Content.Details', new TextField('ReceiptTitle', '(Optional) Product Title for Receipt'));
		
		$fields->addFieldToTab('Root.Content.Details', new NumericField('Price', 'Base Price (in US dollars)'));
		$fields->addFieldToTab('Root.Content.Details', new TextField('Code', 'SKU / Code'));
		$fields->addFieldToTab('Root.Content.Details', new NumericField('Weight', 'Base Weight'));
		
		$fields->addFieldToTab('Root.Content.Details', new LiteralField('ProductCategory', '<h2>Product Category</h2>'));
		$fields->addFieldToTab('Root.Content.Details',
			new $hoctf(
			$this,
			"Category", 
			"ProductCategory",
			array(
				'Title' => 'Title',
				'Code' => 'Foxycart Code'
			),
			'getCMSFields'
		));
		
		$fields->addFieldToTab('Root.Content.Details', new LiteralField('OptionGroups', '<h2>Option Groups</h2><p>Option Groups represent groups of options like size, color, etc</p>'));
		
		$optgrpfield=new $ctf(
			$this,
			"OptionGroup", 
			"OptionGroup",
			array(
				'Title' => 'Title'
			),
			'getCMSFields'
		);
		$optgrpfield->setAddTitle('an Option Group');
		
		$fields->addFieldToTab('Root.Content.Details',$optgrpfield);
		
		/*
		//functions do not work in FieldList with DataObjectManager
		$optionSet = new $hmctf(
			$this,
			'ProductOptions',
			'OptionItem',
			array(
				'Title' => 'Title',
				'weightModifierWithSymbol' => 'Weight Modifier',
				'priceModifierWithSymbol' => 'Price Modifier',
				'codeModifierWithSymbol' => 'Code Modifier',
				'ProductOptionGroup.Title' => 'Option Group'
			),
			'getCMSFields',
			'',
			'ProductOptionGroupID'
		);
		*/
		$optionSet = new $hmctf(
			$this,
			'ProductOptions',
			'OptionItem',
			array(
				'Title' => 'Title',
				'WeightModifier' => 'Weight Modifier',
				'PriceModifier' => 'Price Modifier',
				'CodeModifier' => 'Code Modifier',
				'ProductOptionGroupID' => 'Option Group'
			),
			'getCMSFields',
			'',
			'ProductOptionGroupID'
		);
		
		$optionSet->setParentClass('ProductPage');
		$optionSet->relationAutoSetting = true;
		$optionSet->setAddTitle('a Product Option');
		$optionSet->setPopupSize(560,490);
		
		$fields->addFieldToTab('Root.Content.Details', new LiteralField('OptionItems', 
		'<h2>Product Options</h2>
		<p>Modifiers with a + or - in front of them mean the value will be added or subtracted to the base weight, price, or code entered above.</p>
		<p>Modifiers with a : in front of them mean the value will be used instead of the base value.</p>
		<p>If you have multiple option groups you should use add or subtract, otherwise setting the value will override options in other groups.</p>'));
		$fields->addFieldToTab('Root.Content.Details', $optionSet);
				
		$fields->addFieldToTab('Root.Content.Images', new ImageField('PreviewImage', 'Preview Image'));
		$ProductImageField = new $hmctf(
			$this,
			'ProductImages',
			'ProductImage',
			array('Title' => 'Title'),
			'getCMSFields'
		);
		$ProductImageField->setParentClass('ProductPage');
		$ProductImageField->relationAutoSetting = true;
		$ProductImageField->setAddTitle('a Product Image');
		$fields->addFieldToTab('Root.Content.Images', $ProductImageField);
		return $fields;
	}
	
	public function onBeforeWrite(){
		if(!$this->Category){
			$cat = DataObject::get_one('ProductCategory', "`Code`='DEFAULT'");
			$this->CategoryID = $cat->ID;
		}
		parent::onBeforeWrite();
	}
	
	public function onBeforeDelete(){
		if($this->ProductOptions()) $this->ProductOptions()->delete();
		if($this->ProductImages()) $this->ProductImages()->delete();
		parent::onBeforeDelete(); 
	}
	
	public function getCMSValidator() {
		return new RequiredFields('Price', 'Weight', 'Code');
	}
	
	public function getFormTag(){
		return FoxyCart::FormActionURL();
	}
	
	function PurchaseForm(){
	
		if(!$this->ProductOptions()){
			// if there are no product options..
			return self::SingleProductForm();
			
		} else{
			return self::ProductOptionsForm();
		}
	}
	
	function SingleProductForm(){
		//make sure to urlencode url params
		return sprintf('<div class="addToCartContainer"><a href="%s?name=%s&price=%2.2f&code=%s&category=%s&weight=%s&image=%s">Buy %s for $%2.2f</a></div>',
			self::getFormTag(),
			($this->ReceiptTitle) ? $this->dbObject('ReceiptTitle')->URLATT() : $this->dbObject('Title')->URLATT(),
			$this->Price,
			$this->Code,
			$this->Category()->Code,
			$this->Weight,
			$this->PreviewImage()->PaddedImage(80,80)->URL,
			$this->Title,
			$this->Price
		);
	}
	
	function selectField($name = null, $optionSet = null){
		if($optionSet && $name){
			if($name != 'None'){
				$selectField = "<label for='{$name}'>$name</label><select name='{$name}'>";
			} else {
				$selectField = "<select name='{$name}'>";
			}
			foreach($optionSet as $option){
				
				$modPrice = ($option->PriceModifier) ? (string)$option->PriceModifier : '0';
				$modPriceWithSymbol = OptionItem::getOptionModifierActionSymbol($option->PriceModifierAction).$modPrice;
				
				$modWeight = ($option->WeightModifier) ? (string)$option->WeightModifier : '0';
				$modWeight = OptionItem::getOptionModifierActionSymbol($option->WeightModifierAction).$modWeight;
				
				$modCode = OptionItem::getOptionModifierActionSymbol($option->CodeModifierAction).$option->CodeModifier;
				
				$selectField .= sprintf('<option value="%s{p%s|w%s|c%s}">%s%s</option>',
					$option->Title,
					$modPriceWithSymbol,
					$modWeight,
					$modCode,
					$option->Title,
					($option->PriceModifier != 0) ? ': ('.OptionItem::getOptionModifierActionSymbol($option->PriceModifierAction, $returnWithPlusMinus=true).'$'.$modPrice.')' : ''
				);
			}
			$selectField .= '</select>';
			return $selectField;
		}
	}
	
	function ProductOptionsSet(){
		$options = $this->ProductOptions();
		$grp = $options->groupBy('ProductOptionGroupID');
		$form = "<div class='foxycartOptionsContainer'>";
		foreach($grp as $id=>$optionSet){
			$optionGroupTitle = DataObject::get_by_id('OptionGroup',$id)->Title;
			$form .= $this->selectField($optionGroupTitle, $optionSet);
		}
		$form .= "</div>";
		return $form;
	}
	
	function hiddenTag($name=null, $val=null){
		return sprintf('<input type="hidden" name="%s" value="%s" />',
			$name,
			$val
		);
	}
	
	function AddToCartForm(){
		$form = "<div class='addToCartContainer'>";		
		$form .= $this->hiddenTag('name', ($this->ReceiptTitle) ? htmlspecialchars($this->ReceiptTitle) : htmlspecialchars($this->Title));
		$form .= $this->hiddenTag('category',$this->Category()->Code);
		$form .= $this->hiddenTag('code', $this->Code);
		$form .= $this->hiddenTag('price', $this->Price);
		
		$form .= sprintf('<input type="submit" value="%s" class="submit" /><span class="submitPrice" id="SubmitPrice%s">%s $%2.2f</span>',
			'Add to Cart',
			$this->ID,
			$this->Title,
			$this->Price
		);
		$form .= "</div>";
		return $form;
	}
	
	
	
	function ProductOptionsForm(){
		//start form
		$formclass = 'foxycartForm';
		$form = sprintf('<form action="%s" method="post" accept-charset="utf-8" class="foxycart %s">',
			self::getFormTag(),
			$formclass
		);
		$form .= $this->ProductOptionsSet();
		$form .= $this->AddToCartForm();
		$form .= "</form>";
		
		return $form;
		
	}
}

class ProductPage_Controller extends Page_Controller {
	public function init(){
		Requirements::css('FoxyStripe/css/foxycart.css');
		parent::init();
	}
}