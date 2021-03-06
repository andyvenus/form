<?php
/**
 * User: Andy
 * Date: 21/01/2014
 * Time: 11:37
 */
namespace AV\Form\EntityProcessor;

/**
 * Interface EntityProcessor
 * @package AV\Form\EntityProcessor
 */
interface EntityProcessorInterface
{
    /**
     * Save an array of form data to an entity
     *
     * @param $entity
     * @param $formData
     * @param null $limitFields
     * @return mixed
     */
    public function saveToEntity($entity, $formData, $limitFields = null);

    /**
     * Get an array of data from an entity
     *
     * @param $entity
     * @param array $formParameters
     * @param null $limitFields
     * @return mixed
     */
    public function getFromEntity($entity, array $formParameters, $limitFields = null);
}