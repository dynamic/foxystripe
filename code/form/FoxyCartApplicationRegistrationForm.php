<?php

/**
 * Class FoxyCartApplicationRegistrationForm
 */
class FoxyCartApplicationRegistrationForm extends Form
{

    /**
     * FoxyCartApplicationRegistrationForm constructor.
     * @param Controller $controller
     * @param string $name
     */
    public function __construct(Controller $controller, $name)
    {

        $fields = FieldList::create(
            HiddenField::create('act')
                ->setValue('create_client'),
            TextField::create('project_name')
                ->setTitle('Project Name')
                ->setAttribute('maxlength', 200),
            TextField::create('project_description')
                ->setTitle('Project Description')
                ->setAttribute('maxlength', 200),
            TextField::create('company_url')
                ->setTitle('Company URL')
                ->setAttribute('maxlength', 200),
            TextField::create('contact_name')
                ->setTitle('Contact Name'),
            EmailField::create('contact_email')
                ->setTitle('Contact Email'),
            PhoneNumberField::create('contact_phone')
                ->setTitle('Contact Phone')
        );

        $actions = FieldList::create(
            FormAction::create('doFoxyCartApplicationRegistration')
                ->setTitle('Register Application')
        );

        $validator = RequiredFields::create(
            'project_name',
            'project_description',
            'company_url',
            'contact_name',
            'contact_email',
            'contact_phone'
        );

        parent::__construct($controller, $name, $fields, $actions, $validator);
    }

}