<?php

namespace HMLB\DateBundle\Tests;

use Doctrine\DBAL\Types\Type;
use HMLB\DateBundle\Doctrine\ORM\DBAL\Types\DateTimeType;
use HMLB\DateBundle\Doctrine\ORM\DBAL\Types\DateType;
use HMLB\DateBundle\Tests\Functional\TestKernel;
use PHPUnit_Framework_TestCase;
use Symfony\Bundle\FrameworkBundle\Test;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * DateBundleTest.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class DateBundleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setUp()
    {
        $kernel = new TestKernel('test', true);
        $kernel->boot();

        $this->container = $kernel->getContainer();
    }

    /**
     * @test
     */
    public function typesAreRegistered()
    {
        $this->assertTrue(Type::hasType(DateTimeType::NAME));
        $this->assertTrue(Type::hasType(DateType::NAME));
    }
}
