<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Log;

class AttendanceConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:attendance-consumer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This acts as a consumer to the attendance queue';

    /**
     * Execute the console command.
     */

     /**
     * Execute the console command.
     *
     * @return int
     */

    /**
     * queue_declare function: 
     * Declares queue, creates if needed
     *
     * @param string $queue
     * @param bool $passive
     * @param bool $durable
     * @param bool $exclusive
     * @param bool $auto_delete
     * @param bool $nowait
     * @param array|AMQPTable $arguments
     * @param int|null $ticket
     * @return array|null
     *@throws \PhpAmqpLib\Exception\AMQPTimeoutException if the specified operation timeout was exceeded
    */

    /**
     * basic_consume function: 
     * @param string consumer_tag: Consumer identifier
     * @param bool no_local: Don't receive messages published by this consumer.
     * @param bool no_ack: If set to true, automatic acknowledgement mode will be used by this consumer. See https://www.rabbitmq.com/confirms.html for details.
     * @param bool exclusive: Request exclusive consumer access, meaning only this consumer can access the queue
     * @param bool nowait:
     * callback: A PHP Callback
    */
    public function handle()
    {
        //
        $connection = new AMQPStreamConnection('localhost', 5672, 'admin_anagkazo', 'blender3D');
        $channel = $connection->channel();
        $channel->queue_declare('attendance_queue', false, true, false, false);
        
        $callback = function ($msg) {
            echo ' [x] Received ', $msg->body, "\n";
            Log::info(' [x] Received ', $msg->body, "\n");
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume('attendance_queue','', false, false, false, false, $callback);
        while ($channel->is_consuming()) {
            $channel->wait();
        }
        

        $channel->close();
        return Command::SUCCESS;
    }
}
