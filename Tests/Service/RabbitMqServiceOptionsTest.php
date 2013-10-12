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

class RabbitMqServiceOptionsTest extends RabbitMqBase
{
    /**
     * @dataProvider provideTestOptions
     * @param $options
     * @param bool $expectedException
     */
    public function test_serviceInitialization($options, $expectedException = false)
    {
        if ($expectedException){
            $this->setExpectedException($expectedException);
        }
        $service = new RabbitMqService($this->mockedConnectionFactory, $this->mockedLogger, $options);
        $this->assertInstanceOf('\Trt\AsyncTasksBundle\Service\MqClientInterface', $service);

        $this->assertEquals($options['exchange'], $service->getExchange());
        $this->assertEquals($options['exchange_type'], $service->getExchangeType());

    }

    public function provideTestOptions()
    {
        return array(
            'correct options' => array(
                array('exchange' => 'my_exchange', 'exchange_type' => 'direct'),
                false
            ),
            'missing exchange options' => array(
                array('exchange_type' => 'direct'),
                'Symfony\Component\OptionsResolver\Exception\MissingOptionsException'
            ),
            'wrong exchange_type options' => array(
                array('exchange_type' => 'no-type'),
                'Symfony\Component\OptionsResolver\Exception\MissingOptionsException'
            ),
        );
    }

}