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

class RabbitMqBase extends \PHPUnit_Framework_TestCase
{
    protected $mockedConnectionFactory;
    protected $mockedChannel;
    protected $mockedLogger;
    protected $connection;

    public function setUp()
    {
        $this->mockedLogger = $this->getMockForAbstractClass('Psr\Log\AbstractLogger');

        $this->mockedConnectionFactory = $this->getMockBuilder('Trt\AsyncTasksBundle\Factory\RabbitMqConnectionFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('createConnection'))
            ->getMock();

        $this->connection = $this->getMockBuilder('PhpAmqpLib\Connection\AMQPConnection')
            ->disableOriginalConstructor()
            ->setMethods(array('channel','close'))
            ->getMock();

        $this->mockedConnectionFactory->expects($this->any())
            ->method('createConnection')
            ->will($this->returnValue($this->connection));

        $this->mockedChannel = $this->getMockBuilder('\PhpAmqpLib\Channel\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();

        $this->connection->expects($this->any())
            ->method('channel')
            ->will($this->returnValue($this->mockedChannel));
    }
}