<?php
/**
 * This file is part of symfony2.3SwiftProject package.
 *
 * Simone Di Maulo <toretto460@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Trt\AsyncTasksBundle\Dispatcher;

use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\Event;

class AsyncDispatcher  extends ContainerAwareEventDispatcher
{

    private function getMqService()
    {
        return $this->getContainer()->get('trt_async.mq.service');
    }

    /**
     * {@inheritDoc}
     *
     * Lazily loads listeners for this event from the dependency injection
     * container.
     *
     * @throws \InvalidArgumentException if the service is not defined
     */
    public function dispatch($eventName, Event $event = null)
    {
        if ($this->interceptAsyncEvent($eventName, $event) != null ){
            $this->getContainer()
                ->get('logger')
                ->info(sprintf('Async event intercepted[%s]!',$eventName));
        }

        return parent::dispatch($eventName, $event);
    }

    /**
     * Intercept an async event.
     * The detection policy is based on naming convention
     *
     * @param $eventName
     * @param Event $event
     * @return null|Event
     */
    protected function interceptAsyncEvent($eventName, Event $event = null)
    {
        if(false !== strpos($eventName, $this->getContainer()->getParameter('trt_async_tasks.event.prefix'))){
            return $this->dispatchAsync($event);
        }

        return null;
    }

    /**
     * Serialize the event data and publish it to the redis queue.
     *
     * @param Event $event
     *
     * @return Event
     */
    public function dispatchAsync(Event $event = null)
    {
        $this->getMqService()->publish($event->getName() ,serialize($event));

        return $event;
    }

}