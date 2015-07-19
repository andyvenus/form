# Field options

This page lists all the different field types and their options

## All Fields

Most fields share these common options

#### default

The default value of the field

#### label

The label displayed next to the field

#### help

Any useful information you want to display next to the field. Can contain HTML.

#### required

If set to true, the field must not be left blank by the user and an asterisk will be shown next to the label.

#### allow_unset

Forms work out if they were submitted by the existence of the form data in the GET or POST request. If a field is missing from the request, the form will not consider itself submitted. If `allow_unset` is `true`, that field can be missing from the submitted data. This is specifically for allowing fields to be outright missing from the request, fields simply left blank by the user are always considered submitted.

Be warned, if your form is made out of entirely of fields that can be unset, there'll be no way for the form to know if it's submitted. Consider adding a hidden field if you want to allow all other fields to be unset.

#### attr

This is an array of HTML attributes to give to the field. For example, if you want to set an ID on a form field you can do it like so:

    'attr' => [
        'id' => 'important'
    ]

## Text

    $form->add('field_name', 'text', [
        'label' => 'My Label'
    ]);
    
#### no_trim

By default text fields (and only text fields) will pass submitted values through PHP's `trim()` function to remove whitespace. Set `no_trim` to `true` to disable this on a field.

## Password

    $form->add('field_name', 'password', [
        'label' => 'My Label'
    ]);

## Hidden

    $form->add('field_name', 'hidden');

## Textarea

    $form->add('field_name', 'textarea', [
        'label' => 'My Label'
    ]);

## File

    $form->add('field_name', 'file', [
        'label' => 'My Label'
    ]);

## Select

    $form->add('field_name', 'select', [
        'label' => 'My Label',
        'choices' => [
            'value' => 'Label',
            'second' => 'Another Choice'
        ]
    ]);
    
#### choices

An array of choices for the select field. The above shows the basic key/value array of choices. You can also create groups like so:

    $form->add('field_name', 'select', [
        'label' => 'My Label',
        'choices' => [
            'Group Label' => [
                'value' => 'Label',
                'second' => 'Another Choice'
            ],
            'Group Two' => [
                'another' => 'Another',
            ]
        ]
    ]);

## Radio Group

    $form->add('field_name', 'radio', [
        'label' => 'My Label',
        'choices' => [
            'value' => 'Label',
            'second' => 'Another Choice'
        ]
    ]);
        
#### choices

An array of different choices for the radio group

## Checkbox

    $form->add('field_name', 'radio', [
        'label' => 'My Label',
        'choices' => [
            'checked_value' => 'yes',
            'unchecked_value' => 'no',
            'default' => 'yes'
        ]
    ]);
    
Checkboxes work a little differently to other field types because when unchecked they don't send anything back to the server. So this means that `allow_unset` defaults to `true` and cannot be changed.

As receiving nothing is rarely useful, AV Forms allow you to set a 'checked' value and an 'unchecked' value.

#### checked_value

If the checkbox was ticked, this is the value you will be get from the form. This will also be set on any entities/models. Defaults to 1.

#### unchecked_value

If the checkbox was *not* ticked by the user, this is the value you will be get from the form. This will also be set on any entities/models. Defaults to 0.
