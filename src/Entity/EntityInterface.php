<?php
/**
 * User: Andy
 * Date: 10/04/2018
 * Time: 10:48
 */

namespace AV\Form\Entity;

interface EntityInterface
{
    /**
     * @param array $formData An array of form Values
     * @param null|array $limitFields
     */
    public function setFormData($formData, $limitFields = null);

    /**
     * Get an array of data from an entity
     *
     * @param array $formParameters
     * @param null $limitFields
     * @return array
     */
    public function getFormData(array $formParameters, $limitFields = null);
}
