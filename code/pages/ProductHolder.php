<?php
/**
 *
 * @package FoxyStripe
 *
 */

class ProductHolder extends Page {
	public static $allowed_children = array('ProductHolder', 'ProductPage', 'Page');
	public static $db = array(
		
	);
	
	public static $has_one = array(
		'PreviewImage' => 'Image'
	);
	static $defaults = array(
		
	);
	public function getCMSFields(){
		$fields = parent::getCMSFields();
		
		$fields->addFieldToTab('Root.Image', new UploadField('PreviewImage', 'Preview Image'));
		return $fields;
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
	public function Products() {
	
		$filter = '"ParentID" = ' . $this->ID;
		$limit = 10;
		
		// Build a list of all IDs for ProductGroups that are children
		$holderIDs = $this->ProductGroupIDs();
		
		// If no ProductHolders, no ProductPages. So return false
		if($holderIDs) {
			// Otherwise, do the actual query
			if($filter) $filter .= ' OR ';
			$filter .= '"ParentID" IN (' . implode(',', $holderIDs) . ")";
		}
		
		$order = '"SiteTree"."Title" ASC';

		$entries = ProductPage::get()->where($filter)->sort($order);

    	$list = new PaginatedList($entries, Controller::curr()->request);
    	$list->setPageLength($limit);
    	return $list;
		
	}
}

class ProductHolder_Controller extends Page_Controller {
	public function init(){
		parent::init();
		
		Requirements::css('themes/ss-bootstrap_foxystripe/css/foxystripe.css');
		//Requirements::css('FoxyStripe/css/foxycart.css');
		//Requirements::customScript("window.jQuery || document.write('<script src=\'//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js\'><\/script>');");
	}
}