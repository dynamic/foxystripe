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
	public function populateDefaults(){
          parent::populateDefaults();
          if(!$this->Category){
               $cat = DataObject::get_one('ProductCategory', "`Code`='DEFAULT'");
               $this->CategoryID = $cat->ID;
          }
     }
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
				'productOptionGroupTitle' => 'Option Group'
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
	
	public function onBeforeDelete(){
          if($this->Status != "Published"){
               if($this->ProductOptions()){
                    $options = $this->getComponents('ProductOptions');
                    foreach($options as $option){
                         $option->delete();
                    }
               }
               if($this->ProductImages()){
                    //delete product image dataobjects, not the images themselves.
                    $images = $this->getComponents('ProductImages');
                    foreach($images as $image){
                         $image->delete();
                    }
               }
          }
          parent::onBeforeDelete();
     }
	
     public function getCMSValidator() {
          return new RequiredFields('Price', 'Weight', 'Code');
     }
	
     public function getFormTag(){
          return FoxyCart::FormActionURL();
     }
	
     function PurchaseForm(){
          return self::ProductOptionsForm();
     }
	
	function SingleProductForm(){
		//make sure to urlencode url params
		return sprintf('<div class="addToCartContainer"><a href="%s?name=%s&price=%2.2f&code=%s&category=%s&weight=%s&image=%s"><span class="addToCart">Add To Cart</span><span class="submitPrice">%s $%2.2f</span></a></div>',
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
	
	function ProductOptionsForm(){
		$form = $this->StartForm();
		$form .= $this->AddBaseProductDetails();
		$form .= $this->ProductOptionsSet();
		$form .= $this->AddToCartForm();
		$form .= $this->EndForm();
		return $form;
	}
	
	
	
	function StartForm(){
		//start form
		$formclass = 'foxycartForm';
		$form = sprintf('<form action="%s" method="post" accept-charset="utf-8" class="foxycart %s" id="product%s">',
			self::getFormTag(),
			$formclass,
			$this->ID
		);
		return $form;
	}
	function EndForm(){
		return "</form>";
	}
	
	function AddBaseProductDetails(){
		$form = $this->hiddenTag('name', ($this->ReceiptTitle) ? htmlspecialchars($this->ReceiptTitle) : htmlspecialchars($this->Title));
		$form .= $this->hiddenTag('category',$this->Category()->Code);
		$form .= $this->hiddenTag('code', $this->Code);
		$form .= $this->hiddenTag('price', $this->Price);
		$form .= $this->hiddenTag('weight', $this->Weight);
		if($this->PreviewImage()->Exists()) $form .= $this->hiddenTag('image', $this->PreviewImage()->PaddedImage(80,80)->absoluteURL);
		return $form;
	}
	
	function ProductOptionsSet(){
		$options = $this->ProductOptions();
		$grp = $options->groupBy('ProductOptionGroupID');
		
		
		$form = "<div class='foxycartOptionsContainer'>";
		foreach($grp as $id=>$optionSet){
			$optionGroupTitle = DataObject::get_by_id('OptionGroup',$id)->Title;
			$form .= $this->selectField($optionGroupTitle, $id, $optionSet);
		}
		$form .= "</div>";
		
		
		$script = <<<JS
jQuery(function(){
	
	jQuery('.foxycartOptionsContainer select').change(function(){
		refreshAddToCartPrice();
	});
	
	function refreshAddToCartPrice(){
	
		var addCartDiv = jQuery('form.foxycartForm#product{$this->ID}');
		var baseName = jQuery(addCartDiv).find('input[name=\'name\']').val();
		var basePrice = parseFloat(jQuery(addCartDiv).find('input[name=\'price\']').val());
		
		var newProductPrice = basePrice;
		
		jQuery('form.foxycartForm#product{$this->ID} select').each(function(){
		
			var currentOption = jQuery(this).val();
			//get an array of the modifiers
			currentOption = currentOption.substring(currentOption.lastIndexOf('{')+1, currentOption.lastIndexOf('}')).split('|');
			
			//build a different array of key-value pairs, options[p,c,w] = value
			//more reliable than hoping price is the first array index of currentOption..
			var options = [];
			for(i=0; i< currentOption.length; i++){
				var k = currentOption[i].substr(0,1);
				var val = currentOption[i].substr(1);
				options[k] = val;
			}
			var pricemodifier = options['p'].substr(0,1); // return +,-,:
			
			if(pricemodifier == ':'){
				newProductPrice = parseFloat(options['p'].substr(1));
			} else {
				newProductPrice = newProductPrice+parseFloat(options['p']);
			}
		});
		jQuery('form.foxycartForm#product{$this->ID} .submitPrice').html(baseName+' $'+newProductPrice.toFixed(2));
	}
	if(jQuery('.foxycartOptionsContainer select').length > 0) refreshAddToCartPrice();
});
JS;

		Requirements::customScript($script);
		
		return $form;
	}
	
	function AddToCartForm(){
		$form = "<div class='addToCartContainer'>";
		$form .= "<label for='quantity'>Quantity</label><div class='foxycart_qty'><input type='text' name='quantity' value='1' /></div>";
		$form .= sprintf("<div class='checkoutbtn'><input type='submit' value='%s' class='submit' /><span class='submitPrice' id='SubmitPrice%s'>%s $%2.2f</span></div>",
			'Add to Cart',
			$this->ID,
			$this->Title,
			$this->Price
		);
		$form .= "</div>";
		return $form;
	}
	
	function selectField($name = null, $id = null, $optionSet = null){
		if($optionSet && $id && $name){
			if($name != 'None'){
				$selectField = "<label for='{$name}'>$name</label><select name='{$name}' id='{$id}'>";
			} else {
				$selectField = "<label for='{$name}'>&nbsp;</label><select name='{$name}' id='{$id}'>";
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
					($option->PriceModifier != 0) ? ': ('.OptionItem::getOptionModifierActionSymbol($option->PriceModifierAction, $returnWithOnlyPlusMinus=true).'$'.$modPrice.')' : ''
				);
			}
			$selectField .= '</select>';
			return $selectField;
		}
	}
		
	function hiddenTag($name=null, $val=null){
		return sprintf('<input type="hidden" name="%s" value="%s" />',
			$name,
			$val
		);
	}
	
}

class ProductPage_Controller extends Page_Controller {
	public function init(){
		parent::init();
		Requirements::css('FoxyStripe/css/foxycart.css');
		Requirements::customScript("window.jQuery || document.write('<script src=\'//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js\'><\/script>');");
	}
}
