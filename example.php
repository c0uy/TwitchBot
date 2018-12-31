<?php

require 'autoload.php';

$address = 'www.example.com';
$port = 'www';

echo 'Target '.$address.':'.$port.PHP_EOL;

$socket = new Socket();
$socket->connect($address, $port);

$message = "HEAD / HTTP/1.0\r\n\r\n";
$message .= "Host: www.example.com\r\n";
$message .= "Connection: Close\r\n\r\n";

echo 'Writing Data ...'.PHP_EOL;
$socket->write($message);

echo 'Receiving Data ...'.PHP_EOL;
echo $socket->read().PHP_EOL;

echo 'Closing connexion ...'.PHP_EOL;
$socket->close();