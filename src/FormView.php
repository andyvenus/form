<?php
/**
 * User: Andy
 * Date: 16/01/2014
 * Time: 11:33
 */

namespace AV\Form;

use AV\Form\Exception\InvalidArgumentException;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class FormView
 * @package AV\Form
 *
 * Holds form data ready for the view and provides helper methods
 */
class FormView implements FormViewInterface
{
    /**
     * @var array
     */
    protected $fields = array();

    /**
     * @var array
     */
    protected $flatFields;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var String
     */
    protected $submitButtonLabel = 'Submit';

    /**
     * @var array
     */
    protected $errors;

    /**
     * @var array
     */
    protected $params = [
        'name' => null,
        'method' => 'POST',
        'action' => null
    ];

    /**
     * @var bool
     */
    protected $submitted;

    /**
     * @var array
     */
    protected $sections = [];

    /**
     * @var FormBlueprintInterface
     */
    protected $formBlueprint;

    /**
     * @var bool
     */
    protected $valid;

    /**
     * @var bool
     */
    protected $shouldShowSuccessMessage = false;

    /**
     * @param array $fields
     * @return mixed|void
     * @throws Exception\InvalidArgumentException
     */
    public function setFields(array $fields)
    {
        if (array_key_exists('params', $fields)) {
            throw new InvalidArgumentException("Your form cannot contain a field called 'params' as it clashes with internal functions");
        }

        $this->fields = $this->doFieldTranslations($fields);
    }

    public function setSections(array $sections)
    {
        foreach ($sections as $sectionId => $section) {
            $this->sections[$sectionId]['label'] = $this->translate($section['label']);
        }
    }

    public function getSections()
    {
        return $this->sections;
    }

    public function setFormBlueprint(FormBlueprintInterface $formBlueprint)
    {
        $this->formBlueprint = $formBlueprint;
    }

    public function getSuccessMessage()
    {
        return $this->formBlueprint->getSuccessMessage();
    }

    /**
     * @param bool $shouldShow
     */
    public function setShouldShowSuccessMessage($shouldShow)
    {
        $this->shouldShowSuccessMessage = $shouldShow;
    }

    /**
     * @return bool
     */
    public function shouldShowSuccessMessage()
    {
        return $this->shouldShowSuccessMessage;
    }

    protected function doFieldTranslations($fields) {
        $updatedFields = array();

        foreach ($fields as $fieldName => $field) {

            if (isset($field['options']['label'])) {
                $field['options']['label'] = $this->translate($field['options']['label']);
            }

            if (isset($field['options']['help'])) {
                $field['options']['help'] = $this->translate($field['options']['help']);
            }

            if (isset($field['options']['choices']) && (!isset($field['options']['choices_translate']) || $field['options']['choices_translate'] === true)) {
                foreach ($field['options']['choices'] as $value => $label) {
                    if (!is_array($label)) {
                        $field['options']['choices'][$value] = $this->translate($label);
                    }
                    else {
                        foreach ($label as $subValue => $subLabel) {
                            $field['options']['choices'][$value][$subValue] = $this->translate($subLabel);
                        }
                    }
                }
            }

            if (isset($field['fields'])) {
                $field['fields'] = $this->doFieldTranslations($field['fields']);
            }

            if (strpos($field['name'], '[]') === false) {
                $updatedFields[$fieldName] = $field;
            }
            else {
                $updatedFields[] = $field;
            }
        }

        return $updatedFields;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get fields for a certain section
     *
     * @param $section
     * @param bool $flatten
     * @return array
     */
    public function getSectionFields($section, $flatten = true)
    {
        if ($flatten === false) {
            $fields = $this->fields;
        } else {
            $fields = $this->getFlattenedFields();
        }

        $matchedFields = array();
        foreach ($fields as $fieldName => $field) {
            if (isset($field['options']['section']) && $field['options']['section'] == $section) {
                $matchedFields[$fieldName] = $field;
            }
        }

        return $matchedFields;
    }

    /**
     * Flatten all fields by moving collections into the main array
     *
     * @return array
     */
    public function getFlattenedFields()
    {
        if (!isset($this->flatFields)) {
            $this->flatFields = [];

            foreach ($this->fields as $field) {
                if (isset($field['fields']) && is_array($field['fields'])) {
                    $this->flatFields += $this->flattenCollection($field);
                } else {
                    $this->flatFields[$field['name']] = $field;
                }
            }
        }

        return $this->flatFields;
    }

    /**
     * Flatten the fields in a collection
     *
     * @param $field
     * @return array
     */
    protected function flattenCollection($field)
    {
        if (!isset($field['fields']) || !is_array($field['fields'])) {
            return [];
        }

        $fields = [];

        foreach ($field['fields'] as $field) {
            if (isset($field['fields']) && is_array($field['fields'])) {
                $fields += $this->flattenCollection($field);
            } else {
                $fields[$field['name']] = $field;
            }
        }

        return $fields;
    }

    /**
     * Get any fields that don't have a section set
     *
     * @return array
     */
    public function getFieldsWithoutSection()
    {
        $matchedFields = array();
        foreach ($this->fields as $fieldName => $field) {
            if (!isset($field['options']['section']) || $field['options']['section'] === null || $field['options']['section'] === '') {
                $matchedFields[$fieldName] = $field;
            }
        }

        return $matchedFields;
    }

    /**
     * {@inheritdoc}
     */
    public function setAction($url)
    {
        $this->params['action'] = $url;
    }

    public function getAction()
    {
        return (isset($this->params['action']) ? $this->params['action'] : null);
    }

    /**
     * {@inheritdoc}
     */
    public function setMethod($method)
    {
        $this->params['method'] = $method;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->params['name'] = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function setEncoding($encoding)
    {
        $this->params['encoding'] = $encoding;
    }

    public function getParams()
    {
        return $this->params;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubmitButtonLabel($label)
    {
        $this->submitButtonLabel = $label;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubmitButtonLabel()
    {
        return $this->translate($this->submitButtonLabel);
    }

    /**
     * @param $submitted bool
     */
    public function setSubmitted($submitted)
    {
        $this->submitted = $submitted;
    }

    /**
     * @return bool
     */
    public function isSubmitted()
    {
        return $this->submitted;
    }

    /**
     * @param $valid bool
     */
    public function setValid($valid)
    {
        $this->valid = $valid;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * Inject a translator to provide label translations
     *
     * @param TranslatorInterface|LegacyTranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface|LegacyTranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Translate a string if the translator has been injected
     *
     * @param $str string The string that will be translated
     * @param array $params
     * @return string
     */
    protected function translate($str, $params = array())
    {
        if (isset($this->translator)) {
            return $this->translator->trans($str, $params);
        }
        else {
            $finalParams = array();
            foreach ($params as $placeholder => $value) {
                $finalParams['{'.$placeholder.'}'] = $value;
            }
            return strtr($str, $finalParams);
        }
    }

    /**
     * @param $errors FormError[]
     */
    public function setErrors(array $errors)
    {
        foreach ($errors as $error) {
            if ($error->getTranslate() === true) {
                $params = $error->getTranslationParams();

                if (isset($params['field_label'])) {
                    $params['field_label'] = $this->translate($params['field_label']);
                }

                $error->setMessage($this->translate($error->getMessage(), $params));
            }
            $this->errors[] = $error;
        }
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return isset($this->errors);
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        if (isset($this->errors)) {
            return $this->errors;
        }
        else {
            return null;
        }
    }

    public function toArray()
    {
        $arrErrors = [];

        if (isset($this->errors)) {
            foreach ($this->errors as $error) {
                $arrErrors[] = $error->toArray();
            }
        }

        $data['errors'] = $arrErrors;
        $data['has_errors'] = $this->hasErrors();
        $data['success_message'] = $this->getSuccessMessage();

        return $data;
    }

    /**
     * @return mixed
     * @deprecated
     */
    public function getJsonResponseData()
    {
        return $this->toArray();
    }

    public function get($name)
    {
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        }
        else {
            return null;
        }
    }

    public function has($name)
    {
        if (isset($this->fields[$name])) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function __isset($name)
    {
        return $this->has($name);
    }
} 
