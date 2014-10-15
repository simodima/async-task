<?php
/*
 * This file is part of the async-tasks package.
 *
 * (c) toretto460 <toretto460@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Trt\AsyncTasksBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Load data fixtures from bundles.
 *
 * @author toretto460 <toretto460@gmail.com>
 */
class WorkersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('trt:async:run')
            ->setDescription('Start a new instance of async message consumer')
            ->addArgument('queue_name', InputArgument::REQUIRED, 'Queue Name')
        ;
    }

    protected function writeOut(OutputInterface $output, $message)
    {
        if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
            $output->writeln($message);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queue = $input->getArgument('queue_name');
        $mqService = $this->getContainer()->get('trt_async.mq.service');
        $listenersRegister = $this->getContainer()->get('trt_async.listeners_register.service');
        
        $listeners = $listenersRegister->getListeners($queue);
        if (count($listeners)) {
            $mqService->define($queue);
        }

        $messageStream = $mqService->subscribe($queue);
        $this->writeOut($output, "Subscribed to <info>$queue</info>");

        foreach($messageStream as $message){
            try{
                foreach($listeners as $listener){
                    $this->writeOut(
                        $output,
                        sprintf("<info>%s</info> Listener working on message given from <info>%s</info> queue", get_class($listener), $queue)
                    );
                    $listener->work($message->getData());
                }

                //Process the message
                $mqService->ack($message);

            } catch(\Exception $e) {
                $this->writeOut(
                    $output,
                    sprintf(
                        "An <error>ERROR</error> occured while %s was working on a message from %s queue: %s\n",
                        get_class($listener),
                        $queue,
                        $e->getMessage()
                    )
                );
            }
        }
    }
}
