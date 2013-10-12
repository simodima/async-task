<?php
/*
 * This file is part of the async-tasks package.
 *
 * (c) toretto460 <toretto460@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Trt\AsyncTasksBundle\Service;


use Trt\AsyncTasksBundle\Listener\ListenerInterface;

class ListenersRegister
{

    protected $listeners;

    public function __construct()
    {
        $this->listeners = array();
    }

    /**
     * Ensures that the event key exists.
     *
     * @param $event_name
     */
    private function softDefineEventKey($event_name)
    {
        if(!isset($this->listeners[$event_name])){
            $this->listeners[$event_name] = array();
        }
    }

    /**
     * Add the listener to the $event_name event.
     *
     * @param ListenerInterface $listener
     * @param $event_name
     */
    public function add(ListenerInterface $listener, $event_name)
    {
        $this->softDefineEventKey($event_name);

        $this->listeners[$event_name][] = $listener;
    }

    /**
     * Get the listeners for given event name.
     *
     * @param $event_name
     * @return array
     */
    public function getListeners($event_name)
    {
        $this->softDefineEventKey($event_name);

        return $this->listeners[$event_name];
    }
}