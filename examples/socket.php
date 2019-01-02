<?php

require '../autoload.php';

$address = 'www.example.com';
$port = 'www';

$log->println('Target '.$address.':'.$port);

$socket = new Socket();
$socket->connect($address, $port);

$log->println('Sending Data ...');
$message = "HEAD / HTTP/1.0\r\n\r\n";
$message .= "Host: www.example.com\r\n";
$message .= "Connection: Close\r\n\r\n";
$log->print($message, 'GREEN');
$socket->send($message);

$log->println('Receiving Data ...');
$log->print($socket->read(), 'GREEN');

$log->println('Closing connexion ...');
$socket->close();