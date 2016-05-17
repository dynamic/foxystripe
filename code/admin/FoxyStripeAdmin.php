<?php

class FoxyStripeAdmin extends LeftAndMain
{

    /**
     * @var string
     */
    private static $url_segment = 'foxystripe-config';

    /**
     * @var string
     */
    private static $url_rule = '/$Action/$ID/$OtherID';

    /**
     * @var int
     */
    private static $menu_priority = -1;

    /**
     * @var string
     */
    private static $menu_title = 'FoxyStripe Store';

    /**
     * @var string
     */
    private static $tree_class = 'FS_Store';

    /**
     * @var array
     */
    private static $required_permission_codes = array('EDIT_FSPERMISSION');


    /**
     * @param null $id Not used.
     * @param null $fields Not used.
     *
     * @return Form
     */
    public function getEditForm($id = null, $fields = null)
    {
        $fsConfig = FoxyStripeConfig::current_foxystripe_config();
        $fields = $fsConfig->getCMSFields();

        // Tell the CMS what URL the preview should show
        $home = Director::absoluteBaseURL();
        $fields->push(new HiddenField('PreviewURL', 'Preview URL', $home));

        // Added in-line to the form, but plucked into different view by LeftAndMain.Preview.js upon load
        $fields->push($navField = new LiteralField('SilverStripeNavigator', $this->getSilverStripeNavigator()));
        $navField->setAllowHTML(true);

        // Retrieve validator, if one has been setup (e.g. via data extensions).
        if ($fsConfig->hasMethod("getCMSValidator")) {
            $validator = $fsConfig->getCMSValidator();
        } else {
            $validator = null;
        }

        $actions = $fsConfig->getCMSActions();
        $form = CMSForm::create(
            $this, 'EditForm', $fields, $actions, $validator
        )->setHTMLID('Form_EditForm');
        $form->setResponseNegotiator($this->getResponseNegotiator());
        $form->addExtraClass('cms-content center cms-edit-form');
        $form->setAttribute('data-pjax-fragment', 'CurrentForm');

        if ($form->Fields()->hasTabset()) {
            $form->Fields()->findOrMakeTab('Root')->setTemplate('CMSTabSet');
        }
        $form->setHTMLID('Form_EditForm');
        $form->loadDataFrom($fsConfig);
        $form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));

        // Use <button> to allow full jQuery UI styling
        $actions = $actions->dataFields();
        if ($actions) {
            foreach ($actions as $action) {
                $action->setUseButtonTag(true);
            }
        }

        $this->extend('updateEditForm', $form);

        return $form;
    }

    /**
     * Used for preview controls, mainly links which switch between different states of the page.
     *
     * @return ArrayData
     */
    public function getSilverStripeNavigator()
    {
        return $this->renderWith('CMSSettingsController_SilverStripeNavigator');
    }

    /**
     * Save the current sites {@link SiteConfig} into the database.
     *
     * @param array $data
     * @param Form $form
     * @return String
     */
    public function save_foxystripeconfig($data, $form)
    {
        $fsConfig = FoxyStripeConfig::current_foxystripe_config();
        $form->saveInto($fsConfig);

        try {
            $fsConfig->write();
        } catch (ValidationException $ex) {
            $form->sessionMessage($ex->getResult()->message(), 'bad');
            return $this->getResponseNegotiator()->respond($this->request);
        }

        $this->response->addHeader('X-Status', rawurlencode(_t('LeftAndMain.SAVEDUP', 'Saved.')));

        return $form->forTemplate();
    }


    public function Breadcrumbs($unlinked = false)
    {
        $defaultTitle = self::menu_title_for_class(get_class($this));

        return new ArrayList(array(
            new ArrayData(array(
                'Title' => _t("{$this->class}.MENUTITLE", $defaultTitle),
                'Link' => $this->Link()
            ))
        ));
    }
    
}