<?php
/*
 * This file is part of the async-tasks package.
 *
 * (c) toretto460 <toretto460@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Trt\AsyncTasksBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ListenersCompilerPass implements CompilerPassInterface
{

    const TAG_NAME = 'trt_async.listener.listen';

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('trt_async.listeners_register.service')) {
            return;
        }

        $definition = $container->getDefinition(
            'trt_async.listeners_register.service'
        );

        $taggedServices = $container->findTaggedServiceIds(
            self::TAG_NAME
        );

        foreach ($taggedServices as $id => $attributes) {

            $eventName = $this->getAttribute($attributes, 'event');
            $definition->addMethodCall(
                'add',
                array(new Reference($id), $eventName)
            );
        }
    }

    private function getAttribute($attributes, $name)
    {
        foreach($attributes as $attr){
            if(isset($attr[$name])){
                return $attr[$name];
            }
        }

        throw new \InvalidArgumentException(
            sprintf(
                "The %s tan need a '%s' key",
                self::TAG_NAME,
                $name
            )
        );
    }


}