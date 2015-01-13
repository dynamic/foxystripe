<?php
/**
 *
 * @package FoxyStripe
 *
 */

class ProductHolder extends Page {
	
	private static $allowed_children = array('ProductHolder', 'ProductPage');
	
	private static $db = array(
		
	);
	
	private static $has_one = array(
		'PreviewImage' => 'Image'
	);

	private static $many_many = array(
		'Products' => 'ProductPage'
	);

	private static $many_many_extraFields = array(
		'Products' => array(
			'SortOrder' => 'Int'
		)
	);

    private static $singular_name = 'Product Group';
    private static $plural_name = 'Product Groups';
    private static $description = 'Display a list of related products';
	
	private static $defaults = array(
		
	);
	
	public function getCMSFields(){
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Image', new UploadField('PreviewImage', 'Preview Image'));

		if(SiteConfig::current_site_config()->MultiGroup){
			$config = GridFieldConfig_RelationEditor::create();
			if(class_exists('GridFieldSortableRows')){
				$config->addComponent(new GridFieldSortableRows('SortOrder'));
			}
			if(class_exists('GridFieldManyRelationHandler')){
				$config->removeComponentsByType('GridFieldAddExistingAutocompleter');
				$config->addComponent(new GridFieldManyRelationHandler());
			}
			$fields->addFieldToTab(
				'Root.Products',
				new GridField(
					'Products',
					'Products',
					$this->Products(),
					$config
				)
			);
		}

		$this->extend('updateCMSFields', $fields);
		return $fields;
	}

	public function Products() {
		return $this->getManyManyComponents('Products')->sort('SortOrder');
	}

	/**
	 * loadDescendantProductGroupIDListInto function.
	 * 
	 * @access public
	 * @param mixed &$idList
	 * @return void
	 */
	public function loadDescendantProductGroupIDListInto(&$idList) {
		if ($children = $this->AllChildren()) {
			foreach($children as $child) {
				if(in_array($child->ID, $idList)) continue;
				
				if($child instanceof ProductHolder) {
					$idList[] = $child->ID; 
					$child->loadDescendantProductGroupIDListInto($idList);
				}                             
			}
		}
	}
	
	/**
	 * ProductGroupIDs function.
	 * 
	 * @access public
	 * @return void
	 */
	public function ProductGroupIDs() {
		$holderIDs = array();
		$this->loadDescendantProductGroupIDListInto($holderIDs);
		return $holderIDs;
	}
	
	/**
	 * Products function.
	 * 
	 * @access public
	 * @return void
	 */
	public function ProductList($limit = 10) {

		$config = SiteConfig::current_site_config();

		if($config->ProductLimit>0){
			$limit = $config->ProductLimit;
		}

		if($config->MultiGroup){
			$entries = $this->Products()->sort('SortOrder');
		}else{
			$filter = '"ParentID" = ' . $this->ID;

			// Build a list of all IDs for ProductGroups that are children
			$holderIDs = $this->ProductGroupIDs();

			// If no ProductHolders, no ProductPages. So return false
			if($holderIDs) {
				// Otherwise, do the actual query
				if($filter) $filter .= ' OR ';
				$filter .= '"ParentID" IN (' . implode(',', $holderIDs) . ")";
			}

			$order = '"SiteTree"."Title" ASC';

			$entries = ProductPage::get()->where($filter);
		}


    	$list = new PaginatedList($entries, Controller::curr()->request);
    	$list->setPageLength($limit);
    	return $list;
		
	}
}

class ProductHolder_Controller extends Page_Controller {
	
	public function init(){
		parent::init();

	}
}