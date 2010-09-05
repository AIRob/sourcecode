#!/usr/bin/env php
<?php
###################################################
# RabbitMQ in Action
# Chapter 4.2.x - Hello World Consumer
# 
# Requires: php-amqp http://github.com/bkw/php-amqp
# 
# Author: Alvaro Videla
# (C)2010
###################################################

require_once('../lib/amqp.inc');

define('HOST', 'localhost');
define('PORT', 5672);
define('USER', 'guest');
define('PASS', 'guest');
define('VHOST', '/');

$exchange = 'hello-exchange';
$queue = 'hello-queue';
$consumer_tag = 'consumer';

$conn = new AMQPConnection(HOST, PORT, USER, PASS, VHOST);
$channel = $conn->channel();

$channel->exchange_declare($exchange, 'direct', false, true, false);

$channel->queue_declare($queue);

$channel->queue_bind($queue, $exchange);

$consumer = function($msg){
  echo 'Hello ',  $msg->body, "\n";
  $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

$channel->basic_consume($queue, $consumer_tag, false, false, false, false, $consumer);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->basic_cancel($consumer_tag);
$channel->close();
$conn->close();

?>