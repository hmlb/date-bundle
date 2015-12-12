<?php

namespace HMLB\DateBundle\Form\Type;

use DateTime;
use HMLB\Date\Date;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType as BaseDateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DateTimeType extends BaseDateTimeType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $e) {
                $form = $e->getForm();
                $data = $form->getData();
                if ($data instanceof DateTime) {
                    $data = Date::instance($data);
                    $form->setData($data);
                }
            }
        );
    }

    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\DateTimeType';
    }

    public function getName()
    {
        return 'hmlb_datetime';
    }
}
