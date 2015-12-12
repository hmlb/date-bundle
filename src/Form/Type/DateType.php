<?php

namespace HMLB\DateBundle\Form\Type;

use IntlDateFormatter;
use Symfony\Component\Form\Extension\Core\Type\DateType as BaseDateType;

class DateType extends BaseDateType
{

    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\DateType';
    }

    public function getName()
    {
        return 'hmlb_date';
    }

    public function getBlockPrefix()
    {
        return 'hmlb_date';
    }
}
