<?php

namespace HMLB\DateBundle\Tests;

use Exception;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

/**
 * Trait à utiliser dans les classes de tests pour pouvoir debuger des variables.
 *
 * Meme comportement que dump() mais pour des process qui ne bootent pas le Kernel.
 *
 * S'utilise en appelant $this->dump($stuff) dans les classes de tests.
 *
 * @author Hugues Maignol <hugues.maignol@kitpages.fr>
 */
trait DumperCapabilities
{
    /**
     * @var CliDumper
     */
    private $__dumper;

    /**
     * @var VarCloner
     */
    private $__cloner;

    /**
     * Print dans la console les infos sur la variable en param.
     *
     * @param mixed $var
     *
     * @throws Exception
     */
    protected function dump($var)
    {
        if (null === $this->__dumper) {
            $this->__initDumper();
        }
        $this->__dumper->dump($this->__cloner->cloneVar($var));
    }

    protected function enableGlobalDumpFunction()
    {
        if (function_exists('dump')) {
            return;
        }
        function dump($var)
        {
            $this->__dumper->dump($var);
        }
    }

    private function __initDumper()
    {
        $this->__dumper = new CliDumper();
        $this->__cloner = new VarCloner();
    }
}
