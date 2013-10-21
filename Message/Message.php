<?php
/*
 * This file is part of the async-tasks package.
 *
 * (c) toretto460 <toretto460@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Trt\AsyncTasksBundle\Message;

use PhpAmqpLib\Message\AMQPMessage;

class Message implements MessageInterface
{
    /**
     * @var $originalMessage AMQPMessage
     */
    protected $originalMessage;

    protected $message;

    public function __construct(AMQPMessage $originalMessage)
    {
        $this->originalMessage = $originalMessage;
    }

    /**
     * Get the original AMQPMessage.
     *
     * @return AMQPMessage
     */
    public function getOriginalMessage()
    {
        return $this->originalMessage;
    }

    /**
     * Get the processed message body.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->message;
    }

    /**
     * Set the processed message.
     *
     * @param mixed $message
     */
    public function setData($message)
    {
        $this->message = $message;
    }

    /**
     * Get the message property.
     *
     * @param $property
     * @return mixed|null
     */
    public function get($property)
    {
        return $this->getOriginalMessage()->has($property) ? $this->getOriginalMessage()->get($property) : null;
    }
}
