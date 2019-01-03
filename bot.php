<?php

require 'autoload.php';

$irc = new TwitchIRC();
$irc->connect($config['server']['address'], $config['server']['port']);

if($irc->login($config['account']['nick'], $config['account']['oauth']) && $irc->join($config['server']['channel'])) {

	$irc->sendMessage('Bienvenue sur la chaine de '.$config['server']['channel']);

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
