<?php
/*
 * This file is part of the async-tasks package.
 *
 * (c) toretto460 <toretto460@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Trt\AsyncTasksBundle\Service\Redis;

use Predis\Client;
use Trt\AsyncTasksBundle\Service\MqClientInteface;

class RedisMqService implements MqClientInteface {

    /** @var  $client Client */
    protected $client;

    public function __construct($host = 'localhost', $port = '6379', $params = array())
    {
        $connectionParams = array_merge(
            array(
                'scheme'=>  'tcp',
                'host'  =>  $host,
                'port'  =>  $port
            ), $params
        );
        $this->createClient($connectionParams);
    }

    public function createClient(array $options)
    {
       $this->client = new Client($options);
    }

    public function getClient()
    {
        return $this->client;
    }

    public function connect()
    {
        $this->client->connect();
    }

    public function disconnect()
    {
        $this->client->disconnect();
    }

    public function subscribe($queue)
    {
        $pubSub = $this->client->pubSub();
        $pubSub->subscribe($queue);

        return $pubSub;
    }

    public function publish($queue_name, $message)
    {
        $this->client->publish($queue_name, $message);
    }

    public function isSubscription($message)
    {
        return (property_exists($message, 'kind') && $message->kind === RedisMessageKind::KIND_SUBSCRIBE);
    }

    public function isMessage($message)
    {
        return (property_exists($message, 'kind') && $message->kind === RedisMessageKind::KIND_MESSAGE);
    }
}