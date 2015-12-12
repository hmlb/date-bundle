<?php

namespace HMLB\DateBundle;

use HMLB\DateBundle\DependencyInjection\HMLBDateExtension;
use HMLB\DateBundle\Doctrine\ORM\DBAL\Types\DateTimeType;
use HMLB\DateBundle\Doctrine\ORM\DBAL\Types\DateType;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * HMLBDateBundle.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class HMLBDateBundle extends Bundle
{
    public function __construct()
    {
        $DBALType = '\Doctrine\DBAL\Types\Type';
        if (class_exists($DBALType)) {
            if (!call_user_func($DBALType.'::hasType', DateType::NAME)) {
                call_user_func($DBALType.'::addType', DateType::NAME, DateType::class);
            }
            if (!call_user_func($DBALType.'::hasType', DateTimeType::NAME)) {
                call_user_func($DBALType.'::addType', DateTimeType::NAME, DateTimeType::class);
            }
        }
    }

    public function getContainerExtension()
    {
        return new HMLBDateExtension();
    }
}
