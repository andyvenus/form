<?php
/**
 * User: Andy
 * Date: 14/08/2014
 * Time: 21:39
 */

namespace AV\Form;

interface ChoicesProviderInterface
{
    /**
     * @return array
     */
    public function getChoices();
}