<?php

namespace HMLB\DateBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType as BaseDateTimeType;

class DateTimeType extends BaseDateTimeType
{
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\DateTimeType';
    }

    public function getName()
    {
        return 'hmlb_datetime';
    }

    public function getBlockPrefix()
    {
        return 'hmlb_datetime';
    }
}
