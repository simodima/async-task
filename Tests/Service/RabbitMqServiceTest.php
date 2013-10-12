<?php
namespace Trt\AsyncTasksBundle\Tests\Service;

/*
 * This file is part of the async-tasks package.
 *
 * (c) toretto460 <toretto460@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Trt\AsyncTasksBundle\Service\RabbitMq\RabbitMqService;

class RabbitMqServiceTest extends RabbitMqBase
{
    /**
     * @dataProvider provideTestOptions
     * @param array $options
     */
    public function test_declaringExchange(array $options)
    {
        $this->mockedChannel->expects($this->once())
            ->method('exchange_declare')
            ->with(
                $this->equalTo($options['exchange']),
                $this->equalTo($options['exchange_type']),
                $this->equalTo(false),
                $this->equalTo(true),
                $this->equalTo(false)
            );

        $service = new RabbitMqService($this->mockedConnectionFactory, $this->mockedLogger, $options);

        /**
         * Automatic service connection in constructor
         */
        //$service->connect();
        $this->assertInstanceOf('\Trt\AsyncTasksBundle\Service\MqClientInterface', $service);
    }

    public function provideTestOptions()
    {
        return array(
            'correct options' => array(
                array('exchange' => 'my_exchange', 'exchange_type' => 'direct')
            ),
            'wrong options'   => array(
                array('exchange' => 'exchange_name', 'exchange_type' => null)
            )
        );
    }

    public function test_disconnect()
    {
        $this->mockedChannel->expects($this->once())
            ->method('close');
        $this->connection->expects($this->once())
            ->method('close');

        $service = new RabbitMqService(
            $this->mockedConnectionFactory,
            $this->mockedLogger,
            array('exchange' => 'my_exchange', 'exchange_type' => 'direct')
        );
        $service->connect();
        $service->disconnect();
    }

    /**
     * @dataProvider messagesProvider
     * @param $queueName
     * @param $jsonMessage
     */
    public function test_publish($queueName, $jsonMessage)
    {
        $service = new RabbitMqService(
            $this->mockedConnectionFactory,
            $this->mockedLogger,
            array('exchange' => 'my_exchange', 'exchange_type' => 'direct')
        );

        $this->mockedChannel->expects($this->once())
            ->method('basic_publish')
            ->with(
                $this->isInstanceOf('\PhpAmqpLib\Message\AMQPMessage'),
                $this->equalTo($service->getExchange())
            );

        $service->connect();
        $service->publish($queueName, $jsonMessage);
        $this->assertTrue($service->hasQueue($queueName));
    }

    public function messagesProvider()
    {
        $message = array('key' => 'value');

        return array(
            'queue1' => array('queue1', json_encode($message, true)),
        );
    }
}