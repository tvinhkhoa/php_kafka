<?php

require __DIR__ . '/vendor/autoload.php';

define('TOPIC_NAME', 'ThirdTopic');
$brokers = '127.0.0.1,localhost';
$partition = 1;

// enable.auto.commit set message had read
$conf = new \RdKafka\Conf();
$conf->set('client.id', 'client-msg');
$conf->set('debug', 'fetch, topic');
$conf->set('socket.timeout.ms', 50); // or socket.blocking.max.ms, depending on librdkafka version
$conf->set('queued.min.messages', 2);
$conf->set('group.id', 'consumerGroup1');
$conf->set('enable.auto.commit', true);

if (function_exists('pcntl_sigprocmask')) {
    pcntl_sigprocmask(SIG_BLOCK, array(SIGIO));
    $conf->set('internal.termination.signal', SIGIO);
} else {
    $conf->set('queue.buffering.max.ms', 100);
}

$rk = new \RdKafka\Consumer($conf);
$rk->setLogLevel(LOG_DEBUG);
$rk->addBrokers($brokers); // set in server.properties :9029,:2181

/*
* RD_KAFKA_OFFSET_BEGINNING
* RD_KAFKA_OFFSET_STORED
*/

$topicConf = new RdKafka\TopicConf();
$topicConf->set('auto.commit.interval.ms', 1e3);
// $topicConf->set('auto.offset.reset', 'smallest');
// $topicConf->set("offset.store.sync.interval.ms", 60e3);
$topicConf->set('offset.store.method', 'broker');

//
$topic = $rk->newTopic(TOPIC_NAME, $topicConf);
$topic->consumeStart($partition, RD_KAFKA_OFFSET_STORED);

echo "Consumer running on partion: {$partition}\n\n";

while (true) {
    $msg = $topic->consume($partition, 24*1000);
    if($msg == NULL) {
      continue;
    }

    if ($msg->err) {
        switch ($msg->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                var_dump($msg);
                break;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                echo "No more messages; will wait for more\n";
                break;
            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                echo "Time out\n";
                break;
            default:
                throw new \Exception($msg->errstr(), $msg->err);
                break;
        }
    } else {
        echo $msg->payload . PHP_EOL, "\n";
    }

    // schedule offset store after successfully consuming the message
    $topic->offsetStore($msg->partition, $msg->offset);
}

$topic->consumeStop(0);

?>