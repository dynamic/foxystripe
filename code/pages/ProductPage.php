<?php
/**
 *
 * @package FoxyStripe
 *
 */

class ProductPage extends Page {

	private static $allowed_children = 'none';
	
	private static $db = array(
		'Price' => 'Currency',
		'Weight' => 'Float',
		'Code' => 'Text',
		'ReceiptTitle' => 'Text'
	);
	
	private static $has_one = array(
		'PreviewImage' => 'Image',
		'Category' => 'ProductCategory'
	);
	
	private static $has_many = array(
		'ProductImages' => 'ProductImage',
		'ProductOptions' => 'OptionItem'
	);

    private static $belongs_many_many = array(
        'Orders' => 'Order'
    );
	
	public function populateDefaults() {
		parent::populateDefaults();
		if (!$this->Category) {
			$cat = DataObject::get_one('ProductCategory', "`Code`='DEFAULT'");
			$this->CategoryID = $cat->ID;
		}
	}
	
    private static $defaults = array(
		'ShowInMenus' => false
	);
     
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		
		// Cateogry Dropdown field w/ add new
		$source = function(){
		    return ProductCategory::get()->map()->toArray();
		};
		$catField = DropdownField::create('CategoryID', 'Category', $source());
		if (class_exists('QuickAddNewExtension')) $catField->useAddNew('ProductCategory', $source);
		
		// Product Images gridfield
		$config = GridFieldConfig_RelationEditor::create();
		if (class_exists('GridFieldSortableRows')) $config->addComponent(new GridFieldSortableRows('SortOrder'));
		if (class_exists('GridFieldBulkImageUpload')) {
			$config->addComponent(new GridFieldBulkImageUpload());
			$config->getComponentByType('GridFieldBulkImageUpload')->setConfig('folderName', 'Uploads/ProductImages');	
		}
		$prodImagesField = GridField::create('ProductImages', 'Images', $this->ProductImages(), $config);
		
		// Product Options field
		$config = GridFieldConfig_RelationEditor::create();
		if (class_exists('GridFieldSortableRows')) $config->addComponent(new GridFieldSortableRows('SortOrder'));
		if (class_exists('GridFieldBulkManager')) $config->addComponent(new GridFieldBulkManager());
		$prodOptField = GridField::create('Product Options', 'Options', $this->ProductOptions(), $config);
		
		// Option Groups field
		$config = GridFieldConfig_RecordEditor::create();
		$optGroupField = GridField::create('OptionGroup', 'Option Group', OptionGroup::get(), $config);
		
		
		// Details tab
		$fields->addFieldsToTab('Root.Details', array(
			HeaderField::create('DetailHD', 'Product Details', 2),
			TextField::create('ReceiptTitle', 'Product Title for Receipt')
				->setDescription('Optional'),
			CurrencyField::create('Price'),
			NumericField::create('Weight'),
			TextField::create('Code', 'Product Code'),
			$catField
		));
		
		// Images tab
		$fields->addFieldsToTab('Root.Images', array(
			HeaderField::create('MainImageHD', 'Product Image', 2),
			UploadField::create('PreviewImage', '')
				->setDescription('Image used throughout site to represent this product')
				->setFolderName('Uploads/Products')
				->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'))
				->setAllowedMaxFileNumber(1),
			HeaderField::create('ProductImagesHD', 'Product Image Gallery', 2),
			$prodImagesField
				->setDescription('Additional Product Images, shown in gallery on Product page')
		));
		
		// Options Tab
		$fields->addFieldsToTab('Root.Options', array(
			HeaderField::create('OptionsHD', 'Product Options', 2),
			$prodOptField
		));
		
		// allows CMS fields to be extended
		$this->extend('updateCMSFields', $fields);
		
		return $fields;
	}
	
	public function onBeforeDelete() {
		if($this->Status != "Published") {
			if($this->ProductOptions()) {
				$options = $this->getComponents('ProductOptions');
				foreach($options as $option) {
					$option->delete();
				}
			}
			if($this->ProductImages()) {
				//delete product image dataobjects, not the images themselves.
				$images = $this->getComponents('ProductImages');
				foreach($images as $image) {
					$image->delete();
				}
			}
		}
		parent::onBeforeDelete();
	}
	
	public function getCMSValidator() {
		return new RequiredFields('Price', 'Weight', 'Code');
	}
	
	public function getFormTag() {
		return FoxyCart::FormActionURL();
	}
	
	public function PurchaseForm() {
		return FoxyCart_Helper::fc_hash_html(self::ProductOptionsForm());
	}
	
	public function SingleProductForm() {
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
	
	public function ProductOptionsForm() {
		$form = $this->StartForm();
		$form .= $this->AddBaseProductDetails();
		$form .= $this->ProductOptionsSet();
		$form .= $this->AddToCartForm();
		$form .= $this->EndForm();
		return $form;
	}
	
	public function StartForm() {
		//start form
		$formclass = 'foxycartForm';
		$form = sprintf('<form action="%s" method="post" accept-charset="utf-8" class="foxycart %s" id="product%s">',
			self::getFormTag(),
			$formclass,
			$this->ID
		);
		return $form;
	}
	
	public function EndForm() {
		return "</form>";
	}
	
	public function AddBaseProductDetails(){
		$form = $this->hiddenTag('name', ($this->ReceiptTitle) ? htmlspecialchars($this->ReceiptTitle) : htmlspecialchars($this->Title));
		$form .= $this->hiddenTag('category',$this->Category()->Code);
		$form .= $this->hiddenTag('code', $this->Code);
		$form .= $this->hiddenTag('price', $this->Price);
		$form .= $this->hiddenTag('weight', $this->Weight);
		if($this->PreviewImage()->Exists()) $form .= $this->hiddenTag('image', $this->PreviewImage()->PaddedImage(80,80)->absoluteURL);
		return $form;
	}
	
	public function ProductOptionsSet() {
		$options = $this->ProductOptions();
		
		$groupedProductOptions = new GroupedList($options); 
		$grp = $groupedProductOptions->groupBy("ProductOptionGroupID");
		
		//$grp = $options->groupBy('ProductOptionGroupID');
		
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
	
	public function AddToCartForm() {
		$form = "<div class='addToCartContainer'>";
		$form .= "<label for='quantity'>Quantity</label><div class='foxycart_qty'>";
		$form .= "<select name='quantity'>";
		$form .= "<option value='1'>1</option><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='7'>7</option><option value='8'>8</option><option value='9'>9</option>";
		$form .= "</select>";
		$form .= "</div>";
		$form .= sprintf("<div class='checkoutbtn'><input type='submit' value='%s' class='submit' /><span class='submitPrice' id='SubmitPrice%s'>%s $%2.2f</span></div>",
			'Add to Cart',
			$this->ID,
			$this->Title,
			$this->Price
		);
		$form .= "</div>";
		return $form;
	}
	
	public function selectField($name = null, $id = null, $optionSet = null) {
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
		
	public function hiddenTag($name=null, $val=null) {
		return sprintf('<input type="hidden" name="%s" value="%s" />',
			$name,
			$val
		);
	}
	
}

class ProductPage_Controller extends Page_Controller {

	private static $allowed_actions = array();

	public function init(){
		parent::init();

		Requirements::javascript(THIRDPARTY_DIR.'/jquery/jquery.js');
		Requirements::javascript('foxystripe/thirdparty/flexslider/jquery.flexslider-min.js');
		Requirements::css('foxystripe/thirdparty/flexslider/flexslider.css');
		Requirements::javascript('foxystripe/thirdparty/shadowbox/shadowbox.js');
		Requirements::css('foxystripe/thirdparty/shadowbox/shadowbox.css');

		if(SiteConfig::current_site_config()->CartPage==false){
			Requirements::css('//cdn.foxycart.com/static/scripts/colorbox/1.3.19/style1_fc/colorbox.css?ver=1');
			Requirements::javascript('//cdn.foxycart.com/' . FoxyCart::getFoxyCartStoreName() . '/foxycart.colorbox.js?ver=2');
		}

	}
}
