<?php

namespace HMLB\DateBundle\Tests\Functional;

use Doctrine\Bundle\DoctrineBundle\Registry;
use PHPUnit_Framework_TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * TypeTest.
 *
 * @author Hugues Maignol <hugues.maignol@kitpages.fr>
 */
abstract class AbstractDoctrineTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Registry
     */
    protected $doctrine;
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var TestKernel
     */
    protected $kernel;

    protected function setUp()
    {
        $this->kernel = new TestKernel('test', true);
        $this->kernel->boot();
        $this->container = $this->kernel->getContainer();
        $this->doctrine = $this->container->get('doctrine');

        $this->runCommand('doctrine:database:create');
        $this->runCommand('doctrine:schema:update --force');
    }

    protected function tearDown()
    {
        $this->runCommand('doctrine:database:drop');
    }

    protected function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        return $application->run(new StringInput($command));
    }
}
