<?php

require '../autoload.php';

$address = 'irc.chat.twitch.tv';
$port = 6667;

$access_token = '';
$nick = '';
$channel = '';

$irc = new TwitchIRC();
$irc->connect($address, $port);

if($irc->login($nick, $access_token) && $irc->join($channel)) {
	$irc->sendMessage('HEY');
}

$irc->close();