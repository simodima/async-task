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

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class MessageIterator implements \Iterator
{
    protected $channel;

    protected $message;

    protected $AMQMessage;

    protected $queue;

    protected $counter;

    /**
     * @param AMQPChannel $channel
     * @param $queue
     */
    public function __construct(AMQPChannel $channel, $queue)
    {
        $this->channel = $channel;
        $this->queue   = $queue;
        $this->counter = 0;
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return $this->message;
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        $this->wait();
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        $this->counter;
    }

    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        return count($this->channel->callbacks);
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        $this->channel->basic_consume(
            $this->queue,
            $this->queue,
            false,
            false,
            false,
            false,
            array($this, 'processMessage')
        );

        $this->wait();

        return $this->message;
    }

    protected function wait()
    {
        while ($this->valid()) {
            $this->channel->wait();

            break;
        }
    }

    /**
     * @param  AMQPMessage $AMQMessage
     * @return void
     */
    public function processMessage(AMQPMessage $AMQMessage)
    {
        $this->AMQMessage = $AMQMessage;
        $this->message = new Message($AMQMessage);
        $contentType = $this->message->get('content_type');

        if($contentType === "text/plain"){
            $this->message->setData(unserialize($this->AMQMessage->body));
        }elseif($contentType === "application/json"){
            $this->message->setData(json_decode($this->AMQMessage->body, true));
        }else{
            $this->message->setData($this->AMQMessage->body);
        }

        $this->counter++;
    }
}