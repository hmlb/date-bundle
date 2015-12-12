<?php

namespace HMLB\DateBundle\Form\DataTransformer;

use HMLB\Date\Date;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transforms between a timestamp and a DateTime object.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Florian Eckerstorfer <florian@eckerstorfer.org>
 */
class DateTimeToTimestampTransformer extends BaseDateTimeTransformer
{
    /**
     * Transforms a DateTime object into a timestamp in the configured timezone.
     *
     * @param \DateTime|\DateTimeInterface $dateTime A DateTime object
     *
     * @return int A timestamp
     *
     * @throws TransformationFailedException If the given value is not an instance
     *                                       of \DateTime or if the output
     *                                       timezone is not supported.
     */
    public function transform($value)
    {
        if (null === $value) {
            return;
        }

        if (!$value instanceof Date) {
            throw new TransformationFailedException('Expected a Date.');
        }

        return $value->getTimestamp();
    }

    /**
     * Transforms a timestamp in the configured timezone into a DateTime object.
     *
     * @param string $value A timestamp
     *
     * @return \DateTime A \DateTime object
     *
     * @throws TransformationFailedException If the given value is not a timestamp
     *                                       or if the given timestamp is invalid.
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return;
        }

        if (!is_numeric($value)) {
            throw new TransformationFailedException('Expected a numeric.');
        }

        try {
            $dateTime = new Date();
            $dateTime = $dateTime->setTimezone(new \DateTimeZone($this->outputTimezone));
            $dateTime = $dateTime->setTimestamp($value);

            if ($this->inputTimezone !== $this->outputTimezone) {
                $dateTime = $dateTime->setTimezone(new \DateTimeZone($this->inputTimezone));
            }
        } catch (\Exception $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }

        return $dateTime;
    }
}
