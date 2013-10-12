<?php
namespace Trt\AsyncTasksBundle\Tests\Message;

/*
 * This file is part of the async-tasks package.
 *
 * (c) toretto460 <toretto460@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Trt\AsyncTasksBundle\Message\MessageIterator;

class MessageIteratorTest extends \PHPUnit_Framework_TestCase
{

    protected $mockedChannel;
    protected $iterator;

    public function setUp()
    {
        $this->mockedChannel = $this->getMockBuilder('PhpAmqpLib\Channel\AMQPChannel')
            ->disableOriginalConstructor()
            ->setMethods(array('basic_consume','wait'))
            ->getMock();
        $this->mockedChannel->expects($this->once())
            ->method('basic_consume')
            ->with(
                $this->equalTo('test_queue'),
                $this->equalTo('test_queue'),
                $this->equalTo(false),
                $this->equalTo(false),
                $this->equalTo(false),
                $this->equalTo(false),
                $this->callback(function($array){
                    return (is_array($array) && count($array) == 2);
                })
            );

        $this->iterator = new MessageIterator($this->mockedChannel, 'test_queue');
    }

    public function test_MessageIterator()
    {
        $this->assertCount(0, $this->iterator);
    }

    public function test_Iteration()
    {
        $count = 0;
        $iteration = 1;
        $testBody = '{"JSON":"TEST"}';
        $iterator = $this->iterator;
        $this->mockedChannel->callbacks = $iteration;

        $this->mockedChannel->expects($this->any())
            ->method('wait')
            ->will($this->returnCallback(function() use ($iterator, $testBody){
                $refProp = new \ReflectionProperty($iterator, 'message');
                $refProp->setAccessible(true);
                $refProp->setValue($iterator, new AMQPMessage($testBody));
            }));

        foreach($this->iterator as $message){
            $this->assertInstanceOf('PhpAmqpLib\Message\AMQPMessage', $message);
            $this->assertEquals($testBody, $message->body);

            if ($count == $iteration){
                break;
            }
            $count++;
        }

    }
}