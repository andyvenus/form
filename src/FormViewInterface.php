<?php
/**
 * User: Andy
 * Date: 18/01/2014
 * Time: 16:15
 */
namespace AV\Form;

/**
 * Interface FormViewInterface
 * @package AV\FormBlueprint
 *
 * Interface that the view can use to get form data
 */
interface FormViewInterface
{
    /**
     * @param FormBlueprintInterface $formBlueprint
     */
    public function setFormBlueprint(FormBlueprintInterface $formBlueprint);

    /**
     * Set the form fields
     *
     * @param array $fields
     */
    public function setFields(array $fields);

    /**
     * Set the form sections
     *
     * @param array $sections
     */
    public function setSections(array $sections);

    /**
     * @return array
     */
    public function getFields();

    /**
     * @param $section
     * @return array
     */
    public function getSectionFields($section);

    /**
     * @return array
     */
    public function getSections();

    /**
     * Set the URL the form submits to
     *
     * @param string $url
     */
    public function setAction($url);

    /**
     * Set the form submit method (either POST or GET)
     *
     * @param string $method
     */
    public function setMethod($method);


    /**
     * @param $name
     */
    public function setName($name);

    /**
     * @param $encoding
     */
    public function setEncoding($encoding);

    /**
     * @return array
     */
    public function getParams();

    /**
     * Set the label for the submit button
     *
     * @param $label
     */
    public function setSubmitButtonLabel($label);

    /**
     * Get the submit button label
     *
     * @return string
     */
    public function getSubmitButtonLabel();

    /**
     * @return string|null
     */
    public function getSuccessMessage();

    /**
     * @param $submitted
     */
    public function setSubmitted($submitted);

    /**
     * @return bool
     */
    public function isSubmitted();

    /**
     * @param $valid
     */
    public function setValid($valid);

    /**
     * @return bool
     */
    public function isValid();

    /**
     * @param array $errors
     */
    public function setErrors(array $errors);

    /**
     * @return bool
     */
    public function hasErrors();

    /**
     * @return array|null
     */
    public function getErrors();

    /**
     * @param bool $shouldShow
     */
    public function setShouldShowSuccessMessage($shouldShow);

    /**
     * @return bool;
     */
    public function shouldShowSuccessMessage();

    /**
     * Get the form information in array format that can be returned as JSON
     *
     * @return mixed
     */
    public function toArray();

    /**
     * Check if a form field is set (magic method)
     *
     * @param $name
     * @return mixed
     */
    public function __isset($name);

    /**
     * Get a form field (returns null if not set)
     *
     * @param $name
     * @return mixed
     */
    public function __get($name);
}
