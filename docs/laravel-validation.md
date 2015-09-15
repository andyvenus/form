# Laravel Validation

AV Forms integrate with Laravel's [validator](http://laravel.com/docs/5.1/validation component)

## Adding validation to a field

Validation is easily added to any form field via the `validation` option when creating a field

    $blueprint = new FormBlueprint();
    
    $blueprint->add('website_url', 'text', [
        'label' => 'A label',
        'validation' => 'max:100|url'
    ]);
    
## Adding validation to a model

You can also set validation on a model that will be combined with any form validation. To do this, add a `getValidationRules` method to your model that return an array of validation data just like is shown in the laravel validation documentation.

    // within your eloquent model
    public function getValidationRules()
    {
        return [
            'my_database_field' => 'max:30',
            'example_url_field' => 'url'
        ];
    }

## Adding a custom error message to the form

If you want to set a custom error message on the form, you can do so with the addCustomErrors method on the form handler:

    use AV\Form\FormBlueprint;
    use AV\Form\FormError;
    
    $blueprint = new FormBlueprint();
        
    $blueprint->add('website_url', 'text', [
        'label' => 'A label',
    ]);
    
    $form = Form::build($blueprint);
    
    if ($form->isSubmitted()) {
        if ($form->getData('website_url') != 'http://www.google.com') {
            $form->addCustomErrors([new FormError('website_url', 'Website URL must be http://www.google.com')]);
        }
    }
    
    // if ($form->isValid()) ... etc

The first parameter of the FormError class is the field name related to the error and the second parameter is the error you want to display. When you add a custom error, the form is considered to have validation errors and `isValid` will return false.
