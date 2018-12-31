<?php

require '../autoload.php';

// IRC Connection
$address = 'example.com';
$port = 6667;

$account = 'userName';

$irc = new IRC();
$irc->connect($address, $port);

$irc->send('NICK '.$account);
echo $irc->read();

$irc->close();



