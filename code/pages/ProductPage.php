<?php
/**
 *
 * @package FoxyStripe
 *
 */

class ProductPage extends Page implements PermissionProvider {

	private static $allowed_children = 'none';
	
	private static $db = array(
		'Price' => 'Currency',
		'Weight' => 'Float',
		'Code' => 'Varchar(100)',
		'ReceiptTitle' => 'Text',
		'Featured' => 'Boolean',
		'Available' => 'Boolean'
	);
	
	private static $has_one = array(
		'PreviewImage' => 'Image',
		'Category' => 'ProductCategory'
	);
	
	private static $has_many = array(
		'ProductImages' => 'ProductImage',
		'ProductOptions' => 'OptionItem',
        'OrderDetails' => 'OrderDetail'
	);

    private static $belongs_many_many = array(
		'ProductHolders' => 'ProductHolder'
    );

    private static $singular_name = 'Product';
    private static $plural_name = 'Products';
    private static $description = 'A product that can be added to the shopping cart';

    private static $indexes = array(
        'Code' => true // make unique
    );
	
	private static $defaults = array(
		'ShowInMenus' => false,
		'Available' => true,
        'Weight' => '1.0'
	);

    private static $summary_fields = array(
        'Title',
        'Code',
        'Price.Nice',
        'Category.Title'
    );

    private static $searchable_fields = array(
        'Title',
        'Code',
        'Featured',
        'Available',
        'Category.ID'
    );

    function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels();

        $labels['Title'] = 'Name';
        $labels['Code'] = "Code";
        $labels['Price.Nice'] = 'Price';
        $labels['Featured.Nice'] = 'Featured';
        $labels['Available.Nice'] = 'Available';
        $labels['Category.ID'] = 'Category';
        $labels['Category.Title'] = 'Category';

        return $labels;
    }
     
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		
		// Cateogry Dropdown field w/ add new
		$source = function(){
		    return ProductCategory::get()->map()->toArray();
		};
		$catField = DropdownField::create('CategoryID', 'FoxyCart Category', $source())
            ->setEmptyString('')
            ->setDescription('Required, must also exist in <a href="https://admin.foxycart.com/admin.php?ThisAction=ManageProductCategories" target="_blank">FoxyCart Categories</a>. Used to set category specific options like shipping and taxes. Managed in <a href="admin/settings">Settings > FoxyStripe > Categories</a>');
		if (class_exists('QuickAddNewExtension')) $catField->useAddNew('ProductCategory', $source);
		
		// Product Images gridfield
		$config = GridFieldConfig_RelationEditor::create();
		if (class_exists('GridFieldSortableRows')) $config->addComponent(new GridFieldSortableRows('SortOrder'));
		if (class_exists('GridFieldBulkImageUpload')) {
			$config->addComponent(new GridFieldBulkUpload());
			$config->getComponentByType('GridFieldBulkUpload')->setConfig('folderName', 'Uploads/ProductImages');
		}
		$prodImagesField = GridField::create('ProductImages', 'Images', $this->ProductImages(), $config);
		
		// Product Options field
		$config = GridFieldConfig_RelationEditor::create();
		if (class_exists('GridFieldBulkManager')) $config->addComponent(new GridFieldBulkManager());
		if (class_exists('GridFieldSortableRows')){
			$config->addComponent(new GridFieldSortableRows('SortOrder'));
			$products = $this->ProductOptions()->sort('SortOrder');
		}else{
			$products = $this->ProductOptions();
		}
		$config->removeComponentsByType('GridFieldAddExistingAutocompleter');
		$prodOptField = GridField::create('ProductOptions', 'Options', $products, $config);
		
		// Option Groups field
		$config = GridFieldConfig_RecordEditor::create();
		$optGroupField = GridField::create('OptionGroup', 'Option Group', OptionGroup::get(), $config);
		
		
		// Details tab
		$fields->addFieldsToTab('Root.Details', array(
			HeaderField::create('DetailHD', 'Product Details', 2),
			CheckboxField::create('Available')
				->setTitle('Available for purchase')
                ->setDescription('If unchecked, will remove "Add to Cart" form and instead display "Currently unavailable"'),
            TextField::create('Code', 'Product Code')
                ->setDescription('Required, must be unique. Product identifier used by FoxyCart in transactions'),
            $catField,
            CurrencyField::create('Price')
                ->setDescription('Base price for this product. Can be modified using Product Options'),
            NumericField::create('Weight')
                ->setDescription('Base weight for this product. Can be modified using Product Options'),
			CheckboxField::create('Featured')
				->setTitle('Featured Product'),
            TextField::create('ReceiptTitle', 'Product Title for Receipt')
                ->setDescription('Optional')
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
			LiteralField::create('OptionsDescrip', '<p>Product Options allow products to be customized by attributes such as size or color. Options can also modify the product\'s price, weight or code.</p>'),
			$prodOptField
		));

		if(FoxyCart::store_key_warning()!==null){
			$fields->addFieldToTab('Root.Main', new LiteralField("StoreKeyHeaderWarning", "<p class=\"message error\">Store key must be entered in the <a href=\"/admin/settings/\">site settings</a></p>"), 'Title');
		}
		if(FoxyCart::store_name_warning()!==null){
			$fields->addFieldToTab('Root.Main', new LiteralField("StoreSubDomainHeaderWarning", "<p class=\"message error\">Store sub-domain must be entered in the <a href=\"/admin/settings/\">site settings</a></p>"), 'Title');
		}
		
		// allows CMS fields to be extended
		$this->extend('updateCMSFields', $fields);
		
		return $fields;
	}

	public function onBeforeWrite(){
		parent::onBeforeWrite();
		if(!$this->CategoryID){
			$default = ProductCategory::get()->filter(array('Code' => 'DEFAULT'))->first();
			$this->CategoryID = $default->ID;
		}
		
		//update many_many lists when multi-group is on
		if(SiteConfig::current_site_config()->MultiGroup){
			$holders = $this->ProductHolders();
			$product = ProductPage::get()->byID($this->ID);
			if (isset($product->ParentID)) {
				$origParent = $product->ParentID;
			} else {
				$origParent = null;
			}
			$currentParent = $this->ParentID;
			if($origParent!=$currentParent){
				if($holders->find('ID', $origParent)){
					$holders->removeByID($origParent);
				}
				
			}
			$holders->add($currentParent);
		}

	}

	public function onAfterWrite(){
		parent::onAfterWrite();
		
		

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

	public function validate(){
		$result = parent::validate();

		/*if($this->ID>0){
			if($this->Price <= 0) {
				$result->error('Must set a positive price value');
			}
			if($this->Weight <= 0){
				$result->error('Must set a positive weight value');
			}
			if($this->Code == ''){
				$result->error('Must set a product code');
			}
		}*/

		return $result;
	}
	
	public function getCMSValidator() {
		return new RequiredFields(array('CategoryID', 'Price', 'Weight', 'Code'));
	}
	
	public function getFormTag() {
		return FoxyCart::FormActionURL();
	}
	
	public function PurchaseForm(){
		if($this->Available){
			return (SiteConfig::current_site_config()->CartValidation) ? FoxyCart_Helper::fc_hash_html($this->ProductOptionsForm()) : $this->ProductOptionsForm();
		}
		return "<h3 class=\"unavailable-product\">Product currently unavailable</h3>";
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
        $form .= $this->hiddenTag('product_id', $this->ID);
        $form .= '<input type="hidden" name="price" value="' . $this->Price . '" id="basePrice" />';
		//$form .= $this->hiddenTag('price', $this->Price);
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

		var newProductPrice = parseFloat(jQuery('#basePrice').val());;
		
		jQuery('form.foxycartForm#product{$this->ID} select').each(function(){

		    if ( jQuery(this).attr('id') == 'qty' ) {
		        // todo: modify newProductPrice by Quantity?

            } else {
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

                if(typeof options['p'] != 'undefined'){
                    var pricemodifier = options['p'].substr(0,1); // return +,-,:

                    if(pricemodifier == ':'){
                        newProductPrice = parseFloat(options['p'].substr(1));
                    } else {
                        newProductPrice = newProductPrice+parseFloat(options['p']);
                    }
                }
            }
		

		});
		jQuery('form.foxycartForm#product{$this->ID} .submitPrice').html('$'+newProductPrice.toFixed(2));
	}
	if(jQuery('.foxycartOptionsContainer select').length > 0) refreshAddToCartPrice();
});
JS;

		Requirements::customScript($script);
		
		return $form;
	}
	
	public function AddToCartForm() {
		$form = "<div class='field'>";
		$form .= "<label for='quantity'>Quantity</label><div class='foxycart_qty'>";
		$form .= "<select name='quantity' id='qty'>";
		$form .= "<option value='1'>1</option><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='7'>7</option><option value='8'>8</option><option value='9'>9</option>";
		$form .= "</select>";
		$form .= "</div>";
		$form .= sprintf("<div class='field checkoutbtn'><h4 class='submitPrice' id='SubmitPrice%s'>$%2.2f</h4><input type='submit' value='%s' class='submit' /></div>",
			$this->ID,
			$this->Price,
			'Add to Cart'
		);
		$form .= "</div>";
		return $form;
	}
	
	public function selectField($name = null, $id = null, $optionSet = null) {
		if($optionSet && $id && $name){
			$selectField = '<div class="field selectfield">';
			if($name != 'None'){
				$selectField .= "<label for='{$name}'>$name</label><select name='{$name}' id='{$id}'>";
			} else {
				$selectField .= "<label for='{$name}'>&nbsp;</label><select name='{$name}' id='{$id}'>";
			}
			foreach($optionSet as $option){
				
				$modPrice = ($option->PriceModifier) ? (string)$option->PriceModifier : '0';
				$modPriceWithSymbol = OptionItem::getOptionModifierActionSymbol($option->PriceModifierAction).$modPrice;
				
				$modWeight = ($option->WeightModifier) ? (string)$option->WeightModifier : '0';
				$modWeight = OptionItem::getOptionModifierActionSymbol($option->WeightModifierAction).$modWeight;
				
				$modCode = OptionItem::getOptionModifierActionSymbol($option->CodeModifierAction).$option->CodeModifier;
				
				// is product option avaiable for purchase?
				$available = '';
				if ($option->Available == 0) $available = ' class="outOfStock"';
				
				$selectField .= sprintf('<option value="%s{p%s|w%s|c%s}" %s>%s%s</option>',
					$option->Title,
					$modPriceWithSymbol,
					$modWeight,
					$modCode,
					$available,
					$option->Title,
					($option->PriceModifier != 0) ? ': ('.OptionItem::getOptionModifierActionSymbol($option->PriceModifierAction, $returnWithOnlyPlusMinus=true).'$'.$modPrice.')' : ''
				);
			}
			$selectField .= '</select></div>';
			return $selectField;
		}
	}
		
	public function hiddenTag($name=null, $val=null) {
		return sprintf('<input type="hidden" name="%s" value="%s" />',
			$name,
			$val
		);
	}

	/**
	 * @param Member $member
	 * @return boolean
	 */
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

	public function canPublish($member = null){
		return Permission::check('Product_CANCRUD');
	}

	public function providePermissions() {
		return array(
			'Product_CANCRUD' => 'Allow user to manage Products and related objects'
		);
	}
	
}

class ProductPage_Controller extends Page_Controller {

	private static $allowed_actions = array();

	public function init(){
		parent::init();

		if(SiteConfig::current_site_config()->CartPage==false){
			Requirements::css('//cdn.foxycart.com/static/scripts/colorbox/1.3.19/style1_fc/colorbox.css?ver=1');
			Requirements::javascript('//cdn.foxycart.com/' . FoxyCart::getFoxyCartStoreName() . '/foxycart.colorbox.js?ver=2');
		}

	}
}