<?php
namespace Trt\AsyncTasksBundle\Tests\Dispatcher;

/*
 * This file is part of the async-tasks package.
 *
 * (c) toretto460 <toretto460@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Trt\AsyncTasksBundle\Dispatcher\AsyncDispatcher;
use Trt\AsyncTasksBundle\Event\AsyncEvent;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{

    protected $mockedContainer;
    protected $mockedLogger;
    protected $mockedMqService;
    protected $asyncDispatcher;

    public function setUp()
    {
        $this->mockedLogger = $this->getMock('Psr\Log\AbstractLogger');

        $this->mockedContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->setMethods(array('get','getParameter'))
            ->getMock();

        $this->mockedMqService = $this->getMockBuilder('Trt\AsyncTasksBundle\Service\MqClientInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('publish','subscribe','disconnect','define'))
            ->getMock();

        $callback = function($id){
            $valueMap = array(
                'trt_async.mq.service' =>  $this->mockedMqService,
                'logger' => $this->mockedLogger
            );

            return $valueMap[$id];
        };
        
        $this->mockedContainer->expects($this->any())
            ->method('get')
            ->will($this->returnCallback($callback));

        $this->asyncDispatcher = new AsyncDispatcher($this->mockedContainer);

    }

    /**
     * @dataProvider asyncEventDetectionProvider
     *
     * @param $asyncPrefix
     * @param AsyncEvent $event
     * @param $howManyTimes
     */
    public function test_asyncEventDetectionAndPublishing($asyncPrefix, AsyncEvent $event, $howManyTimes)
    {
        $that = $this;
        $this->mockedContainer->expects($this->once())
            ->method('getParameter')
            ->with($this->equalTo('trt_async_tasks.event.prefix'))
            ->will($this->returnValue($asyncPrefix));

        $this->mockedMqService->expects($howManyTimes)
            ->method('publish')
            ->will($this->returnCallback(function($evtName, $serializedEvt) use ($that, $event){
                $that->assertEquals($event->getName(), $evtName);
                $that->assertEquals(serialize($event), $serializedEvt);
            }));

        $this->asyncDispatcher->dispatch($event->getName(), $event);
    }

    public function asyncEventDetectionProvider()
    {
        return array(
            array('async_prefix', new AsyncEvent('async_prefix.event.test',array('key'=>'value')), $this->once()),
            array('async_prefix', new AsyncEvent('async_ERR_prefix.event.test',array('key'=>'value')), $this->never())
        );
    }

}