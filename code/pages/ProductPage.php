<?php
/**
 *
 * @package FoxyStripe
 *
 */

class ProductPage extends Page implements PermissionProvider {

	private static $allowed_children = 'none';
    private static $default_parent = 'ProductHolder';
    private static $can_be_root = false;

	private static $db = array(
		'Price' => 'Currency',
		'Weight' => 'Decimal',
		'Code' => 'Varchar(100)',
		'ReceiptTitle' => 'HTMLVarchar(255)',
		'Featured' => 'Boolean',
		'Available' => 'Boolean',
		'DiscountTitle' => 'Varchar(50)'
	);

	private static $has_one = array(
		'PreviewImage' => 'Image',
		'Category' => 'ProductCategory'
	);

	private static $has_many = array(
		'ProductImages' => 'ProductImage',
		'ProductOptions' => 'OptionItem',
        'OrderDetails' => 'OrderDetail',
		'ProductDiscountTiers' => 'ProductDiscountTier'
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

        $labels['Title'] = _t('ProductPage.TitleLabel', 'Name');
        $labels['Code'] = _t('ProductPage.CodeLabel', "Code");
        $labels['Price.Nice'] = _t('ProductPage.PriceLabel', 'Price');
        $labels['Featured.Nice'] = _t('ProductPage.NiceLabel', 'Featured');
        $labels['Available.Nice'] = _t('ProductPage.AvailableLabel', 'Available');
        $labels['Category.ID'] = _t('ProductPage.IDLabel', 'Category');
        $labels['Category.Title'] = _t('ProductPage.CategoryTitleLabel', 'Category');

        return $labels;
    }

	public function getCMSFields() {
		$fields = parent::getCMSFields();

        // allow extensions of ProductPage to override the PreviewImage field description
		$previewDescription = ($this->stat('customPreviewDescription')) ? $this->stat('customPreviewDescription') : _t('ProductPage.PreviewImageDescription', 'Image used throughout site to represent this product');

		// Cateogry Dropdown field w/ add new
		$source = function(){
		    return ProductCategory::get()->map()->toArray();
		};
		$catField = DropdownField::create('CategoryID', _t('ProductPage.Category', 'FoxyCart Category'), $source())
            ->setEmptyString('')
            ->setDescription(_t(
                'ProductPage.CategoryDescription',
                'Required, must also exist in
                    <a href="https://admin.foxycart.com/admin.php?ThisAction=ManageProductCategories" target="_blank">
                        FoxyCart Categories
                    </a>.
                    Used to set category specific options like shipping and taxes. Managed in
                        <a href="admin/settings">
                            Settings > FoxyStripe > Categories
                        </a>'
            ));
		if (class_exists('QuickAddNewExtension')) $catField->useAddNew('ProductCategory', $source);

		// Product Images gridfield
		$config = GridFieldConfig_RelationEditor::create();
		if (class_exists('GridFieldSortableRows')) $config->addComponent(new GridFieldSortableRows('SortOrder'));
		if (class_exists('GridFieldBulkImageUpload')) {
			$config->addComponent(new GridFieldBulkUpload());
			$config->getComponentByType('GridFieldBulkUpload')->setUfConfig('folderName', 'Uploads/ProductImages');
		}
		$prodImagesField = GridField::create(
            'ProductImages',
            _t('ProductPage.ProductImages', 'Images'),
            $this->ProductImages(),
            $config
        );

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
		$prodOptField = GridField::create(
            'ProductOptions',
            _t('ProductPage.ProductOptions', 'Options'),
            $products,
            $config
        );

		// Details tab
		$fields->addFieldsToTab('Root.Details', array(
			HeaderField::create('DetailHD', 'Product Details', 2),
			CheckboxField::create('Available')
				->setTitle(_t('ProductPage.Available', 'Available for purchase'))
                ->setDescription(_t(
                    'ProductPage.AvailableDescription',
                    'If unchecked, will remove "Add to Cart" form and instead display "Currently unavailable"'
                )),
            TextField::create('Code')
                ->setTitle(_t('ProductPage.Code', 'Product Code'))
                ->setDescription(_t(
                    'ProductPage.CodeDescription',
                    'Required, must be unique. Product identifier used by FoxyCart in transactions'
                )),
            $catField,
            CurrencyField::create('Price')
                ->setTitle(_t('ProductPage.Price', 'Price'))
                ->setDescription(_t(
                    'ProductPage.PriceDescription',
                    'Base price for this product. Can be modified using Product Options'
                )),
            NumericField::create('Weight')
                ->setTitle(_t('ProductPage.Weight', 'Weight'))
                ->setDescription(_t(
                    'ProductPage.WeightDescription',
                    'Base weight for this product in lbs. Can be modified using Product Options'
                )),
			CheckboxField::create('Featured')
				->setTitle(_t('ProductPage.Featured', 'Featured Product')),
            TextField::create('ReceiptTitle')
                ->setTitle(_t('ProductPage.ReceiptTitle', 'Product Title for Receipt'))
                ->setDescription(_t(
                        'ProductPage.ReceiptTitleDescription', 'Optional'
                ))
		));

		// Images tab
		$fields->addFieldsToTab('Root.Images', array(
			HeaderField::create('MainImageHD', _t('ProductPage.MainImageHD', 'Product Image'), 2),
			UploadField::create('PreviewImage', '')
				->setDescription($previewDescription)
				->setFolderName('Uploads/Products')
				->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'))
				->setAllowedMaxFileNumber(1),
			HeaderField::create('ProductImagesHD', _t('ProductPage.ProductImagesHD'. 'Product Image Gallery'), 2),
			$prodImagesField
				->setDescription(_t(
                        'ProductPage.ProductImagesDescription',
                        'Additional Product Images, shown in gallery on Product page'
                ))
		));

		// Options Tab
		$fields->addFieldsToTab('Root.Options', array(
			HeaderField::create('OptionsHD', _t('ProductPage.OptionsHD', 'Product Options'), 2),
			LiteralField::create('OptionsDescrip', _t(
                'Page.OptionsDescrip',
                '<p>Product Options allow products to be customized by attributes such as size or color.
                    Options can also modify the product\'s price, weight or code.</p>'
            )),
			$prodOptField
		));

		if(!$this->DiscountTitle && $this->ProductDiscountTiers()->exists()){
			$fields->addFieldTotab('Root.Discounts', new LiteralField("ProductDiscountHeaderWarning", "<p class=\"message warning\">A discount title is required for FoxyCart to properly parse the value. The discounts will not be applied until a title is entered.</p>"));
		}

		$fields->addFieldToTab('Root.Discounts', TextField::create('DiscountTitle')->setTitle(_t('Product.DiscountTitle','Discount Title')));
		$discountsConfig = GridFieldConfig_RelationEditor::create();
		$discountsConfig->removeComponentsByType('GridFieldAddExistingAutocompleter');
		$discountsConfig->removeComponentsByType('GridFieldDeleteAction');
		$discountsConfig->addComponent(new GridFieldDeleteAction(false));
		$discountGrid = GridField::create('ProductDiscountTiers', 'Product Discounts', $this->ProductDiscountTiers(), $discountsConfig);
		$fields->addFieldToTab('Root.Discounts', $discountGrid);

		if(FoxyCart::store_name_warning()!==null){
			$fields->addFieldToTab('Root.Main', LiteralField::create("StoreSubDomainHeaderWarning", _t(
                'ProductPage.StoreSubDomainHeaderWarning',
                "<p class=\"message error\">Store sub-domain must be entered in the <a href=\"/admin/settings/\">site settings</a></p>"
            )), 'Title');
		}

		// allows CMS fields to be extended
		$this->extend('updateCMSFields', $fields);

		return $fields;
	}

	public function onBeforeWrite(){

		if(!$this->CategoryID){
			$default = ProductCategory::get()->filter(array('Code' => 'DEFAULT'))->first();
			$this->CategoryID = $default->ID;
		}

		//update many_many lists when multi-group is on
		if(SiteConfig::current_site_config()->MultiGroup){
			$holders = $this->getManyManyComponents('ProductHolders');
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

		$title = ltrim($this->Title);
		$title = rtrim($title);
		$this->Title = $title;

		parent::onBeforeWrite();

	}

	public function onAfterWrite(){


		parent::onAfterWrite();

	}

	public function onBeforeDelete() {
		if(!$this->isPublished()) {

			$delete = function($object){
				$object->delete();
			};

			if($this->ProductOptions()) {
				$options = $this->getComponents('ProductOptions');
				$options->each($delete);
			}
			if($this->ProductImages()) {
				//delete product image dataobjects, not the images themselves.
				$images = $this->getComponents('ProductImages');
				$images->each($delete);
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

	public static function getGeneratedValue($productCode = null, $optionName = null, $optionValue = null, $method = 'name', $output = false, $urlEncode = false){
		$optionName = ($optionName !== null) ? preg_replace('/\s/','_', $optionName) : $optionName;
		return (SiteConfig::current_site_config()->CartValidation)
			? FoxyCart_Helper::fc_hash_value($productCode, $optionName, $optionValue, $method, $output, $urlEncode):
			$optionValue;
	}

	public function getDiscountFieldValue(){
		$tiers = $this->ProductDiscountTiers();
		$bulkString = '';
		foreach($tiers as $tier){
			$bulkString .= "|{$tier->Quantity}-{$tier->Percentage}";
		}
		return "{$this->Title}{allunits{$bulkString}}";
	}

	/**
	 * @param Member $member
	 * @return boolean
	 */
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

	private static $allowed_actions = array(
		'PurchaseForm'
	);

	public function init(){
		parent::init();
		Requirements::javascript("framework/thirdparty/jquery/jquery.js");
		if($this->data()->Available && $this->ProductOptions()->exists()){
			Requirements::javascript("foxystripe/javascript/outOfStock.min.js");
			Requirements::javascript("foxystripe/javascript/ProductOptions.min.js");
		}

		Requirements::customScript(<<<JS
		var productID = {$this->data()->ID};
JS
);
	}

	public function PurchaseForm() {

		$config = SiteConfig::current_site_config();

		$assignAvailable = function($self){
			$self->Available = ($self->getAvailability()) ? true : false;
		};

		$fields = FieldList::create();

		$data = $this->data();
		$hiddenTitle = ($data->ReceiptTitle) ? htmlspecialchars($data->ReceiptTitle) : htmlspecialchars($data->Title);
		$code = $data->Code;

		if($data->Available) {
			$fields->push(HiddenField::create(ProductPage::getGeneratedValue($code, 'name', $hiddenTitle))->setValue($hiddenTitle));
			$fields->push(HiddenField::create(ProductPage::getGeneratedValue($code, 'category', $data->Category()->Code))->setValue($data->Category()->Code));
			$fields->push(HiddenField::create(ProductPage::getGeneratedValue($code, 'code', $data->Code))->setValue($data->Code));
			$fields->push(HiddenField::create(ProductPage::getGeneratedValue($code, 'product_id', $data->ID))->setValue($data->ID));
			$fields->push(HiddenField::create(ProductPage::getGeneratedValue($code, 'price', $data->Price))->setValue($data->Price));//can't override id
			$fields->push(HiddenField::create(ProductPage::getGeneratedValue($code, 'weight', $data->Weight))->setValue($data->Weight));
			if ($this->DiscountTitle && $this->ProductDiscountTiers()->exists()) {
				$fields->push(HiddenField::create(ProductPage::getGeneratedValue($code, 'discount_quantity_percentage', $data->getDiscountFieldValue()))->setValue($data->getDiscountFieldValue()));
			}
			if ($this->PreviewImage()->Exists()) $fields->push(
				HiddenField::create(ProductPage::getGeneratedValue($code, 'image', $data->PreviewImage()->PaddedImage(80, 80)->absoluteURL))
					->setValue($data->PreviewImage()->PaddedImage(80, 80)->absoluteURL)
			);

			$options = $data->ProductOptions();
			$groupedOptions = new GroupedList($options);
			$groupedBy = $groupedOptions->groupBy('ProductOptionGroupID');

			$optionsSet = CompositeField::create();

			foreach($groupedBy as $id => $set){
				$group = OptionGroup::get()->byID($id);
				$title = $group->Title;
				$name = preg_replace('/\s/','_', $title);
				$set->each($assignAvailable);
				$disabled = array();
				$fullOptions = array();
				foreach($set as $item){
					$fullOptions[ProductPage::getGeneratedValue($data->Code, $group->Title, $item->getGeneratedValue(), 'value')] = $item->getGeneratedTitle();
					if(!$item->Availability) array_push($disabled, ProductPage::getGeneratedValue($data->Code, $group->Title, $item->getGeneratedValue(), 'value'));
				}
				$optionsSet->push(
					$dropdown = DropdownField::create($name, $title, $fullOptions)->setTitle($title)
				);
				$dropdown->setDisabledItems($disabled);
			}

			$optionsSet->addExtraClass('foxycartOptionsContainer');
			$fields->push($optionsSet);

			$quantityMax = ($config->MaxQuantity) ? $config->MaxQuantity : 10;
			$count = 1;
			$quantity = array();
			while ($count <= $quantityMax) {
				$countVal = ProductPage::getGeneratedValue($data->Code, 'quantity', $count, 'value');
				$quantity[$countVal] = $count;
				$count++;
			}

			$fields->push(DropdownField::create('quantity', 'Quantity', $quantity));

			$fields->push(HeaderField::create('submitPrice', '$' . $data->Price, 4));


			$actions = FieldList::create(
				$submit = FormAction::create(
					'',
					_t('ProductForm.AddToCart', 'Add to Cart')
				)
			);
			$submit->setAttribute('name', ProductPage::getGeneratedValue($code, 'Submit', _t('ProductForm.AddToCart', 'Add to Cart')));
			if(!$config->StoreName || $config->StoreName == '' || !isset($config->StoreName)){
				$submit->setAttribute('Disabled', true);
			}
			$this->extend('updatePurchaseFormFields', $fields);
		}else{
			$fields->push(HeaderField::create('submitPrice', 'Currently Out of Stock'), 4);
			$actions = FieldList::create();
		}

		$form = Form::create($this, 'PurchaseForm', $fields, $actions);
		$form->setAttribute('action',FoxyCart::FormActionURL());
		$form->disableSecurityToken();

		$this->extend('updatePurchaseForm', $form);

		return $form;
	}
}
