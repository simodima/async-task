<?php
/*
 * This file is part of the async-tasks package.
 *
 * (c) toretto460 <toretto460@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Trt\AsyncTasksBundle\Service\RabbitMq;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use PhpAmqpLib\Connection\AMQPConnection;
use Trt\AsyncTasksBundle\Factory\ConnectionFactoryInterface;
use Trt\AsyncTasksBundle\Message\MessageInterface;
use Trt\AsyncTasksBundle\Message\MessageIterator;
use Trt\AsyncTasksBundle\Service\MqClientInterface;

class RabbitMqService implements MqClientInterface
{
    /** @var  AMQPChannel */
    protected $channel;

    /** @var  AMQPConnection */
    protected $connection;

    /** @var  array */
    protected $options;

    /** @var array  */
    protected $queues = array();

    /** @var bool */
    protected $isConnected = false;

    /** @var  ConnectionFactoryInterface */
    protected $connectionFactory;

    /** @var  LoggerInterface */
    protected $logger;

    /**
     * @param ConnectionFactoryInterface $connectionFactory
     * @param LoggerInterface $logger
     * @param array $options
     */
    public function __construct(ConnectionFactoryInterface $connectionFactory, LoggerInterface $logger, array $options = array())
    {
        $this->resolveOptions($options);
        $this->connectionFactory = $connectionFactory;
        $this->logger = $logger;

        $this->connect();
    }

    public function connect()
    {
        $this->logger->info("MqService connecting...");
        $this->connection = $this->connectionFactory->createConnection($this->options);
        $this->channel = $this->connection->channel();
        $this->exchangeDeclare();
        $this->isConnected = true;
        $this->logger->info("MqService connected");
    }

    public function isConnected()
    {
        return $this->isConnected;
    }

    public function getExchange()
    {
        return $this->options['exchange'];
    }

    public function getExchangeType()
    {
        return $this->options['exchange_type'];
    }

    public function hasQueue($queue)
    {
        return isset($this->queues[$queue]);
    }

    public function addQueue($queue)
    {
        $this->queues[$queue]= $queue;
    }

    protected function resolveOptions(array $options)
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setRequired(array('exchange', 'exchange_type'));
        $optionsResolver->setDefaults(array('exchange_type' => 'direct'));
        $optionsResolver->setAllowedValues(array(
            'exchange_type' => array('direct'),
        ));
        $this->options = $optionsResolver->resolve($options);
    }

    protected function exchangeDeclare()
    {
        $this->logger->info(sprintf("Declaring %s exchange with name %s", $this->getExchangeType(), $this->getExchange()));
        $this->channel->exchange_declare(
            $this->getExchange(),
            $this->getExchangeType(),
            false,
            true,
            false
        );

        return $this;
    }
    
    public function define($queue)
    {
        $this->declareAndBindQueue($queue);
    }

    protected function declareAndBindQueue($queue)
    {
        if(!$this->hasQueue($queue)) {
            $this->addQueue($queue);
            $this->channel->queue_declare($queue, false, true, false, false);
            $this->channel->queue_bind($queue, $this->getExchange(), $queue);
            $this->logger->info(sprintf("Queue %s declared.", $queue));
        }
    }

    public function disconnect()
    {
        if($this->isConnected()){
            $this->channel->close();
            $this->connection->close();
            $this->logger->info("Mq Service Disconnected");
        }
    }

    public function subscribe($queue)
    {
        $this->logger->info(sprintf("Creating Iterator(subscribe) on %s queue", $queue));
        return new MessageIterator($this->channel, $queue);
    }

    public function publish($routingKey, $message)
    {
        if($this->isConnected()){
            $this->declareAndBindQueue($routingKey);
            $amqpMessage = new AMQPMessage($message, array('content_type' => 'text/plain', 'delivery_mode' => 2));
            $this->channel->basic_publish($amqpMessage, $this->getExchange(), $routingKey);
            $this->logger->info(sprintf("Message %s published on %s queue", $message, $routingKey));
        }
    }

    public function ack(MessageInterface $message)
    {
        if($this->isConnected()){
            $this->channel->basic_ack($message->get('delivery_tag'));
            $this->logger->info(sprintf("Sent Ack for %n message", $message->get('delivery_tag')));
        }
    }
}