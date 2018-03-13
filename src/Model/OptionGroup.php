<?php

namespace Dynamic\FoxyStripe\Model;

use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\Permission;

/**
 * Class OptionGroup
 * @package Dynamic\FoxyStripe\Model
 *
 * @property \SilverStripe\ORM\FieldType\DBVarchar Title
 *
 * @method \SilverStripe\ORM\HasManyList Options
 */
class OptionGroup extends DataObject
{
    /**
     * @var array
     */
    private static $db = array(
        'Title' => 'Varchar(100)',
    );

    /**
     * @var array
     */
    private static $has_many = array(
        'Options' => OptionItem::class,
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
     * @var string
     */
    private static $table_name = 'FS_OptionGroup';

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        // create a catch-all group
        if (!self::get()->filter(array('Title' => 'Options'))->first()) {
            $do = new self();
            $do->Title = 'Options';
            $do->write();
        }
        if (!self::get()->filter(array('Title' => 'Size'))->first()) {
            $do = new self();
            $do->Title = 'Size';
            $do->write();
        }
        if (!self::get()->filter(array('Title' => 'Color'))->first()) {
            $do = new self();
            $do->Title = 'Color';
            $do->write();
        }
        if (!self::get()->filter(array('Title' => 'Type'))->first()) {
            $do = new self();
            $do->Title = 'Type';
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
            $result->addError('The first character of the Title can only be a letter', 'bad');
        }
        if (preg_match('/[^a-zA-Z]\s/', $title)) {
            $result->addError('Please only use letters, numbers and spaces in the title', 'bad');
        }

        return $result;
    }

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function onBeforeDelete()
    {
        parent::onBeforeDelete();

        //make sure that if we delete this option group, we reassign the group's option items to the 'None' group.
        $items = OptionItem::get()->filter(array('ProductOptionGroupID' => $this->ID));

        if (isset($items)) {
            if ($noneGroup = self::get()->filter(array('Title' => 'Options'))->first()) {
                /** @var OptionItem $item */
                foreach ($items as $item) {
                    $item->ProductOptionGroupID = $noneGroup->ID;
                    $item->write();
                }
            }
        }
    }

    /**
     * @param bool $member
     *
     * @return bool
     */
    public function canView($member = null)
    {
        return true;
    }

    /**
     * @param null $member
     *
     * @return bool|int
     */
    public function canEdit($member = null)
    {
        switch ($this->Title) {
            case 'Options':
                return false;
                break;
            default:
                return Permission::check('Product_CANCRUD', 'any', $member);
                break;
        }
    }

    /**
     * @param null $member
     *
     * @return bool|int
     */
    public function canDelete($member = null)
    {
        return $this->canEdit($member);
    }

    /**
     * @param null $member
     * @param array $context
     *
     * @return bool|int
     */
    public function canCreate($member = null, $context = [])
    {
        return Permission::check('Product_CANCRUD', 'any', $member);
    }
}
