<?php
/*
 * This file is part of the async-tasks package.
 *
 * (c) toretto460 <toretto460@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Trt\AsyncTasksBundle\Event;

interface AsyncEventInterface
{
    /**
     * @param array $data
     */
    function setData(array $data);

    /**
     * @return array
     */
    function getData();

    /**
     * @return string
     */
    function serialize();

    /**
     * @param string $serialized
     */
    function unserialize($serialized);

    /**
     * Get the event name.
     *
     * @return string
     */
    function getName();
}