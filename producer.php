<?php

require __DIR__ . '/vendor/autoload.php';

define('TOPIC_NAME', 'ThirdTopic');
$brokers = '127.0.0.1:9092';
$partition = 0;

$conf = new \RdKafka\Conf();

if (function_exists('pcntl_sigprocmask')) {
    pcntl_sigprocmask(SIG_BLOCK, array(SIGIO));
    $conf->set('internal.termination.signal', SIGIO);
} else {
    $conf->set('queue.buffering.max.ms', 1);
}

//default partition RD_KAFKA_PARTITION_UA
$message = array(
    'key' => '123456789',
    'data' => 'Message'
);
$rk = new \RdKafka\Producer($conf);
$rk->setLogLevel(LOG_DEBUG);
$rk->addBrokers($brokers);
$topic = $rk->newTopic(TOPIC_NAME);
$topic->produce($partition, 0, json_encode($message));

?>