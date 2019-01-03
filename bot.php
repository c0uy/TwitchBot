<?php

require 'autoload.php';

$address = 'irc.chat.twitch.tv';
$port = 6667;

$access_token = '';
$nick = 'couybot';
$channel = 'th0ny_';

$irc = new TwitchIRC();
$irc->connect($address, $port);

if($irc->login($nick, $access_token) && $irc->join($channel)) {

	$irc->sendMessage('Bienvenue sur la chaine de '.$channel);

	while(true) {
		$buffer = $irc->read();

		if(!empty($buffer)) {
			if($irc->isPing($buffer))
				$irc->sendPong();
			else {
				$bufferExp = explode(':', $buffer);

				$username = explode('!', $bufferExp[1])[0];
				unset($bufferExp[0]);
				unset($bufferExp[1]);
				$message = implode(':', $bufferExp);

				$log->print(date('H:i:s', time()).' < ');
				$log->print($username.' : ', COLOR_USER_NICK);
				$log->println($message, COLOR_USER_MESSAGE);

				if($bufferExp[2] == '!ping')
					$irc->sendMessage('pong');
			}
		}

		usleep(100);
	}

	$irc->sendMessage('Bye !');
	$irc->part();

}

$irc->close();