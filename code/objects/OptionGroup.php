<?php
/**
 *
 * @package FoxyStripe
 *
 */

class OptionGroup extends DataObject
{

    private static $db = array(
        'Title' => 'Varchar(100)'
    );

    private static $singular_name = 'Product Option Group';
    private static $plural_name = 'Product Option Groups';
    private static $description = 'Groups of product options, e.g. size, color, etc';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $this->extend('getCMSFields', $fields);

        return $fields;
    }

    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        // create a catch-all group
        if (!OptionGroup::get()->filter(array('Title' => 'Options'))->first()) {
            $do = new OptionGroup();
            $do->Title = "Options";
            $do->write();
        }
        if (!OptionGroup::get()->filter(array('Title' => 'Size'))->first()) {
            $do = new OptionGroup();
            $do->Title = "Size";
            $do->write();
        }
        if (!OptionGroup::get()->filter(array('Title' => 'Color'))->first()) {
            $do = new OptionGroup();
            $do->Title = "Color";
            $do->write();
        }
        if (!OptionGroup::get()->filter(array('Title' => 'Type'))->first()) {
            $do = new OptionGroup();
            $do->Title = "Type";
            $do->write();
        }
    }

    public function getCMSValidator()
    {
        return new RequiredFields(array('Title'));
    }

    public function validate()
    {
        $result = parent::validate();

        $title = $this->Title;
        $firstChar = substr($title, 0, 1);
        if (preg_match('/[^a-zA-Z]/', $firstChar)) {
            $result->error('The first character of the Title can only be a letter', 'bad');
        }
        if (preg_match('/[^a-zA-Z]\s/', $title)) {
            $result->error('Please only use letters, numbers and spaces in the title', 'bad');
        }

        return $result;
    }

    public function onBeforeDelete()
    {
        parent::onBeforeDelete();

        //make sure that if we delete this option group, we reassign the group's option items to the 'None' group.
        $items = OptionItem::get()->filter(array('ProductOptionGroupID' => $this->ID));

        if (isset($items)) {
            $noneGroup = OptionGroup::get()->filter(array('Title' => 'Options'))->first();
            foreach ($items as $item) {
                $item->ProductOptionGroupID = $noneGroup->ID;
                $item->write();
            }
        }
    }

    public function canView($member = false)
    {
        return true;
    }

    public function canEdit($member = null)
    {
        switch ($this->Title) {
            case 'Options':
                return false;
                break;
            default:
                return Permission::check('Product_CANCRUD');
                break;
        }
    }

    public function canDelete($member = null)
    {
        return $this->canEdit();
    }

    public function canCreate($member = null)
    {
        return Permission::check('Product_CANCRUD');
    }
}
