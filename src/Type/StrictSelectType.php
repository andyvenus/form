<?php
/**
 * User: Andy
 * Date: 10/09/2014
 * Time: 13:59
 */

namespace AV\Form\Type;

class StrictSelectType extends SelectType
{
    public function getDefaultOptions($field)
    {
        if (!isset($field['options']['strict'])) {
            $field['options']['strict'] = true;
        }

        return parent::getDefaultOptions($field);
    }
}
