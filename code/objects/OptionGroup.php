<?php

/**
 * Class OptionGroup
 * @package foxystripe
 */
class OptionGroup extends DataObject
{

    /**
     * @var array
     */
    private static $db = array(
        'Title' => 'Varchar(100)'
    );

    /**
     * @var string
     */
    private static $singular_name = 'Product Option Group';

    /**
     * @var string
     */
    private static $plural_name = 'Product Option Groups';

    /**
     * @var string
     */
    private static $description = 'Groups of product options, e.g. size, color, etc';

    /**
     * @return FieldList
     */
    function getCMSFields()
    {

        $fields = parent::getCMSFields();

        $this->extend('getCMSFields', $fields);

        return $fields;
    }

    /**
     * @throws ValidationException
     * @throws null
     */
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

    /**
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return new RequiredFields(array('Title'));
    }

    /**
     * @return ValidationResult
     */
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

    /**
     * @throws ValidationException
     * @throws null
     */
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

    /**
     * @param bool|false $member
     * @return bool
     */
    public function canView($member = false)
    {
        return true;
    }

    /**
     * @param null $member
     * @return bool|int
     */
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

    /**
     * @param null $member
     * @return bool|int
     */
    public function canDelete($member = null)
    {
        return $this->canEdit();
    }

    /**
     * @param null $member
     * @return bool|int
     */
    public function canCreate($member = null)
    {
        return Permission::check('Product_CANCRUD');
    }

}
