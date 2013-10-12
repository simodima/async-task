<?php
/*
 * This file is part of the async-tasks package.
 *
 * (c) toretto460 <toretto460@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Trt\AsyncTasksBundle\Listener;


use Trt\AsyncTasksBundle\Event\AsyncEventInterface;

interface ListenerInterface
{
    function work(AsyncEventInterface $event);

}