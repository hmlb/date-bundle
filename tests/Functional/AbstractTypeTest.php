<?php

namespace HMLB\DateBundle\Tests\Functional;

use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * TypeTest.
 *
 * @author Hugues Maignol <hugues.maignol@kitpages.fr>
 */
abstract class AbstractTypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactoryInterface
     */
    protected $factory;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var FormBuilderInterface
     */
    protected $builder;

    /**
     * @var ContainerInterface
     */
    protected $container;

    protected function setUp()
    {
        $kernel = new TestKernel('test', true);
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->factory = $this->container->get('form.factory');
        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
    }
}
