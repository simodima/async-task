<?php

namespace Trt\AsyncTasksBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Trt\AsyncTasksBundle\DependencyInjection\Compiler\ListenersCompilerPass;

class TrtAsyncTasksBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ListenersCompilerPass());
    }
}
