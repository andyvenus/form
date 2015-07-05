## Creating a form blueprint

All forms must be built using the `AV\Form\FormBlueprint` class.

You can create a new instance of FormBlueprint anywhere and start adding fields.

    use AV\Form\FormBlueprint;

    $blueprint = new FormBlueprint();
    
    $blueprint->add('field_name', 'text', ['label' => 'My Field']);

This is fine, but it's going to clutter up your controllers and mean the form cannot be reused. So the recommended way to build a form is to instead extend the FormBlueprint class and have it build itself in the constructor.

    use AV\Form\FormBlueprint;
    
    class MyForm extends FormBlueprint
    {
        public function __construct()
        {
            $this->add('field_name', 'text');
            
            // and so on...
        }
    }

## Useful Methods

### setAction($url)

Set the URL a form will submit to. By default, forms submit to the page they are displayed on.

### setMethod($method)

Set the form method (either GET or POST). Defaults to POST.

### setName($name)

Set the name of the form

### add($name, $type, array $options)

Adds a field to the FormBlueprint

### addBefore/addAfter($offset, $name, $type, array $options)

Adds a field before/after an existing field in the form

### replace($name, $type, array $options)

Replace an existing field

### remove($name)

Remove a field

### has($name)

Check if a field with a given name exists

### setSuccessMessage($message)

Set the message to give when a valid form was successfully submitted
