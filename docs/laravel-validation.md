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
