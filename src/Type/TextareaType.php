<?php

namespace AV\Form\Type;

class TextareaType extends DefaultType
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

        return $data;
    }
}
