<?php

namespace HMLB\DateBundle\Tests\Functional;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use HMLB\DateBundle\HMLBDateBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;

/**
 * TestKernel.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class TestKernel extends Kernel
{
    private $tempDir;

    public function __construct($environment, $debug)
    {
        parent::__construct($environment, $debug);

        $tempDir = __DIR__.'/../tmp/';

        $filesystem = new Filesystem();
        $filesystem->remove($tempDir.'cache');
        $filesystem->remove($tempDir.'logs');
        $filesystem->mkdir($tempDir.'cache');
        $filesystem->mkdir($tempDir.'logs');

        $this->tempDir = $tempDir;
    }

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new MonologBundle(),
            new HMLBDateBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config.yml');
    }

    public function getCacheDir()
    {
        return $this->tempDir.'/cache';
    }

    public function getLogDir()
    {
        return $this->tempDir.'/logs';
    }
}
