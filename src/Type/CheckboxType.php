<?php
/**
 * User: Andy
 * Date: 25/03/2014
 * Time: 14:15
 */

namespace AV\Form\Type;

use AV\Form\FormHandler;

class CheckboxType implements TypeInterface
{
    public function getDefaultOptions($field)
    {
        $defaults = array(
            'checked_value' => 1,
            'unchecked_value' => 0,
            'checked' => false
        );

        $field['options'] = array_merge($defaults, $field['options']);

        return $field;
    }

    public function allowUnsetRequest($field)
    {
        // Checkboxes are always allowed to be unset as that's how they work
        return true;
    }

    public function getUnsetRequestData($field)
    {
        return $field['options']['unchecked_value'];
    }

    public function isValidRequestData($field, $data)
    {
        return ($field['options']['checked_value'] == $data);
    }

    public function processRequestData($field, $data)
    {
        return (isset($field['options']['checked_value']) ? $field['options']['checked_value'] : 1);
    }

    public function makeView($field, $allFormData, FormHandler $formHandler)
    {
        $field['value'] = (isset($field['options']['checked_value']) ? $field['options']['checked_value'] : 1);

        if (isset($allFormData[$field['name']]) && $allFormData[$field['name']] == $field['options']['checked_value']) {
            $field['options']['checked'] = true;
        }
        else {
            $field['options']['checked'] = false;
        }

        if (isset($field['original_name'])) {
            $field['name'] = $field['original_name'];
        }

        return $field;
    }
}