<?php

namespace HMLB\DateBundle\Request\ParamConverter;

use Exception;
use HMLB\Date\Date;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Converts request params into Date objects.
 *
 * @author Hugues Maignol <hugues.maignol@kitpages.fr>
 */
class DateParamConverter implements ParamConverterInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws NotFoundHttpException When invalid date given
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $param = $configuration->getName();
        if (!$request->attributes->has($param)) {
            return false;
        }
        $options = $configuration->getOptions();
        $value = $request->attributes->get($param);
        if (!$value && $configuration->isOptional()) {
            return false;
        }
        $invalidDateMessage = 'Invalid date given.';
        try {
            $date = isset($options['format'])
                ? Date::createFromFormat($options['format'], $value)
                : new Date($value);
        } catch (Exception $e) {
            throw new NotFoundHttpException($invalidDateMessage);
        }
        if (!$date) {
            throw new NotFoundHttpException($invalidDateMessage);
        }
        $request->attributes->set($param, $date);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        if (null === $configuration->getClass()) {
            return false;
        }

        return Date::class === $configuration->getClass();
    }
}
