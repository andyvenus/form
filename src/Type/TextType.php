<?php
/**
 * User: Andy
 * Date: 15/05/15
 * Time: 22:40
 */

namespace AV\Form\Type;

class TextType extends DefaultType
{
    public function processRequestData($field, $data)
    {
        if (isset($field['options']['trim'])) {
            $trimType = $field['options']['trim'];

            if ($trimType === true || $trimType === 'trim') {
                return trim($data);
            }

            if ($trimType === 'rtrim') {
                return rtrim($data);
            }

            if ($trimType === 'ltrim') {
                return ltrim($data);
            }
        }

        return (isset($field['options']['no_trim']) && $field['options']['no_trim'] == true) ? $data : trim($data);
    }
}
