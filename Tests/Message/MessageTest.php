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
use Trt\AsyncTasksBundle\Message\Message;
use Trt\AsyncTasksBundle\Message\MessageIterator;

class MessageTest extends \PHPUnit_Framework_TestCase
{

    protected $mockedChannel;
    protected $iterator;

    /**
     * @dataProvider getMessageProvider
     * @param $AMQPmessage
     * @param $property
     * @param $expectedValue
     */
    public function test_getProperty($AMQPmessage, $property, $expectedValue)
    {
        $message = new Message($AMQPmessage);

        $this->assertEquals($expectedValue, $message->get($property));
        $this->assertEquals($AMQPmessage, $message->getOriginalMessage());
    }

    public function getMessageProvider()
    {
        return array(
            "correct property" => array(
                new AMQPMessage('{"json": "test"}', array('content_type'=> 'application/json')),
                'content_type',
                'application/json'
            ),
            "wrong property" => array(
                new AMQPMessage('{"json": "test"}', array('content_type'=> 'application/json')),
                'wrong_property',
                null
            ),
            "null property" => array(
                new AMQPMessage('{"json": "test"}', array('content_type'=> 'application/json','type' => null)),
                'type',
                null
            )
        );

    }
}