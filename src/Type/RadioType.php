<?php
/**
 * User: Andy
 * Date: 25/03/2014
 * Time: 14:15
 */

namespace AV\Form\Type;

use AV\Form\FormHandler;

class RadioType extends DefaultType
{
    public function makeView($field, $allFormData, FormHandler $formHandler)
    {
        $field = parent::makeView($field, $allFormData, $formHandler);

        // Make sure that numerical values are always integers
        if (isset($field['value']) && is_numeric($field['value'])) {
            $field['value'] = intval($field['value']);
        }

        return $field;
    }

    public function isValidRequestData($field, $data)
    {
        if ((!isset($field['options']['strict']) || $field['options']['strict'] === false && !is_array($data)) && !isset($field['options']['choices'][$data])) {
            return false;
        }
        elseif ($data !== null) {
            return true;
        }
        else {
            return false;
        }
    }
}
