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

interface MessageInterface
{
    /**
     * Get the original AMQPMessage.
     *
     * @return AMQPMessage
     */
    public function getOriginalMessage();

    /**
     * Get the processed message body.
     *
     * @return mixed
     */
    public function getObjectMessage();

    /**
     * Set the processed message.
     *
     * @param mixed $message
     */
    public function setObjectMessage($message);

    /**
     * Get the message property.
     *
     * @param $property
     * @return mixed|null
     */
    public function get($property);
}