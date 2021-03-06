<?php
/**
 * User: Andy
 * Date: 10/09/2014
 * Time: 13:59
 */

namespace AV\Form\Type;

use AV\Form\ChoicesProviderInterface;

class SelectType extends DefaultType
{
    public function allowUnsetRequest($field)
    {
        if (empty($field['options']['choices'])) {
            return true;
        }

        return parent::allowUnsetRequest($field);
    }

    public function isValidRequestData($field, $data)
    {
        if (is_array($data) && !isset($field['options']['attr']['multiple'])) {
            return false;
        }
        elseif (isset($field['options']['strict']) && $field['options']['strict'] === true) {
            if (!is_array($data) && !isset($field['options']['choices_flat'][$data])) {
                return false;
            }
            if (is_array($data)) {
                foreach ($data as $val) {
                    if (!isset($val, $field['options']['choices_flat'][$val])) {
                        return false;
                    }
                }
            }

            return true;
        }
        elseif ($data !== null) {
            return true;
        }
        else {
            return false;
        }
    }

    public function getDefaultOptions($field)
    {
        if (!isset($field['options']['choices'])) {
            $field['options']['choices'] = [];
        }

        if (isset($field['options']['choices_provider'])) {
            if (is_object($field['options']['choices_provider']) && $field['options']['choices_provider'] instanceof ChoicesProviderInterface) {
                $field['options']['choices'] = array_replace_recursive($field['options']['choices'], $field['options']['choices_provider']->getChoices());
            }
            elseif (isset($field['options']['choices_provider']['class']) && class_exists($field['options']['choices_provider']['class'])) {
                $choicesProvider = new $field['options']['choices_provider']['class']();
                $field['options']['choices'] = array_replace_recursive($field['options']['choices'], call_user_func(array($choicesProvider, 'getChoices')));
            }
        }

        foreach ($field['options']['choices'] as $choiceId => $choiceOrGroup) {
            if (is_array($choiceOrGroup)) {
                foreach ($choiceOrGroup as $value => $label) {
                    $field['options']['choices_flat'][$value] = $label;
                }
            } else {
                $field['options']['choices_flat'][$choiceId] = $choiceOrGroup;
            }
        }

        return $field;
    }
}
