<?php
namespace makbari\RabbitMQRepository\repository;

use makbari\RabbitMQRepository\interfaces\iQueueRepository;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


class RabbitMQRepository implements iQueueRepository
{

    /**
     * @var AMQPStreamConnection
     */
    public $AMQPStreamConnection;

    /**
     * @var AMQPChannel
     */
    public $channel;


    /**
     * RabbitMQRepository constructor.
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $password
     * @param null $channelId
     */
    public function __construct(string $host, string $port, string $user, string $password, $channelId = null)
    {
        $this->AMQPStreamConnection = new AMQPStreamConnection($host, $port, $user, $password);
        $this->channel = $this->AMQPStreamConnection->channel($channelId);
    }

    /**
     * @param null $channelId
     * @return $this
     */
    public function channel($channelId = null)
    {
        $channel =  $this->AMQPStreamConnection->channel($channelId);
        $this->channel = $channel;

        return $this;
    }


    /**
     * @param string $queue
     * @param bool $passive
     * @param bool $durable
     * @param bool $exclusive
     * @param bool $auto_delete
     * @param bool $nowait
     * @param null $arguments
     * @param null $ticket
     * @return mixed|null
     */
    public function queueDeclare(
        $queue = '',
        $passive = false,
        $durable = false,
        $exclusive = false,
        $auto_delete = true,
        $nowait = false,
        $arguments = null,
        $ticket = null
    )
    {
        return $this->channel->queue_declare(
            $queue,
            $passive,
            $durable,
            $exclusive,
            $auto_delete,
            $nowait,
            $arguments,
            $ticket
        );
    }

    /**
     * @param string $body
     * @param array $properties
     * @return AMQPMessage
     */
    public function createMessage($body = '', $properties = [])
    {
        return new AMQPMessage($body, $properties);
    }

    /**
     * @param AMQPMessage $message
     * @param string $exchange
     * @param string $routing_key
     * @param bool $mandatory
     * @param bool $immediate
     * @param null $ticket
     */
    public function basicPublish(
        $message,
        $exchange = '',
        $routing_key = '',
        $mandatory = false,
        $immediate = false,
        $ticket = null
    )
    {
            $this->channel->basic_publish(
            $message,
            $exchange,
            $routing_key,
            $mandatory,
            $immediate,
            $ticket
        );
    }

    /**
     * @param string $queue
     * @param string $consumer_tag
     * @param bool $no_local
     * @param bool $no_ack
     * @param bool $exclusive
     * @param bool $nowait
     * @param null $callback
     * @param null $ticket
     * @param array $arguments
     * @return mixed|string
     */
    public function basicConsume(
        $queue = '',
        $consumer_tag = '',
        $no_local = false,
        $no_ack = false,
        $exclusive = false,
        $nowait = false,
        $callback = null,
        $ticket = null,
        $arguments = array()
    )
    {
        return $this->channel->basic_consume(
            $queue,
            $consumer_tag,
            $no_local,
            $no_ack,
            $exclusive,
            $nowait,
            $callback,
            $ticket,
            $arguments
        );
    }

    /**
     * closes the channel and the connection
     */
    public function close()
    {
        $this->channel->close();
        $this->AMQPStreamConnection->close();
    }

}