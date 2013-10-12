<?php
/*
 * This file is part of the async-tasks package.
 *
 * (c) toretto460 <toretto460@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Trt\AsyncTasksBundle\Factory;


use PhpAmqpLib\Connection\AMQPConnection;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RabbitMqConnectionFactory implements ConnectionFactoryInterface
{
    public function __construct($options)
    {
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'host'  => 'localhost',
            'port'  => 5672,
            'user'  => 'guest',
            'pass'  => 'guest',
            'vhost' => '/'
        ));
    }

    public function createConnection()
    {
        return new AMQPConnection(
            $this->options['host'],
            $this->options['port'],
            $this->options['user'],
            $this->options['pass'],
            $this->options['vhost']
        );
    }

}