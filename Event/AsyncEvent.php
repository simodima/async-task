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

use Symfony\Component\EventDispatcher\Event;

class AsyncEvent extends Event implements \Serializable, AsyncEventInterface
{
    /**
     * Event data.
     *
     * @var array $data
     */
    protected $data;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @param $eventName
     * @param array $data
     */
    public function __construct($eventName, array $data)
    {
        $this->setName($eventName);
        $this->data = $data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->getName(),
            $this->data,
        ));

    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $unserializedData = unserialize($serialized);

        list($name, $data) = $unserializedData;
        $this->setName($name);
        $this->setData($data);
    }
}